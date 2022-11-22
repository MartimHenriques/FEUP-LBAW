<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Poll
 * 
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property Carbon $date
 * @property bool $is_open
 * @property int $id_event
 * @property int $id_user
 * 
 * @property Event $event
 * @property User $user
 * @property Collection|Option[] $options
 *
 * @package App\Models
 */
class Poll extends Model
{
	protected $table = 'poll';
	public $timestamps = false;

	protected $casts = [
		'is_open' => 'bool',
		'id_event' => 'int',
		'id_user' => 'int'
	];

	protected $dates = [
		'date'
	];

	protected $fillable = [
		'title',
		'description',
		'date',
		'is_open',
		'id_event',
		'id_user'
	];

	public function event()
	{
		return $this->belongsTo(Event::class, 'id_event')
					->where('event.id', '=', 'poll.id_event');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'id_user')
					->where('users.id', '=', 'poll.id_user');
	}

	public function options()
	{
		return $this->hasMany(Option::class, 'id_poll');
	}
}
