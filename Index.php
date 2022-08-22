<?php
    session_start();
    use Symfony\Component\HttpFoundation\Request;
    require 'vendor/autoload.php';
    require 'model/DbManager.php';
    require 'controller/Controller.php';
    require 'model/UserModel.php';
    require 'model/PostModel.php';
    require 'model/CommentModel.php';


    $router = new Router;
    $action = $router->getAction();
    $controller = new Controller($action);

    $controller->run();
    
    class Router {
        public $action = null;
        private $request = null;

        public function __construct() {
            $this->request = new Request(
                $_GET,
                $_POST,
                [],
                $_COOKIE,
                $_FILES,
                $_SERVER
            );
            if ($this->request->query->get('action'))
                $this->action = $this->request->query->get('action');
            else   
                $this->action = 'index';
        }

        public function getAction() {
            return 'render'.ucfirst($this->action);
        }
    }

    