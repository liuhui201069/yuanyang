<?php
/**
 * Created by PhpStorm.
 * User: huiliu
 * Date: 16/1/6
 * Time: 下午5:17
 */

class Menu extends CI_Controller {

    public function index()
    {
        $this->load->view('menu.html');
    }


    public function test(){
        echo 'test';
    }
}