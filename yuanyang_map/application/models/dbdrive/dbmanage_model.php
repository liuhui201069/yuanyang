<?php

class DbManage_model extends CI_Model
{
    var $strTable;
    var $strPrimaryKey;
    var $yuntoo_db;

    var $DB_CONFIG = 'yuntoo';

    function __construct()
    {
        parent::__construct();
        /**
         * load pg database
         */
//        $this->yuntoo_db = $this->load->database('yuntoo', TRUE);
    }

    function initConnection()
    {
//        if (!empty($this->yuntoo_db)) {
//            return;
//        }
//        $this->yuntoo_db = $this->load->database($this->DB_CONFIG, TRUE);
        if ($this->DB_CONFIG == 'yuntoo') {
            $this->yuntoo_db = $this->load->database('yuntoo', TRUE);
        } elseif ($this->DB_CONFIG == 'shijitan') {
            $this->yuntoo_db = $this->load->database('shijitan', TRUE);
        } else {
            $this->yuntoo_db = $this->load->database('default', TRUE);
        }
    }

    /**
     * select single row , always by primary key
     * @param array $arrWhere
     * @param array $arrField
     * @return array
     */
    function selectOne($arrWhere = array(), $arrField = array(), $arrJoinParam = array())
    {
        $this->initConnection();

        if (empty($arrField)) {
            $strField = '*';
        } else {
            $strField = implode(",", $arrField);
        }
        $this->yuntoo_db->select($strField);
        $this->yuntoo_db->from($this->strTable);
        if (!empty($arrWhere)) {
            $this->yuntoo_db->where($arrWhere);
        }

        if (!empty($arrJoinParam)) {
            foreach ($arrJoinParam as $strJoinTable => $strJoinOn) {
                $this->yuntoo_db->join($strJoinTable, $strJoinOn, 'left');
            }
        }
        $ObjResult = $this->yuntoo_db->get();
        $arrResult = $ObjResult->result_array();

        if (sizeof($arrResult) > 1) {
            log_message('ERROR', 'The result must be single line.');
            throw new Exception('The result must be single line.');
        }

        if (sizeof($arrResult) == 0) {
            return array();
        }
        return $arrResult[0];
    }


    /**
     * select from single table
     * @param array $arrWhere
     * @param string $strOrderby
     * @param string $strSort
     * @param int $intLimit
     * @param int $intOffset
     * @param array $arrField
     * @param array $arrOr
     * @return array
     */
    function select($arrWhere = array(), $arrJoinParam = array(), $strOrderby = '', $strSort = 'ASC', $intLimit = 0, $intOffset = 0, $arrField = array(), $arrOr = array())
    {
        $this->initConnection();

        if (empty($arrField)) {
            $strField = '*';
        } else {
            $strField = implode(",", $arrField);
        }
        $this->yuntoo_db->select($strField);
        $this->yuntoo_db->from($this->strTable);
        if (!empty($arrWhere)) {
            $this->yuntoo_db->where($arrWhere);
        }

        if (!empty($arrJoinParam)) {
            foreach ($arrJoinParam as $strJoinTable => $strJoinOn) {
                $this->yuntoo_db->join($strJoinTable, $strJoinOn, 'left');
            }
        }

        if (empty($strOrderby)) {
            $this->yuntoo_db->order_by($this->strPrimaryKey, $strSort);
        } else {
            $this->yuntoo_db->order_by($strOrderby, $strSort);
        }
        if ($intLimit > 0) {
            $this->yuntoo_db->limit($intLimit, $intOffset);
        }
        if (!empty($arrOr)) {
            $this->yuntoo_db->or_where($arrOr);
        }
        $ObjResult = $this->yuntoo_db->get();
        $arrResult = array();
        foreach ($ObjResult->result_array() as $arrRow) {
            $arrResult[] = $arrRow;
        }
        return $arrResult;
    }

