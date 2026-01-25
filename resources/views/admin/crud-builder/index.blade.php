@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 text-primary"><i class="bi bi-tools me-2"></i>CRUD Builder</h5>
        <small class="text-muted">Generate robust CRUDs in seconds</small>
      </div>
      <div class="card-body p-4">
        <form action="{{ route('crud-builder.generate') }}" method="POST" id="crudForm">
          @csrf
          
          <div class="row g-4 mb-4">
            {{-- Left Column: Basic Info --}}
            <div class="col-md-6">
                <div class="card bg-light border-0 h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-3 text-muted">Basic Information</h6>
                        
                        {{-- Model Name --}}
                        <div class="mb-3">
                            <label for="model_name" class="form-label fw-bold">Model Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="model_name" name="model_name" placeholder="e.g. UserLog" required>
                            <div class="form-text">PascalCase (e.g., BlogPost)</div>
                        </div>
    
                        {{-- Custom Table Name --}}
                        <div class="mb-3">
                            <label for="table_name" class="form-label fw-bold">Table Name</label>
                            <input type="text" class="form-control" id="table_name" name="table_name" placeholder="e.g. user_logs">
                            <div class="form-text">Auto-generated (snake_case plural)</div>
                        </div>
                    </div>
                </div>
            </div>
    
            {{-- Right Column: Configuration --}}
            <div class="col-md-6">
                <div class="card bg-light border-0 h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-3 text-muted">Configuration</h6>
    
                        {{-- Menu Icon --}}
                        <div class="mb-3">
                            <label for="menu_icon" class="form-label fw-bold">Menu Icon</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i id="icon_preview" class="fa-solid fa-circle"></i></span>
                                <input type="text" class="form-control" id="menu_icon" name="menu_icon" placeholder="fa-solid fa-circle">
                                <button class="btn btn-outline-primary" type="button" id="open_icon_modal">Select Icon</button>
                            </div>
                        </div>
    
                        {{-- Paginate --}}
                        <div class="mb-3">
                            <label for="paginate" class="form-label fw-bold">Pagination Limit</label>
                            <input type="number" class="form-control" id="paginate" name="paginate" value="10">
                        </div>
    
                        {{-- Options --}}
                        <div class="mb-0">
                            <label class="form-label fw-bold d-block mb-2">Features</label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="soft_delete" name="soft_delete" value="1" checked>
                                    <label class="form-check-label" for="soft_delete">Soft Delete</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="datatables" name="datatables" value="1" checked>
                                    <label class="form-check-label" for="datatables">Datatables</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="migration" name="migration" value="1" checked>
                                    <label class="form-check-label" for="migration">Migration</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="migrate" name="migrate" value="1">
                                    <label class="form-check-label" for="migrate">Run Migrate</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="test_cases" name="test_cases" value="1">
                                    <label class="form-check-label" for="test_cases">Test Cases</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="searchable" name="searchable" value="1" checked>
                                    <label class="form-check-label" for="searchable">Searchable</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="exportable" name="exportable" value="1" checked>
                                    <label class="form-check-label" for="exportable">Exportable</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
    
          {{-- Fields Section --}}
          <div class="card border mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Database Columns & Fields</h6>
                <button type="button" class="btn btn-sm btn-success" id="add_field"><i class="bi bi-plus-lg me-1"></i> Add Field</button>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover table-striped mb-0" id="fields_table">
                  <thead class="table-light">
                    <tr>
                      <th style="width: 20%">Field Name</th>
                      <th style="width: 15%">DB Type</th>
                      <th style="width: 15%">Validations</th>
                      <th style="width: 15%">Html Type</th>
                      <th style="width: 5%" class="text-center">Null</th>
                      <th style="width: 25%">Options / Relation</th>
                      <th style="width: 5%"></th>
                    </tr>
                  </thead>
                  <tbody>
                    {{-- Field rows will be added here by JavaScript --}}
                  </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                <p class="form-text text-muted mb-0"><i class="bi bi-info-circle me-1"></i> The Primary key `id` and timestamps `created_at` and `updated_at` are created automatically.</p>
            </div>
          </div>
    
          <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-primary btn-lg px-5"><i class="bi bi-rocket-takeoff me-2"></i> Generate CRUD</button>
          </div>
        </form>
      </div>
    </div>
</div>

