<?php

namespace App\Controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\HttpFoundation\Request;

class UserController extends DefaultController
{

    public function renderSignIn()
    {
        if ($_SESSION)
            parent::renderIndex();
        else
            echo $this->twig->render('wizard-build-profile.html.twig', ['session' => $_SESSION]);
    }

    public function renderDashbord()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($this->request->request->keys()[0] == 'disconnect') {
                session_destroy();
            }
            if ($this->request->request->keys()[0] == 'delete') {
                $id =  $this->request->request->get('delete');
                $this->post->deletePost($id);
            }
            if ($this->request->request->keys()[0] == 'delete_com') {
                $id =  $this->request->request->get('delete_com');
                $this->comment->deleteComment($id);
            }
            if ($this->request->request->keys()[0] == 'valid') {
                $id =  $this->request->request->get('valid');
                $this->comment->updateComment($id);
            }
        }
        $postlist = $this->post->getlist();
        $comment = $this->comment->getList();
        $user = $this->user->getlist();
        if ($_SESSION)
            echo $this->twig->render('dashbord.html.twig', ['session' => $_SESSION, 'postlists' => $postlist, 'commentlist' => $comment, 'userlist' => $user]);
        else
            echo $this->twig->render('error.html.twig', ['session' => $_SESSION]);
    }

    public function renderSuccesSignin()
    {
        $error = array();
        $count = 0;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($this->user->checkExist($this->request->request->get("username"), 'user_name')) {
                array_push($error, 'This username already exist');
                $count = 1;
            }
            if ($this->user->checkExist($this->request->request->get("email"), 'email')) {
                array_push($error, 'This email already exist');
                $count = 1;
            }
            if ($count != 0)
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

    public function renderLogIn()
    {
        if ($_SESSION)
            parent::renderIndex();
        else
            echo $this->twig->render('login.html.twig', ['session' => $_SESSION]);
    }

    public function renderSuccesLogin()
    {
        $user = $this->user->login($this->request->request->get("log_username"),  $this->request->request->get("log_password"));
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($user) {
                $_SESSION['id'] = $user[0]['id'];
                $_SESSION['user_name'] = $user[0]['user_name'];
                $_SESSION['date_of_birth'] = $user[0]['date_of_birth'];
                $_SESSION['country'] = $user[0]['country'];
                $_SESSION['postal_code'] = $user[0]['postal_code'];
                $_SESSION['town'] = $user[0]['town'];
                $_SESSION['admin'] = $user[0]['admin'];
            }
        }
        if ($_SESSION) {
            parent::renderIndex();
        } else
            echo $this->twig->render('login.html.twig', ['error' => 'wrong username or password']);
    }
}
