<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\noticeWithAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function user(){
        return User::find(Auth::id());
    }

    public function index(){
        if (Auth::check()){
            $user=$this->user();

            return view('admin.index')->with(compact('user'));
        }

        return redirect()->route('show-login-admin');

    }

    public function showUserDeveloper(){
        $users_developer=User::where('account_type',2)->paginate(5);
        $user=$this->user();

        return view('admin.user-developer')->with(compact('users_developer','user'));
    }

    public function updateStatus(Request $request){
        $user=User::find($request->id);
        var_dump($user);
        $user->status=$request->value;
        $user->save();
        return response()->json(['success']);
    }

}
