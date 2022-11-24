<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use hasCompositePrimaryKey;

/**
 * Class Attendee
 * 
 * @property int $id_user
 * @property int $id_event
 * 
 * @property User $user
 * @property Event $event
 *
 * @package App\Models
 */
class Attendee extends Model
{	
	public $timestamps = false;
	protected $table = 'attendee';
	public $incrementing = false;
	protected $primaryKey = ['id_user', 'id_event'];

	protected $fillable = [
		'id_user', 'id_event'
	 ];

	protected $casts = [
		'id_user' => 'int',
		'id_event' => 'int'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'id_user')
					->where('users.id', '=', 'attendee.id_user');
	}

	public function event()
	{
		return $this->belongsTo(Event::class, 'id_event')
					->where('event.id', '=', 'attendee.id_event');
	}
}
