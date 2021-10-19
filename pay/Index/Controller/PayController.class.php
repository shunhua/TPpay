<?php
// +----------------------------------------------------------------------
// | easy pay [ pay to easy ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 All rights reserved.
// +----------------------------------------------------------------------
// | Author: fengxing <QQ:51125330>
// +----------------------------------------------------------------------
namespace Index\Controller;

class PayController extends BaseController {

    //发起支付 查询订单
    public function index() {
        switch ($_REQUEST['fxaction']) {
            case 'orderquery':
                $buffer = SL('Pay/payQuery', $_REQUEST);
                if ($buffer[0] == 1) {
                    $reback = $buffer[1];
                } else {
                    $reback = array(
                        'fxstatus' => 0,
                        'error' => $buffer[1]);
                }
                break;
            default:
                $buffer = SL('Pay/payApi', $_REQUEST);
                $reback = array();
                if ($buffer[0] == 1) {
                    $reback = array(
                        'status' => 1,
                        'payurl' => $buffer[1]);
                    
                } else {
                    // if (empty($buffer[1])) {
                    //    $buffer[1]='Error Code:call http err or 商户未开通  or 此商户支付通道有问题';
                    // }
                    $reback = array(
                        'status' => 0,
                        'Error Code' => $buffer[1],
                        'Error Message' => $buffer[2]);
                }
                break;
        }
        $this->ajaxBack($reback);
    }

    //异步返回
    public function notify() {

        /* $back='{"appid":"wxd1155b98be1006c2","attach":"201710015074666982604","bank_type":"CFT","cash_fee":"100","fee_type":"CNY","is_subscribe":"N","mch_id":"1488975772","nonce_str":"klltl6memi6ceu5zp8mhsaq4c8f3bka5","openid":"oGkuH0dXTSyJiOTKvZAiRg0xndPc","out_trade_no":"201710015074666982604","result_code":"SUCCESS","return_code":"SUCCESS","sign":"38938CBD9DC55DB30F263AED2AA9310B","time_end":"20170926000142","total_fee":"100","trade_type":"MWEB","transaction_id":"4200000003201709264262461142"}';
          $json=  json_decode($back,true);
          $xml = "<xml>";
          foreach ($json as $key=>$val)
          {
          if (is_numeric($val) && gettype($val)!='string'){
          $xml.="<".$key.">".$val."</".$key.">";
          }else{
          $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
          }
          }
          $xml.="</xml>";
          $GLOBALS['HTTP_RAW_POST_DATA']=$xml; */
//        $arr=array(
//            'total_amount'=>1,
//            'buyer_id'=>'2088102116773037',
//            'body'=>'大乐透2.1',
//            'trade_no'=>'2016071921001003030200089909',
//            'refund_fee'=>'0.00',
//            'notify_time'=>'2016-07-19 14:10:49',
//            'subject'=>'大乐透2.1',
//            'sign_type'=>'RSA2',
//            'charset'=>'utf-8',
//            'notify_type'=>'trade_status_sync',
//            'out_trade_no'=>'201710015074676266665',
//            'gmt_close'=>'2016-07-19 14:10:46',
//            'gmt_payment'=>'2016-07-19 14:10:47',
//            'trade_status'=>'TRADE_SUCCESS',
//            'version'=>'1.0',
//            'sign'=>'ELAYeugH8LYFvxnNajOvZhuxNFbN2LhF0l/KL8ANtj8oyPM4NN7Qft2kWJTDJUpQOzCzNnV9hDxh5AaT9FPqRS6ZKxnzM=',
//            'gmt_create'=>'2016-07-19 14:10:44',
//            'app_id'=>'2015102700040153',
//            'seller_id'=>'2088102119685838',
//            'notify_id'=>'4a91b7a78a503640467525113fb7d8bg8e'
//            );
//        $_REQUEST=array_merge($_REQUEST,$arr);
        
         #兴业专属
        include_once COMMON_PATH . "/Tool/xingye/Utils.class.php";//辅助函数
        $utils = new \Utils();
        
        $action ='';  
        foreach ($_REQUEST as $i => $iBuffer) {
            if (strstr(strtolower($i), '/pay/notify')) {
                $action = str_replace('/pay/notify/', '', strtolower($i));
            }
        }
  //        $_REQUEST= array (
  // 'gmt_create' => '2018-07-25 12:08:50',
  // 'charset' => 'UTF-8',
  // 'seller_email' => '810900872@qq.com',
  // 'subject' => 'alipay wap',
  // 'sign' => 'o/OFPdS569bbJ+y3EJ4RWSeyxYVAk5n57Q/noDI+OnslU0YjfTkHw9pP6YteaMoHXPTWbtYZNgsZL5f6kc+gX/fK/eTcKSl4Brdt20mTukNU4msCfY4p1gM3IBYdE0eS8Bv4mX7lr1hX+V0cWrIDB7vOnihaYyDwLLQ7eGW8IqEzlhjinsK2Eh1mFfUkX/WkKZ7U1ori48B4SpykwSUcYprRwpZl1eChLgXrgUsVhETU9HA3JSHe18D0M0DMQU58WfAgr5+5+Ns9EYLKIXolS7f9Yk5kRjWrBPd3QZjO3hnScP67rMh2sHqEqasJYD3sdR1S68cX2pSkQnt4y6EVnw==',
  // 'body' => 'mytest',
  // 'buyer_id' => '2088702821120621',
  // 'invoice_amount' => '0.01',
  // 'notify_id' => 'c401570d9dbb5be1625321302f8986dksd',
  // 'fund_bill_list' => '[{\\"amount\\":\\"0.01\\",\\"fundChannel\\":\\"ALIPAYACCOUNT\\"}]',
  // 'notify_type' => 'trade_status_sync',
  // 'trade_status' => 'TRADE_SUCCESS',
  // 'receipt_amount' => '0.01',
  // 'buyer_pay_amount' => '0.01',
  // 'app_id' => '2018072060686594',
  // 'sign_type' => 'RSA2',
  // 'seller_id' => '2088231040906238',
  // 'gmt_payment' => '2018-07-25 12:08:51',
  // 'notify_time' => '2018-07-25 12:08:51',
  // 'version' => '1.0',
  // 'out_trade_no' => '2017100I725917206239653',
  // 'total_amount' => '0.01',
  // 'trade_no' => '2018072521001004620553226159',
  // 'auth_app_id' => '2018072060686594',
  // 'buyer_logon_id' => '189****3093',
  // 'point_amount' => '0.00',
  // );
        //$utils->xywriteLog("回调数据: ".var_export($_REQUEST,true));
        // switch ($action) {
        //     case 'wechat':
        //         $buffer = SL('Pay/notify_' . $action, $_REQUEST);
        //         break;
        //     case 'alipay':
        //         $buffer = SL('Pay/notify_' . $action, $_REQUEST);
        //         break;      
        // }
        $buffer = SL('Pay/notify_alipay', $_REQUEST);
        exit($buffer[1]); //success
    }

