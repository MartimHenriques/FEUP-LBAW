<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Event;
use App\Models\User;

class EventOrganizer extends Model
{
  // Don't add create and update timestamps in database.
  public $timestamps  = false;

  protected $table = 'event_organizer';

  /**
   * The user that is event organizer of the event
   */
  public function user() {
    return $this->hasOne('App\Models\User');
  }
  
  /**
   * Event organizer manages a event
   */
  public function event() {
    return $this->hasOne('App\Models\Event');
  }
}
