<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SportVue</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Animation -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">



    <style>
        .promo-banner {
            position: relative;
            height: 50px;
            width: 100%;

        }

        html,
        body {
            overflow-x: hidden;
        }

        .promo-slider {
            position: absolute;
            width: 100%;
        }

        .promo-slider .d-flex {
            display: flex !important;
            animation: slideLeft 120s linear infinite;
            width: max-content;
        }

        .promo-slide {
            min-width: 100vw;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            padding: 0 20px;
        }

        @keyframes slideLeft {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-300%);
                /* 3 slides */
            }
        }

        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .btn-danger {
            background-color: #9E0620;
            border: none;
            border-radius: 4px;
            /* Sudut yang lebih tajam */
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #8a051c;
            transform: translateY(-2px);
        }

        .bg-danger {
            background-color: #8a051c;
        }

        .lead {
            font-size: 1.1rem;
            opacity: 0.9;
        }


        .btn-outline-light {
            width: 36px;
            height: 36px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            transform: translateY(-3px);
        }


        .hover-text-white {
            transition: color 0.3s ease;
        }

        .hover-text-white:hover {
            color: white !important;
        }
    </style>

</head>


<body>


    @include('layouts.navigation')
    @yield('content')


</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>

</html>
