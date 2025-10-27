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
            $myReceive = $stmt->fetch(PDO::FETCH_ASSOC);
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
            $myTrade = $stmt->fetch(PDO::FETCH_ASSOC);
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
            $myIssue = $stmt->fetch(PDO::FETCH_ASSOC);
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
                WHERE cl.checked_year = ? AND cl.half = ? ";
        if(!empty($sfab_id)){
            $sql .= "AND cl.fab_id IN ({$sfab_id}) ";
        }
        $sql .= "GROUP BY cl.form_type ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$checked_year, $half]);
            $check_yh_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $check_yh_list;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20240119 查詢表單計畫 step-0
    function nav_show_formplan($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _plan.* ,
                    CASE
                        WHEN DATE_FORMAT(NOW(), '%m-%d %H:%i') BETWEEN DATE_FORMAT(_plan.start_time, '%m-%d %H:%i') AND DATE_FORMAT(_plan.end_time, '%m-%d %H:%i') THEN 'true' 
                        ELSE 'false'
                    END AS onGoing 
                    , _case.title AS case_title
                FROM _formplan _plan
                LEFT JOIN _formcase _case ON _plan._type = _case._type
                WHERE (_plan.flag = 'On') AND ( DATE_FORMAT(NOW(), '%m-%d %H:%i') BETWEEN DATE_FORMAT(_plan.start_time, '%m-%d %H:%i') AND DATE_FORMAT(_plan.end_time, '%m-%d %H:%i')) ";
        if(!empty($form_type)){
            $sql .= " AND _plan._type = ? ";
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _plan.updated_at DESC ";
        $stmt = $pdo->prepare($sql);
        try {
            // echo $sql;
            if(!empty($form_type)){
                $stmt->execute([$form_type]);
            }else{
                $stmt->execute();
            }
            $formplans = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $formplans;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20240313 查詢表單計畫 step-1 回饋 true / false
    function nav_show_plan($query_arr){
        $formplans = nav_show_formplan($query_arr);         // 查詢表單計畫 20240118 == 讓表單呈現 ON 或 Off
        $s_time = (new DateTime(date("Y-m-d H:i"))) -> format("m-d H:i");   // 過濾起始日期取值轉成m-d H:i
        $e_time = (new DateTime(date("Y-m-d H:i"))) -> format("m-d H:i");   // 過濾結束日期取值轉成m-d H:i
        $_inplan = false;
        $result = [];
        foreach($formplans as $plan){                   // 遍歷每一筆計畫
            if($plan["onGoing"] == "true" && $plan["flag"] == "On" && $plan["_inplan"] == "On"){             // 假如計畫啟動中 + 區間 = Off

                $_inplan = true ;                       // 反之就以on為主
                
                $p_s_time = (new DateTime($plan["start_time"])) -> format("m-d H:i");   // 計畫起始日期取值轉成m-d H:i
                $p_e_time = (new DateTime($plan["end_time"]))   -> format("m-d H:i");   // 計畫結束日期取值轉成m-d H:i

                if($p_s_time < $s_time){ $s_time = $p_s_time; }
                if($p_e_time > $e_time){ $e_time = $p_e_time; }

                // 組合輸出陣列
                $result[$plan["_type"]]= array(
                    "case_title" => $plan["case_title"],
                    "start_time" => $s_time,
                    "end_time"   => $e_time,
                    "_inplan"    => $_inplan
                );
            } 
        }
        return $result;
    }

    // 20240125 4.組合我的廠區到$sys_sfab_id => 包含原sfab_id、fab_id和sign_code所涵蓋的廠區
    function get_sfab_id2($sys_id, $type){
        // 1-1a 將fab_id加入sfab_id
        // 1-1.取fab_id
        $fab_id = isset($_SESSION[$sys_id]["fab_id"]) ? $_SESSION[$sys_id]["fab_id"] : "0";
        // 1-1.取sfab_id
        $sfab_id = isset($_SESSION[$sys_id]["sfab_id"]) ? $_SESSION[$sys_id]["sfab_id"] : [];

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
            $coverFab_lists = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $coverFab_lists;

        }catch(PDOException $e){
            echo $e->getMessage();
        }

    }