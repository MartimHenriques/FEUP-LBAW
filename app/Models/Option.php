<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Option
 * 
 * @property int $id
 * @property string $text
 * @property int $id_poll
 * 
 * @property Poll $poll
 * @property Collection|ChooseOption[] $choose_options
 *
 * @package App\Models
 */
class Option extends Model
{
	protected $table = 'option';
	public $timestamps = false;

	protected $casts = [
		'id_poll' => 'int'
	];

	protected $fillable = [
		'text',
		'id_poll'
	];

	public function poll()
	{
		return $this->belongsTo(Poll::class, 'id_poll')
					->where('poll.id', '=', 'option.id_poll');
	}

	public function choose_options()
	{
		return $this->hasMany(ChooseOption::class, 'id_option');
	}
}