    /**
     * select between multi tables
     * @param array $arrWhere
     * @param string $strOrderby
     * @param string $strSort
     * @param int $intLimit
     * @param int $intOffset
     * @param array $arrJoinParam
     * @param array $arrField
     * @return array
     */
    function joinSelect($arrWhere = array(), $strOrderby = '', $strSort = 'ASC', $intLimit = 0, $intOffset = 0, $arrJoinParam = array(), $arrField = array())
    {
        $this->initConnection();

        if (empty($arrField)) {
            $strField = '*';
        } else {
            $strField = implode(",", $arrField);
        }
        $this->yuntoo_db->select($strField);
        $this->yuntoo_db->from($this->strTable);
        if (!empty($arrWhere)) {
            $this->yuntoo_db->where($arrWhere);
        }
        if (!empty($arrJoinParam)) {
            foreach ($arrJoinParam as $strJoinTable => $strJoinOn) {
                $this->yuntoo_db->join($strJoinTable, $strJoinOn, 'left');
            }
        }
        if (empty($strOrderby)) {
            $strOrderby = $this->strPrimaryKey;
        }
        $this->yuntoo_db->order_by($strOrderby, $strSort);
        if ($intLimit > 0) {
            $this->yuntoo_db->limit($intLimit, $intOffset);
        }
        $ObjResult = $this->yuntoo_db->get();
        $arrResult = array();
        foreach ($ObjResult->result_array() as $arrRow) {
            $arrResult[] = $arrRow;
        }
        return $arrResult;
    }


    /**
     * insert single line value
     * @param $arrData
     * @return mixed
     * @throws Exception
     */
    function insert($arrData)
    {
        $this->initConnection();

        if (empty($arrData)) {
            throw new Exception('the data parameter is empty, please check');
        }
        $this->yuntoo_db->insert($this->strTable, $arrData);
        return $this->yuntoo_db->insert_id();
    }

    /**
     * insert multi line values
     * @param $arrData
     * @throws Exception
     */
    function insert_batch($arrData)
    {
        $this->initConnection();

        if (empty($arrData)) {
            throw new Exception('the data parameter is empty, please check');
        }

        $data = array_values($arrData);
        $this->yuntoo_db->insert_batch($this->strTable, $data);
    }

    /**
     * Update data by where condition
     * @param $arrData
     * @param array $arrWhere
     * @throws Exception
     */
    function update($arrData, $arrWhere = array())
    {
        $this->initConnection();

        if (empty($arrData)) {
            throw new Exception('the data parameter is empty, please check');
        }
        if (empty($arrWhere)) {
            if (isset($arrData[$this->strPrimaryKey])) {
                $arrWhere = array($this->strPrimaryKey => $arrData[$this->strPrimaryKey]);
            } else {
                throw new Exception('the where parameter is empty, please check');
            }
        }
        $this->yuntoo_db->update($this->strTable, $arrData, $arrWhere);
    }

    /**
     * 批量更新
     */
    function update_batch($whereColumn, $arrData)
    {
        $this->initConnection();

        if (empty($arrData)) {
            throw new Exception('the data parameter is empty, please check');
        }

        $this->yuntoo_db->update_batch($this->strTable, $arrData, $whereColumn);
    }

    /**
     * delete by single value
     * @param $arrWhere
     * @throws Exception
     */
    function delete($arrWhere)
    {
        $this->initConnection();

        if (empty($arrWhere)) {
            throw new Exception('the where parameter is empty, please check');
        }
        $this->yuntoo_db->delete($this->strTable, $arrWhere);
    }


    /**
     * select count
     * @param array $arrWhere
     * @param array $arrJoinParam
     * @param array $arrOr
     * @return mixed
     */
    function selectCount($arrWhere = array(), $arrJoinParam = array(), $arrOr = array())
    {
        $this->initConnection();

        if (!empty($arrWhere)) {
            $this->yuntoo_db->where($arrWhere);
        }
        if (!empty($arrJoinParam)) {
            foreach ($arrJoinParam as $strJoinTable => $strJoinOn) {
                $this->yuntoo_db->join($strJoinTable, $strJoinOn, 'left');
            }
        }
        if (!empty($arrOr)) {
            $this->yuntoo_db->or_where($arrOr);
        }
        $this->yuntoo_db->from($this->strTable);

        return $this->yuntoo_db->count_all_results();
    }

    /**
     * select count with multi primary values
     * @param null $columname
     * @param array $arrValue
     * @return mixed
     */
    function selectInCount($columname = NULL, $arrValue = array(), $arrJoinParam = array(), $arrWhere = array())
    {
        $this->initConnection();

        if (!empty($columname) && !empty($arrValue)) {
            $this->yuntoo_db->where_in($columname, $arrValue);
        }

        $this->yuntoo_db->from($this->strTable);

        if (!empty($arrWhere)) {
            $this->yuntoo_db->where($arrWhere);
        }

        if (!empty($arrJoinParam)) {
            foreach ($arrJoinParam as $strJoinTable => $strJoinOn) {
                $this->yuntoo_db->join($strJoinTable, $strJoinOn, 'left');
            }
        }

        return $this->yuntoo_db->count_all_results();
    }


