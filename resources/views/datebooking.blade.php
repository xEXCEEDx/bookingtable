<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Reservation Date</title>
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
            height: 100vh;
            color: #ffffff;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: #2f2f2f;
            border-radius: 20px;
            box-shadow: 0 0 10px 2px #212121;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h5 {
            margin: 0;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Select Reservation Date</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('store-reservation-date') }}">
                            @csrf
                            <label for="reservation_date">Select reservation date:</label>
                            <input type="date" id="reservation_date" name="reservation_date" required>
                            <button type="submit">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('date-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const reservationDate = document.getElementById('reservation_date').value;
            window.location.href = `{{ route('table-selection') }}?reservation_date=${reservationDate}`;
        });
    </script>
</body>

</html>
