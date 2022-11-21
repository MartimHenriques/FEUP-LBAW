<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event_Organizer extends Model
{
    public $timestamps  = false;

    protected $table = 'event_organizer';

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'id_user', 'id_event'
    ];
}