    /**
     * select with multi primary values
     * @param $columnname
     * @param $listvals
     * @param array $arrField
     * @param int $intLimit
     * @param int $intOffset
     * @return array
     * @throws Exception
     */
    function selectMultiIn($arrColumnVals, $arrJoinParam = array(), $arrWhere = array(), $arrField = array(), $intLimit = 0, $intOffset = 0, $strOrderby = '', $strSort = 'ASC')
    {
        $this->initConnection();

        if (empty($arrColumnVals)) {
            throw new Exception('the column is empty, please check');
        }

        if (empty($arrField)) {
            $strField = '*';
        } else {
            $strField = implode(",", $arrField);
        }

        $this->yuntoo_db->select($strField);
        $this->yuntoo_db->from($this->strTable);

        foreach($arrColumnVals as $columnname => $listvals){
            $this->yuntoo_db->where_in($columnname, $listvals);
        }

        if (!empty($arrJoinParam)) {
            foreach ($arrJoinParam as $strJoinTable => $strJoinOn) {
                $this->yuntoo_db->join($strJoinTable, $strJoinOn, 'left');
            }
        }

        if (empty($strOrderby)) {
            $this->yuntoo_db->order_by($this->strPrimaryKey, $strSort);
        } else {
            $this->yuntoo_db->order_by($strOrderby, $strSort);
        }

        if (!empty($arrWhere)) {
            $this->yuntoo_db->where($arrWhere);
        }

        if ($intLimit > 0) {
            $this->yuntoo_db->limit($intLimit, $intOffset);
        }
        $ObjResult = $this->yuntoo_db->get();
//        $ObjResult = $this->yuntoo_db->get($this->strTable,$intLimit,$offset);
        $arrResult = array();
        foreach ($ObjResult->result_array() as $arrRow) {
            $arrResult[] = $arrRow;
        }
        return $arrResult;
    }

    /**
     * select with multi primary values
     * @param $columnname
     * @param $listvals
     * @param array $arrField
     * @param int $intLimit
     * @param int $intOffset
     * @return array
     * @throws Exception
     */
    function selectIn($columnname, $listvals, $arrJoinParam = array(), $arrWhere = array(), $arrField = array(), $intLimit = 0, $intOffset = 0, $strOrderby = '', $strSort = 'ASC')
    {
        $this->initConnection();

        if (empty($columnname)) {
            throw new Exception('the column is empty, please check');
        }

        if (empty($listvals) or sizeof($listvals) <= 0) {
            return array();
        }

        if (empty($arrField)) {
            $strField = '*';
        } else {
            $strField = implode(",", $arrField);
        }

        $this->yuntoo_db->select($strField);
        $this->yuntoo_db->from($this->strTable);

        if (!empty($columnname) && !empty($listvals)) {
            $this->yuntoo_db->where_in($columnname, $listvals);
        }

        if (!empty($arrJoinParam)) {
            foreach ($arrJoinParam as $strJoinTable => $strJoinOn) {
                $this->yuntoo_db->join($strJoinTable, $strJoinOn, 'left');
            }
        }

        if (empty($strOrderby)) {
            $this->yuntoo_db->order_by($this->strPrimaryKey, $strSort);
        } else {
            $this->yuntoo_db->order_by($strOrderby, $strSort);
        }

        if (!empty($arrWhere)) {
            $this->yuntoo_db->where($arrWhere);
        }

        if ($intLimit > 0) {
            $this->yuntoo_db->limit($intLimit, $intOffset);
        }
        $ObjResult = $this->yuntoo_db->get();
//        $ObjResult = $this->yuntoo_db->get($this->strTable,$intLimit,$offset);
        $arrResult = array();
        foreach ($ObjResult->result_array() as $arrRow) {
            $arrResult[] = $arrRow;
        }
        return $arrResult;
    }

