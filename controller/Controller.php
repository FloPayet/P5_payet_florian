<?php
    require 'vendor/autoload.php';

    use Twig\Environment;
    use Twig\Loader\FilesystemLoader;
    use Symfony\Component\HttpFoundation\Request;


    class Controller {

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

        private function renderIndex() { 
            $postlist = $this->post->getlist();
            $userlist = $this->user->getlist();
            echo $this->twig->render('index.html.twig', ['postlists' => $postlist, 'userlist' => $userlist, 'session' => $_SESSION]);
        }

        private function renderCreatPost() {
            $post = "";
            if(isset($_GET['update'])) {
                $post = $this->post->getPost($this->request->query->get('update'));
            }
            echo $this->twig->render('creatpost.html.twig', ['session' => $_SESSION, 'post' => $post]);
        }

        private function renderSignIn() {
            if($_SESSION)
                call_user_func(array($this, 'renderIndex'));
            else
                echo $this->twig->render('wizard-build-profile.html.twig', ['session' => $_SESSION]);
        }

        private function renderPost() {
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                $this->comment->content = $this->request->request->get("comment");
                $this->comment->user_id = $_SESSION["id"];
                $this->comment->post_id = $this->request->query->get('post_id');
                $this->comment->insertComment();
            }
            if(isset($_GET['post_id'])) {
                $commentlist = $this->comment->getComment($this->request->query->get('post_id'));
                $post = $this->post->getpost($this->request->query->get('post_id'));
                $user = $this->user->getlist();
                echo $this->twig->render('post.html.twig', ['commentlist' => $commentlist, 'post' => $post, 'userlist' => $user, 'session' => $_SESSION]);
            }
            else
                echo $this->twig->render('error.html.twig', ['session' => $_SESSION]);
        }

        private function renderDashbord() {
            $postlist = $this->post->getlist();
            $comment = $this->comment->getList();
            $user = $this->user->getlist();
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                if ($this->request->request->keys() == 'disconnect') {
                    session_destroy();
                }
                if ($this->request->request->keys() == 'delete') {
                    $id = $_POST['delete'];
                    $this->post->deletePost($id);
                }
                if ($this->request->request->keys() == 'delete_com') {
                    $id = $_POST['delete_com'];
                    $this->comment->deleteComment($id);
                }
                if ($this->request->request->keys() == 'valid') {
                    $id = $_POST['valid'];
                    $this->comment->updateComment($id);
                }
            }
            if($_SESSION)
                echo $this->twig->render('dashbord.html.twig', ['session' => $_SESSION, 'postlists' => $postlist, 'commentlist' => $comment, 'userlist' => $user]);
            else
                echo $this->twig->render('error.html.twig', ['session' => $_SESSION]);
        }

        private function renderSuccesPost() {
            $image_name = "";
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_GET['update'])) {
                    $this->post->title =  $this->request->request->get("title");
                    $this->post->header = $this->request->request->get("header");
                    $this->post->content =  $this->request->request->get("post");
                    $this->post->user_id = $_SESSION['id'];
                    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        move_uploaded_file($_FILES['image']['tmp_name'], './img_upload/'.$_FILES['image']['name']);
                        $image_name = 'img_upload/'.$_FILES['image']['name'];
                    }
                    $this->post->image = $image_name;
                    $this->post->updatePost($_GET['update']);
                } else {
                        $this->post->title =  $this->request->request->get("title");
                        $this->post->header =  $this->request->request->get("header");
                        $this->post->content =  $this->request->request->get("post");
                        $this->post->user_id = $_SESSION['id'];
                        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                            move_uploaded_file($_FILES['image']['tmp_name'], './img_upload/'.$_FILES['image']['name']);
                            $image_name = 'img_upload/'.$_FILES['image']['name'];
                        }
                        $this->post->image = $image_name;
                        $this->post->insertPost();
                    }
            }
            echo $this->twig->render('succes.html.twig', ['session' => $_SESSION]);
        }
        
        private function renderSuccesSignin() {
            $error = array();
            $count = 0;
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                if($this->user->checkExist( $this->request->request->get("username"), 'user_name')) {
                    array_push($error, 'This username already exist');
                    $count = 1;
                }
                if($this->user->checkExist( $this->request->request->get("email"), 'email')) {
                    array_push($error, 'This email already exist');
                    $count = 1;
                }
                if($count != 0)
                    echo $this->twig->render('wizard-build-profile.html.twig', ['error' => $error, 'session' => $_SESSION]);
                else {
                $this->user->user_name =  $this->request->request->get("username");
                $this->user->email =  $this->request->request->get("email");
                $this->user->country =  $this->request->request->get("country");
                $this->user->password =  $this->request->request->get("password");
                $this->user->date_of_birth =  $this->request->request->get("date_of_birth");
                $this->user->postal_code =  $this->request->request->get("postal_code");
                $this->user->town =  $this->request->request->get("town");
                $this->user->insertUser();
                echo $this->twig->render('succes.html.twig', ['session' => $_SESSION]);
                }
            }
        }

        private function renderLogIn() {
            if($_SESSION)
                call_user_func(array($this->renderIndex()));
            else
                echo $this->twig->render('login.html.twig', ['session' => $_SESSION]);
        }

        private function renderSuccesLogin() {
            $user = $this->user->login($_POST["log_username"], $_POST["log_password"]);
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                if($user) {
                        $_SESSION['id'] = $user[0]['id'];
                        $_SESSION['user_name'] = $user[0]['user_name'];
                        $_SESSION['date_of_birth'] = $user[0]['date_of_birth'];
                        $_SESSION['country'] = $user[0]['country'];
                        $_SESSION['postal_code'] = $user[0]['postal_code'];
                        $_SESSION['town'] = $user[0]['town'];
                        $_SESSION['admin'] = $user[0]['admin'];
                    }
            }
            if($_SESSION) {
                call_user_func(array($this, 'renderIndex'));
            }
            else
                echo $this->twig->render('login.html.twig', ['error' => 'wrong username or password']);
        }
    }

    