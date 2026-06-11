<?php
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\RecuController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/recu',[RecuController::class,'index'])->name('recus.index');
    Route::get('/recu/create',[RecuController::class,'create'])->name('recus.create');
    Route::post('/recu',[RecuController::class,'store'])->name('recus.store');
    Route::put('/recu/{recu}',[RecuController::class,'update'])->name('recus.update');
    Route::delete('/recu/{recu}',[RecuController::class,'destroy'])->name('recus.destroy');
    Route::get('/recu/{recu}',[RecuController::class,'show'])->name('recus.show');
    Route::get('/recu/{recu}/edit',[RecuController::class,'edit'])->name('recus.edit');
    Route::get('depenses', [DepenseController::class, 'index'])->name('depenses.index');
 


});

require __DIR__.'/auth.php';
