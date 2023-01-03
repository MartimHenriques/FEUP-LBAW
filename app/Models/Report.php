<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Report
 * 
 * @property int $id
 * @property int $id_event
 * @property int|null $id_manager
 * @property int $id_reporter
 * @property Carbon $date
 * @property string $motive
 * @property USER-DEFINED $state
 * 
 * @property Event $event
 * @property User $user
 * @property Collection|Notification[] $notifications
 *
 * @package App\Models
 */
class Report extends Model
{
	protected $table = 'report';
	public $timestamps = false;

	protected $casts = [
		'id_event' => 'int',
		'id_manager' => 'int',
		'id_reporter' => 'int',
	];

	protected $dates = [
		'date'
	];

	protected $fillable = [
		'id_event',
		'id_manager',
		'id_reporter',
		'date',
		'motive',
		'state'
	];

	public function event()
	{
		return $this->belongsTo(Event::class, 'id_event')
					->where('event.id', '=', 'report.id_event')
					->where('event.id', '=', 'report.id_event');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'id_reporter')
					->where('users.id', '=', 'report.id_reporter')
					->where('users.id', '=', 'report.id_reporter');
	}

	public function notifications()
	{
		return $this->hasMany(Notification::class, 'id_report');
	}
}
