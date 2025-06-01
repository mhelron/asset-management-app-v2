@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #f2f5f9;
    }
    .card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }
    .card-header {
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 1.35rem;
    }
    .table thead th {
        border-bottom: 2px solid #e3e6f0;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }
    .stat-card-icon {
        border-radius: 50%;
        padding: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .letter-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.6rem;
        font-family: 'Arial', sans-serif;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card:hover .letter-icon {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    @media (max-width: 1200px) {
        .asset-type-card {
            flex: 0 0 calc(33.333% - 20px);
        }
    }
    @media (max-width: 992px) {
        .asset-type-card {
            flex: 0 0 calc(50% - 20px);
        }
    }
    @media (max-width: 576px) {
        .asset-type-card {
            flex: 0 0 calc(100% - 20px);
        }
    }
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="m-0 fw-bold pb-4">Dashboard</h1>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="content">
    <div class="container">
        <!-- Primary Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 stat-card-icon bg-primary bg-opacity-10">
                            <i class="bi bi-box-seam text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Items</h6>
                            <h2 class="mb-0 fw-bold">{{ $totalItems }}</h2>
                            <a href="{{ route('inventory.index') }}" class="text-primary small">View all Items →</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 stat-card-icon bg-info bg-opacity-10">
                            <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Users</h6>
                            <h2 class="mb-0 fw-bold">{{ $totalUsers }}</h2>
                            <a href="{{ route('users.index') }}" class="text-info small">View all users →</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 stat-card-icon bg-warning bg-opacity-10">
                            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Unassigned Items</h6>
                            <h2 class="mb-0 fw-bold">{{ $unassignedItems }}</h2>
                            <a href="{{ route('inventory.index') }}" class="text-warning small">View unassigned →</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 stat-card-icon bg-success bg-opacity-10">
                            <i class="bi bi-tags text-success" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Asset Types</h6>
                            <h2 class="mb-0 fw-bold">{{ $assetTypes->count() }}</h2>
                            <a href="{{ route('asset-types.index') }}" class="text-success small">Manage types →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asset Type Statistics Cards -->
        <div class="mb-4">
            <h5 class="mb-3 fw-bold">Asset Type Statistics</h5>
            <div class="row">
                @php
                    // Define a list of colors to use for the cards
                    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                @endphp
                
                @foreach($assetTypeStats as $index => $type)
                    @php
                        $colorIndex = $index % count($colors);
                        $color = $colors[$colorIndex];
                        
                        // Get the first letter of the type name for the icon
                        $firstLetter = strtoupper(substr($type->name, 0, 1));
                        
                        // For types with quantity tracking, count the total quantity
                        $totalQuantity = 0;
                        if ($type->has_quantity) {
                            $totalQuantity = \App\Models\Inventory::where('asset_type_id', $type->id)
                                ->sum('quantity');
                        }
                    @endphp
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="me-3 letter-icon bg-{{ $color }} bg-opacity-10 text-{{ $color }}">
                                    {{ $firstLetter }}
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">{{ $type->name }}</h6>
                                    <h2 class="mb-0 fw-bold">
                                        {{ $type->inventories_count }}
                                        @if($type->has_quantity && $totalQuantity > 0)
                                            <small class="fs-6 text-muted">({{ $totalQuantity }} units)</small>
                                        @endif
                                    </h2>
                                    <a href="{{ route('inventory.index') }}?type={{ $type->id }}" class="text-{{ $color }} small">View items →</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="row mb-4">
            <!-- Asset Distribution by Category -->
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-white">
                        <h5 class="card-title m-0 fw-bold">Asset Distribution by Category</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px;">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-lg-4">
                <div class="card shadow">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0 fw-bold">Recent Activity</h5>
                        <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex border-0 py-3">
                                <div class="me-3">
                                    <span class="badge rounded-circle bg-success p-2">
                                        <i class="bi bi-plus"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-bold">New laptop added to inventory</p>
                                    <small class="text-muted">Today, 10:30 AM</small>
                                </div>
                            </li>
                            <li class="list-group-item d-flex border-0 py-3">
                                <div class="me-3">
                                    <span class="badge rounded-circle bg-primary p-2">
                                        <i class="bi bi-arrow-right"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-bold">Monitor assigned to John Doe</p>
                                    <small class="text-muted">Yesterday, 2:15 PM</small>
                                </div>
                            </li>
                            <li class="list-group-item d-flex border-0 py-3">
                                <div class="me-3">
                                    <span class="badge rounded-circle bg-warning p-2">
                                        <i class="bi bi-exclamation"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-bold">Low stock alert for consumable items</p>
                                    <small class="text-muted">Sep 15, 2023</small>
                                </div>
                            </li>
                            <li class="list-group-item d-flex border-0 py-3">
                                <div class="me-3">
                                    <span class="badge rounded-circle bg-danger p-2">
                                        <i class="bi bi-archive"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-bold">Keyboard archived from inventory</p>
                                    <small class="text-muted">Sep 12, 2023</small>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recently Added Assets -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0 fw-bold">Recently Added Assets</h5>
                        <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Asset Name</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentAssets as $asset)
                                    <tr>
                                        <td>
                                            <a href="{{ route('inventory.show', $asset->id) }}" class="text-decoration-none">{{ $asset->item_name }}</a>
                                        </td>
                                        <td>{{ $asset->category->category ?? 'N/A' }}</td>
                                        <td>{{ $asset->assetType->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $asset->status == 'Active' ? 'success' : 'warning' }}">
                                                {{ $asset->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3">No assets found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assets by Department -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title m-0 fw-bold">Assets by Department</h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div style="height: 220px; width: 220px;">
                            <canvas id="departmentChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Category Chart
    var categoryCtx = document.getElementById('categoryChart').getContext('2d');
    var categoryChart = new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($categoryData as $category)
                    '{{ $category->category }}',
                @endforeach
            ],
            datasets: [{
                label: 'Number of Assets',
                data: [
                    @foreach($categoryData as $category)
                        {{ $category->inventories_count }},
                    @endforeach
                ],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)'
                ],
                borderWidth: 1,
                borderColor: [
                    'rgb(54, 162, 235)',
                    'rgb(255, 99, 132)',
                    'rgb(75, 192, 192)',
                    'rgb(255, 206, 86)',
                    'rgb(153, 102, 255)',
                    'rgb(255, 159, 64)'
                ],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Department Chart
    var deptCtx = document.getElementById('departmentChart').getContext('2d');
    var deptChart = new Chart(deptCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($departmentData as $dept)
                    '{{ $dept->name }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($departmentData as $dept)
                        {{ $dept->inventories_count }},
                    @endforeach
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 206, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                }
            },
            cutout: '70%'
        }
    });
});
</script>
@endsection