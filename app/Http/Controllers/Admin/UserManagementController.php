<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Photographer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            $users = User::select('*');

            return DataTables::of($users)
                ->addColumn('action', function ($user) {
                    return '<div class="d-flex gap-1">
                            <a href="' . route('admin.users.show', $user->id) . '" class="btn btn-sm btn-info">Detail</a>
                            <a href="' . route('admin.users.edit', $user->id) . '" class="btn btn-sm btn-warning">Edit</a>
                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $user->id . '" data-name="' . $user->name . '">Hapus</button>
                        </div>';
                })
                ->editColumn('role', function ($user) {
                    $badgeClass = '';
                    switch ($user->role) {
                        case 'owner':
                            $badgeClass = 'bg-primary';
                            break;
                        case 'admin':
                            $badgeClass = 'bg-success';
                            break;
                        case 'photographer':
                            $badgeClass = 'bg-info';
                            break;
                        default:
                            $badgeClass = 'bg-secondary';
                    }
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($user->role) . '</span>';
                })
                ->editColumn('created_at', function ($user) {
                    return $user->created_at->format('d M Y H:i');
                })
                ->editColumn('profile_picture', function ($user) {
                    if ($user->profile_picture) {
                        return '<img src="' . asset('storage/' . $user->profile_picture) . '" alt="Profile" class="img-thumbnail" width="50">';
                    }
                    return '<span class="badge bg-secondary">No Image</span>';
                })
                ->rawColumns(['action', 'role', 'profile_picture'])
                ->make(true);
        }

        return view('admin.users.index');
    }

    /**
     * Menampilkan form tambah pengguna
     */
    public function create()
    {
        return view('admin.users.create');
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

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan');
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

        return view('admin.users.show', compact('user', 'photographerData'));
    }

    /**
     * Menampilkan form edit pengguna
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
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

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui');
    }

    /**
     * Menghapus pengguna
     */
    public function destroy(User $user)
    {
        try {
            // Cek jika user adalah owner atau admin terakhir
            if ($user->role === 'owner' && User::where('role', 'owner')->count() <= 1) {
                return redirect()->route('admin.users.index')->with('error', 'Tidak dapat menghapus owner terakhir');
            }

            if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
                return redirect()->route('admin.users.index')->with('error', 'Tidak dapat menghapus admin terakhir');
            }

            // Hapus profile picture jika ada
            if ($user->profile_picture && Storage::exists('public/' . $user->profile_picture)) {
                Storage::delete('public/' . $user->profile_picture);
            }

            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Tidak dapat menghapus pengguna');
        }
    }
}
