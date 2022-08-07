<?php

//namespace Model;
class DbManager {

    private static $db = null;
  
    public static function getPDO()
    {
        if (!self::$db) {
            self::$db = new PDO('mysql:host=localhost;dbname=blog;charset=utf8', 'root', '');
        }
        return self::$db;
    }
}