<?php

// // // checked CRUD
    // stock項目--新增 230725
    function store_checked($request){
        $pdo = pdo();
        extract($request);
        // $logs = JSON_encode($stocks_log);
        $sql = "INSERT INTO checked_log(form_type, fab_id, stocks_log, emp_id, updated_user, checked_remark, checked_year, half, created_at, updated_at)VALUES(?,?,?,?,?,?,?,?,now(),now())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$form_type, $fab_id, $stocks_log, $emp_id, $cname, $checked_remark, $checked_year, $half]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 顯示被選定的checked項目 230725
    function show_checked_item($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT checked_log.*, _fab.fab_title, _fab.fab_remark 
                FROM `checked_log`
                LEFT JOIN _fab ON _fab.id = checked_log.fab_id
                WHERE checked_log.id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $checkeds = $stmt->fetch();
            return $checkeds;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 刪除單筆紀錄
    function delete_checked_item($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM checked_log WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            return true;
        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }
// // // checked CRUD -- end


    // user只顯示自己的清單        // 20231225 嵌入分頁工具
    function show_checked($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT cl.* , _f.fab_title , _f.fab_remark 
                FROM `checked_log` cl
                LEFT JOIN _fab _f ON cl.fab_id = _f.id
                -- WHERE checked_log.fab_id = ?
                ORDER BY cl.created_at DESC ";

        // 決定是否採用 page_div 20231225
        if(isset($start) && isset($per)){
            $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料=分頁
        }else{
            $stmt = $pdo->prepare($sql);                // 讀取全部=不分頁
        }
        try {
            // $stmt->execute([$fab_id]);
            $stmt->execute();
            $checkeds = $stmt->fetchAll();
            return $checkeds;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // fab user index查詢自己的點檢紀錄for顯示點檢表功能
    function check_yh_list($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT *
                FROM `checked_log`
                WHERE checked_log.fab_id = ? AND checked_log.checked_year = ? AND checked_log.half = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$fab_id, $checked_year, $half]);
            $check_yh_list = $stmt->fetchAll();
            return $check_yh_list;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // PM顯示全部by年份
    function show_allchecked($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT checked_log.* , _fab.fab_title , _fab.fab_remark 
                FROM `checked_log`
                LEFT JOIN _fab ON checked_log.fab_id = _fab.id
                WHERE (checked_log.checked_year = ? OR checked_log.checked_year is null) AND _fab.flag = 'On'
                ORDER BY _fab.id, created_at DESC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$checked_year]);
            $checkeds = $stmt->fetchAll();
            return $checkeds;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // PM顯示全部by年份 => 供select查詢年份使用
    function show_allchecked_year(){
        $pdo = pdo();
        $sql = "SELECT checked_year
                FROM `checked_log`
                GROUP BY checked_year
                ORDER BY checked_year DESC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $checked_years = $stmt->fetchAll();
            return $checked_years;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // index thead
    function show_fab(){
        $pdo = pdo();
        // 前段-初始查詢語法：全廠+全狀態
        $sql = "SELECT _f.id AS fab_id, _f.fab_title, _f.fab_remark, _f.flag, _s.site_title, _s.site_remark
                FROM _fab _f
                LEFT JOIN _site _s ON _f.site_id = _s.id 
                WHERE _f.flag <> 'off'
                ORDER BY _f.id ASC ";
        $stmt = $pdo->prepare($sql);                                // 讀取全部=不分頁
        try {
            $stmt->execute();                                       //處理 byAll
            $fabs = $stmt->fetchAll();
            return $fabs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // index tbody
    function show_checked_year($request){
        $pdo = pdo();
        extract($request);
        // 前段-初始查詢語法：全廠+全狀態
        $sql = "SELECT form_type, checked_year , half
                FROM `checked_log`
                WHERE checked_year = ?
                GROUP BY half, form_type
                ORDER BY form_type DESC ";
        $stmt = $pdo->prepare($sql);                                // 讀取全部=不分頁
        try {
            $stmt->execute([$checked_year]);                        // 處理 $checked_year
            $checked_year = $stmt->fetchAll();
            return $checked_year;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20240206 可以查詢出廠區的點檢狀況
    $ex_sql="SELECT _f.id, _f.fab_title, _f.pm_emp_id
                , SUM(CASE WHEN cl.form_type = 'stock' THEN 1 ELSE 0 END) AS stock_cunt
                , SUM(CASE WHEN cl.form_type = 'ptstock' THEN 1 ELSE 0 END) AS ptstock_cunt
            FROM `_fab` _f
            LEFT JOIN checked_log cl ON _f.id = cl.fab_id
            WHERE _f.flag <> 'Off'
            GROUP BY _f.id ";

