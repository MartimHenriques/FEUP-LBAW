<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class EventTag
 * 
 * @property int $id_tag
 * @property int $id_event
 * 
 * @property Tag $tag
 * @property Event $event
 *
 * @package App\Models
 */
class EventTag extends Model
{
	protected $table = 'event_tag';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_tag' => 'int',
		'id_event' => 'int'
	];

	public function tag()
	{
		return $this->belongsTo(Tag::class, 'id_tag')
					->where('tag.id', '=', 'event_tag.id_tag');
	}

	public function event()
	{
		return $this->belongsTo(Event::class, 'id_event')
					->where('event.id', '=', 'event_tag.id_event');
	}
}
