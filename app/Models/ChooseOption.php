<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ChooseOption
 * 
 * @property int $id_user
 * @property int $id_option
 * 
 * @property User $user
 * @property Option $option
 *
 * @package App\Models
 */
class ChooseOption extends Model
{
	protected $table = 'choose_option';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_user' => 'int',
		'id_option' => 'int'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'id_user')
					->where('users.id', '=', 'choose_option.id_user');
	}

	public function option()
	{
		return $this->belongsTo(Option::class, 'id_option')
					->where('option.id', '=', 'choose_option.id_option');
	}
}
