<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Notification
 * 
 * @property int $id
 * @property string $content
 * @property Carbon $date
 * @property bool $read
 * @property int $id_user
 * @property USER-DEFINED|null $type
 * @property int|null $id_report
 * @property int|null $id_event
 * @property int|null $id_invitee
 * @property int|null $id_message
 * 
 * @property User $user
 * @property Report|null $report
 * @property Invite|null $invite
 * @property Message|null $message
 *
 * @package App\Models
 */
class Notification extends Model
{
	protected $table = 'notification';
	public $timestamps = false;

	protected $casts = [
		'read' => 'bool',
		'id_user' => 'int',
		'id_report' => 'int',
		'id_event' => 'int',
		'id_invitee' => 'int',
		'id_message' => 'int'
	];

	protected $dates = [
		'date'
	];

	protected $fillable = [
		'content',
		'date',
		'read',
		'id_user',
		'type',
		'id_report',
		'id_event',
		'id_invitee',
		'id_message'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'id_user')
					->where('users.id', '=', 'notification.id_user');
	}

	public function report()
	{
		return $this->belongsTo(Report::class, 'id_report')
					->where('report.id', '=', 'notification.id_report');
	}

	public function invite()
	{
		return $this->belongsTo(Invite::class, 'id_event', 'id_invitee')
					->where('invite.id_event', '=', 'notification.id_event')
					->where('invite.id_invitee', '=', 'notification.id_invitee');
					}

	public function message()
	{
		return $this->belongsTo(Message::class, 'id_message')
					->where('message.id', '=', 'notification.id_message');
	}
}
