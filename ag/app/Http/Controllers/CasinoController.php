<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Casino;
use App\User;

class CasinoController extends Controller
{
    public function index()
    {
        $casino = Casino::get();
        return view('backpanel/casino', compact('casino'));
    }

    public function addCasino($id=0)
    {
        if($id > 0){
            $casino = Casino::find($id);
            return view('backpanel/addCasino',compact('casino'));
        }

        return view('backpanel/addCasino');
    }

    public function listCasino()
    {
        $casino = Casino::all();
        return view('backpanel/listCasino', compact('casino'));
    }

    public function insertCasino(Request $request)
    {
        if($request->has('id')){
            $casino = Casino::find($request->id);
            $casino->casino_title = $request->casino_title;
            $casino->casino_name = $request->casino_name;
            $casino->casino_link = $request->casino_link;
            if($request->has('casino_image')) {
                $imageName = time() . '.' . $request->casino_image->extension();
                $request->casino_image->move(public_path('asset/upload'), $imageName);
                $casino->casino_image = $imageName;
            }
            $casino->save();
            return redirect()->route('casinoAll') ->with('message', 'Data updated successfully.');
        }else{
            $imageName = time() . '.' . $request->casino_image->extension();
            $request->casino_image->move(public_path('asset/upload'), $imageName);
            $data = $request->all();
            $data['casino_image'] = $imageName;
            $data['status'] = 1;

            Casino::create($data);
            return redirect()->route('casinoAll') ->with('message', 'Data created successfully.');
        }
    }

    public function delete($id)
    {
        $casino = Casino::find($id);
        if(!empty($casino)){
            $casino->delete();
        }
        return redirect()->route('casinoAll') ->with('message', 'Casino deleted successfully.');
    }

    public function casinoDetail($id)
    {
        $casino = Casino::find($id);
        return view('backpanel.' . $casino->casino_name . 'back', compact('casino'));
    }

    public function chkstatusactive(Request $request)
    {

        $matchId = $request->fid;

        $chk = $request->chk;
        if ($chk != 1) {
            $status = 0;
        } else {
            $status = 1;
        }

        $upd = Casino::find($matchId);

        $upd->status = $status;

        $upd->update();

        return response()->json(array('result' => 'success', 'message' => 'Status change successfully'));

    }

    public function savecasinoMinLimit(Request $request)
    {

        $fid = $request->fid;

        $chk = $request->chk;

        if ($chk == '')

            $chk = 0;

        $settingData = Casino::find($fid);

        $settingData->min_casino = $chk;

        $upd = $settingData->update();

        if ($upd)

            echo 'Success';

        else

            echo 'Fail';

    }

    public function savecasinoMaxLimit(Request $request)
    {

        $fid = $request->fid;

        $chk = $request->chk;

        if ($chk == '')

            $chk = 0;

        $settingData = Casino::find($fid);

        $settingData->max_casino = $chk;

        $upd = $settingData->update();

        if ($upd)

            echo 'Success';

        else

            echo 'Fail';

    }


}
