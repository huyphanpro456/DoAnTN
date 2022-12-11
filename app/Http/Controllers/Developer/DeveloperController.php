<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Mail\confirmProfile;
use App\Mail\postSimilar;
use App\Models\ApplyList;
use App\Models\Experience;
use App\Models\KeywordKills;
use App\Models\Notify;
use App\Models\Profile;
use App\Models\Recruitment;
use App\Models\StatisticAplly;
use App\Models\User;
use App\Notifications\recruitmentNotify;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Money\Money;

class DeveloperController extends Controller
{
    public function userId(){
        return User::find(Auth::id());
    }

    public function index(){
        $user=$this->userId();

        $users_employer=User::where('account_type',3)->get();
        $date_now=Carbon::now()->toDateString();
        $posts=Recruitment::with('user')->where([['expire','>',$date_now],['status',1]])
            ->orderBy('created_at','DESC')
            ->paginate(6);

        return view('developer.index')->with(compact('posts','users_employer','user'));
    }

    public function showAccount(){
        $account_id=Auth::user()->id;
        $account=User::find($account_id);

        $user=$this->userId();

        return view('developer.account')->with(compact('account','user'));
    }

    public function account(Request $request){
        $data=$request->validate(
            [
                'name' => [''],
                'image' => [''],
            ],
            [
                'name.required'=> 'Vui lòng nhập họ tên',
                'file.file'=> 'Đây không phải là file',
                'file.lt'=> 'Dung lượng file phải ít 1 MB',
            ]);

        $account=$this->userId();

        $account->name=$data['name'];

        $get_image=$request->image;
        $path='img/account/';

        if ($get_image){
            if (file_exists($path.$account->image)){
                unlink($path.$account->image);
            }

            $get_name_image=$get_image->getClientOriginalName();

            $name_image=current(explode('.',$get_name_image));
            $new_image=$name_image.rand(0,99).'.'.$get_image->getClientOriginalExtension();
            $get_image->move($path,$new_image);

            $account->image=$new_image;
        }else{
            $account->image=$account->image;
        }

        $account->save();

        return redirect()->back()->with('success','Cập nhập thành công');
    }

}
