<?php

namespace App\Http\Controllers;

use App\Website;
use Illuminate\Http\Request;
use App\User;
use App\setting;
use App\CreditReference;
use Hash;
use Redirect;
use Auth;
use Session;
use App\Match;
use App\Banner;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        return view('backpanel/index');
    }

    public function changepasspage()
    {
        return view('backpanel/change-password');
    }

    public function changePass($id)
    {
        $user = User::find($id);
        if ($user->agent_level != 'COM') {
            $userdata = CreditReference::where('player_id', $id)->first();
        } else {
            $userdata = setting::first();
        }
        return view('backpanel/changePass', compact('id', 'user', 'userdata'));
    }

    public function balanceoverview($id)
    {

        $loginuser = User::where('id', $id)->where('check_login', 1)->first();
        $user = User::where('id', $id)->first();
        $chkid = $id;
        return view('backpanel.balance-overview', compact('user', 'chkid', 'id'));
    }

    public function change_pass_first()
    {
        $getuser = Auth::user();
        $id = $getuser->id;
        $username = $getuser->user_name;
        return view('backpanel/changePassFirst', compact('id', 'username'));
    }

    public function updatePassword(Request $request, $id)
    {
        $userData = User::find($id);
        $newpass = $request->newpwd;
        $yourpwd = $request->yourpwd;
        $userpass = Auth::user()->password;

        if (Hash::check($yourpwd, $userpass)) {
            $userData->first_login = 1;
            $userData->password = Hash::make($newpass);
            $userData->update();
        } else {
            return Redirect::back()->withErrors(['Your password does not match with current password', 'Password is not match !']);
        }

        if ($userData->agent_level == 'SL'){
            Session::put('SLAminUser', $userData);
            $masterAgent = User::where("agent_level",'COM')->first();
            auth()->loginUsingId($masterAgent->id);
            $adminUser = Auth::User();
            Session::put('adminUser', $adminUser);
        }

        return redirect()->route('home')->with('message', 'Password Change Successfully');
    }

    public function updatePasswordadmin(Request $request, $id)
    {

        $adminpass = Auth::user();
        $userData = User::find($id);
        $newpass = $request->newpwd;
        $yourpwd = $request->yourpwd;

        $check_updpass = $userData->check_updpass;
        if (Hash::check($yourpwd, $adminpass->password)) {
            $userData->password = Hash::make($newpass);
            $userData->check_updpass = $check_updpass + 1;
            $userData->update();
        } else {
            return Redirect::back()->with('error', 'Your password do not match with current password!');
        }
        return redirect()->route('home')->with('message', 'Password Change Successfully');
    }

    public function storeReference(Request $request)
    {
        $userPass = Auth::user()->password;
        $routename = $request->route_name;
        $credit = CreditReference::where('player_id', $request->player_id)->first();
        $count = CreditReference::where('player_id', $request->player_id)->count();
        if (Hash::check($request['current_pass'], $userPass)) {
            if ($count != 0) {
                $balance = $credit->credit;
                $balance = $request->credit;
                $credit->credit = $balance;
                $credit->update();
            } else {
                $data = $request->all();
                CreditReference::create($data);
            }
        } else {
            return Redirect::back()->with('error', 'Incorrect password!');
        }
        return redirect()->route($routename)->with('message', 'Data created successfully.');
    }
}
