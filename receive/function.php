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
    // function show_myFab_lists($request){
        //     $pdo = pdo();
        //     extract($request);
        //     // 抓取USER資料，主要確認是否有負責多數廠區
        //         $check_user_sql = " SELECT u.* FROM _users u WHERE u.emp_id = ? ";
        //         $check_stmt = $pdo->prepare($check_user_sql);
        //         $check_stmt->execute([$emp_id]);
        //         $check_user = $check_stmt->fetch();

        //         if($check_user["sfab_id"] != ""){
        //             $user_Sfab_id = TRUE;       // 有多數廠區
        //         }else{
        //             $user_Sfab_id = FALSE;      // 只有單一
        //         }

        //     $sql = "SELECT u.emp_id, _f.*
        //             FROM `_users` AS u
        //             LEFT JOIN _fab AS _f ON u.fab_id = _f.id
        //             WHERE u.emp_id = ? ";
        //         if($user_Sfab_id){          // 有多數廠區就加入多數查詢
        //             $sql .= " UNION
        //                     SELECT u.emp_id, _f.*
        //                     FROM `_users` AS u
        //                     LEFT JOIN _fab AS _f ON FIND_IN_SET(_f.id, u.sfab_id)
        //                     WHERE u.emp_id = ? "; }
        //     // 後段-堆疊查詢語法：加入排序
        //     // $sql .= " ORDER BY _f.id ASC ";
        //     $stmt = $pdo->prepare($sql);
                    
        //     try {
        //         if($user_Sfab_id){
        //             $stmt->execute([$emp_id, $emp_id]);
        //         }else{
        //             $stmt->execute([$emp_id]);
        //         }
        //         $myFab_lists = $stmt->fetchAll();
        //         return $myFab_lists;

        //     }catch(PDOException $e){
        //         echo $e->getMessage();
        //     }
        // }
// // // index +統計數據 -- end

