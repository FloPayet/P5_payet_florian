<?php
    require('vendor/autoload.php');

    use Twig\Environment;
    use Twig\Loader\FilesystemLoader;


    class Controller {

        public $action = null;
        public $twig;
        public $post;
        public $user;
        public $comment;

        public function __construct($action) {
            $loader = new FilesystemLoader(__DIR__ . '/../template');
            $this->twig = new Environment($loader);
            $this->action = $action;
            $this->post = new PostModel;
            $this->user = new UserModel;
            $this->comment = new CommentModel;
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
                $post = $this->post->getPost($_GET['update']);
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
                $this->comment->content = $_POST["comment"];
                $this->comment->user_id = $_SESSION["id"];
                $this->comment->post_id = $_GET['post_id'];
                $this->comment->insertComment();
            }
            if(isset($_GET['post_id'])) {
                $commentlist = $this->comment->getComment($_GET['post_id']);
                $post = $this->post->getpost($_GET['post_id']);
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
                if (key($_POST) == 'disconnect') {
                    session_destroy();
                }
                if (key($_POST) == 'delete') {
                    $id = $_POST['delete'];
                    $this->post->deletePost($id);
                }
                if (key($_POST) == 'delete_com') {
                    $id = $_POST['delete_com'];
                    $this->comment->deleteComment($id);
                }
                if (key($_POST) == 'valid') {
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
                    $this->post->title = $_POST["title"];
                    $this->post->header = $_POST["header"];
                    $this->post->content = $_POST["post"];
                    $this->post->user_id = $_SESSION['id'];
                    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        move_uploaded_file($_FILES['image']['tmp_name'], './img_upload/'.$_FILES['image']['name']);
                        $image_name = 'img_upload/'.$_FILES['image']['name'];
                    }
                    $this->post->image = $image_name;
                    $this->post->updatePost($_GET['update']);
                } else {
                        $this->post->title = $_POST["title"];
                        $this->post->header = $_POST["header"];
                        $this->post->content = $_POST["post"];
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
                if($this->user->checkExist($_POST["username"], 'user_name')) {
                    array_push($error, 'This username already exist');
                    $count = 1;
                }
                if($this->user->checkExist($_POST["email"], 'email')) {
                    array_push($error, 'This email already exist');
                    $count = 1;
                }
                if($count != 0)
                    echo $this->twig->render('wizard-build-profile.html.twig', ['error' => $error, 'session' => $_SESSION]);
                else {
                $this->user->user_name = $_POST["username"];
                $this->user->email = $_POST["email"];
                $this->user->country = $_POST["country"];
                $this->user->password = $_POST["password"];
                $this->user->date_of_birth = $_POST["date_of_birth"];
                $this->user->postal_code = $_POST["postal_code"];
                $this->user->town = $_POST["town"];
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

    