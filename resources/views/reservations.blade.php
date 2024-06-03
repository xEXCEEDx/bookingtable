<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <div class="container mt-5">
        @if (session('success'))
            <div class="alert alert-success" role="alert" id="successAlert">
                {{ session('success') }}
            </div>
            <script>
                setTimeout(function() {
                    document.getElementById('successAlert').style.display = 'none';
                }, 5000); // 5000 milliseconds = 5 seconds
            </script>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert" id="errorAlert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <script>
                setTimeout(function() {
                    document.getElementById('errorAlert').style.display = 'none';
                }, 5000); // 5000 milliseconds = 5 seconds
            </script>
        @endif

        <div class="row mb-3">
            <div class="col-md-3 mb-3 mb-md-0">
                <a href="{{ route('reservations.clearAll') }}" class="btn btn-danger w-auto">Clear All Reservations</a>
            </div>
            <div class="col-md-6"></div>
            <div class="col-md-3">
                <form action="{{ route('reservations.search') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="query" class="form-control" placeholder="Search">
                        <button type="submit" class="btn btn-primary w-auto">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @if ($reservations->isEmpty())
                    <div class="alert alert-info text-center">
                        ยังไม่มีการจอง
                    </div>
                @else
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Table Number</th>
                                <th scope="col">User Name</th>
                                <th scope="col">Reservation Time</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reservations as $reservation)
                                <tr>
                                    <td>{{ $reservation->table->table_number }}</td>
                                    <td>{{ $reservation->name }}</td>
                                    <td>{{ $reservation->reservation_time }}</td>
                                    <td>
                                        <span class="badge {{ $reservation->status == 'reserved' ? 'bg-primary' : 'bg-success' }}">
                                            {{ ucfirst($reservation->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('reservations.updateTable', $reservation->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
