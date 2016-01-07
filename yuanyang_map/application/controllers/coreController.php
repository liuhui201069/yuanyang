<?php
/**
 * Created by PhpStorm.
 * User: huiliu
 * Date: 16/1/7
 * Time: 上午9:59
 */

class coreController extends CI_Controller{

    var $logger;
    var $username;

    var $view_data;

    function __construct()
    {
        parent::__construct();

        $this->init_log4php();

        $this->output->enable_profiler(TRUE);

        $this->load->helper('array');
        $this->load->helper('post_helper');
    }

    function init_log4php(){
        //初始化日志log4php的logger
        if(empty($this->logger)){
            $config_file = APPPATH . 'config/log4php.properties';
            if ( defined('ENVIRONMENT') && file_exists( APPPATH . 'config/' . ENVIRONMENT . '/log4php.properties' ) ) {
                $config_file = APPPATH . 'config/' . ENVIRONMENT . '/log4php.properties';
            }
            Logger::configure($config_file);
            $this->logger = Logger::getRootLogger();
        }
    }
}