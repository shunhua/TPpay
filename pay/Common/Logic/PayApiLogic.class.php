<?php
// +----------------------------------------------------------------------
// | easy pay [ pay to easy ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 All rights reserved.
// +----------------------------------------------------------------------
// | Author: fengxing <QQ:51125330>
// +----------------------------------------------------------------------
namespace Common\Logic;

class PayApiLogic extends BaseLogic {

    /**
     * 总接口
     * $request = array(
      'id' => $jkpz['jzid'],
      'peizhi' => unserialize($jkpz['params']),
      'style' => $jkpz['style'],
      'fxddh' => $fxid . $fxddh,
      'fxdesc' => $fxdesc,
      'fxfee' => $fxfee,
      'fxattch' => $fxattch,
      'fxnotifyurl' => $fxnotifyurl,
      'fxbackurl' => $fxbackurl,
      'fxpay' => $fxpay,
      'fxip' => $fxip
     * 'isjsapi'=>0 或1 是否调用jsapi
      );
     */
    public function index($request) {
        //调用接口
        $style = $request['style'];
        return $this->$style($request);
    }
// ---------------------------------------兴业银行bsh--------------------------------------------------------
    protected function xingye($request) {
        $types = $request['fxpay'];//支付类型
        include_once COMMON_PATH . "/Tool/xingye/Utils.class.php";//辅助函数
        include_once COMMON_PATH . "/Tool/xingye/config/config.php";//常用配置
        include_once COMMON_PATH . "/Tool/xingye/class/RequestHandler.class.php";//请求类
        include_once COMMON_PATH . "/Tool/xingye/class/ClientResponseHandler.class.php";//应答类
        include_once COMMON_PATH . "/Tool/xingye/class/PayHttpClient.class.php";//中间支付类
        $resHandler = $reqHandler =$pay = $cfg = null;
        #实例化
        $resHandler = new \ClientResponseHandler();
        $reqHandler = new \RequestHandler();
        $pay = new \PayHttpClient();
        $cfg = new \Config();
        $utils = new \Utils();
        $mch_config=$cfg->dataPollingInterval($cfg->RandomQuotes);//轮询的商户配置
       
        $reqHandler->setGateUrl($cfg->C('url'));
       // $reqHandler->setKey($request['peizhi']['xingye_key']);
        $reqHandler->setKey($mch_config['key']);//后期轮询处理商户号、密钥
        #组装参数
        //$reqHandler->setReqParams($request,array('method'));
        $reqHandler->setParameter('out_trade_no',$request['fxddh']);//商户订单号
        $reqHandler->setParameter('body',$request['fxdesc']);//商品描述
        $reqHandler->setParameter('total_fee',$request['fxfee'] * 100);//总金额，以分为单位，不允许包含任何字、符号
        $reqHandler->setParameter('mch_create_ip',$request['fxip']);//订单生成的机器 IP
        $reqHandler->setParameter('service',$types);//接口类型
        //$reqHandler->setParameter('mch_id',$request['peizhi']['xingye_mchid']);//必填项，商户号，由平台分配
        $reqHandler->setParameter('mch_id',$mch_config['appid']);//后期轮询处理商户号、密钥
        $reqHandler->setParameter('notify_url', $request['peizhi']['xingye_notify']);//异步通知地址";//本站回调
        $reqHandler->setParameter('callback_url',$request['fxbackurl']);//同步通知地址
        $reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
        $reqHandler->createSign();//创建签名 
        
        //$utils->xywriteLog("请求参数: ".var_export($reqHandler->getAllParameters(),true));
        //写入支付日志
        $this->paylog('调用兴业接口', '请求参数',serialize($reqHandler->getAllParameters()));
        $xml = $utils->toXml($reqHandler->getAllParameters());
        $pay->setReqContent($reqHandler->getGateURL(),$xml);
        if($pay->call()){
            $resHandler->setContent($pay->getResContent());
            $resHandler->setKey($reqHandler->getKey());
            if($resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才表示成功
                if($resHandler->getParameter('status') == 0 && $resHandler->getParameter('result_code') == 0){
                    // 支付宝扫码
                    if ($types=='pay.weixin.native'  || $types=='pay.unionpay.native') {
                        $pay_info = $resHandler->getParameter('code_url');
                        //$pay_info = 'http://pan.baidu.com/share/qrcode?w=150&h=150&url=' . $resHandler->getParameter('code_url');
                        //$pay_info = 'http://' . $_SERVER['HTTP_HOST'] . '/Pay/scanpay?url=' . $resHandler->getParameter('code_url');
                        //$pay_info = 'http://' . $_SERVER['HTTP_HOST'] . '/Pay/scanpay?url=' . $resHandler->getParameter('code_url');
                    }elseif ($types=='pay.alipay.native') {
                        $pay_info = 'http://' . $_SERVER['HTTP_HOST'] . '/Pay/alijump?url=' . $resHandler->getParameter('code_url');
                    }else{
                        //$pay_info = urlencode($resHandler->getParameter('pay_info'));
                        $pay_info = 'http://' . $_SERVER['HTTP_HOST'] . '/Pay/go?u=' . urlencode($resHandler->getParameter('pay_info'));
                    } 
                    if (!empty($pay_info)) {
                        return array(
                                1,
                                $pay_info
                        );
                        exit();
                    }else{
                        return array(
                                0,
                            $resHandler->getParameter('err_code'),
                            $resHandler->getParameter('err_msg')
                        );
                        exit();
                    }
                    
                }else{
                    return array(
                                0,
                            $resHandler->getParameter('err_code'),
                            $resHandler->getParameter('err_msg')
                    );
                    exit();
                }
            }else{

                return array(
                    0,
                    $resHandler->getParameter('err_code'),
                    $resHandler->getParameter('err_msg')
                );
            }
            
        }else{ 
            return array(
                    0,
                    'Error Code:call http err or 商户未开通  or 此支付通道有问题'
                    //$pay->getErrInfo()
            );
        }
 
        
    }
//----------------------------------------兴业银行--------------------------------------------------------
    //----------------------------------------星支付 start------------------------------------------------------------- 
    protected function star($request) {
        $order_data = array(
            "user_id" => $request['peizhi']['star_mchid'],
            "amount"=> $request['fxfee']*100,
            "order_sn"=> $request['fxddh'],
            "notify_url"=>$request['peizhi']['star_notify'],
            "back_url"=> $request['fxbackurl'],
        );
        $order_data['sign'] = md5($order_data['user_id'].$order_data['order_sn'].$order_data['amount'].$order_data['notify_url'].$request['peizhi']['star_key']);
        $url='http://blue.sls4741.store/api/pay';
        $r = curl($url, $order_data);
        $backr = json_decode($r, true); //json转数组
        if (isset($backr['code']) || $backr['code']=='0001') {
            return array(
                    0,
                $backr['code'],
                $backr['message']
            );
        }
        return array(
                1,
                $backr['url']
          );
        exit();
        
    }