    //异步返回
    public function notify_wechat() { 
        $buffer = SL('Pay/notify_wechat', $_REQUEST);
        exit($buffer[1]); //success
    }

    // 
    //异步返回
    public function notify_zhifubao() { 
        $buffer = SL('Pay/notify_zhifubao', $_REQUEST);
        exit($buffer[1]); //success
    }

    // 兴业回调中间控制器 bsh
    public function notify_xingye() {
        #兴业专属
        include_once COMMON_PATH . "/Tool/xingye/Utils.class.php";//辅助函数
        $utils = new \Utils();
        $xml = file_get_contents('php://input');
        //$utils->xywriteLog("回调xml: ".$xml);
        $request = $utils->parseXML($xml);
        $utils->xywriteLog("回调数据: ".var_export($request,true));
        $buffer = SL('Pay/notify_xingye', $request);
        exit($buffer[1]); //success
    }
    // 悦单回调中间控制器 bsh
    public function notify_yuedan() {
       
        include_once COMMON_PATH . "/Tool/xingye/Utils.class.php";//辅助函数
        $utils = new \Utils();
        // $request = $_REQUEST;

        // if (empty($request)) {
            $json = file_get_contents('php://input');
            //$utils->xywriteLog("ydjson: ".$json);
            //$request['json']='json';
            $json = urldecode($json);
            //$utils->xywriteLog("ydencode: ".$json);
            if ($json) {
                $Arr = explode('&', $json);
                foreach($Arr as $String) {
                  $Ayy = explode('=', $String);
                  $request[$Ayy[0]] = $Ayy[1]; 
                }    
                //$request = json_decode($json,true);
            }
            //$utils->xywriteLog("yd: ".var_export($request,true));
        // }
        $buffer = SL('Pay/notify_yuedan', $request);
        exit($buffer[1]); //success
    } 

    // 开联通回调
    public function notify_hyt() {

      $buffer = SL('Pay/notify_hyt', $_REQUEST);
      exit($buffer[1]); //success
    } 

    // 星支付回调
    public function notify_star() {

      $buffer = SL('Pay/notify_star', $_REQUEST);
      exit($buffer[1]); //success
    } 

    // 拼多多回调
    public function notify_pdd() {
      $json = file_get_contents('php://input');
      $repos = json_decode($json,true);
      $buffer = SL('Pay/notify_bshpdd', $repos); 
      exit($buffer[1]); //success
    } 

    // 拼多多1回调
    public function notify_buff() {
      $json = file_get_contents('php://input');
      $repos = json_decode($json,true);
      $buffer = SL('Pay/notify_buff', $repos); 
      exit($buffer[1]); //success
    } 

    // 悦单回调中间控制器 bsh
    public function notify_umsh5() {
        $buffer = SL('Pay/notify_umsh5', $_REQUEST);
        exit($buffer[1]); //success
    }

