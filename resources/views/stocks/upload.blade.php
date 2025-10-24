cat > resources/views/stocks/upload.blade.php << 'EOF'
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Upload Stock Prices CSV</h4>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning">
                            {{ session('warning') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('upload.import') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Select CSV File</label>
                            <input type="file" 
                                   class="form-control @error('csv_file') is-invalid @enderror" 
                                   id="csv_file" 
                                   name="csv_file" 
                                   accept=".csv" 
                                   required>
                            
                            @error('csv_file')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            
                            <div class="form-text">
                                Expected CSV format: stock,price,date (e.g., "Safaricom",22.25,2019-01-02)
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Upload CSV
                        </button>
                        
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary ms-2">
                            <i class="fas fa-chart-line me-2"></i>View Dashboard
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
EOF