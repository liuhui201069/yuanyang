<?php
/**
 * Created by PhpStorm.
 * User: huiliu
 * Date: 16/1/6
 * Time: 下午5:07
 */
require_once ROOT_PATH . 'application/controllers/coreController.php';

class Point_Marking extends coreController
{

    var $post;

    var $fields = array(
        'longitude',
        'latitude',
        'price'
    );

    function __construct()
    {
        parent::__construct();
        $this->load->model("table/map_info_model", "map_info");

        $this->load->helper('jsonmsg_helper');

        $this->post = $this->input->post(NULL, TRUE); // returns all POST items with XSS filter
    }

    public function index()
    {
        $this->load->view('point_marking/index.html');
    }

    /**
     * 获取点信息
     */
    function getAllPoint()
    {
        $data = $this->map_info->getAllInfos();

        output_jsonmsg_succ($data);
    }

    /**
     * 添加点信息
     */
    function addPoint()
    {
        $jsonStr = $this->post['json'];
        $post_data = json_decode($jsonStr, true);

        $data = elements($this->fields, $post_data);

        $id = $this->map_info->addInfo($data);

        output_jsonmsg_succ($id);
    }


    /**
     * 修改点信息
     */
    function updatePoint($info_id)
    {
        $jsonStr = $this->post['json'];
        $post_data = json_decode($jsonStr, true);

        $data = elements($this->fields, $post_data);

        $this->map_info->updateInfo($info_id,$data);

        output_jsonmsg_succ($info_id);
    }


    /**
     * 修改点信息
     */
    function delPoint($info_id)
    {

        $this->map_info->delInfo($info_id);

        output_jsonmsg_succ($info_id);
    }



}