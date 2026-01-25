@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-gray-800 fw-bold">Menu Builder</h5>
                    <div>
                        <button id="saveOrder" class="btn btn-success btn-sm me-2" style="display: none;">
                            <i class="bi bi-check-lg me-1"></i> Save Order
                        </button>
                        <a href="{{ route('admin.menus.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i> Add New Menu
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dd" id="menu-nestable">
                        <ol class="dd-list">
                            @foreach($menus as $menu)
                                @include('admin.menus.item', ['menu' => $menu])
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.js"></script>
<style>
    .dd { max-width: 100%; }
    .dd-handle { 
        height: auto; 
        padding: 10px 15px; 
        border: 1px solid #e5e7eb; 
        background: #fff; 
        border-radius: 4px; 
        margin-bottom: 10px;
        cursor: move;
    }
    .dd-handle:hover { background: #f8f9fa; }
    .dd-item > button { margin-top: 8px; }
    .dd-placeholder { border: 1px dashed #ccc; background: #f8f9fa; }
</style>
<script>
    $(document).ready(function() {
        $('#menu-nestable').nestable({
            maxDepth: 5
        }).on('change', function() {
            $('#saveOrder').fadeIn();
        });

        $('#saveOrder').click(function() {
            var serializedData = $('#menu-nestable').nestable('serialize');
            
            $.ajax({
                url: "{{ route('admin.menus.order') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    order: serializedData
                },
                success: function(response) {
                    $('#saveOrder').fadeOut();
                    alert('Menu order saved successfully!');
                },
                error: function() {
                    alert('Error saving menu order.');
                }
            });
        });
    });
</script>
@endpush
@endsection
