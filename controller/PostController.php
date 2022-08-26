<?php

namespace App\Controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\HttpFoundation\Request;

class PostController extends DefaultController
{

    public function renderCreatPost()
    {
        $post = "";
        if ($this->request->query->get('update')) {
            $post = $this->post->getPost($this->request->query->get('update'));
        }
        echo $this->twig->render('creatpost.html.twig', ['session' => $_SESSION, 'post' => $post]);
    }

    public function renderPost()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->comment->content = $this->request->request->get("comment");
            $this->comment->user_id = $_SESSION["id"];
            $this->comment->post_id = $this->request->query->get('post_id');
            $this->comment->insertComment();
        }
        if ($this->request->query->get('post_id')) {
            $commentlist = $this->comment->getComment($this->request->query->get('post_id'));
            $post = $this->post->getpost($this->request->query->get('post_id'));
            $user = $this->user->getlist();
            echo $this->twig->render('post.html.twig', ['commentlist' => $commentlist, 'post' => $post, 'userlist' => $user, 'session' => $_SESSION]);
        } else
            echo $this->twig->render('error.html.twig', ['session' => $_SESSION]);
    }

    public function renderSuccesPost()
    {
        $image_name = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($this->request->query->get('update')) {
                $this->post->title =  $this->request->request->get("title");
                $this->post->header = $this->request->request->get("header");
                $this->post->content =  $this->request->request->get("post");
                $this->post->user_id = $_SESSION['id'];
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    move_uploaded_file($_FILES['image']['tmp_name'], './img_upload/' . $_FILES['image']['name']);
                    $image_name = 'img_upload/' . $_FILES['image']['name'];
                }
                $this->post->image = $image_name;
                $this->post->updatePost($this->request->query->get('update'));
            } else {
                $this->post->title =  $this->request->request->get("title");
                $this->post->header =  $this->request->request->get("header");
                $this->post->content =  $this->request->request->get("post");
                $this->post->user_id = $_SESSION['id'];
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    move_uploaded_file($_FILES['image']['tmp_name'], './img_upload/' . $_FILES['image']['name']);
                    $image_name = 'img_upload/' . $_FILES['image']['name'];
                }
                $this->post->image = $image_name;
                $this->post->insertPost();
            }
        }
        echo $this->twig->render('succes.html.twig', ['session' => $_SESSION]);
    }
}
