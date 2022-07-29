<?php
    session_start();
    require('vendor/autoload.php');
    require('model/DbManager.php');
    require('controller/Controller.php');
    require('model/UserModel.php');
    require('model/PostModel.php');
    require('model/CommentModel.php');

    $router = new Router;
    $action = $router->getAction();
    $controller = new Controller($action);

    $controller->run();
    
    class Router {
        public $action = null;

        public function __construct() {
            if (isset($_GET['action']))
                $this->action = $_GET['action'];
            else   
                $this->action = 'index';
        }

        public function getAction() {
            return 'render'.ucfirst($this->action);
        }
    }

    