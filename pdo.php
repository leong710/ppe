<?php
    date_default_timezone_set("Asia/Taipei"); 
    $sys_id = "ppe";

    function pdo(){
        if(!isset($_SESSION)){
            session_start();
        }
        $db_host = "localhost";
        $db_user = "tnesh";
        $db_pw = "&9t040500";
        $db_name = "ppe";
        $db_charset = "utf8mb4";
        
        try {
            $dsn = "mysql:host={$db_host};dbname={$db_name};charset={$db_charset}";
            $pdo = new PDO($dsn, $db_user, $db_pw);
        }catch(PDOException $e){
            print_r($e->getMessage());
        }

        return $pdo;
    }
    