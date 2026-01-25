<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use App\Models\Menu;
use Illuminate\Support\Facades\Schema;

class CrudBuilderController extends Controller
{
    public function index()
    {
        return view('admin.crud-builder.index');
    }

    public function getModels()
    {
        $models = [];
        $modelPath = app_path('Models');
        $files = File::allFiles($modelPath);

        foreach ($files as $file) {
            $models[] = $file->getFilenameWithoutExtension();
        }

        return response()->json($models);
    }

    public function getModelColumns(Request $request)
    {
        $modelName = $request->input('model');
        $modelClass = 'App\\Models\\' . $modelName;

        if (class_exists($modelClass)) {
            $model = new $modelClass();
            $table = $model->getTable();
            $columns = Schema::getColumnListing($table);
            return response()->json($columns);
        }

        return response()->json([], 404);
    }

    public function generate(Request $request)
    {
        $modelName = Str::studly($request->input('model_name'));
        $tableName = $request->input('table_name', Str::snake(Str::plural($modelName)));
        $fields = $request->input('fields');
        $softDelete = $request->has('soft_delete');
        $datatables = $request->has('datatables');
        $migration = $request->has('migration');
        $migrate = $request->has('migrate');
        $paginate = $request->input('paginate');
        $searchable = $request->has('searchable'); // New Flag
        $exportable = $request->has('exportable'); // New Flag
        $testCases = $request->has('test_cases'); // New Flag

        // Prepare placeholders for stubs
        $modelVariable = Str::camel($modelName);
        $modelVariablePlural = Str::plural($modelVariable);
        $className = $modelName . 'Controller';
        $namespace = 'App\\Http\\Controllers\\Admin';
        $modelNamespace = 'App\\Models';

        $replacements = [
            '{{ modelName }}' => $modelName,
            '{{ tableName }}' => $tableName,
            '{{ modelVariable }}' => $modelVariable,
            '{{ modelVariablePlural }}' => $modelVariablePlural,
            '{{ className }}' => $className,
            '{{ namespace }}' => $namespace,
            '{{ modelNamespace }}' => $modelNamespace,
        ];

        // Generate Model
        $this->generateModel($modelName, $fields, $softDelete, $replacements);

        // Generate Migration
        if ($migration) {
            $this->generateMigration($modelName, $tableName, $fields, $softDelete, $replacements);
        }

        // Generate Controller
        $this->generateController($className, $modelName, $fields, $replacements, $searchable, $exportable);

        // Generate Views
        $this->generateViews($modelName, $fields, $replacements, $searchable, $exportable, $datatables);

        // Generate Test Cases
        if ($testCases) {
            $this->generateTestCases($modelName, $tableName, $fields, $replacements);
        }

        // Run Migrations
        if ($migrate) {
            Artisan::call('migrate');
        }

        // Add routes
        $this->addRoutes($modelName, $className);

        // Add to menu
        $this->addToMenu($modelName);

        // Generate Permissions
        $this->generatePermissions($modelName);

        return redirect()->back()->with('success', 'CRUD generated successfully!');
    }

    // ... (addRoutes, addToMenu, generateModel, generateMigration methods remain unchanged) ...
    protected function addRoutes($modelName, $className)
    {
        $modelVariablePlural = Str::plural(Str::camel($modelName));
        $route = "Route::resource('{$modelVariablePlural}', App\\Http\\Controllers\\Admin\\{$className}::class)->names('admin.{$modelVariablePlural}');";
        $route .= "\nRoute::get('{$modelVariablePlural}/export/pdf', [App\\Http\\Controllers\\Admin\\{$className}::class, 'exportPdf'])->name('admin.{$modelVariablePlural}.export.pdf');";
        $route .= "\nRoute::get('{$modelVariablePlural}/export/excel', [App\\Http\\Controllers\\Admin\\{$className}::class, 'exportExcel'])->name('admin.{$modelVariablePlural}.export.excel');";

        $crudRoutesPath = base_path('routes/crud.php');
        
        if (!File::exists($crudRoutesPath)) {
            File::put($crudRoutesPath, "<?php\n\nuse Illuminate\Support\Facades\Route;\n\n");
        }

        File::append($crudRoutesPath, "\n" . $route);
    }

