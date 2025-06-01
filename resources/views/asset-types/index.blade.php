@extends('layouts.app')

@section('content')

<style>
    .card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }
    .card-header {
        background-color: white;
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
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-6">
                <h1 class="m-0 fw-bold">Asset Types</h1>
            </div>
            <div class="col-md-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Asset Types</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- /.content-header -->

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

                <div class="d-flex justify-content-end mb-3 action-buttons">
                    <a href="{{ route('asset-types.create') }}" class="btn btn-dark"><i class="bi bi-plus-lg me-2"></i>Add Asset Type</a>
                </div>

                <!-- Asset Types Table -->
                <div class="card shadow">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold">Asset Types List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>QR Code</th>
                                        <th>Requestable</th>
                                        <th>Quantity</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($assetTypes as $index => $assetType)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $assetType->name }}</td>
                                        <td>{{ Str::limit($assetType->desc, 30) }}</td>
                                        <td><span class="badge {{ $assetType->status == 'Active' ? 'bg-success' : 'bg-secondary' }}">{{ $assetType->status }}</span></td>
                                        <td>
                                            @if($assetType->requires_qr_code)
                                                <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Yes</span>
                                            @else
                                                <span class="badge bg-secondary"><i class="bi bi-x-lg me-1"></i>No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assetType->is_requestable)
                                                <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Yes</span>
                                            @else
                                                <span class="badge bg-secondary"><i class="bi bi-x-lg me-1"></i>No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assetType->has_quantity)
                                                <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Yes</span>
                                            @else
                                                <span class="badge bg-secondary"><i class="bi bi-x-lg me-1"></i>No</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('asset-types.edit', $assetType->id) }}" class="btn btn-sm btn-success me-2"><i class="bi bi-pencil-square"></i></a>
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#archiveModal" 
                                                    data-id="{{ $assetType->id }}" data-name="{{ $assetType->name }}">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No asset types found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.Main content -->

<!-- Archive Confirmation Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="archiveModalLabel">Confirm Archive</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to archive <strong id="fieldName"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="archiveForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Archive</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to Handle Archive Modal -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const archiveButtons = document.querySelectorAll('[data-bs-target="#archiveModal"]');
        const fieldNameElement = document.getElementById('fieldName');
        const archiveForm = document.getElementById('archiveForm');

        archiveButtons.forEach(button => {
            button.addEventListener('click', function() {
                const fieldName = this.getAttribute('data-name');
                const fieldId = this.getAttribute('data-id');
                fieldNameElement.textContent = fieldName;

                // Fix URL construction
                archiveForm.action = "{{ url('asset-types/archive-asset-type/') }}/" + fieldId;
            });
        });
    });
</script>

@endsection 