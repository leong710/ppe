<?php

    // 在NAV找出自己的資料forAlarm 
    function show_myReceive($request){           // 3.查詢領用申請
        $pdo = pdo();
        extract($request);
        $sql = "SELECT COUNT(*) AS idty_count
                FROM `_receive` 
                LEFT JOIN _local ON _receive.local_id = _local.id 
                LEFT JOIN _fab   ON _local.fab_id = _fab.id 
                WHERE (_receive.idty IN (1, 11) AND _receive.in_sign = ? ) OR (_receive.idty = 13 AND FIND_IN_SET( {$emp_id} , _fab.pm_emp_id)) ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$emp_id]);
            $myReceive = $stmt->fetch();
            return $myReceive;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function show_myTrade($request){             // 2.查詢入出庫申請
        $pdo = pdo();
        extract($request);
        $sql = "SELECT DISTINCT i_local.fab_id AS i_sid,
                    (SELECT COUNT(*) FROM `_trade` t2 WHERE t2.in_local = _trade.in_local AND t2.idty = _trade.idty) AS idty_count
                FROM `_trade`
                LEFT JOIN _local i_local ON _trade.in_local = i_local.id
                WHERE _trade.idty = 1 AND i_local.fab_id = ?" ;
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$fab_id]);
            $myTrade = $stmt->fetch();
            return $myTrade;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    function show_myIssue(){                    // 1.查詢請購需求單
        $pdo = pdo();
        $sql = "SELECT COUNT(*) AS idty_count
                FROM `_issue`
                WHERE _issue.idty = 1";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $myIssue = $stmt->fetch();
            return $myIssue;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // fab user index查詢自己的點檢紀錄for顯示點檢表功能
    function sort_check_list($request){         // 庫存點檢
        $pdo = pdo();
        extract($request);
        $sql = "SELECT form_type, count(form_type) AS cunt
                FROM `checked_log` cl
                WHERE cl.checked_year = ? AND cl.half = ? AND cl.fab_id IN ({$sfab_id})
                GROUP BY cl.form_type ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$checked_year, $half]);
            $check_yh_list = $stmt->fetchAll();
            return $check_yh_list;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }


    // 20240125 4.組合我的廠區到$sys_sfab_id => 包含原sfab_id、fab_id和sign_code所涵蓋的廠區
    function get_sfab_id2($sys_id, $type){
        // 1-1a 將fab_id加入sfab_id
        if(isset($_SESSION[$sys_id]["fab_id"])){
            $fab_id = $_SESSION[$sys_id]["fab_id"];              // 1-1.取fab_id
        }else{
            $fab_id = "0";
        }
        if(isset($_SESSION[$sys_id]["sfab_id"])){
            $sfab_id = $_SESSION[$sys_id]["sfab_id"];            // 1-1.取sfab_id
        }else{
            $sfab_id = [];
        }
        if(!in_array($fab_id, $sfab_id)){                        // 1-1.當fab_id不在sfab_id，就把部門代號id套入sfab_id
            array_push($sfab_id, $fab_id);
        }
        // 1-1b 將sign_code涵蓋的fab_id加入sfab_id
        if(isset($_SESSION["AUTH"]["sign_code"])){
            $auth_sign_code["sign_code"] = $_SESSION["AUTH"]["sign_code"];
            $coverFab_lists = show_coverFab_lists2($auth_sign_code);
            if(!empty($coverFab_lists)){
                foreach($coverFab_lists as $coverFab){
                    if(!in_array($coverFab["id"], $sfab_id)){
                        array_push($sfab_id, $coverFab["id"]);
                    }
                }
            }
        }
        // 1-1c 將sfab_id中的0去除
        if (in_array('0', $sfab_id)) {
            unset($sfab_id[array_search('0', $sfab_id)]);
        }
        if (in_array('1', $sfab_id)) {
            unset($sfab_id[array_search('1', $sfab_id)]);
        }
        // 根據需求類別進行編碼 arr=陣列、str=字串
        if($type == "str"){
            $result = implode(",", $sfab_id);                   // 1-1c sfab_id是陣列，要轉成字串
        }else{
            $result = $sfab_id;
        }
        // 1-1c sfab_id是陣列，要轉成字串
        return $result;
    }
    // 20231026 在index表頭顯示my_coverFab區域 = 使用signCode去搜尋
    function show_coverFab_lists2($request){
        $pdo = pdo();
        extract($request);

            $sign_code = substr($sign_code, 0, -2);     // 去掉最後兩個字 =>
            $sign_code = "%".$sign_code."%";            // 加上模糊包裝

        $sql = "SELECT _f.*
                FROM _fab AS _f 
                WHERE _f.sign_code LIKE ?
                ORDER BY _f.id ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$sign_code]);
            $coverFab_lists = $stmt->fetchAll();
            return $coverFab_lists;

        }catch(PDOException $e){
            echo $e->getMessage();
        }

    }