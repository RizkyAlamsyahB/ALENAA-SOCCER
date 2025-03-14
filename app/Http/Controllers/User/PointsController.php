<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\PointsTransaction;

class PointsController extends Controller
{
    /**
     * Display points dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $pointsHistory = PointsTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('users.points.index', compact('user', 'pointsHistory'));
    }

    /**
     * Redeem points for rewards
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'reward_id' => 'required|exists:rewards,id',
        ]);

        $user = Auth::user();
        $reward = Reward::findOrFail($request->reward_id);

        if ($user->points < $reward->points_cost) {
            return back()->with('error', 'Points Anda tidak mencukupi.');
        }

        // Kurangi points user
        $user->points -= $reward->points_cost;
        $user->save();

        // Catat transaksi points
        PointsTransaction::create([
            'user_id' => $user->id,
            'type' => 'redeem',
            'amount' => -$reward->points_cost,
            'description' => 'Redeem untuk ' . $reward->name,
            'reference_id' => $reward->id,
            'reference_type' => 'App\Models\Reward'
        ]);

        // Buat reward redemption
        RewardRedemption::create([
            'user_id' => $user->id,
            'reward_id' => $reward->id,
            'points_used' => $reward->points_cost,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Berhasil menukarkan points untuk ' . $reward->name);
    }

    /**
     * Display points transaction history
     */
    public function history()
    {
        $user = Auth::user();
        $transactions = PointsTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('users.points.history', compact('user', 'transactions'));
    }
}
