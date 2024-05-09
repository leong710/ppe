<?php

// // // index用到
    // 20230818 嵌入分頁工具
    function show_log_list($request){
        $pdo = pdo();
        $stmt_arr = array();
        extract($request);
        $sql = "SELECT al.*
                FROM `autolog` al ";
        
        if($_year != 'All'){
            $sql .= " WHERE year(al.thisDay) = ? ";
            array_push($stmt_arr, $_year);
        }
        if($_month != 'All'){
            $sql .= ($_year != "All" ? " AND ":" WHERE ") ;
            $sql .= " month(al.thisDay) = ? ";
            array_push($stmt_arr, $_month);
        }
        $sql .= " ORDER BY al.thisDay DESC ";
        // 決定是否採用 page_div 20230803
            if(isset($start) && isset($per)){
                $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料=分頁
            }else{
                $stmt = $pdo->prepare($sql);                // 讀取全部=不分頁
            }  
       
        try {
            if(($_year != 'All') || ($_month != "All")){
                $stmt->execute($stmt_arr);                          //處理 byUser & byYear
            }else{
                $stmt->execute();
            }
            $log_lists = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $log_lists;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 顯示全部by年月 => 供查詢年月份使用 20240112
    function show_log_GB_year(){
        $pdo = pdo();
        $sql = "SELECT DISTINCT year(al.thisDay) AS _year
                FROM `autolog` al
                GROUP BY al.thisDay
                ORDER BY _year DESC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $log_list_ym = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $log_list_ym;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
// // // autoLog CRUD
    // fun-1.storeLog 儲存log = C
    function storeLog($request){
        $pdo = pdo();
        extract($request);
        
        if(empty($t_stamp)){
            $t_stamp = date("Y-m-d\TH:i");
        }
        
        // 先確認是否有舊資料
            $sql_check = "SELECT al.*
                          FROM autolog al
                          WHERE al.thisDay = ? AND al.sys = ? ";
            $stmt_check = $pdo -> prepare($sql_check);
            $stmt_check -> execute([$thisDay, $sys]);

        if($stmt_check -> rowCount() >0){       // 已有舊資料
            $row = $stmt_check -> fetch();
            // 舊logs處理
                $row_logs_dec = json_decode($row["logs"]);                              // 1.先將-舊logs JSON純文字解碼成物件
                if(is_object($row_logs_dec)) { $row_logs_dec = (array)$row_logs_dec; }  // 2.將整個logs物件轉成陣列
                $row_autologs = (array)$row_logs_dec["autoLogs"];                       // 3.將舊logs下的autoLogs轉成陣列，放入$row_autologs
            // 新logs處理
                $logs_dec = json_decode($logs);                                         // 1.先將-新logs JSON純文字解碼成物件
                if(is_object($logs_dec)) { $logs_dec = (array)$logs_dec; }              // 2.將物件轉成陣列
                $logs_autoLogs = $logs_dec["autoLogs"];                                 // 3.取其中的autoLogs
                // 整合新舊autoLogs
                forEach($logs_autoLogs AS $key => $value){                              // 4.用繞的方式，取單筆值$value  ($key暫時用不到)
                    array_push($row_autologs, $value);                                  // 5.將新的塞進舊的$row_autologs 
                }

            // 製作log紀錄前處理：塞進去製作元素
                $logs_process["thisDay"]  = $row_logs_dec["thisDay"];                   // 表單狀態
                $logs_process["autoLogs"] = $row_autologs;                              // 帶入新舊整合好的$row_autoLogs
                $logs_enc = json_encode($logs_process);                                 // 1.將陣列編碼成儲存用JSON字串
                $logs_enc = str_replace(array("\r\n","\r","\n"), "_rn_", $logs_enc);    // 2.去除字串內的換行字元

            $sql = "UPDATE autolog 
                    SET logs = ? , t_stamp = ? 
                    WHERE id = ? ";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$logs_enc, $t_stamp, $row["id"]]);
                $result = "UPDATE sucess";
            }catch(PDOException $e){
                echo $e->getMessage();
                $result = "UPDATE error";
            }

        }else{      // 開新資料
            // 新資料處理
                $logs_dec = json_decode($logs);                                         // 1.先將純文字解碼成JSON
                $logs_enc = json_encode($logs_dec);                                     // 2.將JSON編碼成儲存用字串
                $logs_enc = str_replace(array("\r\n","\r","\n"), "_rn_", $logs_enc);    // 3.去除字串內的換行字元

            $sql = "INSERT INTO autolog(thisDay, sys, logs, t_stamp)
                    VALUES(?, ?, ?, ?) ";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$thisDay, $sys, $logs_enc, $t_stamp]);
                $result = "INSERT sucess";
            }catch(PDOException $e){
                echo $e->getMessage();
                $result = "INSERT error";
            }
        }
        return "storeLog ... ".$result;
    }
    // fun-2.showLog 讀取log = R
    function showLog($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT al.*
                FROM autolog al
                WHERE al.id = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $showLog = $stmt->fetch(PDO::FETCH_ASSOC);
            return $showLog;
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
            $result = "sucess";
        }catch(PDOException $e){
            echo $e->getMessage();
            $result = "error";
        }
        return "deleteLog ... ".$result;
    }
    // fun-4.updateLog 更新log = U == 暫時用不到
    function updateLog($request){
        $pdo = pdo();
        extract($request);
        $sql = "UPDATE `autolog`
                SET thisDay=?, sys=?, logs=?
                WHERE id=? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$thisDay, $sys, $logs, $id]);
            $result = "sucess";
        }catch(PDOException $e){
            echo $e->getMessage();
            $result = "error";
        }
        return "updateLog ... ".$result;
    }

