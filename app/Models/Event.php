<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Event
 * 
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property bool $visibility
 * @property string|null $picture
 * @property string $local
 * @property Carbon $publish_date
 * @property Carbon $start_date
 * @property Carbon $final_date
 * @property tsvector|null $tsvectors
 * 
 * @property Collection|Poll[] $polls
 * @property Collection|Report[] $reports
 * @property Collection|Attendee[] $attendees
 * @property Collection|EventOrganizer[] $event_organizers
 * @property Collection|Tag[] $tags
 * @property Collection|Invite[] $invites
 * @property Collection|Message[] $messages
 *
 * @package App\Models
 */
class Event extends Model
{
	protected $table = 'event';
	public $timestamps = false;

	protected $casts = [
		'visibility' => 'bool',
		'tsvectors' => 'tsvector'
	];

	protected $dates = [
		'publish_date',
		'start_date',
		'final_date'
	];

	protected $fillable = [
		'title',
		'description',
		'visibility',
		'picture',
		'local',
		'publish_date',
		'start_date',
		'final_date',
		'tsvectors'
	];

	public function polls()
	{
		return $this->hasMany(Poll::class, 'id_event');
	}

	public function reports()
	{
		return $this->hasMany(Report::class, 'id_event');
	}

	public function attendees()
	{
		return $this->hasMany(Attendee::class, 'id_event');
	}

	public function event_organizers()
	{
		return $this->hasMany(EventOrganizer::class, 'id_event');
	}

	public function tags()
	{
		return $this->belongsToMany(Tag::class, 'event_tag', 'id_event', 'id_tag');
	}

	public function invites()
	{
		return $this->hasMany(Invite::class, 'id_event');
	}

	public function messages()
	{
		return $this->hasMany(Message::class, 'id_event');
	}
}