    protected function addToMenu($modelName)
    {
        $modelVariablePlural = Str::plural(Str::camel($modelName));
        $menuIcon = request()->input('menu_icon', 'bi bi-circle'); // Default to Bootstrap Icon

        // Check if menu already exists
        $exists = Menu::where('route', 'admin.' . $modelVariablePlural . '.index')->exists();
        
        if (!$exists) {
            Menu::create([
                'title' => Str::plural($modelName),
                'route' => 'admin.' . $modelVariablePlural . '.index',
                'icon' => $menuIcon,
                'order' => Menu::max('order') + 1,
            ]);
        }
    }

    protected function generateModel($modelName, $fields, $softDelete, $replacements)
    {
        $modelStub = File::get(resource_path('stubs/model.stub'));
        $fillable = [];
        foreach ($fields as $field) {
            $fillable[] = "'" . $field['name'] . "'";
        }
        $replacements['{{ fillable }}'] = implode(', ', $fillable);

        // Soft Deletes
        if ($softDelete) {
            $replacements['{{ softDeleteImport }}'] = "use Illuminate\\Database\\Eloquent\\SoftDeletes;";
            $replacements['{{ softDeleteTrait }}'] = "use SoftDeletes;";
        } else {
            $replacements['{{ softDeleteImport }}'] = "";
            $replacements['{{ softDeleteTrait }}'] = "";
        }

        // Generate Relationships
        $relationships = [];
        foreach ($fields as $field) {
            if ($field['db_type'] === 'foreignId') {
                $relationName = Str::camel(str_replace('_id', '', $field['name']));
                $relatedModel = Str::studly($relationName);
                if (!empty($field['options'])) {
                    $parts = explode(':', $field['options']);
                    if (count($parts) >= 1) {
                        $relatedModel = $parts[0];
                    }
                }
                
                $relationships[] = "    public function {$relationName}()";
                $relationships[] = "    {";
                $relationships[] = "        return \$this->belongsTo(\\App\\Models\\{$relatedModel}::class, '{$field['name']}');";
                $relationships[] = "    }";
            }
        }
        $replacements['{{ relationships }}'] = implode("\n\n", $relationships);

        $modelContent = str_replace(array_keys($replacements), array_values($replacements), $modelStub);

        $modelPath = app_path('Models/' . $modelName . '.php');
        File::put($modelPath, $modelContent);
    }

    protected function generateMigration($modelName, $tableName, $fields, $softDelete, $replacements)
    {
        $migrationStub = File::get(resource_path('stubs/migration.stub'));
        $schemaFields = [];

        foreach ($fields as $field) {
            $dbType = $field['db_type'];
            $fieldName = $field['name'];
            
            if ($dbType === 'foreignId') {
                $schemaLine = '$table->foreignId(\'' . $fieldName . '\')';
            } else {
                $schemaLine = '$table->' . $dbType . "('" . $fieldName . "')";
            }

            if (isset($field['nullable']) && $field['nullable']) {
                $schemaLine .= '->nullable()';
            }

            $schemaLine .= ';';
            $schemaFields[] = '            ' . $schemaLine;
        }

        if ($softDelete) {
            $schemaFields[] = '            $table->softDeletes();';
        }

        $replacements['{{ fields }}'] = implode("\n", $schemaFields);

        $migrationContent = str_replace(array_keys($replacements), array_values($replacements), $migrationStub);

        $timestamp = date('Y_m_d_His');
        $migrationFileName = $timestamp . '_create_' . $tableName . '_table.php';
        $migrationPath = database_path('migrations/' . $migrationFileName);

        File::put($migrationPath, $migrationContent);
    }

