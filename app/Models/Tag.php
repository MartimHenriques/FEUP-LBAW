<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Tag
 * 
 * @property int $id
 * @property string $tag_name
 * 
 * @property Collection|Event[] $events
 *
 * @package App\Models
 */
class Tag extends Model
{
	protected $table = 'tag';
	public $timestamps = false;

	protected $fillable = [
		'tag_name'
	];

	public function events()
	{
		return $this->belongsToMany(Event::class, 'event_tag', 'id_tag', 'id_event');
	}
}
