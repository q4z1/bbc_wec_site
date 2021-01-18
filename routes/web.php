<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/upload/game', [App\Http\Controllers\GameController::class, 'upload_view'])->name('upload.game.view');
Route::post('/upload/game', [App\Http\Controllers\GameController::class, 'upload'])->name('upload.game.ajax');
Route::post('/update/game/{game}', [App\Http\Controllers\GameController::class, 'update_game'])->name('update.game');

Route::get('/user/theme', [App\Http\Controllers\UserController::class, 'set_theme'])->name('user.theme.set');

Route::get('/shoutbox', [App\Http\Controllers\ShoutBoxMessageController::class, 'index'])->name('shoutbox');

Route::get('/results', [App\Http\Controllers\ResultController::class, 'index'])->name('results');
Route::post('/results', [App\Http\Controllers\ResultController::class, 'filter'])->name('results.filter');
Route::get('/results/game/{game}', [App\Http\Controllers\ResultController::class, 'game'])->name('results.game');
Route::get('/results/game/edit/{game}', [App\Http\Controllers\ResultController::class, 'game_edit'])->name('results.game.edit');
Route::get('/results/halloffame', [App\Http\Controllers\ResultController::class, 'halloffame'])->name('results.halloffame');
Route::post('/results/halloffame', [App\Http\Controllers\ResultController::class, 'halloffame_filter'])->name('results.halloffame.filter');