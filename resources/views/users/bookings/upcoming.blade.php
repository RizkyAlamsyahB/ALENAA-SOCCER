@extends('layouts.app')

@section('title', 'Booking Mendatang')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">📅 Booking Mendatang</h1>
                    <p class="text-gray-600">Lihat semua booking Anda dalam 7 hari ke depan</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Update terakhir</div>
                    <div class="text-lg font-semibold text-gray-900">{{ now()->format('d M Y, H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Reminder Preferences -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">🔔 Pengaturan Reminder</h2>
            <form action="{{ route('user.bookings.update.reminder.preferences') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input
                            type="checkbox"
                            name="reminder_24hours"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            {{ auth()->user()->reminder_24hours ?? true ? 'checked' : '' }}
                        >
                        <span class="text-sm text-gray-700">Reminder 24 jam sebelumnya</span>
                    </label>

                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input
                            type="checkbox"
                            name="reminder_1hour"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            {{ auth()->user()->reminder_1hour ?? true ? 'checked' : '' }}
                        >
                        <span class="text-sm text-gray-700">Reminder 1 jam sebelumnya</span>
                    </label>

                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input
                            type="checkbox"
                            name="reminder_30minutes"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            {{ auth()->user()->reminder_30minutes ?? true ? 'checked' : '' }}
                        >
                        <span class="text-sm text-gray-700">Reminder 30 menit sebelumnya</span>
                    </label>
                </div>
                <div class="mt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        💾 Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        <!-- Booking Timeline -->
        <div class="space-y-6">
            @php
                $allBookings = collect();

                // Gabungkan semua booking
                foreach($fieldBookings as $booking) {
                    $allBookings->push([
                        'type' => 'field',
                        'data' => $booking,
                        'start_time' => $booking->start_time,
                        'title' => $booking->field->name,
                        'icon' => '🏟️',
                        'color' => 'blue'
                    ]);
                }

                foreach($rentalBookings as $booking) {
                    $allBookings->push([
                        'type' => 'rental',
                        'data' => $booking,
                        'start_time' => $booking->start_time,
                        'title' => $booking->rentalItem->name,
                        'icon' => '🛍️',
                        'color' => 'green'
                    ]);
                }

                foreach($photographerBookings as $booking) {
                    $allBookings->push([
                        'type' => 'photographer',
                        'data' => $booking,
                        'start_time' => $booking->start_time,
                        'title' => $booking->photographer->name,
                        'icon' => '📸',
                        'color' => 'purple'
                    ]);
                }

                foreach($membershipSessions as $session) {
                    if($session->fieldBooking) {
                        $allBookings->push([
                            'type' => 'membership',
                            'data' => $session,
                            'start_time' => $session->start_time,
                            'title' => $session->fieldBooking->field->name . ' (Membership)',
                            'icon' => '🏆',
                            'color' => 'yellow'
                        ]);
                    }
                }

                // Urutkan berdasarkan waktu
                $allBookings = $allBookings->sortBy('start_time');
                $groupedBookings = $allBookings->groupBy(function($booking) {
                    return \Carbon\Carbon::parse($booking['start_time'])->format('Y-m-d');
                });
            @endphp

            @if($allBookings->isEmpty())
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                    <div class="text-6xl mb-4">📅</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak ada booking mendatang</h3>
                    <p class="text-gray-600 mb-4">Anda belum memiliki booking dalam 7 hari ke depan</p>
                    <a href="{{ route('user.fields.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                        🏟️ Booking Lapangan Sekarang
                    </a>
                </div>
            @else
                @foreach($groupedBookings as $date => $bookings)
                    @php
                        $carbonDate = \Carbon\Carbon::parse($date);
                        $isToday = $carbonDate->isToday();
                        $isTomorrow = $carbonDate->isTomorrow();
                    @endphp

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Date Header -->
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="text-2xl">
                                        @if($isToday) 🎯
                                        @elseif($isTomorrow) 📍
                                        @else 📅
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $carbonDate->format('d F Y') }}
                                            @if($isToday)
                                                <span class="text-blue-600 font-bold">(Hari Ini)</span>
                                            @elseif($isTomorrow)
                                                <span class="text-orange-600 font-bold">(Besok)</span>
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-600">{{ $carbonDate->format('l') }} • {{ $bookings->count() }} booking</p>
                                    </div>
                                </div>

                                @if($isToday || $isTomorrow)
                                    <div class="text-right">
                                        @php
                                            $nextBooking = $bookings->first();
                                            $timeUntil = \Carbon\Carbon::now()->diffForHumans(\Carbon\Carbon::parse($nextBooking['start_time']), true);
                                        @endphp
                                        <div class="text-sm text-gray-500">Booking pertama</div>
                                        <div class="text-lg font-semibold text-gray-900">{{ $timeUntil }} lagi</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Bookings List -->
                        <div class="p-6 space-y-4">
                            @foreach($bookings as $booking)
                                @php
                                    $data = $booking['data'];
                                    $startTime = \Carbon\Carbon::parse($booking['start_time']);
                                    $endTime = \Carbon\Carbon::parse($data->end_time);
                                    $duration = $startTime->diffInHours($endTime);
                                    $price = $data->total_price ?? $data->price ?? 0;
                                @endphp

                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between">
                                        <!-- Booking Info -->
                                        <div class="flex items-start space-x-4 flex-1">
                                            <div class="text-2xl">{{ $booking['icon'] }}</div>
                                            <div class="flex-1">
                                                <h4 class="text-lg font-semibold text-gray-900 mb-1">
                                                    {{ $booking['title'] }}
                                                </h4>

                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm text-gray-600">
                                                    <div class="flex items-center space-x-2">
                                                        <span>🕐</span>
                                                        <span>{{ $startTime->format('H:i') }} - {{ $endTime->format('H:i') }} WIB</span>
                                                    </div>

                                                    <div class="flex items-center space-x-2">
                                                        <span>⏱️</span>
                                                        <span>{{ $duration }} jam</span>
                                                    </div>

                                                    <div class="flex items-center space-x-2">
                                                        <span>💰</span>
                                                        <span>Rp {{ number_format($price, 0, ',', '.') }}</span>
                                                    </div>
                                                </div>

                                                <!-- Additional Info -->
                                                @if($booking['type'] === 'rental' && isset($data->quantity))
                                                    <div class="mt-2 text-sm text-gray-600">
                                                        <span class="flex items-center space-x-2">
                                                            <span>📦</span>
                                                            <span>Jumlah: {{ $data->quantity }} unit</span>
                                                        </span>
                                                    </div>
                                                @endif

                                                @if($booking['type'] === 'field' && isset($data->field->address))
                                                    <div class="mt-2 text-sm text-gray-600">
                                                        <span class="flex items-center space-x-2">
                                                            <span>📍</span>
                                                            <span>{{ $data->field->address }}</span>
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Status & Actions -->
                                        <div class="text-right">
                                            <!-- Reminder Status -->
                                            <div class="mb-3 space-y-1">
                                                @php
                                                    $reminderStatus = [
                                                        '24h' => $data->reminder_sent_24hours ?? false,
                                                        '1h' => $data->reminder_sent_1hour ?? false,
                                                        '30m' => $data->reminder_sent_30minutes ?? false
                                                    ];
                                                @endphp

                                                <div class="text-xs text-gray-500">Reminder Status:</div>
                                                <div class="flex space-x-1">
                                                    @foreach($reminderStatus as $time => $sent)
                                                        <span class="inline-block w-6 h-6 rounded-full text-xs flex items-center justify-center {{ $sent ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                                            {{ $sent ? '✓' : '○' }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                                <div class="text-xs text-gray-400">24h 1h 30m</div>
                                            </div>

                                            <!-- Time Until -->
                                            @php
                                                $now = \Carbon\Carbon::now();
                                                $timeUntilBooking = $now->diffForHumans($startTime, true);
                                                $isUpcoming = $startTime->isFuture();
                                            @endphp

                                            @if($isUpcoming)
                                                <div class="text-sm">
                                                    <div class="text-gray-500">Dimulai dalam</div>
                                                    <div class="font-semibold text-gray-900">{{ $timeUntilBooking }}</div>
                                                </div>
                                            @else
                                                <div class="text-sm text-red-600 font-semibold">
                                                    Sedang berlangsung
                                                </div>
                                            @endif

                                            <!-- Quick Actions -->
                                            <div class="mt-3 space-y-2">
                                                <a href="{{ route('user.payment.history') }}"
                                                   class="block text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded transition-colors">
                                                    📋 Detail
                                                </a>

                                                @if($booking['type'] === 'field' && isset($data->field))
                                                    <a href="{{ route('user.fields.show', $data->field->id) }}"
                                                       class="block text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded transition-colors">
                                                        🏟️ Lihat Lapangan
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Quick Stats -->
        @if(!$allBookings->isEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Ringkasan Booking</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $fieldBookings->count() }}</div>
                        <div class="text-sm text-gray-600">Lapangan</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $rentalBookings->count() }}</div>
                        <div class="text-sm text-gray-600">Penyewaan</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $photographerBookings->count() }}</div>
                        <div class="text-sm text-gray-600">Fotografer</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600">{{ $membershipSessions->count() }}</div>
                        <div class="text-sm text-gray-600">Membership</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    // Auto refresh halaman setiap 5 menit untuk update status reminder
    setTimeout(function() {
        location.reload();
    }, 300000); // 5 menit
</script>
@endsection
