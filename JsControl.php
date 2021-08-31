<?php
namespace sleifer\jscontrol;

use yii\helpers\Json;
use yii\base\Widget;

class JsControl extends Widget{

    private static $js;
    //private $bundle = null;
    private $actions = [['action' => 'MINI-MENU', 'mini' => 'false']];
    private $status = true;
    private $x_jscontrol = true;

    // constante de posicionamento do gritter
    const GRITTER_POSITION_BOTTOM_LEFT  = 'bottom-left';
    const GRITTER_POSITION_BOTTOM_RIGHT = 'bottom-right';
    const GRITTER_POSITION_TOP_RIGHT    = 'top-right'; // default
    const GRITTER_POSITION_TOP_LEFT     = 'top-left';
    
    // private function __construct(){
    //     $view = $this->getView();
    //     $this->bundle = ActiveAssets::register($view);
    // }
    
    public function __clone(){}
    
    public function __wakeup(){}
     
    public static function js(){
        if(self::$js === null){
            self::$js = new self;
        }
        return self::$js;
    }
    
    public function html($selector, $html){
        $this->actions[] = ['action' => 'HTML', 'selector' => $selector, 'data' => $html];
    }
    public function donwloadPdf($file){
        $this->actions[] = ['action' => 'DOWNLOAD-PDF', 'data' => $file];
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

    public function checkbox($selector, $val){
        $this->actions[] = ['action' => 'CHECKBOX', 'selector' => $selector, 'data' => $val];
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
    
    public function redirect($url, $isAjax = true, $pushstate = true) {
        $this->actions[] = ['action' => 'REDIRECT', 'url' => $url, 'isAjax' => $isAjax, 'pushstate' => $pushstate];
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

    public function clearError($formName, $attribute){
        $this->actions[] = ['action' => 'CLEAR-ERROR', 'formName' => $formName, 'attr' => $attribute];
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

    public function execEvent($selector, $type){
        $this->actions[] = ['action' => 'EXEC-EVENT', 'selector' => $selector, 'type' => $type];
    }
    
    public function miniMenu($mini = true){
        unset($this->actions[0]);

        if(!IS_MOBILE){
            $this->actions[0] = ['action' => 'MINI-MENU', 'mini' => $mini] ;
        }
    }

    public function infoError($model){
        $erros = '';
        foreach($model->errors as $key => $error){
            foreach($error as $val){
                $erros .= $key . ' - ' . $val;
            }
        }

        $html = "
            <div class='alert alert-danger m-b-0'>
                <h5><i class='fa fa-info-circle'></i>Erro</h5>
                {$erros}
            </div>
            <div class='modal-footer'>
                <a href='javascript:;' class='btn btn-white' data-dismiss='modal'>Sair</a>
            </div>
        ";

        $this->actions[] = ['action' => 'OPEN-MODAL', 'title' => 'Erro', 'data' => $html, 'size' => 0, 'type' => 'modal-sm'];
    }

    /**
     * Opções simples, para outra verificar o manual da lib
     * 
     * @api https://sweetalert2.github.io/#configuration
     * 
     * [
     *      'title' => 'Titulo',
     *      'text' => 'Texto',
     *      'icon' => cSweetAlert::cSuccess,
     *      'showCancelButton' => true,
     *      'showCancelButton' => true,
     *      'cancelButtonText' => 'Cancelar',
     *      'confirmButtonText' => 'Salvar'
     * ]
     * 
     */


    public function sweetAlert($params, $urlButtonConfirm = null, $data = null){
        $this->actions[] = ['action' => 'SWEET-ALERT', 'params' => $params, 'urlButtonConfirm' => $urlButtonConfirm, 'data' => $data];
    }
    
    public function XJscontrol($status = true){
        $this->x_jscontrol = $status;
    }


    public static function parseQueryString($params){
        $data = '';
        $sep = '';
        foreach($params as $key => $val){
            $data .= $sep . $key . '=' . $val;
            $sep = '&';
        }
        return $data;
    }
    
    public function clearAction(){
        $this->actions = [];
    }
    
    /**
     *
     * parametro disponiveis para os params
     * title, text, image, sticky, time, class_name
     *
     * parametros disponiveis para o options
     * position -> constant, 
     * fade_in_speed -> int, 
     * fade_out_speed -> int, 
     * time -> int
     * @param string $text
     * @param string $title
     * @param array $params[]
     */
    public function gritter($text, $title = null, $options = null) {
        $params['title'] = $title;
        $params['text']  = $text;
        $this->actions[] = ['action' => 'GRITTER', 'params' => $params, 'options' => $options];
    }
    
    public function send() {
        $rq = \Yii::$app->request;
        if(count($this->actions) > 0 && ($rq->headers->has('X-JSCONTROL') || !$this->x_jscontrol)){
            echo Json::encode(['actions' => $this->actions, 'success' => $this->status]);
        }
        exit;
    }
}