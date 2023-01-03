<?php


namespace App\Policies;


use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
  use HandlesAuthorization;

  public function show(User $user, User $model)
  {
    // determice wether the user can view the model
    return $user -> id == $model -> id;
  }

  public function update(User $user, User $model)
  {
    return $user->id == $model -> id;
  }

  public function delete(User $user, User $model)
  {
    // Only the user or an admin can delete an account
    return $user->id == $model->id || $user->is_admin;
  }

  public function admin(User $user) {
    return $user->is_admin;
  }
}
