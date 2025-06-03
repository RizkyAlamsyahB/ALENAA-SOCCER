<?php

namespace App\Http\Controllers\User;

use App\Models\Review;
use App\Models\Payment;
use App\Models\Field;
use App\Models\RentalItem;
use App\Models\Photographer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    /**
     * Store a new review
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'payment_id' => 'required|exists:payments,id',
                'item_id' => 'required|integer',
                'item_type' => 'required|in:field,rental_item,photographer',
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:1000',
            ]);

            // Periksa payment dan status
            $payment = Payment::where('id', $request->payment_id)->where('user_id', Auth::id())->first();

            if (!$payment) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Pembayaran tidak ditemukan',
                        'redirect' => route('user.payment.detail', $request->payment_id),
                    ],
                    404,
                );
            }

            if ($payment->transaction_status !== 'success') {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Hanya pembayaran yang berhasil yang dapat direview',
                        'redirect' => route('user.payment.detail', $request->payment_id),
                    ],
                    400,
                );
            }

            // Periksa apakah item ada berdasarkan tipe
            $itemType = $request->item_type;
            $itemId = $request->item_id;
            $itemModel = null;
            $modelClass = null;

            if ($itemType === 'field') {
                $itemModel = Field::find($itemId);
                $modelClass = 'App\\Models\\Field';
            } elseif ($itemType === 'rental_item') {
                $itemModel = RentalItem::find($itemId);
                $modelClass = 'App\\Models\\RentalItem';
            } elseif ($itemType === 'photographer') {
                $itemModel = Photographer::find($itemId);
                $modelClass = 'App\\Models\\Photographer';
            }

            if (!$itemModel) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Item tidak ditemukan',
                        'redirect' => route('user.payment.detail', $request->payment_id),
                    ],
                    404,
                );
            }

            // Periksa apakah user sudah pernah review item ini untuk payment ini
            $existingReview = Review::where('user_id', Auth::id())->where('item_id', $itemId)->where('item_type', $modelClass)->where('payment_id', $payment->id)->first();

            if ($existingReview) {
                // Update review yang sudah ada
                $existingReview->rating = $request->rating;
                $existingReview->comment = $request->comment;
                $existingReview->save();

                // Set session alert untuk update
                session()->flash('alert', [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'message' => 'Review Anda berhasil diperbarui.',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Review berhasil diperbarui',
                    'review' => $existingReview,
                    'redirect' => route('user.payment.detail', $request->payment_id),
                ]);
            }

            // Buat review baru
            $review = Review::create([
                'user_id' => Auth::id(),
                'item_id' => $itemId,
                'item_type' => $modelClass,
                'payment_id' => $payment->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'status' => 'active',
            ]);

            // Set session alert untuk review baru
            session()->flash('alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'message' => 'Review kamu sudah kami terima, terima kasih!',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Review berhasil ditambahkan',
                'review' => $review,
                'redirect' => route('user.payment.detail', $request->payment_id),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Set session alert untuk validation error
            session()->flash('alert', [
                'type' => 'danger',
                'title' => 'Error Validasi!',
                'message' => 'Data yang dimasukkan tidak valid. Silakan periksa kembali.',
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $e->errors(),
                    'redirect' => route('user.payment.detail', $request->payment_id),
                ],
                422,
            );
        } catch (\Exception $e) {
            Log::error('Error adding review: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            // Set session alert untuk general error
            session()->flash('alert', [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => 'Terjadi kesalahan saat mengirim review. Silakan coba lagi.',
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                    'redirect' => route('user.payment.detail', $request->payment_id),
                ],
                500,
            );
        }
    }

    /**
     * Get reviews for an item
     */
    public function getItemReviews(Request $request)
    {
        try {
            $request->validate([
                'item_id' => 'required|integer',
                'item_type' => 'required|in:field,rental_item,photographer',
            ]);

            $itemType = $request->item_type;
            $itemId = $request->item_id;
            $modelClass = null;

            if ($itemType === 'field') {
                $modelClass = 'App\\Models\\Field';
            } elseif ($itemType === 'rental_item') {
                $modelClass = 'App\\Models\\RentalItem';
            } elseif ($itemType === 'photographer') {
                $modelClass = 'App\\Models\\Photographer';
            }

            $reviews = Review::with('user')->where('item_id', $itemId)->where('item_type', $modelClass)->where('status', 'active')->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'reviews' => $reviews,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting reviews: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
