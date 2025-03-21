<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\MembershipSubscription;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Ambil riwayat transaksi untuk menampilkan perolehan points
        $recentPayments = Payment::where('user_id', $user->id)
            ->where('transaction_status', 'success')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Ambil data membership aktif user
        $activeMembership = MembershipSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('membership') // Load relasi membership
            ->orderBy('created_at', 'desc')
            ->first();

        // Default tipe membership jika tidak memiliki membership aktif
        $membershipType = 'bronze';
        $membershipName = 'Belum Memiliki Membership';

        if ($activeMembership && $activeMembership->membership) {
            $membershipType = $activeMembership->membership->type ?? 'bronze';
            $membershipName = $activeMembership->membership->name ?? 'Membership';
        }

        return view('profile.edit', [
            'user' => $user,
            'recentPayments' => $recentPayments,
            'activeMembership' => $activeMembership,
            'membershipType' => $membershipType,
            'membershipName' => $membershipName,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Upload and update user's profile picture.
     */
    public function updateProfilePicture(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        // Delete old profile picture if exists
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Store the new image
        $imagePath = $request->file('profile_picture')->store('profile-pictures', 'public');

        // Update user profile
        $user->profile_picture = $imagePath;
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-picture-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Delete profile picture if exists
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