<!-- Icon Picker Modal -->
<div class="modal fade" id="iconModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select Icon</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" id="icon_search" placeholder="Search icons...">
        </div>
        <div class="row row-cols-4 row-cols-md-6 g-3" id="icon_grid">
            {{-- Icons will be populated here --}}
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
  $(document).ready(function() {
    let fieldCounter = 0;

    function addFieldRow() {
      fieldCounter++;
      const newRow = `
        <tr>
          <td><input type="text" name="fields[${fieldCounter}][name]" class="form-control" required></td>
          <td>
            <select name="fields[${fieldCounter}][db_type]" class="form-select db-type-select">
              <option value="string">string</option>
              <option value="integer">integer</option>
              <option value="text">text</option>
              <option value="boolean">boolean</option>
              <option value="date">date</option>
              <option value="datetime">datetime</option>
              <option value="float">float</option>
              <option value="double">double</option>
              <option value="json">json</option>
              <option value="foreignId">foreignId (Relation)</option>
            </select>
          </td>
          <td><input type="text" name="fields[${fieldCounter}][validation]" class="form-control" placeholder="required|max:255"></td>
          <td>
            <select name="fields[${fieldCounter}][html_type]" class="form-select">
              <option value="text">text</option>
              <option value="textarea">textarea</option>
              <option value="number">number</option>
              <option value="checkbox">checkbox</option>
              <option value="radio">radio</option>
              <option value="select">select</option>
              <option value="date">date</option>
              <option value="email">email</option>
              <option value="password">password</option>
              <option value="image">image</option>
            </select>
          </td>
          <td class="text-center">
            <input type="checkbox" name="fields[${fieldCounter}][nullable]" class="form-check-input" value="1">
          </td>
          <td>
             <input type="text" name="fields[${fieldCounter}][options]" class="form-control options-input" placeholder="val:Label or Model:col">
             <div class="relation-selection" style="display:none;">
                <select class="form-select form-select-sm mb-1 model-select">
                    <option value="">Select Model</option>
                </select>
                <select class="form-select form-select-sm column-select">
                    <option value="">Select Column</option>
                </select>
             </div>
          </td>
          <td><button type="button" class="btn btn-danger btn-sm remove-field">X</button></td>
        </tr>
      `;
      $('#fields_table tbody').append(newRow);
    }

    // Add initial field row
    addFieldRow();

    $('#add_field').click(function() {
      addFieldRow();
    });

    $('#fields_table').on('click', '.remove-field', function() {
      $(this).closest('tr').remove();
    });

    // Dynamic Relation Selection Logic
    $(document).on('change', '.db-type-select', function() {
        const row = $(this).closest('tr');
        const dbType = $(this).val();
        const optionsInput = row.find('.options-input');
        const relationDiv = row.find('.relation-selection');

        if (dbType === 'foreignId') {
            optionsInput.hide();
            relationDiv.show();
            loadModels(row);
        } else {
            optionsInput.show();
            relationDiv.hide();
        }
    });

    function loadModels(row) {
        const modelSelect = row.find('.model-select');
        if (modelSelect.children('option').length > 1) return; // Already loaded

        $.get("{{ route('crud-builder.get-models') }}", function(data) {
            modelSelect.empty().append('<option value="">Select Model</option>');
            data.forEach(model => {
                modelSelect.append(`<option value="${model}">${model}</option>`);
            });
        });
    }

    $(document).on('change', '.model-select', function() {
        const row = $(this).closest('tr');
        const model = $(this).val();
        const columnSelect = row.find('.column-select');
        const optionsInput = row.find('.options-input');

        if (model) {
            $.get("{{ route('crud-builder.get-model-columns') }}", { model: model }, function(data) {
                columnSelect.empty().append('<option value="">Select Column</option>');
                data.forEach(column => {
                    columnSelect.append(`<option value="${column}">${column}</option>`);
                });
            });
            updateOptionsValue(row);
        } else {
            columnSelect.empty().append('<option value="">Select Column</option>');
            optionsInput.val('');
        }
    });

    $(document).on('change', '.column-select', function() {
        updateOptionsValue($(this).closest('tr'));
    });

    function updateOptionsValue(row) {
        const model = row.find('.model-select').val();
        const column = row.find('.column-select').val();
        const optionsInput = row.find('.options-input');

        if (model && column) {
            optionsInput.val(`${model}:${column}`);
        }
    }

    // Icon Picker Logic
    const icons = [
        'fa-solid fa-user', 'fa-solid fa-users', 'fa-solid fa-cog', 'fa-solid fa-home', 'fa-solid fa-tachometer-alt',
        'fa-solid fa-shopping-cart', 'fa-solid fa-box', 'fa-solid fa-truck', 'fa-solid fa-file-alt', 'fa-solid fa-file',
        'fa-solid fa-folder', 'fa-solid fa-calendar', 'fa-solid fa-clock', 'fa-solid fa-bell', 'fa-solid fa-envelope',
        'fa-solid fa-comment', 'fa-solid fa-phone', 'fa-solid fa-map', 'fa-solid fa-map-marker-alt', 'fa-solid fa-lock',
        'fa-solid fa-unlock', 'fa-solid fa-key', 'fa-solid fa-shield-alt', 'fa-solid fa-credit-card', 'fa-solid fa-wallet',
        'fa-solid fa-chart-bar', 'fa-solid fa-chart-pie', 'fa-solid fa-chart-line', 'fa-solid fa-heartbeat', 'fa-solid fa-arrow-up',
        'fa-solid fa-list', 'fa-solid fa-th', 'fa-solid fa-check', 'fa-solid fa-times', 'fa-solid fa-plus',
        'fa-solid fa-minus', 'fa-solid fa-edit', 'fa-solid fa-trash', 'fa-solid fa-eye', 'fa-solid fa-eye-slash',
        'fa-solid fa-search', 'fa-solid fa-filter', 'fa-solid fa-sort-amount-up', 'fa-solid fa-sort-amount-down', 'fa-solid fa-download',
        'fa-solid fa-upload', 'fa-solid fa-cloud', 'fa-solid fa-cloud-upload-alt', 'fa-solid fa-cloud-download-alt', 'fa-solid fa-print',
        'fa-solid fa-share', 'fa-solid fa-link', 'fa-solid fa-external-link-alt', 'fa-solid fa-copy', 'fa-solid fa-clipboard',
        'fa-solid fa-star', 'fa-solid fa-heart', 'fa-solid fa-thumbs-up', 'fa-solid fa-thumbs-down', 'fa-solid fa-flag',
        'fa-solid fa-bookmark', 'fa-solid fa-tag', 'fa-solid fa-tags', 'fa-solid fa-archive', 'fa-solid fa-briefcase',
        'fa-solid fa-building', 'fa-solid fa-university', 'fa-solid fa-money-bill', 'fa-solid fa-industry', 'fa-solid fa-warehouse'
    ];

    const iconModal = new bootstrap.Modal(document.getElementById('iconModal'));
    const iconGrid = $('#icon_grid');

    function renderIcons(filter = '') {
        iconGrid.empty();
        icons.forEach(icon => {
            if (icon.includes(filter)) {
                const col = `
                    <div class="col text-center">
                        <button type="button" class="btn btn-outline-light text-dark border p-2 w-100 icon-btn" data-icon="${icon}">
                            <i class="${icon} fs-2"></i>
                            <div class="small mt-1 text-truncate" style="font-size: 10px;">${icon.replace('fa-solid fa-', '')}</div>
                        </button>
                    </div>
                `;
                iconGrid.append(col);
            }
        });
    }

    $('#open_icon_modal').click(function() {
        renderIcons();
        iconModal.show();
    });

    $('#icon_search').on('input', function() {
        renderIcons($(this).val().toLowerCase());
    });

    $(document).on('click', '.icon-btn', function() {
        const icon = $(this).data('icon');
        $('#menu_icon').val(icon);
        $('#icon_preview').attr('class', icon); // Update preview
        iconModal.hide();
    });

    // Auto-Table Name Generation
    $('#model_name').on('input', function() {
        const modelName = $(this).val();
        if (modelName) {
            let tableName = modelName.replace(/([a-z])([A-Z])/g, '$1_$2').toLowerCase();
            if (!tableName.endsWith('s')) {
                tableName += 's';
            }
            $('#table_name').val(tableName);
        } else {
            $('#table_name').val('');
        }
    });

    // Update icon preview on manual input
    $('#menu_icon').on('input', function() {
        $('#icon_preview').attr('class', $(this).val());
    });

    // Auto-format Field Name to snake_case
    $(document).on('input', 'input[name^="fields"][name$="[name]"]', function() {
        let value = $(this).val();
        // Replace spaces and special chars with underscores, keep alphanumeric
        let snakeCase = value.replace(/([a-z])([A-Z])/g, '$1_$2') // Handle camelCase
                             .replace(/\s+/g, '_')                 // Replace spaces with underscores
                             .replace(/[^a-zA-Z0-9_]/g, '')        // Remove non-alphanumeric chars (except underscore)
                             .toLowerCase();                       // Convert to lowercase
        
        // Only update if the value actually changed to avoid cursor jumping issues if possible,
        // though direct replacement is usually fine for this simple logic.
        if (value !== snakeCase) {
             $(this).val(snakeCase);
        }
    });
  });
</script>
@endsection
