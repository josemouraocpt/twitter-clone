<?php
    namespace MF\Controller;

    abstract class Action{
        protected $view;

        public function __construct(){
            $this->view = new \stdClass();
        }

        protected function render($view, $layout){
            $this->view->page = $view;
            if(file_exists("../app/views/".$layout.".phtml")){
                require_once "../app/views/".$layout.".phtml";
            } else{
                $this->content();
            }    
          
        }

        protected function content(){
            $classeAtual = get_class($this);
            $classeAtual = str_replace("App\\controllers\\", "", $classeAtual);
            $classeAtual = strtolower(str_replace('Controller', '', $classeAtual));
            
            require_once "../app/views/".$classeAtual."/". $this->view->page.".phtml";
        }
    }
?>