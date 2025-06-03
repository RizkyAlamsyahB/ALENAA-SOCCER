@extends('layouts.app')

@section('content')
    <div class="py-5">
        <div class="container">
            <!-- Profile Header with Points -->
            <div class="card mb-4">
                <div style="height: 128px; background-color: #9E0620;"></div>
                <div class="px-4 px-sm-5 pb-4">
                    <div class="d-flex flex-column flex-sm-row align-items-center" style="margin-top: -64px;">
                        <!-- User Avatar Section with Profile Picture Upload Form -->
                        <div class="position-relative">
                            <form id="profilePictureForm" method="POST" action="{{ route('profile.picture.update') }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('POST')

                                <div class="rounded-circle border-4 bg-light d-flex align-items-center justify-content-center"
                                    style="width: 128px; height: 128px; border: 4px solid #9E0620; border-radius: 50%; overflow: hidden;">
                                    @if (Auth::user()->profile_picture)
                                        <img src="{{ Storage::url(Auth::user()->profile_picture) }}"
                                            class="w-100 h-100" style="object-fit: cover;">
                                    @else
                                        <i class="fas fa-user text-secondary fs-1"></i>
                                    @endif
                                </div>

                                <input type="file" name="profile_picture" id="profile_picture" class="d-none"
                                    accept="image/*" onchange="document.getElementById('profilePictureForm').submit()">

                                <label for="profile_picture"
                                    class="position-absolute bottom-0 end-0 btn rounded-circle p-2 shadow"
                                    style="background-color: #9E0620; color: white; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                    <i class="fas fa-camera"></i>
                                </label>
                            </form>
                            <!-- Profile picture upload success message -->
                            @if (session('status') === 'profile-picture-updated')
                                <div class="alert alert-success mt-2"
                                    style="position: absolute; width: 200px; font-size: 0.8rem;">
                                    Profile picture updated successfully!
                                </div>
                            @endif
                            <!-- Profile picture upload error message -->
                            @error('profile_picture')
                                <div class="alert alert-danger mt-2"
                                    style="position: absolute; width: 200px; font-size: 0.8rem;">
                                    {{ $message }}
                                </div>
                            @enderror


                        </div>

<!-- User Info Section -->
<div class="mt-4 mt-sm-0 ms-sm-4 text-center text-sm-start"
    style="z-index: 1; background: white; padding: 10px; border-radius: 8px;">
    <h3 class="fs-4 fw-bold text-dark">{{ Auth::user()->name }}</h3>
    <p class="text-secondary mb-2">{{ Auth::user()->email }}</p>
    <div class="mt-2 d-flex flex-wrap gap-2 justify-content-center justify-content-sm-start">

        {{-- Membership Badge --}}
        @if ($membershipType && $membershipName)
            <span class="badge text-white" style="background-color: #9E0620;">
                <i class="fas fa-crown me-1"></i> {{ ucfirst($membershipType) }} Member
            </span>
        @else
            <span class="badge bg-secondary">
                <i class="fas fa-user me-1"></i> Free Account
            </span>
        @endif

        {{-- Email Verification Badge --}}
        @if (Auth::user()->email_verified_at)
            <span class="badge bg-success">
                <i class="fas fa-check-circle me-1"></i> Verified
            </span>
        @else
            <span class="badge bg-danger">
                <i class="fas fa-times-circle me-1"></i> Unverified
            </span>
        @endif
    </div>
