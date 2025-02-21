<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="flex space-x-4">
                {{-- <x-button-link href="/book-field" class="bg-red-600 hover:bg-red-700">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Book Field
                </x-button-link> --}}
                {{-- <x-button-link href="/my-bookings" class="bg-gray-600 hover:bg-gray-700">
                    <i class="fas fa-list mr-2"></i>
                    My Bookings
                </x-button-link> --}}
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total Bookings -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-red-100 rounded-full p-3">
                            <i class="fas fa-calendar text-red-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Bookings</p>
                            <p class="text-2xl font-semibold">12</p>
                        </div>
                    </div>
                </div>

                <!-- Points -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-star text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Points Earned</p>
                            <p class="text-2xl font-semibold">350</p>
                        </div>
                    </div>
                </div>

                <!-- Membership Status -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 rounded-full p-3">
                            <i class="fas fa-crown text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Membership</p>
                            <p class="text-2xl font-semibold">Gold</p>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Bookings -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-clock text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Upcoming</p>
                            <p class="text-2xl font-semibold">3</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Upcoming Bookings -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Upcoming Bookings</h3>
                                {{-- <x-button-link href="/my-bookings" class="text-sm">
                                    View All
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </x-button-link> --}}
                            </div>

                            <div class="space-y-4">
                                <!-- Booking Item 1 -->
                                <div class="flex items-center p-4 border rounded-lg hover:bg-gray-50 transition">
                                    <div class="bg-red-100 rounded-full p-3 mr-4">
                                        <i class="fas fa-futbol text-red-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold">Field A</h4>
                                        <p class="text-sm text-gray-500">
                                            <i class="fas fa-calendar-alt mr-2"></i>
                                            Today, 15:00 - 17:00
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                            Confirmed
                                        </span>
                                    </div>
                                </div>

                                <!-- Booking Item 2 -->
                                <div class="flex items-center p-4 border rounded-lg hover:bg-gray-50 transition">
                                    <div class="bg-red-100 rounded-full p-3 mr-4">
                                        <i class="fas fa-futbol text-red-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold">Field B</h4>
                                        <p class="text-sm text-gray-500">
                                            <i class="fas fa-calendar-alt mr-2"></i>
                                            Tomorrow, 18:00 - 20:00
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">
                                            Pending
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Membership & Points Summary -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Membership Card -->
                    <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-lg shadow-lg p-6 text-white">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <p class="text-sm text-yellow-200">Membership Status</p>
                                <h3 class="text-2xl font-bold text-white">Gold Member</h3>
                            </div>
                            <i class="fas fa-crown text-2xl text-white"></i>
                        </div>

                        <div class="mb-6">
                            <p class="text-sm text-yellow-200 mb-2">Valid Until</p>
                            <p class="font-semibold text-white">December 31, 2024</p>
                        </div>
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-yellow-200">Member ID</p>
                                <p class="font-semibold text-white">SVG-2024-001</p>
                            </div>
                            {{-- <x-button-link href="/membership" class="bg-white text-yellow-600 hover:bg-yellow-100">
                                Renew
                            </x-button-link> --}}
                        </div>
                    </div>


                    <!-- Recent Activities -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-semibold mb-4">Recent Activities</h3>
                        <div class="space-y-4">
                            <div class="flex items-center text-sm">
                                <div class="bg-blue-100 rounded-full p-2 mr-3">
                                    <i class="fas fa-check text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium">Booking Completed</p>
                                    <p class="text-gray-500">Field A - 2 hours</p>
                                </div>
                                <p class="text-gray-400 text-xs ml-auto">2h ago</p>
                            </div>
                            <div class="flex items-center text-sm">
                                <div class="bg-green-100 rounded-full p-2 mr-3">
                                    <i class="fas fa-plus text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium">Points Earned</p>
                                    <p class="text-gray-500">+50 points</p>
                                </div>
                                <p class="text-gray-400 text-xs ml-auto">2h ago</p>
                            </div>
                            <div class="flex items-center text-sm">
                                <div class="bg-yellow-100 rounded-full p-2 mr-3">
                                    <i class="fas fa-star text-yellow-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium">Review Added</p>
                                    <p class="text-gray-500">Field A</p>
                                </div>
                                <p class="text-gray-400 text-xs ml-auto">1d ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            {{-- <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                <x-action-card href="/book-field" icon="calendar-plus" title="Book Field" description="Reserve your spot" />
                <x-action-card href="/equipment" icon="futbol" title="Rent Equipment" description="View available items" />
                <x-action-card href="/tournaments" icon="trophy" title="Tournaments" description="Join competitions" />
                <x-action-card href="/support" icon="headset" title="Support" description="Get help" />
            </div> --}}
        </div>
    </div>

    <!-- Action Card Component -->


</x-app-layout>