    //----------------------------------------拼多多 start------------------------------------------------------------- 
    public function pdd($request) {
        $dataArr = array(
            'mc_id'          => $request['peizhi']['pdd_mchid'],
            'orderid'        => $request['fxddh'],
            'money'          => $request['fxfee'],
            'type'           => ($request['fxpay']=='alipay') ? 1 : 2,
            'notify'         => $request['peizhi']['pdd_notify'],
            'callback'   => $request['fxbackurl'],
        );
        $str ="callback=".$dataArr["callback"]."&mc_id=".$dataArr["mc_id"]."&money=".$dataArr["money"]."&notify=".$dataArr["notify"]."&orderid=".$dataArr["orderid"]."&type=".$dataArr["type"]."&key=".$request["peizhi"]["pdd_key"];
        $dataArr['sign'] =md5($str);
        $payUrl='http://47.103.87.31/gateway/api/pay';
        $res = $this->m_curl($payUrl,$dataArr);
        $data = json_decode($res,true);
        if (!isset($data['payurl']) || $data['code']!=0) {
            return array(
                    0,
                $data['code'],
                $data['msg']
            );
        }
        return array(
                1,
                $data['payurl']
          );
        exit();
        
    }

    public function buff($request) {
        $dataArr = array(
            'mc_id'          => $request['peizhi']['buff_mchid'],
            'orderid'        => $request['fxddh'],
            'money'          => $request['fxfee'],
            'type'           => ($request['fxpay']=='alih5') ? 1 : 2,
            'notify'         => $request['peizhi']['buff_notify'],
            'callback'   => $request['fxbackurl'],
        );
        $str ="callback=".$dataArr["callback"]."&mc_id=".$dataArr["mc_id"]."&money=".$dataArr["money"]."&notify=".$dataArr["notify"]."&orderid=".$dataArr["orderid"]."&type=".$dataArr["type"]."&key=".$request["peizhi"]["buff_key"];
        $dataArr['sign'] =md5($str);
        $payUrl='http://103.45.150.215/gateway/api/buffpay';
        $res = $this->m_curl($payUrl,$dataArr);
        $data = json_decode($res,true);
        if (!isset($data['payurl']) || $data['code']!=0) {
            return array(
                    0,
                $data['code'],
                $data['msg']
            );
        }
        return array(
                1,
                $data['payurl']
          );
        exit();
        
    }

    private function m_curl($url,$data = array())
    {
        //使用crul模拟
        $ch = curl_init();
        //禁用https
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        //允许请求以文件流的形式返回
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch); //执行发送
        curl_close($ch);

        return $result;
    }
//----------------------------------------开联通 start------------------------------------------------------------- 
    protected function hyt($request) {
        $Md5key = $request['peizhi']['hyt_key'];
        $native = array(
            "pay_memberid" => $request['peizhi']['hyt_mchid'],
            "pay_orderid" => $request['fxddh'],
            "pay_amount" => $request['fxfee'],
            "pay_applydate" => date("Y-m-d H:i:s"),
            "pay_bankcode" => $request['fxpay'],
            "pay_notifyurl" => $request['peizhi']['hyt_notify'],
            "pay_callbackurl" => $request['fxbackurl'],
        );
        ksort($native);
        $md5str = "";
        foreach ($native as $key => $val) {
            $md5str = $md5str . $key . "=" . $val . "&";
        }
        $sign = strtoupper(md5($md5str . "key=" . $Md5key));
        $native["pay_md5sign"] = $sign;
        $native['pay_attach'] = "1234|456";
        $native['pay_productname'] ='团购商品';
        //写入支付日志
        // $this->paylog('调用开联通接口', '请求参数',serialize($order_data));
        $data = array(
            'transactionid' => $request['fxddh'],
            'notifyurl' => $request['fxnotifyurl'],
            'params' => serialize($native)
        );
        $pay_info = 'http://' . $_SERVER['HTTP_HOST'] . '/Pay/hytjump?transactionid=' . $data['transactionid'];

        SM('Yinlian')->insertData($data);
        
        return array(
                1,
                $pay_info
          );
        exit();

        
    }
    //签名前数组转字符串
    public function arrayToString($data) {
        ksort($data);
        $query = '';
        foreach ($data as $key => $value) {
            if ($key=='sign' || $key == 'signMsg' || $value === '' || $value === null ) {
                continue;
            }
            $query .= $key."=".$value."&";
        }
        return substr($query, 0, -1);
    }

//----------------------------------------开联通 end------------------------------------------------------------- 


// ---------------------------------------银联悦单bsh--------------------------------------------------------
    protected function yuedan($request) {

        $types = $request['fxpay'];//支付类型
        include_once COMMON_PATH . "/Tool/yuedan/config/config.php";//常用配置
        $cfg = null;
        #实例化
        $cfg = new \Config();
        if ($request['fxfee']>=5000) {
            return array(
                0,
                'INVALID_AMOUNT',
                '温馨提示:系统限制单笔金额必须小于等于4999元'
            );
            exit();
        }
        $mchs_config=$cfg->dataPollingInterval($cfg->RandomQuotes);//轮询的商户配置
        $order_data = array(
            "systemId" => "1001", //商户订单号
            "msgType" => $cfg->Con('msgType'), //商品名
            "counterNo" => "2424", //支付金额 单位元
            "requestTimestamp" => date("Y-m-d H:i:s"), //附加信息
            "notifyUrl" => $request['peizhi']['yuedan_notify'],//异步回调 , 支付结果以异步为准
            "billNo" => $cfg->Con('msgSrcId').$request['fxddh'],
            "billDesc" => "在线支付", 
            "msgSrc" => $cfg->Con('msgSrc'),
            "mid" => $mchs_config['appid'],//$request['peizhi']['yuedan_mchid'],
            "msgId" => "800000000010",
            "billDate" => date("Y-m-d"),
            "tid" =>$mchs_config['tid'],//$cfg->Con('tid'),
            "instMid" => $cfg->Con('instMid'),
            "totalAmount" =>$request['fxfee']*100,
        );
         
        #签名 
        $order_data["sign"] = $cfg->getSign($order_data); //加密
         
        //写入支付日志
        $this->paylog('调用悦单接口', '请求参数',serialize($order_data));
        list($returnCode, $returnContent) = $cfg->http_post_json($cfg->Con('url'), json_encode($order_data));
        $returnContent = json_decode($returnContent, true); //json转数组
       // echo "<pre>";
       // print_r($returnContent);exit();
        if (!isset($returnContent['billQRCode']) || empty($returnContent['billQRCode']) || $returnContent["errCode"] != 'SUCCESS') {
            return array(
                    0,
                $returnContent["errCode"],
                $returnContent["errMsg"]
            );
            exit();
        }else{
           $pay_info=$returnContent['billQRCode'];
           return array(
                    1,
                    $pay_info
            );
            exit();
        }
        
    }
//----------------------------------------银联悦单 end-------------------------------------------------------- 

// ---------------------------------------银联H5--------------------------------------------------------
    protected function umsh5($request) {
        include_once COMMON_PATH . "/Tool/umsh5/config/config.php";//常用配置
        $cfgh5 = null;
        #实例化
        $cfgh5 = new \Config();
        if ($request['fxpay']=='WXPay.jsPay') {
            include_once COMMON_PATH . "/Tool/umsh5/config/payjs.php";//常用配置
            $cfgh5 = null;
            #实例化
            $cfgh5 = new \Payjs();
        }
        $mchs_config=$cfgh5->dataPollingInterval($cfgh5->RandomQuotes);//轮询的商户配置
        $order_data = array(
            "mid" => $mchs_config['appid'],//$request['peizhi']['yuedan_mchid'],
            "tid" =>$mchs_config['tid'],//$cfg->Con('tid'),
            "msgType" => $request['fxpay'], //商品名
            "msgSrc" => $cfgh5->Con('msgSrc'),
            "instMid" => $cfgh5->Con('instMid'),
            "merOrderId" => $cfgh5->Con('msgSrcId').$request['fxddh'],
            "totalAmount" =>$request['fxfee']*100,
            "requestTimestamp" => date("Y-m-d H:i:s"), //附加信息
            "notifyUrl" => $request['peizhi']['umsh5_notify'],//异步回调 , 支付结果以异步为准
           // "goods"=>'[{"body": "弘海贸易","price": "1","goodsName": "弘海贸易","goodsId": "1","quantity": "1","goodsCategory":"TEST"}]',
            //"secureTransaction" => true, //是否保单
            // "sceneType" => "AND_WAP",
            // "merAppName" => "全民付", 
            // "merAppId" => "http://www.chinaums.com",  
        );

        #签名 
        $order_data["sign"] = $cfgh5->getSign($order_data); //加密

        //写入支付日志
        //$this->paylog('调用h5接口', '请求参数',serialize($order_data));
        $url=$cfgh5->Con('url'). '?'.http_build_query($order_data);
        $pay_info = 'http://' . $_SERVER['HTTP_HOST'] . '/Pay/alijump?url='.urlencode($url);
        if ($request['fxpay']=='WXPay.jsPay' || $request['fxpay']=='uac.order') {
           $pay_info = $url;
        }
        return array(
                1,
                $pay_info
          );
        exit();
        
    }