// // // 領用單 CRUD
    // 儲存_receive領用申請表單 20230803
    function store_receive($request){
        $pdo = pdo();
        extract($request);

        $fun = "store_receive";               // for swal_json
        $content_text = "領用申請--";        // for swal_json

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
                        , cata_SN_amount, idty, logs, created_emp_id, created_cname, updated_user, omager, in_sign
                        , created_at, updated_at , uuid) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,now(),now(),uuid())";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$plant, $dept, $sign_code, $emp_id, $cname, $extp, $local_id, $ppty, $receive_remark
                        , $cata_SN_amount_enc, $idty, $logs_enc, $created_emp_id, $created_cname, $created_cname, $omager, $in_sign]);
                $swal_json = array(
                    "fun" => $fun,
                    "action" => "success",
                    "content" => $content_text.'送出成功'
                );
            }catch(PDOException $e){
                echo $e->getMessage();
                $swal_json = array(
                    "fun" => $fun,
                    "action" => "error",
                    "content" => $content_text.'送出失敗'
                );
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
        // item資料前處理
            $cata_SN_amount_enc = json_encode(array_filter($cata_SN_amount));   // 去除陣列中空白元素，再要編碼
    
        // 把_receive表單logs叫近來處理
            $query = array('uuid'=> $uuid );

                $in_sign = $omager;                     // update送出回原主管，不回轉呈簽核
                $flow = "主管簽核";
                $idty_after = "1";                      // 由 5轉呈 存換成 1送出

            $receive_logs = showLogs($query);
        // 製作log紀錄前處理：塞進去製作元素
            $logs_request["action"] = $action;
            $logs_request["step"]   = $step."-編輯";
            $logs_request["idty"]   = $idty;
            $logs_request["cname"]  = $created_cname." (".$created_emp_id.")";
            $logs_request["logs"]   = $receive_logs["logs"];   
            $logs_request["remark"] = $sign_comm;   
        // 呼叫toLog製作log檔
            $logs_enc = toLog($logs_request);

        // 更新_receive表單
        $sql = "UPDATE _receive
                SET plant = ? , dept = ? , sign_code = ? , emp_id = ? , cname = ? , extp = ? , local_id = ? , ppty = ? , receive_remark = ?
                    , cata_SN_amount = ?, idty = ?, logs = ?, updated_user = ?, omager=?, in_sign=?, flow=?, updated_at = now()
                WHERE uuid = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$plant, $dept, $sign_code, $emp_id, $cname, $extp, $local_id, $ppty, $receive_remark, $cata_SN_amount_enc, $idty_after, $logs_enc, $updated_user, $omager, $in_sign, $flow, $uuid]);
            $swal_json = array(
                "fun" => "update_receive",
                "action" => "success",
                "content" => '領用申請--更新成功'
            );
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json = array(
                "fun" => "update_receive",
                "action" => "error",
                "content" => '領用申請--更新失敗'
            );
        }
        return $swal_json;
    }
    // 刪除單筆_receive紀錄 20230803
    function delete_receive($request){
        $pdo = pdo();
        extract($request);
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
    
        // 把_receive表單logs叫近來處理
            $query = array('uuid'=> $uuid );

            if($idty == 5 && !empty($in_sign)){             // case = 5轉呈
                $receive_row = show_receive($query);            // 調閱原表單
                $sign_comm .= " // 原待簽 ".$receive_row["in_sign"]." 轉呈 ".$in_sign;
                $flow = "轉呈簽核";
                $receive_logs["logs"] = $receive_row["logs"];   // 已調閱表單，直接取用logs

            }else{
                $receive_logs = showLogs($query);               // 未調閱表單，另外開表單讀logs

            }
            if(empty($receive_logs["logs"])){
                $receive_logs["logs"] = "";
            }

        // 製作log紀錄前處理：塞進去製作元素
            $logs_request["action"] = $action;
            $logs_request["step"]   = $step;   
            $logs_request["idty"]   = $idty;   
            $logs_request["cname"]  = $updated_user." (".$updated_emp_id.")";
            $logs_request["logs"]   = $receive_logs["logs"];   
            $logs_request["remark"] = $sign_comm;   
        // 呼叫toLog製作log檔
            $logs_enc = toLog($logs_request);
        // 更新_receive表單
        $sql = "UPDATE _receive 
                SET idty = ? , logs = ? , updated_user = ? , updated_at = now() ";
            if($idty == 5){                                         // case = 5轉呈
                $sql .= " , in_sign = ? , flow = ? ";
                $idty_after = "1";                                      // 由 5轉呈 存換成 1送出

            }else if($idty == 3){                                   // case = 3取消/作廢
                $sql .= " , in_sign = ? , flow = ? ";
                $in_sign = NULL;                                        // 由 存換成 NULL
                $flow = NULL ;                                          // 由 存換成 NULL
                $idty_after = $idty;                                    // 由 換成 3作廢

            }else if($idty == 0){                                   // case = 0同意
                $sql .= " , in_sign = ? , flow = ? ";
                $in_sign = NULL;                                        // 由 存換成 NULL
                $flow = 'collect';                                      // 由 存換成 collect 12待領
                $idty_after = 12;                                       // 由 0同意 存換成 12待領/待收

            }else if($idty == 4){                                   // case = 4編輯/作廢
                $sql .= " , in_sign = ? , flow = ? ";
                $in_sign = NULL;                                        // 由 存換成 NULL
                $flow = NULL;                                           // 由 存換成 NULL
                $idty_after = "1";                                      // 由 4編輯 存換成 1送出

            }else if($idty == 13){                                   // case = 13交貨 (Delivery)
                $sql .= " , in_sign = ? , flow = ? , cata_SN_amount = ? ";
                    // $query_omager = query_omager($updated_emp_id);      // 尋找業務負責人的環安主管。
                    // $in_sign = $query_omager['omager_emp_id'];          // 由 存換成 NULL ==> 業務負責人/負責人主管
                $in_sign = NULL;                                        // 由 12->13時，即業務窗口簽核，未到主管
                $flow = 'delivery';                                     // 由 存換成 delivery 13交貨
                $idty_after = $idty;                                    // 由 12待領 存換成 13交貨
                $cata_SN_amount_enc = json_encode(array_filter($cata_SN_amount));   // item資料前處理  // 去除陣列中空白元素再要編碼

            }else if($idty == 11){                                   // case = 11承辦 (Undertake)
                $sql .= " , in_sign = ? , flow = ? ";
                $query_fab_omager = query_fab_omager($fab_sign_code);   // 尋找FAB的環安主管。
                $in_sign = $query_fab_omager['OMAGER'];                 // 由 存換成 NULL ==> 業務負責人/負責人主管
                $flow = 'undertake';                                    // 由 存換成 undertake
                $idty_after = $idty;                                    // 由 11交貨 存換成 11交貨
            
            }else if($idty == 10){                                   // case = 10結案 (close)
                $sql .= " , in_sign = ? , flow = ? ";
                $in_sign = NULL;                                        // 由12->11時，即業務窗口簽核，未到主管
                $flow = 'close';                                        // 由 存換成 close
                $idty_after = $idty;                                    // 由 11交貨 存換成 11交貨

            }else{
                // *** 2023/10/24 這裏要想一下，主管簽完同意後，要清除in_sign和flow
                $idty_after = $idty;                                // 由 5轉呈 存換成 1送出
            }
        $sql .= " WHERE uuid = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            if((in_array($idty, [ 0, 3, 4, 5, 11, 10]))){               // case = 3取消/作廢、case = 5轉呈 4編輯(送出) 11承辦 10結案
                $stmt->execute([$idty_after, $logs_enc, $updated_user, $in_sign, $flow, $uuid]);

            }else if($idty == 13){                                  // case = 13交貨
                $stmt->execute([$idty_after, $logs_enc, $updated_user, $in_sign, $flow, $cata_SN_amount_enc, $uuid]);
                process_receive($request);                      // 呼叫處理fun 處理整張需求的交易事件(多筆)--stock扣帳事宜
                idty13pass11($request);                         // 20231109 當發放人==pm_emp_id則自動簽核，並跳到主管簽核11

            }else{
                $stmt->execute([$idty_after, $logs_enc, $updated_user, $uuid]);
            }
            $swal_json = array(
                "fun" => "sign_receive",
                "action" => "success",
                "content" => '領用申請--sign成功'
            );
        }catch(PDOException $e){
            echo $e->getMessage();
            print_r($e);
            $swal_json = array(
                "fun" => "sign_receive",
                "action" => "error",
                "content" => '領用申請--sign失敗'
            );
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

        // 把_receive表單叫近來處理
            $query = array('uuid'=> $uuid );
            $receive_row = show_receive($query);            // 調閱原表單
            $pm_emp_id = $receive_row["pm_emp_id"];
            $pm_emp_id_arr = explode(",",$pm_emp_id);       //資料表是字串，要炸成陣列
                // case != 13交貨 && 發貨人沒有在pm_emp_id名單中就返回
                // if($idty != 13 && !in_array($updated_emp_id, $pm_emp_id_arr)){ return;}
                if($idty != 13 || !in_array($updated_emp_id, $pm_emp_id_arr)){ 
                    return;
                }
            $receive_logs["logs"] = $receive_row["logs"];   // 已調閱表單，直接取用logs
            if(empty($receive_logs["logs"])){ $receive_logs["logs"] = ""; }
        // 製作log紀錄前處理：塞進去製作元素
            $logs_request["action"] = $action;
            $logs_request["step"]   = "業務承辦";   
            $logs_request["idty"]   = "11";   
            $logs_request["cname"]  = $updated_user." (".$updated_emp_id.")";
            $logs_request["logs"]   = $receive_logs["logs"];   
            $logs_request["remark"] = "自動簽核";   
        // 呼叫toLog製作log檔
            $logs_enc = toLog($logs_request);
        // 更新_receive表單
            $sql = "UPDATE _receive 
                    SET idty = ? , logs = ? , updated_user = ? , updated_at = now() , in_sign = ? , flow = ? 
                    WHERE uuid = ? ";

            $query_fab_omager = query_fab_omager($receive_row["fab_sign_code"]);    // 尋找FAB的環安主管。
            $in_sign = $query_fab_omager['OMAGER'];                                 // 由 存換成 NULL ==> 業務負責人/負責人主管
            $flow = 'undertake';                                                    // 由 存換成 undertake
            $idty_after = '11';                                                     // 由 13交貨 存換成 11承辦

            $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$idty_after, $logs_enc, $updated_user, $in_sign, $flow, $uuid]);
            $swal_json = array(
                "fun" => "sign_receive",
                "action" => "success",
                "content" => $logs_request["step"]."自動簽核--sign成功"
            );
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json = array(
                "fun" => "sign_receive",
                "action" => "error",
                "content" => $logs_request["step"]."自動簽核--sign失敗"
            );
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
        $log_remark = str_replace(array("\r\n","\r","\n"), " ", $remark);
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
        $sql_check = "SELECT _stk.* , _l.low_level , _f.id AS fab_id 
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
                    $p_amount = 0;                                              // 1.發放量餘額 = 0

                    $stmt = $pdo->prepare($sql);
                    try {
                        $stmt->execute([$stk_amount, $updated_user, $stk_row_list[$i]['id']]);
                            $process_result['result'] = "id:".$stk_row_list[$i]['id']."-".$stk_amount;      // 回傳 True: id - amount
        
                    }catch(PDOException $e){
                        echo $e->getMessage();
                            $process_result['error'] = "id:".($stk_row_list[$i]['id'] * -1);               // 回傳 False: - id
                    }
                
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
        
        }else{                                                                  // B.- 開新紀錄
            echo "<script>alert('case:4. 開新紀錄~')</script>";                             // deBug
            // step-1 先把local資料叫出來，抓取low_level數量
                $row_check = "SELECT _local.* FROM `_local` WHERE _local.id=? ";          
                $row = $pdo -> prepare($row_check);
                try {
                    $row -> execute([$p_local]);
                    $row_local = $row->fetch();
                    
                }catch(PDOException $e){
                    echo $e->getMessage();
                }

                if( $row -> rowCount() >0){                                                 // 有取得local資料
                    $row_lowLevel = json_decode($row_local["low_level"]);                   // 將local.low_level解碼
                    if(is_object($row_lowLevel)) { $row_lowLevel = (array)$row_lowLevel; }  // 將物件轉成陣列
                    if(isset($row_lowLevel[$cata_SN])){
                        $low_level = $row_lowLevel[$cata_SN];                                   // 取得該目錄品項的安全存量值
                    }else{
                        $low_level = 0;                                                         // 未取得local資料時，給他一個0
                    }
                }else{
                    $low_level = 0;                                                         // 未取得local資料時，給他一個0
                }
            
            // step-2 建立新紀錄到資料庫
                $p_amount *= -1;                                            // 2.發放量餘額 轉負數


                $sql = "INSERT INTO _stock(local_id, cata_SN, standard_lv, amount, stock_remark, lot_num, updated_user, created_at, updated_at)
                        VALUES(?, ?, ?, ?, ?, ?, ?, now(), now())";         // 2.建立新紀錄到資料庫
                $stmt = $pdo->prepare($sql);
                try {
                    $stmt->execute([$p_local, $cata_SN, $low_level, $p_amount, $stock_remark, $lot_num, $updated_user]);
                        $process_result['result'] = "++".$cata_SN."-".$p_amount;                   // 回傳 True: id - amount

                }catch(PDOException $e){
                    echo $e->getMessage();
                        $process_result['error']  = "--".$cata_SN."-".$p_amount;                   // 回傳 False: - id
                }
        }
        return $process_result;
    }
    // // 處理整張需求的交易事件(多筆)--所屬器材數量之加減
    function process_receive($request){            // 參數：uuid
        $pdo = pdo();
        extract($request);
        $query = array("uuid"=> $uuid);
        $process_remark = "";
        $receive_row = show_receive($query);                                            // 1.調閱原表單
        $cata_SN_amount = json_decode($receive_row["cata_SN_amount"]);                  // 1-1.取出需求清單並解碼
        if(is_object($cata_SN_amount)) { $cata_SN_amount = (array)$cata_SN_amount; }    // 1-2.將需求清單物件轉換成陣列(才有辦法取長度、取SN_key)
            $cata_SN_keys = array_keys($cata_SN_amount);                                // 1-3.取出需求清單的KEY(cata_SN)

        forEach($cata_SN_keys as $cata_SN_key){
            if(is_object($cata_SN_amount[$cata_SN_key])) { 
                $cata_SN_amount[$cata_SN_key] = (array)$cata_SN_amount[$cata_SN_key]; 
            }
            $process = array(
                "p_local" => $receive_row["local_id"],
                "cata_SN" => $cata_SN_key,
                "p_amount" => $cata_SN_amount[$cata_SN_key]["pay"],
                "updated_user" => $updated_user 
            );
            $process_result = process_cata_amount($process);            // 呼叫處理fun  處理交易事件(單筆)
            if($process_result["result"]){                                  // True - 抵扣完成
                $process_remark .= " // 扣帳成功: ".$process_result["result"];
            }else{                                                          // False - 抵扣失敗
                $process_remark .= " // 扣帳失敗: ".$process_result["error"];
            }
        }

        // 把_receive表單logs叫近來處理
            if($receive_row["logs"]){
                $receive_logs["logs"] = $receive_row["logs"];           // 已調閱表單，直接取用logs
            }else{
                $receive_logs = showLogs($query);               // 未調閱表單，另外開表單讀logs
            }
        // 製作log紀錄前處理：塞進去製作元素
            $logs_request["action"] = $action;
            $logs_request["step"]   = $step;   
            // $logs_request["idty"]   = $idty;   
            $logs_request["idty"]   = "14";   // '扣帳 (Debit)'
            $logs_request["cname"]  = $updated_user." (".$updated_emp_id.")";
            $logs_request["logs"]   = $receive_logs["logs"];   
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
// // 刪除
    // function delete_stock($request){
    //     $pdo = pdo();
    //     extract($request);
    //     $sql = "DELETE FROM _stock WHERE id = ?";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         $stmt->execute([$id]);
    //         $swal_json = array(
    //             "fun" => "delete_stock",
    //             "action" => "success",
    //             "content" => '刪除成功'
    //         );
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //         $swal_json = array(
    //             "fun" => "delete_stock",
    //             "action" => "error",
    //             "content" => '刪除失敗'
    //         );
    //     }
    //     return $swal_json;
    // }
    // //修改完成的editStock 進行Update        //from editStock call updateStock
    // function update_stock($request){
    //     $pdo = pdo();
    //     extract($request);
    //         // 20230809 新增確認同local_ld+catalog_id+lot_num的單子合併計算
    //         $sql_check = "SELECT * 
    //                       FROM _stock 
    //                       WHERE local_id =? AND cata_SN =? AND lot_num =? AND po_no=? AND id <>? ";
    //         $stmt_check = $pdo -> prepare($sql_check);
    //         $stmt_check -> execute([$local_id, $cata_SN, $lot_num, $po_no, $id]);

    //         if($stmt_check -> rowCount() >0){     
    //             // 確認no編號是否已經被註冊掉，用rowCount最快~不要用fetch
    //             echo "<script>alert('local同批號衛材已存在，將進行合併計算~')</script>";
    //             $row = $stmt_check -> fetch();
    //             $amount += $row["amount"];
    
    //             $sql = "UPDATE _stock
    //                     SET standard_lv=?, amount=?, stock_remark=CONCAT(stock_remark, CHAR(10), ?), po_no=?, lot_num=?, updated_user=?, updated_at=now()
    //                     WHERE id=?";
    //             $stmt = $pdo->prepare($sql);
    //             try{
    //                 $stmt->execute([$standard_lv, $amount, $stock_remark, $po_no, $lot_num, $updated_user, $row["id"]]);
    //                 $swal_json = array(
    //                     "fun" => "update_stock",
    //                     "action" => "success",
    //                     "content" => '合併套用成功'
    //                 );
    //                 delete_stock($request);     // 已合併到另一儲存項目，故需要刪除舊項目

    //             }catch(PDOException $e){
    //                 echo $e->getMessage();
    //                 $swal_json = array(
    //                     "fun" => "update_stock",
    //                     "action" => "error",
    //                     "content" => '合併套用失敗'
    //                 );
    //             }
    //         }else{
    //             // echo "<script>alert('local器材只有單一筆，不用合併計算~')</script>";
    //             $sql = "UPDATE _stock
    //                     SET local_id=?, cata_SN=?, standard_lv=?, amount=?, stock_remark=?, pno=?, po_no=?, lot_num=?, updated_user=?, updated_at=now()
    //                     WHERE id=?";
    //             $stmt = $pdo->prepare($sql);
    //             try {
    //                 $stmt->execute([$local_id, $cata_SN, $standard_lv, $amount, $stock_remark, $pno, $po_no, $lot_num, $updated_user, $id]);
    //                 $swal_json = array(
    //                     "fun" => "update_stock",
    //                     "action" => "success",
    //                     "content" => '更新套用成功'
    //                 );
    //             }catch(PDOException $e){
    //                 echo $e->getMessage();
    //                 $swal_json = array(
    //                     "fun" => "update_stock",
    //                     "action" => "error",
    //                     "content" => '更新套用失敗'
    //                 );
    //             }
    //         }
    //     return $swal_json;
    // }
// // // process_issue處理交易事件 -- end


    // deBug專用
    function deBug($request){
        extract($request);
        print_r($request);echo '<br>';
        echo '<hr>';
    }
// ---------
    // // 20230116-開啟需求單時，先讀取local衛材存量，供填表單時參考
    // function read_local_stock($request){
    //     $pdo = pdo();
    //     extract($request);
    //     $sql_old = "SELECT _cata.*, _cate.cate_title, _cate.id AS cate_id, ps.*
    //             FROM _cata 
    //             LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no
    //             LEFT JOIN (
    //                         SELECT _stock.cata_SN , sum(_stock.amount) AS amount, s.stock_stand, s.sqty
    //                         FROM `_stock`
    //                         LEFT JOIN _local ON _stock.local_id = _local.id
    //                         LEFT JOIN _cata ON _stock.cata_SN = _cata.SN
    //                         LEFT JOIN (
    //                                     SELECT concat_ws('_',_local.fab_id, _stock.local_id, _stock.cata_SN) AS tcc	
    //                                         , sum(_s.standard_lv) AS stock_stand	
    //                                         , sum(_stock.amount)-sum(_s.standard_lv) AS sqty	
    //                                     FROM `_stock`
    //                                     LEFT JOIN _local ON _stock.local_id = _local.id	
    //                                     LEFT JOIN (	
    //                                                 SELECT _stock.id, _stock.standard_lv	
    //                                                 FROM `_stock`
    //                                                 LEFT JOIN _local _l ON _stock.local_id = _l.id	
    //                                                 GROUP BY local_id, cata_SN	
    //                                                 ) _s ON _stock.id = _s.id	
    //                                     GROUP BY _stock.local_id, _stock.cata_SN	
    //                                 ) s ON concat_ws('_',_local.fab_id, _stock.local_id, _stock.cata_SN) = tcc	
    //                         WHERE _local.id=?
    //                         GROUP BY _local.id, _stock.cata_SN
    //                         ORDER BY cata_SN, lot_num ASC
    //                     ) ps ON	_cata.SN = ps.cata_SN
    //             ORDER BY _cate.id, _cata.id ASC ";

    //     $sql = "SELECT c.*, cate.cate_title, cate.id AS cate_id, ps.*
    //             FROM _cata c
    //             LEFT JOIN _cate cate ON c.cate_no = cate.cate_no
    //             LEFT JOIN (
    //                     SELECT s.cata_SN, SUM(s.amount) AS amount, s.stock_stand, s.sqty
    //                     FROM `_stock` s
    //                     LEFT JOIN _local l ON s.local_id = l.id
    //                     LEFT JOIN _cata c ON s.cata_SN = c.SN
    //                     LEFT JOIN (
    //                             SELECT CONCAT_WS('_', l.fab_id, s.local_id, s.cata_SN) AS tcc,
    //                                 SUM(s.standard_lv) AS stock_stand,
    //                                 SUM(s.amount) - SUM(s.standard_lv) AS sqty
    //                             FROM `_stock` s
    //                             LEFT JOIN _local l ON s.local_id = l.id
    //                             GROUP BY s.local_id, s.cata_SN
    //                         ) s ON CONCAT_WS('_', l.fab_id, s.local_id, s.cata_SN) = s.tcc
    //                     WHERE l.id = ?
    //                     GROUP BY l.id, s.cata_SN
    //                 ) ps ON c.SN = ps.cata_SN
    //             ORDER BY cate.id, c.id ASC ";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         $stmt->execute([$local_id]);
    //         $catalogs = $stmt->fetchAll();
    //         return $catalogs;
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }

    // // create時用自己區域
    // function show_local($request){
    //     $pdo = pdo();
    //     extract($request);
    //     $sql = "SELECT _local.*, _site.site_title, _site.remark as site_remark, _fab.fab_title, _fab.remark as fab_remark
    //             FROM `_local`
    //             LEFT JOIN _site ON _local.site_id = _site.id
    //             LEFT JOIN _fab ON _site.fab_id = _fab.id
    //             WHERE _local.flag='On' AND _local.site_id=?
    //             ORDER BY _fab.id, _local.id ASC";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         $stmt->execute([$site_id]);
    //         $locals = $stmt->fetchAll();
    //         return $locals;
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }

    // // 找出自己的資料 
    // function showMe($request){
    //     $pdo = pdo();
    //     extract($request);
    //     $sql = "SELECT * FROM users WHERE id = ?";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         $stmt->execute([$id]);
    //         $user = $stmt->fetch();
    //         return $user;
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }
    
    // // deBug專用
    // function deBug($request){
    //     extract($request);
    //     print_r($request);echo '<br>';
    //     echo '<hr>';
    // }


