<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Rubik", sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #2f2f2f;
            padding: 10px;
            color: #ffffff;
        }

        .booking-container {
            max-width: 600px;
            width: 100%;
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 700px;
            box-shadow: 0 0 10px 2px #212121;
            background: #2f2f2f;
        }

        nav {
            background-color: #2f2f2f;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ffffff;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
        }

        main {
            padding: 10px;
        }

        .reservation {
            display: flex;
            align-items: center;
            padding: 10px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ffffff;
        }

        .reservation img {
            max-width: 80px;
            margin-right: 20px;
        }

        .reservations {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-left: 20px;
        }

        .reservation-number {
            font-weight: bold;
            font-size: 1.35rem;
            display: block;
        }

        .reservation-date {
            font-size: 1rem;
            display: block;
        }

        .date {
            display: flex;
            margin-right: 50px;
            align-items: center;
        }

        .date img {
            height: 20px;
            width: 20px;
        }
    </style>
</head>

<body>
    <div class="booking-container">
        <nav>
            <a class="nav-link" href="{{ route('booking_dates') }}"><i class="fas fa-chevron-left"></i></a>
            <h5>รายการที่จอง</h5>
        </nav>
        <main>
            @if ($groupedReservations->isEmpty())
                <div class="alert alert-info text-center">
                    No reservations yet.
                </div>
            @else
                @foreach ($groupedReservations as $reservationDate => $reservations)
                    @php
                        $tableNumbers = $reservations->pluck('table.table_number')->implode(', ');
                    @endphp
                    <section class="reservation">
                        <img src="https://img5.pic.in.th/file/secure-sv1/EX-removebg-preview.png" alt="EX">
                        <div class="reservations">
                            <span class="reservation-number">โต๊ะ {{ $tableNumbers }}</span>
                            <div class="date">
                                <img src="https://cdn-icons-png.flaticon.com/128/7691/7691413.png">
                                <span class="reservation-date">{{ \Carbon\Carbon::parse($reservationDate)->format('d M Y') }}</span>
                            </div>
                        </div>
                    </section>
                @endforeach
            @endif
        </main>
    </div>
</body>

</html>
