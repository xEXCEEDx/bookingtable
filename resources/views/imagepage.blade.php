<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <div class="container mt-5">
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

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">อัปโหลดรูปโต๊ะหน้า Booking</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('upload.image') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">อัปโหลดรูปภาพ</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
