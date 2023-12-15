<?php
// // // index +統計數據
    // 20231019 在index表頭顯示我的待簽清單：    // 統計看板--左上：我的待簽清單 / 轄區申請單
        // 參數說明：
        //     1 $fun == 'myReceive'    => 我的申請單
        //     2 $fun == 'inSign'       => 我的待簽清單     不考慮 $fab_id
        //     3 $fun == 'myFab'        => 我的轄區申請單   需搭配 $fab_id
        //          $fab_id == 'All'     就是show全部廠區
        //          $fab_id == 'allMy'   我的所屬轄區 fab & sfab
        //          $fab_id == 'fab_id'  指定單一廠區
        // 
    function show_my_receive($request){
        $pdo = pdo();
        extract($request);

        $sql = "SELECT DISTINCT _r.* , _l.local_title , _l.local_remark , _f.id AS fab_id , _f.fab_title , _f.fab_remark , _f.sign_code AS fab_sign_code , _f.pm_emp_id , _s.site_title , _s.site_remark 
                FROM `_receive` _r
                LEFT JOIN _local _l ON _r.local_id = _l.id
                LEFT JOIN _fab _f ON _l.fab_id = _f.id
                LEFT JOIN _site _s ON _f.site_id = _s.id ";

        if($fun == 'myReceive'){                                            // 處理 $_1我申請單  
            $sql .= " WHERE ? IN (_r.emp_id, _r.created_emp_id) ";

        }else if($fun == 'inSign'){                                         // 處理 $_2我待簽清單  idty = 1申請送出、11發貨後送出、13發貨
            // $sql .= " WHERE (_r.idty IN (1, 11, 13) AND _r.in_sign = ? ) ";
            $sql .= " WHERE (_r.idty IN (1, 11) AND _r.in_sign = ? ) OR (_r.idty = 13 AND FIND_IN_SET({$emp_id}, _f.pm_emp_id)) ";

        }else if($fun == 'myFab'){                                          // 處理 $_3轄區申請單  
            if($fab_id != "All"){                                           // 處理 fab_id != All 進行二階                  
                if($fab_id == "allMy"){                                     // 處理 fab_id = allMy 我的轄區
                    $sql .= " LEFT JOIN _users _u ON _l.fab_id = _u.fab_id OR FIND_IN_SET(_l.fab_id, _u.sfab_id)
                              WHERE (FIND_IN_SET(_l.fab_id, _u.sfab_id) OR (_l.fab_id = _u.fab_id)) AND (_u.emp_id = ?) ";
                }else{                                                      // 處理 fab_id != allMy 就是單點fab_id
                    $sql .= " WHERE _l.fab_id = ? ";
                }
            }                                                               // 處理 fab_id = All 就不用套用，反之進行二階

            if($is_emp_id != "All"){                                        // 處理過濾 is_emp_id != All  
                if($fab_id != "All"){                                       // 處理 fab_id != All 進行二階                  
                    $sql .= " AND ( '{$is_emp_id}' IN (_r.emp_id, _r.created_emp_id)) ";     // 申請單加上查詢對象的is_emp_id
                }else{
                    $sql .= " WHERE ( '{$is_emp_id}' IN (_r.emp_id, _r.created_emp_id)) ";     // 申請單加上查詢對象的is_emp_id
                }
            }

        } else if($fun == 'myCollect'){                                     // 處理 $_5我的待領清單  
                // $sql .= " WHERE ? IN (_r.emp_id, _r.created_emp_id) AND _r.idty = 0 ";
                // $sql .= " WHERE _l.fab_id IN ('{$sfab_id}') AND _r.idty = 0 ";
                // $sql .= " LEFT JOIN _users _u ON _l.fab_id = _u.fab_id OR FIND_IN_SET(_l.fab_id, _u.sfab_id)
                //           WHERE (FIND_IN_SET(_l.fab_id, _u.sfab_id) OR (_l.fab_id = _u.fab_id) OR _l.fab_id IN ({$sfab_id})) AND _u.emp_id = ? AND _r.idty = 12 ";
                // $sql .= " LEFT JOIN _users _u ON _l.fab_id = _u.fab_id OR FIND_IN_SET(_l.fab_id, _u.sfab_id)
                //           WHERE (FIND_IN_SET(_l.fab_id, _u.sfab_id) OR (_l.fab_id = _u.fab_id) OR _l.fab_id IN ({$sfab_id})) AND _r.idty = 12 ";
                $sql .= " LEFT JOIN _users _u ON _l.fab_id = _u.fab_id OR FIND_IN_SET(_l.fab_id, _u.sfab_id)
                          WHERE _l.fab_id IN ({$sfab_id}) AND _r.idty = 12 ";
        }
        
        // 後段-堆疊查詢語法：加入排序
        if($fun == 'myCollect'){
            $sql .= " ORDER BY _l.fab_id ASC, _r.created_at ASC";
        }else{
            $sql .= " ORDER BY _r.created_at DESC";
        }

        // 決定是否採用 page_div 20230803
        if(isset($start) && isset($per)){
            $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per);   // 讀取選取頁的資料=分頁
        }else{
            $stmt = $pdo->prepare($sql);                                // 讀取全部=不分頁
        }
        try {
            if(in_array( $fun , ['inSign', 'myReceive'])){           // 處理 $_2我待簽清單inSign、$_1我申請單myReceive
                $stmt->execute([$emp_id]);

            }else if ($fun == 'myFab' && $fab_id != 'All') {
                if($fab_id == 'allMy'){
                    $stmt->execute([$emp_id]);
                }else{
                    $stmt->execute([$fab_id]);
                }
                // $stmt->execute([$fab_id == 'allMy' ? $emp_id : $fab_id]);
                
            } else {                                                // $_5我的待領清單myCollect 'myCollect'
                $stmt->execute();
            }

            $my_receive_lists = $stmt->fetchAll();
            // if($fun == 'inSign'){  
            //     echo "</br>{$fun}/{$is_emp_id}：".$sql."</br><hr>";
            // }
            return $my_receive_lists;

        }catch(PDOException $e){
            echo $e->getMessage();
            // echo "</br>err:{$fun}/{$is_emp_id}：".$sql."</br><hr>";
        }
    }
    // 20231019 在index表頭顯示allFab區域       // 已包在 4 我的轄區中的fab_id中
    function show_allFab_lists(){
        $pdo = pdo();
        $sql = "SELECT _f.*
                FROM _fab AS _f 
                ORDER BY _f.id ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $allFab_lists = $stmt->fetchAll();
            return $allFab_lists;

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
            // echo "</br>success:{$sign_code}：".$sql."</br><hr>";
            return $coverFab_lists;

        }catch(PDOException $e){
            echo $e->getMessage();
            // echo "</br>err:{$sign_code}：".$sql."</br><hr>";
        }

    }
    // 20231019 在index表頭顯示自己fab區域      // 處理 4我的轄區
    function show_myFab_lists($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _f.id , _f.fab_title , _f.fab_remark , _f.flag , _f.sign_code AS fab_sign_code , _f.pm_emp_id
                FROM _fab AS _f ";
            
        if($fab_id != "All"){
            $sql .= " WHERE _f.id IN ({$sfab_id}) ";
            if($fab_id != "allMy"){
                $sql .= " OR _f.id = ? ";
            }
        }

        // 後段-堆疊查詢語法：加入排序
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
// // // index +統計數據 -- end

// // // 領用單 CRUD
    // 儲存_receive領用申請表單 20230803
    function store_receive($request){
        $pdo = pdo();
        extract($request);

        $swal_json = array(                                 // for swal_json
            "fun"       => "store_receive",
            "content"   => "領用申請--"
        );

        // item資料前處理
        $cata_SN_amount_enc = json_encode(array_filter($cata_SN_amount));   // 去除陣列中空白元素再要編碼
            
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
            $logs_request["step"]   = $step;                   // 節點
            $logs_request["idty"]   = $idty;
            $logs_request["cname"]  = $created_cname." (".$created_emp_id.")";
            $logs_request["logs"]   = "";   
            $logs_request["remark"] = $sign_comm;   
        // 呼叫toLog製作log檔
            $logs_enc = toLog($logs_request);

        //// **** 儲存receive表單
        $sql = "INSERT INTO _receive(plant, dept, sign_code, emp_id, cname, extp, local_id, ppty, receive_remark
                    , cata_SN_amount, idty, flow, logs, created_emp_id, created_cname, updated_user, omager, in_sign, in_signName
                    , created_at, updated_at , uuid) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,now(),now(),uuid())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$plant, $dept, $sign_code, $emp_id, $cname, $extp, $local_id, $ppty, $receive_remark
                    , $cata_SN_amount_enc, $idty, $flow, $logs_enc, $created_emp_id, $created_cname, $created_cname, $omager, $in_sign, $in_signName]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '送出成功';

        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '送出失敗';
        }
        return $swal_json;
    }
    // 顯示被選定的_receive表單 20230803
    function show_receive($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _r.* , _l.fab_id , _l.id AS local_id , _l.local_title , _l.local_remark , _f.fab_title , _f.fab_remark , _f.sign_code AS fab_sign_code , _f.pm_emp_id
                    -- , _s.site_title , _s.site_remark
                FROM `_receive` _r
                LEFT JOIN _local _l ON _r.local_id = _l.id
                LEFT JOIN _fab _f ON _l.fab_id = _f.id
                    -- LEFT JOIN _site _s ON _f.site_id = _s.id
                WHERE _r.uuid = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$uuid]);
            $receive_row = $stmt->fetch();
            return $receive_row;
        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }
    // edit動作的_receive表單
    function update_receive($request){
        $pdo = pdo();
        extract($request);

        $swal_json = array(                                 // for swal_json
            "fun"       => "update_receive",
            "content"   => "更新表單--"
        );

        $receive_row = show_receive($request);            // 調閱原表單
        // 20231207 加入同時送出被覆蓋的錯誤偵測
        if(isset($old_idty) && ($old_idty != $receive_row["idty"])){
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '送出失敗'.' !! 注意 !! 當您送出表單的同時，該表單型態已被修改，送出無效，請返回確認 ~';
            return $swal_json;
        }

        // item資料前處理
        $cata_SN_amount_enc = json_encode(array_filter($cata_SN_amount));   // 去除陣列中空白元素，再要編碼
    
        $in_sign = $omager;                     // update送出回原主管，不回轉呈簽核
        $flow = "Manager";
        $idty_after = "1";                      // 由 5轉呈 存換成 1送出
 
        // 把_receive表單logs叫近來處理
            // $query = array('uuid'=> $uuid );
            // $receive_logs = showLogs($query);
        // 製作log紀錄前處理：塞進去製作元素
            $logs_request["action"] = $action;
            $logs_request["step"]   = $step."-編輯";
            $logs_request["idty"]   = $idty;
            $logs_request["cname"]  = $created_cname." (".$created_emp_id.")";
            $logs_request["logs"]   = $receive_row["logs"];   
            $logs_request["remark"] = $sign_comm;   
        // 呼叫toLog製作log檔
            $logs_enc = toLog($logs_request);

        // 更新_receive表單
        $sql = "UPDATE _receive
                SET plant = ? , dept = ? , sign_code = ? , emp_id = ? , cname = ? , extp = ? , local_id = ? , ppty = ? , receive_remark = ?
                    , cata_SN_amount = ?, idty = ?, logs = ?, updated_user = ?, omager=?, in_sign=?, in_signName=?, flow=?, updated_at = now()
                WHERE uuid = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$plant, $dept, $sign_code, $emp_id, $cname, $extp, $local_id, $ppty, $receive_remark, 
                            $cata_SN_amount_enc, $idty_after, $logs_enc, $updated_user, $omager, $in_sign, $in_signName, $flow, $uuid]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '更新成功';
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '更新失敗';
        }
        return $swal_json;
    }
    // 刪除單筆_receive紀錄 20230803
    function delete_receive($request){
        $pdo = pdo();
        extract($request);
        
        $swal_json = array(                                 // for swal_json
            "fun"       => "delete_receive",
            "content"   => "刪除表單--"
        );

        $receive_row = show_receive($request);            // 調閱原表單
        // 20231207 加入同時送出被覆蓋的錯誤偵測
        if(isset($old_idty) && ($old_idty != $receive_row["idty"])){
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '送出失敗'.' !! 注意 !! 當您送出表單的同時，該表單型態已被修改，送出無效，請返回確認 ~';
            return $swal_json;
        }

        $sql = "DELETE FROM _receive WHERE uuid = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$uuid]);
            return true;
        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }
    // sign動作的_receive表單 20230807
    function sign_receive($request){
        $pdo = pdo();
        extract($request);
    
        $swal_json = array(                                 // for swal_json
            "fun"       => "sign_receive",
            "content"   => "領用申請單--"
        );

        $receive_row = show_receive($request);                    // 調閱原表單
        // 20231207 加入同時送出被覆蓋的錯誤偵測
        if(isset($old_idty) && ($old_idty != $receive_row["idty"])){
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '送出失敗'.' !! 注意 !! 當您送出表單的同時，該表單型態已被修改，送出無效，請返回確認 ~';
            return $swal_json;
        }

        if($idty == 5 && !empty($in_sign)){             // case = 5轉呈
            $sign_comm .= " // 原待簽 ".$receive_row["in_sign"]." 轉呈 ".$in_sign;
            $flow = "forward";
        }

        // 製作log紀錄前處理：塞進去製作元素
            $logs_request["action"] = $action;
            $logs_request["step"]   = $step;   
            $logs_request["idty"]   = $idty;   
            $logs_request["cname"]  = $updated_user." (".$updated_emp_id.")";
            $logs_request["logs"]   = $receive_row["logs"];   
            $logs_request["remark"] = $sign_comm;   
        // 呼叫toLog製作log檔
            $logs_enc = toLog($logs_request);

        // 更新_receive表單
        $sql = "UPDATE _receive 
                SET idty = ? , logs = ? , updated_user = ? , updated_at = now() ";

            if($idty == 0){                                   // case = 0同意
                $sql .= " , in_sign = ? , in_signName=? , flow = ? ";
                $in_sign = NULL;                                        // 由 存換成 NULL
                $in_signName = NULL;                                    // 由 存換成 NULL
                $flow = 'collect';                                      // 由 存換成 collect 12待領
                $idty_after = 12;                                       // 由 0同意 存換成 12待領/待收

            }else if($idty == 2){                                   // case = 2退回
                $sql .= " , in_sign = ? , in_signName=? , flow = ? ";
                $in_sign = NULL;                                        // 由 存換成 NULL
                $in_signName = NULL;                                    // 由 存換成 NULL
                $flow = 'Reject';                                       // 由 存換成 NULL
                $idty_after = $idty;                                    // 由 換成 2退回

            }else if($idty == 3){                                   // case = 3取消/作廢
                $sql .= " , in_sign = ? , in_signName=? , flow = ? ";
                $in_sign = NULL;                                        // 由 存換成 NULL
                $in_signName = NULL;                                    // 由 存換成 NULL
                $flow = "abort" ;                                       // 由 存換成 NULL
                $idty_after = $idty;                                    // 由 換成 3作廢

            }else if($idty == 4){                                   // case = 4編輯/作廢
                $sql .= " , in_sign = ? , in_signName=? , flow = ? ";
                $in_sign = NULL;                                        // 由 存換成 NULL
                $in_signName = NULL;                                    // 由 存換成 NULL
                $flow = "edit";                                           // 由 存換成 NULL
                $idty_after = "1";                                      // 由 4編輯 存換成 1送出

            }else if($idty == 5){                                         // case = 5轉呈
                $sql .= " , in_sign = ? , in_signName=? , flow = ? ";
                $idty_after = "1";                                      // 由 5轉呈 存換成 1送出

            }else if($idty == 10){                                   // case = 10結案 (close)
                $sql .= " , in_sign = ? , in_signName=? , flow = ? ";
                $in_sign = NULL;                                        // 由12->11時，即業務窗口簽核，未到主管
                $in_signName = NULL;                                    // 由 存換成 NULL
                $flow = 'close';                                        // 由 存換成 close
                $idty_after = $idty;                                    // 由 11交貨 存換成 11交貨

            }else if($idty == 11){                                   // case = 11承辦 (Undertake)
                $sql .= " , in_sign = ? , in_signName=? , flow = ? ";
                $query_fab_omager = query_fab_omager($fab_sign_code);   // 尋找FAB的環安主管。
                $in_sign = $query_fab_omager['OMAGER'];                 // 由 存換成 NULL ==> 業務負責人/負責人主管
                $in_signName = $query_fab_omager['cname'];              // 由 存換成 NULL ==> 業務負責人/負責人主管
                $flow = 'ESHmanager';                                   // 由 存換成 ESHmanager
                $idty_after = $idty;                                    // 由 11交貨 存換成 11交貨
                
            }else if($idty == 13){                                   // case = 13交貨 (Delivery)
                $sql .= " , in_sign = ? , in_signName=? , flow = ? , cata_SN_amount = ? ";
                    // $query_omager = query_omager($updated_emp_id);      // 尋找業務負責人的環安主管。
                    // $in_sign = $query_omager['omager_emp_id'];          // 由 存換成 NULL ==> 業務負責人/負責人主管
                $in_sign = NULL;                                        // 由 12->13時，即業務窗口簽核，未到主管
                $in_signName = NULL;                                    // 由 存換成 NULL
                $flow = 'PPEpm';                                        // 由 存換成 delivery 13交貨
                $idty_after = $idty;                                    // 由 12待領 存換成 13交貨
                $cata_SN_amount_enc = json_encode(array_filter($cata_SN_amount));   // item資料前處理  // 去除陣列中空白元素再要編碼
            
            }else{
                // *** 2023/10/24 這裏要想一下，主管簽完同意後，要清除in_sign和flow
                $idty_after = $idty;                                // 由 5轉呈 存換成 1送出
            }
        $sql .= " WHERE uuid = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            if((in_array($idty, [ 0, 2, 3, 4, 5, 11, 10]))){               // case = 2退回、3取消/作廢、5轉呈 4編輯(送出) 11承辦 10結案
                $stmt->execute([$idty_after, $logs_enc, $updated_user, $in_sign, $in_signName, $flow, $uuid]);

            }else if($idty == 13){                                  // case = 13交貨
                $stmt->execute([$idty_after, $logs_enc, $updated_user, $in_sign, $in_signName, $flow, $cata_SN_amount_enc, $uuid]);
                process_receive($request);                      // 呼叫處理fun 處理整張需求的交易事件(多筆)--stock扣帳事宜
                idty13pass11($request);                         // 20231109 當發放人==pm_emp_id則自動簽核，並跳到主管簽核11

            }else{
                $stmt->execute([$idty_after, $logs_enc, $updated_user, $uuid]);
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
                FROM STAFF u
                LEFT JOIN STAFF s ON u.omager = s.emp_id 
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
        // extract($request);
        $sql = "SELECT _d.OSHORT, _d.OFTEXT, _d.OMAGER, CONCAT(_s.NACHN, _s.VORNA) AS cname
                FROM [HCM_VW_DEPT08] _d
                LEFT JOIN [HCM_VW_EMP01_hiring] _s ON _d.OMAGER = _s.PERNR
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

            $receive_row = show_receive($request);            // 調閱原表單

            $pm_emp_id = $receive_row["pm_emp_id"];
            $pm_emp_id_arr = explode(",",$pm_emp_id);       //資料表是字串，要炸成陣列
                // case != 13交貨 && 發貨人沒有在pm_emp_id名單中就返回
                // if($idty != 13 && !in_array($updated_emp_id, $pm_emp_id_arr)){ return;}
                if($idty != 13 || !in_array($updated_emp_id, $pm_emp_id_arr)){ 
                    return;
                }
        // $receive_logs["logs"] = $receive_row["logs"];   // 已調閱表單，直接取用logs
        // if(empty($receive_logs["logs"])){ $receive_logs["logs"] = ""; }
        // 製作log紀錄前處理：塞進去製作元素
            $logs_request["action"] = $action;
            $logs_request["step"]   = "業務承辦";   
            $logs_request["idty"]   = "11";   
            $logs_request["cname"]  = $updated_user." (".$updated_emp_id.")";
            $logs_request["logs"]   = $receive_row["logs"];   
            $logs_request["remark"] = "(自動簽核)";   
        // 呼叫toLog製作log檔
            $logs_enc = toLog($logs_request);

        // 更新_receive表單
            $sql = "UPDATE _receive 
                    SET idty = ? , logs = ? , updated_user = ? , updated_at = now() , in_sign = ? , in_signName = ? , flow = ? 
                    WHERE uuid = ? ";

            $query_fab_omager = query_fab_omager($receive_row["fab_sign_code"]);    // 尋找FAB的環安主管。
            $in_sign = $query_fab_omager['OMAGER'];                                 // 由 存換成 NULL ==> 業務負責人/負責人主管
            $in_signName = $query_fab_omager['cname'];                              // 由 存換成 NULL ==> 業務負責人/負責人主管
            $flow = 'ESHmanager';                                                   // 由 存換成 ESHmanager
            $idty_after = '11';                                                     // 由 13交貨 存換成 11承辦

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
// // // 領用單 CRUD -- end

// // // Create表單會用到
    // 20230825 單選出貨區域
    function select_local($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _l.*, _s.site_title, _s.site_remark, _f.fab_title, _f.fab_remark, _f.buy_ty , _f.sign_code AS fab_signCode , _f.pm_emp_id
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
        $sql = "SELECT _l.*, _s.site_title, _s.site_remark, _f.fab_title, _f.fab_remark , _f.sign_code AS fab_sign_code , _f.pm_emp_id
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
    // 20230721 開啟需求單時，秀出catalog全部
    function show_catalogs(){
        $pdo = pdo();
        $sql = "SELECT _cata.*, _cate.cate_title, _cate.cate_no , _cate.id AS cate_id
                FROM _cata
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no
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
    
    function show_categories(){
        $pdo = pdo();
        $sql = "SELECT * FROM _cate ORDER BY id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $categories = $stmt->fetchAll();
            return $categories;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 在index表頭顯示各類別的數量：
    function show_sum_category(){
        $pdo = pdo();
        $sql = "SELECT _cate.id AS cate_id, _cate.cate_no, _cate.cate_title, _cate.cate_remark  
                        ,(SELECT COUNT(*) FROM `_cata` c WHERE c.cate_no = _cate.cate_no) AS 'catalog_count'
                FROM _cate ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $sum_category = $stmt->fetchAll();
            return $sum_category;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20230724 在index秀出所有的衛材清單
    function show_local_stock($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _stk.*
                        , _l.local_title, _l.local_remark
                        , _f.id AS fab_id, _f.fab_title, _f.fab_remark
                        , _s.id as site_id, _s.site_title, _s.site_remark
                        , _cata.*
                        , _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no 
                FROM `_stock` _stk 
                RIGHT JOIN _local _l ON _stk.local_id = _l.id 
                LEFT JOIN _fab _f ON _l.fab_id = _f.id 
                LEFT JOIN _site _s ON _f.site_id = _s.id 
                LEFT JOIN _cata ON _stk.cata_SN = _cata.SN 
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no 
                WHERE _l.id=? AND cata_SN IS NOT null
                -- ORDER BY catalog_id, lot_num ASC
                ORDER BY cata_SN ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$local_id]);
            $stocks = $stmt->fetchAll();
            return $stocks;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20230724 在index秀出所有的衛材清單
    function show_Sub_stock($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _stk.*
                        , _l.local_title, _l.local_remark
                        , _f.id AS fab_id, _f.fab_title, _f.fab_remark
                        , _s.id as site_id, _s.site_title, _s.site_remark
                        , _cata.pname, _cata.cata_remark, _cata.SN, _cata.unit as cata_unit
                        , _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no 
                FROM `_stock` _stk 
                LEFT JOIN _local _l ON _stk.local_id = _l.id 
                LEFT JOIN _fab _f ON _l.fab_id = _f.id 
                LEFT JOIN _site _s ON _f.site_id = _s.id 
                LEFT JOIN _cata ON _stk.cata_SN = _cata.SN 
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no 
                WHERE _f.id=? AND cata_SN IS NOT null
                ORDER BY site_id, fab_id, local_id, cata_SN, lot_num ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$site_id]);
            $stocks = $stmt->fetchAll();
            return $stocks;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
// // // Create表單會用到 -- end

// // // CSV & Log tools
    // 匯出CSV
    function export_csv($filename,$data){ 
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Content-Disposition: attachment;filename="' . $filename . '";');
        header('Content-Type: application/csv; charset=UTF-8');
        echo $data; 
    } 
    // 製作記錄JSON_Log檔   20230803
    function toLog($request){
        extract($request);
        // log資料前處理
        // 交易狀態：0完成/1待收/2退貨/3取消/12發貨
        switch($idty){
            case "0":   $action = '同意 (Approve)';        break;
            case "1":   $action = '送出 (Submit)';         break;
            case "2":   $action = '退回 (Reject)';         break;
            case "3":   $action = '作廢 (Abort)';          break;
            case "4":   $action = '編輯 (Edit)';           break;
            case "5":   $action = '轉呈 (Forwarded)';      break;
            case "6":   $action = '暫存 (Save)';           break;
            case "10":  $action = '同意 (Approve)';        break;    // 結案 (Close)
            case "11":  $action = '同意 (Approve)';        break;    // 承辦 (Undertake)
            case "12":  $action = '待收發貨 (Awaiting collection)';   break;
            case "13":  $action = '交貨 (Delivery)';       break;
            case "14":  $action = '庫存-扣帳 (Debit)';      break;
            case "15":  $action = '庫存-回補 (Replenish)';  break;
            case "16":  $action = '庫存-入賬 (Account)';    break;
            default:    $action = '錯誤 (Error)';         return;
        }

        if(!isset($logs)){
            $logs = [];
            $logs_arr =[];
        }else{
            $logs_dec = json_decode($logs);
            $logs_arr = (array) $logs_dec;
        }

        $app = [];  // 定義app陣列=appry
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
    // 讀取所有JSON_Log記錄 20230804
    function showLogs($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _receive.logs
                FROM `_receive`
                WHERE _receive.uuid = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$uuid]);
            $receive_logs = $stmt->fetch();
            return $receive_logs;

        }catch(PDOException $e){
            echo $e->getMessage();

        }
    }
    // 刪除單項log值-20230804
    function updateLogs($request){
        $pdo = pdo();
        extract($request);

        $query = array('uuid'=> $uuid );
        // 把_receive表單叫近來處理
        $receive = showLogs($query);
        //這個就是JSON格式轉Array新增字串==搞死我
        $logs_dec = json_decode($receive['logs']);
        $logs_arr = (array) $logs_dec;
        // unset($logs_arr[$log_id]);  // 他會產生index導致原本的表亂掉
        array_splice($logs_arr, $log_id, 1);  // 用這個不會產生index

        $logs = json_encode($logs_arr);
        $sql = "UPDATE _receive 
                SET logs = ? 
                WHERE uuid = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$logs, $uuid]);
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
        $stock_remark   = "* 發放欠額";                                // 0.備註

        // 先把舊資料叫出來，進行加扣數量參考基準
        $sql_check = "SELECT _stk.* , _l.low_level , _l.local_title , _f.id AS fab_id , _f.fab_title 
                      FROM `_stock` _stk
                      LEFT JOIN _local _l ON _stk.local_id = _l.id 
                      LEFT JOIN _fab _f ON _l.fab_id = _f.id 
                      WHERE _stk.local_id = ? AND cata_SN = ? 
                      ORDER BY _stk.lot_num ASC ";          
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$p_local, $cata_SN]);

        if($stmt_check -> rowCount() >0){                                       // A.- 已有紀錄
            // echo "<script>alert('process_trade:已有紀錄~')</script>";         // deBug
            $stk_row_list = $stmt_check -> fetchAll();
            $stk_row_list_length = count($stk_row_list);                        // 取stock件數長度

            $sql = "UPDATE _stock SET amount=?, updated_user=?, updated_at=now() WHERE id=? ";

            for($i = 0; $i < $stk_row_list_length; $i++ ){
                $stk_amount = $stk_row_list[$i]['amount'];                      // $stk_amount=品項儲存量
                
                // if($stk_amount >= $p_amount){                                   // 1.儲存量大於等於發放量
                //     echo "<script>alert('case:1.儲存量 >= 發放量: 儲存量({$stk_amount}) - 扣除量({$p_amount})')</script>";          // deBug
                //     $stk_amount -= $p_amount;                                   // 1.儲存量餘額 = 儲存量 - 發放量
                //     $p_amount = 0;                                              // 1.發放量餘額 = 0

                // }else{                                                          // 2.儲存量小於發放量
                //     echo "<script>alert('case:2.儲存量 < 發放量: 儲存量({$stk_amount}) - 扣除量({$p_amount})')</script>";              // deBug
                //     $p_amount -= $stk_amount;                                   // 2.發放量餘額 = 發放量 - 儲存量
                //     $stk_amount = 0;                                            // 2.儲存量餘額 = 0
                // }

                    $stk_amount -= $p_amount;                                   // 1.儲存量餘額 = 儲存量 - 發放量
                    $cama = array(
                        'icon'  => ' - ',     // log交易訊息中加減號
                        'title' => ' 扣帳 '   // log交易訊息 動作
                    );

                    $stmt = $pdo->prepare($sql);
                    try {
                        $stmt->execute([$stk_amount, $updated_user, $stk_row_list[$i]['id']]);
                        $process_result['result'] = $stk_row_list[$i]['fab_title'] . "_" . $stk_row_list[$i]['local_title'] . " " . $stk_row_list[$i]['cata_SN'] . $cama['icon'] . $p_amount . " = ".$stk_amount;      // 回傳 True: id - amount
        
                    }catch(PDOException $e){
                        echo $e->getMessage();
                        $process_result['error'] = $stk_row_list[$i]['fab_title'] . "_" . $stk_row_list[$i]['local_title'] . " " . $cama['title'] . "id:" . ($stk_row_list[$i]['id'] * -1);               // 回傳 False: - id
                    }
                    
                    $p_amount = 0;                                              // 1.發放量餘額 = 0
                    if($p_amount <= 0){
                        return $process_result;   // pay扣完了就離開
                    // }else if(($i + 1) >= $stk_row_list_length && $p_amount > 0){    // 3.stk現有筆數用完了，但還有需求餘額
                    //     echo "<script>alert('case:3. stk現有筆數用完了，但還有需求餘額: {$p_amount}')</script>";              // deBug
                    //     $p_amount *= -1;                                            // 3.發放量餘額 轉負數
                    //     $standard_lv = $stk_row_list[0]['standard_lv'];             // 3.安全存量

                    //     $sql = "INSERT INTO _stock(local_id, cata_SN, standard_lv, amount, stock_remark, lot_num, updated_user, created_at, updated_at)
                    //             VALUES(?, ?, ?, ?, ?, ?, ?, now(), now())";         // 3.建立新紀錄到資料庫
                    //     $stmt = $pdo->prepare($sql);
                    //     try {
                    //         $stmt->execute([$p_local, $cata_SN, $standard_lv, $p_amount, $stock_remark, $lot_num, $updated_user]);
                    //             $process_result['result'] = "++".$cata_SN."-".$p_amount;                   // 回傳 True: id - amount

                    //     }catch(PDOException $e){
                    //         echo $e->getMessage();
                    //             $process_result['error'] = "--".$cata_SN."-".$p_amount;                   // 回傳 False: - id
                    //     }
                    }
            }

            return $process_result;
        
        }else{                                                                              // B.- 開新紀錄
            // echo "<script>alert('case:4. 開新紀錄~')</script>";                             // deBug
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
                // $p_amount *= -1;                                                            // 2.發放量餘額 轉負數
                $t_amount = $p_amount * -1;                                                            // 2.發放量餘額 轉負數
                $cama = array(
                    'icon'  => ' - ',     // log交易訊息中加減號
                    'title' => ' 扣帳 '   // log交易訊息 動作
                );

                $sql = "INSERT INTO _stock(local_id, cata_SN, standard_lv, amount, stock_remark, lot_num, updated_user, created_at, updated_at)
                        VALUES(?, ?, ?, ?, ?, ?, ?, now(), now())";                             // 2.建立新紀錄到資料庫
                $stmt = $pdo->prepare($sql);
                try {
                    $stmt->execute([$p_local, $cata_SN, $low_level, $t_amount, $stock_remark, $lot_num, $updated_user]);
                    $process_result['result'] = $row['fab_title'] . "_" . $row['local_title'] . " +新 ". $cata_SN . $cama['icon'] . $p_amount . " = " . $t_amount;                   // 回傳 True: id - amount

                }catch(PDOException $e){
                    echo $e->getMessage();
                    $process_result['error'] = $row['fab_title'] . "_" . $row['local_title'] . " -新 ". $cata_SN . $cama['icon'] . $p_amount . " = " . $t_amount;                   // 回傳 False: - id
                }
        }
        return $process_result;
    }
    // // 處理整張需求的交易事件(多筆)--所屬器材數量之加減
    function process_receive($request){            // 參數：uuid
        $pdo = pdo();
        extract($request);
        $query = array("uuid"=> $uuid);
        $receive_row = show_receive($query);                                            // 1.調閱原表單
        $process_remark = "";

        $cata_SN_amount = json_decode($receive_row["cata_SN_amount"]);                  // 1-1.取出需求清單並解碼
        if(is_object($cata_SN_amount)) { $cata_SN_amount = (array)$cata_SN_amount; }    // 1-2.將需求清單物件轉換成陣列(才有辦法取長度、取SN_key)
            $cata_SN_keys = array_keys($cata_SN_amount);                                // 1-3.取出需求清單的KEY(cata_SN)

        forEach($cata_SN_keys as $ikey){
            if(is_object($cata_SN_amount[$ikey])) { $cata_SN_amount[$ikey] = (array)$cata_SN_amount[$ikey]; }

            $process = array(
                "p_local"       => $receive_row["local_id"],
                "cata_SN"       => $ikey,
                "p_amount"      => $cata_SN_amount[$ikey]["pay"],
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
            // $logs_request["idty"]   = $idty;   
            $logs_request["idty"]   = "14";   // '扣帳 (Debit)'
            $logs_request["cname"]  = $updated_user." (".$updated_emp_id.")";
            $logs_request["logs"]   = $receive_row["logs"];   
            $logs_request["remark"] = $process_remark;   
        // 呼叫toLog製作log檔
            $logs_enc = toLog($logs_request);
        // 更新uuid的log檔，注入扣帳資訊
            $log_sql = " UPDATE _receive SET logs = ? WHERE uuid = ? ";
            $stmt = $pdo->prepare($log_sql);
            try {
                $stmt->execute([$logs_enc, $uuid]);
            }catch(PDOException $e){
                echo $e->getMessage();
            }
        return;
    }
// // // process_issue處理交易事件 -- end

// // // 查詢待簽名單for send MAPP
function inSign_list(){
    $pdo = pdo();
    $sql = "SELECT _r.in_sign AS emp_id, _r.in_signName AS cname, COUNT(_r.in_sign) AS waiting
            FROM _receive _r
            WHERE _r.in_sign IS NOT NULL
            GROUP BY _r.in_sign
            HAVING _r.in_sign IS NOT NULL ";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute();
        $inSign_list = $stmt->fetchAll();
        return $inSign_list;

    }catch(PDOException $e){
        echo $e->getMessage();
        return false;
    }
}

function check_ip($request){
    extract($request);
    $local_pc = array(                      // 建立local_pc查詢陣列
        '127.0.0.1'   => '7132e2545d301024dfb18da07cceccedb41b4864',   // 127.0.0.1
        'tw059332n_1' => 'a2e9ef3a208c4882a99ec708d09cedc7ebb92bb6',   // tw059332n-10.53.90.184
        'tw059332n_2' => 'dc7f33a2a06752e87d62a7e75bd0feedbddf1cbd',   // tw059332n-169.254.69.80
        'tw059332n_3' => '0afa7ce76ab41ba4845e719d3246c48dadb611fd',   // tw059332n-10.53.110.83
        'tw074163p'   => 'c2cb37acb2c9eb3e4068ac55d278ac7d9bea85e3'    // tw074163p-10.53.90.114
    );
    $ip = sha1(md5($ip));
    
    if(in_array($ip, $local_pc)){
        return true;
    }else{
        return false;
    }
}

// // // 查詢待簽名單 -- end

    // deBug專用
    function deBug($request){
        extract($request);
        print_r($request);echo '<br>';
        echo '<hr>';
    }
// ---------
