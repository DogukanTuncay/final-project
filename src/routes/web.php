<?php

use Illuminate\Support\Facades\Route;
use App\Models\CourseChapter;
use Illuminate\Http\Request;
use App\Exceptions\UniqueConstraintException;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // ... existing code ...
    
    // Fill in the blank routes
    Route::resource('fill-in-the-blanks', 'Admin\FillInTheBlankController');
    Route::post('fill-in-the-blanks/{id}/toggle-status', 'Admin\FillInTheBlankController@toggleStatus')->name('fill-in-the-blanks.toggle-status');
    
    // ... existing code ...
});