// // // 暫時停用
// // // index +統計數據
    // // 20230803 在_receive_list秀出所有_receive清單 // 20230803 嵌入分頁工具
    // function show_receive_list($request){
    //     $pdo = pdo();
    //     extract($request);
    //     $sql = "SELECT _r.* , _l.local_title , _l.local_remark , _f.fab_title , _f.fab_remark , _s.site_title , _s.site_remark 
    //                     -- , u.cname AS fab_cname
    //             FROM `_receive` _r
    //             LEFT JOIN _local _l ON _r.local_id = _l.id
    //             LEFT JOIN _fab _f ON _l.fab_id = _f.id
    //             LEFT JOIN _site _s ON _f.site_id = _s.id
    //             -- LEFT JOIN (SELECT * FROM _users WHERE role != '' AND role != 3) u ON u.fab_id = _l.fab_id
    //              ";
    //     // 處理 byUser or admin 不同顯示內容
    //     if($emp_id != 'All'){
    //         $sql .= " WHERE _r.emp_id=? OR _r.created_emp_id=? ";                   
    //         if($role <= 1){
    //             $sql .= " OR _r.idty=1 ";      //處理 byAdmin
    //         }
    //     }
    //     // 後段-堆疊查詢語法：加入排序
    //     $sql .= " ORDER BY _r.updated_at DESC";
    //     // 決定是否採用 page_div 20230803
    //         if(isset($start) && isset($per)){
    //             $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料=分頁
    //         }else{
    //             $stmt = $pdo->prepare($sql);                // 讀取全部=不分頁
    //         }

    //     try {
    //         if($emp_id != 'All'){
    //             $stmt->execute([$emp_id, $emp_id]);         //處理 by User 
    //         }else{
    //             $stmt->execute();                           //處理 by All
    //         }
    //         $issues = $stmt->fetchAll();
    //         return $issues;

    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }
    // // 20230808 在index表頭顯示各類別的數量：    // 統計看板--上：表單核簽狀態
    // function show_sum_receive($request){
    //     $pdo = pdo();
    //     extract($request);
    //     if($emp_id == 'All'){
    //         $sql = "SELECT DISTINCT _r.ppty, _r.idty,
    //                     (SELECT COUNT(*) FROM `_receive` _r2 WHERE  _r2.idty = _r.idty AND _r2.ppty = _r.ppty) AS idty_count
    //                 FROM `_receive` _r ";
    //     }else{
    //         $sql = "SELECT DISTINCT _r.ppty, _r.idty,
    //                     (SELECT COUNT(*) FROM `_receive` _r2 WHERE  _r2.idty = _r.idty AND _r2.ppty = _r.ppty AND ( _r2.emp_id=? OR _r2.created_emp_id=? )) AS idty_count
    //                 FROM `_receive` _r
    //                 WHERE _r.emp_id=? OR _r.created_emp_id=? ";             //處理 byUser
    //     }
    //     // 後段-堆疊查詢語法：加入排序
    //     $sql .= " ORDER BY _r.ppty, _r.idty ASC";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         if($emp_id == 'All'){
    //             $stmt->execute();                                           //處理 byAll
    //         }else{
    //             $stmt->execute([$emp_id, $emp_id, $emp_id, $emp_id]);       //處理 byUser
    //         }
    //         $sum_receive = $stmt->fetchAll();
    //         return $sum_receive;
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }
    // // 20230719 在index表頭顯示各類別的數量：    // 統計看板--下：轉PR單 ??? 
    // function show_sum_receive_ship($request){
    //     $pdo = pdo();
    //     extract($request);
    //     if($emp_id != 'All'){
    //         $sql = "SELECT DISTINCT _receive._ship, LEFT(_receive.in_date, 10) AS in_date,
    //                     (SELECT COUNT(*) FROM `_receive` _i2 WHERE _i2._ship = _receive._ship AND ( _i2.out_user_id=? OR _i2.in_user_id=? )) AS ship_count
    //                 FROM `_receive`
    //                 WHERE _receive._ship IS NOT NULL AND ( _receive.out_user_id=? OR _receive.in_user_id=? )";      //處理 byUser
    //     }else{
    //         $sql = "SELECT DISTINCT _receive._ship, LEFT(_receive.in_date, 10) AS in_date,
    //                     (SELECT COUNT(*) FROM `_receive` _i2 WHERE _i2._ship = _receive._ship) AS ship_count
    //                 FROM `_receive`
    //                 WHERE _receive._ship IS NOT NULL ";
    //     }
    //     // 後段-堆疊查詢語法：加入排序
    //     $sql .= " ORDER BY _receive._ship DESC";
    //     $stmt = $pdo->prepare($sql.' LIMIT 10');    // TOP 10
    //     try {
    //         if($emp_id != 'All'){
    //             $stmt->execute([$emp_id, $emp_id, $emp_id, $emp_id]);       //處理 byUser
    //         }else{
    //             $stmt->execute();                           //處理 byAll
    //         }
    //         $sum_receive_ship = $stmt->fetchAll();
    //         return $sum_receive_ship;
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }


// // // issue需求單 CRUD
    // // 儲存交易表單
    // function store_issue($request){
    //     $pdo = pdo();
    //     extract($request);
    //     // item資料前處理

    //         $catalog_SN = array_filter($catalog_SN);            // 去除陣列中空白元素
    //         $amount = array_filter($amount);                    // 去除陣列中空白元素
    //         // 小陣列要先編碼才能塞進去大陣列
    //             $catalog_SN_enc = json_encode($catalog_SN);
    //             $amount_enc = json_encode($amount);
    //         //陣列合併
    //             $item_arr = [];
    //             $item_arr['catalog_SN'] = $catalog_SN_enc;
    //             $item_arr['amount'] = $amount_enc;
    //         // implode()把陣列元素組合為字串：
    //             $item_str = implode("_," , $item_arr);               // 陣列轉成字串進行儲存到mySQL

    //         // 設定表單狀態idty=1待領
    //             $idty = '1';

    //     //// **** 儲存issue表單
    //         $sql = "INSERT INTO _issue(create_date, item, in_user_id, in_local, idty, logs, ppty)VALUES(now(),?,?,?,?,?,?)";
    //         $stmt = $pdo->prepare($sql);
    //         try {
    //             $stmt->execute([$item_str, $in_user_id, $in_local, $idty, $logs, $ppty]);
    //         }catch(PDOException $e){
    //             echo $e->getMessage();
    //         }

    // }
    // // 顯示被選定的issue表單
    // function showIssue($request){
    //     $pdo = pdo();
    //     extract($request);
    //     $sql = "SELECT _issue.*, users_o.cname as cname_o, users_i.cname as cname_i
    //                     , _local_o.local_title as local_o_title, _local_o.local_remark as local_o_remark
    //                     , _local_i.local_title as local_i_title, _local_i.local_remark as local_i_remark
    //                     , _fab_o.fab_title as fab_o_title, _fab_o.fab_remark as fab_o_remark
    //                     , _fab_i.fab_title as fab_i_title, _fab_i.fab_remark as fab_i_remark
    //                     , _site_o.id as site_o_id, _site_o.site_title as site_o_title, _site_o.site_remark as site_o_remark
    //                     , _site_i.id as site_i_id, _site_i.site_title as site_i_title, _site_i.site_remark as site_i_remark
    //             FROM `_issue`
    //             LEFT JOIN _users users_o ON _issue.out_user_id = users_o.emp_id
    //             LEFT JOIN _users users_i ON _issue.in_user_id = users_i.emp_id
    //             LEFT JOIN _local _local_o ON _issue.out_local = _local_o.id
    //             LEFT JOIN _fab _fab_o ON _local_o.fab_id = _fab_o.id
    //             LEFT JOIN _site _site_o ON _fab_o.site_id = _site_o.id
    //             LEFT JOIN _local _local_i ON _issue.in_local = _local_i.id
    //             LEFT JOIN _fab _fab_i ON _local_i.fab_id = _fab_i.id
    //             LEFT JOIN _site _site_i ON _fab_i.site_id = _site_i.id 
    //             WHERE _issue.id = ?";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         $stmt->execute([$id]);
    //         $issue = $stmt->fetch();
    //         return $issue;
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }
    // // 驗收動作的update表單
    // function update_issue($request){
    //     $pdo = pdo();
    //     extract($request);
    //     $issue_id = array('id' => $p_id);               // 指定issue_id
    //     $issue = showIssue($issue_id);                  // 把issue~原表單叫近來處理
    //         $b_ppty = $issue['ppty'];                       // 指定~原表單需求類別ppty
    //         $in_local = $issue['in_local'];                 // 指定~原收件區in_local
    //         $out_local = $issue['out_local'];               // 指定~原發貨區out_local
    //         $logs = $issue['logs'];                         // 指定~原表單記錄檔logs
    //         $b_idty = $issue['idty'];                       // 指定~原表單狀態b_idty
    //         $in_date = $issue['in_date'];                   // 指定~原表單狀態in_date
    //         $item_str = $issue["item"];                     // 把item整串(未解碼)存到$item_str
    //         $item_arr = explode("_," ,$item_str);           // 把字串轉成陣列進行後面的應用
    //         $item_dec = json_decode($item_arr[0]);          // 解碼後存到$item_dec     = catalog_id
    //         $amount_dec = json_decode($item_arr[1]);        // 解碼後存到$amount_dec   = amount
    //     //PHP stdClass Object轉array 
    //         if(is_object($item_dec)) { $item_dec = (array)$item_dec; } 
    //         if(is_object($amount_dec)) { $amount_dec = (array)$amount_dec; } 

    //     // V2 判斷前單$b_idty不是1待簽、12待領，就返回        
    //     if($b_idty == 1 || $b_idty == 12){
    //         $idty = $p_idty;
    //     }else{    
    //         echo "<script>alert('$b_idty.此表單在您簽核前已被改變成[非待簽核]狀態，請再確認，謝謝!');</script>";
    //         return;
    //     }
        
    //     // 12待收 => 10結案
    //     if($b_ppty == 1 && $b_idty == 12 && $p_idty == 10){
    //         // 逐筆呼叫處理
    //         foreach(array_keys($item_dec) as $it){
    //             // 假如po_num是空的，給他NA
    //             if(empty($po_num_dec[$it])){
    //                 $po_num_dec[$it] = 'NA';
    //             }
        
    //             $process = [];  // 清空預設值
    //             $process = array('stock_id' => $it,
    //                             'lot_num' => $in_date,             // lot_num = 批號/期限；因PM發貨時會把發貨日寫入in_date，所以只能暫時先吃他
    //                             'po_num' => $out_local,            // po_num = 採購編號；因PM發貨時會把PO_num寫入out_local
    //                             'catalog_id' => $item_dec[$it],    // catalog_id = 器材目錄id
    //                             'p_amount' => $amount_dec[$it],    // p_amount = 正常數量
    //                             'p_local' => $in_local,             // p_local = local單位id
    //                             'idty' => $b_idty);                // idty = 交易狀態
    //             process_issue($process);
    //         }
    //     }

    //     // 把原本沒有的塞進去
    //     $request['idty'] = $idty;   
    //     $request['cname'] = $_SESSION["AUTH"]["cname"];
    //     $request['logs'] = $logs;   
        
    //     // 呼叫toLog製作log檔
    //     $logs_enc = toLog($request);

    //     // 更新trade表單
    //     $sql = "UPDATE _issue 
    //             SET idty=?, logs=?, in_date=now() 
    //             WHERE id=? ";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         $stmt->execute([$p_idty, $logs_enc, $p_id]);
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }

    // }
    // // 刪除單筆Issue紀錄
    // function deleteIssue($request){
    //     $pdo = pdo();
    //     extract($request);
    //     $sql = "DELETE FROM _issue WHERE id = ?";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         $stmt->execute([$id]);
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }

    // // 20230719 在issue_list秀出所有issue清單
    // function show_issue_list($request){
    //     $pdo = pdo();
    //     extract($request);
    //     $sql = "SELECT _issue.*, users_o.cname as cname_o, users_i.cname as cname_i,
    //                     _local_o.local_title as local_o_title, _local_o.local_remark as local_o_remark,
    //                     _local_i.local_title as local_i_title, _local_i.local_remark as local_i_remark,
    //                     _fab_o.fab_title as fab_o_title, _fab_o.fab_remark as fab_o_remark, 
    //                     _fab_i.fab_title as fab_i_title, _fab_i.fab_remark as fab_i_remark, 
    //                     _site_o.id as site_o_id, _site_o.site_title as site_o_title, _site_o.site_remark as site_o_remark,
    //                     _site_i.id as site_i_id, _site_i.site_title as site_i_title, _site_i.site_remark as site_i_remark 
    //             FROM `_issue`
    //             LEFT JOIN _users users_o ON _issue.out_user_id = users_o.emp_id
    //             LEFT JOIN _users users_i ON _issue.in_user_id = users_i.emp_id
    //             LEFT JOIN _local _local_o ON _issue.out_local = _local_o.id
    //             LEFT JOIN _fab _fab_o ON _local_o.fab_id = _fab_o.id
    //             LEFT JOIN _site _site_o ON _fab_o.site_id = _site_o.id
    //             LEFT JOIN _local _local_i ON _issue.in_local = _local_i.id
    //             LEFT JOIN _fab _fab_i ON _local_i.fab_id = _fab_i.id
    //             LEFT JOIN _site _site_i ON _fab_i.site_id = _site_i.id ";
    //     if($emp_id != 'All'){
    //         if($_SESSION[$sys_id]["role"] <= 1){
    //             $sql .= " WHERE _issue.out_user_id=? OR _issue.in_user_id=? OR _issue.idty=1 ";      //處理 byUser
    //         }else{
    //             $sql .= " WHERE _issue.out_user_id=? OR _issue.in_user_id=? ";      //處理 byUser
    //         }
    //     }
    //     // 後段-堆疊查詢語法：加入排序
    //     $sql .= " ORDER BY create_date DESC";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         if($emp_id != 'All'){
    //             $stmt->execute([$emp_id, $emp_id]);         //處理 by User 
    //         }else{
    //             $stmt->execute();                           //處理 by All
    //         }
    //         $issues = $stmt->fetchAll();
    //         return $issues;
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }
    // // 20230719 分頁工具
    // function issue_page_div($start, $per, $request){
    //     $pdo = pdo();
    //     extract($request);
    //     $sql = "SELECT _issue.*, users_o.cname as cname_o, users_i.cname as cname_i,
    //                     _local_o.local_title as local_o_title, _local_o.local_remark as local_o_remark,
    //                     _local_i.local_title as local_i_title, _local_i.local_remark as local_i_remark,
    //                     _fab_o.fab_title as fab_o_title, _fab_o.fab_remark as fab_o_remark, 
    //                     _fab_i.fab_title as fab_i_title, _fab_i.fab_remark as fab_i_remark, 
    //                     _site_o.id as site_o_id, _site_o.site_title as site_o_title, _site_o.site_remark as site_o_remark,
    //                     _site_i.id as site_i_id, _site_i.site_title as site_i_title, _site_i.site_remark as site_i_remark 
    //             FROM `_issue`
    //             LEFT JOIN _users users_o ON _issue.out_user_id = users_o.emp_id
    //             LEFT JOIN _users users_i ON _issue.in_user_id = users_i.emp_id
    //             LEFT JOIN _local _local_o ON _issue.out_local = _local_o.id
    //             LEFT JOIN _fab _fab_o ON _local_o.fab_id = _fab_o.id
    //             LEFT JOIN _site _site_o ON _fab_o.site_id = _site_o.id
    //             LEFT JOIN _local _local_i ON _issue.in_local = _local_i.id
    //             LEFT JOIN _fab _fab_i ON _local_i.fab_id = _fab_i.id
    //             LEFT JOIN _site _site_i ON _fab_i.site_id = _site_i.id ";
    //     if($emp_id != 'All'){
    //         if($_SESSION[$sys_id]["role"] <= 1){
    //             $sql .= " WHERE _issue.out_user_id=? OR _issue.in_user_id=? OR _issue.idty=1 ";      //處理 byUser
    //         }else{
    //             $sql .= " WHERE _issue.out_user_id=? OR _issue.in_user_id=? ";      //處理 byUser
    //         }
    //     }
    //     // 後段-堆疊查詢語法：加入排序
    //     $sql .= " ORDER BY create_date DESC";
    //     $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料
    //     try {
    //         if($emp_id != 'All'){
    //             $stmt->execute([$emp_id, $emp_id]);       //處理 byUser
    //         }else{
    //             $stmt->execute();               //處理 byAll
    //         }
    //         $rs = $stmt->fetchAll();
    //         return $rs;
    //     }catch(PDOException $e){
    //         echo $e->getMessage(); 
    //     }
    // }
