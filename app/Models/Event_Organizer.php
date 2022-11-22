<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
//use hasCompositePrimaryKey;

class Event_Organizer extends Model
{
    public $timestamps  = false;
    public $incrementing = false;
    protected $table = 'event_organizer';
    public $keyType = 'string';
    protected $primaryKey = ['id_user', 'id_event'];
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'id_user', 'id_event'
    ];
}