<?php
class Payjs{
    private $config = array(
        'url'=>'https://qr.chinaums.com/netpay-portal/webpay/pay.do',	//支付请求url，无需更改	
        'msgSrc'=>'WWW.WHJDZRSM.COM',       //消息来源(msgSrc)
        'instMid'=>'YUEDANDEFAULT',       //机构商户号(instMid)
        'msgSrcId'=>'4410',             //来源编号（msgSrcId）
        'md5key'=>'bjSHKxG5D7ZXJkmZRrxJMC7DZzXc7pGAGhfM5iWeSwdrRP8R',             //MD5密钥
        'tid'=>'88880001'			    //终端号(tid)	测试数据
       );

    public function Con($cfgName){
        return $this->config[$cfgName];
    }
    // 随机轮训切换数组 
    public $RandomQuotes = array(
        array('appid'=>'898420152113088','tid'=>'51140608'),
        //array('appid'=>'898340149000005','tid'=>'88880001'),
    );
    // 回调根据商户号查询密钥数组
    public $Randomnotify = array(
        //array('appid'=>'898340149000005','tid'=>'88880001'),
       array('appid'=>'898420152113088','tid'=>'51140608'),
    );
    public function dataPollingInterval($list,$polling_time='10m',$polling_number=1) {
        // 规划轮询间隔时间的参数:
        $interval = false;
        // 判断$polling_time 的类型是 秒、分、小时、天 中的哪1种。
        $arg = array( 
            's'=>1,            // 秒
            'm'=>60,            // 分= 60 sec
            'h' =>3600,        // 时= 3600 sec
            'd' => 86400,    // 天= 86400 sec
        );

        // 判断间隔时间的类型，并计算间隔时间
        foreach ($arg as $k => $v) {
            if (false!== stripos($polling_time,$k)) {
                $interval = intval($polling_time)*$v;
                break;
            }
        }
          // 判断间隔时间
        if(!is_int($interval)){  
            return false;
        }
        // 从今年开始的秒数
        $this_year_begin_second = strtotime(date('Y-01-01 01:00:01', time()));
        
        // 当前秒数 - 今年开始的秒数，得到今年到目前为止的秒数。
        $polling_time = time()-$this_year_begin_second;
        
        // 从今年到目前为止的秒数，计算得到当前轮数
        $len = count($list); // 总长度
        $start_index = intval($polling_time/$interval);
        $start_index = $polling_number*$start_index%$len; // 轮排数量 * 轮数 , 取余 总数量。
        $res = array();
        // 将轮数 指向到数组的索引，然后从这个索引开始接着往下循环遍历。
        for ($i=0; $i<$len; ++$i) {
            $index=$i+$start_index; // 索引的变化是根据时间来变
            if ($index>=$len) {
                $index=$index-$len ;
            }
            $res[]=$list[$index]; // 存入结果
        }
        return $res[0];
    }  
    //查询二维数组是否存在某个值，存在就返回
    public function multidimensional_search($parents, $searched) {  
        if (empty($searched) || empty($parents)) {  
            return array('appid'=>'','tid'=>'');  
        }  
        foreach ($parents as $key => $value) {   
            if($value['appid'] == $searched){ 
                return $parents[$key];
            }  
        }  
       return array('appid'=>'','tid'=>'');
    }
   
    /**
     * http post json请求
     */
    public function http_post_json($url, $jsonStr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($jsonStr)
            )
        );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
     
        return array($httpCode, $response);
    }
    /**
     * 随机生成7位随机数
     */
    public function generate_code($length = 7) {
        $min = pow(10 , ($length - 1));
        $max = pow(10, $length) - 1;
        return rand($min, $max);
    }
   /**
     * 创建md5摘要,规则是:按参数名称a-z排序,遇到空值的参数不参加签名。
     */
    public function getSign($requestarray)
    {
        ksort($requestarray);
        reset($requestarray);
        $md5str = "";
        foreach ($requestarray as $key => $val) {
            if(!empty($val) && $key!='sign'){
                $md5str = $md5str . $key . "=" . $val . "&";
            }
        }
        $md5str = rtrim($md5str,"&");
        $sign = strtoupper(md5($md5str .$this->Con('md5key')));
        return $sign; 

    }
    /**
     * 异步签名检查。
     */
    public function notifySign($requestarray) 
    {
        $notifySign = $requestarray["sign"];
        ksort($requestarray);
        reset($requestarray);
        $md5str = "";
        foreach ($requestarray as $key => $val) {
            if(!empty($val) && $key!='sign'){
                $md5str = $md5str . $key . "=" . $val . "&";
            }
        }
        $md5str = rtrim($md5str,"&");
        $sign = strtoupper(md5($md5str .$this->Con('md5key')));
        return $sign;
    }

    public function xywriteLog($text) {
        $path=dirname(__FILE__)."/log/log.txt";
        if(!file_exists(dirname($path))) mkdir(dirname($path),0777,true);
        if(!file_exists($path)) file_put_contents($path,'');
        file_put_contents ( $path, date ( "Y-m-d H:i:s" ) . "  " . $text . "\r\n", FILE_APPEND );
    }

}
?>