//----------------------------------------银联H5 end-------------------------------------------------------- 

    // ------------------------------------银联电子 start------------------------------------------------------
    protected function yinlian($request) {
        define(transResvered, "trans_");
        define(cardResvered, "card_");
        define(transResveredKey, "TranReserved");
        define(signatureField, "Signature");
        include_once COMMON_PATH . "/Tool/yinlian/util/common.php";//常用配置
        include_once COMMON_PATH . "/Tool/yinlian/util/SecssUtil.class.php";//常用配置

        $order_data = array(
            "MerId" => $request['peizhi']['yinlian_mchid'], //商户订单号
            "MerOrderNo" => $request['fxddh'], //商品名
            "OrderAmt" => $request['fxfee']*100, //支付金额 单位元
            "TranDate" => date('Ymd'), //附加信息
            "TranTime" => date('Hms'),
            "BusiType" => "0001", 
            "TranType" => $request['fxpay'],
            "Version" => '20140728',
            "MerPageUrl" => $request['fxbackurl'],
            "MerBgUrl" => $request['peizhi']['yinlian_notify'],//异步回调 , 支付结果以异步为准
        );

        $transResvedJson = array();
        $cardInfoJson = array();
        $sendMap = array();
        foreach ($order_data as $key => $value) {
            if (isEmpty($value)) {
                continue;
            }
            if (startWith($key, transResvered)) {
                // 组装交易扩展域
                $key = substr($key, strlen(transResvered));
                $transResvedJson[$key] = $value;
            } else 
                if (startWith($key, cardResvered)) {
                    // 组装有卡交易信息域
                    $key = substr($key, strlen(cardResvered));
                    $cardInfoJson[$key] = $value;
                } else {
                    $sendMap[$key] = $value;
                }
        }

        $transResvedStr = null;
        $cardResvedStr = null;
        if (count($transResvedJson) > 0) {
            $transResvedStr = json_encode($transResvedJson);
        }
        if (count($cardInfoJson) > 0) {
            $cardResvedStr = json_encode($cardInfoJson);
        }
        
        $secssUtil = new \SecssUtil();
        
        if (! isEmpty($transResvedStr)) {
            $transResvedStr = $secssUtil->decryptData($transResvedStr);
            $sendMap[transResveredKey] = $transResvedStr;
        }
        if (! isEmpty($cardResvedStr)) {
            $cardResvedStr = $secssUtil->decryptData($cardResvedStr);
            $sendMap[cardResveredKey] = $cardResvedStr;
        }
       
        $securityPropFile = COMMON_PATH . "Tool/yinlian/config/security.properties";
        
        $secssUtil->init($securityPropFile);
        $secssUtil->sign($sendMap);
        $order_data[signatureField] = $secssUtil->getSign();

        
        //写入支付日志
        //$this->paylog('调用银联接口', '请求参数',serialize($order_data));
        //写入记录交易号
        $data = array(
            'transactionid' => $request['fxddh'],
            'notifyurl' => $request['fxnotifyurl'],
            'params' => serialize($order_data)
        );
        $pay_info = 'http://' . $_SERVER['HTTP_HOST'] . '/Pay/ylup?transactionid=' . $data['transactionid'];

        SM('Yinlian')->insertData($data);
        
        return array(
                1,
                $pay_info
          );
        exit();
        
    }
    // -------------------------------------------------------end-------------------------------------------------------------
    /**
     * 微信接口
     */
    protected function wechat($request) {
        $types = 'MWEB';
        switch ($request['fxpay']) {
            case 'wxwap':
                $types = 'MWEB';
                break;
            case 'wxgzh':
                $types = 'JSAPI';
                break;
            case 'wxsm':
                $types = 'NATIVE';
                break;
        }

        if ($types == "JSAPI" && empty($request['isjsapi'])) {
            return [1,"http://" . $_SERVER['HTTP_HOST'] . "/Pay/jsapi?style=wechat&id=" . $request['id'] . '&ddh=' . $request['fxddh'] . '&t=' . time()];
            //return [1,"http://" . $_SERVER['HTTP_HOST'] . "/Index/Pay/jsapi/style/wechat/id/" . $request['id'] . '/ddh/' . $request['fxddh'] . '/t/' . time()];
        }

        include_once COMMON_PATH . "/Tool/wechat/lib/WxPay.Api.php";
        if ($types == 'JSAPI') {
            include_once COMMON_PATH . "/Tool/wechat/WxPay.JsApiPay.php";
            $tools = new \JsApiPay();
            $openId = $tools->GetOpenid($request['peizhi']['wechat_appid'],$request['peizhi']['wechat_appsecret']);
        } else {
        }
        include_once COMMON_PATH . "/Tool/wechat/WxPay.NativePay.php";

        \WxPayConfig::setAPPID($request['peizhi']['wechat_appid']);
        \WxPayConfig::setMCHID($request['peizhi']['wechat_mchid']);
        \WxPayConfig::setKEY($request['peizhi']['wechat_key']);
        \WxPayConfig::setAPPSECRET($request['peizhi']['wechat_appsecret']);

        $input = new \WxPayUnifiedOrder();
        $input->SetBody((string) $request['fxdesc']);
        $input->SetAttach((string) $request['fxattch']);
        $input->SetOut_trade_no((string) $request['fxddh']);
        $input->SetTotal_fee($request['fxfee'] * 100);
        $input->SetNotify_url("http://" . $_SERVER['HTTP_HOST'] . "/Pay/notify_" . $request['style']);
        $input->SetTrade_type((string) $types);
        if ($types == 'MWEB')
            $input->SetSpbill_create_ip((string) $request['fxip']);
        else {
            $input->SetTime_start((string) date("YmdHis"));
            $input->SetTime_expire((string) (date("YmdHis", time() + 600)));
            $input->SetGoods_tag("");

            if ($types == 'NATIVE') {
                $input->SetSpbill_create_ip((string) get_client_ip(0, true));
                $input->SetProduct_id((string) $request['fxddh']);
            }

            if ($types == 'JSAPI') {
                $input->SetOpenid($openId);
            }
        }

        $notify = new \NativePay();
        $result = $notify->GetPayUrl($input);

        if ($types == 'JSAPI') {
            $jsApiParameters = $tools->GetJsApiParameters($result);
            $editAddress = $tools->GetEditAddressParameters();
            return array(
                1,
                array(
                    'jsApiParameters' => $jsApiParameters,
                    'editAddress' => $editAddress));
        }

        if ($result["return_msg"] != 'OK') {
            return array(
                0,
                $result["return_msg"]);
        }
        if ($types == "NATIVE")
            $result = $result["code_url"];
        else
            $result = 'http://' . $_SERVER['HTTP_HOST'] . '/Pay/go?u=' . urlencode($result["mweb_url"]);

        return array(
            1,
            $result);
    }

    /**
     * 支付宝接口
     */
    protected function alipay($request) {
        switch ($request['fxpay']) {
            case 'zfbwap':
                //调用接口
                include_once COMMON_PATH . "/Tool/alipay/wappay/service/AlipayTradeService.php";
                include_once COMMON_PATH . "/Tool/alipay/wappay/buildermodel/AlipayTradeWapPayContentBuilder.php";
                include_once COMMON_PATH . "/Tool/alipay/config.php";
                // 轮询
                include_once COMMON_PATH . "/Tool/alipay/config/config.php";//常用配置
                $cfg = null;
                #实例化
                $cfg = new \Config();
                $RandomQuotes = SM('Alipay')->selectData('appid,private,public', 'status=0', 'id asc');
                $mch_config=$cfg->dataPollingInterval($RandomQuotes);//轮询的商户配置-数据表
                //$mch_config=$cfg->dataPollingInterval($cfg->RandomQuotes);//轮询的商户配置
                $config['app_id'] = $mch_config['appid'];
                $config['merchant_private_key'] = $mch_config['private'];
                $config['alipay_public_key'] = $mch_config['public'];

                // $config['app_id'] = $request['peizhi']['alipay_appid'];
                // $config['merchant_private_key'] = $request['peizhi']['alipay_private'];
                // $config['alipay_public_key'] = $request['peizhi']['alipay_public'];
                $config['sign_type'] = $request['peizhi']['alipay_sign'];
                $config['notify_url'] = $request['fxnotifyurl'];
                $config['return_url'] = $request['fxbackurl'];

                // $subject = $request['fxdesc'];
                // if ($subject)
                $subject = '商品订单-'.$request['fxddh'];
                
                $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
                $payRequestBuilder->setBody($request['fxattch']); //商品描述，可空
                $payRequestBuilder->setSubject($subject); //订单名称，必填
                $payRequestBuilder->setOutTradeNo($request['fxddh']); //商户订单号，商户网站订单系统中唯一订单号，必填
                $payRequestBuilder->setTotalAmount($request['fxfee']); //付款金额，必填
                $payRequestBuilder->setTimeExpress("1m"); //超时时间

                $payResponse = new \AlipayTradeService($config);
                $result = $payResponse->wapPay($payRequestBuilder, $request['fxbackurl'], $request['fxnotifyurl']);
                return [1,'http://' . $_SERVER['HTTP_HOST'] . '/Pay/alijump?url='.urlencode($result)];
                break;
            case 'zfbsm':
                include COMMON_PATH . "/Tool/alipay/config.php";
                include_once COMMON_PATH . "/Tool/alipay/f2fpay/service/AlipayTradeService.php";
                include_once COMMON_PATH . "/Tool/alipay/f2fpay/model/builder/AlipayTradePrecreateContentBuilder.php";
                 // 轮询
                include_once COMMON_PATH . "/Tool/alipay/config/config.php";//常用配置
                $cfg = null;
                #实例化
                $cfg = new \Config();
                $mch_config=$cfg->dataPollingInterval($cfg->RandomQuotes);//轮询的商户配置
                $config['app_id'] = $mch_config['appid'];
                $config['merchant_private_key'] = $mch_config['private'];
                $config['alipay_public_key'] = $mch_config['public'];

                // $config['app_id'] = $request['peizhi']['alipay_appid'];
                // $config['merchant_private_key'] = $request['peizhi']['alipay_private'];
                // $config['alipay_public_key'] = $request['peizhi']['alipay_public'];
                $config['sign_type'] = $request['peizhi']['alipay_sign'];
                $config['notify_url'] = $request['fxnotifyurl'];
                $config['return_url'] = $request['fxbackurl'];
                $config['QueryDuration'] = '5m';
                $config['MaxQueryRetry'] = '5';
                // (必填) 商户网站订单系统中唯一订单号，64个字符以内，只能包含字母、数字、下划线，
                // 需保证商户系统端不能重复，建议通过数据库sequence生成，
                //$outTradeNo = "qrpay".date('Ymdhis').mt_rand(100,1000);
                $outTradeNo = $request['fxddh'];

                // (必填) 订单标题，粗略描述用户的支付目的。如“xxx品牌xxx门店当面付扫码消费”
                $subject=$request['fxdesc'];
                if (!$subject)  $subject = '商品订单-'.$request['fxddh'];

                // (必填) 订单总金额，单位为元，不能超过1亿元
                // 如果同时传入了【打折金额】,【不可打折金额】,【订单总金额】三者,则必须满足如下条件:【订单总金额】=【打折金额】+【不可打折金额】
                $totalAmount = $request['fxfee'];


                // (不推荐使用) 订单可打折金额，可以配合商家平台配置折扣活动，如果订单部分商品参与打折，可以将部分商品总价填写至此字段，默认全部商品可打折
                // 如果该值未传入,但传入了【订单总金额】,【不可打折金额】 则该值默认为【订单总金额】- 【不可打折金额】
                //String discountableAmount = "1.00"; //
                // (可选) 订单不可打折金额，可以配合商家平台配置折扣活动，如果酒水不参与打折，则将对应金额填写至此字段
                // 如果该值未传入,但传入了【订单总金额】,【打折金额】,则该值默认为【订单总金额】-【打折金额】
                //$undiscountableAmount = "0.01";
                // 卖家支付宝账号ID，用于支持一个签约账号下支持打款到不同的收款账号，(打款到sellerId对应的支付宝账号)
                // 如果该字段为空，则默认为与支付宝签约的商户的PID，也就是appid对应的PID
                //$sellerId = "";
                // 订单描述，可以对交易或商品进行一个详细地描述，比如填写"购买商品2件共15.00元"
                $body = $request['fxattch'];

                //商户操作员编号，添加此参数可以为商户操作员做销售统计
                //$operatorId = "test_operator_id";
                // (可选) 商户门店编号，通过门店号和商家后台可以配置精准到门店的折扣信息，详询支付宝技术支持
                //$storeId = "test_store_id";
                // 支付宝的店铺编号
                //$alipayStoreId= "test_alipay_store_id";
                // 业务扩展参数，目前可添加由支付宝分配的系统商编号(通过setSysServiceProviderId方法)，系统商开发使用,详情请咨询支付宝技术支持
                //$providerId = ""; //系统商pid,作为系统商返佣数据提取的依据
                //$extendParams = new ExtendParams();
                //$extendParams->setSysServiceProviderId($providerId);
                //$extendParamsArr = $extendParams->getExtendParams();
                // 支付超时，线下扫码交易定义为5分钟
                $timeExpress = "5m";
                /*
                  // 商品明细列表，需填写购买商品详细信息，
                  $goodsDetailList = array();

                  // 创建一个商品信息，参数含义分别为商品id（使用国标）、名称、单价（单位为分）、数量，如果需要添加商品类别，详见GoodsDetail
                  $goods1 = new GoodsDetail();
                  $goods1->setGoodsId("apple-01");
                  $goods1->setGoodsName("iphone");
                  $goods1->setPrice(3000);
                  $goods1->setQuantity(1);
                  //得到商品1明细数组
                  $goods1Arr = $goods1->getGoodsDetail();

                  // 继续创建并添加第一条商品信息，用户购买的产品为“xx牙刷”，单价为5.05元，购买了两件
                  $goods2 = new GoodsDetail();
                  $goods2->setGoodsId("apple-02");
                  $goods2->setGoodsName("ipad");
                  $goods2->setPrice(1000);
                  $goods2->setQuantity(1);
                  //得到商品1明细数组
                  $goods2Arr = $goods2->getGoodsDetail();

                  $goodsDetailList = array($goods1Arr,$goods2Arr);
                 */
                //第三方应用授权令牌,商户授权系统商开发模式下使用
                //$appAuthToken = "";//根据真实值填写
                // 创建请求builder，设置请求参数
                $qrPayRequestBuilder = new \AlipayTradePrecreateContentBuilder();
                $qrPayRequestBuilder->setOutTradeNo($outTradeNo);
                $qrPayRequestBuilder->setTotalAmount($totalAmount);
                $qrPayRequestBuilder->setTimeExpress($timeExpress);
                $qrPayRequestBuilder->setSubject($subject);
                $qrPayRequestBuilder->setBody($body);
                //$qrPayRequestBuilder->setUndiscountableAmount($undiscountableAmount);
                //$qrPayRequestBuilder->setExtendParams($extendParamsArr);
                //$qrPayRequestBuilder->setGoodsDetailList($goodsDetailList);
                //$qrPayRequestBuilder->setStoreId($storeId);
                //$qrPayRequestBuilder->setOperatorId($operatorId);
                //$qrPayRequestBuilder->setAlipayStoreId($alipayStoreId);
                //$qrPayRequestBuilder->setAppAuthToken($appAuthToken);
                // 调用qrPay方法获取当面付应答

                $qrPay = new \AlipayTradeServicef2f($config);
                $qrPayResult = $qrPay->qrPay($qrPayRequestBuilder);
                
                //根据状态值进行业务处理
                switch ($qrPayResult->getTradeStatus()) {
                    case "SUCCESS":
                        //echo "支付宝创建订单二维码成功:"."<br>---------------------------------------<br>";
                        $response = $qrPayResult->getResponse();
                        return [1,$response->qr_code];
                        // return [1,'http://pan.baidu.com/share/qrcode?w=150&h=150&url=' .$response->qr_code];
                        //print_r($response);
                        break;
                    case "FAILED":
                        return [0,"支付宝创建订单二维码失败!!!"];
                        //if(!empty($qrPayResult->getResponse())){
                        //print_r($qrPayResult->getResponse());
                        //}
                        break;
                    case "UNKNOWN":
                        return [0,"系统异常，状态未知!!!"];
                        //if(!empty($qrPayResult->getResponse())){
                        //print_r($qrPayResult->getResponse());
                        //}
                        break;
                    default:
                        return [0,"不支持的返回状态，创建订单二维码返回异常!!!"];
                        break;
                }
                break;
        }
    }


    // 
    /**
     * 支付宝接口
     */
    protected function zhifubao($request) {
        switch ($request['fxpay']) {
            case 'zfb':
                //调用接口
                include_once COMMON_PATH . "/Tool/alipay/wappay/service/AlipayTradeService.php";
                include_once COMMON_PATH . "/Tool/alipay/wappay/buildermodel/AlipayTradeWapPayContentBuilder.php";
                // 轮询
                include_once COMMON_PATH . "/Tool/zhifubao/config/config.php";//常用配置
                $cfg = null;
                #实例化
                $cfg = new \Config();
                $mch_config=$cfg->dataPollingInterval($cfg->RandomQuotes);//轮询的商户配置
                $config['app_id'] = $mch_config['appid'];
                $config['merchant_private_key'] = $mch_config['private'];
                $config['alipay_public_key'] = $mch_config['public'];

                // $config['app_id'] = $request['peizhi']['alipay_appid'];
                // $config['merchant_private_key'] = $request['peizhi']['alipay_private'];
                // $config['alipay_public_key'] = $request['peizhi']['alipay_public'];
                $config['sign_type'] = $request['peizhi']['zhifubao_sign'];
                $config['notify_url'] = $request['peizhi']['zhifubao_notify'];
                $config['return_url'] = $request['fxbackurl'];

                // echo "<pre>";
                // print_r($config);exit();
                // $subject = $request['fxdesc'];
                // if ($subject)
                $subject = '商品订单-'.$request['fxddh'];
                
                $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
                $payRequestBuilder->setBody($request['fxattch']); //商品描述，可空
                $payRequestBuilder->setSubject($subject); //订单名称，必填
                $payRequestBuilder->setOutTradeNo($request['fxddh']); //商户订单号，商户网站订单系统中唯一订单号，必填
                $payRequestBuilder->setTotalAmount($request['fxfee']); //付款金额，必填
                $payRequestBuilder->setTimeExpress("1m"); //超时时间

                $payResponse = new \AlipayTradeService($config);
                $result = $payResponse->wapPay($payRequestBuilder, $request['fxbackurl'], $config['notify_url']);
                return [1,'http://' . $_SERVER['HTTP_HOST'] . '/Pay/alijump?url='.urlencode($result)];
                break;
            case 'zfbsaoma':
                include COMMON_PATH . "/Tool/alipay/config.php";
                include_once COMMON_PATH . "/Tool/alipay/f2fpay/service/AlipayTradeService.php";
                include_once COMMON_PATH . "/Tool/alipay/f2fpay/model/builder/AlipayTradePrecreateContentBuilder.php";
                 // 轮询
                include_once COMMON_PATH . "/Tool/alipay/config/config.php";//常用配置
                $cfg = null;
                #实例化
                $cfg = new \Config();
                $mch_config=$cfg->dataPollingInterval($cfg->RandomQuotes);//轮询的商户配置
                $config['app_id'] = $mch_config['appid'];
                $config['merchant_private_key'] = $mch_config['private'];
                $config['alipay_public_key'] = $mch_config['public'];

                // $config['app_id'] = $request['peizhi']['alipay_appid'];
                // $config['merchant_private_key'] = $request['peizhi']['alipay_private'];
                // $config['alipay_public_key'] = $request['peizhi']['alipay_public'];
                $config['sign_type'] = $request['peizhi']['alipay_sign'];
                $config['notify_url'] = $request['fxnotifyurl'];
                $config['return_url'] = $request['fxbackurl'];
                $config['QueryDuration'] = '5m';
                $config['MaxQueryRetry'] = '5';
                // (必填) 商户网站订单系统中唯一订单号，64个字符以内，只能包含字母、数字、下划线，
                // 需保证商户系统端不能重复，建议通过数据库sequence生成，
                //$outTradeNo = "qrpay".date('Ymdhis').mt_rand(100,1000);
                $outTradeNo = $request['fxddh'];

                // (必填) 订单标题，粗略描述用户的支付目的。如“xxx品牌xxx门店当面付扫码消费”
                $subject=$request['fxdesc'];
                if (!$subject)  $subject = '商品订单-'.$request['fxddh'];

                // (必填) 订单总金额，单位为元，不能超过1亿元
                // 如果同时传入了【打折金额】,【不可打折金额】,【订单总金额】三者,则必须满足如下条件:【订单总金额】=【打折金额】+【不可打折金额】
                $totalAmount = $request['fxfee'];


                // (不推荐使用) 订单可打折金额，可以配合商家平台配置折扣活动，如果订单部分商品参与打折，可以将部分商品总价填写至此字段，默认全部商品可打折
                // 如果该值未传入,但传入了【订单总金额】,【不可打折金额】 则该值默认为【订单总金额】- 【不可打折金额】
                //String discountableAmount = "1.00"; //
                // (可选) 订单不可打折金额，可以配合商家平台配置折扣活动，如果酒水不参与打折，则将对应金额填写至此字段
                // 如果该值未传入,但传入了【订单总金额】,【打折金额】,则该值默认为【订单总金额】-【打折金额】
                //$undiscountableAmount = "0.01";
                // 卖家支付宝账号ID，用于支持一个签约账号下支持打款到不同的收款账号，(打款到sellerId对应的支付宝账号)
                // 如果该字段为空，则默认为与支付宝签约的商户的PID，也就是appid对应的PID
                //$sellerId = "";
                // 订单描述，可以对交易或商品进行一个详细地描述，比如填写"购买商品2件共15.00元"
                $body = $request['fxattch'];

                //商户操作员编号，添加此参数可以为商户操作员做销售统计
                //$operatorId = "test_operator_id";
                // (可选) 商户门店编号，通过门店号和商家后台可以配置精准到门店的折扣信息，详询支付宝技术支持
                //$storeId = "test_store_id";
                // 支付宝的店铺编号
                //$alipayStoreId= "test_alipay_store_id";
                // 业务扩展参数，目前可添加由支付宝分配的系统商编号(通过setSysServiceProviderId方法)，系统商开发使用,详情请咨询支付宝技术支持
                //$providerId = ""; //系统商pid,作为系统商返佣数据提取的依据
                //$extendParams = new ExtendParams();
                //$extendParams->setSysServiceProviderId($providerId);
                //$extendParamsArr = $extendParams->getExtendParams();
                // 支付超时，线下扫码交易定义为5分钟
                $timeExpress = "5m";
                /*
                  // 商品明细列表，需填写购买商品详细信息，
                  $goodsDetailList = array();

                  // 创建一个商品信息，参数含义分别为商品id（使用国标）、名称、单价（单位为分）、数量，如果需要添加商品类别，详见GoodsDetail
                  $goods1 = new GoodsDetail();
                  $goods1->setGoodsId("apple-01");
                  $goods1->setGoodsName("iphone");
                  $goods1->setPrice(3000);
                  $goods1->setQuantity(1);
                  //得到商品1明细数组
                  $goods1Arr = $goods1->getGoodsDetail();

                  // 继续创建并添加第一条商品信息，用户购买的产品为“xx牙刷”，单价为5.05元，购买了两件
                  $goods2 = new GoodsDetail();
                  $goods2->setGoodsId("apple-02");
                  $goods2->setGoodsName("ipad");
                  $goods2->setPrice(1000);
                  $goods2->setQuantity(1);
                  //得到商品1明细数组
                  $goods2Arr = $goods2->getGoodsDetail();

                  $goodsDetailList = array($goods1Arr,$goods2Arr);
                 */
                //第三方应用授权令牌,商户授权系统商开发模式下使用
                //$appAuthToken = "";//根据真实值填写
                // 创建请求builder，设置请求参数
                $qrPayRequestBuilder = new \AlipayTradePrecreateContentBuilder();
                $qrPayRequestBuilder->setOutTradeNo($outTradeNo);
                $qrPayRequestBuilder->setTotalAmount($totalAmount);
                $qrPayRequestBuilder->setTimeExpress($timeExpress);
                $qrPayRequestBuilder->setSubject($subject);
                $qrPayRequestBuilder->setBody($body);
                //$qrPayRequestBuilder->setUndiscountableAmount($undiscountableAmount);
                //$qrPayRequestBuilder->setExtendParams($extendParamsArr);
                //$qrPayRequestBuilder->setGoodsDetailList($goodsDetailList);
                //$qrPayRequestBuilder->setStoreId($storeId);
                //$qrPayRequestBuilder->setOperatorId($operatorId);
                //$qrPayRequestBuilder->setAlipayStoreId($alipayStoreId);
                //$qrPayRequestBuilder->setAppAuthToken($appAuthToken);
                // 调用qrPay方法获取当面付应答

                $qrPay = new \AlipayTradeServicef2f($config);
                $qrPayResult = $qrPay->qrPay($qrPayRequestBuilder);
                
                //根据状态值进行业务处理
                switch ($qrPayResult->getTradeStatus()) {
                    case "SUCCESS":
                        //echo "支付宝创建订单二维码成功:"."<br>---------------------------------------<br>";
                        $response = $qrPayResult->getResponse();
                        return [1,$response->qr_code];
                        // return [1,'http://pan.baidu.com/share/qrcode?w=150&h=150&url=' .$response->qr_code];
                        //print_r($response);
                        break;
                    case "FAILED":
                        return [0,"支付宝创建订单二维码失败!!!"];
                        //if(!empty($qrPayResult->getResponse())){
                        //print_r($qrPayResult->getResponse());
                        //}
                        break;
                    case "UNKNOWN":
                        return [0,"系统异常，状态未知!!!"];
                        //if(!empty($qrPayResult->getResponse())){
                        //print_r($qrPayResult->getResponse());
                        //}
                        break;
                    default:
                        return [0,"不支持的返回状态，创建订单二维码返回异常!!!"];
                        break;
                }
                break;
        }
    }
}