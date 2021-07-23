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
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/pages', [App\Http\Controllers\PageController::class, 'get'])->name('pages');
Route::resource('page', 'App\Http\Controllers\PageController');

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
Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('user.view');
Route::get('/user/delete/{user}', [App\Http\Controllers\UserController::class, 'delete'])->name('user.delete');
Route::post('/user/update/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('user.update');

Route::get('/shoutbox', [App\Http\Controllers\ShoutBoxMessageController::class, 'index'])->name('shoutbox');
Route::post('/shoutbox', [App\Http\Controllers\ShoutBoxMessageController::class, 'filter'])->name('shoutbox.filter');
Route::get('/shoutbox/map/{sbpost}', [App\Http\Controllers\ShoutBoxMessageController::class, 'map_single'])->name('shoutbox.map');
Route::post('/shoutbox/new', [App\Http\Controllers\ShoutBoxMessageController::class, 'post'])->name('shoutbox.post');
Route::get('/shoutbox/delete/{shoutBoxMessage}', [App\Http\Controllers\ShoutBoxMessageController::class, 'delete'])->name('shoutbox.delete');
Route::post('/shoutbox/update/{shoutBoxMessage}', [App\Http\Controllers\ShoutBoxMessageController::class, 'update'])->name('shoutbox.update');

Route::get('/registration', [App\Http\Controllers\RegistrationController::class, 'index'])->name('registration');
Route::get('/registration/date/get/{date}', [App\Http\Controllers\GameDateController::class, 'get'])->name('registration.date');
Route::post('/registration/date/new', [App\Http\Controllers\GameDateController::class, 'add'])->name('registration.date.new');
Route::get('/registration/date/delete/{date?}', [App\Http\Controllers\GameDateController::class, 'delete'])->name('registration.date.delete');
Route::post('/registration/date/update/{date?}', [App\Http\Controllers\GameDateController::class, 'update'])->name('registration.date.update');
Route::post('/registration/register/{date?}', [App\Http\Controllers\RegistrationController::class, 'register'])->name('registration.register');
Route::post('/registration/update/{reg?}', [App\Http\Controllers\RegistrationController::class, 'update'])->name('registration.update');
Route::get('/registration/delete/{reg?}', [App\Http\Controllers\RegistrationController::class, 'delete'])->name('registration.delete');

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
