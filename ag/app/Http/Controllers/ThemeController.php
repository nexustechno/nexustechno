<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Theme;
use App\Casino;
use App\User;

class ThemeController extends Controller
{
  public function index()
  {   
    $theme = Theme::get();  
    return view('backpanel/theme',compact('theme'));
  }
  public function addTheme()
  {
    return view('backpanel/addTheme');
  }
  public function listTheme()
  {
  	$theme = Casino::all();
    return view('backpanel/listTheme',compact('theme'));
  }
  public function insertTheme(Request $request)
  {   
    $data = $request->all(); 
    Theme::create($data);
      return redirect()->route('themeAll')
      ->with('message','Data created successfully.'); 	
  }
  public function casinoDetail($id)
  {
    $casino = Casino::find($id);
    return view('backpanel.'.$casino->casino_name.'back',compact('casino'));
  }

  public function chkstatusactiveTheme(Request $request)
  {    

      $matchId = $request->fid;

      $chk=$request->chk;
      if($chk!=1){
        $status=0;
      }else{
        $status=1;
      }

      $upd=Theme::find($matchId);

      $upd->status = $status;

      $upd->update();

      return response()->json(array('result'=> 'success','message'=> 'Status change successfully')); 

  }
  public function editTheme($id)
  {
     $theme = Theme::find($id);
     return view('backpanel/editTheme',compact('theme'));
  }
  public function updatetheme(Request $request,$id)
  {
      $theme = Theme::find($id);
      $theme->theme_name   = $request->theme_name ;
      $theme->main_theme   = $request->main_theme;
      $theme->header_theme = $request->header_theme;
      $theme->update();
      return redirect()->route('themeAll')->with('message','Theme update successfully');
  }
  public function delTheme($id){
      $theme = Theme::find($id);
      $theme->delete();
      return redirect()->route('themeAll')->with('message','Theme delete successfully');
    }
    
}
