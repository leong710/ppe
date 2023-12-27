<?php

// // // for sum_receive
    function show_local(){
        $pdo = pdo();
        // 前段-初始查詢語法：全廠+全狀態
        $sql = "SELECT _l.*, _f.fab_title, _f.fab_remark, _s.site_title, _s.site_remark
                FROM `_local` _l
                LEFT JOIN _fab _f ON _l.fab_id = _f.id
                LEFT JOIN _site _s ON _f.site_id = _s.id
                WHERE _l.flag <> 'off'
                ORDER BY _f.id, _l.id ASC ";
        $stmt = $pdo->prepare($sql);                                // 讀取全部=不分頁
        try {
            $stmt->execute();                                       //處理 byAll
            $locals = $stmt->fetchAll();
            return $locals;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function show_catalogs(){   
        $pdo = pdo();
        $sql = "SELECT _ca.*, _ce.id AS cate_id, _ce.cate_title, _ce.cate_remark, _ce.cate_no, _ce.flag AS cate_flag , _p.price
                FROM _cata _ca
                LEFT JOIN _cate _ce ON _ca.cate_no = _ce.cate_no 
                LEFT JOIN _pno _p ON _ca.SN = _p.cata_SN
                ORDER BY _ca.cate_no, _ca.id ASC "; 
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $catalogs = $stmt->fetchAll();
            return $catalogs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    // 顯示被選定的_receive表單 20231226
    function show_receives($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _r.local_id , _r.cata_SN_amount , DATE_FORMAT(_r.created_at, '%m') as mm
                    -- , _l.fab_id , _l.id AS local_id , _l.local_title , _l.local_remark 
                    -- , _f.fab_title , _f.fab_remark , _f.sign_code AS fab_sign_code , _f.pm_emp_id
                    -- , _s.site_title , _s.site_remark
                FROM `_receive` _r
                    -- LEFT JOIN _local _l ON _r.local_id = _l.id
                    -- LEFT JOIN _fab _f ON _l.fab_id = _f.id
                    -- LEFT JOIN _site _s ON _f.site_id = _s.id
                WHERE _r.idty = 10 ";          // 10=結案
        if($report_mm == "All"){
            $sql .= "AND DATE_FORMAT(_r.created_at,'%Y') = ? ";
        }else{
            $sql .= "AND DATE_FORMAT(_r.created_at,'%Y-%m') = ? ";
        }
        $stmt = $pdo->prepare($sql);
        try {
        if($report_mm == "All"){
            $stmt->execute([$report_yy]);
        }else{
            $report_ym = $report_yy."-".$report_mm;
            $stmt->execute([$report_ym]);
        }
            $receives = $stmt->fetchAll();
            return $receives;
        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }
    // PM顯示全部by年份 => 供查詢年份使用
    function show_allReceive_yy(){
        $pdo = pdo();
        $sql = "SELECT DISTINCT DATE_FORMAT(created_at, '%Y') as yy 
                -- SELECT DISTINCT DATE_FORMAT(created_at, '%Y-%m') as ym 
                FROM _receive 
                ORDER BY yy DESC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $allReceive_yys = $stmt->fetchAll();
            return $allReceive_yys;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // PM顯示全部by年份 => 供查詢年份使用
    function show_allReceive_ymm($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT DISTINCT DATE_FORMAT(_r.created_at, '%m') as mm 
                FROM _receive _r
                WHERE DATE_FORMAT(_r.created_at,'%Y') = ?
                ORDER BY mm ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$report_yy]);
            $allReceive_ymms = $stmt->fetchAll();
            return $allReceive_ymms;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

// // // for sum_issue
    // 顯示被選定的_issue表單 20231226
    function show_issues($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _i.in_local as local_id , _i.item as cata_SN_amount , DATE_FORMAT(_i.create_date, '%m') as mm
                    -- , _l.fab_id , _l.id AS local_id , _l.local_title , _l.local_remark 
                    -- , _f.fab_title , _f.fab_remark , _f.sign_code AS fab_sign_code , _f.pm_emp_id
                    -- , _s.site_title , _s.site_remark
                FROM `_issue` _i
                    -- LEFT JOIN _local _l ON _i.local_id = _l.id
                    -- LEFT JOIN _fab _f ON _l.fab_id = _f.id
                    -- LEFT JOIN _site _s ON _f.site_id = _s.id
                WHERE _i.idty = 10 ";          // 10=結案
        if($report_mm == "All"){
            $sql .= "AND DATE_FORMAT(_i.create_date,'%Y') = ? ";
        }else{
            $sql .= "AND DATE_FORMAT(_i.create_date,'%Y-%m') = ? ";
        }
        $stmt = $pdo->prepare($sql);
        try {
        if($report_mm == "All"){
            $stmt->execute([$report_yy]);
        }else{
            $report_ym = $report_yy."-".$report_mm;
            $stmt->execute([$report_ym]);
        }
            $issues = $stmt->fetchAll();
            return $issues;
        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }
    // PM顯示全部by年份 => 供查詢年份使用
    function show_allIssue_yy(){
        $pdo = pdo();
        $sql = "SELECT DISTINCT DATE_FORMAT(_i.create_date, '%Y') as yy 
                -- SELECT DISTINCT DATE_FORMAT(_i.create_date, '%Y-%m') as ym 
                FROM _issue _i
                ORDER BY yy DESC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $allIssue_yys = $stmt->fetchAll();
            return $allIssue_yys;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // PM顯示全部by年份 => 供查詢年份使用
    function show_allIssue_ymm($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT DISTINCT DATE_FORMAT(_i.create_date, '%m') as mm 
                FROM _issue _i
                WHERE DATE_FORMAT(_i.create_date,'%Y') = ?
                ORDER BY mm ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$report_yy]);
            $allIssue_ymms = $stmt->fetchAll();
            return $allIssue_ymms;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }