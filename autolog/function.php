<?php

    function accessDeniedAdmin(){
        session_start();
        if(!isset($_SESSION["AUTH"]) || $_SESSION["AUTH"]["role"] != 0){
            header('location:../view/index.php');
            return;
        }
    }
   
    function show_log_list(){
        $pdo = pdo();
        $sql = "SELECT autolog.*
                FROM `autolog`
                ORDER BY autolog.t_stamp DESC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $log_lists = $stmt->fetchAll();
            return $log_lists;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    // fun-2.showLog 讀取log = R
    function showLog($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT autolog.*
                FROM autolog
                WHERE autolog.id = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $showLog = $stmt->fetch();
            return $showLog;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    // fun-1.storeLog 儲存log = C
    function storeLog($request){
        $pdo = pdo();
        extract($request);
        $sql = "INSERT INTO autolog( sys, remark, t_stamp)VALUES(?, ?, ?) ";
        if(empty($t_stamp)){
            $t_stamp = date("Y-m-d\TH:i");
        }
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$sys, $remark, $t_stamp]);
            return "storeLog done";
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    // fun-3.deleteLog 刪除log = D
    function deleteLog($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM `autolog` WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            return "deleteLog done";
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    // fun-4.updateLog 更新log = U
    function updateLog($request){
        $pdo = pdo();
        extract($request);
        $sql = "UPDATE `autolog`
                SET sys=?, remark=?
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$sys, $remark, $id]);
            return "updateLog done";
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }