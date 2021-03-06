<?php

// +----------------------------------------------------------------------
// | easy pay [ pay to easy ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 All rights reserved.
// +----------------------------------------------------------------------
// | Author: fengxing <QQ:51125330>
// +----------------------------------------------------------------------

namespace Common\Logic;

class PayLogic extends BaseLogic {

    protected $moduleName = '账单';
    public $zt = array(
        0 => '未支付',
        1 => '已支付',
        2 => '冻结');

    /**
     * 列表
     */
    public function index($request) {
        $map = array();
        $data = ' 1=1 ';
        //高级查询
        if ($request['userid']) {
            $map['userid'] = $request['userid'];
            $data .= ' AND userid = "' . $request['userid'] . '" ';
        }
        if (!is_numeric($request['status'])) {
            $request['status'] = 0;
            $_REQUEST['status'] = 0;
        }
        $map['status'] = $request['status'];
        $data .= ' AND status ="' . $request['status'] . '" ';

        $start = $request['start'];
        if (strstr($start, '-')) {
            $start = strtotime($start);
        }
        $end = $request['end'];
        if (strstr($end, '-')) {
            $end = strtotime($end);
        }
        if ($start) {
            if (empty($end))
                $end = time();
            $map['start'] = $start;
            $map['end'] = $end;
            $request['start'] = date('Y-m-d', $start);
            $request['end'] = date('Y-m-d', $end);
            $data .= ' AND addtime between ' . ($start) . ' and ' . ($end) . ' ';
        }

        $pay = SM('Pay');
        $count = $pay->selectCount(
                $data, 'id'); // 查询满足要求的总记录
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $perpage = C('FX_PERPAGE'); //每页行数
        $page = page($count, $request['p'], $perpage) . ',' . $perpage;
        $list = $pay->pageData('*', $data, 'id DESC', $page);
        foreach ($list as $i => $iList) {
            $list[$i]['statusname'] = $this->zt[$iList['status']];
            $list[$i]['addtime'] = date('Y-m-d H:i:s', $iList['addtime']);
            $list[$i] = string('formatMoneyByArray', $list[$i], array(
                'money'));
        }
        $pageList = $this->pageList($count, $perpage, $map);

        $titlename = $this->zt[$request['status']];

        //统计数据
        $times = strtotime(date('Y-m-d', time()));
        $tj = array();
        $tj['today'] = $pay->sumData('money', 'addtime>=' . $times);
        $tj['paytoday'] = $pay->sumData('money', 'status=1 and addtime>=' . $times);
        $tj['all'] = $pay->sumData('money', '1=1');
        $tj['payall'] = $pay->sumData('money', 'status=1');
        foreach ($tj as $i => $iTj) {
            if (empty($iTj))
                $tj[$i] = 0;
        }

        $params = array(
            'zt' => $this->zt,
            'tj' => $tj,
            'list' => $list,
            'page' => $pageList,
            'pageName' => $titlename . $this->moduleName . '管理'
        );
        return [1,
            $params];
    }

    /**
     * 保存
     */
    public function save($request) {
        $id = $request['id']; //获取数据标识
        $status = $request['status']; //获取数据标识

        if (empty($id) || !is_numeric($status)) {
            return [0,
                '数据标识不能为空！'];
        }

        if (!is_array($id)) {
            $id = explode(',', $id);
        }

        //状态为1的数据不能修改状态

        $pay = SM('Pay');
        if ($pay->updateData(array(
                    'status' => $status), 'id in (' . implode(',', $id) . ') && status!=1') === false) {
            return [0,
                '修改失败！'];
        } else {
            $this->adminLog($this->moduleName, '修改账单状态id为【' . implode(',', $id) . '】的数据');
            return [1,
                '修改成功！'];
        }
    }

    /**
     * 已支付账单
     */
    public function yzf($request) {
        header('Location:' . U('Pay/index', array(
                    'status' => 1)));
        exit();
    }

    /**
     * 发起支付
     * $check pzid支付id
     */
    public function payApi($request, $check = '') {
        $fxid = $request['fxid'];
        $fxddh = $request['fxddh'];
        $fxdesc = $request['fxdesc'];
        $fxfee = $request['fxfee'];
        $fxattch = $request['fxattch'];
        $fxnotifyurl = $request['fxnotifyurl'];
        $fxbackurl = $request['fxbackurl'];
        $fxpay = $request['fxpay'];
        $fxsign = $request['fxsign'];
        $fxip = $request['fxip'];
        //判断商户号 key是否存在
        $userBuffer = SM('User')->findData('*', 'userid=' . $fxid);
        if (!$userBuffer || $userBuffer['status'] == 1) {
            return [0,
                '商户号错误。'];
        }

        $fxkey = $userBuffer['miyao'];
        //判断订单长度
        if (strlen($fxddh) > 28) {
            return [0,
                '订单号长度必须小于28位。'];
        }

        if (empty($fxfee) || !is_numeric($fxfee * 100) || $fxfee <= 0) {
            return [0,
                '支付金额有误。'];
        }

        if ($fxfee > 49999) {
            return [0,
                '支付金额超限额,单笔最高49999。'];
        }

        //判断回调地址是否是http
        if (!checkString('checkIfHttp', $fxnotifyurl) || !checkString('checkIfHttp', $fxbackurl)) {
            return [0,
                '同步、异步回调网址有误。'];
        }

        //判断签名是否正确 商务号+商户订单号+支付金额+异步通知地址+商户秘钥
        if ($fxsign != md5($fxid . $fxddh . $fxfee . $fxnotifyurl . $fxkey)) {
            return [0,
                '签名错误。'];
        }

        //判断订单号重复
        $ddBuffer = SM('Dingdan')->findData('*', 'ordernum="' . $fxid . $fxddh . '"');
        if ($ddBuffer) {
            return [0,
                '订单号重复，请更换后重试。'];
        }

        //pay是否在允许范围内 接口及配置都有数据
        $jiekou = SM('Jiekou')->selectData('*', '1=1', 'jkid asc');
        $jiekoubuffer = string('arrayKey', $jiekou, 'jkstyle');

        if (!$jiekoubuffer[$fxpay]) {
            return [0,
                '请求类型有误。'];
        } else {
           
            
            $zjbuffer = SM('Jiekouzj')->findData('*', 'jkid=' . $jiekoubuffer[$fxpay]['jkid'] . ' and ifopen=1 and ifchoose=1');
            if (!$zjbuffer) {
                return [0,
                    '该请求类型暂时不可用。总后台已关闭此类型'];
            }
        }
        $userpzbuffer1 = SM('Userpz')->findData('*', 'jkid=' . $jiekoubuffer[$fxpay]['jkid'] .' and userid='.$userBuffer['id']);
        
        if ($userpzbuffer1) {
           $userpzbuffer = SM('Userpz')->findData('*', 'jkid=' . $jiekoubuffer[$fxpay]['jkid'] .' and userid='.$userBuffer['id']. ' and ifopen=1 and ifchoose=1');
            if (!$userpzbuffer) {
                return [0,
                    '该请求类型暂时不可用。商户已关闭此类型'];
            }
        }


        //查询支付账户和类型
        $jkpz = SM('Jiekoupeizhi')->findData('*', 'pzid=' . $zjbuffer['pzid']);
        if (!$jkpz) {
            return [0,
                '该请求类型暂时不可用。'];
        }
       
        $jkdata = array(
            'id' => $zjbuffer['zjid'],
            'peizhi' => unserialize($jkpz['params']),
            'style' => $jkpz['style'],
            'fxddh' => $fxid . $fxddh,
            'fxdesc' => $fxdesc,
            'fxfee' => $fxfee,
            'fxattch' => $fxattch,
            'fxnotifyurl' =>  "http://" . $_SERVER['HTTP_HOST'] . "/Pay/notify/" . $jkpz['style'],//本站回调
            'fxbackurl' => $fxbackurl, //用户同步回调 用户需要做订单查询以支持实时订单状态
            'fxpay' => $fxpay,
            'fxip' => $fxip
        );

        //调用接口返回
        $result = SL('PayApi')->index($jkdata);

        if ($result[0] == 1) {
            $userpz = SM('Userpz')->findData('*', 'jkid=' . $jiekoubuffer[$fxpay]['jkid'] .' and userid='.$userBuffer['id']. ' and ifopen=1 and ifchoose=1');
            if ($userpz) {
                $fl = $userpz['fl'];//商户专属费率  目前使用这个
            }else{
                $fl = $zjbuffer['fl'];//全网费率
            }
           
            $havemoney = $fxfee - $fxfee * $fl / 100;
            if ($havemoney < 0.01) {
                $havemoney = 0;
            } else {
                $havemoney = round($havemoney, 2);
            }
            // 计算代理提成
            $userdata = SM('User')->findData('*', 'userid='.$fxid. ' and status=0');//查出支付的商户人
            
            if (empty($userdata['agent'])) {
                $agentmoney =0;
                $agentfl =0;
            }else{
                $agentdata = SM('User')->findData('*', 'userid='.$userdata['agent']. ' and status=0');//查出支付的商户的代理
                $agentpz = SM('Userpz')->findData('*', 'jkid=' . $jiekoubuffer[$fxpay]['jkid'] .' and userid='.$agentdata['id']. ' and ifopen=1 and ifchoose=1');
                if ($userpz) {
                    $agentfl = $agentpz['fl'];//商户专属费率  目前使用这个
                }else{
                    $agentfl = $zjbuffer['fl'];//全网费率
                }
                $agentmoney = (($fl- $agentfl) / 100) * $fxfee;//(商户费率-代理费率)*支付金额
            }
            if ($agentmoney < 0.01) {
                $agentmoney = 0;
            } else {
                $agentmoney = round($agentmoney, 2);
            }

            $fj = array(
                'fxdesc' => $fxdesc,
                'fxattch' => $fxattch,
                'fxnotifyurl' => $fxnotifyurl,
                'fxbackurl' => $fxbackurl,
                'fxip' => $fxip
            );

            //写入订单
            $data = array(
                'status' => 0,
                'ordernum' => $fxid . $fxddh,
                'userid' => $fxid,
                'totalmoney' => $fxfee,
                'havemoney' => $havemoney,
                'agentmoney' => $agentmoney,
                'tz' => 0,
                'preordernum' => '',
                'zjid' => $zjbuffer['zjid'],
                'jkid' => $jiekoubuffer[$fxpay]['jkid'],
                'addtime' => time(),
                'fl' => $fl,
                'agentfl' => $agentfl,
                'jkstyle' => $fxpay,
                'paytime' => 0,
                'fj' => serialize($fj)
            );
            
            SM('Dingdan')->insertData($data);
            //写入支付日志
            //$this->paylog('写入订单', '写入订单的数据',serialize($data),$fxid);
        }
        return $result;
    }
    // -----------------------------------------异步回调兴业银行 s-----------------------------------------------------
    public function notify_xingye($request) {
        include_once COMMON_PATH . "/Tool/xingye/Utils.class.php";//辅助函数
        include_once COMMON_PATH . "/Tool/xingye/config/config.php";//常用配置
        $cfg = new \Config();
        $utils = new \Utils();
        if (empty($request)){
             return [0,
                '异步回调错误，请重试。'];
        }
        
        #log
        //$utils->xywriteLog("最后回调数据: ".var_export($request,true));
        //写入支付日志
        $this->paylog('兴业回调金富通', '回调到支付系统数据',serialize($request));
        $ddh = $request['out_trade_no'];
        //根据订单号查找对应支付账户
        $ddBuffer = SM('Dingdan')->findData('*', 'ordernum="' . $ddh . '"');
        if (!$ddBuffer) {
            return [0,
                '订单号不存在，请重试。'];
        }
        //获取支付账号
        // $pzBuffer = SL('Api')->getJkByZj($ddBuffer['zjid']);
        // $jkpz = unserialize($pzBuffer['params']);
        // $config['mchid'] = $jkpz['xingye_mchid'];
        // $config['key'] = $jkpz['xingye_key'];
        $mch_config=$cfg->multidimensional_search($cfg->Randomnotify,$request['mch_id']);//轮询的商户配置
        if (empty($mch_config['appid']) || empty($mch_config['key'])) {
            return [0,
                '兴业商户号不存在或轮询商户号不一致，请重试。'];
        }
        $config['mchid'] = $mch_config['appid'];
        $config['key'] = $mch_config['key'];
        // 异步回调检查
        $result = $utils->notifySign($request,$config['key']);
        if ($result) {
            //商户订单号
            $out_trade_no = $request['out_trade_no'];
            //兴业交易号
            $trade_no = $request['transaction_id'];
            //交易状态
            $trade_status = $request['trade_status'];
            //订单金额
            $total_amount = $request['total_fee'] /100;

            $newdata = array();
            $newdata['ddh'] = $out_trade_no;
            $newdata['qudao'] = $trade_no;
            $newdata['fee'] = $total_amount;
            $newdata['type'] = 1;
            $newdata['method'] = 'post';

             if ($request['status'] == 0 && $request['result_code'] == 0) {
                $return = $this->changeDingdan($newdata);
                if ($return[0] === 0)
                    exit('fail');
               
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "success";  //请不要修改或删除
        } else {
            //验证失败
            echo "fail"; //请不要修改或删除
        }
        exit();
    }
     /**
     * 同步回调支付宝
     */
    public function backurl_xingye($request) {

    }
    //---------------------------------------------异步回调兴业银行 e--------------------------------------------------


    // -----------------------------------------异步回调银联悦单 s-----------------------------------------------------
    public function notify_yuedan($request) {
        include_once COMMON_PATH . "/Tool/yuedan/config/config.php";//常用配置
        $cfg = new \Config();
        if (empty($request)){
             return [0,
                '异步回调错误，请重试。'];
        }
        
        //写入支付日志
        $this->paylog('银联悦单', '回调到支付系统数据',serialize($request));
        $ddh =substr($request['billNo'], strlen($cfg->Con('msgSrcId')));
        include_once COMMON_PATH . "/Tool/xingye/Utils.class.php";//辅助函数
        $utils = new \Utils();
       
        //根据订单号查找对应支付账户
        $ddBuffer = SM('Dingdan')->findData('*', 'ordernum="' . $ddh . '"');
        if (!$ddBuffer) {
            return [0,
                '订单号不存在，请重试。'];
        }
        // 异步回调检查
        $result = $cfg->notifySign($request);
        //$utils->xywriteLog("yd1result: ".$result);
        if ($request['sign']) {
            $request['billPayment']=json_decode($request['billPayment'],true);
            //商户订单号
            $out_trade_no = $ddh;
            //兴业交易号
            $trade_no = $request['billPayment']['targetOrderId'];
            //交易状态
            $trade_status = $request['billStatus'];
            //订单金额
            $total_amount = $request['totalAmount'] /100;

            $newdata = array();
            $newdata['ddh'] = $out_trade_no;
            $newdata['qudao'] = $trade_no;
            $newdata['fee'] = $total_amount;
            $newdata['type'] = 2;
            $newdata['method'] = 'post';
            $utils->xywriteLog("newdata: ".var_export($newdata,true));

             if ($request['billPayment']['status']=='TRADE_SUCCESS' && $request['billStatus']=='PAID') {
                $return = $this->changeDingdan($newdata);
                if ($return[0] === 0)
                    exit('fail');
               
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "SUCCESS";  //请不要修改或删除
        } else {
            //验证失败
            echo "fail"; //请不要修改或删除
        }
        exit();
    }
     /**
     * 同步回调
     */
    public function backurl_yuedan($request) {

    }

    public function backurl_star($request) {

    }
    public function backurl_pdd($request) {

    }
    public function backurl_buff($request) {

    }
    //---------------------------------------------异步回调银联悦单 e--------------------------------------------------


    // --------------------------------------------开联通--------------------------------------------------------------
    // -----------------------------------------异步回调银联悦单 s-----------------------------------------------------
    public function notify_hyt($request) {
        
        //写入支付日志
        $this->paylog('合益通回调', '回调数据',serialize($request));
        $ddh = $request['orderid'];
        //根据订单号查找对应支付账户
        $ddBuffer = SM('Dingdan')->findData('*', 'ordernum="' . $ddh . '"');
        if (!$ddBuffer) {
            return [0,
                '订单号不存在，请重试。'];
        }

        //获取支付账号
        $pzBuffer = SL('Api')->getJkByZj($ddBuffer['zjid']);
        $jkpz = unserialize($pzBuffer['params']);
        $returnArray = array( // 返回字段
            "memberid" => $request["memberid"], // 商户ID
            "orderid" =>  $request["orderid"], // 订单号
            "amount" =>  $request["amount"], // 交易金额
            "datetime" =>  $request["datetime"], // 交易时间
            "transaction_id" =>  $request["transaction_id"], // 支付流水号
            "returncode" => $request["returncode"],
        );
        $md5key = $jkpz['hyt_key'];
        ksort($returnArray);
        reset($returnArray);
        $md5str = "";
        foreach ($returnArray as $key => $val) {
            $md5str = $md5str . $key . "=" . $val . "&";
        }
        $mysign = strtoupper(md5($md5str . "key=" . $md5key));
        
        if ($request['sign'] == $mysign) {
            //商户订单号
            $out_trade_no = $ddh;
            //兴业交易号
            $trade_no = $request['transaction_id'];
            //交易状态
            $trade_status = $request['returncode'];
            //订单金额
            $total_amount = $request['amount'];

            $newdata = array();
            $newdata['ddh'] = $out_trade_no;
            $newdata['qudao'] = $trade_no;
            $newdata['fee'] = $total_amount;
            $newdata['type'] = 6;
            $newdata['method'] = 'post';
            //$utils->xywriteLog("newdata: ".var_export($newdata,true));

            if ($request['returncode']== "00") {
                $return = $this->changeDingdan($newdata);
                if ($return[0] === 0)
                    exit('fail');
               
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "OK";  //请不要修改或删除
        } else {
            //验证失败
            echo "fail"; //请不要修改或删除
        }
        exit();
    }

    //签名前数组转字符串
    public function arrayToString($data) {
        ksort($data);
        $query = '';
        foreach ($data as $key => $value) {
            if ($key=='sign' || $key == 'signMsg' || $key=='errorCode' || $key == 'errorMsg'|| $key == 'ext2' || $value == '' || $value == null) {
                continue;
            }
            $query .= $key."=".$value."&";
        }
        return substr($query, 0, -1);
    }
     /**
     * 同步回调
     */
    public function backurl_hyt($request) {

    }
    //---------------------------------------------开联通 e--------------------------------------------------


    // -----------------------------------------星支付 s-----------------------------------------------------
    public function notify_star($request) {
        //写入支付日志
        $this->paylog('星支付回调', '回调数据',serialize($request));
        $ddh = $request['order_sn'];
        //根据订单号查找对应支付账户
        $ddBuffer = SM('Dingdan')->findData('*', 'ordernum="' . $ddh . '"');
        if (!$ddBuffer) {
            return [0,
                '订单号不存在，请重试。'];
        }

        //获取支付账号
        $pzBuffer = SL('Api')->getJkByZj($ddBuffer['zjid']);
        $jkpz = unserialize($pzBuffer['params']);

        $mysign = md5($request['user_id'].$request['order_sn'].$request['amount'].$jkpz['star_key']);
        if ($request['sign'] == $mysign) {        
            //商户订单号
            $out_trade_no = $ddh;
            //兴业交易号
            $trade_no = $request['transaction_id'];
            //订单金额
            $total_amount = $request['amount'] / 100;

            $newdata = array();
            $newdata['ddh'] = $out_trade_no;
            $newdata['qudao'] = $trade_no;
            $newdata['fee'] = $total_amount;
            $newdata['type'] = 7;
            $newdata['method'] = 'post';

            $return = $this->changeDingdan($newdata);
            if ($return[0] === 0) exit('fail');
                
            echo "SUCCESS";  //请不要修改或删除
        } else {
            //验证失败
            echo "fail"; //请不要修改或删除
        }
        exit();
    }

    // -----------------------------------------拼多多 s-----------------------------------------------------
    public function notify_bshpdd($request) {
        //写入支付日志  
        $this->paylog('pdd回调', '回调数据',serialize($request)); 
        $ddh = $request['reorderid'];
        //根据订单号查找对应支付账户
        $ddBuffer = SM('Dingdan')->findData('*', 'ordernum="' . $ddh . '"');
        if (!$ddBuffer) {
            return [0,
                '订单号不存在，请重试。'];
        }

        if ($ddBuffer['tz'] == 2) {
            exit('ok');
        }

        //获取支付账号
        $pzBuffer = SL('Api')->getJkByZj($ddBuffer['zjid']);
        $jkpz = unserialize($pzBuffer['params']);
        $str ='mc_id='.$request['mc_id'].'&reorderid='.$request['reorderid'].'&status='.$request['status'].'&successtime='.$request['successtime'].'&sysorderid='.$request['sysorderid'].'&key='.$jkpz['pdd_key'];
        $mysign =md5($str);
        if ($request['sign'] == $mysign) {           
            //商户订单号
            $out_trade_no = $ddh;
            //兴业交易号
            $trade_no = $request['sysorderid'];
            //订单金额
            $total_amount = $ddBuffer['totalmoney'];

            $newdata = array();
            $newdata['ddh'] = $out_trade_no;
            $newdata['qudao'] = $trade_no;
            $newdata['fee'] = $total_amount;
            $newdata['type'] = 8;
            $newdata['method'] = 'post';

            if ($request['status'] == 'success') {
                $return = $this->changeDingdan($newdata);
                if ($return[0] === 0) exit('fail');
            }
 
            echo "ok";  //请不要修改或删除
        } else {
            //验证失败
            echo "fail"; //请不要修改或删除
        }
        exit();
    }

    // -----------------------------------------拼多多 s-----------------------------------------------------
    public function notify_buff($request) {
        //写入支付日志  
        $this->paylog('buff回调', '回调数据',serialize($request)); 
        $ddh = $request['reorderid'];
        //根据订单号查找对应支付账户
        $ddBuffer = SM('Dingdan')->findData('*', 'ordernum="' . $ddh . '"');
        if (!$ddBuffer) {
            return [0,
                '订单号不存在，请重试。'];
        }

        if ($ddBuffer['tz'] == 2) {
            exit('ok');
        }

        //获取支付账号
        $pzBuffer = SL('Api')->getJkByZj($ddBuffer['zjid']);
        $jkpz = unserialize($pzBuffer['params']);
        $str ='mc_id='.$request['mc_id'].'&reorderid='.$request['reorderid'].'&status='.$request['status'].'&successtime='.$request['successtime'].'&sysorderid='.$request['sysorderid'].'&key='.$jkpz['buff_key'];
        $mysign =md5($str);
        if ($request['sign'] == $mysign) {           
            //商户订单号
            $out_trade_no = $ddh;
            //兴业交易号
            $trade_no = $request['sysorderid'];
            //订单金额
            $total_amount = $ddBuffer['totalmoney'];

            $newdata = array();
            $newdata['ddh'] = $out_trade_no;
            $newdata['qudao'] = $trade_no;
            $newdata['fee'] = $total_amount;
            $newdata['type'] = 9;
            $newdata['method'] = 'post';

            if ($request['status'] == 'success') {
                $return = $this->changeDingdan($newdata);
                if ($return[0] === 0) exit('fail');
            }
 
            echo "ok";  //请不要修改或删除
        } else {
            //验证失败
            echo "fail"; //请不要修改或删除
        }
        exit();
    }

    // -----------------------------------------异步回调银联悦单 s-----------------------------------------------------
    public function notify_umsh5($request) {
        include_once COMMON_PATH . "/Tool/umsh5/config/config.php";//常用配置
        $cfg = new \Config();
        if (empty($request)){
             return [0,
                '异步回调错误，请重试。'];
        }
        
        $ddh =substr($request['merOrderId'], strlen($cfg->Con('msgSrcId')));

        //根据订单号查找对应支付账户
        $ddBuffer = SM('Dingdan')->findData('*', 'ordernum="' . $ddh . '"');
        if (!$ddBuffer) {
            return [0,
                '订单号不存在，请重试。'];
        }
        // 异步回调检查
        //$result = $cfg->notifySign($request);
       
        if ($request['sign']) {
            //商户订单号
            $out_trade_no = $ddh;
            //兴业交易号
            $trade_no = $request['targetOrderId'];
            //交易状态
            $trade_status = $request['status'];
            //订单金额
            $total_amount = $request['totalAmount'] /100;

            $newdata = array();
            $newdata['ddh'] = $out_trade_no;
            $newdata['qudao'] = $trade_no;
            $newdata['fee'] = $total_amount;
            $newdata['type'] = 5;
            $newdata['method'] = 'post';

             if ($request['status']=='TRADE_SUCCESS') {
                $return = $this->changeDingdan($newdata);
                if ($return[0] === 0)
                    exit('fail');
               
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "SUCCESS";  //请不要修改或删除
        } else {
            //验证失败
            echo "fail"; //请不要修改或删除
        }
        exit();
    }
     /**
     * 同步回调
     */
    public function backurl_umsh5($request) {

    }
    //---------------------------------------------异步回调银联H5 e--------------------------------------------------

    /**
     * 异步回调微信
     */
    public function notify_wechat($request) {
        include_once COMMON_PATH . "/Tool/wechat/lib/WxPay.Api.php";
        include_once COMMON_PATH . "/Tool/wechat/lib/WxPay.Notify.php";
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $result = \WxPayResults::Init($xml);
        $ddh = $result['out_trade_no'];
        //根据订单号查找对应支付账户
        $ddBuffer = SM('Dingdan')->findData('*', 'ordernum="' . $ddh . '"');
        if (!$ddBuffer) {
            return [0,
                '订单号不存在，请重试。'];
        }
        //获取支付账号
        $pzBuffer = SL('Api')->getJkByZj($ddBuffer['zjid']);
        $jkpz = unserialize($pzBuffer['params']);

        \WxPayConfig::setAPPID($jkpz['wechat_appid']);
        \WxPayConfig::setMCHID($jkpz['wechat_mchid']);
        \WxPayConfig::setKEY($jkpz['wechat_key']);
        \WxPayConfig::setAPPSECRET($jkpz['wechat_appsecret']);
        if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
           $data=array();
           $data['ddh']=$result['out_trade_no'];
           $data['qudao']=$result['transaction_id'];
           $data['fee']=$result['total_fee']/100;
           $data['method']='post';
           $data['type'] = 0;

           $return = $this->changeDingdan($data);
       }
       if($return[0]===0){
           return ;
       }
       $reptyxml='<xml>
  <return_code><![CDATA[SUCCESS]]></return_code>
  <return_msg><![CDATA[OK]]></return_msg>
</xml>';
       \WxpayApi::replyNotify($reptyxml);
        exit();
    }

    /**
     * 同步回调微信
     */
    public function backurl_wechat($request) {

    }

    /**
     * 异步回调支付宝
     */
    public function notify_alipay($request) {
        include_once COMMON_PATH . "/tool/alipay/config.php";
        include_once COMMON_PATH . "/Tool/alipay/wappay/service/AlipayTradeService.php";
        include_once COMMON_PATH . "/Tool/alipay/config/config.php";//常用配置
        $cfg = new \Config();

        $ddh = $request['out_trade_no'];
        //根据订单号查找对应支付账户
        $ddBuffer = SM('Dingdan')->findData('*', 'ordernum="' . $ddh . '"');
        if (!$ddBuffer) {
            return [0,
                '订单号不存在，请重试。'];
        }
        //获取支付账号
        $pzBuffer = SL('Api')->getJkByZj($ddBuffer['zjid']);
        $jkpz = unserialize($pzBuffer['params']);

        // 数据表配置支付宝
        $Randomnotify = SM('Alipay')->selectData('appid,private,public', 'status=0', 'id asc');
        $mch_config=$cfg->multidimensional_search($Randomnotify,$request['app_id']);//轮询的商户配置
        if (empty($mch_config['appid']) || empty($mch_config['private']) || empty($mch_config['public'])) {
            return [0,
                '支付宝商户号不存在或轮询商户号不一致，请重试。'];
        }
        $config['app_id'] = $mch_config['appid'];
        $config['merchant_private_key'] = $mch_config['private'];
        $config['alipay_public_key'] = $mch_config['public'];
        // $config['app_id'] = $jkpz['alipay_appid'];
        // $config['merchant_private_key'] = $jkpz['alipay_private'];
        // $config['alipay_public_key'] = $jkpz['alipay_public'];
        $config['sign_type'] = $jkpz['alipay_sign'];

        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($request);
        
        if (1) {
            //商户订单号
            $out_trade_no = $request['out_trade_no'];
            //支付宝交易号
            $trade_no = $request['trade_no'];
            //交易状态
            $trade_status = $request['trade_status'];
            //订单金额
            $total_amount = $request['total_amount'];
            //描述
            $body = $request['body'];

            $newdata = array();
            $newdata['ddh'] = $out_trade_no;
            $newdata['qudao'] = $trade_no;
            $newdata['fee'] = $total_amount;
            $newdata['method'] = 'post';
            $newdata['type'] = 3;

            if ($request['trade_status'] == 'TRADE_FINISHED') {
                $return = $this->changeDingdan($newdata);
                if ($return[0] === 0)
                    exit('fail');
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
            } else if ($request['trade_status'] == 'TRADE_SUCCESS') {
                $return = $this->changeDingdan($newdata);
                if ($return[0] === 0)
                    exit('fail');
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "success";  //请不要修改或删除
        } else {
            //验证失败
            echo "fail"; //请不要修改或删除
        }
        exit();
    }

    // 
     /**
     * 异步回调支付宝
     */
    public function notify_zhifubao($request) {
         $this->paylog('官方回调', '回调到支付系统数据',serialize($request));
        include_once COMMON_PATH . "/tool/alipay/config.php";
        include_once COMMON_PATH . "/Tool/alipay/wappay/service/AlipayTradeService.php";
        include_once COMMON_PATH . "/Tool/zhifubao/config/config.php";//常用配置
        $cfg = new \Config();

        $ddh = $request['out_trade_no'];
        //根据订单号查找对应支付账户
        $ddBuffer = SM('Dingdan')->findData('*', 'ordernum="' . $ddh . '"');
        if (!$ddBuffer) {
            return [0,
                '订单号不存在，请重试。'];
        }
        //获取支付账号
        $pzBuffer = SL('Api')->getJkByZj($ddBuffer['zjid']);
        $jkpz = unserialize($pzBuffer['params']);

        $mch_config=$cfg->multidimensional_search($cfg->Randomnotify,$request['app_id']);//轮询的商户配置
        if (empty($mch_config['appid']) || empty($mch_config['private']) || empty($mch_config['public'])) {
            return [0,
                '支付宝商户号不存在或轮询商户号不一致，请重试。'];
        }
        $config['app_id'] = $mch_config['appid'];
        $config['merchant_private_key'] = $mch_config['private'];
        $config['alipay_public_key'] = $mch_config['public'];
        // $config['app_id'] = $jkpz['alipay_appid'];
        // $config['merchant_private_key'] = $jkpz['alipay_private'];
        // $config['alipay_public_key'] = $jkpz['alipay_public'];
        $config['sign_type'] = $jkpz['alipay_sign'];

        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($request);
        
        if (1) {
            //商户订单号
            $out_trade_no = $request['out_trade_no'];
            //支付宝交易号
            $trade_no = $request['trade_no'];
            //交易状态
            $trade_status = $request['trade_status'];
            //订单金额
            $total_amount = $request['total_amount'];
            //描述
            $body = $request['body'];

            $newdata = array();
            $newdata['ddh'] = $out_trade_no;
            $newdata['qudao'] = $trade_no;
            $newdata['fee'] = $total_amount;
            $newdata['method'] = 'post';
            $newdata['type'] = 3;

            if ($request['trade_status'] == 'TRADE_FINISHED') {
                $return = $this->changeDingdan($newdata);
                if ($return[0] === 0)
                    exit('fail');
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
            } else if ($request['trade_status'] == 'TRADE_SUCCESS') {
                $return = $this->changeDingdan($newdata);
                if ($return[0] === 0)
                    exit('fail');
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "success";  //请不要修改或删除
        } else {
            //验证失败
            echo "fail"; //请不要修改或删除
        }
        exit();
    }

     /**
     * 异步回调支付宝
     */
    public function notify_yinlian($request) {
        //$this->paylog('银联回调paymax', '回调到支付系统数据',serialize($request));
        include_once COMMON_PATH . "/Tool/yinlian/util/common.php";//常用配置
        include_once COMMON_PATH . "/Tool/yinlian/util/SecssUtil.class.php";//常用配置
        $secssUtil = new \SecssUtil();
        $securityPropFile = COMMON_PATH . "Tool/yinlian/config/security.properties";
        $secssUtil->init($securityPropFile);
        $ddh = $request['MerOrderNo'];
        //根据订单号查找对应支付账户
        $ddBuffer = SM('Dingdan')->findData('*', 'ordernum="' . $ddh . '"');
       
        if (!$ddBuffer) {
            return [0,
                '订单号不存在，请重试。'];
        }
         
        if ($secssUtil->verify($request)) {
            //商户订单号
            $out_trade_no = $request['MerOrderNo'];
            //支付宝交易号
            $trade_no = $request['AcqSeqId'];
            //交易状态
            $trade_status = $request['OrderStatus'];
            //订单金额
            $total_amount = $request['OrderAmt']/100;

            $newdata = array();
            $newdata['ddh'] = $out_trade_no;
            $newdata['qudao'] = $trade_no;
            $newdata['fee'] = $total_amount;
            $newdata['method'] = 'post';
            $newdata['type'] = 4;

            if ($request['OrderStatus'] == '0000') {
                $return = $this->changeDingdan($newdata);
                if ($return[0] === 0)
                    exit('fail');
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "success";  //请不要修改或删除
        } else {
            //验证失败
            echo "fail"; //请不要修改或删除
        }
        exit();
    }
     /**
     * 同步回调银联电子
     */
    public function backurl_yinlian($request) {

    }

    /**
     * 同步回调支付宝
     */
    public function backurl_alipay($request) {

    }

    /**
     * 同步回调支付宝
     */
    public function backurl_zhifubao($request) {

    }
    public function scanpay($request) {
            echo "<pre>";
            print_r($request);exit();
           
    }

    /**
     * 公众号类H5支付
     */
    public function jsapi($request) {
        $style = $request['style'];

        $id = $request['id'];
        $ddh = $request['ddh'];
        //判断订单号
        $ddBuffer = SM('Dingdan')->findData('*', 'ordernum="' . $ddh . '"');
        if (!$ddBuffer) {
            return [0,
                '订单号不存在，请重试。'];
        }

        //获取支付账号
        $pzBuffer = SL('Api')->getJkByZj($id);
        if (!$pzBuffer) {
            return [0,
                '数据id错误，请重试。'];
        }
        if (!$pzBuffer['params']) {
            return [0,
                '数据id错误，请重试。。'];
        }
        if (!$pzBuffer['jkstyle']) {
            return [0,
                '数据id错误，请重试。。。'];
        }
        switch ($style) {
            case 'wechat':
                return $this->jsapi_wechat($pzBuffer, $ddBuffer);
                break;
        }
    }

    //微信公众号
    protected function jsapi_wechat($pzBuffer, $ddBuffer) {
        global $publicData;
        $peizhi = $publicData['peizhi'];

        $fj = unserialize($ddBuffer['fj']);
        $jkdata = array(
            'id' => $pzBuffer['zjid'],
            'peizhi' => unserialize($pzBuffer['params']),
            'apihttp' => $peizhi['apihttp'],
            'style' => $pzBuffer['style'],
            'fxddh' => $ddBuffer['ordernum'],
            'fxdesc' => $fj['fxdesc'],
            'fxfee' => $ddBuffer['totalmoney'],
            'fxattch' => $fj['fxattch'],
            'fxnotifyurl' => "http://" . $_SERVER['HTTP_HOST'] . "/Pay/notify/" . $pzBuffer['style'], //本站回调
            'fxbackurl' => $fj['fxbackurl'], //用户同步回调 用户需要做订单查询以支持实时订单状态
            'fxpay' => $ddBuffer['jkstyle'],
            'fxip' => $fj['fxip'],
            'isjsapi' => 1
        );

        //调用接口返回
        return SL('PayApi')->index($jkdata);
    }

    /**
     * 订单状态改变，在支付成功后
     * @param array $data ['ddh'=>订单号,'fee'=>金额,'qudao'=>渠道,'method'=>'post or get','back'=>【1代表返回路径】]
     */
    public function changeDingdan($data) {
        global $publicData;
        $peizhi = $publicData['peizhi'];

        //判断订单是否已经支付
        $dingdan = SM('Dingdan');
        $ddBuffer = SM('Dingdan')->findData('*', 'ordernum="' . $data['ddh'] . '"');
        if (!$ddBuffer)
            return [0,
                'no order in db'];

        if ($ddBuffer['totalmoney'] != $data['fee']) {
            return [0,
                'pay money diff'];
        }

        if ($ddBuffer['tz'] == 2) {
            return [0,
                'order success'];
        }

        if ($ddBuffer['status'] == 0) {
            $dingdan->dbStartTrans(); //开始事务
            $flag = true; //事务标志
            $status = 1;
            $money = 0;
            //获取用户扣量信息
            $zijianBuffer = SM('Zijian')->findData('*', 'userid=' . $ddBuffer['userid']);
            if (!$zijianBuffer || $zijianBuffer['initval'] == 0) {
                //不扣量状态
                $money = $ddBuffer['havemoney'];
            } else {
                $zijian = $zijianBuffer['zijian'] - 1;
                if ($zijian == 0) {
                    if ($ddBuffer['totalmoney'] > $peizhi['klinitmoney'] && $ddBuffer['totalmoney'] > 0) {
                        SM('Zijian')->updateData(array(
                            'zijian' => $zijianBuffer['initval']), 'userid=' . $ddBuffer['userid']);
                        $status = 2;
                    } else {
                        $money = $ddBuffer['havemoney'];
                    }
                } else {
                    SM('Zijian')->updateData(array(
                        'zijian' => $zijian), 'userid=' . $ddBuffer['userid']);
                    $money = $ddBuffer['havemoney'];
                }
            }
            //改变订单状态
            $ddBuffer['paytime'] = time();
            $ddBuffer['status'] = $status;
            $ddBuffer['preordernum'] = $data['qudao'];

            $result = $dingdan->updateData(array(
                'status' => $status,
                'paytime' => time(),
                'preordernum' => $data['qudao']), 'ddid=' . $ddBuffer['ddid']);
            if ($result === false)
                $flag = false;

            if ($money > 0 && $ddBuffer['tz'] != 2) {
                // 从自增改为更新余额
                $usersBuffer = SM('User')->findData('*', 'userid=' . $ddBuffer['userid']);
                $result = SM('User')->updateData(array(
                        'money' => ($usersBuffer['money'] + $money)), 'userid=' . $ddBuffer['userid']);
                //$result = SM('User')->conAddData('money=money+' . $money, 'userid=' . $ddBuffer['userid'], 'money');
                if ($result === false)
                    $flag = false;
            }
            // 分发代理提成
            if ($ddBuffer['agentmoney']>0) {
                $usersBuffer = SM('User')->findData('*', 'userid=' . $ddBuffer['userid']);
                if (!empty($usersBuffer['agent'])) {
                    //写入返点明细表
                    $fandiandata = array(
                        'type' => $data['type'],//1 兴业 2 悦单
                        'orderid' => $ddBuffer['ddid'],
                        'userid' => $usersBuffer['agent'],
                        'havemoney' => $ddBuffer['agentmoney'],
                        'addtime' => time(),
                    ); 
                    SM('Fddetail')->insertData($fandiandata);
                    //写入支付日志
                    $this->paylog('写入返点', '写入返点明细表',serialize($fandiandata),$ddBuffer['userid']);
                    //$results = SM('User')->conAddData('money=money+' . $ddBuffer['agentmoney'], 'userid=' . $usersBuffer['agent'], 'money');

                    $agentBuffer = SM('User')->findData('*', 'userid=' . $usersBuffer['agent']);
                    $result = SM('User')->updateData(array(
                        'money' => ($agentBuffer['money'] + $ddBuffer['agentmoney'])), 'userid=' . $usersBuffer['agent']);
                    //$results = SM('User')->conAddData('money=money+' . $ddBuffer['agentmoney'], 'userid=' . $usersBuffer['agent'], 'money');
                    if ($result === false)
                    $flag = false;
                }
            }

            //处理事务
            if ($flag === false) {
                $dingdan->dbRollback();
                return [0,
                    'db change error'];
            } else {
                $dingdan->dbCommit();
            }
        } else {
            $status = $ddBuffer['status'];
        }
         #兴业专属
        include_once COMMON_PATH . "/Tool/xingye/Utils.class.php";//辅助函数
        $utils = new \Utils();

        if ($status != 2) {
            //通知用户
            $userBuffer = SM('User')->findData('*', 'userid=' . $ddBuffer['userid']);
            $fj = unserialize($ddBuffer['fj']);
            $ddhYuan = substr($ddBuffer['ordernum'], strlen($ddBuffer['userid']));
            $pp = $status . $ddBuffer['userid'] . $ddhYuan . $ddBuffer['totalmoney'] . $userBuffer['miyao'];
            $k = md5($pp);
            $post_data = array(
                'fxid' => $ddBuffer['userid'],
                'fxddh' => $ddhYuan,
                'fxorder' => $ddBuffer['preordernum'],
                'fxdesc' => $fj['fxdesc'],
                'fxfee' => $ddBuffer['totalmoney'],
                'fxattch' => $fj['fxattch'],
                'fxstatus' => $status,
                'fxtime' => $ddBuffer['paytime'],
                'fxsign' => $k
            );
           
        // $utils->xywriteLog("回调福汇数据: ".var_export($post_data,true));
         //写入支付日志
        $this->paylog('金富通回调商户', '回调到商户数据',serialize($post_data), $ddBuffer['userid']);

            if ($data['method'] == 'post') {
                $url = $fj['fxnotifyurl'];
                $result = curl($url, $post_data);
            } else {
                $url = $fj['fxbackurl'];
                $arr = array();
                foreach ($post_data as $i => $k) {
                    $arr[] = $i . '=' . urlencode($k);
                }
                $url = $url . '?' . implode('&', $arr);
                if ($data['back'] == 1)
                    return $url;

                $result = curl($url);
            }
             $utils->xywriteLog("result: ".$result);
            if (strtolower($result) == 'success' && $ddBuffer['tz'] < 2) {
                //通知成功
                SM('Dingdan')->updateData(array(
                    'tz' => 2), 'ddid=' . $ddBuffer['ddid']);
            } elseif ($ddBuffer['tz'] < 1) {
                SM('Dingdan')->updateData(array(
                    'tz' => 1), 'ddid=' . $ddBuffer['ddid']);
            }
        }

        return [1,
            'success'];
    }

    /**
     * 查询订单状态
     */
    public function payQuery($request) {
        $fxid = $request['fxid'];
        $fxddh = $request['fxddh'];
        $fxsign = $request['fxsign'];
        $fxaction = $request['fxaction'];

        //判断商户号 key是否存在
        $userBuffer = SM('User')->findData('*', 'userid=' . $fxid);
        if (!$userBuffer || $userBuffer['status'] == 1) {
            return [0,
                '商户号错误。'];
        }

		$fxkey = $userBuffer['miyao'];
        //判断订单长度
        if (strlen($fxddh) > 28) {
            return [0,
                '订单号长度必须小于28位。'];
        }

        //判断签名是否正确 商务号+商户订单号+商户秘钥
        if ($fxsign != md5($fxid . $fxddh . $fxaction . $fxkey)) {
            return [0,
                '签名错误。'];
        }

        $buffer = SM('Dingdan')->findData('*', 'ordernum="' . $fxid . $fxddh . '" and status<2');
        if (!$buffer) {
            return [0,
                '订单号不存在。'];
        }

        $fj = unserialize($buffer['fj']);
        $data = array(
            'fxid' => $fxid,
            'fxstatus' => $buffer['status'],
            'fxddh' => $fxddh,
            'fxorder' => $buffer['preordernum'],
            'fxdesc' => $fj['fxdesc'],
            'fxfee' => $buffer['totalmoney'],
            'fxattch' => $fj['fxattch'],
            'fxtime' => $buffer['paytime'],
            'fxsign' => md5($buffer['status'] . $fxid . $fxddh . $buffer['totalmoney'] . $fxkey)
        );
        //订单状态+商务号+商户订单号+支付金额+商户秘钥

        return [1,
            $data];
    }

     public function alijump($request) {
        $url = $request['url'];
        if (strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')!== false ) {
           return [1,
            $url];
        }else{
            exit('<script>location.href="' . $url . '";</script>');
        }
        exit();
        
    }


}
