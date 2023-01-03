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

// Profile
Route::get('profile/{id}', 'UserController@showProfile');
// edit profile
Route::get('profile/{id}/edit', 'UserController@showEditProfile');
Route::post('profile/{id}/edit', 'UserController@saveChanges') -> name('saveChanges');
Route::get('profile/{id}/delete', [UserController::class, 'deleteProfile']) -> name('deleteProfile');

//home
Route::get('/', 'HomeController@show');

//admin
Route::get('delete/user/{id}', [AdminController::class, 'deleteUser']) -> name('deleteUser');
Route::get('delete/event/{id}', [AdminController::class, 'deleteEvent']) -> name('deleteEvent');
Route::get('manage/users', 'AdminController@showUsers');
Route::get('manage/events', 'AdminController@showEvents');
Route::get('manage/reports', 'AdminController@showReports');

Route::get('block/user/{id}', 'AdminController@blockUser');
Route::get('unblock/user/{id}', 'AdminController@unblockUser');

//feed
Route::get('events', 'EventController@showEvents');

//event
Route::get('events/{id}/info', 'EventController@showOneEventInfo')->name('event');
Route::get('events/{id}/forum', 'EventController@showOneEventForum');

Route::get('events_create', [EventController::class, 'showForm'])->name('events_create');
Route::post('events_create', [EventController::class, 'createEvent']);
Route::get('editEvent/{id}', [EventController::class, 'showEditEventForm'])->name('editEvent');
Route::post('editEvent/{id}', [EventController::class, 'editEvent']);

Route::get('join/event/{id}', [EventController::class, 'joinEvent']);
Route::get('abstain/event/{id}', [EventController::class, 'abstainEvent']);
Route::get('remove_from_event/{id_attendee}/{id_event}', [EventController::class, 'removeFromEvent']) -> name('removeFromEvent');
Route::get('event_organizer/{id_user}/{id_event}', [Event_OrganizerController::class, 'makeAnOrganizer'])->name('makeAnOrganizer');
Route::post('/create/report/{id}', [EventController::class, 'reportEvent']);

Route::post('/api/eventsSearch', [EventController::class,'searchEvents']);

//messages
Route::post('/api/event/comment/create', [MessageController::class,'createComment']);
Route::post('/api/event/reply/create', [MessageController::class,'createReply']);
Route::get('/api/event/comment/delete/{id}', [MessageController::class,'deleteComment']);
Route::post('/api/comment/vote/create', [MessageController::class,'vote']);
Route::post('/api/comment/vote/delete', [MessageController::class,'deleteVote']);
Route::post('/edit_comment', [MessageController::class,'editComment']);
Route::post('/edit_comment/cancel', [MessageController::class,'cancelEditComment']);

//invites
Route::get('/invites/{id}', [InvitesController::class,'showInvite']);
Route::post('/invites/{id}/deal', [InvitesController::class,'dealWithInvite']);
Route::get('/event/{id}/invite', [InvitesController::class,'showInviteForm']);
Route::post('/event/{id}/invite', [InvitesController::class,'create']);

//reports
Route::post('/report/{id}/deal', [ReportsController::class,'dealWithReport']);
Route::get('/report/{id}', [ReportsController::class,'showReportForm']);

//notifications
Route::get('/notifications', [NotificationsController::class, 'showNotifications']);
Route::post('/notifications/{id}/clear', [NotificationsController::class, 'clearAll']);

//my events
Route::get('my_events', 'EventController@showMyEvents');
Route::get('calendar', 'EventController@showEventsAttend');

//contact us
Route::get('contact_us', 'StaticPagesController@showContactUs')->name('contact_us');
Route::post('contact_us', [StaticPagesController::class, 'sendEmail']);
//static pages
Route::get('about_us', [StaticPagesController::class, 'showAbout']);
Route::get('user/help', [StaticPagesController::class, 'showUserHelp']);

// password reset
Route::get('forgot_password', 'ForgotPassword@show')->middleware('guest')->name('password.request');
Route::post('forgot_password', 'ForgotPassword@request')->middleware('guest')->name('password.email');
Route::get('recover_password', 'ForgotPassword@showRecover')->middleware('guest')->name('password.reset');;
Route::post('recover_password', 'ForgotPassword@recover')->middleware('guest')->name('password.update');;
