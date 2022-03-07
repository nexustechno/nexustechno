<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Auth;
use Illuminate\Support\Facades\Hash;
use Redirect;

class AccountController extends Controller
{
    public function index()
    {
    	return view('back/account');
    }
    public function addUser(Request $request)
    {
    	$data = $request->all();
    	$user = auth()->user();
    	$data['parentid'] = $user->id;
    	$data['password'] =  Hash::make($request->password); 
    	if (Hash::check($request->password,$data['password'])) { 
        	User::create($data);
        	return redirect()->route('account')->with('message','Data created successfully.'); 
        }else{
            return Redirect::back()->withErrors(['Your password do not match with current password', 'Password is not match !']);
        }	                  
    }
}
