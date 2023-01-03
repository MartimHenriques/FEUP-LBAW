<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
/**
 * Class Message
 * 
 * @property int $id
 * @property string|null $content
 * @property Carbon $date
 * @property int $like_count
 * @property int $id_event
 * @property int $id_user
 * @property int|null $parent
 * 
 * @property Event $event
 * @property User $user
 * @property Message|null $message
 * @property Collection|Message[] $messages
 * @property Collection|MessageFile[] $message_files
 * @property Collection|Notification[] $notifications
 * @property Collection|Vote[] $votes
 *
 * @package App\Models
 */
class Message extends Model
{
	protected $table = 'message';
	public $timestamps = false;

	protected $casts = [
		'like_count' => 'int',
		'id_event' => 'int',
		'id_user' => 'int',
		'parent' => 'int'
	];

	protected $dates = [
		'date'
	];

	protected $fillable = [
		'content',
		'date',
		'like_count',
		'id_event',
		'id_user',
		'parent'
	];

	public function event()
	{
		return $this->belongsTo(Event::class, 'id_event')
					->where('event.id', '=', 'message.id_event');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User', 'id_user');
        }

	public function message()
	{
		return $this->belongsTo(Message::class, 'parent')
					->where('message.id', '=', 'message.parent');
	}

	public function messages()
	{
		return $this->hasMany(Message::class, 'parent');
	}

	public function message_files()
	{
		return $this->hasMany(MessageFile::class, 'id_message');
	}

	public function notifications()
	{
		return $this->hasMany(Notification::class, 'id_message');
	}

	public function votes()
	{
		return $this->hasMany(Vote::class, 'id_message');
	}
	public function voted(User $user){
		return ($this->hasMany('App\Models\Vote', 'id_message')->where('id_user', $user->id)->get()->isNotEmpty());
	}
	
}
