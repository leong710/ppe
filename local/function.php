<?php

// site
    // site項目--新增 230703
    function store_site($request){
        $pdo = pdo();
        extract($request);
        $sql = "INSERT INTO _site(site_title, site_remark, flag, updated_user, created_at, updated_at)VALUES(?,?,?,?,now(),now())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$site_title, $site_remark, $flag, $updated_user]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // from edit_site.php 依ID找出要修改的site內容
    function edit_site($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT * FROM _site WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $site = $stmt->fetch();
            return $site;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // from edit_site.php call update_site 修改完成的edit_site 進行Update
    function update_site($request){
        $pdo = pdo();
        extract($request);
        $sql = "UPDATE _site
                SET site_title=?, site_remark=?, flag=?, updated_user=?, updated_at=now()
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$site_title, $site_remark, $flag, $updated_user, $id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    function delete_site($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM _site WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 隱藏或開啟
    function changeSite_flag($request){
        $pdo = pdo();
        extract($request);

        $sql_check = "SELECT _site.* FROM _site WHERE id=?";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$id]);
        $row = $stmt_check -> fetch();

        if($row['flag'] == "Off" || $row['flag'] == "chk"){
            $flag = "On";
        }else{
            $flag = "Off";
        }

        $sql = "UPDATE _site SET flag=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$flag, $id]);
            $Result = array(
                'table' => $table, 
                'id' => $id,
                'flag' => $flag
            );
            return $Result;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function show_site($request){       // 已加入分頁功能
        $pdo = pdo();
            extract($request);
        $sql = "SELECT _site.*
                FROM _site
                ORDER BY _site.id ASC";
        // 決定是否採用 page_div 20230803
            if(isset($start) && isset($per)){
                $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per);   // 讀取選取頁的資料=分頁
            }else{
                $stmt = $pdo->prepare($sql);                                // 讀取全部=不分頁
            }
        try {
            $stmt->execute();
            $sites = $stmt->fetchAll();
            return $sites;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
// site

// _Fab
    // fab項目--新增 230703
    function store_fab($request){
        $pdo = pdo();
        extract($request);
        $sql = "INSERT INTO _fab(site_id, fab_title, fab_remark, buy_ty, sign_code, flag, updated_user, created_at, updated_at)VALUES(?,?,?,?,?,?,?,now(),now())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$site_id, $fab_title, $fab_remark, $buy_ty, $sign_code, $flag, $updated_user]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // from edit_fab.php 依ID找出要修改的fab內容
    function edit_fab($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT * FROM _fab WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $fab = $stmt->fetch();
            return $fab;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // from edit_fab.php  call update_Fab 修改完成的edit_Fab 進行Update
    function update_fab($request){
        $pdo = pdo();
        extract($request);

        if(!empty($pm_emp_id)){
            $pm_emp_id = implode(",", $pm_emp_id);       //管理權組是陣列，要儲存前要轉成字串
        }else{
            $pm_emp_id = "";
        }

        $sql = "UPDATE _fab
                SET site_id=?, fab_title=?, fab_remark=?, buy_ty=?, sign_code=?, pm_emp_id=?, flag=?, updated_user=?, updated_at=now()
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$site_id, $fab_title, $fab_remark, $buy_ty, $sign_code, $pm_emp_id, $flag, $updated_user, $id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function delete_fab($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM _fab WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 隱藏或開啟
    function changeFab_flag($request){
        $pdo = pdo();
        extract($request);

        $sql_check = "SELECT _fab.* FROM _fab WHERE id=?";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$id]);
        $row = $stmt_check -> fetch();

        if($row['flag'] == "Off" || $row['flag'] == "chk"){
            $flag = "On";
        }else{
            $flag = "Off";
        }

        $sql = "UPDATE _fab SET flag=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$flag, $id]);
            $Result = array(
                'table' => $table, 
                'id' => $id,
                'flag' => $flag
            );
            return $Result;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function show_fab($request){       // 已加入分頁功能
        $pdo = pdo();
            extract($request);
        $sql = "SELECT _fab.*, _site.site_title, _site.site_remark, _site.flag AS site_flag
                FROM _fab
                LEFT JOIN _site ON _site.id = _fab.site_id
                ORDER BY _site.id, _fab.id ASC ";
        // 決定是否採用 page_div 20230803
            if(isset($start) && isset($per)){
                $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per);   // 讀取選取頁的資料=分頁
            }else{
                $stmt = $pdo->prepare($sql);                                // 讀取全部=不分頁
            }
        try {
            $stmt->execute();
            $fabs = $stmt->fetchAll();
            return $fabs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
// _Fab

// Local
    // local項目--新增 230703
    function store_local($request){
        $pdo = pdo();
        extract($request);
        $sql = "INSERT INTO _local(fab_id, local_title, local_remark, flag, updated_user, created_at, updated_at)VALUES(?,?,?,?,?,now(),now())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$fab_id, $local_title, $local_remark, $flag, $updated_user,]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // from edit_local.php 依ID找出要修改的local內容
    function edit_local($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT * FROM _local WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $local = $stmt->fetch();
            return $local;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    //from edit_Local.php call update_Local 修改完成的edit_Local 進行Update
    function update_local($request){
        $pdo = pdo();
        extract($request);
        $sql = "UPDATE _local
                SET fab_id=?, local_title=?, local_remark=?, flag=?, updated_user=?, updated_at=now()
                WHERE id=? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$fab_id, $local_title, $local_remark, $flag, $updated_user, $id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function delete_local($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM _local WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 隱藏或開啟
    function changeLocal_flag($request){
        $pdo = pdo();
        extract($request);

        $sql_check = "SELECT _local.* FROM _local WHERE id=?";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$id]);
        $row = $stmt_check -> fetch();

        if($row['flag'] == "Off" || $row['flag'] == "chk"){
            $flag = "On";
        }else{
            $flag = "Off";
        }

        $sql = "UPDATE _local SET flag=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$flag, $id]);
            $Result = array(
                'table' => $table, 
                'id' => $id,
                'flag' => $flag
            );
            return $Result;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function show_local($request){      // 已加入分頁功能
        $pdo = pdo();
        extract($request);
        // 前段-初始查詢語法：全廠+全狀態
        $sql = "SELECT _local.*, _fab.fab_title, _fab.fab_remark, _fab.flag AS fab_flag -- , u.cname
                FROM `_local`
                LEFT JOIN _fab ON _local.fab_id = _fab.id
                -- LEFT JOIN (SELECT * FROM _users WHERE role != '' AND role != 3) u ON u.fab_id = _local.fab_id
                -- WHERE _local.flag = 'On'
                ";
        if($fab_id != 'All'){
            $sql .= " WHERE _local.fab_id=? ";
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _fab.id, _local.id ASC ";
        // 決定是否採用 page_div 20230803
        if(isset($start) && isset($per)){
            $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per);   // 讀取選取頁的資料=分頁
        }else{
            $stmt = $pdo->prepare($sql);                                // 讀取全部=不分頁
        }
        try {
            if($fab_id == 'All'){
                $stmt->execute();               //處理 byAll
            }else{
                $stmt->execute([$fab_id]);      //處理 byFab
            }
            $locals = $stmt->fetchAll();
            return $locals;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
// Local


    // 設定low_level時用全local區域 20230707_updated
    function show_allLocal(){
        $pdo = pdo();
        $sql = "SELECT _local.*, _site.site_title, _site.site_remark, _fab.fab_title, _fab.fab_remark, _fab.flag AS fab_flag
                FROM `_local`
                LEFT JOIN _fab ON _local.fab_id = _fab.id
                LEFT JOIN _site ON _fab.site_id = _site.id
                -- WHERE _local.flag='On'
                ORDER BY _site.id, _fab.id, _local.id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $locals = $stmt->fetchAll();
            return $locals;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 設定low_level時用選則區域 20230707_updated
    function select_local($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _local.*, _site.site_title, _site.site_remark, _fab.fab_title, _fab.fab_remark, _fab.buy_ty
                FROM `_local`
                LEFT JOIN _fab ON _local.fab_id = _fab.id
                LEFT JOIN _site ON _fab.site_id = _site.id
                WHERE _local.id=? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$local_id]);
            $local = $stmt->fetch();
            return $local;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 秀出catalog全部 20230707_updated
    function show_catalogs(){
        $pdo = pdo();
        $sql = "SELECT _cata.*, _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no, _cate.flag AS cate_flag
                FROM _cata 
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no 
                ORDER BY _cate.id, _cata.id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $catalogs = $stmt->fetchAll();
            return $catalogs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 儲存low_level設定值
    function store_lowLevel($request){
        $pdo = pdo();
        extract($request);
        
        $swal_json = array(                                 // for swal_json
            "fun"       => "store_lowLevel",
            "content"   => "安全存量設定--"
        );
        // low_level資料前處理
        // $low_level = array_filter($low_level);             // 去除陣列中空白元素  => 20231229_這會把安量0的給濾除，導致錯誤
        // $amount = array_filter($amount);                    // 去除陣列中空白元素
        // // 小陣列要先編碼才能塞進去大陣列
        //     $catalog_SN_enc = json_encode($catalog_SN);
        //     $amount_enc = json_encode($amount);
        // //陣列合併
        //     $low_level_arr = [];
        //     $low_level_arr['catalog_SN'] = $catalog_SN_enc;
        //     $low_level_arr['amount'] = $amount_enc;
        // // implode()把陣列元素組合為字串：
        //     $low_level_str = $amount_enc;               // 陣列轉成字串進行儲存到mySQL
            $low_level_str = json_encode($low_level);      // 陣列轉成字串進行儲存到mySQL
        
        $sql = "UPDATE _local
                SET low_level=?, updated_user=?, updated_at=now()
                WHERE id=? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$low_level_str, $updated_user, $local_id]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '儲存成功';

        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '儲存失敗';
        }
        return $swal_json;
    }
    // --- stock index  20231218 領用量：在low_level表單中顯示cata_SN年領用量
    function show_my_receive($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT DISTINCT _r.* , _l.local_title , _l.local_remark , _f.id AS fab_id , _f.fab_title , _f.fab_remark , _f.sign_code AS fab_sign_code , _f.pm_emp_id  
                FROM `_receive` _r
                LEFT JOIN _local _l ON _r.local_id = _l.id
                LEFT JOIN _fab _f ON _l.fab_id = _f.id
                WHERE _r.local_id = ? AND DATE_FORMAT(_r.created_at, '%Y') = ? AND (_r.idty = 10 OR _r.idty = 11 OR _r.idty = 13)";  // 10=結案
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$local_id, $thisYear]);
            $my_receive_lists = $stmt->fetchAll();
            return $my_receive_lists;

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // step-1.把stock裡面的cata_SN讀出來By
    function show_stock_cata_SN($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _s.local_id, _s.cata_SN
                FROM `_stock` _s
                WHERE local_id = ?
                GROUP BY _s.cata_SN ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$local_id]);
            $show_stock_cata_SN_result = $stmt->fetchAll();
            return $show_stock_cata_SN_result;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // step-2.更新stock裡面的standard_lv(安全存量)
    function update_stock_stand_lv($request){
        $pdo = pdo();
        extract($request);

        $swal_json = array(                                 // for swal_json
            "fun"       => "update_stock_stand_lv",
            "content"   => "安全存量設定--"
        );

        $sql = "UPDATE `_stock`
                SET standard_lv = ?
                WHERE local_id = ? AND cata_SN = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$standard_lv, $local_id, $cata_SN]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '儲存成功';
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '儲存失敗';
        }
        return $swal_json;
    }