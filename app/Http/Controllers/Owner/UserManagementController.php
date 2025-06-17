<?php

namespace App\Http\Controllers\Owner;

use App\Models\User;
use App\Models\Field;
use App\Models\Membership;
use App\Models\Photographer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\PhotographerBooking;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class UserManagementController extends Controller
{
    /**
     * Menampilkan daftar pengguna dengan server-side processing
     */
public function index(Request $request)
{
    if ($request->ajax()) {
        // Ambil semua user kecuali yang memiliki role 'user'
        $users = User::where('role', '!=', 'user')->select('*');

        return DataTables::of($users)
            ->addColumn('action', function ($user) {
                return '<div class="d-flex gap-1">
                        <a href="' . route('owner.users.show', $user->id) . '" class="btn btn-sm btn-info">Detail</a>
                        <a href="' . route('owner.users.edit', $user->id) . '" class="btn btn-sm btn-warning">Edit</a>
                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $user->id . '" data-name="' . $user->name . '">Hapus</button>
                    </div>';
            })
            ->editColumn('role', function ($user) {
                $badgeClass = match ($user->role) {
                    'owner' => 'bg-primary',
                    'admin' => 'bg-success',
                    'photographer' => 'bg-info',
                    default => 'bg-secondary',
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($user->role) . '</span>';
            })
            ->editColumn('created_at', fn($user) => $user->created_at->format('d M Y H:i'))
            ->editColumn('profile_picture', function ($user) {
                return $user->profile_picture
                    ? '<img src="' . asset('storage/' . $user->profile_picture) . '" alt="Profile" class="img-thumbnail" width="50">'
                    : '<span class="badge bg-secondary">No Image</span>';
            })
            ->rawColumns(['action', 'role', 'profile_picture'])
            ->make(true);
    }

    return view('owner.users.index');
}


    /**
     * Menampilkan form tambah pengguna
     */
    public function create()
    {
        return view('owner.users.create');
    }

    /**
     * Menyimpan pengguna baru
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['owner', 'admin', 'user', 'photographer'])],
            'phone_number' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'points' => 'nullable|integer|min:0',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Set default points jika tidak diisi
        if (!isset($validatedData['points'])) {
            $validatedData['points'] = 0;
        }

        // Enkripsi password
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Set email_verified_at
        $validatedData['email_verified_at'] = now();

        // Upload profile picture jika ada
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validatedData['profile_picture'] = $path;
        }

        User::create($validatedData);

        return redirect()->route('owner.users.index')->with('success', 'Pengguna berhasil ditambahkan');
    }

    /**
     * Menampilkan detail pengguna
     */
    public function show(User $user)
    {
        $photographerData = null;

        if ($user->role == 'photographer') {
            // Ambil semua data fotografer untuk user ini
            $photographerData = Photographer::where('user_id', $user->id)->get();
        }

        return view('owner.users.show', compact('user', 'photographerData'));
    }

    /**
     * Menampilkan form edit pengguna
     */
    public function edit(User $user)
    {
        return view('owner.users.edit', compact('user'));
    }

    /**
     * Memperbarui data pengguna
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(['owner', 'admin', 'user', 'photographer'])],
            'phone_number' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'points' => 'nullable|integer|min:0',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update password hanya jika diisi
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        // Upload profile picture jika ada
        if ($request->hasFile('profile_picture')) {
            // Hapus gambar lama jika ada
            if ($user->profile_picture && Storage::exists('public/' . $user->profile_picture)) {
                Storage::delete('public/' . $user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validatedData['profile_picture'] = $path;
        }

        $user->update($validatedData);

        return redirect()->route('owner.users.index')->with('success', 'Pengguna berhasil diperbarui');
    }

/**
 * Menghapus pengguna (hanya administrator dan fotografer)
 */
public function destroy(User $user)
{
    try {
        // Cek apakah role user diizinkan untuk dihapus
        if ($user->role === 'owner' || $user->role === 'user') {
            return redirect()->route('owner.users.index')->with('error', 'Pengguna dengan role ' . $user->role . ' tidak dapat dihapus dari sistem');
        }

        // Cek jika user adalah admin terakhir
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('owner.users.index')->with('error', 'Tidak dapat menghapus admin terakhir');
        }

        // Pengecekan khusus untuk fotografer
        if ($user->role === 'photographer') {
            // Cek jika fotografer sedang memiliki booking aktif atau masa depan
            $photographer = Photographer::where('user_id', $user->id)->first();

            if ($photographer) {
                // Cek apakah fotografer memiliki booking yang belum selesai
                $hasActiveBookings = PhotographerBooking::where('photographer_id', $photographer->id)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->whereDate('end_time', '>=', now())
                    ->exists();

                if ($hasActiveBookings) {
                    return redirect()->route('owner.users.index')->with('error', 'Tidak dapat menghapus fotografer. Fotografer memiliki booking yang masih aktif.');
                }

                // Cek apakah fotografer menangani lapangan
                $hasAssignedFields = Field::where('photographer_id', $photographer->id)->exists();

                if ($hasAssignedFields) {
                    return redirect()->route('owner.users.index')->with('error', 'Tidak dapat menghapus fotografer. Fotografer masih ditugaskan ke satu atau lebih lapangan.');
                }

                // Cek jika fotografer digunakan dalam paket membership
                $usedInMembership = Membership::where('photographer_id', $photographer->id)
                    ->where('includes_photographer', true)
                    ->exists();

                if ($usedInMembership) {
                    return redirect()->route('owner.users.index')->with('error', 'Tidak dapat menghapus fotografer. Fotografer masih digunakan dalam paket membership.');
                }
            }
        }

        // Hapus profile picture jika ada
        if ($user->profile_picture && Storage::exists('public/' . $user->profile_picture)) {
            Storage::delete('public/' . $user->profile_picture);
        }

        // Untuk fotografer, hapus juga data fotografer terkait
        if ($user->role === 'photographer') {
            $photographer = Photographer::where('user_id', $user->id)->first();
            if ($photographer) {
                // Hapus foto profil fotografer jika ada
                if ($photographer->photo && Storage::exists('public/' . $photographer->photo)) {
                    Storage::delete('public/' . $photographer->photo);
                }

                // Hapus data fotografer
                $photographer->delete();
            }
        }

        $user->delete();
        return redirect()->route('owner.users.index')->with('success', 'Pengguna berhasil dihapus');
    } catch (\Exception $e) {
        Log::error('Error deleting user: ' . $e->getMessage(), [
            'user_id' => $user->id,
            'role' => $user->role,
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->route('owner.users.index')
            ->with('error', 'Tidak dapat menghapus pengguna. Error: ' . $e->getMessage());
    }
}
public function customers(Request $request)
{
    if ($request->ajax()) {
        // Ambil semua user dengan role 'user'
        $users = User::where('role', 'user')->select('*');

        return DataTables::of($users)
            ->editColumn('role', function ($user) {
                $badgeClass = match ($user->role) {
                    'owner' => 'bg-primary',
                    'admin' => 'bg-success',
                    'photographer' => 'bg-info',
                    default => 'bg-secondary',
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($user->role) . '</span>';
            })
            ->editColumn('created_at', fn($user) => $user->created_at->format('d M Y H:i'))
            ->editColumn('profile_picture', function ($user) {
                return $user->profile_picture
                    ? '<img src="' . asset('storage/' . $user->profile_picture) . '" alt="Profile" class="img-thumbnail" width="50">'
                    : '<span class="badge bg-secondary">No Image</span>';
            })
            ->rawColumns(['role', 'profile_picture'])
            ->make(true);
    }

    return view('owner.users.customers');
}

}
