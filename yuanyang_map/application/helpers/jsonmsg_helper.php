<?php
/**
 * Created by PhpStorm.
 * User: liuhui
 * Date: 15-3-9
 * Time: 上午10:54
 */


function output_jsonmsg_succ($data = array(),$msg = array())
{
    foreach($data as $key => $value){
        if(is_integer($value)){
            $data[$key] = "$value";
        }
    }

    $message = array(
        'status' => TRUE,
        'data' => $data,
        'msg' => $msg
    );
    echo json_encode($message);
    exit;
}


function output_jsonmsg_fail($message,$data=array())
{
    $message =  array(
        'status' => FALSE,
        'data' => $data,
        'msg' => $message
    );
    echo json_encode($message);
    exit;
}