    /**
     * select like values
     * @param $columnname
     * @param $likevalue
     * @param $arrField
     * @param int $intLimit
     * @param int $intOffset
     * @return array
     * @throws Exception
     */
    function selectMultiLike($columnname, $likevalue, $columnname2, $likevalue2, $arrJoinParam = array(), $arrField = array(), $arrWhere = array(), $intLimit = 0, $intOffset = 0, $arrWhereIn = array())
    {
        $this->initConnection();

        if (empty($arrField)) {
            $strField = '*';
        } else {
            $strField = implode(",", $arrField);
        }

        $this->yuntoo_db->select($strField);
        $this->yuntoo_db->from($this->strTable);
//        if (!empty($likevalue) && !empty($columnname)) {
//            $this->yuntoo_db->like($columnname, $likevalue, 'both');
//        }
//
//        if (!empty($likevalue2) && !empty($columnname2)) {
//            $this->yuntoo_db->or_like($columnname2, $likevalue2, 'both');
//        }
        $arrWhere['('.$columnname.' like '.'\'%'.$likevalue.'%\''.' OR '.$columnname2.' like '.'\'%'.$likevalue2.'%\''.')'] = null;

        if (!empty($arrJoinParam)) {
            foreach ($arrJoinParam as $strJoinTable => $strJoinOn) {
                $this->yuntoo_db->join($strJoinTable, $strJoinOn, 'left');
            }
        }

        $this->yuntoo_db->order_by($this->strPrimaryKey, 'DESC');

        if (!empty($arrWhere)) {
            $this->yuntoo_db->where($arrWhere);
        }

        if (!empty($arrWhereIn)) {
            foreach ($arrWhereIn as $key => $value) {
                $this->yuntoo_db->where_in($key, $value);
            }
        }

        if ($intLimit > 0) {
            $this->yuntoo_db->limit($intLimit, $intOffset);
        }

        $ObjResult = $this->yuntoo_db->get();
        $arrResult = array();
        foreach ($ObjResult->result_array() as $arrRow) {
            $arrResult[] = $arrRow;
        }
        return $arrResult;
    }

    /**
     * select like values
     * @param $columnname
     * @param $likevalue
     * @param $arrField
     * @param int $intLimit
     * @param int $intOffset
     * @return array
     * @throws Exception
     */
    function selectLike($columnname, $likevalue, $arrJoinParam = array(), $arrField = array(), $arrWhere = array(), $intLimit = 0, $intOffset = 0, $arrWhereIn = array())
    {
        $this->initConnection();

        if (empty($arrField)) {
            $strField = '*';
        } else {
            $strField = implode(",", $arrField);
        }

        $this->yuntoo_db->select($strField);
        $this->yuntoo_db->from($this->strTable);
        if (!empty($likevalue) && !empty($columnname)) {
            $this->yuntoo_db->like($columnname, $likevalue, 'both');
        }

        if (!empty($arrJoinParam)) {
            foreach ($arrJoinParam as $strJoinTable => $strJoinOn) {
                $this->yuntoo_db->join($strJoinTable, $strJoinOn, 'left');
            }
        }

        $this->yuntoo_db->order_by($this->strPrimaryKey, 'DESC');

        if (!empty($arrWhere)) {
            $this->yuntoo_db->where($arrWhere);
        }

        if (!empty($arrWhereIn)) {
            foreach ($arrWhereIn as $key => $value) {
                $this->yuntoo_db->where_in($key, $value);
            }
        }

        if ($intLimit > 0) {
            $this->yuntoo_db->limit($intLimit, $intOffset);
        }

        $ObjResult = $this->yuntoo_db->get();
        $arrResult = array();
        foreach ($ObjResult->result_array() as $arrRow) {
            $arrResult[] = $arrRow;
        }
        return $arrResult;
    }


    /**
     * select like count
     * @param $columnname
     * @param $likevalue
     * @param $arrField
     * @param int $intLimit
     * @param int $intOffset
     * @return array
     * @throws Exception
     */
    function selectCountMultiLike($columnname, $likevalue, $columnname2, $likevalue2, $arrjoinParams = array(), $arrWhere = array(), $arrWhereIn = array())
    {
        $this->initConnection();

        $this->yuntoo_db->from($this->strTable);

        $arrWhere['('.$columnname.' like '.'\'%'.$likevalue.'%\''.' OR '.$columnname2.' like '.'\'%'.$likevalue2.'%\''.')'] = null;
//        if (!empty($likevalue) && !empty($columnname)) {
//            $this->yuntoo_db->like($columnname, $likevalue, 'both');
//        }
//
//        if (!empty($likevalue2) && !empty($columnname2)) {
//            $this->yuntoo_db->or_like($columnname2, $likevalue2, 'both');
//        }

        if (!empty($arrjoinParams)) {
            foreach ($arrjoinParams as $strJoinTable => $strJoinOn) {
                $this->yuntoo_db->join($strJoinTable, $strJoinOn, 'left');
            }
        }

        if (!empty($arrWhere)) {
            $this->yuntoo_db->where($arrWhere);
        }

        if (!empty($arrWhereIn)) {
            foreach ($arrWhereIn as $key => $value) {
                $this->yuntoo_db->where_in($key, $value);
            }
        }

        $num = $this->yuntoo_db->count_all_results();
        return $num;
    }

