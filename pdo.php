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
    
    function pdo_hrdb(){
        if(!isset($_SESSION)){
            session_start();
        }
        // tw074164p => 環安正式區
        $db_host = "10.53.90.163,1433";
        $db_user = "tnesh";
        $db_pw = "&9t040500";
        $db_name = "hrdb";
        $db_charset = "utf8mb4";
        
        try {
            $dsn = "sqlsrv:Server={$db_host};Database={$db_name};encrypt=false;";           // msSQL  ..encrypt=false;解決憑證不受信任問題
            $pdo = new PDO($dsn, $db_user, $db_pw);
            // 设置 PDO 错误模式为异常
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "连接成功";
        }catch(PDOException $e){
            // print_r($e->getMessage());
            die("连接失败: " . $e->getMessage());
        }

        return $pdo;
    }