<?php

namespace App\Http\Controllers\Owner;

use App\Models\Review;
use App\Models\Field;
use App\Models\RentalItem;
use App\Models\Photographer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ReviewsController extends Controller
{
    /**
     * Menampilkan daftar semua review
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $reviews = Review::with(['user', 'payment'])->select('reviews.*');

            return DataTables::of($reviews)
                ->addColumn('action', function ($review) {
                    return '<div class="d-flex gap-1">
                            <a href="' .
                        route('owner.reviews.show', $review->id) .
                        '" class="btn btn-sm btn-info">Detail</a>
                            <button type="button" class="btn btn-sm btn-' .
                        ($review->status === 'active' ? 'warning' : 'success') .
                        ' toggle-btn"
                                data-id="' .
                        $review->id .
                        '"
                                data-status="' .
                        $review->status .
                        '">
                                ' .
                        ($review->status === 'active' ? 'Nonaktifkan' : 'Aktifkan') .
                        '
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' .
                        $review->id .
                        '">Hapus</button>
                        </div>';
                })
                ->addColumn('user_name', function ($review) {
                    return $review->user ? $review->user->name : 'User Tidak Ditemukan';
                })
                ->addColumn('item_name', function ($review) {
                    $itemClass = $review->item_type;
                    $item = $itemClass::find($review->item_id);

                    if ($item) {
                        return $item->name;
                    }

                    return 'Item Tidak Ditemukan';
                })
                ->addColumn('item_type_label', function ($review) {
                    $types = [
                        'App\\Models\\Field' => 'Lapangan',
                        'App\\Models\\RentalItem' => 'Penyewaan',
                        'App\\Models\\Photographer' => 'Fotografer',
                    ];

                    return $types[$review->item_type] ?? $review->item_type;
                })
                ->addColumn('payment_info', function ($review) {
                    if ($review->payment) {
                        return 'Order ID: ' . $review->payment->order_id . '<br>Total: Rp ' . number_format($review->payment->amount, 0, ',', '.');
                    }
                    return 'Pembayaran Tidak Ditemukan';
                })
                ->addColumn('rating_stars', function ($review) {
                    $stars = '';
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $review->rating) {
                            $stars .= '<i class="bi bi-star-fill text-warning"></i> ';
                        } else {
                            $stars .= '<i class="bi bi-star text-secondary"></i> ';
                        }
                    }
                    return $stars . ' (' . $review->rating . ')';
                })
                ->editColumn('status', function ($review) {
                    if ($review->status === 'active') {
                        return '<span class="badge bg-success">Aktif</span>';
                    } else {
                        return '<span class="badge bg-danger">Nonaktif</span>';
                    }
                })
                ->editColumn('created_at', function ($review) {
                    return $review->created_at->format('d M Y H:i');
                })
                ->editColumn('updated_at', function ($review) {
                    return $review->updated_at->format('d M Y H:i');
                })
                ->rawColumns(['action', 'rating_stars', 'status', 'payment_info'])
                ->make(true);
        }

        return view('owner.reviews.index');
    }

    /**
     * Menampilkan detail review
     */
    public function show(Review $review)
    {
        // Load relasi
        $review->load(['user', 'payment']);

        // Dapatkan data item berdasarkan tipe
        $itemClass = $review->item_type;
        $item = $itemClass::find($review->item_id);

        return view('owner.reviews.show', compact('review', 'item'));
    }

    /**
     * Toggle status review (active/inactive)
     */
    public function toggleStatus(Request $request, Review $review)
    {
        try {
            $newStatus = $review->status === 'active' ? 'inactive' : 'active';
            $review->status = $newStatus;
            $review->save();

            return response()->json([
                'success' => true,
                'message' => 'Status review berhasil diubah menjadi ' . ($newStatus === 'active' ? 'aktif' : 'nonaktif'),
                'new_status' => $newStatus,
                'new_status_label' => $newStatus === 'active' ? 'Aktif' : 'Nonaktif',
                'new_button_text' => $newStatus === 'active' ? 'Nonaktifkan' : 'Aktifkan',
                'new_button_class' => $newStatus === 'active' ? 'warning' : 'success',
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling review status: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Menghapus review
     */
    public function destroy(Review $review)
    {
        try {
            $review->delete();

            return redirect()->route('owner.reviews.index')->with('success', 'Review berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting review: ' . $e->getMessage());

            return redirect()
                ->route('owner.reviews.index')
                ->with('error', 'Gagal menghapus review: ' . $e->getMessage());
        }
    }

    /**
     * Dapatkan review untuk item tertentu
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
                $item = Field::findOrFail($itemId);
            } elseif ($itemType === 'rental_item') {
                $modelClass = 'App\\Models\\RentalItem';
                $item = RentalItem::findOrFail($itemId);
            } elseif ($itemType === 'photographer') {
                $modelClass = 'App\\Models\\Photographer';
                $item = Photographer::findOrFail($itemId);
            }

            $reviews = Review::with('user')->where('item_id', $itemId)->where('item_type', $modelClass)->orderBy('created_at', 'desc')->paginate(10);

            return view('owner.reviews.item-reviews', compact('reviews', 'item', 'itemType'));
        } catch (\Exception $e) {
            Log::error('Error getting item reviews: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Gagal mendapatkan review: ' . $e->getMessage());
        }
    }

    /**
     * Dashboard review summary
     */
    public function reviewSummary()
    {
        try {
            // Statistik umum
            $totalReviews = Review::count();
            $activeReviews = Review::where('status', 'active')->count();
            $avgRating = Review::avg('rating');

            // Review terbaru
            $latestReviews = Review::with(['user'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Rating per kategori
            $fieldRatings = Review::where('item_type', 'App\\Models\\Field')->avg('rating');

            $rentalRatings = Review::where('item_type', 'App\\Models\\RentalItem')->avg('rating');

            $photographerRatings = Review::where('item_type', 'App\\Models\\Photographer')->avg('rating');

            // Distribusi rating
            $ratingDistribution = [];
            for ($i = 1; $i <= 5; $i++) {
                $ratingDistribution[$i] = Review::where('rating', $i)->count();
            }

            return view('owner.reviews.summary', compact('totalReviews', 'activeReviews', 'avgRating', 'latestReviews', 'fieldRatings', 'rentalRatings', 'photographerRatings', 'ratingDistribution'));
        } catch (\Exception $e) {
            Log::error('Error getting review summary: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Gagal mendapatkan ringkasan review: ' . $e->getMessage());
        }
    }
}