    protected function generateController($className, $modelName, $fields, $replacements, $searchable = true, $exportable = true)
    {
        $controllerStub = File::get(resource_path('stubs/controller.stub'));

        // ... (Image handling logic remains same) ...
        $imageFields = [];
        foreach ($fields as $field) {
            if ($field['html_type'] === 'image') {
                $imageFields[] = $field['name'];
            }
        }

        $except = "['_token', '_method']"; 
        if (count($imageFields) > 0) {
            $except = "['_token', '_method', '" . implode("', '", $imageFields) . "']";
        }

        $storeImage = '';
        foreach ($imageFields as $field) {
            $storeImage .= "        if (\$request->hasFile('{$field}')) {\n";
            $storeImage .= "            \$data['{$field}'] = \$request->file('{$field}')->store('{$replacements['{{ modelVariablePlural }}']}', 'public');\n";
            $storeImage .= "        }\n";
        }

        $updateImage = '';
        foreach ($imageFields as $field) {
            $updateImage .= "        if (\$request->hasFile('{$field}')) {\n";
            $updateImage .= "            if (\$item->{$field}) {\n";
            $updateImage .= "                Storage::disk('public')->delete(\$item->{$field});\n";
            $updateImage .= "            }\n";
            $updateImage .= "            \$data['{$field}'] = \$request->file('{$field}')->store('{$replacements['{{ modelVariablePlural }}']}', 'public');\n";
            $updateImage .= "        }\n";
        }

        $deleteImage = '';
        foreach ($imageFields as $field) {
            $deleteImage .= "        if (\$item->{$field}) {\n";
            $deleteImage .= "            Storage::disk('public')->delete(\$item->{$field});\n";
            $deleteImage .= "        }\n";
        }

        $replacements['{{ except }}'] = $except;
        $replacements['{{ storeImage }}'] = $storeImage;
        $replacements['{{ updateImage }}'] = $updateImage;
        $replacements['{{ deleteImage }}'] = $deleteImage;

        // Validation Rules
        $rules = [];
        foreach ($fields as $field) {
            $fieldRules = [];
            if (isset($field['validation']) && !empty($field['validation'])) {
                $fieldRules[] = $field['validation'];
            }
            if (isset($field['nullable']) && $field['nullable']) {
                $fieldRules[] = 'nullable';
            } else {
                $fieldRules[] = 'required';
            }
            
            if (!empty($fieldRules)) {
                $rules[] = "            '{$field['name']}' => '" . implode('|', $fieldRules) . "',";
            }
        }
        $replacements['{{ rules }}'] = implode("\n", $rules);

        // Relations Data for View
        $relations = [];
        $compact = [];
        foreach ($fields as $field) {
            if ($field['db_type'] === 'foreignId') {
                $relationName = Str::camel(str_replace('_id', '', $field['name']));
                $relatedModel = Str::studly($relationName);
                if (!empty($field['options'])) {
                    $parts = explode(':', $field['options']);
                    if (count($parts) >= 1) {
                        $relatedModel = $parts[0];
                    }
                }
                $pluralRelation = Str::plural($relationName);
                $relations[] = "        \${$pluralRelation} = \\App\\Models\\{$relatedModel}::all();";
                $compact[] = "'{$pluralRelation}'";
            }
        }
        
        $replacements['{{ relations }}'] = implode("\n", $relations);
        if (!empty($compact)) {
             $replacements['{{ compact }}'] = ', compact(' . implode(', ', $compact) . ')';
        } else {
             $replacements['{{ compact }}'] = '';
        }

        // Search Logic
        $searchLogic = '';
        if ($searchable) {
            $searchLogic = "        \$query = {$modelName}::query();\n";
            $searchLogic .= "        if (\$request->has('search')) {\n";
            $searchLogic .= "            \$search = \$request->input('search');\n";
            $searchLogic .= "            \$query->where(function(\$q) use (\$search) {\n";
            $first = true;
            foreach ($fields as $field) {
                if (in_array($field['db_type'], ['string', 'text'])) {
                    if ($first) {
                        $searchLogic .= "                \$q->where('{$field['name']}', 'like', \"%\$search%\")\n";
                        $first = false;
                    } else {
                        $searchLogic .= "                  ->orWhere('{$field['name']}', 'like', \"%\$search%\")\n";
                    }
                }
            }
            if (!$first) {
                $searchLogic = rtrim($searchLogic) . ";\n";
            }
            $searchLogic .= "            });\n";
            $searchLogic .= "        }\n";
            $searchLogic .= "        \$items = \$query->paginate(10);";
        } else {
            $searchLogic = "        \$items = {$modelName}::paginate(10);";
        }
        $replacements['{{ searchLogic }}'] = $searchLogic;

        // Export Methods
        $exportMethods = '';
        if ($exportable) {
            $exportMethods .= "\n    public function exportPdf()\n";
            $exportMethods .= "    {\n";
            $exportMethods .= "        \$items = {$modelName}::all();\n";
            $exportMethods .= "        \$pdf = \\Barryvdh\\DomPDF\\Facade\\Pdf::loadView('admin.{$replacements['{{ modelVariablePlural }}']}.pdf', compact('items'));\n";
            $exportMethods .= "        return \$pdf->download('{$replacements['{{ modelVariablePlural }}']}.pdf');\n";
            $exportMethods .= "    }\n\n";
            
            $exportMethods .= "    public function exportExcel()\n";
            $exportMethods .= "    {\n";
            $exportMethods .= "        // Excel export logic using maatwebsite/excel\n";
            $exportMethods .= "        // For simplicity, we can use a simple CSV download or implement proper Excel export class later\n";
            $exportMethods .= "        return response()->streamDownload(function () {\n";
            $exportMethods .= "            \$items = {$modelName}::all();\n";
            $exportMethods .= "            \$handle = fopen('php://output', 'w');\n";
            $exportMethods .= "            // Add Header\n";
            // ... Logic to add header row ...
            $exportMethods .= "            fputcsv(\$handle, ['ID', " . implode(", ", array_map(function($f) { return "'".Str::title($f['name'])."'"; }, $fields)) . "]);\n";
            $exportMethods .= "            foreach (\$items as \$item) {\n";
            $exportMethods .= "                fputcsv(\$handle, [\$item->id, " . implode(", ", array_map(function($f) { return "\$item->{$f['name']}"; }, $fields)) . "]);\n";
            $exportMethods .= "            }\n";
            $exportMethods .= "            fclose(\$handle);\n";
            $exportMethods .= "        }, '{$replacements['{{ modelVariablePlural }}']}.csv');\n";
            $exportMethods .= "    }\n";
        }
        $replacements['{{ exportMethods }}'] = $exportMethods;


        $controllerContent = str_replace(array_keys($replacements), array_values($replacements), $controllerStub);

        $controllerPath = app_path('Http/Controllers/Admin/' . $className . '.php');
        File::put($controllerPath, $controllerContent);
    }

