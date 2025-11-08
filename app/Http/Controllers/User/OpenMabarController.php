<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Field;
use App\Models\OpenMabar;
use App\Helpers\EmailHelper;
use App\Mail\MabarBroadcast;
use App\Mail\MabarCancelled;
use App\Models\FieldBooking;
use App\Models\MabarMessage;
use Illuminate\Http\Request;
use App\Models\MabarParticipant;
use App\Models\MembershipSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
            $query->where('title', 'like', "%{$search}%")->orWhereHas('fieldBooking.field', function ($q) use ($search) {
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
        $openMabar = OpenMabar::with(['fieldBooking', 'fieldBooking.field', 'user', 'participants', 'participants.user'])->findOrFail($id);

        // Cek apakah user saat ini sudah bergabung ke mabar ini
        $isJoined = false;
        $userParticipant = null;

        if (Auth::check()) {
            $userParticipant = MabarParticipant::where('open_mabar_id', $id)->where('user_id', Auth::id())->first();

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
        $usedBookingIds = OpenMabar::whereIn('field_booking_id', $fieldBookings->pluck('id'))->pluck('field_booking_id')->toArray();

        // Filter booking yang belum digunakan untuk open mabar
        $availableBookings = $fieldBookings->filter(function ($booking) use ($usedBookingIds) {
            return !in_array($booking->id, $usedBookingIds);
        });

        // Jika tidak ada booking yang tersedia
        if ($availableBookings->isEmpty()) {
            return redirect()->route('user.mabar.index')->with('error', 'Anda tidak memiliki booking lapangan yang aktif dan tersedia untuk membuat Open Mabar. Silakan booking lapangan terlebih dahulu.');
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
        $fieldBooking = FieldBooking::where('id', $request->field_booking_id)->where('user_id', Auth::id())->where('status', 'confirmed')->where('end_time', '>', now())->firstOrFail();

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

            return redirect()->route('user.mabar.show', $openMabar->id)->with('success', 'Open Mabar berhasil dibuat! Sekarang Anda dapat mengundang pemain lain untuk bergabung.');
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
        $existingActiveParticipant = MabarParticipant::where('open_mabar_id', $id)->where('user_id', Auth::id())->where('status', '!=', 'cancelled')->first();

        if ($existingActiveParticipant) {
            return back()->with('info', 'Anda sudah terdaftar sebagai peserta Open Mabar ini.');
        }
// Cek apakah user sudah terdaftar di mabar lain pada waktu yang sama
$overlappingMabars = MabarParticipant::join('open_mabars', 'mabar_participants.open_mabar_id', '=', 'open_mabars.id')
    ->where('mabar_participants.user_id', Auth::id())
    ->where('mabar_participants.status', '!=', 'cancelled')
    ->where(function ($query) use ($openMabar) {
        // Cek overlap waktu
        // Mabar 1 mulai sebelum Mabar 2 selesai DAN Mabar 1 selesai setelah Mabar 2 mulai
        $query->where(function ($q) use ($openMabar) {
            $q->where('open_mabars.start_time', '<', $openMabar->end_time)
              ->where('open_mabars.end_time', '>', $openMabar->start_time);
        });
    })
    ->first();

if ($overlappingMabars) {
    return back()->with('error', 'Anda sudah terdaftar pada Open Mabar lain yang jadwalnya bentrok.');
}
        // Cek apakah user pernah bergabung sebelumnya dan membatalkan
        $previouslyCancelled = MabarParticipant::where('open_mabar_id', $id)->where('user_id', Auth::id())->where('status', 'cancelled')->first();

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

            return redirect()->route('user.mabar.show', $id)->with('success', 'Anda berhasil bergabung dengan Open Mabar ini! Silakan lakukan pembayaran secara langsung saat tiba di lapangan.');
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

            return redirect()->route('user.mabar.show', $id)->with('success', 'Anda telah membatalkan keikutsertaan dari Open Mabar ini.');
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
        $openMabar = OpenMabar::where('id', $mabarId)->where('user_id', Auth::id())->firstOrFail();

        // Cek peserta
        $participant = MabarParticipant::where('id', $participantId)->where('open_mabar_id', $mabarId)->firstOrFail();

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

    /**
     * Menampilkan obrolan grup mabar
     */
    public function showChat($id)
    {
        $openMabar = OpenMabar::with([
            'fieldBooking',
            'fieldBooking.field',
            'user',
            'participants' => function ($query) {
                $query->where('status', '!=', 'cancelled');
            },
            'participants.user',
            'messages',
            'messages.user',
        ])->findOrFail($id);

        // Cek apakah user adalah pembuat atau peserta dari mabar ini
        $isParticipant = $openMabar->participants->where('user_id', Auth::id())->where('status', '!=', 'cancelled')->count() > 0;
        $isCreator = $openMabar->user_id === Auth::id();

        if (!$isParticipant && !$isCreator) {
            return redirect()->route('user.mabar.show', $id)->with('error', 'Anda tidak memiliki akses ke obrolan grup ini.');
        }

        return view('users.mabar.chat', compact('openMabar'));
    }

    /**
     * Menyimpan pesan baru
     */
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $openMabar = OpenMabar::findOrFail($id);

        // Cek apakah user adalah pembuat atau peserta dari mabar ini
        $isParticipant = MabarParticipant::where('open_mabar_id', $id)->where('user_id', Auth::id())->where('status', '!=', 'cancelled')->exists();
        $isCreator = $openMabar->user_id === Auth::id();

        if (!$isParticipant && !$isCreator) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke obrolan grup ini.',
                ],
                403,
            );
        }

        $message = new MabarMessage();
        $message->open_mabar_id = $id;
        $message->user_id = Auth::id();
        $message->message = $request->message;
        $message->save();

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim.',
            'data' => [
                'id' => $message->id,
                'user_name' => Auth::user()->name,
                'message' => $message->message,
                'created_at' => $message->created_at->format('d M Y H:i'),
            ],
        ]);
    }
    public function showBroadcastForm($id)
    {
        $openMabar = OpenMabar::with([
            'fieldBooking',
            'participants' => function ($query) {
                $query->where('status', '!=', 'cancelled');
            },
            'participants.user'
        ])->findOrFail($id);

        // Pastikan hanya pembuat yang bisa mengakses
        if ($openMabar->user_id !== Auth::id()) {
            return redirect()->route('user.mabar.show', $id)
                ->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }

        // Cek apakah ada peserta yang aktif
        $activeParticipants = $openMabar->participants->where('status', '!=', 'cancelled');
        if ($activeParticipants->isEmpty()) {
            return redirect()->route('user.mabar.show', $id)
                ->with('info', 'Belum ada peserta yang terdaftar pada Open Mabar ini. Tidak dapat mengirim broadcast.');
        }

        return view('users.mabar.broadcast', compact('openMabar'));
    }

    /**
     * Kirim pesan broadcast ke semua peserta
     */
    public function sendBroadcast(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:100',
            'message' => 'required|string',
        ]);

        $openMabar = OpenMabar::with([
            'fieldBooking',
            'fieldBooking.field',
            'participants' => function ($query) {
                $query->where('status', '!=', 'cancelled');
            },
            'participants.user',
        ])->findOrFail($id);

        // Pastikan hanya pembuat yang bisa mengakses
        if ($openMabar->user_id !== Auth::id()) {
            return redirect()->route('user.mabar.show', $id)->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }

        // Jika tidak ada peserta, kembalikan dengan pesan
        if ($openMabar->participants->isEmpty()) {
            return redirect()->route('user.mabar.show', $id)->with('info', 'Tidak ada peserta yang terdaftar untuk menerima pesan broadcast.');
        }

        // Kirim email ke semua peserta
        $subject = $request->subject;
        $message = $request->message;
        $eventDetails = 'Tanggal: ' . Carbon::parse($openMabar->start_time)->format('d M Y') . "\nWaktu: " . Carbon::parse($openMabar->start_time)->format('H:i') . ' - ' . Carbon::parse($openMabar->end_time)->format('H:i') . "\nLokasi: " . $openMabar->fieldBooking->field->name;

        $participantCount = 0;

        try {
            foreach ($openMabar->participants as $participant) {
                // Gunakan Mail facade untuk mengirim email
                Mail::to($participant->user->email)->send(new MabarBroadcast($openMabar, Auth::user(), $subject, $message, $eventDetails));

                $participantCount++;
            }

            // Log aktivitas broadcast
            Log::info('Broadcast message sent', [
                'mabar_id' => $openMabar->id,
                'sender_id' => Auth::id(),
                'recipient_count' => $participantCount,
                'subject' => $subject,
            ]);

            return redirect()
                ->route('user.mabar.show', $id)
                ->with('success', 'Pesan broadcast berhasil dikirim ke ' . $participantCount . ' peserta.');
        } catch (\Exception $e) {
            Log::error('Error sending broadcast message: ' . $e->getMessage(), [
                'mabar_id' => $openMabar->id,
                'sender_id' => Auth::id(),
            ]);

            return redirect()
                ->route('user.mabar.show', $id)
                ->with('error', 'Terjadi kesalahan saat mengirim pesan: ' . $e->getMessage());
        }
    }
