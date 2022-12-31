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
            // if (file_exists($path.$account->image)){
            //     unlink($path.$account->image);
            // }

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

    public function post_info($slug){
        $user=$this->userId();

        $post=Recruitment::with('user')->where('slug_title',$slug)->first();
        $kills=explode(',',$post->kills);

        $posts_same=Recruitment::with('user')
            ->where(function ($query) use ($kills,$post){
            for ($i=0;$i< count($kills);$i++) {
                $query->orWhere('kills','like','%'.$kills[$i].'%')
                    ->where('id','<>',$post->id)
                    ->where([['expire','>',Carbon::now()->toDateString()],['status',1]]);
            }
        })->orderBy('created_at','DESC')->get();

        if (Auth::user() != null){
            $user_id=Auth::user()->id;

            $profiles=Profile::where('user_id',$user_id)->orderBy('id','DESC')->get();
            $apply=ApplyList::where('user_id',$user_id)->where('recruitment_id',$post->id)->where('status',0)->first();

            return view('developer.post_info')->with(compact('post','kills','posts_same','profiles','apply','user'));
        }else{
            return view('developer.post_info')->with(compact('post','kills','posts_same','user'));

        }
    }

    public function getMorePost(Request $request){
        $date_now=Carbon::now()->toDateString();

        if ($request->ajax()){
            $posts=Recruitment::with('user')->where([['expire','>',$date_now],['status',1]])
                ->orderBy('created_at','DESC')
                ->paginate(6);
            return view('developer.posts-more')->with(compact('posts'))->render();
        }
    }

    public function save_post(){
        $user=$this->userId();

        return view('developer.save_post')->with(compact('user'));
    }
}
