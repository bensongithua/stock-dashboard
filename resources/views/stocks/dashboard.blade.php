@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Top 5 Stock Performers</h4>
                    <a href="{{ route('upload.show') }}" class="btn btn-outline-primary">
                        <i class="fas fa-upload me-2"></i>Upload New Data
                    </a>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard') }}" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ $endDate }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-secondary me-2">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                Clear
                            </a>
                        </div>
                    </form>

                    @if($topPerformers->count() > 0)
                        <div class="mb-5">
                            <canvas id="performanceChart" height="100"></canvas>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Rank</th>
                                        <th>Stock Name</th>
                                        <th>First Price</th>
                                        <th>Last Price</th>
                                        <th>Price Gain</th>
                                        <th>Gain %</th>
                                        <th>Period</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topPerformers as $index => $stock)
                                        <tr>
                                            <td><strong>#{{ $index + 1 }}</strong></td>
                                            <td><strong>{{ $stock['stock_name'] }}</strong></td>
                                            <td>{{ number_format($stock['first_price'], 6) }}</td>
                                            <td>{{ number_format($stock['last_price'], 6) }}</td>
                                            <td class="{{ $stock['price_gain'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($stock['price_gain'], 6) }}
                                            </td>
                                            <td class="{{ $stock['gain_percentage'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                <strong>{{ $stock['gain_percentage'] }}%</strong>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($stock['first_date'])->format('M j, Y') }} - 
                                                {{ \Carbon\Carbon::parse($stock['last_date'])->format('M j, Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5>No stock data available</h5>
                            <p class="text-muted">Upload a CSV file to see performance analytics.</p>
                            <a href="{{ route('upload.show') }}" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Upload Your First CSV
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($topPerformers->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    const labels = @json($topPerformers->pluck('stock_name'));
    const gains = @json($topPerformers->pluck('gain_percentage'));
    
    const backgroundColors = gains.map(gain => 
        gain >= 0 ? 'rgba(46, 204, 113, 0.8)' : 'rgba(231, 76, 60, 0.8)'
    );
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Price Gain (%)',
                data: gains,
                backgroundColor: backgroundColors,
                borderColor: gains.map(gain => 
                    gain >= 0 ? 'rgba(46, 204, 113, 1)' : 'rgba(231, 76, 60, 1)'
                ),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Gain: ${context.parsed.y}%`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Percentage Gain (%)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Stock Symbols'
                    }
                }
            }
        }
    });
});
</script>
@endif
@endsection