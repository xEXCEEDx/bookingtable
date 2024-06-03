<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Table Booking</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;700;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">


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
    }

    .booking-container {
        max-width: 600px;
        width: 100%;
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 0 10px 2px #212121;
        background: #2f2f2f;
    }

    nav {
        background-color: #2f2f2f;
        padding: 5px 20px;
        /* Increased padding for better spacing */
        display: flex;
        /* Flex container */
        justify-content: space-between;
        /* Items evenly spaced */
        align-items: center;
        /* Center align items vertically */
        border-bottom: 1px solid #232323; /* Add this line */
    }

    .nav-link {
        color: white;
        text-decoration: none;
        font-size: 1.5rem;
        /* Adjusted font size */

    }

    .imgnav {
        width: 50px;
        /* Adjusted width */
        height: auto;
        /* Maintains aspect ratio */
        color: #000000;
        /* Added color for consistency */

    }

    .seat-details {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        font-size: 15px;
    }

    .seat-legends {
        display: flex;
        justify-content: space-around;
        gap: 10px;
    }

    .seat-legend {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .hidden {
        display: none;
    }

    .seat-legend span {
        color: #ffffff;
    }

    .seat-box {
        display: grid;
        grid-template-columns: repeat(10, 1fr);
        gap: 10px;
        justify-items: center;
    }

    .seat {
        justify-content: center;

        width: 2.5rem;
        height: 2.5rem;
        border-radius: 20%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        cursor: pointer;

        position: relative;
        color: white;
        background-color: #2f2f2f;
    }

    .available {
        background-color: #5a899d;
    }

    .reserved {
        background-color: #ced9e2;
        cursor: not-allowed;
    }

    .selected {
        background-color: #07cbbe;
    }

    .seat-number {
        font-size: 1rem;
        color: #ffffff;
        text-align: center;
        line-height: 30px;
    }

    .booking-details {
        display: none;
        padding: 20px;
        background: #242424;
        display: flex;
        flex-direction: column;
        gap: 10px;
        color: white;
    }

    .details-row {
        display: flex;
        justify-content: space-between;
    }

    .continue {
        background: rgb(56, 56, 56);
        color: #ffffff;
        border: none;
        border-radius: 0.323rem;
        padding: 0.625rem;
        cursor: pointer;

        align-self: center;
        margin-left: 13.7rem;
        justify-content: center;
    }

    img {
        width: 100%;

    }

    @media (max-width: 768px) {
        .seat-box {
            grid-template-columns: repeat(10, 1fr);
            justify-content: center;
        }

        .seat {
            justify-content: center;

            width: 2.5rem;
            height: 2.5rem;
            font-size: 0.5rem;
            line-height: 2rem;
        }
    }

    @media (max-width: 480px) {
        .seat-box {
            grid-template-columns: repeat(10, 1fr);
            justify-content: center;
        }

        .seat {
            justify-content: center;

            width: 1.5rem;
            height: 1.5rem;
            font-size: 0.25rem;
            line-height: 1.25rem;
        }

        .continue {
            border-radius: 0.323rem;
            padding: 0.625rem;
            cursor: pointer;

            align-self: center;
            margin-left: 33%;
            justify-content: center;
        }
    }
</style>
</head>

<body>
    <div class="booking-container">
        <nav>
            <a class="nav-link" href="#"><i class="fas fa-chevron-left"></i></a>
            <a href='{{ route('userreservations') }}'>
            <img src="https://img.icons8.com/?size=100&id=68456&format=png&color=FFFFFF"  class="imgnav" alt="">
        </a>
        </nav>


        <div>
            <img src="https://uppic.cloud/ib/iqbCuu6eGJ.png" alt="Image">
        </div>
        <div class="seat-details">
            <div class="seat-legends">
                <div class="seat-legend">
                    <div class="seat available"></div><span>ว่างอยู่</span>
                </div>
                <div class="seat-legend">
                    <div class="seat reserved"></div><span>จองแล้ว</span>
                </div>
                <div class="seat-legend">
                    <div class="seat selected"></div><span>โต๊ะที่เลือก</span>
                </div>
            </div>
            <div class="seat-box">
                <!-- Dynamic content for seats -->
                @foreach ($tables as $table)
                    <div class="seat {{ $table->status }}" data-id="{{ $table->table_number }}">
                        <div class="seat-number">{{ $table->table_number }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Booking details section -->
        <div class="booking-details" id="booking-details">
            <div class="details-row">
                <span>โต๊ะที่เลือก:</span><span id="tickets-reserved">-</span>
            </div>
            <div class="details-row">
                <span>ราคารวม:</span><span id="total-price">0฿</span>
            </div>
            <form method="POST" action="{{ route('booking.confirm') }}" id="reservation-form">
                @csrf
                <div class="mb-3">
                    <!-- Hidden select element for selected tables -->
                    <select multiple class="hidden" id="tables" name="tables[]"></select>
                </div>
                <!-- Submit button to confirm reservation -->
                <button type="submit" class="continue">ยืนยันการจอง</button>
            </form>
        </div>
    </div>

    <!-- Reservation form -->


    <script>
        const seatBox = document.querySelector('.seat-box');
        const tickets = document.getElementById('tickets-reserved');
        const price = document.getElementById('total-price');
        const bookingDetails = document.getElementById('booking-details');
        const reservationForm = document.getElementById('reservation-form');
        const reservationTablesSelect = document.getElementById('tables');

        const selectedSeats = new Set();
        const maxSeatsAllowed = 5;
        const seatPrice = 40;

        function toggleSeat(event) {
            const seat = event.target.closest('.seat');
            if (!seat || seat.classList.contains('reserved')) return;

            const seatId = parseInt(seat.dataset.id);
            if (seat.classList.contains('available') && selectedSeats.size < maxSeatsAllowed) {
                seat.classList.remove('available');
                seat.classList.add('selected');
                selectedSeats.add(seatId);
            } else if (seat.classList.contains('selected')) {
                seat.classList.remove('selected');
                seat.classList.add('available');
                selectedSeats.delete(seatId);
            }
            updateDetails();
        }

        function updateDetails() {
            tickets.textContent = selectedSeats.size > 0 ? [...selectedSeats].join(", ") : "-";
            price.textContent = `฿${selectedSeats.size * seatPrice}`;

            bookingDetails.style.display = selectedSeats.size > 0 ? 'flex' : 'none';

            // Clear current select options
            reservationTablesSelect.innerHTML = '';

            // Add selected seats to the select element
            selectedSeats.forEach(seatId => {
                const option = document.createElement('option');
                option.value = seatId;
                option.text = `Table ${seatId}`;
                option.selected = true;
                reservationTablesSelect.appendChild(option);
            });
        }

        seatBox.addEventListener('click', toggleSeat);
        reservationForm.addEventListener('submit', function(event) {
            event.preventDefault();
            // Here you can perform additional actions before form submission if needed
            this.submit();
        });

        updateDetails(); // Initial update on page load
    </script>
</body>

</html>
