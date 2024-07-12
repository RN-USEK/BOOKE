<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Filament\Pages\ViewBook;
use App\Filament\Pages\CategoryBooks;
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


// Route::get('/', [BookController::class, 'index'])->name('home');
// Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// Route::get('/app/books/{book}', ViewBook::class)->name('filament.app.books.view');

Route::get('/', function () {
    return redirect('/app/login');
});

Route::get('/login', function () {
    return redirect('/app/login');
});
// // routes/web.php

Route::get('/filament/pages/dashboard', ViewBook::class)->name('filament.app.pages.dashboard');
Route::get('app/category/{category}', CategoryBooks::class)->name('category-books');
