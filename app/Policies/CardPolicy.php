<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Event;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class EventPolicy
{
    use HandlesAuthorization;

    public function show(User $user, Event $event)
    {
      // Only a card owner can see it
      return $user->id == $event->user_id;
    }

    public function list(User $user)
    {
      // Any user can list its own cards
      return Auth::check();
    }

    public function create(User $user)
    {
      // Any user can create a new card
      return Auth::check();
    }

    public function delete(User $user, Card $card)
    {
      // Only a card owner can delete it
      return $user->id == $card->user_id;
    }
}
