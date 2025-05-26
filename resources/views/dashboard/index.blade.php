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
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 stat-card-icon bg-primary bg-opacity-10">
                            <i class="bi bi-box-seam text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Items</h6>
                            <h2 class="mb-0 fw-bold">{{ \App\Models\Inventory::count() }}</h2>
                            <a href="{{ route('inventory.index') }}" class="text-primary small">View all Items →</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 stat-card-icon bg-success bg-opacity-10">
                            <i class="bi bi-cpu text-success" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Components</h6>
                            <h2 class="mb-0 fw-bold">{{ \App\Models\Components::count() }}</h2>
                            <a href="#" class="text-success small">View all components →</a>
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
                            <h2 class="mb-0 fw-bold">{{ \App\Models\User::count() }}</h2>
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
                            <h2 class="mb-0 fw-bold">{{ \App\Models\Inventory::whereNull('users_id')->count() }}</h2>
                            <a href="{{ route('inventory.index') }}" class="text-warning small">View unassigned →</a>
                        </div>
                    </div>
                </div>
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
                                    <p class="mb-0 fw-bold">Component marked for maintenance</p>
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
                                        <th>Tag</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(\App\Models\Inventory::with('category')->latest()->take(5)->get() as $asset)
                                    <tr>
                                        <td>
                                            <a href="{{ route('inventory.show', $asset->id) }}" class="text-decoration-none">{{ $asset->item_name }}</a>
                                        </td>
                                        <td>{{ $asset->category->category ?? 'N/A' }}</td>
                                        <td><span class="badge bg-secondary">{{ $asset->asset_tag }}</span></td>
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
            labels: ['Laptops', 'Desktops', 'Monitors', 'Printers', 'Phones', 'Other'],
            datasets: [{
                label: 'Number of Assets',
                data: [12, 19, 8, 5, 10, 3],
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
            labels: ['IT', 'HR', 'Finance', 'Marketing', 'Operations'],
            datasets: [{
                data: [30, 20, 15, 10, 25],
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