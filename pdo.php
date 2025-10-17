<?php
    date_default_timezone_set("Asia/Taipei"); 
    $sys_id = "carux_ppe";
    $config = require 'pdo_config.php';

    function pdo(){
        if(!isset($_SESSION)){
            session_start();
        }
        global $sys_id;
        global $config;
            $db_name    = $sys_id;
            $db_host    = $config["db_host"];
            $db_charset = $config["db_charset"];
            $db_user    = $config["db_user"];
            $db_pw      = $config["db_pass"];
        
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
        global $config;
            // tw074164p => 環安正式區
            $db_host    = $config["hrdb_host"];
            $db_name    = $config["hrdb_name"];
            $db_charset = $config["hrdb_charset"];
            $db_user    = $config["hrdb_user"];
            $db_pw      = $config["hrdb_pass"];

        try {
            $dsn = "sqlsrv:Server={$db_host};Database={$db_name};encrypt=false;";           // msSQL  ..encrypt=false;解決憑證不受信任問題
            // $pdo = new PDO($dsn, $db_user, $db_pw);
            // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // 设置 PDO 错误模式为异常
            $pdo = new PDO($dsn, $db_user, $db_pw, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 2 // 設定連線超時為 2 秒
            ]);
            // echo "hrDB连接成功...";
        }catch(PDOException $e){
            // echo "hrDB資料庫連線失敗: " . $e->getMessage() . "\n嘗試連接到localhost資料庫...";
            try {
                $dsn = "mysql:host=localhost;dbname={$db_name};charset={$db_charset}";
                $pdo = new PDO($dsn, $db_user, $db_pw);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // echo "localhost連接成功..."; // 建議在生產環境中去除
            }catch(PDOException $e){
                die("hrDB&localhost连接失败: " . $e->getMessage());
            }
        }

        return $pdo;
    }