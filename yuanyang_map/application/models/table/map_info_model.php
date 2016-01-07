<?php
/**
 * Created by PhpStorm.
 * User: huiliu
 * Date: 16/1/7
 * Time: 上午9:55
 */

class map_info_model extends DbManage_model
{
    var $DB_CONFIG = 'yuntoo';
    var $strTable = 'map_info';
    var $strPrimaryKey = 'map_info.id';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据展览ID获取展览信息
     */
    function getInfoById($id)
    {
        $arrWhere = array('map_info.id' => $id);
        return $this->select($arrWhere, array(), '', '', 0, 0, array());
    }

    /**
     * 获取全部资讯数量
     */
    function getAllInfosCount()
    {
        $arrWhere = array('del_flag' => 0);

        return $this->selectCount($arrWhere);
    }

    /**
     * 获取全部资讯信息
     */
    function getAllInfos($intLimit = 0, $intOffset = 0)
    {
        $arrWhere = array('del_flag' => 0);

        return $this->select($arrWhere, array(), '', '', $intLimit, $intOffset);
    }

    /**
     * 添加资讯信息
     */
    function addInfo($data)
    {

        if (empty($data)) {
            return -1;
        }

        $nowtime = date('Y-m-d H:i:s', time());
        $data ['update_time'] = $nowtime;
        $data ['create_time'] = $nowtime;

        $data['del_flag'] = 0; //默认为0，表示正常未删除状态

        return $this->insert($data);
    }

    /**
     * 修改资讯信息
     */
    function updateInfo($info_id, $data)
    {
        $data ['update_time'] = date('Y-m-d H:i:s', time());

        $this->update($data, array('id' => $info_id));
    }

    /**
     * 删除资讯
     */
    function delInfo($info_id)
    {
        $data = array();

        $data['del_flag'] = 1;

        $this->update($data, array('id' => $info_id));
    }

}