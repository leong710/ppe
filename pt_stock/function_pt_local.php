<?php

// // // index
    // 20231026 在index表頭顯示my_coverFab區域 = 使用signCode去搜尋
    function show_coverFab_lists($request){
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
    // 20240123 index fab_list：role <=1 ? All+all_fab : sFab_id+allMy => select_fab_id
    function show_fab_list($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _f.id, _f.fab_title, _f.fab_remark, _f.flag, _site.site_title 
                FROM _fab _f
                LEFT JOIN _site ON _f.site_id = _site.id 
                WHERE _f.flag = 'On' ";
        if($sfab_id != 'All'){
            $sql .= " AND _f.id IN ({$sfab_id}) ";
        }  
        $sql .= " ORDER BY _f.id ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $fabs = $stmt->fetchAll();
            return $fabs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function show_select_fab($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _f.id, _f.fab_title, _f.fab_remark, _f.flag, _site.site_title 
                FROM _fab _f
                LEFT JOIN _site ON _f.site_id = _site.id 
                ";
        if($select_fab_id != 'All' && $select_fab_id != "allMy"){
            $sql .= " WHERE _f.id=? ";
        }
        $sql .= " ORDER BY _f.id ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            if($select_fab_id != 'All' && $select_fab_id != "allMy"){
                $stmt->execute([$select_fab_id]);
            }else{
                $stmt->execute();
            }
            $fabs = $stmt->fetch();
            return $fabs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20240123 edit時用role <=0 ? all全區域 : user sFab_id
    function show_fabs_local($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _l.*, _f.fab_title, _f.fab_remark, _f.flag AS fab_flag, _s.site_title, _s.site_remark
                FROM `pt_local` _l
                LEFT JOIN _fab _f ON _l.fab_id = _f.id
                LEFT JOIN _site _s ON _f.site_id = _s.id ";

        if($select_fab_id == "allMy" && $sfab_id != "All"){
            $sql .= " WHERE _l.fab_id IN ({$sfab_id}) ";
        }else if($select_fab_id != "All" && $select_fab_id != "allMy" ){
            $sql .= " WHERE _l.fab_id = ? ";
        }

        $sql .= " ORDER BY _s.id, _f.id, _l.id ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            if($select_fab_id != "All" && $select_fab_id != "allMy"){
                $stmt->execute([$select_fab_id]);
            }else{
                $stmt->execute();
            }
            $ptlocals = $stmt->fetchAll();
            return $ptlocals;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 設定low_level時用選則區域 20230707_updated
    function select_local($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _l.*, _f.fab_title, _f.fab_remark, _f.buy_ty , _s.site_title, _s.site_remark
                FROM `pt_local` _l
                LEFT JOIN _fab _f ON _l.fab_id = _f.id
                LEFT JOIN _site _s ON _f.site_id = _s.id
                WHERE _l.id=? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$select_local_id]);
            $local = $stmt->fetch();
            return $local;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20240123 catalog Function
    function show_ptcatalogs(){
        $pdo = pdo();
        $sql = "SELECT _cata.* , _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no, _cate.flag AS cate_flag
                FROM _cata
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no 
                WHERE _cata.cate_no = 'J'
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
    // 20240125 4.組合我的廠區到$sys_sfab_id => 包含原sfab_id、fab_id和sign_code所涵蓋的廠區
    function get_sfab_id($sys_id, $type){
        // 1-1a 將fab_id加入sfab_id
        if(isset($_SESSION[$sys_id]["fab_id"])){
            $fab_id = $_SESSION[$sys_id]["fab_id"];              // 1-1.取fab_id
        }else{
            $fab_id = "0";
        }
        $sfab_id = $_SESSION[$sys_id]["sfab_id"];                // 1-1.取sfab_id
        if(!in_array($fab_id, $sfab_id)){                        // 1-1.當fab_id不在sfab_id，就把部門代號id套入sfab_id
            array_push($sfab_id, $fab_id);
        }
        // 1-1b 將sign_code涵蓋的fab_id加入sfab_id
        if(isset($_SESSION["AUTH"]["sign_code"])){
            $auth_sign_code["sign_code"] = $_SESSION["AUTH"]["sign_code"];
            $coverFab_lists = show_coverFab_lists($auth_sign_code);
            if(!empty($coverFab_lists)){
                foreach($coverFab_lists as $coverFab){
                    if(!in_array($coverFab["id"], $sfab_id)){
                        array_push($sfab_id, $coverFab["id"]);
                    }
                }
            }
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
    // --- ptstock index  20231218 領用量
    function show_ptreceive($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT DISTINCT _r.* -- , _l.local_title , _l.local_remark 
                    , _f.id AS fab_id , _f.fab_title , _f.fab_remark , _f.sign_code AS fab_sign_code , _f.pm_emp_id , _s.site_title , _s.site_remark 
                FROM `_receive` _r
                -- LEFT JOIN _local _l ON _r.local_id = _l.id
                LEFT JOIN _fab _f ON _r.fab_id = _f.id
                LEFT JOIN _site _s ON _f.site_id = _s.id
                WHERE _r.fab_id = ? AND DATE_FORMAT(_r.created_at, '%Y') = ? AND ( _r.idty = 1)";  // 10=結案
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$fab_id, $thisYear]);
            $my_receive_lists = $stmt->fetchAll();
            return $my_receive_lists;

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
// pt_Local 20240122 for 除汙劑/應變器材
    // local項目--新增 20240122
    function store_ptlocal($request){
        $pdo = pdo();
        extract($request);
        $sql = "INSERT INTO pt_local(fab_id, local_title, local_remark, flag, updated_user, created_at, updated_at)VALUES(?,?,?,?,?,now(),now())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$fab_id, $local_title, $local_remark, $flag, $updated_user,]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // from edit_ptlocal.php 依ID找出要修改的ptlocal內容
    function edit_ptlocal($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT * FROM pt_local WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $ptlocal = $stmt->fetch();
            return $ptlocal;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    //from edit_ptLocal.php call update_ptLocal 修改完成的edit_ptLocal 進行Update
    function update_ptlocal($request){
        $pdo = pdo();
        extract($request);
        $sql = "UPDATE pt_local
                SET fab_id=?, local_title=?, local_remark=?, flag=?, updated_user=?, updated_at=now()
                WHERE id=? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$fab_id, $local_title, $local_remark, $flag, $updated_user, $id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function delete_ptlocal($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM pt_local WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 隱藏或開啟
    // function changePTLocal_flag($request)  // move to api_function.php


    function show_ptlocal($request){      // 已加入分頁功能
        $pdo = pdo();
        extract($request);
        // 前段-初始查詢語法：全廠+全狀態
        $sql = "SELECT pt_local.*, _fab.fab_title, _fab.fab_remark, _fab.flag AS fab_flag -- , u.cname
                FROM `pt_local`
                LEFT JOIN _fab ON pt_local.fab_id = _fab.id
                -- LEFT JOIN (SELECT * FROM _users WHERE role != '' AND role != 3) u ON u.fab_id = _local.fab_id
                -- WHERE _local.flag = 'On'
                ";
        if($fab_id != 'All'){
            $sql .= " WHERE pt_local.fab_id=? ";
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _fab.id, pt_local.id ASC ";
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
            $ptlocals = $stmt->fetchAll();
            return $ptlocals;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
// pt_Local

    // 儲存low_level設定值
    function store_lowLevel($request){
        $pdo = pdo();
        extract($request);
        
        $swal_json = array(                                 // for swal_json
            "fun"       => "store_lowLevel",
            "content"   => "安全存量設定--"
        );
        // low_level資料前處理
            $low_level_str = json_encode($low_level);      // 陣列轉成字串進行儲存到mySQL
        
        $sql = "UPDATE pt_local
                SET low_level=?, updated_user=?, updated_at=now()
                WHERE id=? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$low_level_str, $updated_user, $select_local_id]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '儲存成功';

        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '儲存失敗';
        }
        return $swal_json;
    }

    // step-1.把stock裡面的cata_SN讀出來By
    function show_stock_cata_SN($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _s.local_id, _s.cata_SN
                FROM `pt_stock` _s
                WHERE local_id = ?
                GROUP BY _s.cata_SN ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$select_local_id]);
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

        $sql = "UPDATE `pt_stock`
                SET standard_lv = ?
                WHERE local_id = ? AND cata_SN = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$standard_lv, $select_local_id, $cata_SN]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '儲存成功';
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '儲存失敗';
        }
        return $swal_json;
    }