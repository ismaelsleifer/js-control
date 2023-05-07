<?php
namespace sleifer\jscontrol;

use Yii;
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
    /**
     * Modifica o elemento interno da tag <div>{AQUI}</div>
     */
    public function html($selector, $html){
        $this->addAction('HTML', ['selector' => $selector, 'data' => $html]);
    }

    public function donwloadPdf($file){
        $this->addAction('DOWNLOAD-PDF', ['data' => $file]);
    }
    
    /**
     * Modifica o titulo da pagina
     */
    public function title($title){
        $this->addAction('HTML', ['selector' => 'title', 'data' => $title]);
    }
    
    /**
     * Altera o value do input
     */
    public function val($selector, $val){
        $this->addAction('VAL', ['selector' => $selector, 'data' => $val]);
    }
    
    /**
     * Adiciona um atributo ao elemento
     */
    public function attr($selector, $val){
        $this->addAction('ATTR', ['selector' => $selector, 'data' => $val]);
    }

    /**
     * Remove um atributo ao elemento
     */
    public function removeAttr($selector, $attribute){
        $this->addAction('REMOVE_ATTR', ['selector' => $selector, 'data' => $attribute]);
    }

    public function checkbox($selector, $val){
        $this->addAction('CHECKBOX', ['selector' => $selector, 'data' => $val]);
    }
    
    /**
     * Abre uma tela em modelo modal 
     */
    public function modal($title, $html, $size = 500, $type = cDialog::cDefault){
        $this->addAction('OPEN-MODAL', ['title' => $title, 'data' => $html, 'size' => $size, 'type' => $type]);
    }
    
    /**
     * Abre uma tela em modelo Dialog
     */
    public function dialog($title, $html, $type = cDialog::cDefault){
        $this->addAction('OPEN-DIALOG', ['title' => $title, 'data' => $html, 'type' => $type]);
    }
    /**
     * inseri na tela um modal que foi criado no backend
     */
    public function createModal($id, $html, $options = 'show'){
        $html = "<div id='{$id}'>{$html}</div>";
        $this->addAction('CREATE-MODAL', ['data' => $html, 'id' => $id, 'options' => $options]);
    }
    
    /**
     * fecha todos os dialogs ou modais abertos
     */
    public function closeDialog($selector = '.modal'){
        $this->addAction('CLOSE-MODAL', ['selector' => $selector]);
    }

    public function closeModal($selector = '.modal'){
        $this->addAction('CLOSE-MODAL', ['selector' => $selector]);
    }
    
    /**
     * gera um alert na tela
     */
    public function alert($msg) {
        $this->addAction('ALERT', ['msg' => $msg]);
    }
    
    public function redirect($url, $isAjax = true, $pushstate = true) {
        $this->addAction('REDIRECT', ['url' => $url, 'isAjax' => $isAjax, 'pushstate' => $pushstate]);
    }
    
    public function populateOption($selector, $array) {
        $options = '<option>Selecione</option>';
        foreach($array as $key => $value){
            $options .= "<option value='{$key}'>{$value}</option>";
        }
        $this->addAction('OPTION', ['selector' => $selector, 'data' => $options]);
    }
    
    public function updateGrid($id, $options = null) {
        $this->addAction('UPDATEGRID', ['id' => $id, 'options' => $options]);
    }
    
    public function remove($selector){
        $this->addAction('REMOVE', ['selector' => $selector]);
    }
    
    public function removeDataGrid($grid, $id){
        $this->addAction('REMOVEDATAGRID', ['grid' => $grid, 'id' => $id]);
    }
    
    public function execFunction($name){
        $this->addAction('EXEC-FUNCTION', ['name' => $name]);
    }
    
    public function addErrors($formName, $errors){
        $this->addAction('ADD-ERRORS', ['formName' => $formName, 'errors' => $errors]);
    }

    public function clearError($formName, $attribute){
        $this->addAction('CLEAR-ERROR', ['formName' => $formName, 'attr' => $attribute]);
    }
    
    public function removeClass($selector, $className){
        $this->addAction('REMOVE-CLASS', ['selector' => $selector, 'className' => $className]);
    }
    
    public function addClass($selector, $className){
        $this->addAction('ADD-CLASS', ['selector' => $selector, 'className' => $className]);
    }
    
    public function newTab($link){
        $this->addAction('NEW-TAB', ['link' => $link]);
    }

    public function execEvent($selector, $type){
        $this->addAction('EXEC-EVENT', ['selector' => $selector, 'type' => $type]);
    }

    public function append($selector, $data){
        $this->addAction('APPEND', ['selector' => $selector, 'data' => $data]);
    }

    public function prepend($selector, $data){
        $this->addAction('PREPEND', ['selector' => $selector, 'data' => $data]);
    }

    public function replaceWith($selector, $data){
        $this->addAction('REPLACE-WITH', ['selector' => $selector, 'data' => $data]);
    }

    public function fadeIn($selector, $duration = null){
        $this->addAction('FADEIN', ['selector' => $selector, 'duration' => $duration]);
    }

    public function fadeOut($selector, $duration = null){
        $this->addAction('FADEOUT', ['selector' => $selector, 'duration' => $duration]);
    }

    public function hide($selector){
        if(is_array($selector)){
            foreach($selector as $s){
                $this->addAction('HIDE', ['selector' => $s]);
            }
        }else{
            $this->addAction('HIDE', ['selector' => $selector]);
        }
    }

    public function show($selector){
        if(is_array($selector)){
            foreach($selector as $s){
                $this->addAction('SHOW', ['selector' => $s]);
            }
        }else{
            $this->addAction('SHOW', ['selector' => $selector]);
        }
    }

    public function animate($selector, $params){
        $this->addAction('ANIMATE', ['selector' => $selector, 'params' => $params]);
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

        $this->addAction('OPEN-MODAL', ['title' => 'Erro', 'data' => $html, 'size' => 0, 'type' => 'modal-sm']);
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
        $this->addAction('SWEET-ALERT', ['params' => $params, 'urlButtonConfirm' => $urlButtonConfirm, 'data' => $data]);
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

    public function addAction($action, $params = []){
        $action = ['action' => $action];
        foreach($params as $name => $value){
            $action[$name] = $value;
        }
        $this->actions[] = $action; 
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
        $this->addAction('GRITTER', ['params' => $params, 'options' => $options]);
    }
    
    public function send() {
        $rq = Yii::$app->request;
        if(count($this->actions) > 0 && ($rq->headers->has('X-JSCONTROL') || !$this->x_jscontrol)){
            echo Json::encode(['actions' => $this->actions, 'success' => $this->status]);
        }
        exit;
    }
}