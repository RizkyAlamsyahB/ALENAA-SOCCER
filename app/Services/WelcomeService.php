<?php

namespace App\Services;

use App\Models\Field;
use App\Models\Review;
use App\Models\Discount;
use App\Models\FieldBooking;
use App\Models\RentalBooking;
use App\Models\MembershipSubscription;

class WelcomeService
{
    public function getWelcomePageData()
    {
        return [
            'testimonials' => $this->getTestimonials(),
            'activeDiscounts' => $this->getActiveDiscounts(),
            'fieldCount' => Field::count(),
            'activeMemberCount' => $this->getActiveMemberCount(),
            'recentFieldBookings' => $this->getRecentFieldBookings(),
            'recentRentalBookings' => $this->getRecentRentalBookings(),
        ];
    }

    private function getTestimonials()
    {
        return Review::with(['user', 'reviewable'])
            ->where('rating', '>=', 4)
            ->whereNotNull('comment')
            ->where('status', 'active')
            ->orderByRaw('LENGTH(comment) DESC')
            ->limit(3)
            ->get();
    }

    private function getActiveDiscounts()
    {
        return Discount::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>', now());
            })
            ->orderBy('value', 'desc')
            ->limit(4)
            ->get();
    }

    private function getActiveMemberCount()
    {
        return MembershipSubscription::where('status', 'active')
            ->whereDate('end_date', '>=', now())
            ->distinct('user_id')
            ->count('user_id');
    }

    private function getRecentFieldBookings()
    {
        return FieldBooking::with(['field', 'user'])
            ->whereIn('status', ['confirmed'])
            ->orderBy('start_time', 'asc')
            ->limit(3)
            ->get();
    }

    private function getRecentRentalBookings()
    {
        return RentalBooking::with(['rentalItem'])
            ->whereIn('status', ['confirmed'])
            ->orderBy('start_time', 'asc')
            ->limit(3)
            ->get();
    }
}
