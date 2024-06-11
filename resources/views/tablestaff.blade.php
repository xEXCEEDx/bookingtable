<x-app-layout>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-3">
                <h2>Set Total Tables</h2>
                <form action="{{ route('tables.setTotal') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="total_tables" class="form-label">Total Tables</label>
                        <input type="number" class="form-control" id="total_tables" name="total_tables" value="{{ $tables->count() }}" required>
                    </div>
                    <div>
                    <button type="submit" class="btn btn-primary mb-5">Update Total Tables</button>
                </form>
            </div>
</div>
            <div class="col-md-9">
                <h2>Manage Restaurant Tables</h2>
                @if (session('success'))
                    <div class="alert alert-success" role="alert" id="successAlert">
                        {{ session('success') }}
                    </div>
                    <script>
                        setTimeout(function() {
                            document.getElementById('successAlert').style.display = 'none';
                        }, 5000);
                    </script>
                @endif
                <div class="container mt-3">
                    <table class="table table-bordered">
                        <thead class="text-center">
                            <tr>
                                <th scope="col">Table Number</th>
                                <th scope="col">Today ({{ now()->format('Y-m-d') }})</th>
                                <th scope="col">Tomorrow ({{ now()->addDay()->format('Y-m-d') }})</th>
                                <th scope="col">Day After Tomorrow ({{ now()->addDays(2)->format('Y-m-d') }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tables as $table)
                                <tr class="text-center">
                                    <td>{{ $table->table_number }}</td>
                                    <td>
                                        @php
                                            $statusToday = $table->statuses->where('date', now()->format('Y-m-d'))->first();
                                        @endphp
                                        <form action="{{ route('tables.updateStatus', $table->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="date" value="{{ now()->format('Y-m-d') }}" />
                                            <input type="hidden" name="status" value="{{ $statusToday && $statusToday->status == 'reserved' ? 'available' : 'reserved' }}" />
                                            <button class="btn btn-{{ $statusToday && $statusToday->status == 'reserved' ? 'danger' : 'primary' }}" type="submit">
                                                {{ $statusToday && $statusToday->status == 'reserved' ? 'Cancel' : 'Reserve' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        @php
                                            $statusTomorrow = $table->statuses->where('date', now()->addDay()->format('Y-m-d'))->first();
                                        @endphp
                                        <form action="{{ route('tables.updateStatus', $table->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="date" value="{{ now()->addDay()->format('Y-m-d') }}" />
                                            <input type="hidden" name="status" value="{{ $statusTomorrow && $statusTomorrow->status == 'reserved' ? 'available' : 'reserved' }}" />
                                            <button class="btn btn-{{ $statusTomorrow && $statusTomorrow->status == 'reserved' ? 'danger' : 'primary' }}" type="submit">
                                                {{ $statusTomorrow && $statusTomorrow->status == 'reserved' ? 'Cancel' : 'Reserve' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        @php
                                            $statusAfterTomorrow = $table->statuses->where('date', now()->addDays(2)->format('Y-m-d'))->first();
                                        @endphp
                                        <form action="{{ route('tables.updateStatus', $table->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="date" value="{{ now()->addDays(2)->format('Y-m-d') }}" />
                                            <input type="hidden" name="status" value="{{ $statusAfterTomorrow && $statusAfterTomorrow->status == 'reserved' ? 'available' : 'reserved' }}" />
                                            <button class="btn btn-{{ $statusAfterTomorrow && $statusAfterTomorrow->status == 'reserved' ? 'danger' : 'primary' }}" type="submit">
                                                {{ $statusAfterTomorrow && $statusAfterTomorrow->status == 'reserved' ? 'Cancel' : 'Reserve' }}
                                            </button>
                                        </form>
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
