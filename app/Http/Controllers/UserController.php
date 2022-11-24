<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;


class UserController extends Controller
{
    /**
     * Shows the card for a given id.
     *
     * @param  int  $id
     * @return Response
     */
    public function showProfile()
    {
      // definir smp 
      $users= User::find(Auth::user()->id);
      return view('pages.profile', ['users' => $users]);
    }

    public function showEditProfile()
    {
      $users= User::find(Auth::user()->id);
      return view('pages.editProfile', ['users' => $users]);
    }

    public function savePicture(Request $request, User $users){
      $img = $request->picture; 
      if($img != null){
          $imageName = $users->username . '.' . $img->extension();
          $img -> move(public_path('avatars/'), $imageName);
          $users->picture = $imageName;
      }     
    }

    public function savePassword(Request $request){
      $request->validate([
        'new_password' => 'confirmed',
      ]);

      # Match old password
      if(!Hash::check($request->old_password, auth()->user()->password)){
          return back()->with("error", "Old Password Doesn't match!");
      }


      #Update the new Password
      User::whereId(auth()->user()->id)->update([
          'password' => Hash::make($request->new_password)
      ]);  
    }

    public function saveChanges(Request $request){

      $users = User::find(Auth::user()->id);

      if($request->has('picture') && is_null($request->input('new_password'))){
        $this->savePicture($request, $users);
      }

      if(is_null($request->input('picture')) && $request->has('new_password')){
        $this->savePassword($request);
      } 

      else {
        $this->savePicture($request, $users);
        $this->savePassword($request);
      }

      $users->save();

      return back()->with("status", "Profile changed successfully!");
    }
}
