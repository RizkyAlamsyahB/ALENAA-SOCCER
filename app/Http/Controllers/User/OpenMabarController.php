<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Field;
use App\Models\OpenMabar;
use App\Models\FieldBooking;
use Illuminate\Http\Request;
use App\Models\MabarParticipant;
use App\Models\MembershipSession;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OpenMabarController extends Controller
{
    /**
     * Menampilkan daftar open mabar
     */
    public function index(Request $request)
    {
        // Query dasar untuk open mabar
        $query = OpenMabar::with(['fieldBooking', 'fieldBooking.field', 'user'])
            ->where('status', 'open')
            ->where('end_time', '>', now());

        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                ->orWhereHas('fieldBooking.field', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }

        // Filter berdasarkan level
        if ($request->has('level') && !empty($request->level) && $request->level != 'all') {
            $level = $request->level;
            $query->where('level', $level);
        }

        // Filter berdasarkan lokasi (field)
        if ($request->has('location') && !empty($request->location) && $request->location != 'all') {
            $fieldId = $request->location;
            $query->whereHas('fieldBooking', function ($q) use ($fieldId) {
                $q->where('field_id', $fieldId);
            });
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_lowest':
                $query->orderBy('price_per_slot', 'asc');
                break;
            case 'price_highest':
                $query->orderBy('price_per_slot', 'desc');
                break;
            default:
                $query->orderBy('start_time', 'asc');
                break;
        }

        // Eksekusi query dengan paginasi
        $openMabars = $query->paginate(9);

        // Dapatkan daftar lapangan untuk filter
        $fields = Field::all();

        return view('users.mabar.index', compact('openMabars', 'fields'));
    }

    /**
     * Menampilkan detail open mabar
     */
    public function show($id)
    {
        $openMabar = OpenMabar::with([
            'fieldBooking',
            'fieldBooking.field',
            'user',
            'participants',
            'participants.user'
        ])->findOrFail($id);

        // Cek apakah user saat ini sudah bergabung ke mabar ini
        $isJoined = false;
        $userParticipant = null;

        if (Auth::check()) {
            $userParticipant = MabarParticipant::where('open_mabar_id', $id)
                ->where('user_id', Auth::id())
                ->first();

            $isJoined = !is_null($userParticipant);
        }

        return view('users.mabar.show', compact('openMabar', 'isJoined', 'userParticipant'));
    }

    /**
     * Menampilkan form untuk membuat open mabar
     */
    public function create()
    {
        // Ambil booking lapangan milik user yang sudah confirmed dan belum expired
        $fieldBookings = FieldBooking::with(['field'])
            ->where('user_id', Auth::id())
            ->where('status', 'confirmed')
            ->where('end_time', '>', now())
            ->orderBy('start_time', 'asc')
            ->get();

        // Cek apakah booking ini sudah digunakan untuk open mabar
        $usedBookingIds = OpenMabar::whereIn('field_booking_id', $fieldBookings->pluck('id'))
            ->pluck('field_booking_id')
            ->toArray();

        // Filter booking yang belum digunakan untuk open mabar
        $availableBookings = $fieldBookings->filter(function ($booking) use ($usedBookingIds) {
            return !in_array($booking->id, $usedBookingIds);
        });

        // Jika tidak ada booking yang tersedia
        if ($availableBookings->isEmpty()) {
            return redirect()->route('user.mabar.index')
                ->with('error', 'Anda tidak memiliki booking lapangan yang aktif dan tersedia untuk membuat Open Mabar. Silakan booking lapangan terlebih dahulu.');
        }

        // Pilihan level untuk form
        $levels = ['beginner', 'intermediate', 'advanced', 'all'];

        return view('users.mabar.create', compact('availableBookings', 'levels'));
    }

    /**
     * Menyimpan open mabar baru
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'field_booking_id' => 'required|exists:field_bookings,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_slot' => 'required|numeric|min:0',
            'total_slots' => 'required|integer|min:1|max:30',
            'level' => 'required|string|in:beginner,intermediate,advanced,all',
        ]);

        // Cek apakah booking ini milik user yang login
        $fieldBooking = FieldBooking::where('id', $request->field_booking_id)
            ->where('user_id', Auth::id())
            ->where('status', 'confirmed')
            ->where('end_time', '>', now())
            ->firstOrFail();

        // Cek apakah booking ini sudah digunakan untuk open mabar
        $existingMabar = OpenMabar::where('field_booking_id', $fieldBooking->id)->first();
        if ($existingMabar) {
            return back()->with('error', 'Booking lapangan ini sudah digunakan untuk Open Mabar lain.');
        }

        try {
            DB::beginTransaction();

            // Buat open mabar baru
            $openMabar = new OpenMabar();
            $openMabar->field_booking_id = $fieldBooking->id;
            $openMabar->user_id = Auth::id();
            $openMabar->title = $request->title;
            $openMabar->description = $request->description;
            $openMabar->start_time = $fieldBooking->start_time;
            $openMabar->end_time = $fieldBooking->end_time;
            $openMabar->price_per_slot = $request->price_per_slot;
            $openMabar->total_slots = $request->total_slots;
            $openMabar->filled_slots = 0; // Awalnya belum ada yang bergabung
            $openMabar->level = $request->level;
            $openMabar->status = 'open';
            $openMabar->save();

            DB::commit();

            return redirect()->route('user.mabar.show', $openMabar->id)
                ->with('success', 'Open Mabar berhasil dibuat! Sekarang Anda dapat mengundang pemain lain untuk bergabung.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Open Mabar: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat membuat Open Mabar. Silakan coba lagi.');
        }
    }

/**
 * Bergabung dengan open mabar
 */
public function join(Request $request, $id)
{
    $openMabar = OpenMabar::findOrFail($id);

    // Cek status mabar
    if ($openMabar->status != 'open') {
        return back()->with('error', 'Open Mabar ini sudah tidak tersedia untuk diikuti.');
    }

    // Cek apakah masih ada slot yang tersedia
    if ($openMabar->filled_slots >= $openMabar->total_slots) {
        return back()->with('error', 'Maaf, semua slot pada Open Mabar ini sudah terisi.');
    }

    // Cek apakah user sudah bergabung sebelumnya
    $existingActiveParticipant = MabarParticipant::where('open_mabar_id', $id)
        ->where('user_id', Auth::id())
        ->where('status', '!=', 'cancelled')
        ->first();

    if ($existingActiveParticipant) {
        return back()->with('info', 'Anda sudah terdaftar sebagai peserta Open Mabar ini.');
    }

    // Cek apakah user pernah bergabung sebelumnya dan membatalkan
    $previouslyCancelled = MabarParticipant::where('open_mabar_id', $id)
        ->where('user_id', Auth::id())
        ->where('status', 'cancelled')
        ->first();

    try {
        DB::beginTransaction();

        if ($previouslyCancelled) {
            // Update status participant yang sebelumnya
            $previouslyCancelled->status = 'joined';
            $previouslyCancelled->payment_status = 'pending';
            $previouslyCancelled->save();
        } else {
            // Buat entri participant baru
            $participant = new MabarParticipant();
            $participant->open_mabar_id = $openMabar->id;
            $participant->user_id = Auth::id();
            $participant->status = 'joined';
            $participant->payment_status = 'pending';
            $participant->payment_method = 'cash';
            $participant->amount_paid = $openMabar->price_per_slot;
            $participant->save();
        }

        // Update jumlah filled_slots
        $openMabar->filled_slots += 1;

        // Periksa apakah sudah penuh
        if ($openMabar->filled_slots >= $openMabar->total_slots) {
            $openMabar->status = 'full';
        }

        $openMabar->save();

        DB::commit();

        return redirect()->route('user.mabar.show', $id)
            ->with('success', 'Anda berhasil bergabung dengan Open Mabar ini! Silakan lakukan pembayaran secara langsung saat tiba di lapangan.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error joining Open Mabar: ' . $e->getMessage());

        return back()->with('error', 'Terjadi kesalahan saat bergabung dengan Open Mabar. Silakan coba lagi.');
    }
}

/**
 * Batalkan keikutsertaan dalam open mabar
 */
public function cancel($id)
{
    $openMabar = OpenMabar::findOrFail($id);

    // Cek apakah user memang terdaftar sebagai peserta
    $participant = MabarParticipant::where('open_mabar_id', $id)
        ->where('user_id', Auth::id())
        ->where('status', '!=', 'cancelled') // Tambahkan kondisi ini
        ->first();

    if (!$participant) {
        return back()->with('error', 'Anda tidak terdaftar sebagai peserta di Open Mabar ini atau sudah membatalkan keikutsertaan sebelumnya.');
    }

    try {
        DB::beginTransaction();

        // Update status participant
        $participant->status = 'cancelled';
        $participant->save();

        // Update jumlah filled_slots, pastikan tidak negatif
        if ($openMabar->filled_slots > 0) {
            $openMabar->filled_slots -= 1;
        }

        // Jika sebelumnya full, kembalikan ke open
        if ($openMabar->status == 'full') {
            $openMabar->status = 'open';
        }

        $openMabar->save();

        DB::commit();

        return redirect()->route('user.mabar.show', $id)
            ->with('success', 'Anda telah membatalkan keikutsertaan dari Open Mabar ini.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error cancelling Open Mabar participation: ' . $e->getMessage());

        return back()->with('error', 'Terjadi kesalahan saat membatalkan keikutsertaan. Silakan coba lagi.');
    }
}

    /**
     * Tandai peserta sebagai hadir (untuk pembuat mabar)
     */
    public function markAttended(Request $request, $mabarId, $participantId)
    {
        // Cek apakah user adalah pembuat mabar
        $openMabar = OpenMabar::where('id', $mabarId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Cek peserta
        $participant = MabarParticipant::where('id', $participantId)
            ->where('open_mabar_id', $mabarId)
            ->firstOrFail();

        try {
            // Update status peserta
            $participant->status = 'attended';
            $participant->payment_status = 'paid';
            $participant->save();

            return back()->with('success', 'Status kehadiran dan pembayaran peserta berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error marking attendance: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat memperbarui status peserta.');
        }
    }

    /**
     * Menampilkan daftar mabar yang diikuti oleh user
     */
    public function myMabars()
    {
        // Ambil mabar yang dibuat oleh user
        $createdMabars = OpenMabar::with(['fieldBooking', 'fieldBooking.field'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil mabar yang diikuti oleh user
        $joinedMabars = MabarParticipant::with(['openMabar', 'openMabar.fieldBooking', 'openMabar.fieldBooking.field'])
            ->where('user_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get()
            ->pluck('openMabar');

        return view('users.mabar.my-mabars', compact('createdMabars', 'joinedMabars'));
    }
}
