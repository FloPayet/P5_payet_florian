<?php

//namespace user_model;
//require('DbManager.php');

    class UserModel {

        public $id = null;
        public $email = "";
        public $user_name = "";
        public $password = "";
        public $date_of_birth = "";
        public $country = "";
        public $postal_code = "";
        public $town = "";
        public $admin = 0;
        public $active = 1;

        public function __construct($id = null)
        {
            if ($id != NULL || $id != 0) {

                $this->id = $id;
            }
        }

        public function insertUser()
        {
            $sqlQuery = 'INSERT INTO user(email, user_name, password, date_of_birth, country, postal_code, town, admin) 
            VALUES (:email, :user_name, :password, :date_of_birth, :country, :postal_code, :town, :admin)';
            $insertUser = DbManager::getPDO()->prepare($sqlQuery);
            $insertUser->execute([
                'email' => $this->email,
                'user_name' => $this->user_name,
                'password' => $this->password,
                'date_of_birth' => $this->date_of_birth,
                'country' => $this->country,
                'postal_code' => $this->postal_code,
                'town' => $this->town,
                'admin' => $this->admin,
            ]);
        }

        public function deleteUser() 
        {
            $sqlQuery = 'DELETE FROM user WHERE id = :id';
            $deleteUSer = DbManager::getPDO()->prepare($sqlQuery);
            $deleteUSer->execute([
                'id' => $this->id,
            ]);
        }

        public function checkExist($value, $cat) {
            $sqlQuery = "SELECT $cat FROM user WHERE $cat='$value'";
            return DbManager::getPDO()
            ->query($sqlQuery)->fetchAll(PDO::FETCH_ASSOC);
        }

        public function login($username, $password) {
            $sqlQuery = "SELECT * FROM user WHERE user_name='$username' and password='$password'";
            return DbManager::getPDO()
            ->query($sqlQuery)->fetchAll(PDO::FETCH_ASSOC);
        }

        public function add_user() {
            $this->user_name = $_POST["username"];
            $this->email = $_POST["email"];
            $this->country = $_POST["country"];
            $this->password = $_POST["password"];
            $this->date_of_birth = $_POST["date_of_birth"];
            $this->postal_code = $_POST["postal_code"];
            $this->town = $_POST["town"];
            $this->admin = 0;
            $this->insertUser();                
        }

        public function getlist() {
            $sqlQuery = 'SELECT id, user_name, email, password, date_of_birth, country, postal_code, town, admin FROM user';
            return DbManager::getPDO()
            ->query($sqlQuery)->fetchAll(PDO::FETCH_ASSOC);
        }
    }