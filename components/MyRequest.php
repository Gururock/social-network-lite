<?php

namespace app\components;



        

class MyRequest extends \yii\web\Request{

    private $_requestUri;
    public $enableCsrfValidation;

    public function init() {
        
        
        
            $this->enableCsrfValidation = false; 
        return parent::init();    
    }

   

}