// // // Log tools
    // 刪除單項log值-20231215
    function delLog_item($request){
        $pdo = pdo();
        extract($request);
        $query = array('id'=> $id );
        // 把_receive表單叫近來處理
            $row = showLog($query);
            $row_logs_dec = json_decode($row["logs"]);                              // 1.先將-舊logs JSON純文字解碼成物件
            if(is_object($row_logs_dec)) { $row_logs_dec = (array)$row_logs_dec; }  // 2.將整個logs物件轉成陣列
            $row_autologs = (array)$row_logs_dec["autoLogs"];                       // 3.將舊logs下的autoLogs轉成陣列，放入$row_autologs
        // 刪除單項
            array_splice($row_autologs, $log_id, 1);                                // 用這個刪除順序內的item不會產生index
        // 製作log紀錄前處理：塞進去製作元素
            $logs_process["thisDay"]  = $row_logs_dec["thisDay"];                   // 1.表單狀態
            $logs_process["autoLogs"] = $row_autologs;                              // 2.帶入新舊整合好的$row_autoLogs
            $logs_enc = json_encode($logs_process);                                 // 3.將陣列編碼成儲存用JSON字串

        $sql = "UPDATE autolog 
                SET logs = ? 
                WHERE id = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$logs_enc, $id]);
            $result = "UPDATE sucess";
        }catch(PDOException $e){
            echo $e->getMessage();
            $result = "UPDATE error";
        }
        return "delLog_item ... ".$result;
    }

    // 整合+製作記錄JSON_Log檔 == 暫時用不到 = 參考用
    function toLog($request){
        extract($request);

        if(!isset($logs)){
            $logs = [];
            $logs_arr =[];
        }else{
            $logs_dec = json_decode($logs);
            $logs_arr = (array) $logs_dec;
        }

        $app = [];                                  // 定義app陣列
        // 因為remark=textarea會包含換行符號，必須用str_replace置換/n標籤
        $log_remark = str_replace(array("\r\n","\r","\n"), "_rn_", $remark);
        $app = array(   "step"      => $step,
                        "cname"     => $cname,
                        "datetime"  => date('Y-m-d H:i:s'), 
                        "action"    => $action,
                        "remark"    => $log_remark);

        array_push($logs_arr, $app);
        $logs = json_encode($logs_arr);

        return $logs;        
        
    }
        