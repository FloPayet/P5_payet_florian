<?php

require 'vendor/autoload.php';
session_start();

use Symfony\Component\HttpFoundation\Request;


$router = new Router;
$action = $router->getAction();
$select_control = $router->getController();
$controller = "App\\Controller\\$select_control";
$controller = new $controller($action);
$controller->run();

class Router
{
    public $action = null;
    public $select_control = null;
    private $request = null;

    public function __construct()
    {
        $this->request = new Request(
            $_GET,
            $_POST,
            [],
            $_COOKIE,
            $_FILES,
            $_SERVER
        );
        if ($this->request->query->get('action')) {
            if (!$this->request->query->get('controller'))
                $this->select_control = 'Default';
            else
                $this->select_control = $this->request->query->get('controller');
            $this->action = $this->request->query->get('action');
        } else {
            $this->action = 'index';
            $this->select_control = 'Default';
        }
    }

    public function getAction()
    {
        return 'render' . ucfirst($this->action);
    }

    public function getController()
    {
        return $this->select_control . 'Controller';
    }
}