</div>


                        <!-- Points Card (Positioned to the right) -->
                        <div class="mt-4 mt-sm-0 ms-sm-auto text-center text-sm-start"
                            style="z-index: 1; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); min-width: 220px;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge p-2" style="background-color: #FFD700; color: #000;">
                                    <i class="fas fa-coins me-1"></i> Points
                                </span>
                                <span class="fw-bold text-dark">{{ $user->points ?? 0 }}</span>
                            </div>
                            <div class="progress mb-2" style="height: 10px; background-color: #f0f0f0;">
                                <div class="progress-bar" role="progressbar"
                                    style="width: {{ min(100, ($user->points ?? 0) / 10) }}%; background-color: #9E0620;"
                                    aria-valuenow="{{ $user->points ?? 0 }}" aria-valuemin="0" aria-valuemax="1000">
                                </div>
                            </div>
                            <a href="{{ route('user.points.index') }}" class="btn btn-sm w-100 mt-2 text-white"
                                style="background-color: #9E0620;">
                                <i class="fas fa-gift me-1"></i> Redeem Rewards
                            </a>
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
                            <i class="fas fa-user-circle me-2"></i>Informasi Pribadi
                        </button>


                        <button class="nav-link text-secondary flex-shrink-0" id="tab-security">
                            <i class="fas fa-lock me-2"></i>Keamanan
                        </button>
                    </nav>

                </div>
            </div>

            <!-- Sidebar Components (Membership & Recent Activity) -->
            <div class="sidebar-components">
                <!-- Membership Status -->
                @if ($membershipType && $membershipName)
                    <div class="card-membership mb-4 text-black"
                        style="background: url('assets/bg-card-{{ $membershipType }}.jpg') no-repeat center/cover; border-radius: 12px; overflow: hidden;">
                        <div class="card-body">
                            <div class="mb-3">
                                <h3 class="h5 mb-1 text-uppercase font-weight-bold">{{ strtoupper($membershipType) }}</h3>
                                <p class="mb-0 text-black">{{ $membershipName }}</p>
                                <p class="mb-0 text-black small">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>
                @endif


            </div>

            <!-- Tab Content -->
            <div class="row">
                <div class="col-md-8">

                    <!-- Personal Info Tab -->
                    <div id="content-personal-info" class="tab-pane" style="display: block;">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="card-title mb-0">Informasi Pribadi</h5>
                                </div>
                                <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                                    @csrf
                                    @method('patch')

                                    <div class="mb-3">
                                        <label class="form-label">Nama</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', $user->name) }}" required autofocus
                                            autocomplete="name">
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
                                                {{ __('Alamat email Anda belum diverifikasi.') }}

                                                <button form="send-verification"
                                                    class="btn btn-link p-0 m-0 align-baseline">
                                                    {{ __('Klik disini untuk mengirim ulang email verifikasi.') }}
                                                </button>
                                            </p>

                                            @if (session('status') === 'verification-link-sent')
                                                <p class="mt-2 text-success">
                                                    {{ __('Link verifikasi baru telah dikirim ke alamat email Anda.') }}
                                                </p>
                                            @endif
                                        </div>
                                    @endif

                                    <button type="submit" class="btn text-white" style="background-color: #9E0620;">Simpan
                                        Perubahan</button>
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
                                    <h5 class="card-title mb-0">Perbarui Kata Sandi</h5>
                                </div>
                                <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                                    @csrf
                                    @method('put')

                                    <div class="mb-3">
                                        <label class="form-label">Kata Sandi Saat Ini</label>
                                        <input type="password" name="current_password" class="form-control"
                                            autocomplete="current-password">
                                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Kata Sandi Baru</label>
                                        <input type="password" name="password" class="form-control"
                                            autocomplete="new-password">
                                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Konfirmasi Kata Sandi</label>
                                        <input type="password" name="password_confirmation" class="form-control"
                                            autocomplete="new-password">
                                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                                    </div>

                                    <button type="submit" class="btn text-white"
                                        style="background-color: #9E0620;">Perbarui Kata Sandi</button>

                                    @if (session('status') === 'password-updated')
                                        <p class="text-success mt-2">{{ __('Tersimpan.') }}</p>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                    @push('scripts')
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const hargaSlotInput = document.getElementById('harga_slot');
                                const jumlahSlotInput = document.getElementById('jumlah_slot');
                                const estimatedEarnings = document.getElementById('estimated_earnings');
                                const progressBar = document.querySelector('.progress-bar');

                                function updatePreview() {
                                    const harga = parseInt(hargaSlotInput.value) || 0;
                                    const slot = parseInt(jumlahSlotInput.value) || 0;
                                    const total = harga * slot;

                                    estimatedEarnings.textContent = `Rp ${total.toLocaleString('id-ID')}`;
                                    progressBar.style.width = `${(slot/10) * 100}%`;
                                }

                                hargaSlotInput.addEventListener('input', updatePreview);
                                jumlahSlotInput.addEventListener('input', updatePreview);
                            });
                        </script>
                    @endpush



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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
@endsection