    protected function generateViews($modelName, $fields, $replacements, $searchable = true, $exportable = true, $datatables = false)
    {
        $modelVariablePlural = Str::plural(Str::camel($modelName));
        $viewDirectory = resource_path('views/admin/' . $modelVariablePlural);
        if (!File::isDirectory($viewDirectory)) {
            File::makeDirectory($viewDirectory, 0755, true);
        }

        // Generate index view
        $indexStub = File::get(resource_path('stubs/index.stub'));
        $tableHeaders = [];
        $tableColumns = [];
        // ... (Table headers/columns logic remains same) ...
        foreach ($fields as $field) {
            $headerName = Str::title(str_replace('_', ' ', $field['name']));
            if ($field['db_type'] === 'foreignId' && Str::endsWith($field['name'], '_id')) {
                $headerName = Str::title(Str::replaceLast('_id', '', $field['name']));
            }
            $tableHeaders[] = '                    <th>' . $headerName . '</th>';
            
            if ($field['html_type'] === 'image') {
                $tableColumns[] = '                    <td><img src="{{ asset(\'storage/\' . $item->' . $field['name'] . ') }}" width="50" /></td>';
            } elseif ($field['db_type'] === 'foreignId') {
                 $relationName = Str::camel(Str::replaceLast('_id', '', $field['name']));
                 $displayColumn = 'id'; // Default
                 if (isset($field['options']) && strpos($field['options'], ':') !== false) {
                     $parts = explode(':', $field['options']);
                     if (count($parts) >= 2) {
                         $displayColumn = $parts[1];
                     }
                 }
                 $tableColumns[] = '                    <td>{{ $item->' . $relationName . '->' . $displayColumn . ' ?? \'\' }}</td>';
            } else {
                $tableColumns[] = '                    <td>{{ $item->' . $field['name'] . ' }}</td>';
            }
        }
        $replacements['{{ tableHeaders }}'] = implode("\n", $tableHeaders);
        $replacements['{{ tableColumns }}'] = implode("\n", $tableColumns);

        // Inject Search Form
        $searchForm = '';
        if ($searchable) {
            $searchForm = '            <div class="d-flex gap-2">';
            $searchForm .= '                <input type="text" id="searchInput" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request(\'search\') }}">';
            $searchForm .= '            </div>';
        }
        $replacements['{{ searchForm }}'] = $searchForm;

        // Inject Export Buttons
        $exportButtons = '';
        if ($exportable) {
            $exportButtons = '            <div class="btn-group btn-group-sm ms-2">';
            $exportButtons .= '                <a href="{{ route(\'admin.' . $modelVariablePlural . '.export.pdf\') }}" class="btn btn-outline-danger"><i class="bi bi-file-pdf"></i> PDF</a>';
            $exportButtons .= '                <a href="{{ route(\'admin.' . $modelVariablePlural . '.export.excel\') }}" class="btn btn-outline-success"><i class="bi bi-file-excel"></i> Excel</a>';
            $exportButtons .= '            </div>';
        }
        $replacements['{{ exportButtons }}'] = $exportButtons;

        $replacements['{{ exportButtons }}'] = $exportButtons;

        // Inject DataTables Script
        $dataTablesScript = '';
        if ($datatables) {
            $dataTablesScript = "
@push('scripts')
<link href=\"https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css\" rel=\"stylesheet\">
<script src=\"https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js\"></script>
<script src=\"https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js\"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            \"paging\": true,
            \"lengthChange\": true,
            \"searching\": true,
            \"ordering\": true,
            \"info\": true,
            \"autoWidth\": false,
            \"responsive\": true,
            \"language\": {
                \"search\": \"_INPUT_\",
                \"searchPlaceholder\": \"Search records...\"
            }
        });
    });
