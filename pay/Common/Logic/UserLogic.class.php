<?php
// +----------------------------------------------------------------------
// | easy pay [ pay to easy ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 All rights reserved.
// +----------------------------------------------------------------------
// | Author: fengxing <QQ:51125330>
// +----------------------------------------------------------------------
namespace Common\Logic;

class UserLogic extends BaseLogic {

    protected $moduleName = '用户';

    /**
     * 列表
     */
    public function index($request) {
       
        $map = array();
        $data = ' 1=1 ';
        //高级查询
        if ($request['username']) {
            $request['username'] = $request['username'];
            $data.=' AND username like "%' . $request['username'] . '%" ';
        }
        if ($request['userid']) {
            $map['userid'] = $request['userid'];
            $data.=' AND userid ="' . $request['userid'] . '" ';
        }
        if ($request['email']) {
            $map['email'] = $request['email'];
            $data.=' AND email = "' . $request['email'] . '" ';
        }
        if ($request['phone']) {
            $map['phone'] = $request['phone'];
            $data.=' AND phone ="' . $request['phone'] . '" ';
        }
        if ($request['agent']) {
            $map['agent'] = $request['agent'];
            $data.=' AND agent ="' . $request['agent'] . '" ';
        }
        if (is_numeric($request['ifagent'])) {
            $map['ifagent'] = $request['ifagent'];
            $data.=' AND ifagent = "' . $request['ifagent'] . '" ';
        }
        if (is_numeric($request['status'])) {
            $map['status'] = $request['status'];
            $data.=' AND status = "' . $request['status'] . '" ';
        }
        $perpage = C('FX_PERPAGE'); //每页行数
        $user = SM('User');
        $count = $user->selectCount(
                $data, 'id'
        ); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page = page($count, $request['p'], $perpage) . ',' . $perpage;

        $list = $user->pageData(
                '*', $data, 'id DESC', $page
        );
        foreach ($list as $i => $iList) {
            $list[$i]['addtime'] = date('Y-m-d H:i:s', $list[$i]['addtime']);
            $list[$i]['ifagent'] = $list[$i]['ifagent']==1 ? '是' : '否';
            $list[$i]['agent'] = $list[$i]['agent']==0 ? '无' : $list[$i]['agent'];
            $list[$i] = string('formatMoneyByArray', $list[$i], array(
                'money','tx'));
        }
        $pageList = $this->pageList($count, $perpage, $map);

        // 按条件统计总余额
        $totalmoney = $user->sumData('money',$data);
        $totaltx = $user->sumData('tx',$data); 
        $params = array(
            'list' => $list,
            'page' => $pageList,
            'totalmoney' => $totalmoney,
            'totaltx' => $totaltx,
            'pageName' => $this->moduleName . '管理'
        );
        return [1,
            $params];
    }
    public function userpeizhi()
    {
        $jiekouzj=SM('Jiekouzj');
        $list = $jiekouzj->selectData(
            '*',
            '1=1',
            'zjid ASC'
           );
        $jiekouzjArr=array();
        foreach($list as $iList){
            $jiekouzjArr[$iList['jkid']]=$iList;
        }

        $jiekou=SM('Jiekou');
        $list = $jiekou->selectData(
            '*',
            '1=1',
            'jkid ASC'
           );
        $jiekouArr=array();
        foreach($list as $i=>$iList){
            $list[$i]['pzid']=$jiekouzjArr[$iList['jkid']]['pzid'];
            $list[$i]['fl']=$jiekouzjArr[$iList['jkid']]['fl'];
            $list[$i]['ifopen']=$jiekouzjArr[$iList['jkid']]['ifopen'];
            $list[$i]=string('formatMoneyByArray',$list[$i],array('fl'));
        }
        return $list;
    }
    /**
     * 添加
     */
    public function add($request) {
        // 商户配置处理
        $list=$this->userpeizhi();
        $params = array(
            'act' => 'add',
            'list' => $list,
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
        $userModel = SM('User');
        $row = $userModel->findData('*', 'id=' . $request['id']);

        // 商户配置
        $userpz=SM('Userpz');
        $list = $userpz->selectData(
            '*',
            'userid='.$request['id'],
            'id ASC'
           );

        if (empty($list)) {
            $list=$this->userpeizhi();

            // $jiekouzj=SM('Jiekouzj');
            // $list = $jiekouzj->selectData(
            //     '*',
            //     '1=1',
            //     'zjid ASC'
            //    );
            // $jiekouzjArr=array();
            // foreach($list as $iList){
            //     $jiekouzjArr[$iList['jkid']]=$iList;
            // }

            // $jiekou=SM('Jiekou');
            // $list = $jiekou->selectData(
            //     '*',
            //     '1=1',
            //     'jkid ASC'
            //    );
            // $jiekouArr=array();
            // foreach($list as $i=>$iList){
            //     $list[$i]['pzid']=$jiekouzjArr[$iList['jkid']]['pzid'];
            //     $list[$i]['fl']=$jiekouzjArr[$iList['jkid']]['fl'];
            //     $list[$i]['ifopen']=$jiekouzjArr[$iList['jkid']]['ifopen'];
            //     $list[$i]=string('formatMoneyByArray',$list[$i],array('fl'));
            // }
        }else{
            $jiekou=SM('Jiekou');
            foreach($list as $i=>$iList){
                $list[$i]['jkid']=$iList['jkid'];
                $list[$i]['jkname']=$jiekou->selectData('jkname', 'jkid=' . $iList['jkid'])[0]['jkname'];
                $list[$i]['jkstyle']=$jiekou->selectData('jkstyle', 'jkid=' . $iList['jkid'])[0]['jkstyle'];
                $list[$i]['pzid']=$iList['pzid'];
                $list[$i]['fl']=$iList['fl'];
                $list[$i]['ifopen']=$iList['ifopen'];
                $list[$i]=string('formatMoneyByArray',$list[$i],array('fl'));
            }
            
            $jiekoudata=$this->userpeizhi();
            //写入配置中间
            foreach ($jiekoudata as $key => $value) {
                $userpzs=SM('Userpz');
                $exists = $userpzs->selectData('*','userid='.$request['id'].' and jkid='.$value['jkid'],'id ASC');  
                if (!$exists) {
                    $list[]=array(
                        'jkname'=>$value['jkname'],
                        'jkid'=>$value['jkid'],
                        'fl'=>$value['fl'],
                        'ifopen'=>$value['ifopen']
                    );
                } 
                
            }
        }
        
       
       
        // 商户配置
        $params = array(
            'edit' => $row,
            'list' => $list,
            'act' => 'edit',
            'pageName' => '修改' . $this->moduleName
        );
        return [1,
            $params,
            'User/add'];
    }

    /**
     * 保存
     */
    public function save($request) {
       
        $id = $request['id']; //获取数据标识
        $act = $request['act']; //获取模板标识
        //判断数据标识
        if (empty($id) && $act == 'edit') {
            return [0,
                '数据标识不能为空！'];
        }
        if (empty($act)) {
            return [0,
                '模板标识不能为空！'];
        }
        $user = SM('User');
        $data = array();
        $data['qq'] = $request['qq'];
        $data['email'] = $request['email'];
        $data['phone'] = $request['phone'];
        $data['status'] = $request['status'];
        $data['ifagent'] = $request['ifagent'];
        $data['ifatm'] = $request['ifatm'];


        if (!empty($data['phone']) && !checkString('checkIfPhone', $data['phone']))
            return [0,
                '请输入正确的手机号！'];
        if (!empty($data['email']) && !checkString('checkIfEmail', $data['email']))
            return [0,
                '请输入正确的邮箱地址！'];
        if ($data['ifagent']==1 && !empty($request['agent']))
            return [0,
                '代理商户暂不能添加代理id！'];        
        //检查用户代理费率
        $checkagent = $user->selectData(
                    '*', 'id="' . $id . '"');   
        
        $flag=1;   
        $agentdata=array();          
        if (empty($request['agent'])){
            $flag=0;
        }else{
            $agentdata = $user->selectData(
                    '*', 'userid="' . $request['agent'] . '"'); 
        }

        //判断新密码
        $password = $request['password'];
        $password2 = $request['password2'];
        if ($password != "" || $password2 != "") {
            if ($password2 != $password) {
                return [0,
                    '两次输入的密码不一致！'];
            }
            //密码规范
            if (!checkString('checkUserPassWord', $password)) {
                return [0,
                    '密码长度大于8，数字，字母组合！'];
            }
            $data['password'] = md5($request['username'] . $request['password']);
        }
        // 处理商户费率配置
        $jkid=$request['jkid'];
        $userpzbuffer=array();
        foreach($jkid as $iJkid){
            $userpzbuffer[]=array(
                'jkid'=>$iJkid,
                'fl'=>$request['fl_'.$iJkid],
                'ifopen'=>$request['ifopen_'.$iJkid]
            );
        }


        if ($act == 'add') {
            if ($password == "") {
                return [0,
                    '请输入密码！'];
            }
            if ($request['txpassword'] == "") {
                return [0,
                    '请输入提现密码！'];
            }
            $data['username'] = $request['username'];
            $data['userid'] = $this->createUserID();
            $data['lastip'] = get_client_ip(0, true);
            $data['miyao'] = string('randString', 32);
            $data['savecode'] = string('randString', 6);
            $data['addtime'] = time();
            $data['txpassword'] = md5($data['username'] . $request['txpassword']);
            $data['agent'] = !empty($request['agent']) ? $request['agent'] : 0;

            //检查用户名称长度
            if (!checkString('isEngLength', 4, 20, $data['username'])) {
                return [0,
                    '用户名称必须为4-20位字母或数字！'];
            }
            //检查用户名称重复
            $buffer = $user->selectData(
                    'userid', 'username="' . $data['username'] . '"');
            if ($buffer) {
                return [0,
                    '用户名重复请更换'];
            }

            if (($id = $user->insertData($data)) === false) {
                return [0,
                    '添加失败'];
            } else {
                // 处理商户代理费率
                if($userpzbuffer){
                        $addAllBuffer=array();
                        //写入配置中间
                        foreach($userpzbuffer as $i=>$iZjbuffer){
                            // 判断是否大于代理费率
                                if ($flag==1) {
                                        $userpz=SM('Userpz');
                                        $dailiexists = $userpz->selectData('*','userid='.$agentdata[0]['id'].' and jkid='.$iZjbuffer['jkid'],'id ASC');
                                        if ($dailiexists) {
                                           if ($iZjbuffer['fl']>=$dailiexists[0]['fl']) {
                                               // 保存
                                                $addAllBuffer[]=array(
                                                    'userid'=>$id,
                                                    'jkid'=>$iZjbuffer['jkid'],
                                                    'fl'=>$iZjbuffer['fl'],
                                                    'ifopen'=>$iZjbuffer['ifopen']
                                                );
                                           }else{
                                                return [1,'费率设置失败[不能小于上级代理费率]，请去操作编辑商户费率',U('User/index')];
                                           }
                                        }else{
                                            // 保存
                                            $addAllBuffer[]=array(
                                                'userid'=>$id,
                                                'jkid'=>$iZjbuffer['jkid'],
                                                'fl'=>$iZjbuffer['fl'],
                                                'ifopen'=>$iZjbuffer['ifopen']
                                            );
                                        } 
                                }else{
                                    // 保存
                                    $addAllBuffer[]=array(
                                        'userid'=>$id,
                                        'jkid'=>$iZjbuffer['jkid'],
                                        'fl'=>$iZjbuffer['fl'],
                                        'ifopen'=>$iZjbuffer['ifopen']
                                    );
                                } 
                                
                        }
                        if (!empty($addAllBuffer)) {
                            SM('Userpz')->addAllData($addAllBuffer);  
                        }
                        
                    }
                //写入用户扣量
                SL('Kou')->addUser($data['userid']);

                //写入日志
                $this->adminLog($this->moduleName, '添加用户【' . $data['username'] . '】');
                return [1,
                    '添加成功！',
                    U('User/index')];
            }
        } elseif ($act == 'edit') {
            $data['id'] = $id;
            $buffer = $user->findData(
                    'userid,username,password', 'id="' . $data['id'] . '"');
            if (!$buffer) {
                return [0,
                    '用户不存在'];
            }

            if ($password != "" || $password2 != "") $data['password'] = md5($buffer['username'] . $request['password']);
            if ($request['txpassword'])
                $data['txpassword'] = md5($buffer['username'] . $request['txpassword']);
           
            $data['agent'] = !empty($request['agent']) ? $request['agent'] : 0;
            if ($user->updateData(
                            $data, 'id=' . $data['id']) === false) {
                return [0,
                    '修改失败'];
            } else {
                if($userpzbuffer){
                    //写入配置中间
                    $userpz=SM('Userpz');
                    $list = $userpz->selectData('*','userid='.$data['id'],'id ASC');
                    $addAllBuffer=array();
                    if (!empty($list)) {
                        foreach($userpzbuffer as $i=>$iZjbuffer){
                            $exists = $userpz->selectData('*','userid='.$data['id'].' and jkid='.$iZjbuffer['jkid'],'id ASC');  
                            if ($exists) {
                                // 判断是否大于代理费率
                                if ($flag==1) {
                                    $dailiexists = $userpz->selectData('*','userid='.$agentdata[0]['id'].' and jkid='.$iZjbuffer['jkid'],'id ASC');
                                    if ($dailiexists) {
                                       if ($iZjbuffer['fl']>=$dailiexists[0]['fl']) {
                                           // 更新
                                           $userpz->updateData(array(
                                                'fl'=>$iZjbuffer['fl'],
                                                'ifopen'=>$iZjbuffer['ifopen']
                                            ),'id='.$exists[0]['id']);
                                       }else{
                                            return [0,'修改失败 接口id为:'.$iZjbuffer['jkid'].' 的支付类型不能小于上级代理的费率['.$dailiexists[0]['fl'].']'];
                                       }
                                    }else{
                                        $userpz->updateData(array(
                                            'fl'=>$iZjbuffer['fl'],
                                            'ifopen'=>$iZjbuffer['ifopen']
                                        ),'id='.$exists[0]['id']);
                                    }
                                }else{
                                    $userpz->updateData(array(
                                        'fl'=>$iZjbuffer['fl'],
                                        'ifopen'=>$iZjbuffer['ifopen']
                                    ),'id='.$exists[0]['id']);
                                } 
                                
                            }else{
                                // 判断是否大于代理费率
                                if ($flag==1) {
                                    $dailiexists = $userpz->selectData('*','userid='.$agentdata[0]['id'].' and jkid='.$iZjbuffer['jkid'],'id ASC');
                                    if ($dailiexists) {
                                       if ($iZjbuffer['fl']>=$dailiexists[0]['fl']) {
                                           // 更新
                                            $addAllBuffer[]=array(
                                                'userid'=>$data['id'],
                                                'jkid'=>$iZjbuffer['jkid'],
                                                'fl'=>$iZjbuffer['fl'],
                                                'ifopen'=>$iZjbuffer['ifopen']
                                            );
                                       }else{
                                            return [0,'修改失败 接口id为:'.$iZjbuffer['jkid'].' 的支付类型不能小于上级代理的费率['.$dailiexists[0]['fl'].']'];
                                       }
                                    }else{
                                        $addAllBuffer[]=array(
                                            'userid'=>$data['id'],
                                            'jkid'=>$iZjbuffer['jkid'],
                                            'fl'=>$iZjbuffer['fl'],
                                            'ifopen'=>$iZjbuffer['ifopen']
                                        );
                                    }
                                }else{
                                    $addAllBuffer[]=array(
                                        'userid'=>$data['id'],
                                        'jkid'=>$iZjbuffer['jkid'],
                                        'fl'=>$iZjbuffer['fl'],
                                        'ifopen'=>$iZjbuffer['ifopen']
                                    );
                                } 
                               
                            } 
                        }

                        if (empty($addAllBuffer)) {
                           //写入日志
                            $this->adminLog($this->moduleName, '修改用户UserID为【' . $buffer['userid'] . '】的数据');
                            return [1,'修改成功！',__URL__]; 
                        }
                    }else{
                        foreach($userpzbuffer as $i=>$iZjbuffer){
                            // 判断是否大于代理费率
                            if ($flag==1) {
                                $dailiexists = $userpz->selectData('*','userid='.$agentdata[0]['id'].' and jkid='.$iZjbuffer['jkid'],'id ASC');
                                if ($dailiexists) {
                                   if ($iZjbuffer['fl']>=$dailiexists[0]['fl']) {
                                       // 更新
                                       $addAllBuffer[]=array(
                                            'userid'=>$data['id'],
                                            'jkid'=>$iZjbuffer['jkid'],
                                            'fl'=>$iZjbuffer['fl'],
                                            'ifopen'=>$iZjbuffer['ifopen']
                                        );
                                   }else{
                                        return [0,'修改失败 接口id为:'.$iZjbuffer['jkid'].' 的支付类型不能小于上级代理的费率['.$dailiexists[0]['fl'].']'];
                                   }
                                }else{
                                    $addAllBuffer[]=array(
                                        'userid'=>$data['id'],
                                        'jkid'=>$iZjbuffer['jkid'],
                                        'fl'=>$iZjbuffer['fl'],
                                        'ifopen'=>$iZjbuffer['ifopen']
                                    );
                                }
                            }else{
                                $addAllBuffer[]=array(
                                    'userid'=>$data['id'],
                                    'jkid'=>$iZjbuffer['jkid'],
                                    'fl'=>$iZjbuffer['fl'],
                                    'ifopen'=>$iZjbuffer['ifopen']
                                );
                            } 
                            
                        }
                        
                    }
                    if (!empty(addAllBuffer)) {
                        SM('Userpz')->addAllData($addAllBuffer);  
                    }  
                    
                }

                //写入日志
                $this->adminLog($this->moduleName, '修改用户UserID为【' . $buffer['userid'] . '】的数据');
                return [1,'修改成功！',__URL__]; 
            }
        }
    }

    /**
     * 删除
     */
    public function delete($request) {

        $id = $request['id']; //获取数据标识
        $idArray = explode(',', $id);
        if (in_array(1, $idArray)) {
            return [0,
                '删除失败，请不要删除编号为1的超级用户'];
        }
        if (!$id) {
            return [0,
                '数据标识不能为空',
                __URL__];
        }
        if (SM('User')->deleteData(
                        'id in (' . implode(',', $idArray) . ')') === false) {
            return [0,
                '删除失败'];
        } else {
            SM('Userpz')->deleteData('userid in (' . implode(',', $idArray) . ')');
            //写入日志
            $this->adminLog($this->moduleName, '删除用户UserID为【' . implode(',', $idArray) . '】的数据');
            return [1,
                '删除成功',
                __URL__];
        }
    }

    /**
     * 登录状态验证
     */
    public function checklogin() {
        $userID = $this->getCookieUserID();
        $oldCode = $this->getCookieCode();
        if (!$userID || !$oldCode) {
            return [0,
                '未登录'];
        }
        //获取用户信息
        $buffer = SM('User')->selectData(
                '*', "userid='" . $userID . "'"
        );

        if (!$buffer) {
            return [0,
                '账号有误。'];
        }
        if ($buffer[0]['Status'] != 0) {
            return [0,
                '账号已被禁止，请联系客服。'];
        }

        $time = C('FX_COOKIE_TIMEOUT');
        $code = md5($buffer[0]['userid'] . $buffer[0]['username'] . $buffer[0]['savecode'] . ceil(time() / $time));
        $code1 = md5($buffer[0]['userid'] . $buffer[0]['username'] . $buffer[0]['savecode'] . (ceil(time() / $time) - 1));
        if ($oldCode != $code && $oldCode != $code1) {
            $this->setCookieUserID(null, $time);
            $this->setCookieUserName(null, $time);
            $this->setCookieCode(null, $time);
            return [0,
                '账户已过期，请重新登录。'];
        }


        if ($oldCode == $code1) {
            $this->setCookieUserID($buffer[0]['userid'], $time);
            $this->setCookieUserName($buffer[0]['username'], $time);
            $this->setCookieCode($code, $time);
        }
        unset($buffer[0]['password']);
        return [1,
            $buffer[0]];
    }

    /**
     * 注册事件
     */
    public function reg($request) {
        if (IS_POST) {
            $agent = $request['agent'];
            if (empty($agent) || !is_numeric($agent))
                $agent = 0;
            $ifagent = $request['ifagent'];
            if (empty($ifagent) || !is_numeric($ifagent))
                $ifagent = 0;
            $userName = $request['username'];
            $password = $request['pass'];
            $password1 = $request['pass1'];
            $txmm = $request['txmm'];
            $txmm1 = $request['txmm1'];
            $qq = $request['qq'];
            $phone = $request['phone'];
            $email = $request['email'];
            $yzm = $request['yzm'];
            //验证码
            if (md5($yzm)!=  session('verify')) {
                return [0,
                    '验证码错误！'];
            }

            //检查管理员名称长度
            if (!checkString('isEngLength', 4, 20, $userName)) {
                return [0,
                    '用户名称必须为4-20位字母或数字！'];
            }
            //密码规范
            if (!checkString('checkUserPassWord', $password)) {
                return [0,
                    '密码长度大于8，数字，字母组合！'];
            }
            if ($password1 != $password) {
                return [0,
                    '两次输入的密码不一致！'];
            }
            if (!checkString('checkUserPassWord', $txmm)) {
                return [0,
                    '提现密码长度大于8，数字，字母组合！'];
            }
            if ($txmm != $txmm1) {
                return [0,
                    '两次输入的提现密码不一致！'];
            }
            if (!is_numeric($qq) || strlen($qq) < 5) {
                return [0,
                    '请输入正确的qq号！'];
            }
            if (empty($phone) || !checkString('checkIfPhone', $phone))
                return [0,
                    '请输入正确的手机号！'];
            if (empty($email) || !checkString('checkIfEmail', $email))
                return [0,
                    '请输入正确的邮箱地址！'];


            $user = SM('User');
            //检查名称重复
            $buffer = $user->selectData(
                    'userid', 'username="' . $userName . '"');
            if ($buffer) {
                return [0,
                    '用户名重复请更换'];
            }
            //判断agent是否存在
            if ($agent) {
                $buffer = $user->selectData(
                        'userid', 'userid="' . $agent . '"');
                if (!$buffer)
                    $agent = 0;
            }


            //获取配置信息
            $peizhi = SL('Param')->getPZ();

            $data = array();
            $data['savecode'] = $user->saveCode();
            $data['username'] = $userName;
            $data['addtime'] = time();
            $data['password'] = md5($data['username'] . $password);
            $data['txpassword'] = md5($data['username'] . $txmm);
            $data['qq'] = $qq;
            $data['phone'] = $phone;
            $data['email'] = $email;
            $data['miyao'] = string('randString', 32);
            $data['lastip'] = get_client_ip(0, true);
            $data['addtime'] = time();
            $data['ifagent'] = $ifagent;
            $data['agent'] = $agent;
            $data['status'] = 0;
            if ($peizhi['ifregcheck'] == 1)
                $data['status'] = 1;
            $data['userid'] = $this->createUserID();
            if ($user->insertData($data) === false) {
                return [0,
                    '添加失败'];
            } else {
                //写入用户扣量
                SL('Kou')->addUser($data['userid']);

                //写入日志
                $this->userLog($this->moduleName, '用户注册【' . $data['userid'] . '】', $data['userid']);
                return [1,
                    '注册成功！',
                    __URL__];
            }
        }
    }

    /**
     * 登录事件验证
     */
    public function login($request) {
        if (IS_POST) {
            $userName = $request['username'];
            $password = $request['password'];
            if (empty($userName)) {
                return array(
                    0,
                    '账户不能为空');
            }
            if (empty($password)) {
                return array(
                    0,
                    '密码不能为空');
            }

            $fields = '*';
            $userName = preg_replace('/\s+/', '', $userName);
            $where = array();
            $where['username'] = $userName;
            $user = SM('User');
            $data = $user->findData(
                    $fields, $where
            );
            if (empty($data)) {
                return array(
                    0,
                    '账户密码有误。');
            }
            $password = trim($password);
            if ($data['password'] !== md5($data['username'] . $password)) {
                return array(
                    0,
                    '密码错误');
            }
            //判断用户状态
            if ($data['status'] == 1) {
                return array(
                    0,
                    '账户被锁定，请联系用户。');
            }

            //修改用户最后一次登录ip
            $newdata = array(
                'lastip' => get_client_ip(0, true),
                'logintimes' => $data['logintimes'] + 1
            );
            $user->updateData($newdata, 'id=' . $data['id']);

            //写入cookie
            $time = C('FX_COOKIE_TIMEOUT');
            $data['usercode'] = md5($data['userid'] . $data['username'] . $data['savecode'] . ceil(time() / $time));
            unset($data['password']);
            $this->userLog('用户登录', '用户【' . $data['username'] . '】登录系统', $data['userid']);
            $this->setCookieUserID($data['userid'], $time);
            $this->setCookieUserName($data['username'], $time);
            $this->setCookieCode($data['usercode'], $time);

            return array(
                1,
                '登录成功',
                U('Index/Home/index'));
        }
    }

    /**
     * 生成用户id
     */
    protected function createUserID() {
        $buffer = SM('User')->findData('userid', '1=1', 'userid desc');
        $year = (string) date('Y', time());
        if (empty($buffer)) {
            return $year . '100';
        }
        $rstr = substr($buffer['userid'], count($year));
        $n = str_repeat('9', count($rstr));
        if ($rstr == $n) {
            return $year . '1' . str_repeat('0', count($rstr));
        }
        return (int) $buffer['userid'] + 1;
    }

}