// // // issue  -- end

// // // issueAmoun待轉PR總表
    // // 在issue_list秀出所有issueAmount清單
    // function show_issueAmount($ppty){
    //     $pdo = pdo();
    //     // extract($request);
    //     $sql = "SELECT _issue.*,
    //                     _local_i.local_title as local_i_title, _local_i.local_remark as local_i_remark,              
    //                     _fab_i.id as fab_i_id, _fab_i.fab_title as fab_i_title, _fab_i.fab_remark as fab_i_remark, 
    //                     _site_i.id as site_i_id, _site_i.site_title as site_i_title, _site_i.site_remark as site_i_remark 
    //             FROM `_issue`
    //             LEFT JOIN _local _local_i ON _issue.in_local = _local_i.id
    //             LEFT JOIN _fab _fab_i     ON _local_i.fab_id = _fab_i.id
    //             LEFT JOIN _site _site_i   ON _fab_i.site_id = _site_i.id
    //             WHERE _issue.idty=0 AND _issue._ship IS NULL ";

    //     if($ppty != "All"){
    //         $sql .= " AND _issue.ppty=? ";
    //     }

    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         if($ppty != "All"){
    //             $stmt->execute([$ppty]);
    //         }else{
    //             $stmt->execute();
    //         }    
    //         $issuesAmount = $stmt->fetchAll();
    //         return $issuesAmount;
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }
    // // 20221220 在issue_list 以pr_no review_issueAmount清單
    // function review_issueAmount($pr_no){
    //     $pdo = pdo();
    //     // extract($request);
    //     $sql = "SELECT _issue.*,
    //                     _local_i.local_title as local_i_title, _local_i.local_remark as local_i_remark,          
    //                     _fab_i.id as fab_i_id, _fab_i.fab_title as fab_i_title, _fab_i.fab_remark as fab_i_remark, 
    //                     _site_i.id as site_i_id, _site_i.site_title as site_i_title, _site_i.site_remark as site_i_remark 
    //             FROM `_issue`
    //             LEFT JOIN _local _local_i ON _issue.in_local = _local_i.id
    //             LEFT JOIN _fab _fab_i     ON _local_i.fab_id = _fab_i.id
    //             LEFT JOIN _site _site_i   ON _fab_i.site_id = _site_i.id
    //             WHERE _issue._ship = ?";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         $stmt->execute([$pr_no]);      
    //         $issuesAmount = $stmt->fetchAll();
    //         return $issuesAmount;
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }
    // // from show_issueAmoun call updateIssue
    // // 20230723 issue進度Update => 轉PR
    // function update_issue2pr($request){
    //     $pdo = pdo();
    //     extract($request);
    //     $issue2pr = explode(",",$issue2pr);       //資料表是字串，要炸成陣列
    //     $issue2pr = array_unique($issue2pr);      //去除重複值，例如：一個廠有申請6項，以前跑6次log，現在改1次

    //     if(empty($issue2pr)){   // 假如要處理的issue id是空的，就返回
    //         echo "<script>alert('issue表單id有誤，請再確認~謝謝!');</script>";
    //         return;
    //     }
        
    //     $sql = "UPDATE _issue
    //             SET idty=?, _ship=?, logs=?, in_date=now()
    //             WHERE id=?";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         foreach($issue2pr as $issue2pr_id){

    //             $issue_id = array('id' => $issue2pr_id);      // 指定issue_id
    //             $row = showIssue($issue_id);                  // 把issue表單叫進來處理
    //             $logs = $row['logs'];                         // 指定表單記錄檔logs
    //             // 把原本沒有的塞進去
    //             $request['cname'] = $_SESSION["AUTH"]["cname"];
    //             $request['logs'] = $logs;   
        
    //             // 呼叫toLog製作log檔
    //             $logs_enc = toLog($request);
    //             $stmt->execute([$idty, $remark, $logs_enc, $issue2pr_id]);
    //         }
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }
    // // 20221220 issue進度Update => 發貨 
    // function update_pr2fab($request){
    //     $pdo = pdo();
    //     extract($request);
    //     $pr2fab = explode(",",$pr2fab);       //資料表是字串，要炸成陣列
    //     $pr2fab = array_unique($pr2fab);      //去除重複值，例如：一個廠有申請6項，以前跑6次log，現在改1次

    //     if(empty($pr2fab)){   // 假如要處理的issue id是空的，就返回
    //         echo "<script>alert('issue表單id有誤，請再確認~謝謝!');</script>";
    //         return;
    //     }
        
    //     $sql = "UPDATE _issue
    //             SET out_user_id=?, out_local=?, idty=?, logs=?, in_date=now()
    //             WHERE id=?";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         foreach($pr2fab as $pr2fab_id){

    //             $issue_id = array('id' => $pr2fab_id);      // 指定issue_id
    //             $row = showIssue($issue_id);                  // 把issue表單叫進來處理
    //             $logs = $row['logs'];                         // 指定表單記錄檔logs
    //             // 把原本沒有的塞進去
    //             $request['cname'] = $_SESSION["AUTH"]["cname"];
    //             $request['logs'] = $logs;   
        
    //             // 呼叫toLog製作log檔
    //             $logs_enc = toLog($request);
    //             $stmt->execute([$out_user_id, $remark, $idty, $logs_enc, $pr2fab_id]);
    //         }
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }
    // // 20221220 issue進度Update => 收貨
    // function update_getIssue($request){
    //     $pdo = pdo();
    //     extract($request);
    //     $sql = "UPDATE _issue
    //             SET idty=?, logs=?, in_date=now()
    //             WHERE id=? ";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         $issue_id = array('id' => $getIssue);      // 指定issue_id
    //         $row = showIssue($issue_id);                  // 把issue表單叫進來處理
    //         $logs = $row['logs'];                         // 指定表單記錄檔logs
    //         // 把原本沒有的塞進去
    //         $request['cname'] = $_SESSION["AUTH"]["cname"];
    //         $request['logs'] = $logs;   
    
    //         // 呼叫toLog製作log檔
    //         $logs_enc = toLog($request);
    //         $stmt->execute([$idty, $logs_enc, $getIssue]);
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }
// // // 待轉PR總表 -- end