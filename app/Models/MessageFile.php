<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MessageFile
 * 
 * @property int $id
 * @property string|null $file
 * @property int $id_message
 * 
 * @property Message $message
 *
 * @package App\Models
 */
class MessageFile extends Model
{
	protected $table = 'message_file';
	public $timestamps = false;

	protected $casts = [
		'id_message' => 'int'
	];

	protected $fillable = [
		'file',
		'id_message'
	];

	public function message()
	{
		return $this->belongsTo(Message::class, 'id_message')
					->where('message.id', '=', 'message_file.id_message');
	}
}
