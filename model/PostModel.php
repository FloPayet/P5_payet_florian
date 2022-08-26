<?php

namespace App\Model;

class PostModel
{

    public $id = null;
    public $title = "";
    public $header = "";
    public $content = "";
    public $user_id = "";
    public $image = "";

    public function __construct($id = null)
    {
        if ($id != NULL || $id != 0) {

            $this->id = $id;
            //ici reqette recuperer les données de l'user
            //$this->email = $email; 
        }
    }

    public function insertPost()
    {
        $sqlQuery = 'INSERT INTO post(title, header, content, user_id, image) 
        VALUES (:title, :header, :content, :user_id, :image)';
        $insertPost = DbManager::getPDO()->prepare($sqlQuery);
        $insertPost->execute([
            'title' => $this->title,
            'header' => $this->header,
            'content' => $this->content,
            'user_id' => $this->user_id,
            'image' => $this->image,
        ]);
    }
    public function deletePost($id)
    {
        $sqlQuery = "DELETE FROM post WHERE id = '$id'";
        $deletePost = DbManager::getPDO()->prepare($sqlQuery);
        $deletePost->execute([
            'id' => $id,
        ]);
    }

    public function updatePost($id)
    {
        $sqlQuery = "UPDATE post SET title = :title, header = :header, content = :content, user_id = :user_id, image = :image
        WHERE id = '$id'";
        $updatePost = DbManager::getPDO()->prepare($sqlQuery);
        $updatePost->execute([
            'title' => $this->title,
            'header' => $this->header,
            'content' => $this->content,
            'user_id' => $this->user_id,
            'image' => $this->image,
        ]);
    }

    public function getlist()
    {
        $sqlQuery = 'SELECT id, title, header, content, date_creat, date_last_update, image, user_id FROM post ORDER BY date_last_update DESC';
        return DbManager::getPDO()
            ->query($sqlQuery)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPost($id)
    {
        $sqlQuery = "SELECT id, content, date_creat , date_last_update, title, header, user_id, image  FROM post WHERE id = '$id'";
        return DbManager::getPDO()
            ->query($sqlQuery)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
