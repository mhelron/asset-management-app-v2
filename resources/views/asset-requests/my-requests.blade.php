@extends('layouts.app')

@section('content')
<!-- Content Header -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">My Asset Requests</h1>
            </div>
            <div class="col-sm-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">My Asset Requests</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <!-- Alert Box for Success -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Alert Box for Errors-->
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-diamond me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold">My Requests</h5>
                        <a href="{{ route('asset-requests.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i> New Request
                        </a>
                    </div>
                    <div class="card-body">
                        @if(count($assetRequests) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Asset</th>
                                            <th>Status</th>
                                            <th>Date Needed</th>
                                            <th>Date Requested</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assetRequests as $request)
                                            <tr>
                                                <td>{{ $request->id }}</td>
                                                <td>
                                                    <a href="{{ route('inventory.show', $request->inventory->id) }}">
                                                        {{ $request->inventory->item_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @if($request->status == 'Pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($request->status == 'Approved')
                                                        <span class="badge bg-success">Approved</span>
                                                    @elseif($request->status == 'Rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                    @elseif($request->status == 'Completed')
                                                        <span class="badge bg-primary">Completed</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $request->status }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($request->date_needed)->format('M d, Y') }}</td>
                                                <td>{{ $request->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <a href="{{ route('asset-requests.show', $request->id) }}" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if($request->status == 'Pending')
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal" data-id="{{ $request->id }}">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-4">
                                {{ $assetRequests->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">No Requests</h5>
                                <p class="text-muted">You haven't made any asset requests yet.</p>
                                <a href="{{ route('asset-requests.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-lg me-1"></i> Make a Request
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Request Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancelModalLabel">Cancel Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this request? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep Request</button>
                <form id="cancelForm" action="" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="Cancelled">
                    <input type="hidden" name="status_note" value="Cancelled by user">
                    <button type="submit" class="btn btn-danger">Yes, Cancel Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set up cancel modal
        const cancelModal = document.getElementById('cancelModal');
        if (cancelModal) {
            cancelModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const requestId = button.getAttribute('data-id');
                const form = document.getElementById('cancelForm');
                form.action = `{{ url('asset-requests/update-status') }}/${requestId}`;
            });
        }
    });
</script>
@endsection 