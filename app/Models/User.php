<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * 
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string|null $picture
 * @property string|null $is_blocked
 * @property bool|null $is_admin
 * 
 * @property Collection|Poll[] $polls
 * @property Collection|Report[] $reports
 * @property Collection|Attendee[] $attendees
 * @property Collection|ChooseOption[] $choose_options
 * @property Collection|EventOrganizer[] $event_organizers
 * @property Collection|Invite[] $invites
 * @property Collection|Message[] $messages
 * @property Collection|Notification[] $notifications
 * @property Collection|Vote[] $votes
 *
 * @package App\Models
 */
class User extends Authenticatable
{
	use Notifiable;
	
	protected $table = 'users';
	public $timestamps = false;

	protected $casts = [
		'is_admin' => 'bool'
	];

	protected $hidden = [
		'password', 'remember_token',
	];

	protected $fillable = [
		'username',
		'password',
		'email',
		'picture',
		'is_blocked',
		'is_admin'
	];

	public function polls()
	{
		return $this->hasMany(Poll::class, 'id_user');
	}

	public function reports()
	{
		return $this->hasMany(Report::class, 'id_reporter');
	}

	public function attendees()
	{
		return $this->hasMany(Attendee::class, 'id_user');
	}

	public function choose_options()
	{
		return $this->hasMany(ChooseOption::class, 'id_user');
	}

	public function event_organizers()
	{
		return $this->hasMany(EventOrganizer::class, 'id_user');
	}

	public function invites()
	{
		return $this->hasMany(Invite::class, 'id_organizer');
	}

	public function messages()
	{
		return $this->hasMany(Message::class, 'id_user');
	}

	public function notifications()
	{
		return $this->hasMany(Notification::class, 'id_user');
	}

	public function votes()
	{
		return $this->hasMany(Vote::class, 'id_user');
	}
}
