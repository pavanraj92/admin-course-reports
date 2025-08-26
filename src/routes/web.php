<?php

use Illuminate\Support\Facades\Route;
use admin\course_reports\Controllers\ReportManagerController;

Route::name('admin.')->middleware(['web', 'admin.auth'])->group(function () {
    Route::get('reports', [ReportManagerController::class, 'index'])->name('reports.index');
});
