<?php
// +----------------------------------------------------------------------
// | easy pay [ pay to easy ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 All rights reserved.
// +----------------------------------------------------------------------
// | Author: fengxing <QQ:51125330>
// +----------------------------------------------------------------------
namespace Common\Logic;

class AlipayLogic extends BaseLogic {

    protected $moduleName = '支付宝';

    /**
     * 列表
     */
    public function index($request) {
        $map = array();
        $data = ' 1=1 ';
        //高级查询
        if ($request['appid']) {
            $request['appid'] = $request['appid'];
            $data.=' AND appid like "%' . $request['appid'] . '%" ';
        }
        if (is_numeric($request['status'])) {
            $map['status'] = $request['status'];
            $data.=' AND status = "' . $request['status'] . '" ';
        }
        $perpage = C('FX_PERPAGE'); //每页行数
        $admin = SM('Alipay');
        $count = $admin->selectCount(
                $data, 'id'
        ); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page = page($count, $request['p'], $perpage) . ',' . $perpage;

        $list = $admin->pageData(
                '*', $data, 'id DESC', $page
        );
        foreach ($list as $i => $iList) {
            $list[$i]['addtime'] = date('Y-m-d H:i:s', $list[$i]['addtime']);
        }
        $pageList = $this->pageList($count, $perpage, $map);

        $params = array(
            'list' => $list,
            'page' => $pageList,
            'pageName' => $this->moduleName . '管理'
        );
        return [1,
            $params];
    }

    /**
     * 添加
     */
    public function add($request) {
        $params = array(
            'act' => 'add',
            'pageName' => '添加' . $this->moduleName
        );
        return [1,
            $params];
    }

    /**
     * 修改
     */
    public function edit($request) {
        if (!$request['id']) {
            return [0,
                '参数错误！'];
        }
        $userModel = SM('Alipay');
        $row = $userModel->findData('*', 'id=' . $request['id']);
        $params = array(
            'edit' => $row,
            'act' => 'edit',
            'pageName' => '修改' . $this->moduleName
        );
        return [1,
            $params,
            'Alipay/add'];
    }

    /**
     * 保存
     */
    public function save($request) {
        $adminID = $request['id']; //获取数据标识
        $act = $request['act']; //获取模板标识
        //判断数据标识
        if (empty($adminID) && $act == 'edit') {
            return [0,
                '数据标识不能为空！'];
        }
        if (empty($act)) {
            return [0,
                '模板标识不能为空！'];
        }
        $admin = SM('Alipay');
        $data = array();
        $data['appid'] = $request['appid'];
        $data['private'] = $request['private'];
        $data['public'] = $request['public'];
        $data['status'] = $request['status'];

        if ($act == 'add') {
            $data['addtime'] = time();
            if ($admin->insertData($data) === false) {
                return [0,
                    '添加失败'];
            } else {
                //写入日志
                $this->adminLog($this->moduleName, '添加支付宝【' . $data['appid'] . '】');
                return [1,
                    '添加成功！',
                    __URL__];
            }
        } elseif ($act == 'edit') {
            $data['id'] = $adminID;
            $buffer = $admin->selectData(
                    'id,appid', 'id="' . $data['id'] . '"');
            if (!$buffer) {
                return [0,
                    '支付宝账户不存在'];
            }
            if ($admin->updateData(
                            $data, 'id=' . $data['id']) === false) {
                return [0,
                    '修改失败'];
            } else {
                //写入日志
                $this->adminLog($this->moduleName, '修改支付宝账户为【' . $adminID . '】的数据');
                return [1,
                    '修改成功！',
                    __URL__];
            }
        }
    }

    /**
     * 删除
     */
    public function delete($request) {
        $adminID = $request['id']; //获取数据标识
        $idArray = explode(',', $adminID);
        if (!$adminID) {
            return [0,
                '数据标识不能为空',
                __URL__];
        }
        if (SM('Alipay')->deleteData(
                        'id in (' . implode(',', $idArray) . ')') === false) {
            return [0,
                '删除失败'];
        } else {
            //写入日志
            $this->adminLog($this->moduleName, '删除ID为【' . implode(',', $idArray) . '】的数据');
            return [1,
                '删除成功',
                __URL__];
        }
    }

}
