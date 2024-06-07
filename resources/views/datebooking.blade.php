<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Booking Date</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Custom CSS for Flatpickr and logo -->
    <style>
        .flatpickr-calendar {
            background-color: #222222;
            color: #ffffff;
            border: 1px solid #333;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(161, 135, 135, 0.1);
            font-family: 'Arial', sans-serif;
            width: 320px;
            position: absolute;
            z-index: 1000;
            display: flex;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
        }

        .flatpickr-day {
            background-color: transparent;
            color: #ffffff;
        }

        .flatpickr-innerContainer {
            background-color: #282828;
            color: #ffffff;
        }

        .flatpickr-day:hover {
            background-color: #353535;
            color: #ffffff;
            cursor: pointer;
        }

        .flatpickr-monthYear-container {
            background-color: #ffffff;
            color: #ffffff;
            padding: 10px;
            border-bottom: 1px solid #ffffff;
        }

        .flatpickr-current-month {
            font-weight: bold;
            color: #ffffff;
        }

        .flatpickr-months .flatpickr-month {
            background-color: #5e5e5e;
        }

        .flatpickr-prev-month,
        .flatpickr-next-month {
            fill: white;
            color: #ffffff;
            cursor: pointer;
            background-color: #5e5e5e;
            position: center;
        }

        .flatpickr-weekdays.flatpickr-visible {
            background-color: #333;
            color: #ffffff;
        }

        .flatpickr-month {
            background-color: #898989;
            color: #ffffff;
        }

        .flatpickr-weekdays .flatpickr-weekday {
            color: #ffffff;
        }

        .flatpickr-value {
            background-color: #282828;
            color: #ffffff;
        }

        .flatpickr-label {
            color: #ffffff;
        }

        .flatpickr-timeInput {
            background-color: #282828;
            color: #ffffff;
        }

        .flatpickr-timeInput input {
            color: #ffffff;
        }

        .flatpickr-timeInput .flatpickr-timeInput--hour,
        .flatpickr-timeInput .flatpickr-timeInput--minute {
            background-color: #333;
            color: #ffffff;
        }

        .flatpickr-timeInput .flatpickr-timeInput--select {
            background-color: #282828;
            color: #ffffff;
        }

        .flatpickr-timeInput .flatpickr-timeInput--select:focus {
            background-color: #333;
            color: #ffffff;
        }

        /* Additional styles */
        body {
            font-family: "Rubik", sans-serif;
            background: #222222;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 0;
        }

        .booking-container {
            max-width: 600px;
            width: 90%;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 0 10px 2px #212121;
            background: #222222;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        #logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        #logo-container img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .btn-primary {
            background-color: #5a899d;
            border-color: #5a899d;
        }

        .btn-primary:hover {
            background-color: #4a7380;
            border-color: #4a7380;
        }

        .text-danger {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }

        /* Hide today */
        .flatpickr-day.today {
            display: none;
        }

        /* Positioning for the icon */
        .icon-container {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .icon-container img {
            width: 50px;
            height: 50px;
        }
    </style>
</head>

<body>
    <div class="icon-container">
        <a href="{{ route('userreservations') }}">
            <img src="https://img.icons8.com/?size=100&id=68456&format=png&color=FFFFFF" class="imgnav" alt="">
        </a>
    </div>
    <div class="booking-container">
        <div id="logo-container">
            <img src="https://img5.pic.in.th/file/secure-sv1/EX-removebg-preview.png" alt="Logo">
        </div>
        <h1 class="mb-4">EXCEED BAR</h1>

        <!-- Icon link -->

        <button type="button" class="btn btn-primary" tabindex="0" onclick="openCalendar()">เลือกวันจองโต๊ะ</button>
        <div id="flatpickr-calendar" class="flatpickr-calendar"></div>
        <form id="dateForm" action="{{ route('booking') }}" method="GET" style="display: none;">
            <input type="hidden" name="date" id="selectedDate">
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Function to open Flatpickr calendar
        function openCalendar() {
            const today = new Date();
            const tomorrow = new Date(today);
            const dayAfterTomorrow = new Date(today);

            tomorrow.setDate(today.getDate() + 1);
            dayAfterTomorrow.setDate(today.getDate() + 1);

            const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDayOfMonth = new Date(today.getFullYear(), today.getMonth() + 1);

            const pastDays = [];
            for (let d = firstDayOfMonth; d < today; d.setDate(d.getDate() + 1)) {
                pastDays.push(new Date(d));
            }

            var calendar = flatpickr('#flatpickr-calendar', {
                inline: true,
                dateFormat: 'Y-m-d',
                minDate: firstDayOfMonth,
                maxDate: lastDayOfMonth,
                disable: pastDays,
                onChange: function (selectedDates, dateStr, instance) {
                    const selectedDate = selectedDates[0].toISOString().split('T')[0];
                    const allowedDates = [today.toISOString().split('T')[0], tomorrow.toISOString().split('T')[0], dayAfterTomorrow.toISOString().split('T')[0]];
                    if (!allowedDates.includes(selectedDate)) {
                        alert('วันที่คุณเลือกยังไม่เปิดทำการให้จอง');
                    } else {
                        const nextDay = new Date(selectedDates[0]);
                        nextDay.setDate(nextDay.getDate() + 1); // Add 1 day
                        const nextDateStr = nextDay.toISOString().split('T')[0];
                        window.location.href = "{{ route('booking') }}?date=" + nextDateStr;
                    }
                }
            });

            // Set initial selected date from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const dateParam = urlParams.get('date');
            if (dateParam) {
                calendar.setDate(dateParam, true); // Set selected date in Flatpickr
            } else {
                calendar.setDate(today, false); // Default to today's date if no date parameter
            }

            document.getElementById('flatpickr-calendar').style.display = 'block';

        }
    </script>

</body>

</html>
