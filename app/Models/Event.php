<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
  // Don't add create and update timestamps in database.
  public $timestamps  = false;

  protected $table = 'event';
  protected $fillable = [
    'title', 'description', 'visibility', 'picture', 'local', 'publish_date', 'start_date', 'final_date'
  ];

    /**
     * The cards this user owns.
     */
    public function messages() {
      return $this->hasMany('App\Models\Message');
    } 

}
