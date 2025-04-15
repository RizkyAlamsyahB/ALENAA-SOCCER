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
                                            alt="{{ Auth::user()->name }}" class="w-100 h-100" style="object-fit: cover;">
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

                            <!-- Profile picture upload error message -->
                            @error('profile_picture')
                                <div class="alert alert-danger mt-2"
                                    style="position: absolute; width: 200px; font-size: 0.8rem;">
                                    {{ $message }}
                                </div>
                            @enderror

                            <!-- Profile picture upload success message -->
                            @if (session('status') === 'profile-picture-updated')
                                <div class="alert alert-success mt-2"
                                    style="position: absolute; width: 200px; font-size: 0.8rem;">
                                    Profile picture updated successfully!
                                </div>
                            @endif
                        </div>

<!-- User Info Section -->
<div class="mt-4 mt-sm-0 ms-sm-4 text-center text-sm-start"
    style="z-index: 1; background: white; padding: 10px; border-radius: 8px;">
    <h3 class="fs-4 fw-bold text-dark">{{ Auth::user()->name }}</h3>
    <p class="text-secondary mb-2">{{ Auth::user()->email }}</p>
    <div class="mt-2 d-flex flex-wrap gap-2 justify-content-center justify-content-sm-start">
        <span class="badge text-white" style="background-color: #9E0620;">
            <i class="fas fa-crown me-1"></i> Premium Member
        </span>

        @if(Auth::user()->email_verified_at)
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
                            <a href="{{ route('user.points.index') }}" class="btn btn-sm w-100 mt-2 text-white" style="background-color: #9E0620;">
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
                            <i class="fas fa-user-circle me-2"></i>Personal Info
                        </button>
                        <button class="nav-link text-secondary flex-shrink-0" id="tab-open-mabar">
                            <i class="fas fa-gamepad  me-2"></i>Open Mabar
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
                                    <h5 class="card-title mb-0">Personal Information</h5>
                                </div>
                                <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                                    @csrf
                                    @method('patch')

                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
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


                    <!-- Open Mabar Tab -->
                    <div id="content-open-mabar" class="tab-pane" style="display: none;">
                        <!-- Create Mabar Card -->
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">🎮 Buat Open Mabar</h5>
                                    <span class="badge bg-success">Creator Mode</span>
                                </div>
                            </div>
                            <div class="card-body">
                                {{-- <form action="{{ route('mabar.store') }}" method="POST">
                                 @csrf --}}
                                <div class="row">
                                    <!-- Game Selection -->
                                    <div class="col-md-6 mb-3">
                                        <label for="game_type" class="form-label">🎯 Pilih Game</label>
                                        <select class="form-select" id="game_type" name="game_type" required>
                                            <option value="">Pilih game...</option>
                                            <option value="mobile_legends">Mobile Legends</option>
                                            <option value="pubg_mobile">PUBG Mobile</option>
                                            <option value="free_fire">Free Fire</option>
                                            <option value="valorant">Valorant</option>
                                        </select>
                                    </div>

                                    <!-- Game Mode -->
                                    <div class="col-md-6 mb-3">
                                        <label for="game_mode" class="form-label">🎲 Mode Permainan</label>
                                        <select class="form-select" id="game_mode" name="game_mode" required>
                                            <option value="">Pilih mode...</option>
                                            <option value="ranked">Ranked</option>
                                            <option value="classic">Classic</option>
                                            <option value="custom">Custom Room</option>
                                        </select>
                                    </div>

                                    <!-- Pricing Section -->
                                    <div class="col-md-6 mb-3">
                                        <label for="harga_slot" class="form-label">💰 Harga per Slot (Rp)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="harga_slot" name="harga_slot"
                                                placeholder="10000" required min="1000">
                                        </div>
                                    </div>

                                    <!-- Slot Management -->
                                    <div class="col-md-6 mb-3">
                                        <label for="jumlah_slot" class="form-label">👥 Jumlah Slot</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="jumlah_slot"
                                                name="jumlah_slot" placeholder="4" required min="1"
                                                max="10">
                                            <span class="input-group-text">slots</span>
                                        </div>
                                    </div>

                                    <!-- Schedule -->
                                    <div class="col-md-6 mb-3">
                                        <label for="jadwal" class="form-label">📅 Jadwal Mabar</label>
                                        <input type="datetime-local" class="form-control" id="jadwal" name="jadwal"
                                            required>
                                    </div>

                                    <!-- Duration -->
                                    <div class="col-md-6 mb-3">
                                        <label for="durasi" class="form-label">⏱️ Durasi (Jam)</label>
                                        <select class="form-select" id="durasi" name="durasi" required>
                                            <option value="1">1 Jam</option>
                                            <option value="2">2 Jam</option>
                                            <option value="3">3 Jam</option>
                                            <option value="4">4 Jam</option>
                                        </select>
                                    </div>

                                    <!-- Description -->
                                    <div class="col-12 mb-3">
                                        <label for="deskripsi" class="form-label">📌 Deskripsi & Aturan</label>
                                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"
                                            placeholder="Jelaskan detail sesi mabar, peraturan, dan persyaratan khusus..." required></textarea>
                                    </div>

                                    <!-- Additional Features -->
                                    <div class="col-12 mb-4">
                                        <div class="card bg-light border">
                                            <div class="card-body">
                                                <h6 class="card-title">✨ Fitur Tambahan</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="voice_chat" name="features[]" value="voice_chat">
                                                            <label class="form-check-label" for="voice_chat">🎤 Voice Chat
                                                                Required</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="livestream" name="features[]" value="livestream">
                                                            <label class="form-check-label" for="livestream">📺 Will
                                                                Livestream</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="beginners" name="features[]" value="beginners">
                                                            <label class="form-check-label" for="beginners">🌱 Pemula
                                                                Welcome</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="competitive" name="features[]" value="competitive">
                                                            <label class="form-check-label" for="competitive">🏆
                                                                Competitive Play</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview Section -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">💫 Preview Pendapatan</h6>
                                        <span class="text-success" id="estimated_earnings">Rp 0</span>
                                    </div>
                                    <div class="progress mt-2" style="height: 10px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn text-white" style="background-color: #9E0620;">
                                        🚀 Buat Open Mabar
                                    </button>
                                </div>
                                {{-- </form> --}}
                            </div>
                        </div>

                        <!-- Quick Tips Card -->
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">💡 Tips Membuat Mabar</h6>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">✓ Tetapkan harga yang kompetitif</li>
                                    <li class="mb-2">✓ Berikan deskripsi yang jelas</li>
                                    <li class="mb-2">✓ Tentukan aturan dengan detail</li>
                                    <li>✓ Pastikan jadwal yang flexibel</li>
                                </ul>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle tab ubah jadwal
            const btnUbahJadwal = document.getElementById('tab-ubah-jadwal');
            btnUbahJadwal.addEventListener('click', function() {
                // Sembunyikan semua tab panes
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.style.display = 'none';
                });

                // Tampilkan tab schedule
                document.getElementById('content-schedule').style.display = 'block';

                // Update active state pada buttons
                document.querySelectorAll('.nav-link').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
@endsection
