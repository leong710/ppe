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
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function show_site(){
        $pdo = pdo();
        $sql = "SELECT _site.*
                FROM _site
                ORDER BY _site.id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $sites = $stmt->fetchAll();
            return $sites;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    //分頁工具
    function page_div_site($start, $per){
        $pdo = pdo();
        $sql = "SELECT _site.*
                FROM _site
                ORDER BY _site.id ASC";
        $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料
        try {
            $stmt->execute();
            $rs = $stmt->fetchAll();
            return $rs;
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
        $sql = "INSERT INTO _fab(site_id, fab_title, fab_remark, buy_ty, flag, updated_user, created_at, updated_at)VALUES(?,?,?,?,?,?,now(),now())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$site_id, $fab_title, $fab_remark, $buy_ty, $flag, $updated_user]);
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
        $sql = "UPDATE _fab
                SET site_id=?, fab_title=?, fab_remark=?, buy_ty=?, flag=?, updated_user=?, updated_at=now()
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$site_id, $fab_title, $fab_remark, $buy_ty, $flag, $updated_user, $id]);
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
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function show_fab(){
        $pdo = pdo();
        $sql = "SELECT _fab.*, _site.site_title, _site.site_remark, _site.flag AS site_flag
                FROM _fab
                LEFT JOIN _site ON _site.id = _fab.site_id
                ORDER BY _site.id, _fab.id ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $fabs = $stmt->fetchAll();
            return $fabs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    //分頁工具
    function page_div_fab($start, $per){
        $pdo = pdo();
        $sql = "SELECT _fab.*, _site.site_title, _site.site_remark, _site.flag AS site_flag
                FROM _fab
                LEFT JOIN _site ON _site.id = _fab.site_id
                ORDER BY _site.id, _fab.id ASC ";
        $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料
        try {
            $stmt->execute();
            $rs = $stmt->fetchAll();
            return $rs;
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
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function show_local($request){
        $pdo = pdo();
        extract($request);
        // 前段-初始查詢語法：全廠+全狀態
        $sql = "SELECT _local.*, _fab.fab_title, _fab.fab_remark, _fab.flag AS fab_flag
                FROM `_local`
                LEFT JOIN _fab ON _local.fab_id = _fab.id
                -- WHERE _local.flag = 'On'
                ";
        if($fab_id != 'All'){
            $sql .= " WHERE _local.fab_id=? ";
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _fab.id, _local.id ASC ";
        $stmt = $pdo->prepare($sql);
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
    // 分頁工具
    function page_div_local($start, $per, $request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _local.*, _fab.fab_title, _fab.fab_remark, _fab.flag AS fab_flag
                FROM `_local`
                LEFT JOIN _fab ON _local.fab_id = _fab.id
                -- WHERE _local.flag = 'On'
                ";
        if($fab_id != 'All'){
            $sql .= " WHERE _local.fab_id=? ";
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _fab.id, _local.id ASC ";
        $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料
        try {
            if($fab_id -= 'All'){
                $stmt->execute();
            }else{
                $stmt->execute([$fab_id]);
            }
            $rs = $stmt->fetchAll();
            return $rs;
        }catch(PDOException $e){
            echo $e->getMessage(); 
        }
    }
// Local


    // 設定low_level時用全local區域
    function show_allLocal(){
        $pdo = pdo();
        $sql = "SELECT _local.*, _site.site_title, _site.site_remark , _fab.fab_title, _fab.fab_remark, _fab.flag AS fab_flag
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
    // 設定low_level時用選則區域
    function select_local($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _local.*, _site.site_title, _site.remark as site_remark, _site.buy_ty, _fab.fab_title, _fab.remark as fab_remark
                FROM `_local`
                LEFT JOIN _site ON _local.site_id = _site.id
                LEFT JOIN _fab ON _site.fab_id = _fab.id
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
    // 秀出catalog全部
    function show_catalogs(){
        $pdo = pdo();
        $sql = "SELECT _catalog.*, users.cname, categories.cate_title, categories.id AS cate_id
                FROM _catalog 
                LEFT JOIN users ON users.id = _catalog.user_id
                LEFT JOIN categories ON _catalog.category_id = categories.id
                ORDER BY categories.id, _catalog.id ASC";
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
        // low_level資料前處理

            $catalog_id = array_filter($catalog_id);            // 去除陣列中空白元素
            $amount = array_filter($amount);                    // 去除陣列中空白元素
            // 小陣列要先編碼才能塞進去大陣列
                $catalog_id_enc = json_encode($catalog_id);
                $amount_enc = json_encode($amount);
            //陣列合併
                $low_level_arr = [];
                $low_level_arr['catalog_id'] = $catalog_id_enc;
                $low_level_arr['amount'] = $amount_enc;
            // implode()把陣列元素組合為字串：
                $low_level_str = $amount_enc;               // 陣列轉成字串進行儲存到mySQL

            $sql = "UPDATE _local
                    SET low_level=?, user_id=?, updated_at=now()
                    WHERE id=? ";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$low_level_str, $user_id, $local_id]);
            }catch(PDOException $e){
                echo $e->getMessage();
            }

    }