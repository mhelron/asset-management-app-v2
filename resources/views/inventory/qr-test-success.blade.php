    @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="m-0"><i class="fas fa-check-circle me-2"></i> QR Code Test Successful!</h4>
                </div>

                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="mb-4">
                            <i class="fas fa-qrcode text-success" style="font-size: 64px;"></i>
                        </div>
                        <h3 class="mb-3">Your QR Code is Working Correctly</h3>
                        <p class="lead">The QR code for asset <strong>"{{ $inventoryItem->item_name }}"</strong> was scanned successfully.</p>
                        
                        <div class="alert alert-info mt-4">
                            <p><strong>Asset Details:</strong></p>
                            <ul class="mb-0 text-start">
                                <li><strong>Asset Tag:</strong> {{ $inventoryItem->asset_tag }}</li>
                                <li><strong>Serial Number:</strong> {{ $inventoryItem->serial_no }}</li>
                                <li><strong>Type:</strong> {{ $inventoryItem->assetType->name ?? 'N/A' }}</li>
                                <li><strong>Category:</strong> {{ $inventoryItem->category->category ?? 'N/A' }}</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('inventory.show', $inventoryItem->id) }}" class="btn btn-primary me-2">
                                <i class="fas fa-info-circle me-1"></i> View Asset Details
                            </a>
                            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                                <i class="fas fa-list me-1"></i> Back to Inventory
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 