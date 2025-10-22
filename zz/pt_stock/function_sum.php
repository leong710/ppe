<?php
// // // index
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

    function show_ptlocal(){
        $pdo = pdo();
        // 前段-初始查詢語法：全廠+全狀態
        $sql = "SELECT _l.*, _f.fab_title, _f.fab_remark, _s.site_title, _s.site_remark
                FROM `pt_local` _l
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
    // 20240123 catalog Function
    function show_ptcatalogs(){
        $pdo = pdo();
        $sql = "SELECT * 
                FROM _cata
                WHERE _cata.cate_no = 'J'
                ORDER BY id ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $catalogs = $stmt->fetchAll();
            return $catalogs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

// // // for sum_ptstore
    // --- ptstock index  20240122
    function show_ptstock(){
        $pdo = pdo();
        $sql = "SELECT _stk.*, _stk.id AS stk_id
                        ,_l.local_title, _l.local_remark, _f.id AS fab_id, _f.fab_title, _f.fab_remark, _s.id as site_id, _s.site_title, _s.site_remark
                        ,_cata.pname, _cata.cata_remark, _cata.SN, _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no, _cata.flag AS cata_flag 
                FROM `pt_stock` _stk 
                LEFT JOIN pt_local _l ON _stk.local_id = _l.id 
                LEFT JOIN _fab _f ON _l.fab_id = _f.id 
                LEFT JOIN _site _s ON _f.site_id = _s.id 
                LEFT JOIN _cata ON _stk.cata_SN = _cata.SN 
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no 
                WHERE _cata.cate_no = 'J' AND _f.flag <> 'off' AND _l.flag <> 'off'
                 ";
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY fab_id, local_id, cata_SN, _stk.lot_num ASC ";
        $stmt = $pdo->prepare($sql);                                // 讀取全部=不分頁
        try {
            $stmt->execute();
            $ptstocks = $stmt->fetchAll();
            return $ptstocks;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }



// // // for sum_receive
    // 顯示被選定的_receive表單 20231226
    function show_receives($request){
        $pdo = pdo();
        extract($request);
        // $sql = "SELECT _r.local_id , _r.cata_SN_amount , DATE_FORMAT(_r.created_at, '%m') as mm , DATE_FORMAT(_r.created_at, '%Y') as yy 
        $sql = "SELECT _r.local_id , _r.cata_SN_amount , month(_r.created_at) as mm , year(_r.created_at) as yy 
                    -- , _l.fab_id , _l.id AS local_id , _l.local_title , _l.local_remark 
                    -- , _f.fab_title , _f.fab_remark , _f.sign_code AS fab_sign_code , _f.pm_emp_id
                    -- , _s.site_title , _s.site_remark
                FROM `_receive` _r
                    -- LEFT JOIN _local _l ON _r.local_id = _l.id
                    -- LEFT JOIN _fab _f ON _l.fab_id = _f.id
                    -- LEFT JOIN _site _s ON _f.site_id = _s.id
                WHERE( _r.idty = 10 OR _r.idty = 11 OR _r.idty = 13) ";          // 10=結案 13=已發貨
        if($report_mm == "All"){
            $sql .= "AND year(_r.created_at) = ? ";
        }else{
            $sql .= "AND year(_r.created_at) = ? AND month(_r.created_at) = ?";
        }
        $stmt = $pdo->prepare($sql);
        try {
        if($report_mm == "All"){
            $stmt->execute([$report_yy]);
        }else{
            $report_ym = $report_yy."-".$report_mm;
            $stmt->execute([$report_yy ,$report_mm]);
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
        $sql = "SELECT DISTINCT month(_r.created_at) as mm 
                -- SELECT DISTINCT DATE_FORMAT(_r.created_at, '%m') as mm  // 這個01會少了0
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

// // // for sum_trade(import)
    // 顯示被選定的_trade表單 20231226
    function show_trades($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _t.in_local as local_id , _t.item as cata_SN_amount , DATE_FORMAT(_t.out_date, '%m') as mm
                FROM `_trade` _t
                WHERE _t.form_type = 'import' AND _t.idty = 10 
                    -- , _l.fab_id , _l.id AS local_id , _l.local_title , _l.local_remark 
                    -- , _f.fab_title , _f.fab_remark , _f.sign_code AS fab_sign_code , _f.pm_emp_id
                    -- , _s.site_title , _s.site_remark
                    -- LEFT JOIN _local _l ON _t.local_id = _l.id
                    -- LEFT JOIN _fab _f ON _l.fab_id = _f.id
                    -- LEFT JOIN _site _s ON _f.site_id = _s.id
                    ";          // 10=結案
        if($report_mm == "All"){
            $sql .= "AND DATE_FORMAT(_t.out_date,'%Y') = ? ";
        }else{
            $sql .= "AND DATE_FORMAT(_t.out_date,'%Y-%m') = ? ";
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
    function show_allTrade_yy(){
        $pdo = pdo();
        $sql = "SELECT DISTINCT DATE_FORMAT(_t.out_date, '%Y') as yy 
                FROM _trade _t
                WHERE _t.form_type = 'import' AND _t.idty = 10 
                ORDER BY yy DESC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $alltrade_yys = $stmt->fetchAll();
            return $alltrade_yys;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // PM顯示全部by年份 => 供查詢年份使用
    function show_allTrade_ymm($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT DISTINCT DATE_FORMAT(_t.out_date, '%m') as mm 
                FROM _trade _t
                WHERE _t.form_type = 'import' AND _t.idty = 10 AND DATE_FORMAT(_t.out_date,'%Y') = ?
                ORDER BY mm ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$report_yy]);
            $allTrade_ymms = $stmt->fetchAll();
            return $allTrade_ymms;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }