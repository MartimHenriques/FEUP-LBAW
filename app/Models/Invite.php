<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Invite
 * 
 * @property int $id_event
 * @property int $id_invitee
 * @property int $id_organizer
 * @property bool|null $accepted
 * 
 * @property Event $event
 * @property User $user
 * @property Collection|Notification[] $notifications
 *
 * @package App\Models
 */
class Invite extends Model
{
	protected $table = 'invite';
	public $incrementing = false;
	public $timestamps = false;
	use HasCompositePrimaryKey;
	protected $primaryKey = ['id_invitee', 'id_event'];

	protected $casts = [
		'id_event' => 'int',
		'id_invitee' => 'int',
		'id_organizer' => 'int',
		'accepted' => 'bool',
		'to_attend' => 'bool'
	];

	protected $fillable = [
		'id_organizer',
		'accepted',
		'to_attend'
	];

	public function event()
	{
		return $this->belongsTo(Event::class, 'id_event')
					->where('event.id', '=', 'invite.id_event');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'id_organizer')
					->where('users.id', '=', 'invite.id_organizer');
	}

	public function notifications()
	{
		return $this->hasMany(Notification::class, 'id_event', 'id_invitee');
	}
}
