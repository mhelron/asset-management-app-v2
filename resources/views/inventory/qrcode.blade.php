@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>QR Code for {{ $inventoryItem->item_name }}</span>
                    <div>
                        <button onclick="window.print();" class="btn btn-sm btn-primary">
                            <i class="fas fa-print"></i> Print QR Code
                        </button>
                        <a href="{{ route('inventory.show', $inventoryItem->id) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Asset
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            {!! $qrCode !!}
                        </div>
                        <div class="mt-2">
                            <p class="font-weight-bold">{{ $inventoryItem->asset_tag }}</p>
                            <p>{{ $inventoryItem->item_name }}</p>
                            <p><small>{{ $inventoryItem->serial_no }}</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .card, .card * {
            visibility: visible;
        }
        .card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .card-header button, 
        .card-header a {
            display: none;
        }
    }
</style>
@endsection 