</script>
@endpush
";
        }
        $replacements['{{ dataTablesScript }}'] = $dataTablesScript;

        $indexContent = str_replace(array_keys($replacements), array_values($replacements), $indexStub);
        File::put($viewDirectory . '/index.blade.php', $indexContent);

        // Generate Table Partial
        $tableStub = File::get(resource_path('stubs/table.stub'));
        $tableContent = str_replace(array_keys($replacements), array_values($replacements), $tableStub);
        File::put($viewDirectory . '/table.blade.php', $tableContent);

        // Generate PDF View
        if ($exportable) {
            $pdfStub = File::get(resource_path('stubs/pdf.stub'));
            // Reuse table headers/columns logic but stripped of HTML for PDF if needed, or keep simple
            // For PDF we might want simpler table
            $pdfHeaders = [];
            $pdfColumns = [];
            foreach ($fields as $field) {
                 $headerName = Str::title($field['name']);
                 $pdfHeaders[] = '                <th>' . $headerName . '</th>';
                 $pdfColumns[] = '                <td>{{ $item->' . $field['name'] . ' }}</td>';
            }
            
            $replacements['{{ tableHeaders }}'] = implode("\n", $pdfHeaders);
            $replacements['{{ tableColumns }}'] = implode("\n", $pdfColumns);
            $replacements['{{ appName }}'] = config('app.name', 'Laravel');
            $replacements['{{ date }}'] = date('Y-m-d H:i:s');

            $pdfContent = str_replace(array_keys($replacements), array_values($replacements), $pdfStub);
            File::put($viewDirectory . '/pdf.blade.php', $pdfContent);
        }

        // Generate create view (unchanged)
        // ... (create view generation logic) ...
        $createStub = File::get(resource_path('stubs/create.stub'));
        $formFields = [];
        $hasFile = false;
        $hasTextarea = false;
        foreach ($fields as $field) {
            $colClass = ($field['html_type'] === 'textarea' || $field['html_type'] === 'editor') ? 'col-12' : 'col-md-4';
            $formFields[] = '            <div class="' . $colClass . ' mb-3">';
            $formFields[] = '                <label for="' . $field['name'] . '" class="form-label">' . Str::title(str_replace('_', ' ', $field['name'])) . '</label>';
            
            if ($field['html_type'] === 'textarea') {
                $hasTextarea = true;
                $formFields[] = '                <textarea class="form-control editor @error(\'' . $field['name'] . '\') is-invalid @enderror" id="' . $field['name'] . '" name="' . $field['name'] . '" rows="3">{{ old(\'' . $field['name'] . '\') }}</textarea>';
            } else if ($field['html_type'] === 'image') {
                $formFields[] = '                <input type="file" class="form-control @error(\'' . $field['name'] . '\') is-invalid @enderror" id="' . $field['name'] . '" name="' . $field['name'] . '">';
                $hasFile = true;
            } else if ($field['db_type'] === 'foreignId') {
                $relationName = Str::camel(str_replace('_id', '', $field['name']));
                $pluralRelation = Str::plural($relationName);
                $displayCol = 'name';
                if (!empty($field['options'])) {
                    $parts = explode(':', $field['options']);
                    if (count($parts) >= 2) {
                        $displayCol = $parts[1];
                    }
                }
                $formFields[] = '                <select class="form-select @error(\'' . $field['name'] . '\') is-invalid @enderror" id="' . $field['name'] . '" name="' . $field['name'] . '">';
                $formFields[] = '                    <option value="">Select ' . Str::title($relationName) . '</option>';
                $formFields[] = '                    @foreach($' . $pluralRelation . ' as $item)';
                $formFields[] = '                        <option value="{{ $item->id }}" {{ old(\'' . $field['name'] . '\') == $item->id ? \'selected\' : \'\' }}>{{ $item->' . $displayCol . ' }}</option>';
                $formFields[] = '                    @endforeach';
                $formFields[] = '                </select>';
            } else if ($field['html_type'] === 'select') {
                $formFields[] = '                <select class="form-select @error(\'' . $field['name'] . '\') is-invalid @enderror" id="' . $field['name'] . '" name="' . $field['name'] . '">';
                $formFields[] = '                    <option value="">Select ' . Str::title($field['name']) . '</option>';
                if (!empty($field['options'])) {
                    $options = explode(',', $field['options']);
                    foreach ($options as $option) {
                        $parts = explode(':', $option);
                        $val = $parts[0];
                        $label = isset($parts[1]) ? $parts[1] : $val;
                        $formFields[] = '                    <option value="' . $val . '" {{ old(\'' . $field['name'] . '\') == \'' . $val . '\' ? \'selected\' : \'\' }}>' . $label . '</option>';
                    }
                }
                $formFields[] = '                </select>';
            } else if ($field['html_type'] === 'radio') {
                 if (!empty($field['options'])) {
                    $options = explode(',', $field['options']);
                    foreach ($options as $option) {
                        $parts = explode(':', $option);
                        $val = $parts[0];
                        $label = isset($parts[1]) ? $parts[1] : $val;
                        $formFields[] = '                <div class="form-check form-check-inline">';
                        $formFields[] = '                    <input class="form-check-input @error(\'' . $field['name'] . '\') is-invalid @enderror" type="radio" name="' . $field['name'] . '" id="' . $field['name'] . '_' . $val . '" value="' . $val . '" {{ old(\'' . $field['name'] . '\') == \'' . $val . '\' ? \'checked\' : \'\' }}>';
                        $formFields[] = '                    <label class="form-check-label" for="' . $field['name'] . '_' . $val . '">' . $label . '</label>';
                        $formFields[] = '                </div>';
                    }
                }
            } else if ($field['html_type'] === 'checkbox') {
                $formFields[] = '                <div class="form-check form-switch">';
                $formFields[] = '                    <input type="hidden" name="' . $field['name'] . '" value="0">';
                $formFields[] = '                    <input class="form-check-input @error(\'' . $field['name'] . '\') is-invalid @enderror" type="checkbox" id="' . $field['name'] . '" name="' . $field['name'] . '" value="1" {{ old(\'' . $field['name'] . '\') ? \'checked\' : \'\' }}>';
                $formFields[] = '                    <label class="form-check-label" for="' . $field['name'] . '">' . Str::title($field['name']) . '</label>';
                $formFields[] = '                </div>';
            } else {
                $formFields[] = '                <input type="' . $field['html_type'] . '" class="form-control @error(\'' . $field['name'] . '\') is-invalid @enderror" id="' . $field['name'] . '" name="' . $field['name'] . '" value="{{ old(\'' . $field['name'] . '\') }}">';
            }
            $formFields[] = '                @error(\'' . $field['name'] . '\')';
            $formFields[] = '                    <div class="invalid-feedback">{{ $message }}</div>';
            $formFields[] = '                @enderror';
            $formFields[] = '            </div>';
        }
        $replacements['{{ formFields }}'] = implode("\n", $formFields);
        if ($hasFile) {
            $replacements['{{ enctype }}'] = 'enctype="multipart/form-data"';
        } else {
            $replacements['{{ enctype }}'] = '';
        }
        
        $createContent = str_replace(array_keys($replacements), array_values($replacements), $createStub);
        File::put($viewDirectory . '/create.blade.php', $createContent);

        // Generate edit view (unchanged)
        // ... (edit view generation logic) ...
        $editStub = File::get(resource_path('stubs/edit.stub'));
        $formFields = [];
        $hasFile = false;
        $hasTextarea = false;
        foreach ($fields as $field) {
            $colClass = ($field['html_type'] === 'textarea' || $field['html_type'] === 'editor') ? 'col-12' : 'col-md-6';
            $formFields[] = '            <div class="' . $colClass . ' mb-3">';
            $formFields[] = '                <label for="' . $field['name'] . '" class="form-label">' . Str::title(str_replace('_', ' ', $field['name'])) . '</label>';
            if ($field['html_type'] === 'textarea') {
                $hasTextarea = true;
                $formFields[] = '                <textarea class="form-control editor @error(\'' . $field['name'] . '\') is-invalid @enderror" id="' . $field['name'] . '" name="' . $field['name'] . '" rows="3">{{ old(\'' . $field['name'] . '\', $item->' . $field['name'] . ') }}</textarea>';
            } else if ($field['html_type'] === 'image') {
                $formFields[] = '                <input type="file" class="form-control @error(\'' . $field['name'] . '\') is-invalid @enderror" id="' . $field['name'] . '" name="' . $field['name'] . '">';
                $formFields[] = '                <img src="{{ asset(\'storage/\' . $item->' . $field['name'] . ') }}" width="100" class="mt-2 rounded" />';
                $hasFile = true;
            } else if ($field['db_type'] === 'foreignId') {
                $relationName = Str::camel(str_replace('_id', '', $field['name']));
                $pluralRelation = Str::plural($relationName);
                $displayCol = 'name';
                if (!empty($field['options'])) {
                    $parts = explode(':', $field['options']);
                    if (count($parts) >= 2) {
                        $displayCol = $parts[1];
                    }
                }
                $formFields[] = '                <select class="form-select @error(\'' . $field['name'] . '\') is-invalid @enderror" id="' . $field['name'] . '" name="' . $field['name'] . '">';
                $formFields[] = '                    <option value="">Select ' . Str::title($relationName) . '</option>';
                $formFields[] = '                    @foreach($' . $pluralRelation . ' as $relItem)';
                $formFields[] = '                        <option value="{{ $relItem->id }}" {{ old(\'' . $field['name'] . '\', $item->' . $field['name'] . ') == $relItem->id ? \'selected\' : \'\' }}>{{ $relItem->' . $displayCol . ' }}</option>';
                $formFields[] = '                    @endforeach';
                $formFields[] = '                </select>';
            } else if ($field['html_type'] === 'select') {
                $formFields[] = '                <select class="form-select @error(\'' . $field['name'] . '\') is-invalid @enderror" id="' . $field['name'] . '" name="' . $field['name'] . '">';
                $formFields[] = '                    <option value="">Select ' . Str::title($field['name']) . '</option>';
                if (!empty($field['options'])) {
                    $options = explode(',', $field['options']);
                    foreach ($options as $option) {
                        $parts = explode(':', $option);
                        $val = $parts[0];
                        $label = isset($parts[1]) ? $parts[1] : $val;
                        $formFields[] = '                    <option value="' . $val . '" {{ old(\'' . $field['name'] . '\', $item->' . $field['name'] . ') == \'' . $val . '\' ? \'selected\' : \'\' }}>' . $label . '</option>';
                    }
                }
                $formFields[] = '                </select>';
            } else if ($field['html_type'] === 'radio') {
                 if (!empty($field['options'])) {
                    $options = explode(',', $field['options']);
                    foreach ($options as $option) {
                        $parts = explode(':', $option);
                        $val = $parts[0];
                        $label = isset($parts[1]) ? $parts[1] : $val;
                        $formFields[] = '                <div class="form-check form-check-inline">';
                        $formFields[] = '                    <input class="form-check-input @error(\'' . $field['name'] . '\') is-invalid @enderror" type="radio" name="' . $field['name'] . '" id="' . $field['name'] . '_' . $val . '" value="' . $val . '" {{ old(\'' . $field['name'] . '\', $item->' . $field['name'] . ') == \'' . $val . '\' ? \'checked\' : \'\' }}>';
                        $formFields[] = '                    <label class="form-check-label" for="' . $field['name'] . '_' . $val . '">' . $label . '</label>';
                        $formFields[] = '                </div>';
                    }
                }
            } else if ($field['html_type'] === 'checkbox') {
                $formFields[] = '                <div class="form-check form-switch">';
                $formFields[] = '                    <input type="hidden" name="' . $field['name'] . '" value="0">';
                $formFields[] = '                    <input class="form-check-input @error(\'' . $field['name'] . '\') is-invalid @enderror" type="checkbox" id="' . $field['name'] . '" name="' . $field['name'] . '" value="1" {{ old(\'' . $field['name'] . '\', $item->' . $field['name'] . ') ? \'checked\' : \'\' }}>';
                $formFields[] = '                    <label class="form-check-label" for="' . $field['name'] . '">' . Str::title($field['name']) . '</label>';
                $formFields[] = '                </div>';
            } else {
                $formFields[] = '                <input type="' . $field['html_type'] . '" class="form-control @error(\'' . $field['name'] . '\') is-invalid @enderror" id="' . $field['name'] . '" name="' . $field['name'] . '" value="{{ old(\'' . $field['name'] . '\', $item->' . $field['name'] . ') }}">';
            }
            $formFields[] = '                @error(\'' . $field['name'] . '\')';
            $formFields[] = '                    <div class="invalid-feedback">{{ $message }}</div>';
            $formFields[] = '                @enderror';
            $formFields[] = '            </div>';
        }
        $replacements['{{ formFields }}'] = implode("\n", $formFields);
        if ($hasFile) {
            $replacements['{{ enctype }}'] = 'enctype="multipart/form-data"';
        } else {
            $replacements['{{ enctype }}'] = '';
        }

        $editContent = str_replace(array_keys($replacements), array_values($replacements), $editStub);
        File::put($viewDirectory . '/edit.blade.php', $editContent);
    }
    protected function generateTestCases($modelName, $tableName, $fields, $replacements)
    {
        $testStub = File::get(resource_path('stubs/test.stub'));
        
        // Generate dummy data for factory/tests
        $factoryData = [];
        foreach ($fields as $field) {
            if ($field['name'] === 'id' || $field['name'] === 'created_at' || $field['name'] === 'updated_at') continue;
            
            $value = "''";
            if ($field['db_type'] === 'string') $value = "'Test " . Str::title($field['name']) . "'";
            if ($field['db_type'] === 'text') $value = "'Test Content'";
            if ($field['db_type'] === 'integer') $value = "1";
            if ($field['db_type'] === 'boolean') $value = "1";
            
            $factoryData[] = "'{$field['name']}' => $value";
        }
        $replacements['{{ factoryData }}'] = '[' . implode(', ', $factoryData) . ']';

        $testContent = str_replace(array_keys($replacements), array_values($replacements), $testStub);
        
        $testPath = base_path('tests/Feature/' . $modelName . 'Test.php');
        File::put($testPath, $testContent);
    }

    protected function generatePermissions($modelName)
    {
        $modelLower = Str::snake(Str::plural($modelName));
        $permissions = ['browse', 'add', 'edit', 'delete'];

        foreach ($permissions as $action) {
            \App\Models\Permission::updateOrCreate(
                ['name' => "{$modelLower}.{$action}"],
                ['name' => "{$modelLower}.{$action}"]
            );
        }
    }
}
