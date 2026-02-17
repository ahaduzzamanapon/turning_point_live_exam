<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
Route::prefix('admin')->group(function () {
    Route::get('login', [AuthController::class, 'login'])->name('admin.login');
    Route::post('login', [AuthController::class, 'loginPost'])->name('admin.login.post');
    Route::get('logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::middleware('auth:admin')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        Route::post('menus/order', [\App\Http\Controllers\Admin\MenuController::class, 'updateOrder'])->name('admin.menus.order')->middleware('permission:menus.edit');
        Route::resource('menus', \App\Http\Controllers\Admin\MenuController::class)->names('admin.menus')->middleware('permission:menus.browse');
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->names('admin.users')->middleware('permission:users.browse');
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->names('admin.roles')->middleware('permission:roles.browse');
        Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class)->names('admin.permissions')->middleware('permission:permissions.browse');

        Route::get('crud-builder', [\App\Http\Controllers\Admin\CrudBuilderController::class, 'index'])->name('crud-builder.index');
        Route::post('crud-builder/generate', [\App\Http\Controllers\Admin\CrudBuilderController::class, 'generate'])->name('crud-builder.generate');
        Route::get('crud-builder/models', [\App\Http\Controllers\Admin\CrudBuilderController::class, 'getModels'])->name('crud-builder.get-models');
        Route::get('crud-builder/model-columns', [\App\Http\Controllers\Admin\CrudBuilderController::class, 'getModelColumns'])->name('crud-builder.get-model-columns');

        Route::get('theme', [\App\Http\Controllers\Admin\ThemeController::class, 'index'])->name('admin.theme.index')->middleware('permission:theme.browse');
        Route::post('theme', [\App\Http\Controllers\Admin\ThemeController::class, 'update'])->name('admin.theme.update')->middleware('permission:theme.edit');

        Route::post('theme/apply', [\App\Http\Controllers\Admin\ThemeController::class, 'applyPreset'])->name('admin.theme.apply')->middleware('permission:theme.edit');
        Route::post('theme/preset', [\App\Http\Controllers\Admin\ThemeController::class, 'storePreset'])->name('admin.theme.preset.store')->middleware('permission:theme.edit');
        Route::get('theme/preset/{id}/edit', [\App\Http\Controllers\Admin\ThemeController::class, 'editPreset'])->name('admin.theme.preset.edit')->middleware('permission:theme.edit');
        Route::put('theme/preset/{id}', [\App\Http\Controllers\Admin\ThemeController::class, 'updatePreset'])->name('admin.theme.preset.update')->middleware('permission:theme.edit');
        Route::delete('theme/preset/{id}', [\App\Http\Controllers\Admin\ThemeController::class, 'destroyPreset'])->name('admin.theme.preset.destroy')->middleware('permission:theme.edit');

        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index')->middleware('permission:settings.browse');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update')->middleware('permission:settings.edit');

        // Exam Management Routes
        Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class)->names('admin.subjects');
        Route::resource('topics', \App\Http\Controllers\Admin\TopicController::class)->names('admin.topics');
        // Question Import
        Route::post('questions/import', [\App\Http\Controllers\Admin\QuestionController::class, 'import'])->name('admin.questions.import');
        Route::resource('questions', \App\Http\Controllers\Admin\QuestionController::class)->names('admin.questions');

        // Exam Assignment
        Route::get('exams/{exam}/assign', [\App\Http\Controllers\Admin\ExamController::class, 'assign'])->name('admin.exams.assign');
        Route::post('exams/{exam}/assign', [\App\Http\Controllers\Admin\ExamController::class, 'storeAssignment'])->name('admin.exams.storeAssignment');

        // Static Question Paper Management
        Route::get('exams/{exam}/paper', [\App\Http\Controllers\Admin\ExamPaperController::class, 'index'])->name('admin.exams.paper.index');
        Route::post('exams/{exam}/paper/generate', [\App\Http\Controllers\Admin\ExamPaperController::class, 'generate'])->name('admin.exams.paper.generate');
        Route::post('exams/{exam}/paper/store', [\App\Http\Controllers\Admin\ExamPaperController::class, 'store'])->name('admin.exams.paper.store');
        Route::delete('exams/{exam}/paper/{question}', [\App\Http\Controllers\Admin\ExamPaperController::class, 'destroy'])->name('admin.exams.paper.destroy');
        Route::get('exams/paper/search', [\App\Http\Controllers\Admin\ExamPaperController::class, 'search'])->name('admin.exams.paper.search');

        Route::resource('exams', \App\Http\Controllers\Admin\ExamController::class)->names('admin.exams');

        // Result Management
        Route::get('results', [\App\Http\Controllers\Admin\ResultController::class, 'index'])->name('admin.results.index');
        Route::get('results/{id}', [\App\Http\Controllers\Admin\ResultController::class, 'show'])->name('admin.results.show');

        // Wallet Management
        Route::get('wallet', [\App\Http\Controllers\Admin\WalletController::class, 'index'])->name('admin.wallet.index');
        Route::post('wallet/add', [\App\Http\Controllers\Admin\WalletController::class, 'addMoney'])->name('admin.wallet.add');

        // Event Management
        Route::get('events/{event}/results', [\App\Http\Controllers\Admin\EventController::class, 'results'])->name('admin.events.results');
        Route::post('events/{event}/distribute-prizes', [\App\Http\Controllers\Admin\EventController::class, 'distributePrizes'])->name('admin.events.distribute');

        // Event Paper
        Route::get('events/{event}/paper', [\App\Http\Controllers\Admin\EventPaperController::class, 'index'])->name('admin.events.paper.index');
        Route::post('events/{event}/paper/store', [\App\Http\Controllers\Admin\EventPaperController::class, 'store'])->name('admin.events.paper.store');
        Route::post('events/{event}/paper/auto-generate', [\App\Http\Controllers\Admin\EventPaperController::class, 'autoGenerate'])->name('admin.events.paper.auto');
        Route::post('events/{event}/paper/reorder', [\App\Http\Controllers\Admin\EventPaperController::class, 'reorder'])->name('admin.events.paper.reorder');
        Route::delete('events/{event}/paper/{question}', [\App\Http\Controllers\Admin\EventPaperController::class, 'destroy'])->name('admin.events.paper.destroy');
        Route::get('events/paper/search', [\App\Http\Controllers\Admin\EventPaperController::class, 'search'])->name('admin.events.paper.search');

        Route::resource('events', \App\Http\Controllers\Admin\EventController::class)->names('admin.events');

        require base_path('routes/crud.php');
    });
});