    /**
     * select like count
     * @param $columnname
     * @param $likevalue
     * @param $arrField
     * @param int $intLimit
     * @param int $intOffset
     * @return array
     * @throws Exception
     */
    function selectCountLike($columnname, $likevalue, $arrjoinParams = array(), $arrWhere = array(), $arrWhereIn = array())
    {
        $this->initConnection();

        $this->yuntoo_db->from($this->strTable);

        if (!empty($likevalue) && !empty($columnname)) {
            $this->yuntoo_db->like($columnname, $likevalue, 'both');
        }

        if (!empty($arrjoinParams)) {
            foreach ($arrjoinParams as $strJoinTable => $strJoinOn) {
                $this->yuntoo_db->join($strJoinTable, $strJoinOn, 'left');
            }
        }

        if (!empty($arrWhere)) {
            $this->yuntoo_db->where($arrWhere);
        }

        if (!empty($arrWhereIn)) {
            foreach ($arrWhereIn as $key => $value) {
                $this->yuntoo_db->where_in($key, $value);
            }
        }

        $num = $this->yuntoo_db->count_all_results();
        return $num;
    }

    /**
     * select with where or condition
     * @param array $arrWhere
     * @param array $arrOrderBy
     * @param int $intLimit
     * @param int $intOffset
     * @param array $arrField
     * @param array $arrOr
     * @return array
     */
    function selectOr($arrWhere = array(), $arrOrderBy = array(), $intLimit = 0, $intOffset = 0, $arrField = array(), $arrOr = array())
    {
        $this->initConnection();

        if (empty($arrField)) {
            $strField = '*';
        } else {
            $strField = implode(",", $arrField);
        }
        $this->yuntoo_db->select($strField);
        $this->yuntoo_db->from($this->strTable);
        if (!empty($arrWhere)) {
            $this->yuntoo_db->where($arrWhere);
        }
        if (!empty($arrOrderBy)) {
            foreach ($arrOrderBy as $strKey => $strVal) {
                $this->yuntoo_db->order_by($strKey, $strVal);
            }
        }
        if ($intLimit > 0) {
            $this->yuntoo_db->limit($intLimit, $intOffset);
        }
        if (!empty($arrOr)) {
            $strTempOr = array();
            foreach ($arrOr as $strKey => $strVal) {
                $strTempOr[] = $strKey . $strVal;
            }
//			$this->yuntoo_db->where('('.implode(' OR ',$strTempOr).')','');
            $this->yuntoo_db->or_where($arrOr);
        }
        $ObjResult = $this->yuntoo_db->get();
        $arrResult = array();
        foreach ($ObjResult->result_array() as $arrRow) {
            $arrResult[] = $arrRow;
        }
        return $arrResult;
    }


    /**
     * 获取某列最大值
     */
    function selectMax($column_name, $arrWhere = array())
    {
        $this->initConnection();

        $this->yuntoo_db->select_max($column_name);
        $this->yuntoo_db->from($this->strTable);
        if (!empty($arrWhere)) {
            $this->yuntoo_db->where($arrWhere);
        }
        $ObjResult = $this->yuntoo_db->get();
        $arrResult = $ObjResult->result_array();

        if (sizeof($arrResult) > 1) {
            log_message('ERROR', 'The result must be single line.');
            throw new Exception('The result must be single line.');
        }

        if (sizeof($arrResult) == 0) {
            return 0;
        }

        //TODO 这里应该返回的key指定？
        return (double)$arrResult[0][$column_name];
    }


    /**
     * 获取某列最小值
     */
    function selectMin($column_name, $arrWhere = array())
    {
        $this->initConnection();

        $this->yuntoo_db->select_min($column_name);
        $this->yuntoo_db->from($this->strTable);
        if (!empty($arrWhere)) {
            $this->yuntoo_db->where($arrWhere);
        }
        $ObjResult = $this->yuntoo_db->get();
        $arrResult = $ObjResult->result_array();

        if (sizeof($arrResult) > 1) {
            log_message('ERROR', 'The result must be single line.');
            throw new Exception('The result must be single line.');
        }

        if (sizeof($arrResult) == 0) {
            return 0;
        }

        //TODO 这里应该返回的key指定？
        return (double)$arrResult[0][$column_name];
    }
}