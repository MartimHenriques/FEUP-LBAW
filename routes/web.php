<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EventController;
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


// Authentications
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// edit profile
Route::get('profile/editProfile', 'UserController@showEditProfile');
Route::post('profile/editProfile', 'UserController@saveChanges') -> name('saveChanges');

// Profile
Route::get('profile', 'UserController@showProfile');

//home
Route::get('/', 'HomeController@show');

//admin
Route::get('deleteUser/{id}', [AdminController::class, 'deleteUser']) -> name('deleteUser');
Route::get('manageUsers', 'AdminController@showUsers');
Route::get('manageEvents', 'AdminController@showEvents');
Route::get('manageReports', 'AdminController@showReports');

//feed
Route::get('events', 'EventController@showEvents')->name('events');

//event
Route::get('events/{id}', 'EventController@showOneEvent')->name('event');

Route::get('eventsCreate', [EventController::class, 'showForm'])->name('eventsCreate');
Route::post('eventsCreate', [EventController::class, 'createEvent']);
Route::get('editEvent/{id}', [EventController::class, 'showEditEventForm'])->name('editEvent');
Route::post('editEvent/{id}', [EventController::class, 'editEvent']);

Route::get('removeFromEvent/{id_attendee}/{id_event}', [EventController::class, 'removeFromEvent']) -> name('removeFromEvent');

Route::get('joinEvent/{id}', [EventController::class, 'joinEvent']);
Route::get('abstainEvent/{id}', [EventController::class, 'abstainEvent']);
Route::get('editEvent/{id}', [EventController::class, 'showEditEventForm'])->name('editEvent');
Route::post('editEvent/{id}', [EventController::class, 'editEvent']);
Route::get('removeFromEvent/{id_attendee}/{id_event}', [EventController::class, 'removeFromEvent']) -> name('removeFromEvent');

Route::get('myevents', 'EventController@showMyEvents');
Route::get('calendar', 'EventController@showEventsAttend');

Route::post('/api/search', 'SearchController@show');

