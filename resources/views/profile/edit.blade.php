@extends('users.layouts.app')

@section('content')
    <div class="py-5">
        <div class="container">
            <!-- Profile Header -->
            <div class="card mb-4">
                <div style="height: 128px; background-color: #9E0620;"></div>
                <div class="px-4 px-sm-5 pb-4">
                    <div class="d-flex flex-column flex-sm-row align-items-center" style="margin-top: -64px;">
                        <div class="position-relative">
                            <div class="rounded-circle border-4 bg-light d-flex align-items-center justify-content-center"
                                style="width: 128px; height: 128px; border: 4px solid #9E0620; border-radius: 50%; overflow: hidden;">
                                <i class="fas fa-user text-secondary fs-1"></i>
                            </div>
                            <button class="position-absolute bottom-0 end-0 btn rounded-circle p-2 shadow"
                                style="background-color: #9E0620; color: white; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <div class="mt-4 mt-sm-0 ms-sm-4 text-center text-sm-start"
                            style="z-index: 1; background: white; padding: 10px; border-radius: 8px;">
                            <h3 class="fs-4 fw-bold text-dark">{{ Auth::user()->name }}</h3>
                            <p class="text-secondary mb-2">{{ Auth::user()->email }}</p>
                            <div class="mt-2 d-flex flex-wrap gap-2 justify-content-center justify-content-sm-start">
                                <span class="badge text-white" style="background-color: #9E0620;">
                                    <i class="fas fa-crown me-1"></i> Premium Member
                                </span>
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i> Verified
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Navigation -->
            <div class="card mb-4">
                <div class="card-body p-0">
                    <nav class="nav nav-tabs d-flex flex-nowrap overflow-x-auto"
                    style="white-space: nowrap; -webkit-overflow-scrolling: touch;">
                    <button class="nav-link active flex-shrink-0" style="color: #9E0620;" id="tab-personal-info">
                        <i class="fas fa-user-circle me-2"></i>Personal Info
                    </button>
                    <button class="nav-link text-secondary flex-shrink-0" id="tab-open-mabar">
                        <i class="fas fa-gamepad  me-2"></i>Open Mabar
                    </button>
                    <button class="nav-link text-secondary flex-shrink-0" id="tab-ubah-jadwal">
                        <i class="fas fa-calendar-alt me-2"></i>Ubah Jadwal
                    </button>
                    <button class="nav-link text-secondary flex-shrink-0" id="tab-billing">
                        <i class="fas fa-credit-card me-2"></i>Billing
                    </button>
                    <button class="nav-link text-secondary flex-shrink-0" id="tab-notifications">
                        <i class="fas fa-bell me-2"></i>Notifications
                    </button>
                    <button class="nav-link text-secondary flex-shrink-0" id="tab-security">
                        <i class="fas fa-lock me-2"></i>Security
                    </button>
                </nav>

                </div>
            </div>

            <!-- Sidebar Components (Membership & Recent Activity) -->
            <div class="sidebar-components">
                <!-- Membership Status -->
                <div class="card-membership mb-4 text-black"
                    style="background: url('assets/bg-card-silver.jpg') no-repeat center/cover; border-radius: 12px; overflow: hidden;">
                    <div class="card-body">
                        <div class="mb-3">
                            <h3 class="h5 mb-1 text-uppercase font-weight-bold">DIAMOND</h3>
                            <p class="mb-0 text-black">TEST</p>
                            <p class="mb-0 text-black small">rizkyalamsyah703@gmail.com</p>
                            <p class="mb-0 text-black small">10477087</p>
                        </div>
                        <p class="small text-black">Click untuk info lebih lanjut.</p>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title h5 mb-4">Recent Activity</h3>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px; background-color: rgba(158, 6, 32, 0.1);">
                                    <i class="fas fa-calendar-check" style="color: #9E0620;"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <p class="mb-1">Booking Completed</p>
                                <p class="text-secondary mb-1">Field A - 2 hours</p>
                                <small class="text-muted">2 hours ago</small>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px; background-color: rgba(158, 6, 32, 0.1);">
                                    <i class="fas fa-star" style="color: #9E0620;"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <p class="mb-1">Points Earned</p>
                                <p class="text-secondary mb-1">+50 points</p>
                                <small class="text-muted">2 hours ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="row">
                <div class="col-md-8">
                    <!-- Personal Info Tab -->
                    <!-- Personal Info Tab -->
                    <div id="content-personal-info" class="tab-pane" style="display: block;">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="card-title mb-0">Personal Information</h5>
                                </div>
                                <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                                    @csrf
                                    @method('patch')

                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email', $user->email) }}" required autocomplete="username">
                                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                    </div>

                                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                                        <div>
                                            <p class="text-sm mt-2 text-muted">
                                                {{ __('Your email address is unverified.') }}

                                                <button form="send-verification"
                                                    class="btn btn-link p-0 m-0 align-baseline">
                                                    {{ __('Click here to re-send the verification email.') }}
                                                </button>
                                            </p>

                                            @if (session('status') === 'verification-link-sent')
                                                <p class="mt-2 text-success">
                                                    {{ __('A new verification link has been sent to your email address.') }}
                                                </p>
                                            @endif
                                        </div>
                                    @endif

                                    <button type="submit" class="btn text-white" style="background-color: #9E0620;">Save
                                        Changes</button>


                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Security Tab -->
                    <div id="content-security" class="tab-pane" style="display: none;">
                        <!-- Password Section -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="card-title mb-0">Update Password</h5>
                                </div>
                                <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                                    @csrf
                                    @method('put')

                                    <div class="mb-3">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" name="current_password" class="form-control"
                                            autocomplete="current-password">
                                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" name="password" class="form-control"
                                            autocomplete="new-password">
                                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="form-control"
                                            autocomplete="new-password">
                                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                                    </div>

                                    <button type="submit" class="btn text-white"
                                        style="background-color: #9E0620;">Update Password</button>

                                    @if (session('status') === 'password-updated')
                                        <p class="text-success mt-2">{{ __('Saved.') }}</p>
                                    @endif
                                </form>
                            </div>
                        </div>

                        <!-- Delete Account Section -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="card-title mb-0">Delete Account</h5>
                                    <span class="text-muted">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Permanent Action
                                    </span>
                                </div>
                                <p class="text-muted mb-4">Once your account is deleted, all of its resources and data will
                                    be permanently deleted. Before deleting your account, please download any data or
                                    information that you wish to retain.</p>
                                <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                                    @csrf
                                    @method('delete')

                                    <button type="submit" class="btn text-white" style="background-color: #9E0620;"
                                        onclick="return confirm('Are you sure you want to delete your account?')">
                                        Delete Account
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Notifications Tab -->
                    <div id="content-notifications" class="tab-pane" style="display: none;">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Notifications Settings</h5>
                                <p>Manage your notification preferences here.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Tab -->
                    <div id="content-billing" class="tab-pane" style="display: none;">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Billing Information</h5>
                                <p>Manage your billing information here.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Open Mabar Tab -->
                    <div id="content-open-mabar" class="tab-pane" style="display: none;">
                        <div class="card mb-4">

                                {{-- <form action="{{ route('mabar.store') }}" method="POST">
                                    @csrf --}}
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <h5 class="card-title">🎮 Buat Open Mabar</h5>

                                            <!-- Input Harga Per Slot -->
                                            <div class="mb-3">
                                                <label for="harga_slot" class="form-label">💰 Harga per Slot (Rp)</label>
                                                <input type="number" class="form-control" id="harga_slot" name="harga_slot" placeholder="Contoh: 10000" required>
                                            </div>

                                            <!-- Input Jumlah Slot -->
                                            <div class="mb-3">
                                                <label for="jumlah_slot" class="form-label">👥 Jumlah Slot</label>
                                                <input type="number" class="form-control" id="jumlah_slot" name="jumlah_slot" placeholder="Contoh: 10" required>
                                            </div>

                                            <!-- Input Deskripsi -->
                                            <div class="mb-3">
                                                <label for="deskripsi" class="form-label">📌 Deskripsi</label>
                                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Jelaskan detail tentang sesi mabar..." required></textarea>
                                            </div>

                                            <!-- Tombol Submit -->
                                            <button type="submit" class="btn btn-success w-100">🚀 Buat Open Mabar</button>
                                        </div>
                                    </div>
                                {{-- </form> --}}


                        </div>
                    </div>
                </div>

                <!-- Sidebar Column -->
                <div class="col-md-4">
                    <!-- Sidebar components will be dynamically included here using JavaScript -->
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const tabs = document.querySelectorAll(".nav-link");
                    const contents = document.querySelectorAll(".tab-pane");
                    const sidebarComponents = document.querySelector(".sidebar-components");
                    const sidebarColumn = document.querySelector(".col-md-4");

                    // Store the original sidebar content
                    const originalSidebar = sidebarComponents.cloneNode(true);

                    // Make sure the original sidebar is in place for the first tab
                    if (sidebarColumn) {
                        sidebarColumn.innerHTML = '';
                        sidebarColumn.appendChild(sidebarComponents);
                    }

                    tabs.forEach(tab => {
                        tab.addEventListener("click", function() {
                            // Remove active class from all tabs
                            tabs.forEach(t => {
                                t.classList.remove("active");
                                t.classList.add("text-secondary");
                            });

                            // Add active class to clicked tab
                            tab.classList.add("active");
                            tab.classList.remove("text-secondary");

                            // Hide all content then show selected
                            contents.forEach(content => {
                                content.style.display = "none";
                            });

                            // Get the tab identifier by removing the 'tab-' prefix
                            const tabIdentifier = tab.id.replace('tab-', '');
                            const activeContent = document.getElementById(`content-${tabIdentifier}`);

                            if (activeContent) {
                                activeContent.style.display = "block";
                            }

                            // Always use a clone of the original sidebar
                            if (sidebarColumn) {
                                sidebarColumn.innerHTML = '';
                                sidebarColumn.appendChild(originalSidebar.cloneNode(true));
                            }
                        });
                    });
                });
            </script>

        </div>


    </div>
    <style>
        .card-membership {
            max-width: 350px;
            /* Batas lebar kartu */
            margin: auto;
            /* Agar tetap di tengah */
            border-radius: 12px;
            overflow: hidden;
            padding: 20px;
        }

        .card-membership:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            transform: translateY(-5px);
            /* Sedikit mengangkat saat hover */
        }

        .card:hover {
            transform: translateY(-5px);
            /* Sedikit mengangkat saat hover */
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
            /* Lebih tebal saat hover */
        }

        .form-control:focus {
            border-color: #9E0620;
            box-shadow: 0 0 0 0.25rem rgba(158, 6, 32, 0.25);
        }

        .nav-link.active {
            border-color: #9E0620 !important;
            color: #9E0620 !important;
        }

        .invalid-feedback {
            color: #9E0620;
        }

        /* Added style for better text visibility */
        .card {
            overflow: visible;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            /* Soft shadow */
            transition: all 0.3s ease-in-out;
        }

        .nav-tabs .nav-link {
            transition: all 0.3s ease-in-out;
        }

        .nav-tabs .nav-link.active {
            color: #9E0620 !important;
            font-weight: bold;
            border-bottom: 3px solid #9E0620 !important;
            /* Garis bawah tebal */
            margin-bottom: -2px;
            /* Sedikit dinaikkan */
        }
    </style>
    <!-- Add this in your layout head section -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <style>
        .toastify {
            padding: 14px 24px;
            border-radius: 8px;
            font-size: 14px;


            display: flex;

            align-items: center;
        }

        /* Progress bar toast */
        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: rgba(255, 255, 255, 0.7);
            transform-origin: left;
            transition: width linear;
        }

        @media (max-width: 768px) {
            .toastify {
                left: 50% !important;
                transform: translateX(-50%);
            }
        }
    </style>

    <script>
        function showToast(message, type = 'success') {
            const backgroundColor = type === 'success' ? '#50C878' : '#dc3545';
            const duration = 5000;
            const isMobile = window.innerWidth <= 768;

            const toast = Toastify({
                text: message,
                duration: duration,
                gravity: isMobile ? "top" : "bottom",
                position: isMobile ? "center" : "right",
                backgroundColor: backgroundColor,
                stopOnFocus: true,
                close: true, // Tombol close otomatis muncul
                className: "custom-toast",
                onClick: function() {}
            });

            toast.showToast();

            // Tunggu sebentar sampai toast dibuat di DOM
            setTimeout(() => {
                const toastElement = document.querySelector(".custom-toast");
                if (toastElement) {
                    const progressBar = document.createElement("div");
                    progressBar.className = "toast-progress";
                    toastElement.appendChild(progressBar);

                    // Animasi progress bar
                    progressBar.style.transition = `width ${duration}ms linear`;
                    setTimeout(() => {
                        progressBar.style.width = "0%";
                    }, 10);
                }
            }, 100);
        }

        document.addEventListener("DOMContentLoaded", function() {
            @if (session('status') === 'profile-updated')
                showToast('Profile updated successfully!');
            @endif

            @if (session('status') === 'password-updated')
                showToast('Password updated successfully!');
            @endif

            @if (session('status') === 'verification-link-sent')
                showToast('Verification link sent!');
            @endif

            @if ($errors->any())
                showToast('There was an error with your submission.', 'error');
            @endif
        });
    </script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
@endsection
