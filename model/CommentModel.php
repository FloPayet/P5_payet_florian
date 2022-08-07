<?php

class CommentModel {
    public $content = "";
    public $user_id = "";
    public $post_id = "";
    public $posted = 0;

    public function insertComment() {
        if(!$this->user_id)
            $this->user_id = 0;
        if(!$this->post_id)
            $this->post_id = 0;
        $sqlQuery = 'INSERT INTO comment(content, user_id, post_id, posted) 
        VALUES (:content, :user_id, :post_id, :posted)';
        $insertPost = DbManager::getPDO()->prepare($sqlQuery);
        $insertPost->execute([
            'content' => $this->content,
            'user_id' => $this->user_id,
            'post_id' => $this->post_id,
            'posted' => $this->posted,
        ]);
    }

    public function deleteComment($id) 
    {
        $sqlQuery = "DELETE FROM comment WHERE id = '$id'";
        $deletePost = DbManager::getPDO()->prepare($sqlQuery);
        $deletePost->execute([
            'id' => $id,
        ]);
    }

    public function updateComment($id)
    {
        $posted = 1;
        $sqlQuery = "UPDATE comment SET posted = 1
        WHERE id = '$id'";
        $updatePost = DbManager::getPDO()->prepare($sqlQuery);
        $updatePost->execute([
            'posted' => 1,
        ]);
    }

    public function getList() {
        $sqlQuery = "SELECT id, date, content, user_id , post_id, posted FROM comment";
        return DbManager::getPDO()
        ->query($sqlQuery)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getComment($id) {
        $sqlQuery = "SELECT date, content, user_id , post_id, posted FROM comment WHERE post_id = '$id'";
        return DbManager::getPDO()
        ->query($sqlQuery)->fetchAll(PDO::FETCH_ASSOC);
    }
}