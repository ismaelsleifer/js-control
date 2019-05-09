<?php
namespace sleifer\jscontrol;
use yii\helpers\Json;

class JsControl{
    
    private static $js;
    private $actions = [['action' => 'MINI-MENU', 'mini' => 'false']];
    
    private function __construct(){}
    
    private function __clone(){}
    
    private function __wakeup(){}
    
    public static function js()
    {
        if(self::$js === null){
            self::$js = new self;
        }
        return self::$js;
    }
    
    public function alert($msg) {
        $this->actions[] = ['action' => 'ALERT', 'msg' => $msg];
    }
    
    public function send() {
        if(count($this->actions) > 0){
            echo Json::encode(['returnFunction' => $this->returnFunction, 'actions' => $this->actions, 'success' => $this->success]);
        }
    }
}