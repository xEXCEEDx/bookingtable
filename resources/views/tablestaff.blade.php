<x-app-layout>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h2>Set Total Tables</h2>
                <form action="{{ route('tables.setTotal') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="total_tables" class="form-label">Total Tables</label>
                        <input type="number" class="form-control" id="total_tables" name="total_tables" value="{{ $tables->count() }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Total Tables</button>
                </form>
            </div>

            <div class="col-md-6">
                <h2>Manage Restaurant Tables</h2>
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
                <div class="container mt-3">
                    <table class="table">
                        <thead class="text-center">
                            <tr>
                                <th scope="col">Table Number</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tables as $table)
                                <tr class="text-center">
                                    <td>{{ $table->table_number }}</td>
                                    <td>{{ $table->status }}</td>
                                    <td>
                                        @if ($table->status == 'available')
                                            <form action="{{ route('tables.updateStatus', $table->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="reserved">
                                                <button type="submit" class="btn btn-primary btn-sm">Reserve</button>
                                            </form>
                                        @elseif ($table->status == 'reserved')
                                            <form action="{{ route('tables.updateStatus', $table->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="available">
                                                <button type="submit" class="btn btn-danger btn-sm">Available</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
