<?php

// // // checked CRUD
    // stock項目--新增 230725
    function store_checked($request){
        $pdo = pdo();
        extract($request);

        $swal_json = array(                                 // for swal_json
            "fun"       => "store_checked",
            "content"   => "存量點檢單--",
            "msg"       => ""
        );

        // $logs = JSON_encode($stocks_log);
        $sql = "INSERT INTO checked_log(form_type, fab_id, stocks_log, emp_id, updated_user, checked_remark, checked_year, half, created_at, updated_at)VALUES(?,?,?,?,?,?,?,?,now(),now())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$form_type, $fab_id, $stocks_log, $emp_id, $cname, $checked_remark, $checked_year, $half]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '送出成功';
            // 20240220_增加mapp推播訊息
                if($form_type == "stock"){
                    $form_type_name = "個人防護具";
                }else{
                    $form_type_name = "除汙器材";
                }
            $swal_json["msg"]     .= "【環安PPE系統】".$form_type_name."點檢完成通知\n(".$cname.") 已提送".$fab_title."(".$fab_remark.")".$checked_year."/".$half."點檢記錄";
            $swal_json["msg"]     .= "，如已確認完畢，請忽略此訊息！\n** 請至以下連結查看文件：\nhttp://tw059332n.cminl.oa/ppe/checked/";
            $swal_json["msg"]     .= "\n溫馨提示：\n1.登錄過程中如出現提示輸入帳號密碼，請以cminl\\NT帳號格式\n<此訊息為系統自動發出，請勿回覆>";

        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '送出失敗';
        }
        return $swal_json;
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
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
// // // checked CRUD -- end

    // user只顯示自己的清單
    function show_checked($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT checked_log.*, _fab.fab_title 
                FROM `checked_log`
                LEFT JOIN _fab ON _fab.id = checked_log.fab_id
                WHERE checked_log.fab_id = ?
                ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$fab_id]);
            $checkeds = $stmt->fetchAll();
            return $checkeds;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function checked_page_div($start, $per, $request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT checked_log.*, _fab.fab_title 
                FROM `checked_log`
                LEFT JOIN _fab ON _fab.id = checked_log.fab_id
                WHERE checked_log.fab_id = ?
                ORDER BY created_at DESC";
        $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料
        try {
            $stmt->execute([$fab_id]);
            $rs = $stmt->fetchAll();
            return $rs;
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
    // PM顯示全部by年份 => 供查詢年份使用
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
    // --- stock index  20230725
    function show_stock_forCheck($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _stk.*, 
                        _l.local_title, _l.local_remark, _f.id AS fab_id, _f.fab_title, _f.fab_remark, _s.id as site_id, _s.site_title, _s.site_remark, 
                        _cata.pname, _cata.cata_remark, _cata.SN, _cata.size, _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no 
                FROM `_stock` _stk 
                LEFT JOIN _local _l ON _stk.local_id = _l.id 
                LEFT JOIN _fab _f ON _l.fab_id = _f.id 
                LEFT JOIN _site _s ON _f.site_id = _s.id 
                LEFT JOIN _cata ON _stk.cata_SN = _cata.SN 
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no 
                WHERE _f.id=?
                ORDER BY fab_id, local_id, cata_SN, lot_num ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$fab_id]);
            $stocks = $stmt->fetchAll();
            return $stocks;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

// 20240220_增加mapp推播訊息
    // 20231106 結案簽核時，送簽給主管環安 = 找出業務窗口的環安主管
    function query_omager($emp_id){
        $pdo = pdo_hrdb();
        $sql = "SELECT u.emp_id, u.cname , u.omager AS omager_emp_id, s.cname AS omager_cname
                FROM `STAFF` u
                LEFT JOIN `STAFF` s ON u.omager = s.emp_id 
                where u.emp_id = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$emp_id]);
            $query_omager = $stmt->fetch();
            return $query_omager;

        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }
    // 20231106 結案簽核時，送簽給主管環安 = 找出業務窗口的環安主管
    function query_FAB_omager($sign_code){
        $pdo = pdo_hrdb();
        $sql = "SELECT _d.OSHORT, _d.OFTEXT, _d.OMAGER, CONCAT(_s.NACHN, _s.VORNA) AS cname
                FROM `HCM_VW_DEPT08` _d
                LEFT JOIN `HCM_VW_EMP01_hiring` _s ON _d.OMAGER = _s.PERNR
                WHERE _d.OSHORT = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$sign_code]);
            $query_omager = $stmt->fetch();
            return $query_omager;

        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }


