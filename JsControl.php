<?php
namespace sleifer\jscontrol;
use yii\helpers\Json;
use yii\base\Widget;
use yii\helpers\VarDumper;

class JsControl extends Widget{
    
    private static $js;
    private $bundle = null;
    private $actions = [['action' => 'MINI-MENU', 'mini' => 'false']];
    private $status = true;
    
    private function __construct(){
        $view = $this->getView();
        $this->bundle = ActiveAssets::register($view);
    }
    
    public function __clone(){}
    
    public function __wakeup(){}
    
    
    public static function js()
    {
        if(self::$js === null){
            self::$js = new self;
        }
        return self::$js;
    }
    
    public function html($selector, $html){
        $this->actions[] = ['action' => 'HTML', 'selector' => $selector, 'data' => $html];
    }
    
    public function title($title){
        $this->actions[] = ['action' => 'HTML', 'selector' => 'title', 'data' => $title];
    }
    
    public function val($selector, $val){
        $this->actions[] = ['action' => 'VAL', 'selector' => $selector, 'data' => $val];
    }
    
    public function attr($selector, $val){
        $this->actions[] = ['action' => 'ATTR', 'selector' => $selector, 'data' => $val];
    }
    
    public function modal($title, $html, $size = 500, $type = cDialog::cDefault){
        $this->actions[] = ['action' => 'OPEN-MODAL', 'title' => $title, 'data' => $html, 'size' => $size, 'type' => $type];
    }
    
    public function dialog($title, $html,  $type = cDialog::cDefault){
        $this->actions[] = ['action' => 'OPEN-DIALOG', 'title' => $title, 'data' => $html, 'type' => $type];
    }
    
    public function closeDialog(){
        $this->actions[] = ['action' => 'CLOSE-MODAL'];
    }
    
    public function alert($msg) {
        $this->actions[] = ['action' => 'ALERT', 'msg' => $msg];
    }
    
    public function redirect($url) {
        $this->actions[] = ['action' => 'REDIRECT', 'url' => $url];
    }
    
    public function populateOption($selector, $array) {
        $options = '<option>Selecione</option>';
        foreach($array as $key => $value){
            $options .= "<option value='{$key}'>{$value}</option>";
        }
        $this->actions[] = ['action' => 'OPTION', 'selector' => $selector, 'data' => $options];
    }
    
    public function updateGrid($id, $options = null) {
        $this->actions[] = ['action' => 'UPDATEGRID', 'id' => $id, 'options' => $options];
    }
    
    public function remove($selector){
        $this->actions[] = ['action' => 'REMOVE', 'selector' => $selector];
    }
    
    public function removeDataGrid($grid, $id){
        $this->actions[] = ['action' => 'REMOVEDATAGRID', 'grid' => $grid, 'id' => $id];
    }
    
    public function execFunction($name){
        $this->actions[] = ['action' => 'EXEC-FUNCTION', 'name' => $name];
    }
    
    public function addErros($formName, $errors){
        $this->actions[] = ['action' => 'ADD-ERRORS', 'formName' => $formName, 'errors' => $errors];
    }
    
    public function removeClass($selector, $className){
        $this->actions[] = ['action' => 'REMOVE-CLASS', 'selector' => $selector, 'className' => $className];
    }
    
    public function addClass($selector, $className){
        $this->actions[] = ['action' => 'ADD-CLASS', 'selector' => $selector, 'className' => $className];
    }
    
    public function newTab($link){
        $this->actions[] = ['action' => 'NEW-TAB', 'link' => $link];
    }
    
    public function miniMenu($mini = true){
        unset($this->actions[0]);
        $this->actions[0] = ['action' => 'MINI-MENU', 'mini' => $mini] ;
    }
    
    public function clearAction(){
        $this->actions = [];
    }
    
    /**
     *
     * parêmetro disponiveis para os params
     * title, text, image, sticky, time, class_name
     *
     * @param string $text
     * @param string $title
     * @param array $params[]
     */
    public function gritter($text, $title = null, $params = []) {
        $params['title'] = $title;
        $params['text']  = $text;
        
        $this->actions[] = ['action' => 'GRITTER', 'params' => $params];
    }
    
    public function send() {
        $rq = \Yii::$app->request;
        if(count($this->actions) > 0 && $rq->headers->has('X-JSCONTROL')){
            echo Json::encode(['actions' => $this->actions, 'success' => $this->status]);
        }
        exit;
    }
}