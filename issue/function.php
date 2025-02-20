<?php
// // // index 統計數據
    // 20230719 在issue_list秀出所有issue清單   20240108
    function show_issue_list($request){
        $pdo = pdo();
        $stmt_arr = array();
        extract($request);
        $sql = "SELECT _issue.*, users_o.cname as cname_o, users_i.cname as cname_i
                       , _local_o.local_title as local_o_title, _local_o.local_remark as local_o_remark
                       , _local_i.local_title as local_i_title, _local_i.local_remark as local_i_remark
                       , _fab_o.id as fab_o_id, _fab_o.fab_title as fab_o_title, _fab_o.fab_remark as fab_o_remark
                       , _fab_i.id as fab_i_id, _fab_i.fab_title as fab_i_title, _fab_i.fab_remark as fab_i_remark
                       , _site_o.id as site_o_id, _site_o.site_title as site_o_title, _site_o.site_remark as site_o_remark
                       , _site_i.id as site_i_id, _site_i.site_title as site_i_title, _site_i.site_remark as site_i_remark
                FROM `_issue`
                LEFT JOIN _users users_o ON _issue.out_user_id = users_o.emp_id
                LEFT JOIN _users users_i ON _issue.in_user_id = users_i.emp_id
                LEFT JOIN _local _local_o ON _issue.out_local = _local_o.id
                LEFT JOIN _local _local_i ON _issue.in_local = _local_i.id
                LEFT JOIN _fab _fab_o ON _local_o.fab_id = _fab_o.id
                LEFT JOIN _fab _fab_i ON _local_i.fab_id = _fab_i.id
                LEFT JOIN _site _site_o ON _fab_o.site_id = _site_o.id
                LEFT JOIN _site _site_i ON _fab_i.site_id = _site_i.id ";
        if($_year != 'All'){
            $sql .= " WHERE year(_issue.create_date) = ? ";
            array_push($stmt_arr, $_year);
        }

        if($fab_id != "All"){                                           // 處理 fab_id != All 進行二階                  
            $sql .= ($_year != "All" ? " AND ":" WHERE ") ;
            if($fab_id == "allMy"){                                     // 處理 fab_id = allMy 我的轄區
                $sql .= " (_fab_o.id IN ( {$sfab_id} ) OR _fab_i.id IN ( {$sfab_id} )) ";     // = $sfab_id
            }else{                                                      // 處理 fab_id != allMy 就是單點fab_id
                $sql .= " ( ? IN (_fab_o.id, _fab_i.id)) ";             // ? = $fab_id
                array_push($stmt_arr, $fab_id);
            }
        }  

        if($is_emp_id != 'All'){
            $sql .= ($_year != "All" || $fab_id != "All" ? " OR ":" WHERE ") ;
            if($role <= 1){
                $sql .= " ( ( ? IN (_issue.out_user_id, _issue.in_user_id)) OR _issue.idty=1 ) ";  //處理 byUser
            }else{
                $sql .= " ( ? IN (_issue.out_user_id, _issue.in_user_id )) ";                      //處理 byUser
            }
            array_push($stmt_arr, $is_emp_id);
        }
            $sql .= " ORDER BY _issue.create_date DESC";
            if(isset($start) && isset($per)){
                $stmt = $pdo->prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料=分頁
            }else{
                $stmt = $pdo->prepare($sql);                            // 讀取全部=不分頁
            }
     
        try {
            if(($_year != 'All') || (($fab_id != "All") && ($fab_id != "allMy")) || ($is_emp_id != "All")){
                $stmt->execute($stmt_arr);                              //處理 byUser & byYear
            }else{
                $stmt->execute();                                       //處理 byAll
            }
            $issues = $stmt->fetchAll();
            return $issues;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20230719 在index表頭顯示各類別的數量：    // 統計看板--上：表單核簽狀態
    function show_sum_issue($request){
        $pdo = pdo();
        extract($request);
        if($emp_id != 'All'){
            $sql = "SELECT DISTINCT _issue.ppty, _issue.idty,
                        (SELECT COUNT(*) FROM `_issue` _i2 WHERE  _i2.idty = _issue.idty AND _i2.ppty = _issue.ppty
                         AND ( _i2.out_user_id=? OR _i2.in_user_id=? )) AS idty_count
                    FROM `_issue` 
                    WHERE _issue.out_user_id=? OR _issue.in_user_id=?";      //處理 byUser
        }else{
            $sql = "SELECT DISTINCT _issue.ppty, _issue.idty,
                        (SELECT COUNT(*) FROM `_issue` _i2 WHERE  _i2.idty = _issue.idty AND _i2.ppty = _issue.ppty) AS idty_count
                    FROM `_issue` ";
        }
        $sql .= " ORDER BY _issue.ppty, _issue.idty ASC";
        $stmt = $pdo->prepare($sql);
        try {
            if($emp_id != 'All'){
                $stmt->execute([$emp_id, $emp_id, $emp_id, $emp_id]);       //處理 byUser
            }else{
                $stmt->execute();                                           //處理 byAll
            }
            $sum_issue = $stmt->fetchAll();
            return $sum_issue;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20230719 在index表頭顯示各類別的數量：                                 // 統計看板--下：轉PR單
    function show_sum_issue_ship($request){
        $pdo = pdo();
        extract($request);
        if($emp_id != 'All'){
            $sql = "SELECT DISTINCT _issue._ship, LEFT(_issue.in_date, 10) AS in_date,
                        (SELECT COUNT(*) FROM `_issue` _i2 WHERE _i2._ship = _issue._ship AND ( _i2.out_user_id=? OR _i2.in_user_id=? )) AS ship_count
                    FROM `_issue`
                    WHERE _issue._ship IS NOT NULL AND ( _issue.out_user_id=? OR _issue.in_user_id=? ) ";      //處理 byUser
        }else{
            $sql = "SELECT DISTINCT _issue._ship, LEFT(_issue.in_date, 10) AS in_date,
                        (SELECT COUNT(*) FROM `_issue` _i2 WHERE _i2._ship = _issue._ship) AS ship_count
                    FROM `_issue`
                    WHERE _issue._ship IS NOT NULL ";
        }
        $sql .= " ORDER BY _issue._ship DESC";
        $stmt = $pdo->prepare($sql.' LIMIT 25');                            // TOP 25
        try {
            if($emp_id != 'All'){
                $stmt->execute([$emp_id, $emp_id, $emp_id, $emp_id]);       //處理 byUser
            }else{
                $stmt->execute();                                           //處理 byAll
            }
            $sum_issue_ship = $stmt->fetchAll();
            return $sum_issue_ship;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 在index秀出所有的衛材清單
    function show_Sub_stock($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT stock.*, users.cname, _site.id as site_id, _site.site_title, _site.remark as site_remark, 
                        _fab.id AS fab_id, _fab.fab_title, _fab.remark as fab_remark, 
                        _local.local_title, _local.remark as loccal_remark, _catalog.title as catalog_title, 
                        _catalog.unit as catalog_unit, categories.cate_title
                FROM `stock`
                LEFT JOIN users ON users.id = stock.user_id
                RIGHT JOIN _local ON stock.local_id = _local.id
                LEFT JOIN _site ON _local.site_id = _site.id
                LEFT JOIN _fab ON _site.fab_idd = _fab.i
                LEFT JOIN _catalog ON _catalog.id = stock.catalog_id
                LEFT JOIN categories ON _catalog.category_id = categories.id
                WHERE _site.id=? AND catalog_id IS NOT null
                ORDER BY site_id, fab_id, local_id, catalog_id, lot_num ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$site_id]);
            $stocks = $stmt->fetchAll();
            return $stocks;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 在index秀出所有的衛材清單
    function show_local_stock($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT stock.*, users.cname, _site.id as site_id, _site.site_title, _site.remark as site_remark, 
                        _fab.id AS fab_id, _fab.fab_title, _fab.remark as fab_remark, 
                        _local.local_title, _local.remark as loccal_remark, _catalog.title as catalog_title, 
                        _catalog.unit as catalog_unit, categories.cate_title
                FROM `stock`
                LEFT JOIN users ON users.id = stock.user_id
                RIGHT JOIN _local ON _local.id = stock.local_id
                LEFT JOIN _fab ON _fab.id = _local.fab_id
                LEFT JOIN _site ON _site.id = _local.site_id
                LEFT JOIN _catalog ON _catalog.id = stock.catalog_id
                LEFT JOIN categories ON _catalog.category_id = categories.id
                WHERE _local.id=? AND catalog_id IS NOT null
                ORDER BY site_id, fab_id, local_id, catalog_id, lot_num ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$local_id]);
            $stocks = $stmt->fetchAll();
            return $stocks;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20231019 在index表頭顯示我的待簽清單：    // 統計看板--左上：我的待簽清單 / 轄區申請單
    // 參數說明：2 $fun == 'inSign'       => 我的待簽清單     不考慮 $fab_id
    function show_my_inSign($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT DISTINCT _i.* 
                        , _l.local_title , _l.local_remark 
                        , _f.id AS fab_id , _f.fab_title , _f.fab_remark , _f.sign_code AS fab_sign_code , _f.pm_emp_id 
                        , _s.site_title , _s.site_remark 
                FROM `_issue` _i
                LEFT JOIN _local _l ON _i.in_local = _l.id
                LEFT JOIN _fab _f ON _l.fab_id = _f.id
                LEFT JOIN _site _s ON _f.site_id = _s.id ";

        if($fun == 'inSign'){                                           // 處理 $_2我待簽清單  idty = 1申請送出、11發貨後送出、13發貨
            $sql .= " WHERE (_i.idty IN (1, 11) AND _i.in_sign = ? ) OR (_i.idty = 13 AND FIND_IN_SET({$emp_id}, _f.pm_emp_id)) ";
            if($sys_role <= 1 ){                                        // 處理 $_2我待簽清單  idty = 1申請送出、11發貨後送出、13發貨
                $sql .= " OR (_i.idty IN (1, 11) AND _i.flow = 'PPEpm' ) ";
            }
        }
        $sql .= " ORDER BY _i.create_date DESC";
        if(isset($start) && isset($per)){
            $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per);   // 讀取選取頁的資料=分頁
        }else{
            $stmt = $pdo->prepare($sql);                                // 讀取全部=不分頁
        }
        try {
            if(in_array( $fun , ['inSign', 'myIssue'])){                // 處理 $_2我待簽清單inSign、$_1我申請單myIssue
                $stmt->execute([$emp_id]);
            } else {                                                    // $_5我的待領清單myCollect 'myCollect'
                $stmt->execute();
            }
            $my_inSign_lists = $stmt->fetchAll();
            return $my_inSign_lists;

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
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
    // 20231019 在index表頭顯示自己fab區域      // 處理 4我的轄區
    function show_myFab_lists($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _f.id , _f.fab_title , _f.fab_remark , _f.flag , _f.sign_code AS fab_sign_code , _f.pm_emp_id
                FROM _fab AS _f 
                WHERE _f.flag = 'On' ";
            
        if($fab_id != "All"){
            $sql .= " AND ( _f.id IN ({$sfab_id}) ";
            if($fab_id != "allMy"){
                $sql .= " OR _f.id = ? ) ";
            }else{
                $sql .= " ) ";
            }
        }

        $sql .= " ORDER BY _f.id ASC ";
        $stmt = $pdo->prepare($sql);
                
        try {
            if($fab_id != "All"){
                if($fab_id != "allMy"){
                    $stmt->execute([$fab_id]);
                }else{
                    $stmt->execute();
                }
            }else{
                $stmt->execute();
            }
            $myFab_lists = $stmt->fetchAll();
            return $myFab_lists;

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 取出年份清單 => 供面篩選
    function show_issue_GB_year(){
        $pdo = pdo();
        $sql = "SELECT DISTINCT year(_i.create_date) AS _year, month(_i.create_date) AS _month
                FROM `_issue` _i
                GROUP BY _i.create_date
                ORDER BY _i.create_date DESC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $checked_years = $stmt->fetchAll();
            return $checked_years;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20240119 查詢表單計畫 step-0
    function show_formplan($request){
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
        $sql .= " ORDER BY _plan.updated_at DESC ";
        $stmt = $pdo->prepare($sql);
        try {
            if(!empty($form_type)){
                $stmt->execute([$form_type]);
            }else{
                $stmt->execute();
            }
            $formplans = $stmt->fetchAll();
            return $formplans;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20240119 查詢表單計畫 step-1 回饋 true / false
    function show_plan($query_arr){
        $formplans = show_formplan($query_arr);         // 查詢表單計畫 20240118 == 讓表單呈現 ON 或 Off
        $s_time = date("Y-m-d");
        $e_time = date("Y-m-d");
        $_inplan = null;
        $case_title = "";
        foreach($formplans as $plan){                   // 遍歷每一筆計畫
            if($plan["onGoing"] == "true"){             // 假如計畫啟動中 + 區間 = Off
                if($plan["_inplan"] == "Off"){
                    $_inplan = false ;                  // 任何一個計畫的_inplan為Off，設為false
                    break;                              // 跳出迴圈，因為已經確定結果
                } else {
                    $_inplan = true ;                   // 反之就以on為主
                    if($plan["start_time"] < $s_time){
                        $s_time = $plan["start_time"];
                    }
                    if($plan["end_time"] > $e_time){
                        $e_time = $plan["end_time"];
                    }
                } 
            } 
            $case_title = $plan["case_title"];
        }
        $result = array(
            "case_title" => $case_title,
            "start_time" => $s_time,
            "end_time"   => $e_time,
            "_inplan"    => $_inplan
        );
        return $result;
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
    
// // // index 統計數據 -- end

// // // issue需求單 CRUD
    // 儲存issue交易表單 20240103
    function store_issue($request){
        $pdo = pdo();
        extract($request);
        $swal_json = array(                                 // for swal_json
            "fun"       => "store_issue",
            "content"   => "請購需求單--"
        );
        // item資料前處理
        $item_str = json_encode(array_filter($item));   // 去除陣列中空白元素再要編碼
        // 開新單送出時，強迫待簽==主管
        if($idty == 1 && $action == "create"){
            if(!empty($omager)){
                $in_sign = $omager;
            }else{
                $in_sign = "";
            }
        }
        $flow = "Manager";
        // 製作log紀錄前處理：塞進去製作元素
            $logs_request["action"] = $action;
            $logs_request["step"]   = $step;                // 節點-簽單人角色
            $logs_request["idty"]   = $idty;
            $logs_request["cname"]  = $created_cname." (".$created_emp_id.")";
            $logs_request["logs"]   = "";   
            $logs_request["remark"] = $sign_comm;   
        // 呼叫toLog製作log檔
            $logs = toLog($logs_request);
        //// **** 儲存issue表單
        $sql = "INSERT INTO _issue(plant, dept, sign_code, in_user_id, cname, extp, in_local, ppty, issue_remark, item
                            , idty, omager, in_sign, in_signName, flow, logs, created_emp_id, created_cname, updated_user
                            , updated_at, create_date )
                VALUES( ?,?,?,?,?,  ?,?,?,?,?,  ?,?,?,?,?,   ?,?,?,?,  now(),now() ) ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$plant, $dept, $sign_code, $emp_id, $cname, $extp, $in_local, $ppty, $issue_remark, $item_str
                            , $idty, $omager, $in_sign, $in_signName, $flow, $logs, $created_emp_id, $created_cname, $updated_user]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '送出成功';
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '送出失敗';
        }
        return $swal_json;
    }
    // 顯示被選定的issue表單 20240103
    function show_issue($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _issue.*, users_o.cname as cname_o, users_i.cname as cname_i
                        -- , _local_o.local_title as local_o_title, _local_o.local_remark as local_o_remark
                        , _local_i.local_title as local_i_title, _local_i.local_remark as local_i_remark
                        -- , _fab_o.id AS fab_o_id, _fab_o.fab_title as fab_o_title, _fab_o.fab_remark as fab_o_remark, _fab_o.pm_emp_id as fab_o_pm_emp_id
                        , _fab_i.id AS fab_i_id, _fab_i.fab_title as fab_i_title, _fab_i.fab_remark as fab_i_remark, _fab_i.sign_code AS fab_i_sign_code, _fab_i.pm_emp_id as fab_i_pm_emp_id
                        -- , _site_o.id as site_o_id, _site_o.site_title as site_o_title, _site_o.site_remark as site_o_remark
                        -- , _site_i.id as site_i_id, _site_i.site_title as site_i_title, _site_i.site_remark as site_i_remark
                FROM `_issue`
                LEFT JOIN _users users_o ON _issue.out_user_id = users_o.emp_id
                LEFT JOIN _users users_i ON _issue.in_user_id = users_i.emp_id
                -- LEFT JOIN _local _local_o ON _issue.out_local = _local_o.id
                LEFT JOIN _local _local_i ON _issue.in_local = _local_i.id
                -- LEFT JOIN _fab _fab_o ON _local_o.fab_id = _fab_o.id
                LEFT JOIN _fab _fab_i ON _local_i.fab_id = _fab_i.id
                -- LEFT JOIN _site _site_o ON _fab_o.site_id = _site_o.id
                -- LEFT JOIN _site _site_i ON _fab_i.site_id = _site_i.id 
                WHERE _issue.id = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $issue = $stmt->fetch();
            return $issue;
        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }
    // edit動作的update表單 20240104
    function update_issue($request){
        $pdo = pdo();
        extract($request);
        $swal_json = array(                                 // for swal_json
            "fun"       => "update_issue",
            "content"   => "更新表單--"
        );
        $issue_row = show_issue($request);                  // 1.調閱原表單
        // 20231207 加入同時送出被覆蓋的錯誤偵測
        if(isset($old_idty) && ($old_idty != $issue_row["idty"])){
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '同意失敗'.' !! 注意 !! 當您送出表單的同時，該表單型態已被修改，送出無效，請返回確認 ~';
            return $swal_json;
        }
        // item資料前處理
        $item_str = json_encode(array_filter($item));       // 去除陣列中空白元素再要編碼
        if($action == "edit"){
            $in_sign = $omager;                     // update送出回原主管，不回轉呈簽核
            $flow = "Manager";
            $idty = 1;
        }
        // 把_issue表單logs叫近來處理
            // $query = array('id'=> $id );
            // $issue_logs = showLogs($query);
        // 製作log紀錄前處理：塞進去製作元素
            $logs_request["action"] = $action;
            $logs_request["step"]   = $step."-編輯";
            $logs_request["idty"]   = $idty;
            $logs_request["cname"]  = $created_cname." (".$created_emp_id.")";
            $logs_request["logs"]   = $issue_row["logs"];  
            $logs_request["remark"] = $sign_comm;   
        // 呼叫toLog製作log檔
            $logs_enc = toLog($logs_request);
        // 更新_issue表單
        $sql = "UPDATE _issue 
                SET plant=?, dept=?, sign_code=?, in_user_id=?, cname=?, extp=?, in_local=?, ppty=?, issue_remark=?, item=?
                    , idty=?, omager=?, in_sign=?, in_signName=?, flow=?, logs=?, updated_user=?, updated_at=now()
                WHERE id=? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$plant, $dept, $sign_code, $emp_id, $cname, $extp, $in_local, $ppty, $issue_remark, $item_str
                            , $idty, $omager, $in_sign, $in_signName, $flow, $logs_enc, $updated_user, $id]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '更新成功';
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '更新失敗';
        }
        return $swal_json;
    }
    // 刪除單筆Issue紀錄 20240104
    function delete_issue($request){
        $pdo = pdo();
        extract($request);
        $swal_json = array(                                 // for swal_json
            "fun"       => "delete_issue",
            "content"   => "刪除表單--"
        );
        $issue_row = show_issue($request);                  // 1.調閱原表單
        // 20231207 加入同時送出被覆蓋的錯誤偵測
        if(isset($old_idty) && ($old_idty != $issue_row["idty"])){
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '送出失敗'.' !! 注意 !! 當您送出表單的同時，該表單型態已被修改，送出無效，請返回確認 ~';
            return $swal_json;
        }
        $sql = "DELETE FROM _issue WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            return true;
        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }
    // sign動作的_issue表單 20230807
    function sign_issue($request){
        $pdo = pdo();
        extract($request);
        $swal_json = array(                                 // for swal_json
            "fun"       => "sign_issue",
            "content"   => "請購需求單--"
        );
        $issue_row = show_issue($request);                      // 1.調閱原表單
        // 20231207 加入同時送出被覆蓋的錯誤偵測
        if(isset($old_idty) && ($old_idty != $issue_row["idty"])){
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '送出失敗'.' !! 注意 !! 當您送出表單的同時，該表單型態已被修改，送出無效，請返回確認 ~';
            return $swal_json;
        }
        if($idty == 5 && !empty($in_sign)){             // case = 5轉呈
            $sign_comm .= " // 原待簽 ".$issue_row["in_sign"]." 轉呈 ".$in_sign;
            $flow = "forward";
        }
        // 製作log紀錄前處理：塞進去製作元素
            $logs_request["action"] = $action;
            $logs_request["step"]   = $step;                                // 節點-簽單人角色
            $logs_request["idty"]   = $idty;   
            $logs_request["cname"]  = $updated_user." (".$updated_emp_id.")";
            $logs_request["logs"]   = $issue_row["logs"];   
            if($idty == 13){
                $po_no = strtoupper(trim(str_replace(' ', '', $po_no)));    // 去除空白、轉大寫
                $logs_request["remark"] = $po_no."：".$sign_comm;
            }else{
                $logs_request["remark"] = $sign_comm;
            }
        // 呼叫toLog製作log檔
            $logs_enc = toLog($logs_request);
        // 流程
        // (user)填寫需求單+送出 => 2.待簽/申請人主管簽核 => 3.待簽/業務負責人簽核 => 5.待簽/環安主管簽核 => 表單結案~
        // 更新_issue表單
        $sql = "UPDATE _issue 
                SET idty = ? , logs = ? , updated_user = ? , updated_at = now() , in_sign = ? , in_signName=? , flow = ? ";

                if($idty == 0){                                             // case = 0同意
                    $in_sign     = NULL;                                        // 由 存換成 NULL
                    $in_signName = NULL;                                        // 由 存換成 NULL
                    if( in_array($issue_row["flow"], ["Manager", "Forwarded"]) ){   // -- 當簽核者是主管或轉呈：
                        $flow        = 'PPEpm';                                     // 由 存換成 PPEpm
                        $idty_after  = 1;                                           // 由 0同意 存換成 1待簽
                    }else{                                                          // -- 當簽和者是PPEpm
                        $flow        = 'waitPR';                                    // 由 存換成 waitPR
                        $idty_after  = $idty;                                       // 由 0同意 存換成 0同意/待轉PR
                    }

                }else if($idty == 2){                                       // case = 2退回
                    $in_sign     = NULL;                                        // 由 存換成 NULL
                    $in_signName = NULL;                                        // 由 存換成 NULL
                    $flow        = 'Reject';                                    // 由 存換成 NULL
                    $idty_after  = $idty;                                       // 由 換成 2退回

                }else if($idty == 3){                                       // case = 3取消/作廢
                    $in_sign     = NULL;                                        // 由 存換成 NULL
                    $in_signName = NULL;                                        // 由 存換成 NULL
                    $flow        = "abort" ;                                    // 由 存換成 NULL
                    $idty_after  = $idty;                                       // 由 換成 3作廢

                }else if($idty == 4){                                       // case = 4編輯/作廢
                    $in_sign     = NULL;                                        // 由 存換成 NULL
                    $in_signName = NULL;                                        // 由 存換成 NULL
                    $flow        = "edit";                                      // 由 存換成 NULL
                    $idty_after  = "1";                                         // 由 4編輯 存換成 1送出

                }else if($idty == 5){                                       // case = 5轉呈
                    $flow        = 'Forwarded';                                 // 由 存換成 Forwarded
                    $idty_after = "1";                                          // 由 5轉呈 存換成 1送出

                }else if($idty == 10){                                      // case = 10結案 (close)
                    $sql        .= " , in_date = now() ";
                    $in_sign     = NULL;                                        // 由12->11時，即業務窗口簽核，未到主管
                    $in_signName = NULL;                                        // 由 存換成 NULL
                    $flow        = 'close';                                     // 由 存換成 close
                    $idty_after  = $idty;                                       // 由 11交貨 存換成 11交貨

                // 這一段在 update_issue2pr完成 由 0同意 存換成 11交貨
                // }else if($idty == 11){                                      // case = 11待交貨 (collect)
                    // $sql        .= " , in_sign = ? , in_signName=? , flow = ? ";
                    // $query_fab_omager = query_fab_omager($fab_sign_code);       // 尋找FAB的環安主管。
                    // $in_sign     = $query_fab_omager['OMAGER'];                 // 由 存換成 NULL ==> 業務負責人/負責人主管
                    // $in_signName = $query_fab_omager['cname'];                  // 由 存換成 NULL ==> 業務負責人/負責人主管
                    // $flow        = 'ESHmanager';                                // 由 存換成 ESHmanager
                    // $idty_after  = $idty;                                       // 由 0同意 存換成 11交貨
                
                // }else if($idty == 12){                                      // case = 12驗收 (acceptance)
                    // $sql .= " , in_date = now() ";
                    // $idty_after = 10;                                            // 存換成 10結案
            
                }else if($idty == 13){                                      // case = 13交貨 (Delivery)
                    $sql        .= " , item = ? , out_local = ? , out_user_id = ? ";
                        // $query_omager = query_omager($updated_emp_id);       // 尋找業務負責人的環安主管。
                        // $in_sign = $query_omager['omager_emp_id'];           // 由 存換成 NULL ==> 業務負責人/負責人主管
                    $in_sign     = NULL;                                        // 由 12->13時，即業務窗口簽核，未到主管
                    $in_signName = NULL;                                        // 由 存換成 NULL
                    $flow        = 'acceptance';                                // 由 存換成 acceptance/驗收 13交貨
                    $idty_after  = $idty;                                       // 由 11 存換成 13交貨
                    $item_enc    = json_encode(array_filter($item));            // item資料前處理--去除陣列中空白元素再要編碼

                }else{
                    // *** 2023/10/24 這裏要想一下，主管簽完同意後，要清除in_sign和flow
                    $sql = "UPDATE _issue 
                            SET idty = ? , logs = ? , updated_user = ? , updated_at = now() ";
                    $idty_after = $idty;                                        // 由 5轉呈 存換成 1送出
                }

        $sql .= " WHERE id = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            if((in_array($idty, [ 0, 1, 2, 3, 4, 5, 10 ]))){                // case = 2退回、3取消/作廢、5轉呈 4編輯(送出) 10結案 11承辦
                $stmt->execute([$idty_after, $logs_enc, $updated_user,  $in_sign, $in_signName, $flow, $id]);

                if($idty == 10){                                            // case = 10驗收 結案
                    process_issue($request);                                // 呼叫處理fun 處理整張需求的交易事件(多筆)--issue入帳事宜
                }

            }else if((in_array($idty, [ 13 ]))){                            // case = 13交貨
                $stmt->execute([$idty_after, $logs_enc, $updated_user,  $in_sign, $in_signName, $flow, $item_enc, $po_no, $updated_emp_id, $id]);

            }else{
                $stmt->execute([$idty_after, $logs_enc, $id]);
            }
            $swal_json["action"]   = "success";
            $swal_json["content"] .= 'sign成功';
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= 'sign失敗';
        }
        return $swal_json;
    }

        // 20231106 結案簽核時，送簽給主管環安 = 找出業務窗口的環安主管
        function query_omager($emp_id){
            $pdo = pdo_hrdb();
            // extract($request);
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
        // 20231109 當發放人==pm_emp_id則自動簽核，並跳到主管簽核11
        function idty13pass11($request){
            $pdo = pdo();
            extract($request);
    
            $swal_json = array(                                 // for swal_json
                "fun"       => "idty13pass11",
                "content"   => "自動簽核--"
            );
    
                $issue_row = show_issue($request);            // 調閱原表單
    
                $pm_emp_id = $issue_row["pm_emp_id"];
                $pm_emp_id_arr = explode(",",$pm_emp_id);       //資料表是字串，要炸成陣列
                    // case != 13交貨 && 發貨人沒有在pm_emp_id名單中就返回
                    // if($idty != 13 && !in_array($updated_emp_id, $pm_emp_id_arr)){ return;}
                    if($idty != 13 || !in_array($updated_emp_id, $pm_emp_id_arr)){ 
                        return;
                    }
            // $issue_logs["logs"] = $issue_row["logs"];   // 已調閱表單，直接取用logs
            // if(empty($issue_logs["logs"])){ $issue_logs["logs"] = ""; }
            // 製作log紀錄前處理：塞進去製作元素
                $logs_request["action"] = $action;
                $logs_request["step"]   = "業務承辦";   
                $logs_request["idty"]   = "11";   
                $logs_request["cname"]  = $updated_user." (".$updated_emp_id.")";
                $logs_request["logs"]   = $issue_row["logs"];   
                $logs_request["remark"] = "(自動簽核)";   
            // 呼叫toLog製作log檔
                $logs_enc = toLog($logs_request);
            // 更新_issue表單
                $sql = "UPDATE _issue 
                        SET idty = ? , logs = ? , updated_user = ? , updated_at = now() , in_sign = ? , in_signName = ? , flow = ? 
                        WHERE id = ? ";
                $query_fab_omager   = query_fab_omager($issue_row["fab_sign_code"]);    // 尋找FAB的環安主管。
                $in_sign            = $query_fab_omager['OMAGER'];                      // 由 存換成 NULL ==> 業務負責人/負責人主管
                $in_signName        = $query_fab_omager['cname'];                       // 由 存換成 NULL ==> 業務負責人/負責人主管
                $flow               = 'ESHmanager';                                     // 由 存換成 ESHmanager
                $idty_after         = '11';                                             // 由 13交貨 存換成 11承辦
                $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$idty_after, $logs_enc, $updated_user, $in_sign, $in_signName, $flow, $uuid]);
                $swal_json["action"]   = "success";
                $swal_json["content"] .= $logs_request["step"].'--sign成功';
            }catch(PDOException $e){
                echo $e->getMessage();
                $swal_json["action"]   = "error";
                $swal_json["content"] .= $logs_request["step"].'--sign失敗';
            }
            return $swal_json;
        }
// // // issue  -- end

// // // issueAmoun待轉PR總表
    // 在issue_list秀出所有issueAmount清單
    function show_issueAmount($ppty){
        $pdo = pdo();
        // extract($request);
        $sql = "SELECT _issue.*,
                        _local_i.local_title as local_i_title, _local_i.local_remark as local_i_remark,              
                        _fab_i.id as fab_i_id, _fab_i.fab_title as fab_i_title, _fab_i.fab_remark as fab_i_remark, 
                        _site_i.id as site_i_id, _site_i.site_title as site_i_title, _site_i.site_remark as site_i_remark 
                FROM `_issue`
                LEFT JOIN _local _local_i ON _issue.in_local = _local_i.id
                LEFT JOIN _fab _fab_i     ON _local_i.fab_id = _fab_i.id
                LEFT JOIN _site _site_i   ON _fab_i.site_id = _site_i.id
                WHERE _issue.idty=0 AND _issue._ship IS NULL ";

        if($ppty != "All"){
            $sql .= " AND _issue.ppty=? ";
        }

        $stmt = $pdo->prepare($sql);
        try {
            if($ppty != "All"){
                $stmt->execute([$ppty]);
            }else{
                $stmt->execute();
            }    
            $issuesAmount = $stmt->fetchAll();
            return $issuesAmount;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20221220 在issue_list 以pr_no review_issueAmount清單
    function review_issueAmount($pr_no){
        $pdo = pdo();
        $sql = "SELECT _issue.*,
                        _local_i.local_title as local_i_title, _local_i.local_remark as local_i_remark,          
                        _fab_i.id as fab_i_id, _fab_i.fab_title as fab_i_title, _fab_i.fab_remark as fab_i_remark, 
                        _site_i.id as site_i_id, _site_i.site_title as site_i_title, _site_i.site_remark as site_i_remark 
                FROM `_issue`
                LEFT JOIN _local _local_i ON _issue.in_local = _local_i.id
                LEFT JOIN _fab _fab_i     ON _local_i.fab_id = _fab_i.id
                LEFT JOIN _site _site_i   ON _fab_i.site_id = _site_i.id
                WHERE _issue._ship = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$pr_no]);      
            $issuesAmount = $stmt->fetchAll();
            return $issuesAmount;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // from show_issueAmoun call updateIssue
    // 20230723 issue進度Update => 轉PR
    function update_issue2pr($request){
        $pdo = pdo();
        extract($request);
        $issue2pr = explode(",",$issue2pr);         //資料表是字串，要炸成陣列
        $issue2pr = array_unique($issue2pr);        //去除重複值，例如：一個廠有申請6項，以前跑6次log，現在改1次

        if(empty($issue2pr)){                       // 假如要處理的issue id是空的，就返回
            echo "<script>alert('issue表單id有誤，請再確認~謝謝!');</script>";
            return;
        }
        $remark = strtoupper(trim(str_replace(' ', '', $remark)));    // PR開單確認，它存在remark中；去除空白、轉大寫
        $sql = "UPDATE _issue
                SET idty = ?, flow=?, _ship = ?, logs = ? 
                WHERE id = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            foreach($issue2pr as $issue2pr_id){
            // 把_issue表單logs叫近來處理
                $query = array('id'=> $issue2pr_id );
                $issue_logs = showLogs($query);
                if(empty($issue_logs["logs"])){
                    $issue_logs["logs"] = "";
                }
            // 製作log紀錄前處理：塞進去製作元素
                $logs_request["step"]   = $step;                    // 節點-簽單人角色
                $logs_request["idty"]   = $idty;   
                $logs_request["cname"]  = $updated_user." (".$updated_emp_id.")";
                $logs_request["logs"]   = $issue_logs["logs"];
                $logs_request["remark"] = $remark;
            // 呼叫toLog製作log檔
                $logs_enc = toLog($logs_request);
                $stmt->execute([$idty, $flow, $remark, $logs_enc, $issue2pr_id]);
            }
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20221220 issue進度Update => 發貨 
    function update_pr2fab($request){
        $pdo = pdo();
        extract($request);
        $pr2fab = explode(",",$pr2fab);       //資料表是字串，要炸成陣列
        $pr2fab = array_unique($pr2fab);      //去除重複值，例如：一個廠有申請6項，以前跑6次log，現在改1次
        if(empty($pr2fab)){   // 假如要處理的issue id是空的，就返回
            echo "<script>alert('issue表單id有誤，請再確認~謝謝!');</script>";
            return;
        }
        $sql = "UPDATE _issue
                SET out_user_id=?, out_local=?, idty=?, logs=?, in_date=now()
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            foreach($pr2fab as $pr2fab_id){
                $issue_id = array('id' => $pr2fab_id);      // 指定issue_id
                $row = show_issue($issue_id);                  // 把issue表單叫進來處理
                $logs = $row['logs'];                         // 指定表單記錄檔logs
                // 把原本沒有的塞進去
                $request['cname'] = $_SESSION["AUTH"]["cname"];
                $request['logs'] = $logs;   
                // 呼叫toLog製作log檔
                $logs_enc = toLog($request);
                $stmt->execute([$out_user_id, $remark, $idty, $logs_enc, $pr2fab_id]);
            }
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20221220 issue進度Update => 收貨
    function update_getIssue($request){
        $pdo = pdo();
        extract($request);
        $sql = "UPDATE _issue
                SET idty=?, logs=?, in_date=now()
                WHERE id=? ";
        $stmt = $pdo->prepare($sql);
        try {
            $issue_id = array('id' => $getIssue);      // 指定issue_id
            $row = show_issue($issue_id);                  // 把issue表單叫進來處理
            $logs = $row['logs'];                         // 指定表單記錄檔logs
            // 把原本沒有的塞進去
            $request['cname'] = $_SESSION["AUTH"]["cname"];
            $request['logs'] = $logs;   
            // 呼叫toLog製作log檔
            $logs_enc = toLog($request);
            $stmt->execute([$idty, $logs_enc, $getIssue]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
// // // 待轉PR總表 -- end

// // // Create表單會用到
    // 20230719 單選自己區域
    function select_local($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _l.*, _s.site_title, _s.site_remark, _f.fab_title, _f.fab_remark, _f.buy_ty 
                FROM `_local` _l
                LEFT JOIN _fab _f ON _l.fab_id = _f.id
                LEFT JOIN _site _s ON _f.site_id = _s.id
                WHERE _l.flag='On' AND _l.id=?
                ORDER BY _s.id, _f.id, _l.id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$local_id]);
            $local = $stmt->fetch();
            return $local;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20230719 create、撥補時用全區域
    function show_allLocal(){
        $pdo = pdo();
        $sql = "SELECT _l.*, _s.site_title, _s.site_remark, _f.fab_title, _f.fab_remark
                FROM `_local` _l
                LEFT JOIN _fab _f ON _l.fab_id = _f.id
                LEFT JOIN _site _s ON _f.site_id = _s.id
                WHERE _l.flag='On'
                ORDER BY _s.id, _f.id, _l.id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $locals = $stmt->fetchAll();
            return $locals;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20230116-開啟需求單時，先讀取local衛材存量，供填表單時參考
    function read_local_stock($request){
        $pdo = pdo();
        extract($request);
        $sql_old = "SELECT _cata.*, _cate.cate_title, _cate.id AS cate_id, ps.*
                FROM _cata 
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no
                LEFT JOIN (
                            SELECT _stock.cata_SN , sum(_stock.amount) AS amount, s.stock_stand, s.sqty
                            FROM `_stock`
                            LEFT JOIN _local ON _stock.local_id = _local.id
                            LEFT JOIN _cata ON _stock.cata_SN = _cata.SN
                            LEFT JOIN (
                                        SELECT concat_ws('_',_local.fab_id, _stock.local_id, _stock.cata_SN) AS tcc	
                                            , sum(_s.standard_lv) AS stock_stand	
                                            , sum(_stock.amount)-sum(_s.standard_lv) AS sqty	
                                        FROM `_stock`
                                        LEFT JOIN _local ON _stock.local_id = _local.id	
                                        LEFT JOIN (	
                                                    SELECT _stock.id, _stock.standard_lv	
                                                    FROM `_stock`
                                                    LEFT JOIN _local _l ON _stock.local_id = _l.id	
                                                    GROUP BY local_id, cata_SN	
                                                    ) _s ON _stock.id = _s.id	
                                        GROUP BY _stock.local_id, _stock.cata_SN	
                                    ) s ON concat_ws('_',_local.fab_id, _stock.local_id, _stock.cata_SN) = tcc	
                            WHERE _local.id=?
                            GROUP BY _local.id, _stock.cata_SN
                            ORDER BY cata_SN, lot_num ASC
                        ) ps ON	_cata.SN = ps.cata_SN
                ORDER BY _cate.id, _cata.id ASC ";

        $sql = "SELECT c.*, cate.cate_title, cate.id AS cate_id, ps.*, _p.part_no, _p.pno_remark, _p._year, _p.size
                FROM _cata c
                LEFT JOIN _cate cate ON c.cate_no = cate.cate_no
                LEFT JOIN _pno _p ON c.SN = _p.cata_SN
                LEFT JOIN (
                        SELECT s.cata_SN, SUM(s.amount) AS amount, s.stock_stand, s.sqty
                        FROM `_stock` s
                        LEFT JOIN _local l ON s.local_id = l.id
                        LEFT JOIN _cata c ON s.cata_SN = c.SN
                        LEFT JOIN (
                                SELECT CONCAT_WS('_', l.fab_id, s.local_id, s.cata_SN) AS tcc,
                                    SUM(s.standard_lv) AS stock_stand,
                                    SUM(s.amount) - SUM(s.standard_lv) AS sqty
                                FROM `_stock` s
                                LEFT JOIN _local l ON s.local_id = l.id
                                GROUP BY s.local_id, s.cata_SN
                            ) s ON CONCAT_WS('_', l.fab_id, s.local_id, s.cata_SN) = s.tcc
                        WHERE l.id = ?
                        GROUP BY l.id, s.cata_SN
                    ) ps ON c.SN = ps.cata_SN
                WHERE _p.flag = 'On'
                ORDER BY cate.id, c.id ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$local_id]);
            $catalogs = $stmt->fetchAll();
            return $catalogs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 秀出catalog全部
    // 20230721 開啟需求單時，先讀取local衛材存量，供填表單時參考
    function show_catalogs(){
        $pdo = pdo();
        $sql = "SELECT _cata.*, _cate.cate_title, _cate.cate_no , _cate.id AS cate_id , _pno.*
                FROM _cata
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no
                LEFT JOIN _pno ON _cata.SN = _pno.cata_SN
                -- WHERE _cata.flag = 'On'
                ORDER BY _cata.id ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $catalogs = $stmt->fetchAll();
            return $catalogs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
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

// // // Create表單會用到 -- end

// // // process_issue處理交易事件
    // 處理交易事件--所屬器材數量之加減
    function process_issue_old($process){
        $pdo = pdo();
        extract($process);
        // 先把舊資料叫出來，進行加扣數量
        $sql_check = "SELECT stock.* 
                        FROM `stock`
                        WHERE stock.local_id = ? AND stock.catalog_id = ? AND stock.lot_num = ?";          
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$p_local, $catalog_id, $lot_num]);
        if($stmt_check -> rowCount() >0){     
        // 已有紀錄
        $row = $stmt_check -> fetch();
            // 交易狀態：0完成/1待收/2退貨/3取消
            switch($idty){
                case "0":
                    $row['amount'] += $p_amount; 
                    break;
                case "1":
                    $row['amount'] -= $p_amount; 
                    break;
                case "2":
                    $row['amount'] += $p_amount; 
                    break;
                case "3":
                    // echo "<script>alert('local器材已存在，將進行合併計算~')</script>";
                    $row['amount'] += $p_amount; 
                    break;
                case "12":
                    $row['amount'] += $p_amount; 
                    break;
                default:
                    echo "<script>alert('請使用正確的交易狀態')</script>";
                    $msg = "請使用正確的idty交易狀態";
                    return $msg;
            }
            $sql = "UPDATE stock SET amount=?, updated_at=now()
                    WHERE stock.id=?";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$row['amount'], $row['id']]);
            }catch(PDOException $e){
                echo $e->getMessage();
            }
            return;
        
        }else{
        // 開新紀錄
            $sql = "INSERT INTO stock(local_id, catalog_id, standard_lv, amount, remark, lot_num, po_num, d_remark, user_id, created_at, updated_at)VALUES(?,?,?,?,?,?,?,?,?,now(),now())";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$p_local, $catalog_id, $p_amount, $p_amount, '', $lot_num, $po_num, '', $_SESSION[$sys_id]['id']]);   // remark & d_remark 都留空
            }catch(PDOException $e){
                echo $e->getMessage();
            }
        }
        
    }
// // // process_issue處理交易事件 -- end

// // // CSV & Log tools
    // 匯出CSV
    function export_csv($filename,$data){ 
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Content-Disposition: attachment;filename="' . $filename . '";');
        header('Content-Type: application/csv; charset=UTF-8');
        echo $data; 
    } 
    // 製作記錄JSON_Log檔
    function toLog($request){
        extract($request);
        // log資料前處理
        // 交易狀態：0完成/1待收/2退貨/3取消/12發貨
        switch($idty){
            case "0":   $action = '同意 (Approve)';         break;
            case "1":   $action = '送出 (Submit)';          break;
            case "2":   $action = '退回 (Reject)';          break;
            case "3":   $action = '作廢 (Abort)';           break;
            case "4":   $action = '編輯 (Edit)';            break;
            case "5":   $action = '轉呈 (Transmit)';        break;
            case "6":   $action = '暫存 (Save)';            break;
            case "10":  $action = '驗收結案 (Close)';       break;    // 結案 (Close)
            case "11":  $action = '轉PR';                   break;    // 承辦 (Undertake)
            case "12":  $action = '待收發貨 (Awaiting collection)';   break;
            case "13":  $action = '交貨 (Delivery)';        break;
            case "14":  $action = '庫存-扣帳 (Debit)';      break;
            case "15":  $action = '庫存-回補 (Replenish)';  break;
            case "16":  $action = '庫存-入帳 (Account)';    break;
            default:    $action = '錯誤 (Error)';           return;
        }

        if(!isset($logs)){
            $logs = [];
            $logs_arr =[];
        }else{
            $logs_dec = json_decode($logs);
            $logs_arr = (array) $logs_dec;
        }

        $app = [];                                  // 定義app陣列=appry
        date_default_timezone_set("Asia/Taipei");   // 設定台灣時區，用在陣列新增datetime
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
    // 讀取所有JSON_Log記錄
    function showLogs($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _issue.*
                FROM `_issue`
                WHERE _issue.id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $_issue = $stmt->fetch();
            return $_issue;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 刪除單項log值-20220215
    function updateLogs($request){
        $pdo = pdo();
        extract($request);
        $query = array('id'=> $id );
        // 把trade表單叫近來處理
            // $trade = showTrade($query);
        $trade = showLogs($query);
        //這個就是JSON格式轉Array新增字串==搞死我
        $logs_dec = json_decode($trade['logs']);
        $logs_arr = (array) $logs_dec;
        // unset($logs_arr[$log_id]);  // 他會產生index導致原本的表亂掉
        array_splice($logs_arr, $log_id, 1);  // 用這個不會產生index
        $logs = json_encode($logs_arr);
        $sql = "UPDATE _issue 
                SET logs=? 
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$logs, $id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
// // // CSV & Log tools -- end

// // // process_issue處理交易事件
    // 20231031 處理交易事件(單筆)--所屬器材數量之加減 case 11交貨 (Delivery)
    // ** 20230725 處理貨單時 已在stock內的只能用ID，不要用SN...因為可能有多筆同SN，而導致錯亂 ??!!
    function process_cata_amount($process){     // 參數：$p_local, $cata_SN, $p_amount, $updated_user
        $pdo = pdo();
        extract($process);
        if(!isset($updated_user)){
            $updated_user = $_SESSION["AUTH"]["cname"];               // 0.預設更新人
        }
        $lot_num        = "9999-12-31";                               // 0.批號/效期
        $stock_remark   = " *".$po_no."請購入帳：".$p_amount;                                // 0.備註
        // 先把舊資料叫出來，進行加扣數量參考基準
            $sql_check = "SELECT _stk.* , _l.low_level , _l.local_title , _f.id AS fab_id , _f.fab_title
                        FROM `_stock` _stk
                        LEFT JOIN _local _l ON _stk.local_id = _l.id 
                        LEFT JOIN _fab _f ON _l.fab_id = _f.id 
                        WHERE _stk.local_id = ? AND cata_SN = ? 
                        ORDER BY _stk.lot_num ASC ";          
            $stmt_check = $pdo -> prepare($sql_check);
            $stmt_check -> execute([$p_local, $cata_SN]);

        if($stmt_check -> rowCount() >0){                                           // A.- 已有紀錄
            $stk_row_list = $stmt_check -> fetchAll();
            $stk_row_list_length = count($stk_row_list);                            // 取stock件數長度
            $sql = "UPDATE _stock SET amount=?, stock_remark=?, updated_user=?, updated_at=now() WHERE id=? ";
            for($i = 0; $i < $stk_row_list_length; $i++ ){
                $stk_amount = $stk_row_list[$i]['amount'];                          // $stk_amount=品項儲存量
                $stk_amount += $p_amount;                                           // 1.儲存量餘額 = 儲存量 + 發放量
                $cama = array(
                    'icon'  => ' + ',     // log交易訊息中加減號
                    'title' => ' 入帳 '   // log交易訊息 動作
                );
                $stock_remark .= $stk_row_list[$i]['stock_remark'];
                $stmt = $pdo->prepare($sql);
                try {
                    $stmt->execute([$stk_amount, $stock_remark, $updated_user, $stk_row_list[$i]['id']]);
                        $process_result['result'] = $stk_row_list[$i]['fab_title'] . "_" . $stk_row_list[$i]['local_title'] . " " . $stk_row_list[$i]['cata_SN'] . $cama['icon'] . $p_amount . "=" . $stk_amount;      // 回傳 True: id + amount
                }catch(PDOException $e){
                    echo $e->getMessage();
                        $process_result['error'] = $stk_row_list[$i]['fab_title'] . "_" . $stk_row_list[$i]['local_title'] . " " . $cama['title'] . "id:".($stk_row_list[$i]['id'] * -1);               // 回傳 False: - id
                }
                $p_amount = 0;                                                      // 1.發放量餘額 = 0
                if($p_amount <= 0){
                    return $process_result;                                         // pay扣完了就離開
                }
            }
            return $process_result;
        }else{                                                                              // B.- 開新紀錄
            // echo "<script>alert('case:4. 開新紀錄~')</script>";                              // deBug
            // step-1 先把local資料叫出來，抓取low_level數量
                $row_check = "SELECT _l.* , _f.fab_title 
                                FROM `_local` _l
                                LEFT JOIN _fab _f ON _l.fab_id = _f.id  
                                WHERE _l.id = ? ";          
                $stmt_check = $pdo -> prepare($row_check);
                $stmt_check -> execute([$p_local]);
                if($stmt_check -> rowCount() >0){                                               // 有取得local資料
                    $row = $stmt_check->fetch();
                    $row_lowLevel = json_decode($row["low_level"]);                             // 將local.low_level解碼
                    if(is_object($row_lowLevel)) { $row_lowLevel = (array)$row_lowLevel; }      // 將物件轉成陣列
                    if(isset($row_lowLevel[$cata_SN])){
                        $low_level = $row_lowLevel[$cata_SN];                                   // 取得該目錄品項的安全存量值
                    }else{
                        $low_level = 0;                                                         // 未取得local資料時，給他一個0
                    }
                }else{
                    $low_level = 0;                                                             // 未取得local資料時，給他一個0
                }
            // step-2 建立新紀錄到資料庫
                // $p_amount *= -1;                                                             // 2.發放量餘額 轉負數
                $cama = array(
                    'icon'  => ' + ',     // log交易訊息中加減號
                    'title' => ' 入帳 '   // log交易訊息 動作
                );
                $sql = "INSERT INTO _stock(local_id, cata_SN, standard_lv, amount, stock_remark, lot_num, updated_user, created_at, updated_at)
                        VALUES(?, ?, ?, ?, ?, ?, ?, now(), now())";                             // 2.建立新紀錄到資料庫
                $stmt = $pdo->prepare($sql);
                try {
                    $stmt->execute([$p_local, $cata_SN, $low_level, $p_amount, $stock_remark, $lot_num, $updated_user]);
                    $process_result['result'] = $row['fab_title'] . "_" . $row['local_title'] . " +新 ". $cata_SN . $cama['icon'] . $p_amount . " = " . $p_amount;                   // 回傳 True: id - amount
                
                }catch(PDOException $e){
                    echo $e->getMessage();
                    $process_result['error'] = $row['fab_title'] . "_" . $row['local_title'] . " -新 ". $cata_SN . $cama['icon'] . $p_amount . " = " . $p_amount;                   // 回傳 False: - id
                }
        }
        return $process_result;
    }
    // // 處理整張需求的交易事件(多筆)--所屬器材數量之加減
    function process_issue($request){            // 參數：uuid
        $pdo = pdo();
        extract($request);
        $query = array("id"=> $id);
        $issue_row = show_issue($query);                                // 1.調閱原表單
        $process_remark = "";
        $item = json_decode($issue_row["item"]);                        // 1-1.取出需求清單並解碼
        if(is_object($item)) { $item = (array)$item; }                  // 1-2.將需求清單物件轉換成陣列(才有辦法取長度、取SN_key)
            $item_keys = array_keys($item);                             // 1-3.取出需求清單的KEY(item)
        forEach($item_keys as $ikey){
            if(is_object($item[$ikey])) {$item[$ikey] = (array)$item[$ikey]; }
            $process = array(
                "p_local"       => $issue_row["in_local"],
                "cata_SN"       => $ikey,
                "p_amount"      => $item[$ikey]["pay"],
                "po_no"         => $issue_row["out_local"],
                "updated_user"  => $updated_user 
            );
            $process_result = process_cata_amount($process);            // 呼叫處理fun  處理交易事件(單筆)
            if($process_result["result"]){                                  // True - 抵扣完成
                if(empty($process_remark)){
                    $process_remark = "## ".$process_result["result"];
                }else{
                    $process_remark .= "_rn_## ".$process_result["result"];
                }
            }else{                                                          // False - 抵扣失敗
                if(empty($process_remark)){
                    $process_remark = "## ".$process_result["error"];
                }else{
                    $process_remark .= "_rn_## ".$process_result["error"];
                }      
            }
        }
        // 製作log紀錄前處理：塞進去製作元素
            $logs_request["action"] = $action;
            $logs_request["step"]   = $step;   
            $logs_request["idty"]   = "16";   // '入帳 (Account)'
            $logs_request["cname"]  = $updated_user." (".$updated_emp_id.")";
            $logs_request["logs"]   = $issue_row["logs"];   
            $logs_request["remark"] = $process_remark;   
        // 呼叫toLog製作log檔
            $logs_enc = toLog($logs_request);
        // 更新uuid的log檔，注入扣帳資訊
            $log_sql = " UPDATE _issue SET logs = ? WHERE id = ? ";
            $stmt = $pdo->prepare($log_sql);
            try {
                $stmt->execute([$logs_enc, $id]);
            }catch(PDOException $e){
                echo $e->getMessage();
            }
        return;
    }

    // 找出自己的資料 
    function showMe($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $user = $stmt->fetch();
            return $user;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // create時用自己區域
    function show_local($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _local.*, _site.site_title, _site.remark as site_remark, _fab.fab_title, _fab.remark as fab_remark
                FROM `_local`
                LEFT JOIN _site ON _local.site_id = _site.id
                LEFT JOIN _fab ON _site.fab_id = _fab.id
                WHERE _local.flag='On' AND _local.site_id=?
                ORDER BY _fab.id, _local.id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$site_id]);
            $locals = $stmt->fetchAll();
            return $locals;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // 供query_otherSite使用
    function show_site(){
        $pdo = pdo();
        $sql = "SELECT _site.*, _fab.fab_title, _fab.remark AS fab_remark 
                FROM _site
                LEFT JOIN _fab ON _site.fab_id = _fab.id
                ORDER BY id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $sites = $stmt->fetchAll();
            return $sites;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // deBug專用
    function deBug($request){
        extract($request);
        print_r($request);echo '<br>';
        echo '<hr>';
    }





