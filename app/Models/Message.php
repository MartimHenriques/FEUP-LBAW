<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
  // Don't add create and update timestamps in database.
  public $timestamps  = false;
  protected $table = 'message';
  protected $fillable = [
    'content', 'date', 'likeCount', 'idEvent', 'idUser', 'parent'
  ];
  /**
   * The event this message belongs to
   */
  public function event() {
    return $this->belongsTo('App\Models\Event');
  }

  public function user() {
    return $this->belongsTo('App\Models\User');
  }

  /**
   * Items inside this card
   */
  public function items() {
    return $this->hasMany('App\Models\Item');
  }
}