/**
 * Hapus open mabar yang sudah dibuat dan kirim notifikasi ke peserta
 */
public function destroy(Request $request, $id)
{
    // Cek apakah open mabar ada dan milik user yang login
    $openMabar = OpenMabar::with([
            'fieldBooking',
            'fieldBooking.field',
            'participants' => function ($query) {
                $query->where('status', '!=', 'cancelled');
            },
            'participants.user'
        ])
        ->where('id', $id)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    // Cek apakah ada peserta yang sudah bergabung dan membayar
    $paidParticipants = $openMabar->participants
        ->where('payment_status', 'paid')
        ->count();

    if ($paidParticipants > 0 && !$request->has('force_delete')) {
        return back()->with('error', 'Open Mabar tidak dapat dihapus karena sudah ada peserta yang membayar. Jika tetap ingin menghapus, silakan berikan informasi refund kepada peserta.')
            ->with('show_force_delete_modal', true)
            ->with('mabar_id', $id);
    }

    try {
        DB::beginTransaction();

        // Dapatkan alasan pembatalan dan informasi refund jika ada
        $cancellationReason = $request->cancellation_reason ?? null;
        $refundInfo = $request->refund_info ?? null;

        // Jika ada peserta, kirim email notifikasi pembatalan
        $participantsCount = $openMabar->participants->count();
        Log::info('Starting to send cancellation emails', [
            'mabar_id' => $openMabar->id,
            'participants_count' => $participantsCount,
            'mabar_title' => $openMabar->title
        ]);

        $emailsSent = 0;
        $emailsFailed = 0;

        if ($participantsCount > 0) {
            foreach ($openMabar->participants as $participant) {
                try {
                    // Validasi email peserta terlebih dahulu
                    if (!filter_var($participant->user->email, FILTER_VALIDATE_EMAIL)) {
                        Log::warning('Invalid email format for participant', [
                            'mabar_id' => $openMabar->id,
                            'participant_id' => $participant->id,
                            'email' => $participant->user->email
                        ]);
                        $emailsFailed++;
                        continue;
                    }

                    // Log sebelum pengiriman email
                    Log::info('Attempting to send cancellation email', [
                        'mabar_id' => $openMabar->id,
                        'participant_id' => $participant->id,
                        'email' => $participant->user->email,
                        'user_name' => $participant->user->name
                    ]);

                    // Buat konten teks fallback
                    $textContent = "PEMBERITAHUAN: Open Mabar \"{$openMabar->title}\" telah dibatalkan.\n" .
                                   "Silakan cek email Anda untuk detail lebih lanjut atau hubungi " . Auth::user()->email;

                    // Coba metode 1: Menggunakan MabarCancelled Mail class
                    try {
                        Mail::to($participant->user->email)->send(
                            new MabarCancelled(
                                $openMabar,
                                Auth::user(),
                                $participant,
                                $cancellationReason,
                                $refundInfo
                            )
                        );

                        Log::info('Cancellation email sent successfully using Mail class', [
                            'mabar_id' => $openMabar->id,
                            'participant_id' => $participant->id,
                            'email' => $participant->user->email
                        ]);

                        $emailsSent++;
                    } catch (\Exception $e) {
                        Log::warning('Failed to send email using Mail class, trying alternative method: ' . $e->getMessage(), [
                            'mabar_id' => $openMabar->id,
                            'participant_id' => $participant->id,
                        ]);

                        // Metode 2: Menggunakan helper dengan retry
                        $subject = 'Open Mabar "' . $openMabar->title . '" Telah Dibatalkan';
                        $viewData = [
                            'openMabar' => $openMabar,
                            'organizer' => Auth::user(),
                            'participant' => $participant,
                            'cancellationReason' => $cancellationReason,
                            'refundInfo' => $refundInfo,
                        ];

                        $success = EmailHelper::sendWithFallback(
                            $participant->user->email,
                            $subject,
                            $textContent,
                            'emails.mabar-cancelled',
                            $viewData
                        );

                        if ($success) {
                            Log::info('Cancellation email sent successfully with fallback method', [
                                'mabar_id' => $openMabar->id,
                                'participant_id' => $participant->id,
                                'email' => $participant->user->email
                            ]);
                            $emailsSent++;
                        } else {
                            throw new \Exception('Both email methods failed');
                        }
                    }

                } catch (\Exception $e) {
                    // Log detail error saat mengirim email
                    Log::error('Error sending cancellation email: ' . $e->getMessage(), [
                        'mabar_id' => $openMabar->id,
                        'participant_id' => $participant->id,
                        'email' => $participant->user->email,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $emailsFailed++;
                    // Lanjutkan proses meskipun ada error email
                }
            }
        }

        // Ringkasan pengiriman email
        Log::info('Cancellation email summary', [
            'mabar_id' => $openMabar->id,
            'total_participants' => $participantsCount,
            'emails_sent' => $emailsSent,
            'emails_failed' => $emailsFailed
        ]);

        // Hapus semua pesan di mabar (jika ada)
        MabarMessage::where('open_mabar_id', $id)->delete();

        // Hapus semua peserta mabar (jika ada)
        MabarParticipant::where('open_mabar_id', $id)->delete();

        // Hapus open mabar
        $openMabar->delete();

        DB::commit();

        $message = 'Open Mabar berhasil dihapus';
        if ($participantsCount > 0) {
            if ($emailsSent > 0) {
                $message .= ' dan notifikasi telah dikirim ke ' . $emailsSent . ' dari ' . $participantsCount . ' peserta.';
                if ($emailsFailed > 0) {
                    $message .= ' ' . $emailsFailed . ' email gagal dikirim.';
                }
            } else {
                $message .= ' namun terjadi masalah saat mengirim email ke peserta.';
            }
        }

        return redirect()->route('user.mabar.index')
            ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting Open Mabar: ' . $e->getMessage(), [
                'mabar_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menghapus Open Mabar: ' . $e->getMessage());
        }
    }
}
