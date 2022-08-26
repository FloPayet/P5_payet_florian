<?php

    require 'vendor/autoload.php';
    use Twig\Environment;
    use Twig\Loader\FilesystemLoader;
    use Symfony\Component\HttpFoundation\Request;

    class IndexController {
        public $action = null;
        public $twig;
        public $post;
        public $user;
        public $comment;
        private $request;

        public function __construct($action) {
            $loader = new FilesystemLoader(__DIR__ . '/../template');
            $this->twig = new Environment($loader);
            $this->action = $action;
            $this->post = new PostModel;
            $this->user = new UserModel;
            $this->comment = new CommentModel;
            $this->request = new Request(
                $_GET,
                $_POST,
                [],
                $_COOKIE,
                $_FILES,
                $_SERVER
            );
        }

        public function run() {
            if(method_exists($this, $this->action)) {
                call_user_func(array($this, $this->action));
            }
            else
                $this->renderError();
        }
        
        private function renderError() {
            echo $this->twig->render('error.html.twig', ['session' => $_SESSION]);
        }

        protected function renderIndex() { 
            $postlist = $this->post->getlist();
            $userlist = $this->user->getlist();
            echo $this->twig->render('index.html.twig', ['postlists' => $postlist, 'userlist' => $userlist, 'session' => $_SESSION]);
        }
    }