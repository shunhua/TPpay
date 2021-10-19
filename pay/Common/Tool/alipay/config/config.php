<?php
class Config{

    private $cfg = array(
        'version'=>'2.0'		//版本号
    );
   
	// 随机轮训切换数组 
	public $RandomQuotes = array(
		array('appid'=>'2019012963181489','private'=>'MIIEpAIBAAKCAQEAryIfSLdcTN2Hh3HEvQTFv35+3WTcvTYQt5RskBMVEyEr/u6lCaIdMn8RnGDnY6/i1DKDca39W6Xzxpt8w1TGxEetnIKDMoCg1FEPTLxyBr8YNsUC2TqkeKGfFqtTgSUQuocA0PB6QAhD4xY9LWQhdIpldVhylnBiZ/1thsb82mX/KAl0vtykaoMCpUPVaGYKu0WzuBhNBx4GaSAat25POp7xavDi8oE+lhOFLDoUKkcm57bf/amUSqlUdPbv4GHVwHcYluDzEcZtCKcO7pnwpawt2klnyN0V7jg2r+SShCrMADfWjnJJOZ2dBsw87519iCNwUeTKHWXNYjr5iPFCXQIDAQABAoIBAQCRNuTjwY4Z+hH3n8D2ze898iA1aP2TMjI4ViySZhAydW3qi2xzCWXWSgCLPtp+EQgu1Neiuhb7GCaDBsgzmqbZd2mf/aPVi0xP4AqkoRiXOXpVZ5QOFQ7tK24jONobmmU9lNV7afqj/3Zy5CzD52PKIzsvSrBwxy0BduSLPZHJcCvDBMQnOUd8jSpV4LYihVHwdreS14PMGq9sqLmzfF98Fxz5eVED6PRCySZezOD1lSNC8VyxMMJm9+UfRQ8g0LA94CdvQJOvd6iit+XLUQu5BnElfR58mSN3bLZD3jTps50XMF86AYGnw0k6vvcrUlTVCX0hua+WntGxKF6l9zWhAoGBAN7AgJvew2brblKK7Q7uro0z/zaLWW8xNolHfHuceamM6fBuYaGPI8YARp/YfVnGsFnHwYxDasYHBVO6yO+7paRrOfg1SwJvFCjvD4qPLcm1Rw3yVc+TxZO8Hr1SAJ0vvuAd1woHKAULYtAfkOCOAZj1qNb4Z00/fSyqIGpnf7qVAoGBAMlGFE5MAawojQt7VotdlKB4in3wptjOV0EsSQg65omz1oNI/UYL1vnjk7CDLPzIl58oyKUgSu8JAGPBmUD7QXYw3QQeN+1/cAsJ5aRJ6HrNxLlWs3Rbqs1Y9arUVjI5d65YdPqXPPH7X/OJtotVml2jYpiBWv9CzCEqGZu/8z6pAoGAEeYB74Rcyx5LxRIorjR7jhkJfsZ/rzGTIkC+Peh61ibefVVBPwwTYcuP4TQyDk6qyOwGH1EjeToDMZEmqCy5yJZdGBagKlfmlMtwwj9y/Gw2g2Zfgshq7c4F4kx+hCLcAYIkT0f37T/W9sYyi/idJD9JC8z17/0T/8zDwgkC+GkCgYAOEm49Aydix/sEgH0OhV5s0cCtCXRA2DxVfCknACc6HW60AxhiG1XOZtDemG0+xy+CmzFcCiPzDmgOkWyLlIWBI+1v6kTyv+QvgWOi1uAAJf7JDnzBd987OCsE5I+aN1ZcxCZ5lrVWHcMZndk7aKLARToRe4nHKv5mmzDrKMJy8QKBgQDVIcncJ8T+WunRMLD+AIVVb7ixKEpxAie9eER1uHlm5LqO2d6HOU6ocrmZFgOcNjpmOrkn678pgYUa3BSf2iHfKpKIlfcv6lPaPp3uCBtmWmqBfE4cXX4YilMprhs/wd+2vJ2T3ieExT+E4KUO10NrMruPH/BZuL65HVulwA4FpA==','public'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArq7/gzy3IZ3APL04DuBXSU2f/Z/OMay/x6yqDHqQeFSmJYcAm/QWl3XCLZGw6LZTwRFFmvM+fyZ2t4peqtuNGUj/yvDKgMRM4h+v3V4LPmzbrQpJqXCouBLvsctwAJ6w5zIRhjJ8Bj72tgGEtF9fH+F25IE6dkdXmMzK2I3JM+zz2xxzzsHY9dWSEnUWL+iYdb8rpcl9Mn21Sn3di9/1j14/ud+vcsCFEy20odaJS6upubB+6K0JeANaB+1WYijIVt95fBuSPoVT7qzjQTvzfCs6U7457UOGPq2eK1S6UeTkSUgTtPsvGRORsbLuoEjaEene1Ry2TogGu2ssYZt3SwIDAQAB'),
	);
	// 回调根据商户号查询密钥数组
	public $Randomnotify = array(
		array('appid'=>'2019012963181489','private'=>'MIIEpAIBAAKCAQEAryIfSLdcTN2Hh3HEvQTFv35+3WTcvTYQt5RskBMVEyEr/u6lCaIdMn8RnGDnY6/i1DKDca39W6Xzxpt8w1TGxEetnIKDMoCg1FEPTLxyBr8YNsUC2TqkeKGfFqtTgSUQuocA0PB6QAhD4xY9LWQhdIpldVhylnBiZ/1thsb82mX/KAl0vtykaoMCpUPVaGYKu0WzuBhNBx4GaSAat25POp7xavDi8oE+lhOFLDoUKkcm57bf/amUSqlUdPbv4GHVwHcYluDzEcZtCKcO7pnwpawt2klnyN0V7jg2r+SShCrMADfWjnJJOZ2dBsw87519iCNwUeTKHWXNYjr5iPFCXQIDAQABAoIBAQCRNuTjwY4Z+hH3n8D2ze898iA1aP2TMjI4ViySZhAydW3qi2xzCWXWSgCLPtp+EQgu1Neiuhb7GCaDBsgzmqbZd2mf/aPVi0xP4AqkoRiXOXpVZ5QOFQ7tK24jONobmmU9lNV7afqj/3Zy5CzD52PKIzsvSrBwxy0BduSLPZHJcCvDBMQnOUd8jSpV4LYihVHwdreS14PMGq9sqLmzfF98Fxz5eVED6PRCySZezOD1lSNC8VyxMMJm9+UfRQ8g0LA94CdvQJOvd6iit+XLUQu5BnElfR58mSN3bLZD3jTps50XMF86AYGnw0k6vvcrUlTVCX0hua+WntGxKF6l9zWhAoGBAN7AgJvew2brblKK7Q7uro0z/zaLWW8xNolHfHuceamM6fBuYaGPI8YARp/YfVnGsFnHwYxDasYHBVO6yO+7paRrOfg1SwJvFCjvD4qPLcm1Rw3yVc+TxZO8Hr1SAJ0vvuAd1woHKAULYtAfkOCOAZj1qNb4Z00/fSyqIGpnf7qVAoGBAMlGFE5MAawojQt7VotdlKB4in3wptjOV0EsSQg65omz1oNI/UYL1vnjk7CDLPzIl58oyKUgSu8JAGPBmUD7QXYw3QQeN+1/cAsJ5aRJ6HrNxLlWs3Rbqs1Y9arUVjI5d65YdPqXPPH7X/OJtotVml2jYpiBWv9CzCEqGZu/8z6pAoGAEeYB74Rcyx5LxRIorjR7jhkJfsZ/rzGTIkC+Peh61ibefVVBPwwTYcuP4TQyDk6qyOwGH1EjeToDMZEmqCy5yJZdGBagKlfmlMtwwj9y/Gw2g2Zfgshq7c4F4kx+hCLcAYIkT0f37T/W9sYyi/idJD9JC8z17/0T/8zDwgkC+GkCgYAOEm49Aydix/sEgH0OhV5s0cCtCXRA2DxVfCknACc6HW60AxhiG1XOZtDemG0+xy+CmzFcCiPzDmgOkWyLlIWBI+1v6kTyv+QvgWOi1uAAJf7JDnzBd987OCsE5I+aN1ZcxCZ5lrVWHcMZndk7aKLARToRe4nHKv5mmzDrKMJy8QKBgQDVIcncJ8T+WunRMLD+AIVVb7ixKEpxAie9eER1uHlm5LqO2d6HOU6ocrmZFgOcNjpmOrkn678pgYUa3BSf2iHfKpKIlfcv6lPaPp3uCBtmWmqBfE4cXX4YilMprhs/wd+2vJ2T3ieExT+E4KUO10NrMruPH/BZuL65HVulwA4FpA==','public'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArq7/gzy3IZ3APL04DuBXSU2f/Z/OMay/x6yqDHqQeFSmJYcAm/QWl3XCLZGw6LZTwRFFmvM+fyZ2t4peqtuNGUj/yvDKgMRM4h+v3V4LPmzbrQpJqXCouBLvsctwAJ6w5zIRhjJ8Bj72tgGEtF9fH+F25IE6dkdXmMzK2I3JM+zz2xxzzsHY9dWSEnUWL+iYdb8rpcl9Mn21Sn3di9/1j14/ud+vcsCFEy20odaJS6upubB+6K0JeANaB+1WYijIVt95fBuSPoVT7qzjQTvzfCs6U7457UOGPq2eK1S6UeTkSUgTtPsvGRORsbLuoEjaEene1Ry2TogGu2ssYZt3SwIDAQAB'),
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
            return array('appid'=>'','private'=>'','public'=>'');  
        }  
        foreach ($parents as $key => $value) {   
            if($value['appid'] == $searched){ 
            	return $parents[$key];
            }  
        }  
       return array('appid'=>'','private'=>'','public'=>'');
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