<?php
// +----------------------------------------------------------------------
// | easy pay [ pay to easy ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 All rights reserved.
// +----------------------------------------------------------------------
// | Author: fengxing <QQ:51125330>
// +----------------------------------------------------------------------
namespace Index\Controller;
class HomeController extends BaseController {
    public function index(){
        $buffer = SL(CONTROLLER_NAME.'/index', $_REQUEST);
        $this->reback($buffer,1);
    }
    public function pass(){
        $buffer = SL(CONTROLLER_NAME.'/pass', $_REQUEST);
        $this->reback($buffer,!IS_AJAX);
    }
    public function dluserfl(){
        $buffer = SL(CONTROLLER_NAME.'/dluserfl', $_REQUEST);
        $this->reback($buffer,!IS_AJAX);
    }
    public function loginout(){
        $buffer = SL(CONTROLLER_NAME.'/loginout', $_REQUEST);
        $this->reback($buffer,1);
    }
    public function dingdan(){
        $buffer = SL(CONTROLLER_NAME.'/dingdan', $_REQUEST);
        $this->reback($buffer,1);
    }
    public function dingdancf(){
        $buffer = SL(CONTROLLER_NAME.'/dingdancf', $_REQUEST);
        $this->reback($buffer,!IS_AJAX);
    }
    public function yhk(){
        $buffer = SL(CONTROLLER_NAME.'/yhk', $_REQUEST);
        $this->reback($buffer,!IS_AJAX);
    }
    public function yhkadd(){
        $buffer = SL(CONTROLLER_NAME.'/yhkadd', $_REQUEST);
        $this->reback($buffer,!IS_AJAX);
    }
    public function tx(){
        $buffer = SL(CONTROLLER_NAME.'/tx', $_REQUEST);
        $this->reback($buffer,!IS_AJAX);
    }
    public function txjl(){
        $buffer = SL(CONTROLLER_NAME.'/txjl', $_REQUEST);
        $this->reback($buffer,1);
    }
    public function fl(){
        $buffer = SL(CONTROLLER_NAME.'/fl', $_REQUEST);
        $this->reback($buffer,1);
    }
    public function api(){
        $buffer = SL(CONTROLLER_NAME.'/api', $_REQUEST);
        $this->reback($buffer,!IS_AJAX);
    }
    public function dl(){
        $buffer = SL(CONTROLLER_NAME.'/dl', $_REQUEST);
        $this->reback($buffer,1);
    }
    public function dluser(){
        $buffer = SL(CONTROLLER_NAME.'/dluser', $_REQUEST);
        $this->reback($buffer,1);
    }
   
    public function dldingdan(){
        $buffer = SL(CONTROLLER_NAME.'/dldingdan', $_REQUEST);
        $this->reback($buffer,1);
    }
    public function dlfandian(){
        $buffer = SL(CONTROLLER_NAME.'/dlfandian', $_REQUEST);
        $this->reback($buffer,1);
    }
}