<?php
use App\User;
use App\UserHirarchy;
function childdata($id)
{
    $cat=User::where('parentid',$id)->get();
    $children = array();
    $i = 0;   
    foreach ($cat as $key => $cat_value) {
        $children[] = array();
        $children[] = $cat_value->id;
        $new=childdata($cat_value->id);
        $children = array_merge($children, $new);
        $i++;
    } 

    $new=array();
    foreach($children as $child)
    {
        if(!empty($child))
        $new[]=$child;
    }
    return $new;
}

$agent = User::whereNotIn('agent_level',['PL','SL'])->get();
foreach ($agent as  $agentData) {
    $dataResult = childdata($agentData->id);
    
    if($agentData->agent_level=='COM'){
        $direct_user=1;
    }else{
        $direct_user=0;
    }
    $subUser='';
    foreach ($dataResult as $value) {
        if($subUser==''){
            $subUser=$value;
        }else{
            $subUser=$value.','.$subUser;
        }
    }

    
        $data_hir['direct_user'] = $direct_user;
        $data_hir['agent_user'] = $agentData->id;
        $data_hir['sub_user'] = $subUser;
        UserHirarchy::create($data_hir);
    
}