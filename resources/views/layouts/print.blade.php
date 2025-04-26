<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print</title>

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 100px;
            height: auto;
        }
        .header h1, .header p {
            margin: 0;
        }
        .header h1 {
            font-size: 24px;
        }
    </style>
</head>
<body>
    
    <div class="header">
        <img src="{{ asset('assets/images/gtc-logo.png') }}" alt="Logo">
        <h1>Global Tekno Komputer</h1>
        {{-- <p>Alamat: Jl. Prof. Dr. Hamka, Air Tawar Barat, Padang, Sumatera Barat</p> --}}
    </div>
    <hr style="margin-bottom: 1.5rem;">

    @yield('content')
</body>
</html>