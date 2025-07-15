@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div
                                    class="dashboard-welcome-container p-4 p-lg-5 h-100 d-flex flex-column justify-content-center">
                                    <div class="welcome-time mb-2">
                                        <div class="current-date fs-5 text-muted"></div>
                                        <div class="current-time display-4 fw-bold text-primary"></div>
                                    </div>


                                    <h2 class="fs-1 fw-light mb-0 text-gradient" id="sikap">SIKAP</h2>

                                    <p class="lead mt-2 text-strong">
                                        Sistem Informasi Kesekretariatan Administrasi Perusahaan
                                    </p>


                                </div>
                            </div>
                            <div class="col-lg-6 d-none d-lg-block">
                                <div class="dashboard-image-container">
                                    <img src="https://images.unsplash.com/photo-1497215842964-222b430dc094?q=80&w=1770&auto=format&fit=crop"
                                        alt="Office Dashboard" class="img-fluid dashboard-image">
                                    <div class="overlay"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .dashboard-welcome-container {
            background-color: #fff;
            position: relative;
            z-index: 1;
        }

        .dashboard-image-container {
            position: relative;
            height: 100%;
            overflow: hidden;
        }

        .dashboard-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(13, 110, 253, 0.3));
        }

        .welcome-heading {
            margin-bottom: 0;
        }


        .text-gradient {
            background: linear-gradient(90deg, #0d6efd, #0dcaf0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: transparent;
        }

        .quick-action-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .quick-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .quick-action-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .bg-primary-subtle {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-success-subtle {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-info-subtle {
            background-color: rgba(13, 202, 240, 0.1);
        }

        @media (max-width: 991.98px) {
            .welcome-heading {
                font-size: 2.5rem;
            }

            h2.fs-1 {
                font-size: 1.8rem !important;
            }

            .current-time {
                font-size: 2.5rem !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Update jam dan tanggal
            function updateClock() {
                const now = new Date();

                // Format waktu
                let hours = now.getHours();
                let minutes = now.getMinutes();
                let seconds = now.getSeconds();

                // Format tanggal dalam Bahasa Indonesia
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                    'September', 'Oktober', 'November', 'Desember'
                ];

                const day = days[now.getDay()];
                const date = now.getDate();
                const month = months[now.getMonth()];
                const year = now.getFullYear();

                // Tampilkan waktu dengan format jam:menit:detik
                hours = hours < 10 ? '0' + hours : hours;
                minutes = minutes < 10 ? '0' + minutes : minutes;
                seconds = seconds < 10 ? '0' + seconds : seconds;

                $('.current-time').text(`${hours}:${minutes}:${seconds}`);
                $('.current-date').text(`${day}, ${date} ${month} ${year}`);

                // Update setiap detik
                setTimeout(updateClock, 1000);
            }

            // Atur salam berdasarkan waktu
            function setGreeting() {
                const hour = new Date().getHours();
                let greeting;

                if (hour >= 5 && hour < 12) {
                    greeting = "Selamat Pagi,";
                } else if (hour >= 12 && hour < 15) {
                    greeting = "Selamat Siang,";
                } else if (hour >= 15 && hour < 19) {
                    greeting = "Selamat Sore,";
                } else {
                    greeting = "Selamat Malam,";
                }

                $('.welcome-heading').text(greeting);
            }

            // Jalankan fungsi
            updateClock();
            setGreeting();

            // Inisialisasi tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $(".alert").fadeOut("slow");
            }, 5000);
        });
    </script>
@endpush
