<?php
    session_start();
    use Symfony\Component\HttpFoundation\Request;
    require 'vendor/autoload.php';
    require 'model/DbManager.php';
    //require 'controller/Controller.php';
    require 'controller/IndexController.php';
    require 'controller/PostController.php';
    require 'controller/UserController.php';
    require 'model/UserModel.php';
    require 'model/PostModel.php';
    require 'model/CommentModel.php';


    $router = new Router;
    $action = $router->getAction();
    $select_control = $router->getController();
    $controller = new $select_control($action);
    $controller->run();
    
    class Router {
        public $action = null;
        public $select_control = null;
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
            if ($this->request->query->get('action')) {
                $this->action = $this->request->query->get('action');
                $this->select_control = $this->request->query->get('controller');
            }
            else { 
                $this->action = 'index';
                $this->select_control = 'Index';
            }
        }

        public function getAction() {
            return 'render'.ucfirst($this->action);
        }

        public function getController() {
            return $this->select_control.'Controller';
        }
    }

    