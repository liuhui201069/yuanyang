<?php
/**
 * Created by PhpStorm.
 * User: huiliu
 * Date: 16/1/7
 * Time: 上午9:59
 */

class coreController extends CI_Controller{

    var $logger;

    function __construct()
    {
        parent::__construct();

        $this->init_log4php();

        $this->load->helper('array');
    }

    function init_log4php(){
//        //初始化日志log4php的logger
//        if(empty($this->logger)){
//            $config_file = APPPATH . 'config/log4php.properties';
//            Logger::configure($config_file);
//            $this->logger = Logger::getRootLogger();
//        }
    }
}