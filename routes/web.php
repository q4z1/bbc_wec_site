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
Route::get('/delete/game/{game}', [App\Http\Controllers\GameController::class, 'delete_game'])->name('delete.game');
Route::get('/delete/award/{award}', [App\Http\Controllers\AwardController::class, 'delete_award'])->name('delete.award');
Route::post('/upload/game', [App\Http\Controllers\GameController::class, 'upload'])->name('upload.game.ajax');
Route::post('/update/game/{game}', [App\Http\Controllers\GameController::class, 'update_game'])->name('update.game');
Route::get('/player/{player?}', [App\Http\Controllers\PlayerController::class, 'index'])->name('player');
Route::post('/player/tickets/{player?}', [App\Http\Controllers\PlayerController::class, 'tickets'])->name('player.tickets');
Route::get('/players/delete/{player?}', [App\Http\Controllers\PlayerController::class, 'delete'])->name('player.delete');
Route::get('/players', [App\Http\Controllers\PlayerController::class, 'all'])->name('player.all');
Route::post('/players', [App\Http\Controllers\PlayerController::class, 'all'])->name('player.all');
Route::get('/players/list', [App\Http\Controllers\PlayerController::class, 'playerlist'])->name('player.list');
Route::get('/user/theme', [App\Http\Controllers\UserController::class, 'set_theme'])->name('user.theme.set');

Route::get('/shoutbox', [App\Http\Controllers\ShoutBoxMessageController::class, 'index'])->name('shoutbox');

Route::get('/registration', [App\Http\Controllers\RegistrationController::class, 'index'])->name('registration');
Route::post('/registration/newdate', [App\Http\Controllers\RegistrationController::class, 'add'])->name('registration.newdate');
Route::put('/registration/add/{date?}', [App\Http\Controllers\RegistrationController::class, 'register'])->name('registration.register');
Route::post('/registration/{reg?}', [App\Http\Controllers\RegistrationController::class, 'update'])->name('registration.update');
Route::delete('/registration/{reg?}', [App\Http\Controllers\RegistrationController::class, 'delete'])->name('registration.delete');
Route::get('/results', [App\Http\Controllers\ResultController::class, 'index'])->name('results');
Route::post('/results', [App\Http\Controllers\ResultController::class, 'filter'])->name('results.filter');
Route::get('/results/game/{game}', [App\Http\Controllers\ResultController::class, 'game'])->name('results.game');
Route::post('/results/games/{player}', [App\Http\Controllers\ResultController::class, 'player_games'])->name('results.games.player');
Route::get('/results/game/edit/{game}', [App\Http\Controllers\ResultController::class, 'game_edit'])->name('results.game.edit');
Route::get('/results/halloffame', [App\Http\Controllers\ResultController::class, 'halloffame'])->name('results.halloffame');
Route::get('/results/ranking', [App\Http\Controllers\ResultController::class, 'ranking'])->name('results.ranking');
Route::post('/results/ranking', [App\Http\Controllers\ResultController::class, 'ranking']);
Route::post('/results/halloffame', [App\Http\Controllers\ResultController::class, 'halloffame_filter'])->name('results.halloffame.filter');

Route::get('/awards', [App\Http\Controllers\AwardController::class, 'index'])->name('award.view');
Route::post('/awards/upload', [App\Http\Controllers\AwardController::class, 'upload'])->name('award.upload');
Route::post('/awards/edit/{award}', [App\Http\Controllers\AwardController::class, 'edit'])->name('award.edit');
Route::get('/awards/delete/{award}', [App\Http\Controllers\AwardController::class, 'delete'])->name('award.delete');
Route::post('/awards/assign/{award}', [App\Http\Controllers\AwardController::class, 'assign'])->name('award.assign');
Route::get('/awards/assignments/{award}', [App\Http\Controllers\AwardController::class, 'assignments'])->name('award.assignments');
