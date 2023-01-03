<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MessageController;
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
Route::get('profile/delete', [UserController::class, 'deleteProfile']) -> name('deleteProfile');

//home
Route::get('/', 'HomeController@show');

//admin
Route::get('deleteUser/{id}', [AdminController::class, 'deleteUser']) -> name('deleteUser');
Route::get('deleteEvent/{id}', [AdminController::class, 'deleteEvent']) -> name('deleteEvent');
Route::get('manageUsers', 'AdminController@showUsers');
Route::get('manageEvents', 'AdminController@showEvents');
Route::get('manageReports', 'AdminController@showReports');

Route::get('blockUser/{id}', 'AdminController@blockUser');
Route::get('unblockUser/{id}', 'AdminController@unblockUser');

//feed
Route::get('events', 'EventController@showEvents');

//event
Route::get('events/{id}/info', 'EventController@showOneEventInfo');
Route::get('events/{id}/forum', 'EventController@showOneEventForum');

Route::get('eventsCreate', [EventController::class, 'showForm'])->name('eventsCreate');
Route::post('eventsCreate', [EventController::class, 'createEvent']);
Route::get('editEvent/{id}', [EventController::class, 'showEditEventForm'])->name('editEvent');
Route::post('editEvent/{id}', [EventController::class, 'editEvent']);

Route::get('joinEvent/{id}', [EventController::class, 'joinEvent']);
Route::get('abstainEvent/{id}', [EventController::class, 'abstainEvent']);
Route::get('editEvent/{id}', [EventController::class, 'showEditEventForm'])->name('editEvent');
Route::post('editEvent/{id}', [EventController::class, 'editEvent']);
Route::get('removeFromEvent/{id_attendee}/{id_event}', [EventController::class, 'removeFromEvent']) -> name('removeFromEvent');
Route::get('eventOrganizer/{id_user}/{id_event}', [Event_OrganizerController::class, 'makeAnOrganizer'])->name('makeAnOrganizer');

Route::post('/api/eventsSearch', [EventController::class,'searchEvents']);

//messages
Route::post('/api/event/comment/create', [MessageController::class,'createComment']);
Route::post('/api/event/reply/create', [MessageController::class,'createReply']);
Route::get('/api/event/comment/delete/{id}', [MessageController::class,'deleteComment']);
Route::post('/api/comment/vote/create', [MessageController::class,'vote']);
Route::post('/api/comment/vote/delete', [MessageController::class,'deleteVote']);
Route::post('/editMessage/{id}', [MessageController::class,'editMessage']);

//my events
Route::get('myevents', 'EventController@showMyEvents');
Route::get('calendar', 'EventController@showEventsAttend');

//contact us
Route::get('contactUs', 'StaticPagesController@showContactUs')->name('contactus');
Route::post('contactUs', [StaticPagesController::class, 'sendEmail']);
//static pages
Route::get('aboutUS', [StaticPagesController::class, 'showAbout']);
Route::get('userHelp', [StaticPagesController::class, 'showUserHelp']);

Route::get('forgot_password', 'ForgotPassword@show')->middleware('guest')->name('password.request');
Route::post('forgot_password', 'ForgotPassword@request')->middleware('guest')->name('password.email');
Route::get('recover_password', 'ForgotPassword@showRecover')->middleware('guest')->name('password.reset');;
Route::post('recover_password', 'ForgotPassword@recover')->middleware('guest')->name('password.update');;