    // 银联回调中间控制器 bsh
    public function notify_yinlian() {
        $request = array();
        foreach($_POST as $key=>$value){
            $request[$key] = urldecode($value);
        } 
        $buffer = SL('Pay/notify_yinlian', $request);
        exit($buffer[1]); //success
    }     

    //同步返回
    public function backurl() {
        foreach ($_REQUEST as $i => $iBuffer) {
            if (strstr(strtolower($i), '/pay/backurl')) {
                $action = str_replace('/pay/backurl/', '', strtolower($i));
            }
        }
        switch ($action) {
            case 'wechat':
                $buffer = SL('Pay/backurl_' . $action, $_REQUEST);
                break;
            case 'alipay':
                $buffer = SL('Pay/backurl_' . $action, $_REQUEST);
                break;
            case 'xingye':
                $buffer = SL('Pay/backurl_' . $action, $_REQUEST);
                break; 
            case 'yuedan':
                $buffer = SL('Pay/backurl_' . $action, $_REQUEST);
                break; 
            case 'star':
                $buffer = SL('Pay/backurl_' . $action, $_REQUEST);
                break;
            case 'pdd':
                $buffer = SL('Pay/backurl_' . $action, $_REQUEST);
                break;               
        }
        header('Location:' . $buffer[1]); //跳转
        exit();
    }

    /**
     * 公众号类H5支付
     */
    public function jsapi() {
        $buffer = SL('Pay/jsapi', $_REQUEST);
        $this->reback($buffer, 1);
    }
    public function alijump() {
      
        $buffer = SL('Pay/alijump', $_REQUEST);
        $this->reback($buffer, 1);
    }
     /**
     * 公众号类H5支付
     */
    public function scanpay() {
        // $buffer = SL('Pay/scanpay', $_REQUEST);
        // $this->reback($buffer,!IS_AJAX);
         $http = $_GET['url'];
         $pay_info = 'http://pan.baidu.com/share/qrcode?w=150&h=150&url=' . $http;
        exit('<script>location.href="' . $pay_info . '";</script>');
    }

    /**
     * 跳转
     */
    public function go() {
        $http = $_GET['u'];
        exit('<script>location.href="' . $http . '";</script>');
    }

     // 银联调起
    public function ylup() {
        // include_once COMMON_PATH . "/Tool/yinlian/util/Settings_INI.php";//常用配置
        // $settings = new \Settings_INI();
        // $settings->load(COMMON_PATH. "/Tool/yinlian/config/path.properties");
       
        $tjurl = 'https://payment.chinapay.com/CTITS/service/rest/page/nref/000000000017/0/0/0/0/0';//$settings->get("pay_url");
        $transactionid = $_GET['transactionid'];
        $yinlian = SM('Yinlian')->findData('*', 'transactionid="' . $transactionid . '"');
        
      if ($yinlian) {
            $params=unserialize($yinlian['params']);
            header("Content-type: text/html; charset=utf-8");
            $str = '<form id="Form1" name="Form1" method="post" action="' . $tjurl . '">';
            foreach ($params as $key => $val) {
                $str = $str . '<input type="hidden" name="' . $key . '" value="' . $val . '"/>';
            }
            $str = $str . '</form>';
            $str = $str . '<script>';
            $str = $str . 'document.Form1.submit();';
            $str = $str . '</script>';
            exit($str);
      }else{
            echo "交易号有误";exit();
      }
    }

    // 开联通调起
    public function hytjump() {
      $transactionid = $_GET['transactionid'];
      $yinlian = SM('Yinlian')->findData('*', 'transactionid="' . $transactionid . '"');
          
      if ($yinlian) {
          $params=unserialize($yinlian['params']);
          $tjurl = 'http://get.72if.cn/Pay_Index.html';
 
          header("Content-type: text/html; charset=utf-8");
          $str = '<form id="Form1" name="Form1" method="post" action="' . $tjurl . '">';
          foreach ($params as $key => $val) {
              $str = $str . '<input type="hidden" name="' . $key . '" value="' . $val . '"/>';
          }
          $str = $str . '</form>';
          $str = $str . '<script>';
          $str = $str . 'document.Form1.submit();';
          $str = $str . '</script>';
          exit($str);
      }else{
              echo "交易号有误";exit();
      }

    }


    /**
     * 提交
     */
    public function formpost() {
        $http = $_GET;
        $tjurl=$http['wg'];
        unset($http['wg']);
        header("Content-type: text/html; charset=utf-8");
        $str = '<form id="Form1" name="Form1" method="post" action="' . $tjurl . '">';
        foreach ($http as $key => $val) {
            if(empty($val)) continue;
            $str = $str . '<input type="hidden" name="' . $key . '" value="' . $val . '"/>';
        }
        //$str = $str . '<input type="submit" style="width:20%;height:40px;" value="确认支付"/>';
        $str = $str . '</form>';
        $str = $str . '<script>';
        $str = $str . 'document.Form1.submit();';
        $str = $str . '</script>';
        exit($str);
    }

}
