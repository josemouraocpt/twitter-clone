<?php 
namespace App\controllers;

use MF\Controller\Action;
use MF\Model\Container;


class AppController extends Action{

    public function timeline(){
        $this->validaAutenticacao();

        $tweet = Container::getModel('tweet');
        $usuario = Container::getModel('usuario');

        $tweet->__set('id_usuario', $_SESSION['id']);
        $usuario->__set('id', $_SESSION['id']);

        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();


        $this->view->tweets = $tweet->getAll();

        $this->render("timeline", "layout");
    }

    public function tweet(){
        $this->validaAutenticacao();

        $tweet = Container::getModel('tweet');
        
        $tweet->__set('tweet', $_POST['tweet']);
        $tweet->__set('id_usuario', $_SESSION['id']);

        $tweet->salvar();
        header("Location: /timeline");
    }   

    public function quemSeguir(){
        $this->validaAutenticacao();

        $pesquisar_por = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

        $usuarios = [];
        
        if($pesquisar_por != ''){
            $usuario = Container::getModel('usuario');
            $usuario->__set('nome', $pesquisar_por);
            $usuario->__set('id', $_SESSION['id']);
            $usuarios = $usuario->getAll();
        }

        $usuario = Container::getModel('usuario');
        $this->view->usuarios = $usuarios;

        $usuario->__set('id', $_SESSION['id']);

        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

        
        $this->render("quemSeguir", "layout");
    }


    public function acao(){
        $this->validaAutenticacao();
        $acao = isset($_GET['acao']) ? $_GET['acao'] : ''; 
        $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : ''; 

        $usuario = Container::getModel('usuario');
        $usuario->__set('id', $_SESSION['id']);

        if($acao == 'seguir'){
            $usuario->seguir($id_usuario_seguindo);
        } else if($acao == 'deixar_de_seguir'){
            $usuario->deixarDeSeguir( $id_usuario_seguindo);
        }
        header("Location: /quem_seguir");
    }


    public function deletarTweet(){
        $this->validaAutenticacao();

        $tweet = Container::getModel('tweet');

        $tweet->__set('id_usuario', $_SESSION['id']);

        $id_delete = $_GET['id_delete'];

        $tweet->deleta($id_delete);

        header("Location: /timeline");
    }
    

    public function validaAutenticacao(){
        session_start();
        if($_SESSION['id'] != '' && $_SESSION['nome'] != ''){
            return true;
        }else{
            header("Location: /?login=erro");
        }
    }
}

?>