<?php
class Config{
    private $cfg = array(
        'url'=>'https://pay.swiftpass.cn/pay/gateway',	//支付请求url，无需更改
        'mchId'=>'7551000001',		//测试商户号，商户正式上线时需更改为自己的
        'key'=>'9d101c97133837e13dde2d32a5054abb',   //测试密钥，商户需更改为自己的
        'version'=>'2.0'		//版本号
       );
   
	// 随机轮训切换数组 
	public $RandomQuotes = array(
		// array('appid'=>'101510267188','key'=>'083375248c7a5d81ddbf409d3d5a7db4'),
		// array('appid'=>'101520268711','key'=>'f26d4b621a4cda93b99d2c8f19a33b66'),
		// array('appid'=>'101540268725','key'=>'41aa422f48cd11d3059bccb192018038'),
		// array('appid'=>'101580268473','key'=>'d27401de2fe1345cefd51b2c12a6094e'),
		// array('appid'=>'101580268471','key'=>'7b5e54df04105298647092ac34efe394'),
		// array('appid'=>'101500269007','key'=>'5917eea1e9e2518a0ede6a44140f6071'),
		// array('appid'=>'101520268709','key'=>'ec889fd52cf7201630d1e86820752e80'),
		// array('appid'=>'101590267765','key'=>'e8064c8c7f08729a4f830d58773cfaa0'),
		// array('appid'=>'101530268122','key'=>'86ce65a4d15197ea79c14265e74f50fa'),
		// array('appid'=>'101550267545','key'=>'efd7a72149314e10a7a6ea8cf14dad1c'),

		// array('appid'=>'101510264880','key'=>'798226a85d5923473262abcb61eab34b'),
		// array('appid'=>'101550265265','key'=>'c85f081b05bbc992cb834e7e532289c0'),
		// array('appid'=>'101540266365','key'=>'2dad09371c9bd0888b65598f25197fd7'),
		// array('appid'=>'101560265754','key'=>'e0c16fb3f9c5c0e04881379556e59912'),
		// array('appid'=>'101580266009','key'=>'d840f45c96a7be3cb40d55cfc3b3e379'),

		// array('appid'=>'101550264303','key'=>'463a11cf7a09f6b472063cf3feda65c5'),
		// array('appid'=>'101590264569','key'=>'cc35faca237382dac5b8859993dc9bb1'),
		// array('appid'=>'101550264301','key'=>'4a990f649302a26044c0a948302b7f00'),
		// array('appid'=>'101580265041','key'=>'03b3a05064e5b600f7f49dc4b8685c18'),

		// array('appid'=>'101590264568','key'=>'989e8c72cbac39d55a04e035580fda7f'),
		// array('appid'=>'101540265473','key'=>'d9df02d4cbeed024e3c323c6c09f6de5'),
		// array('appid'=>'101520265341','key'=>'238e336df38fa39eb338efa442140ed8'),

		// array('appid'=>'101520265337','key'=>'1c2b5058ada7e43cebc404861414851e'),
		// array('appid'=>'101550264302','key'=>'14327cab388382d749861d4b6de6f634'),
		// array('appid'=>'101510263899','key'=>'8496f2d8307484886e6515b04fc0dfa5'),

		// array('appid'=>'101530258517','key'=>'de55872cea4c9313ff53c88ca7d7e651'),
		// array('appid'=>'101510257471','key'=>'1a52e7e3a20a2c2947597d123f3de893'),
		// array('appid'=>'101550258071','key'=>'f2acef779d74af2de8e336c9e2c59486'),
		// array('appid'=>'101550258072','key'=>'0408b42c45254939b0fcf85679396117'),
		// array('appid'=>'101530258518','key'=>'0bca92e0f6011a5a8458e48ee50af560'),
		// array('appid'=>'101540258944','key'=>'3a4ff0a61f57ac16dd3a57257c3d0670'),
		// array('appid'=>'101570257968','key'=>'cb0e4d6d72c289e28dd0b6aa8c5f7a39'),
		// array('appid'=>'101560258441','key'=>'df5848ef280770a7ec896c37ea828ed6'),
		// array('appid'=>'101550258074','key'=>'aab65151a981c8386d383d20811be9d9'),

		// 2018.1.6 新增
		// array('appid'=>'101570265430','key'=>'879097e144bcd56b8b8938ffbba0bd9e'),
		// array('appid'=>'101590265490','key'=>'87d8fbbe4573bb9a9576ae324bf34fb5'),
		// array('appid'=>'101580266008','key'=>'84e4890dc0bd57ede26e6fd4a860acb7'),
		// array('appid'=>'101570265427','key'=>'2839c45a1b28b219418c0bb79c56cf30'),
		// array('appid'=>'101590265488','key'=>'05ce97c31eaff1a42e7dfe48a23d3451'),
	);
	// 回调根据商户号查询密钥数组
	public $Randomnotify = array(
		array('appid'=>'101510267188','key'=>'083375248c7a5d81ddbf409d3d5a7db4'),
		array('appid'=>'101520268711','key'=>'f26d4b621a4cda93b99d2c8f19a33b66'),
		array('appid'=>'101540268725','key'=>'41aa422f48cd11d3059bccb192018038'),
		array('appid'=>'101580268473','key'=>'d27401de2fe1345cefd51b2c12a6094e'),
		array('appid'=>'101580268471','key'=>'7b5e54df04105298647092ac34efe394'),
		array('appid'=>'101500269007','key'=>'5917eea1e9e2518a0ede6a44140f6071'),
		array('appid'=>'101520268709','key'=>'ec889fd52cf7201630d1e86820752e80'),
		array('appid'=>'101590267765','key'=>'e8064c8c7f08729a4f830d58773cfaa0'),
		array('appid'=>'101530268122','key'=>'86ce65a4d15197ea79c14265e74f50fa'),
		array('appid'=>'101550267545','key'=>'efd7a72149314e10a7a6ea8cf14dad1c'),
		
		array('appid'=>'101510264880','key'=>'798226a85d5923473262abcb61eab34b'),
		array('appid'=>'101550265265','key'=>'c85f081b05bbc992cb834e7e532289c0'),
		array('appid'=>'101540266365','key'=>'2dad09371c9bd0888b65598f25197fd7'),
		array('appid'=>'101560265754','key'=>'e0c16fb3f9c5c0e04881379556e59912'),
		array('appid'=>'101580266009','key'=>'d840f45c96a7be3cb40d55cfc3b3e379'),
		
		array('appid'=>'101550264303','key'=>'463a11cf7a09f6b472063cf3feda65c5'),
		array('appid'=>'101590264569','key'=>'cc35faca237382dac5b8859993dc9bb1'),
		array('appid'=>'101550264301','key'=>'4a990f649302a26044c0a948302b7f00'),
		array('appid'=>'101580265041','key'=>'03b3a05064e5b600f7f49dc4b8685c18'),
		
		array('appid'=>'101590264568','key'=>'989e8c72cbac39d55a04e035580fda7f'),
		array('appid'=>'101540265473','key'=>'d9df02d4cbeed024e3c323c6c09f6de5'),
		array('appid'=>'101520265341','key'=>'238e336df38fa39eb338efa442140ed8'),

		array('appid'=>'101520265337','key'=>'1c2b5058ada7e43cebc404861414851e'),
		array('appid'=>'101550264302','key'=>'14327cab388382d749861d4b6de6f634'),
		array('appid'=>'101510263899','key'=>'8496f2d8307484886e6515b04fc0dfa5'),

		array('appid'=>'101530258517','key'=>'de55872cea4c9313ff53c88ca7d7e651'),
		array('appid'=>'101510257471','key'=>'1a52e7e3a20a2c2947597d123f3de893'),
		array('appid'=>'101550258071','key'=>'f2acef779d74af2de8e336c9e2c59486'),
		array('appid'=>'101550258072','key'=>'0408b42c45254939b0fcf85679396117'),
		array('appid'=>'101530258518','key'=>'0bca92e0f6011a5a8458e48ee50af560'),
		array('appid'=>'101540258944','key'=>'3a4ff0a61f57ac16dd3a57257c3d0670'),
		array('appid'=>'101570257968','key'=>'cb0e4d6d72c289e28dd0b6aa8c5f7a39'),
		array('appid'=>'101560258441','key'=>'df5848ef280770a7ec896c37ea828ed6'),
		array('appid'=>'101550258074','key'=>'aab65151a981c8386d383d20811be9d9'),

		// 2018.1.6 新增
		array('appid'=>'101570265430','key'=>'879097e144bcd56b8b8938ffbba0bd9e'),
		array('appid'=>'101590265490','key'=>'87d8fbbe4573bb9a9576ae324bf34fb5'),
		array('appid'=>'101580266008','key'=>'84e4890dc0bd57ede26e6fd4a860acb7'),
		array('appid'=>'101570265427','key'=>'2839c45a1b28b219418c0bb79c56cf30'),
		array('appid'=>'101590265488','key'=>'05ce97c31eaff1a42e7dfe48a23d3451'),
	);
	public function dataPollingInterval($list,$polling_time='3m',$polling_number=1) {
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
            return array('appid'=>'','key'=>'');  
        }  
        foreach ($parents as $key => $value) {   
            if($value['appid'] == $searched){ 
            	return $parents[$key];
            }  
        }  
       return array('appid'=>'','key'=>'');
    }
     // 随机时间
	// private $DayOfTheYear=date('i');// m、h、i、s  
	// // 随机轮训切换数组函数
	// public function RandomQuoteByInterval($TimeBase, $QuotesArray){
	// 	  // Make sure it is a integer
	// 	  $TimeBase = intval($TimeBase);
	// 	  // How many items are in the array?
	// 	  $ItemCount = count($QuotesArray);
	// 	  // By using the modulus operator we get a pseudo
	// 	  // random index position that is between zero and the
	// 	  // maximal value (ItemCount)
	// 	  $RandomIndexPos = ($TimeBase % $ItemCount);
	// 	  // Now return the random array element
	// 	  return $QuotesArray[$RandomIndexPos];
	// }
	
    
    public function C($cfgName){
        return $this->cfg[$cfgName];
    }

}
?>