<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\RdirektoratController;
use App\Http\Controllers\RprojectController;
use App\Http\Controllers\RrkapController;
use App\Http\Controllers\RcatalogController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\NonprojectController;
use App\Http\Controllers\KanbanProjectController;
use App\Http\Controllers\HprojectController;
use App\Http\Controllers\LaporanController;

Route::redirect('/', '/login');

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('login', [LoginController::class, 'login'])->middleware('guest');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profil / Ubah Password
    Route::post('/change-password', [PenggunaController::class, 'changePassword'])->name('change.password');
    // Admin Only Routes
    Route::middleware(\App\Http\Middleware\CheckLevelAdmin::class)->group(function () {
        Route::resource('pengguna', PenggunaController::class);
        Route::resource('rdirektorat', RdirektoratController::class);
        Route::resource('rproject', RprojectController::class);
        Route::resource('rrkap', RrkapController::class);
        Route::resource('rcatalog', RcatalogController::class);
        Route::get('rcatalog/{id}/projects', [RcatalogController::class, 'getProjects']);
        Route::resource('project', ProjectController::class);
        Route::resource('nonproject', NonprojectController::class);
        Route::get('/kanban-progress', [KanbanProjectController::class, 'index'])->name('kanban.index');
        Route::post('/kanban-progress/update-step', [KanbanProjectController::class, 'updateStep'])->name('kanban.update_step');
    });

    Route::get('/hproject/{project_id}', [HprojectController::class, 'index'])->name('hproject.index');
    Route::post('/hproject', [HprojectController::class, 'store'])->name('hproject.store');
    Route::put('/hproject/{id}', [HprojectController::class, 'update'])->name('hproject.update');
    Route::delete('/hproject/{id}', [HprojectController::class, 'destroy'])->name('hproject.destroy');
    Route::post('/hproject/parse-nde', [HprojectController::class, 'parseNde'])->name('hproject.parse_nde');
    
    // Evidence MinIO Routes
    Route::post('/hproject/{project_id}/evidence', [HprojectController::class, 'storeEvidence'])->name('hproject.evidence.store');
    Route::get('/hproject/{project_id}/evidence/{filename}/view', [HprojectController::class, 'viewEvidence'])->where('filename', '.*')->name('hproject.evidence.view');
    Route::get('/hproject/{project_id}/evidence/{filename}/download', [HprojectController::class, 'downloadEvidence'])->where('filename', '.*')->name('hproject.evidence.download');
    Route::delete('/hproject/{project_id}/evidence/{filename}', [HprojectController::class, 'destroyEvidence'])->where('filename', '.*')->name('hproject.evidence.destroy');

    Route::get('/laporan/progress', [LaporanController::class, 'progressIndex'])->name('laporan.progress');
    Route::get('/laporan/nonproject', [LaporanController::class, 'nonprojectIndex'])->name('laporan.nonproject');
});
