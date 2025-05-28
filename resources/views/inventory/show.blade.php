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
    .nav-tabs .nav-link {
        color: #5a5c69;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        color: #212529;
        font-weight: 600;
        border-bottom: 3px solid #212529;
    }
    .component-card {
        transition: all 0.3s ease;
    }
    .component-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
    .view-toggle .btn {
        padding: 0.25rem 0.5rem;
    }
    /* Modal backdrop styling */
    .modal-backdrop {
        opacity: 0.5 !important;
    }
    /* Add dark overlay class */
    .dark-modal .modal-backdrop {
        opacity: 0.8 !important;
        background-color: #000;
    }
    /* Body class when modal is open */
    body.modal-open-dark::after {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.7);
        z-index: 1040;
    }
    /* Notes/Comments styling */
    .comment {
        border-bottom: 1px solid #e3e6f0;
        padding: 10px 0;
    }
    .comment:last-child {
        border-bottom: none;
    }
    .comment-header {
        display: flex;
        align-items: center;
        margin-bottom: 6px;
    }
    .comment-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-right: 8px;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #495057;
        font-size: 0.8rem;
    }
    .comment-user {
        font-weight: 600;
        margin-bottom: 0;
        font-size: 0.85rem;
        line-height: 1.2;
    }
    .comment-time {
        font-size: 0.7rem;
        color: #6c757d;
        margin-bottom: 0;
        line-height: 1.2;
    }
    .comment-content {
        margin-left: 38px;
        font-size: 0.85rem;
        /* Use white-space normal and replace line breaks with <br> tags in PHP */
        white-space: normal;
        margin-top: 0;
        padding-top: 0;
        line-height: 1.4;
        overflow-wrap: break-word;
        word-break: break-word;
    }
    .comment-box {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 10px;
        margin-top: 15px;
    }
    .comments-section {
        max-height: 500px;
        overflow-y: auto;
        margin-bottom: 15px;
    }
    /* Custom scrollbar for comments section */
    .comments-section::-webkit-scrollbar {
        width: 6px;
    }
    .comments-section::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .comments-section::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    .comments-section::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    .comment-actions {
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .comment:hover .comment-actions {
        opacity: 1;
    }
    /* Additional styling to make notes more compact */
    .card-body {
        padding: 1rem;
    }
    
    .comment + .comment {
        margin-top: 5px;
    }
    
    textarea.form-control {
        padding: 0.5rem;
    }
    
    /* Fix note content spacing */
    .comment-content p {
        margin-bottom: 0;
    }
    
    /* Specific fix for extra whitespace */
    .comment-content br + br {
        content: "";
        display: block;
        margin-top: 0.5em;
    }
    
    .comment-content br {
        line-height: 1;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-6">
                <h1 class="m-0 fw-bold">Asset Details</h1>
            </div>
            <div class="col-md-6" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb float-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Asset Details</li>
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
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('inventory.edit', $inventoryItem->id) }}" class="btn btn-success me-2">
                        <i class="bi bi-pencil-square me-2"></i>Edit Asset
                    </a>
                    <a href="{{ route('inventory.index') }}" class="btn btn-danger">
                        <i class="bi bi-arrow-return-left me-2"></i>Back
                    </a>
                </div>

                <div class="row">
                    <!-- Left Side - Asset Details with Tabs -->
                    <div class="col-lg-8">
                        <div class="card shadow h-100">
                            <div class="card-body">
                                <ul class="nav nav-tabs" id="assetTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active px-4 py-3" id="asset_profile" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">
                                            <i class="bi bi-box-seam me-2"></i>Asset Details
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link px-4 py-3" id="asset_history" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">
                                            <i class="bi bi-clock-history me-2"></i>History
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content mt-4" id="assetTabsContent">
                                    <!-- Asset Profile Tab -->
                                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="asset_profile">
                                        <div class="row">
                                            <!-- Asset Image and QR Code (Left) -->
                                            <div class="col-md-4 text-center">
                                                <div class="mb-4">
                                                    @if($inventoryItem->image_path)
                                                        <img src="{{ asset('storage/' . $inventoryItem->image_path) }}" alt="{{ $inventoryItem->item_name }}" class="img-fluid rounded shadow mb-3" style="max-width: 100%; max-height: 250px;">
                                                    @else
                                                        <div class="rounded bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 200px; height: 200px;">
                                                            <i class="bi bi-box-seam fs-1 text-secondary"></i>
                                                        </div>
                                                    @endif
                                                    <h4 class="mt-3 fw-bold">{{ $inventoryItem->item_name }}</h4>
                                                    <p class="text-muted">{{ $inventoryItem->category->category ?? 'N/A' }}</p>
                                                    <span class="badge bg-{{ $inventoryItem->status == 'Active' ? 'success' : 'warning' }} px-3 py-2">
                                                        {{ $inventoryItem->status ?? 'Active' }}
                                                    </span>
                                                </div>
                                                
                                                <!-- QR Code Section (moved from tab) -->
                                                @if(isset($inventoryItem->assetType) && $inventoryItem->assetType->requires_qr_code && $qrCode)
                                                <div class="mt-4 pt-2 border-top">
                                                    <h5 class="mb-3">QR Code</h5>
                                                    <div class="mb-3">
                                                        {!! $qrCode !!}
                                                    </div>
                                                    <div class="mt-2">
                                                        <p class="fw-bold mb-1">{{ $inventoryItem->asset_tag }}</p>
                                                        <p class="mb-2">{{ $inventoryItem->serial_no }}</p>
                                                        <button onclick="printQRCode()" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-print me-1"></i> Print QR
                                                        </button>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            <!-- Asset Information (Right) -->
                                            <div class="col-md-8">
                                                <table class="table table-striped table-hover">
                                                    <tr>
                                                        <th width="180">Asset Tag</th>
                                                        <td>{{ $inventoryItem->asset_tag }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Serial Number</th>
                                                        <td>{{ $inventoryItem->serial_no }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Model Number</th>
                                                        <td>{{ $inventoryItem->model_no }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Category</th>
                                                        <td>{{ $inventoryItem->category->category ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Asset Type</th>
                                                        <td>{{ $inventoryItem->assetType->name ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Department</th>
                                                        <td>{{ $inventoryItem->department->name ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Owner</th>
                                                        <td>
                                                            @if($inventoryItem->users_id && $inventoryItem->user)
                                                                <a href="{{ route('users.view', $inventoryItem->user->id) }}">
                                                                    {{ $inventoryItem->user->first_name }} {{ $inventoryItem->user->last_name }}
                                                                </a>
                                                            @elseif($inventoryItem->department_id && $inventoryItem->department)
                                                                <span class="badge bg-info">Department: {{ $inventoryItem->department->name }}</span>
                                                            @elseif($inventoryItem->location_id && $inventoryItem->location)
                                                                <span class="badge bg-secondary">Location: {{ $inventoryItem->location->name }}</span>
                                                            @else
                                                                <span class="badge bg-secondary">Not assigned</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Date Purchased</th>
                                                        <td>{{ \Carbon\Carbon::parse($inventoryItem->date_purchased)->format('F d, Y') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Purchased From</th>
                                                        <td>{{ $inventoryItem->purchased_from }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Location</th>
                                                        <td>{{ $inventoryItem->location->name ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Manufacturer</th>
                                                        <td>{{ $inventoryItem->manufacturer }}</td>
                                                    </tr>
                                                    
                                                    <!-- Include Custom Fields directly in details tab -->
                                                    @if($inventoryItem->custom_fields && count($inventoryItem->custom_fields) > 0)
                                                        @foreach($allCustomFields as $field)
                                                            @if(isset($inventoryItem->custom_fields[$field->name]))
                                                            <tr>
                                                                <th>{{ $field->name }}</th>
                                                                <td>
                                                                    @php
                                                                        $fieldValue = $inventoryItem->custom_fields[$field->name];
                                                                    @endphp
                                                                    
                                                                    @if(is_array($fieldValue))
                                                                        @if(isset($fieldValue['original_name']))
                                                                            <a href="{{ asset('storage/' . $fieldValue['path']) }}" target="_blank">
                                                                                {{ $fieldValue['original_name'] }}
                                                                            </a>
                                                                        @else
                                                                            {{ implode(', ', $fieldValue) }}
                                                                        @endif
                                                                    @else
                                                                        {{ $fieldValue }}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- History Tab -->
                                    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="asset_history">
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Asset history tracking will be implemented in a future update.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Side (Asset Notes) -->
                    <div class="col-lg-4">
                        <div class="card shadow h-100">
                            <div class="card-header bg-white">
                                <h5 class="card-title m-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Asset Notes</h5>
                            </div>
                            <div class="card-body">
                                @if(isset($assetNotes) && count($assetNotes) > 0)
                                    <div class="comments-section">
                                        @foreach($assetNotes->sortBy('created_at') as $note)
                                        <div class="comment">
                                            <div class="comment-header">
                                                <div class="comment-avatar">
                                                    @if($note->user && $note->user->profile_picture)
                                                        <img src="{{ asset($note->user->profile_picture) }}" alt="Profile" width="40" height="40" class="rounded-circle">
                                                    @else
                                                        {{ $note->user ? substr($note->user->first_name, 0, 1) . substr($note->user->last_name, 0, 1) : 'U' }}
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="comment-user">{{ $note->user ? $note->user->first_name . ' ' . $note->user->last_name : 'Unknown User' }}</p>
                                                    <p class="comment-time">{{ $note->created_at->setTimezone('Asia/Manila')->format('F d, Y h:i A') }}</p>
                                                </div>
                                                @if(Auth::id() == $note->user_id && !str_contains(isset($note->id) ? $note->id : '', 'transfer_'))
                                                <div class="comment-actions">
                                                    <button type="button" class="btn btn-sm text-primary edit-note-btn" data-id="{{ $note->id }}" data-content="{{ $note->content }}" data-bs-toggle="modal" data-bs-target="#editNoteModal">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm text-danger delete-note-btn" data-id="{{ $note->id }}" data-bs-toggle="modal" data-bs-target="#deleteNoteModal">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="comment-content">
                                                {!! nl2br(e($note->content)) !!}
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    @if($inventoryItem->log_note)
                                    <div class="comment">
                                        <div class="comment-header">
                                            <div class="comment-avatar">U</div>
                                            <div>
                                                <p class="comment-user">System</p>
                                                <p class="comment-time">{{ $inventoryItem->updated_at->setTimezone('Asia/Manila')->format('F d, Y h:i A') }}</p>
                                            </div>
                                        </div>
                                        <div class="comment-content">
                                            {!! nl2br(e($inventoryItem->log_note)) !!}
                                        </div>
                                    </div>
                                    @else
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        No notes have been added yet.
                                    </div>
                                    @endif
                                @endif
                                
                                <!-- Comment Box -->
                                <div class="comment-box">
                                    <form action="{{ route('inventory.add-note', $inventoryItem->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="note_content" class="form-label">Add a note</label>
                                            <textarea class="form-control" id="note_content" name="note_content" rows="3" placeholder="Write your note here..."></textarea>
                                        </div>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-plus-circle me-2"></i>Add Note
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Note Modal -->
<div class="modal fade" id="editNoteModal" tabindex="-1" aria-labelledby="editNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="editNoteModalLabel">Edit Note</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editNoteForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_note_content" class="form-label">Note Content</label>
                        <textarea class="form-control" id="edit_note_content" name="note_content" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Note Modal -->
<div class="modal fade" id="deleteNoteModal" tabindex="-1" aria-labelledby="deleteNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteNoteModalLabel">Delete Note</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this note? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteNoteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Note Actions -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Edit Note Modal Handler
        const editButtons = document.querySelectorAll('.edit-note-btn');
        const editForm = document.getElementById('editNoteForm');
        const editNoteContent = document.getElementById('edit_note_content');
        
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const noteId = this.getAttribute('data-id');
                const noteContent = this.getAttribute('data-content');
                
                // Set form action URL with the proper note ID
                editForm.action = "{{ url('inventory/update-note') }}/" + {{ $inventoryItem->id }} + "/" + noteId;
                
                // Set form content
                editNoteContent.value = noteContent;
            });
        });
        
        // Delete Note Modal Handler
        const deleteButtons = document.querySelectorAll('.delete-note-btn');
        const deleteForm = document.getElementById('deleteNoteForm');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const noteId = this.getAttribute('data-id');
                
                // Set form action URL with the proper note ID
                deleteForm.action = "{{ url('inventory/delete-note') }}/" + {{ $inventoryItem->id }} + "/" + noteId;
            });
        });
        
        // Fix line break issues in textareas
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            textarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.shiftKey) {
                    // Let the browser handle Shift+Enter naturally
                    return true;
                }
                if (e.key === 'Enter' && !e.shiftKey) {
                    // Prevent default behavior for normal Enter (form submission)
                    e.preventDefault();
                    // Add the note when user presses Enter without Shift
                    const form = this.closest('form');
                    if (form && this.value.trim().length > 0) {
                        form.submit();
                    }
                }
            });
        });
    });

    // Function to print just the QR code
    function printQRCode() {
        // Get the QR code content from the main page
        const qrCodeSection = document.querySelector('.col-md-4 .mt-4.pt-2');
        
        if (!qrCodeSection) {
            alert('QR code not found');
            return;
        }
        
        const printContent = qrCodeSection.innerHTML;
        const originalContent = document.body.innerHTML;
        
        document.body.innerHTML = `
            <div style="text-align: center; padding: 20px;">
                <h3>QR Code for ${document.querySelector('.col-md-4 h4').textContent}</h3>
                ${printContent}
            </div>
            <style>
                button { display: none; }
            </style>
        `;
        
        window.print();
        document.body.innerHTML = originalContent;
        
        // Reattach event listeners after restoring content
        document.addEventListener('DOMContentLoaded', function() {
            // Edit Note Modal Handler
            const editButtons = document.querySelectorAll('.edit-note-btn');
            const editForm = document.getElementById('editNoteForm');
            const editNoteContent = document.getElementById('edit_note_content');
            
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const noteId = this.getAttribute('data-id');
                    const noteContent = this.getAttribute('data-content');
                    
                    // Set form action URL with the proper note ID
                    editForm.action = "{{ url('inventory/update-note') }}/" + {{ $inventoryItem->id }} + "/" + noteId;
                    
                    // Set form content
                    editNoteContent.value = noteContent;
                });
            });
            
            // Delete Note Modal Handler
            const deleteButtons = document.querySelectorAll('.delete-note-btn');
            const deleteForm = document.getElementById('deleteNoteForm');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const noteId = this.getAttribute('data-id');
                    
                    // Set form action URL with the proper note ID
                    deleteForm.action = "{{ url('inventory/delete-note') }}/" + {{ $inventoryItem->id }} + "/" + noteId;
                });
            });
            
            // Fix line break issues in textareas
            const textareas = document.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                textarea.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && e.shiftKey) {
                        // Let the browser handle Shift+Enter naturally
                        return true;
                    }
                    if (e.key === 'Enter' && !e.shiftKey) {
                        // Prevent default behavior for normal Enter (form submission)
                        e.preventDefault();
                        // Add the note when user presses Enter without Shift
                        const form = this.closest('form');
                        if (form && this.value.trim().length > 0) {
                            form.submit();
                        }
                    }
                });
            });
        });
    }
    
    // Function to copy QR URL to clipboard
    function copyUrl() {
        const assetUrl = "{{ route('inventory.show', $inventoryItem->id) }}";
        
        // Use modern clipboard API if available
        if (navigator.clipboard) {
            navigator.clipboard.writeText(assetUrl)
                .then(() => alert('URL copied to clipboard!'))
                .catch(err => {
                    console.error('Failed to copy: ', err);
                    fallbackCopy(assetUrl);
                });
        } else {
            fallbackCopy(assetUrl);
        }
    }
    
    function fallbackCopy(text) {
        // Create temporary input element
        const input = document.createElement('input');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        alert('URL copied to clipboard!');
    }
</script>

@endsection 