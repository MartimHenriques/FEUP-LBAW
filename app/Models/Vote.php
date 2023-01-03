<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Vote
 * 
 * @property int $id_user
 * @property int $id_message
 * 
 * @property User $user
 * @property Message $message
 *
 * @package App\Models
 */
class Vote extends Model
{
	protected $table = 'vote';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_user' => 'int',
		'id_message' => 'int'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'id_user')
					->where('users.id', '=', 'vote.id_user');
	}

	public function message()
	{
		return $this->belongsTo(Message::class, 'id_message')
					->where('message.id', '=', 'vote.id_message');
	}
}
