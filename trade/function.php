<?php
// // // Trade index
    // 20230724 在trad_list秀出所有Trad清單 // 20230818 嵌入分頁工具    20240108
    function show_trade_list($request){
        $pdo = pdo();
        extract($request);
        if(!isset($_year)){
            $_year = "All";
        }
        $sql = "SELECT _trade.*, users_o.cname as cname_o, users_i.cname as cname_i
                        , _local_o.local_title as local_o_title, _local_o.local_remark as local_o_remark
                        , _local_i.local_title as local_i_title, _local_i.local_remark as local_i_remark
                        , _fab_o.id as fab_o_id, _fab_o.fab_title as fab_o_title, _fab_o.fab_remark as fab_o_remark
                        , _fab_i.id as fab_i_id, _fab_i.fab_title as fab_i_title, _fab_i.fab_remark as fab_i_remark
                        , _site_o.id as site_o_id, _site_o.site_title as site_o_title, _site_o.site_remark as site_o_remark
                        , _site_i.id as site_i_id, _site_i.site_title as site_i_title, _site_i.site_remark as site_i_remark
                FROM `_trade`
                LEFT JOIN _users users_o ON _trade.out_user_id = users_o.emp_id
                LEFT JOIN _users users_i ON _trade.in_user_id = users_i.emp_id 
                LEFT JOIN _local _local_o ON _trade.out_local = _local_o.id
                LEFT JOIN _local _local_i ON _trade.in_local = _local_i.id
                LEFT JOIN _fab _fab_o ON _local_o.fab_id = _fab_o.id
                LEFT JOIN _fab _fab_i ON _local_i.fab_id = _fab_i.id
                LEFT JOIN _site _site_o ON _fab_o.site_id = _site_o.id
                LEFT JOIN _site _site_i ON _fab_i.site_id = _site_i.id ";
        if($_year != 'All'){
            $sql .= " WHERE year(_trade.out_date) = ? ";
        }

        if($emp_id != 'All'){
            if($_year != 'All'){
                $sql .= " AND ( _trade.out_user_id=? OR _trade.in_user_id=? OR _fab_o.id=? OR _fab_i.id=? ) ";        //處理 byUser
            }else{
                $sql .= " WHERE ( _trade.out_user_id=? OR _trade.in_user_id=? OR _fab_o.id=? OR _fab_i.id=? ) ";      //處理 byUser
            }
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY out_date DESC";
        // 決定是否採用 page_div 20230803
            if(isset($start) && isset($per)){
                $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料=分頁
            }else{
                $stmt = $pdo->prepare($sql);                // 讀取全部=不分頁
            }

        try {
            if($emp_id != 'All'){
                if($_year != 'All'){
                    $stmt->execute([$_year, $emp_id, $emp_id, $fab_id, $fab_id]);       //處理 byUser & byYear
                }else{
                    $stmt->execute([ $emp_id, $emp_id, $fab_id, $fab_id]);              //處理 byUser &byAll
                }
            }else{
                if($_year != 'All'){
                    $stmt->execute([$_year]);              //處理 byYear
                }else{
                    $stmt->execute();                      //處理 byAll
                }
            }
            $trades = $stmt->fetchAll();
            return $trades;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20230724 在index表頭顯示各類別的數量：    // 統計看板--上：表單核簽狀態
    function show_sum_trade($request){
        $pdo = pdo();
        extract($request);
        if($emp_id != 'All'){
            $sql = "SELECT DISTINCT _trade.idty,
                        (SELECT COUNT(*) FROM `_trade` _i2 WHERE  _i2.idty = _trade.idty
                            AND ( _i2.out_user_id=? OR _i2.in_user_id=? )) AS idty_count
                    FROM `_trade` 
                    WHERE _trade.out_user_id=? OR _trade.in_user_id=?";      //處理 byUser
        }else{
            $sql = "SELECT DISTINCT _trade.idty,
                        (SELECT COUNT(*) FROM `_trade` _i2 WHERE  _i2.idty = _trade.idty) AS idty_count
                    FROM `_trade` ";
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _trade.idty ASC";
        $stmt = $pdo->prepare($sql);
        try {
            if($emp_id != 'All'){
                $stmt->execute([$emp_id, $emp_id, $emp_id, $emp_id]);       //處理 byUser
            }else{
                $stmt->execute();                                           //處理 byAll
            }
            $sum_trade = $stmt->fetchAll();
            return $sum_trade;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20230724 在index表頭顯示各類別的數量：    // 統計看板--下：轉PR單
    function show_sum_trade_ship($request){
        $pdo = pdo();
        extract($request);
        if($emp_id != 'All'){
            $sql = "SELECT DISTINCT _issue._ship, LEFT(_issue.in_date, 10) AS in_date,
                        (SELECT COUNT(*) FROM `_issue` _i2 WHERE _i2._ship = _issue._ship AND ( _i2.out_user_id=? OR _i2.in_user_id=? )) AS ship_count
                    FROM `_issue`
                    WHERE _issue._ship IS NOT NULL AND ( _issue.out_user_id=? OR _issue.in_user_id=? )";      //處理 byUser
        }else{
            $sql = "SELECT DISTINCT _issue._ship, LEFT(_issue.in_date, 10) AS in_date,
                        (SELECT COUNT(*) FROM `_issue` _i2 WHERE _i2._ship = _issue._ship) AS ship_count
                    FROM `_issue`
                    WHERE _issue._ship IS NOT NULL ";
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _issue._ship DESC";
        $stmt = $pdo->prepare($sql.' LIMIT 10');            // TOP 10
        try {
            if($emp_id != 'All'){
                $stmt->execute([$emp_id, $emp_id, $emp_id, $emp_id]);       //處理 byUser
            }else{
                $stmt->execute();                           //處理 byAll
            }
            $sum_issue_ship = $stmt->fetchAll();
            return $sum_issue_ship;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // // 20240108 在index表頭顯示我的待簽清單：    // 統計看板--左上：我的待簽清單 / 轄區申請單
    // // 參數說明：
    // //     2 $fun == 'inSign'       => 我的待簽清單     不考慮 $fab_id
    // function show_my_inSign($request){
    //     $pdo = pdo();
    //     extract($request);

    //     $sql = "SELECT DISTINCT _t.* 
    //                 , _l_i.local_title AS in_local_title, _l_i.local_remark AS in_local_remark
    //                 , _f_i.id AS in_fab_id , _f_i.fab_title AS in_fab_title, _f_i.fab_remark AS in_fab_reamrk, _f_i.sign_code AS in_fab_sign_code , _f_i.pm_emp_id AS in_fab_pm_emp_id 
    //                 , _s_i.site_title AS in_site_title, _s_i.site_remark AS in_site_reamrk 
    //                 , _l_o.local_title AS out_local_title, _l_o.local_remark AS out_local_remark
    //                 , _f_o.id AS out_fab_id , _f_o.fab_title AS out_fab_title, _f_o.fab_remark AS out_fab_reamrk, _f_o.sign_code AS out_fab_sign_code , _f_o.pm_emp_id AS out_fab_pm_emp_id 
    //                 , _s_o.site_title AS out_site_title, _s_o.site_remark AS out_site_reamrk 
    //             FROM `_trade` _t
    //             LEFT JOIN _local _l_i ON _t.in_local = _l_i.id
    //             LEFT JOIN _fab _f_i ON _l_i.fab_id = _f_i.id
    //             LEFT JOIN _site _s_i ON _f_i.site_id = _s_i.id
    //             LEFT JOIN _local _l_o ON _t.out_local = _l_o.id
    //             LEFT JOIN _fab _f_o ON _l_o.fab_id = _f_o.id
    //             LEFT JOIN _site _s_o ON _f_o.site_id = _s_o.id;
    //              ";

    //     if($fun == 'inSign'){                                         // 處理 $_2我待簽清單  idty = 1申請送出、11發貨後送出、13發貨
    //         // $sql .= " WHERE (_r.idty IN (1, 11, 13) AND _r.in_sign = ? ) ";
    //         $sql .= " WHERE (_t.idty IN (1, 11) AND _t.in_sign = ? ) OR (_t.idty = 13 AND FIND_IN_SET({$emp_id}, _f.pm_emp_id)) ";
    //     }
        
    //     // 後段-堆疊查詢語法：加入排序
    //     $sql .= " ORDER BY _r.created_at DESC";

    //     // 決定是否採用 page_div 20230803
    //     if(isset($start) && isset($per)){
    //         $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per);   // 讀取選取頁的資料=分頁
    //     }else{
    //         $stmt = $pdo->prepare($sql);                                // 讀取全部=不分頁
    //     }
    //     try {
    //         if(in_array( $fun , ['inSign', 'myTrade'])){            // 處理 $_2我待簽清單inSign、$_1我申請單myTrade
    //             $stmt->execute([$emp_id]);
    //         } else {                                                // $_5我的待領清單myCollect 'myCollect'
    //             $stmt->execute();
    //         }
    //         $my_inSign_lists = $stmt->fetchAll();
    //         return $my_inSign_lists;

    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }
    // 取出年份清單 => 供面篩選
    function show_trade_GB_year(){
        $pdo = pdo();
        $sql = "SELECT DISTINCT year(_t.out_date) AS _year
                FROM `_trade` _t
                GROUP BY _t.out_date
                ORDER BY _t.out_date DESC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $checked_years = $stmt->fetchAll();
            return $checked_years;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
// // // Trade index -- end

// // // Trade CRUD     export 調撥出庫
    // 儲存交易表單 20230919 edition
    function store_trade($request){
        $pdo = pdo();
        extract($request);
        $process_remark = "";

        if($form_type == "export"){             // export=出庫，需要執行預扣庫存
            $swal_json = array(                                 // for swal_json
                "fun"       => "store_trade",
                "content"   => "調撥出庫--"
            );
            // **** 預扣功能    
                // StdObject轉換成Array
                if(is_object($item)) { $item = (array)$item; } 
                // 逐筆呼叫處理
                foreach(array_keys($item) as $item_key){
                    $item_key_arr = explode(",", $item_key);
                        if($item_key_arr[0]){ $cata_SN = $item_key_arr[0]; } else { $cata_SN = ""; }        // 序號
                        if($item_key_arr[1]){ $stk_id  = $item_key_arr[1]; } else { $stk_id  = ""; }        // 儲存id
    
                    $item_value = $item[$item_key];
                    $item_value_arr = explode(",", $item_value);
                        if($item_value_arr[0]){ $amount  = $item_value_arr[0]; } else { $amount  = ""; }    // 數量
                        if($item_value_arr[1]){ $po_no   = $item_value_arr[1]; } else { $po_no   = ""; }    // pn號碼
                        if($item_value_arr[2]){ $lot_num = $item_value_arr[2]; } else { $lot_num = ""; }    // 批號
    
                    $process = [];  // 清空預設值
                    // 打包處理訊息process
                    $process = array('updated_user' => $updated_user,
                                     'stock_id'     => $stk_id,
                                     'lot_num'      => $lot_num,
                                     'po_no'        => $po_no,
                                     'cata_SN'      => $cata_SN,
                                     'p_amount'     => $amount,
                                     'p_local'      => $out_local,      // 預扣出庫id存量
                                     'idty'         => $idty );
                    // 呼叫預扣原本數量功能=呼叫process_trade($request)
                    $process_result = process_trade($process);  // 呼叫處理fun 處理整張需求的交易事件(一次一筆)--stock扣帳事宜
                    if(isset($process_result["result"])){                                  // True - 抵扣完成
                        if(empty($process_remark)){
                            $process_remark = "## ".$process_result["result"];
                        }else{
                            $process_remark .= "_rn_## ".$process_result["result"];
                        }
                    }else{                                                                  // False - 抵扣失敗
                        if(empty($process_remark)){
                            $process_remark = "## ".$process_result["error"];
                        }else{
                            $process_remark .= "_rn_## ".$process_result["error"];
                        }                                                          
                    }
                }
            // **** 預扣功能 end  

        }else if($form_type == "import"){       // import=入庫，不須執行預扣
            $swal_json = array(                                 // for swal_json
                "fun"       => "store_restock",
                "content"   => "其他入庫--"
            );
            $out_local = $po_no;                // 因為其他入庫沒有out_local，所以導入pm_no
            $process_result = true;             // 但要把$process_result = true;讓寫入工作可以繼續
        }

        if($process_result){
            // echo "<script>alert('預扣功能：success')</script>";
            // item資料前處理
            $item_enc = json_encode(array_filter($item));          // 去除陣列中空白元素再要編碼
    
            // 製作log紀錄前處理：塞進去製作元素
                $logs_request["action"] = $action;
                $logs_request["step"]   = $step;                                    // 節點-簽單人角色
                $logs_request["idty"]   = $idty;                                    // 表單狀態
                $logs_request["cname"]  = $updated_user." (".$out_user_id.")";      // 開單人
                $logs_request["logs"]   = "";   
                $logs_request["remark"] = $sign_comm;   
            // 呼叫toLog製作log檔
                $logs_enc = toLog($logs_request);

            // 20231121 追加插入log：1送出=預扣、2退件=回補
                if(in_array($idty, [1 ,2]) && $form_type == "export"){
                    $logs_request["logs"]   = $logs_enc;
                    $logs_request["remark"] = $process_remark;  
                    
                    switch($idty){
                        case "1":           // 1送出=14扣帳
                            $logs_request["idty"]   = "14";                   // 表單狀態
                            break;
                        case "2":           // 2退件=15回補
                            $logs_request["idty"]   = "15";                   // 表單狀態
                            break;
                        default:            // 預定失效 
                            return; 
                            break;
                    }
                    $logs_enc = toLog($logs_request);
                }
    
                // 設定表單狀態idty=1待領、簽核中
                $idty = '1';

            //// **** 儲存Trade表單
            $sql = "INSERT INTO _trade(form_type, item, out_user_id, out_local, in_local, idty, logs, out_date)VALUES(?,?,?,?,?,?,?,now())";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$form_type, $item_enc, $out_user_id, $out_local, $in_local, $idty, $logs_enc]);
                $swal_json["action"]   = "success";
                $swal_json["content"] .= '送出成功';
            }catch(PDOException $e){
                echo $e->getMessage();
                $swal_json["action"]   = "error";
                $swal_json["content"] .= '送出失敗';
            }

        }else{
            // echo "<script>alert('預扣功能：error')</script>";
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '預扣功能失敗';
        }
        return $swal_json;
    }
    // 顯示被選定的Trade表單
    function show_trade($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _trade.*, users_o.cname as cname_o, users_i.cname as cname_i
                        , _local_o.local_title as local_o_title, _local_o.local_remark as local_o_remark
                        , _local_i.local_title as local_i_title, _local_i.local_remark as local_i_remark
                        , _fab_o.id AS fab_o_id, _fab_o.fab_title as fab_o_title, _fab_o.fab_remark as fab_o_remark
                        , _fab_i.id AS fab_i_id, _fab_i.fab_title as fab_i_title, _fab_i.fab_remark as fab_i_remark
                        , _site_o.id as site_o_id, _site_o.site_title as site_o_title, _site_o.site_remark as site_o_remark
                        , _site_i.id as site_i_id, _site_i.site_title as site_i_title, _site_i.site_remark as site_i_remark
                FROM `_trade`
                LEFT JOIN _users users_o ON _trade.out_user_id = users_o.emp_id
                LEFT JOIN _users users_i ON _trade.in_user_id = users_i.emp_id
                LEFT JOIN _local _local_o ON _trade.out_local = _local_o.id
                LEFT JOIN _local _local_i ON _trade.in_local = _local_i.id
                LEFT JOIN _fab _fab_o ON _local_o.fab_id = _fab_o.id
                LEFT JOIN _fab _fab_i ON _local_i.fab_id = _fab_i.id
                LEFT JOIN _site _site_o ON _fab_o.site_id = _site_o.id
                LEFT JOIN _site _site_i ON _fab_i.site_id = _site_i.id 
                WHERE _trade.id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $trade = $stmt->fetch();
            return $trade;
        }catch(PDOException $e){
            echo $e->getMessage();
            return false;

        }
    }
    // 20231121退件後編輯重送的update表單
    function update_trade($request){
        $pdo = pdo();
        extract($request);

        $swal_json = array(                                 // for swal_json
            "fun"       => "update_trade",
            "content"   => "更新表單--"
        );
        $process_remark = "";

        $trade_row = show_trade($request);                  // 把trade表單叫近來處理 入賬或回補
        // 20231207 加入同時送出被覆蓋的錯誤偵測
        if(isset($old_idty) && ($old_idty != $trade_row["idty"])){
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '同意失敗'.' !! 注意 !! 當您送出表單的同時，該表單型態已被修改，送出無效，請返回確認 ~';
            return $swal_json;
        }

        if($form_type == "export"){             // export=出庫，需要執行預扣庫存
            $swal_json = array(                                 // for swal_json
                "fun"       => "update_trade",
                "content"   => "調撥出庫--"
            );

            // **** 預扣功能    
                // StdObject轉換成Array
                if(is_object($item)) { $item = (array)$item; } 
                // 逐筆呼叫處理
                foreach(array_keys($item) as $item_key){
                    $item_key_arr = explode(",", $item_key);
                        if($item_key_arr[0]){ $cata_SN = $item_key_arr[0]; } else { $cata_SN = ""; }        // 序號
                        if($item_key_arr[1]){ $stk_id  = $item_key_arr[1]; } else { $stk_id  = ""; }        // 儲存id
    
                    $item_value = $item[$item_key];
                    $item_value_arr = explode(",", $item_value);
                        if($item_value_arr[0]){ $amount  = $item_value_arr[0]; } else { $amount  = ""; }    // 數量
                        if($item_value_arr[1]){ $po_no   = $item_value_arr[1]; } else { $po_no   = ""; }    // pn號碼
                        if($item_value_arr[2]){ $lot_num = $item_value_arr[2]; } else { $lot_num = ""; }    // 批號
    
                    $process = [];  // 清空預設值
                    // 打包處理訊息process
                    $process = array('updated_user' => $updated_user,
                                     'stock_id'     => $stk_id,
                                     'lot_num'      => $lot_num,
                                     'po_no'        => $po_no,
                                     'cata_SN'      => $cata_SN,
                                     'p_amount'     => $amount,
                                     'p_local'      => $out_local,      // 預扣出庫id存量
                                     'idty'         => $idty );
                    // 呼叫預扣原本數量功能=呼叫process_trade($request)
                    $process_result = process_trade($process);  // 呼叫處理fun 處理整張需求的交易事件(一次一筆)--stock扣帳事宜
                    if(isset($process_result["result"])){                                  // True - 抵扣完成
                        if(empty($process_remark)){
                            $process_remark = "## ".$process_result["result"];
                        }else{
                            $process_remark .= "_rn_## ".$process_result["result"];
                        }
                    }else{                                                                  // False - 抵扣失敗
                        if(empty($process_remark)){
                            $process_remark = "## ".$process_result["error"];
                        }else{
                            $process_remark .= "_rn_## ".$process_result["error"];
                        }                                                          
                    }
                }
            // **** 預扣功能 end  

        }else if($form_type == "import"){       // import=入庫，不須執行預扣
            $swal_json = array(                                 // for swal_json
                "fun"       => "update_restock",
                "content"   => "其他入庫--"
            );
            $out_local = $po_no;                // 因為其他入庫沒有out_local，所以導入pm_no
            $process_result = true;             // 但要把$process_result = true;讓寫入工作可以繼續
        }

        if($process_result){
            // echo "<script>alert('預扣功能：success')</script>";
            // item資料前處理
            $item_enc = json_encode(array_filter($item));           // 去除陣列中空白元素再要編碼

            // 把_trade表單logs叫近來處理       //// 前面已用show_trade呼叫表單
                // $query = array('id' => $id );
                // $trade_logs = showLogs($query);                         // 未調閱表單，另外開表單讀logs
            // 製作log紀錄前處理：塞進去製作元素
                $logs_request["action"] = $action;
                $logs_request["step"]   = $step."-編輯";                            // 節點-簽單人角色
                $logs_request["idty"]   = $idty;                                    // 表單狀態
                $logs_request["cname"]  = $updated_user." (".$out_user_id.")";      // 開單人
                $logs_request["logs"]   = $trade_row["logs"];   
                $logs_request["remark"] = $sign_comm;  
            // 呼叫toLog製作log檔
                $logs_enc = toLog($logs_request);

            // 20231121 追加插入log：1送出=預扣、2退件=回補
                if((in_array($idty, [1 ,2])) && ($form_type == "export")){
                    $logs_request["logs"]   = $logs_enc;
                    $logs_request["remark"] = $process_remark;  
                    
                    switch($idty){
                        case "1":           // 1送出=14扣帳
                            $logs_request["idty"]   = "14";                   // 表單狀態
                            break;
                        case "2":           // 2退件=15回補
                            $logs_request["idty"]   = "15";                   // 表單狀態
                            break;
                        default:            // 預定失效 
                            return; 
                            break;
                    }
                    $logs_enc = toLog($logs_request);
                }

            // 更新trade表單
            $sql = "UPDATE _trade 
                    SET item=?, out_user_id=?, out_local=?, in_local=?, idty=?, logs=?, out_date=now()
                    WHERE id=? ";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$item_enc, $out_user_id, $out_local, $in_local, $idty, $logs_enc, $id]);
                $swal_json["action"]   = "success";
                $swal_json["content"] .= '更新成功';
            }catch(PDOException $e){
                echo $e->getMessage();
                $swal_json["action"]   = "error";
                $swal_json["content"] .= '更新失敗';
            }
        }else{
            // echo "<script>alert('預扣功能：error')</script>";
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '預扣功能失敗';
        }
        return $swal_json;
    }

    // 刪除單筆Trade紀錄 20230919 edition
    function delete_trade($request){
        $pdo = pdo();
        extract($request);

        $swal_json = array(                                 // for swal_json
            "fun"       => "delete_trade",
            "content"   => "刪除表單--"
        );

        $trade_row = show_trade($request);                  // 調閱原表單
        // 20231207 加入同時送出被覆蓋的錯誤偵測
        if(isset($old_idty) && ($old_idty != $trade_row["idty"])){
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '同意失敗'.' !! 注意 !! 當您送出表單的同時，該表單型態已被修改，送出無效，請返回確認 ~';
            return $swal_json;
        }
        $process_result = true; // 把trade表單叫近來處理 預扣回補 = 預設已完持

        if($process_result){    // 預扣回補:success
            // echo "<script>alert('預扣回補功能：success')</script>";
            // 執行刪除表單
            $sql = "DELETE FROM _trade WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$id]);
                return true;
            }catch(PDOException $e){
                echo $e->getMessage();
                return false;
            }
        }else{                  // 預扣回補:error
            // echo "<script>alert('預扣回補功能：error')</script>";
            return false;
        }
    }
    // 編輯Trade表單 20230920 edition
    function edit_trade($request){
        $pdo = pdo();
        extract($request);

        $swal_json = array(                                 // for swal_json
            "fun"       => "edit_trade",
            "content"   => "編輯表單--"
        );
        $process_remark = "";
        
        $trade_row = show_trade($request);                  // 把trade表單叫近來處理 預扣回補
        // 20231207 加入同時送出被覆蓋的錯誤偵測
        if(isset($old_idty) && ($old_idty != $trade_row["idty"])){
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '同意失敗'.' !! 注意 !! 當您送出表單的同時，該表單型態已被修改，送出無效，請返回確認 ~';
            return $swal_json;
        }

        $item = json_decode($trade_row["item"]);  
        // StdObject轉換成Array
        if(is_object($item)) { $item = (array)$item; } 
        // 指向入賬、回補local_id
        $t_local  = $trade_row["out_local"];       // 退回2(回補)出庫廠區

        // **** 預扣回補功能    
            // 逐筆呼叫處理
            foreach(array_keys($item) as $item_key){
                $item_key_arr = explode(",", $item_key);
                    if($item_key_arr[0]){ $cata_SN = $item_key_arr[0]; } else { $cata_SN = ""; }
                    if($item_key_arr[1]){ $stk_id  = $item_key_arr[1]; } else { $stk_id  = ""; }

                $item_value = $item[$item_key];
                $item_value_arr = explode(",", $item_value);
                    if($item_value_arr[0]){ $amount  = $item_value_arr[0]; } else { $amount  = ""; }
                    if($item_value_arr[1]){ $po_no   = $item_value_arr[1]; } else { $po_no   = ""; }
                    if($item_value_arr[2]){ $lot_num = $item_value_arr[2]; } else { $lot_num = ""; }

                $process = [];              // 清空預設值
                $process = array('stock_id' => $stk_id,
                                 'lot_num'  => $lot_num,
                                 'po_no'    => $po_no,
                                 'cata_SN'  => $cata_SN,
                                 'p_amount' => $amount,
                                 'p_local'  => $t_local,
                                 'idty'     => '3' );   // 必須使用idty:3 = 取消上一張狀態，先回補到原位
                // 後續要加入預扣原本數量功能=呼叫process_trade($request)
                $process_result = process_trade($process);
                if(isset($process_result["result"])){                                  // True - 抵扣完成
                    if(empty($process_remark)){
                        $process_remark = "## ".$process_result["result"];
                    }else{
                        $process_remark .= "_rn_## ".$process_result["result"];
                    }
                }else{                                                                  // False - 抵扣失敗
                    if(empty($process_remark)){
                        $process_remark = "## ".$process_result["error"];
                    }else{
                        $process_remark .= "_rn_## ".$process_result["error"];
                    }                                                          
                }
            }
        // **** 預扣回補功能 end   

        if($process_result){    // 預扣回補:success
            // echo "<script>alert('預扣回補功能：success')</script>";
            // 梳理表單資料製作logs前處理
                // $item_enc = json_encode(array_filter($item));          // 去除陣列中空白元素再要編碼

                // // 表單簽單人角色
                // if($trade_row["out_user_id"] == $_SESSION["AUTH"]["emp_id"]){ $step_role = "填單人";}
                // if($trade_row["in_local"] == $_SESSION[$sys_id]["fab_id"] || (in_array($trade_row["in_local"], $_SESSION[$sys_id]["sfab_id"])) ){ $step_role = "收貨人";}
                // if(!isset($step_role) && ($_SESSION[$sys_id]["role"] <= 1)){ $step_role = "管你圓";}
            // 製作log紀錄前處理：塞進去製作元素
                $logs_request["idty"]   = $idty;                    // 表單狀態 4編輯
                $logs_request["cname"]  = $updated_user." (".$updated_emp_id.")";            // 簽單人
                $logs_request["step"]   = $step;                    // 節點-簽單人角色
                $logs_request["logs"]   = $trade_row["logs"];           // 帶入舊logs
                // $logs_request["remark"] = $sign_comm.$process_remark;               // 簽核command
                $logs_request["remark"] = $process_remark;               // 簽核command
            // 呼叫toLog製作log檔
                $logs_enc = toLog($logs_request);

            // 執行更新表單
            // $p_idty = '4';    // 返回退回-編輯狀態
            $p_idty = $idty;    // 返回退回-編輯狀態
            $sql = "UPDATE _trade SET idty=?, logs=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$p_idty, $logs_enc, $id]);
                $swal_json["action"]   = "success";
                $swal_json["content"] .= '更新成功';
            }catch(PDOException $e){
                echo $e->getMessage();
                $swal_json["action"]   = "error";
                $swal_json["content"] .= '更新失敗';
            }
        }else{                  // 預扣回補:error
            // echo "<script>alert('預扣回補功能：error')</script>";
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '預扣功能失敗';
        }
        return $swal_json;
    }
    // 簽核動作處理：
    function sign_trade($request){
        $pdo = pdo();
        extract($request);
        
        $process_remark = "";                       // log交易訊息中加減數

        if($form_type == "export"){                 // export=出庫，需要執行預扣庫存
            $swal_json = array(                                 // for swal_json
                "fun"       => "sign_trade",
                "content"   => "調撥出庫--"
            );
        }else if($form_type == "import"){           // import=入庫，不須執行預扣
            $swal_json = array(                                 // for swal_json
                "fun"       => "sign_restock",
                "content"   => "其他入庫--"
            );
        }
        
        $trade_row = show_trade($request);               // 把trade表單叫近來處理 入賬或回補
        // 20231207 加入同時送出被覆蓋的錯誤偵測
        if(isset($old_idty) && ($old_idty != $trade_row["idty"])){
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '同意失敗'.' !! 注意 !! 當您送出表單的同時，該表單型態已被修改，送出無效，請返回確認 ~';
            return $swal_json;
        }

        // **** 入賬、回補功能
            $item = json_decode($trade_row["item"]);  
            // StdObject轉換成Array
            if(is_object($item)) { $item = (array)$item; } 

            // 指向入賬、回補local_id
            switch($idty){      // idty 同意0(入賬)、退回2(回補)、作廢3(回補) 進行選擇性套用!!!!
                case "0":       // 0同意(入賬)
                    $t_local  = $trade_row["in_local"];         // 入庫廠區
                    break;

                case "2":       // 2退回(回補)
                    if($form_type == "export"){                 // export=出庫，需要執行預扣庫存
                        $t_local  = $trade_row["out_local"];    // 出庫廠區
                    }else if($form_type == "import"){           // import=入庫，不須執行預扣
                        $t_local  = "";                         // 不處理
                    }
                    break;

                case "3":       // 3作廢(不處理)
                default:        // 預定失效 
                    $t_local  = "";                             // 不處理
                    break;
            }

            // export/調撥：0同意(入賬)、2退件(回補)；import/其他：0同意(入賬)；ALL排除簽核3作廢，暫時無須處理!
            if( (($form_type == "export") && in_array($idty, [0, 2])) || (($form_type == "import") && ($idty == 0)) ){    // 0同意(入賬)、2退件(回補)；排除簽核3作廢，暫時無須處理!
                // 逐筆呼叫處理
                foreach(array_keys($item) as $item_key){
                    $item_key_arr = explode(",", $item_key);
                        if($item_key_arr[0]){ $t_cata_SN = $item_key_arr[0]; } else { $t_cata_SN = ""; }            // cata_SN 序號
                        if($item_key_arr[1]){ $t_stk_id  = $item_key_arr[1]; } else { $t_stk_id  = ""; }            // stock_id 儲存id
    
                    $item_value = $item[$item_key];
                    $item_value_arr = explode(",", $item_value);
                        if($item_value_arr[0]){ $t_amount  = $item_value_arr[0]; } else { $t_amount  = ""; }        // amount 數量       
                        if($item_value_arr[1]){ $t_po_no   = $item_value_arr[1]; } else { $t_po_no   = ""; }        // po_no po號碼
                        if($item_value_arr[2]){ $t_lot_num = $item_value_arr[2]; } else { $t_lot_num = ""; }        // lot_num 批號
    
                    $process = [];              // 清空預設值
                    // 打包處理訊息process
                    $process = array('updated_user' => $updated_user,
                                     'stock_id'     => $t_stk_id,
                                     'lot_num'      => $t_lot_num,
                                     'po_no'        => $t_po_no,
                                     'cata_SN'      => $t_cata_SN,
                                     'p_amount'     => $t_amount,
                                     'p_local'      => $t_local,        // 處理庫存id存量
                                     'idty'         => $idty );         // idty:3 = 取消
                    // 後續要加入預扣原本數量功能=呼叫process_trade($request)
                    $process_result = process_trade($process);
                    if(isset($process_result["result"])){                                  // True - 抵扣完成
                        if(empty($process_remark)){
                            $process_remark = "## ".$process_result["result"];
                        }else{
                            $process_remark .= "_rn_## ".$process_result["result"];
                        }
                    }else{                                                                  // False - 抵扣失敗
                        if(empty($process_remark)){
                            $process_remark = "## ".$process_result["error"];
                        }else{
                            $process_remark .= "_rn_## ".$process_result["error"];
                        }                                                          
                    }
                }
            } else {
                $process_result = true; // 因為排除簽核3取消，暫時無須處理! 但需標示處理已完成

            }
        // **** 入賬、回補功能 end  

        if($process_result){
            // echo "<script>alert('入賬、回補功能：success')</script>";

            // 梳理表單資料製作logs前處理
            // $item_enc = json_encode(array_filter($item));          // 去除陣列中空白元素再要編碼
            // 製作log紀錄前處理：塞進去製作元素
                $logs_request["action"] = $action;
                $logs_request["step"]   = $step;                    // 節點-簽單人角色
                $logs_request["idty"]   = $idty;                    // 表單狀態
                $logs_request["cname"]  = $updated_user." (".$updated_emp_id.")";            // 簽單人
                $logs_request["logs"]   = $trade_row["logs"];           // 帶入舊logs
                $logs_request["remark"] = $sign_comm;               // 簽核command
            // 呼叫toLog製作log檔
                $logs_enc = toLog($logs_request);

            // 20231121 追加插入log：1送出=預扣、2退件=回補
            if((in_array($idty, [0, 1 ,2]) && ($form_type == "export")) || (in_array($idty, [0]) && ($form_type == "import"))){     // import=入庫，不須執行預扣
                $logs_request["logs"]   = $logs_enc;
                $logs_request["remark"] = $process_remark;               // 簽核command
                
                switch($idty){
                    case "0":           // 0同意=16入帳
                        $logs_request["idty"] = "16";
                        break;
                    case "1":           // 1送出=14扣帳
                        $logs_request["idty"] = "14";
                        break;
                    case "2":           // 2退件=15回補
                        $logs_request["idty"] = "15";
                        break;
                    default:            // 預定失效 
                        break;
                }
                $logs_enc = toLog($logs_request);
            }
    
            // 儲存更新trade表單
            // 決定表單儲存時的狀態
            switch($idty){      // idty 同意0(入賬)、退回2(不處理)、作廢3(回補) 進行選擇性套用!!!!
                case "0":                                               // 0同意
                    $p_idty = '10';                                     // 指定10結案狀態
                    $p_in_user_id = $updated_emp_id;
                    $sql = "UPDATE _trade SET in_user_id=?, idty=?, logs=?, in_date=now() WHERE id=?";
                    $stmt = $pdo->prepare($sql);
                    try {
                        $stmt->execute([$p_in_user_id, $p_idty, $logs_enc, $id]);
                        $swal_json["action"]   = "success";
                        $swal_json["content"] .= '同意成功';
                    }catch(PDOException $e){
                        echo $e->getMessage();
                        $swal_json["action"]   = "error";
                        $swal_json["content"] .= '同意失敗';
                    }
                    break;

                case "2":                                               // 2退回  => 2與3共用
                case "3":                                               // 3作廢  => 2與3共用
                    if($idty == 2){
                        $p_idty = '2';                                      // 指定2退回狀態
                        $content_text_ext = "退回";                         // for swal_json
                    }else if($idty == 3){
                        $p_idty = '3';                                      // 指定3作廢狀態
                        $content_text_ext = "作廢";                         // for swal_json
                    }else{                                              // out of spec
                        $p_idty = '6';                                      // 指定6暫存狀態
                        $content_text_ext = "暫存";                         // for swal_json
                    }

                    $sql = "UPDATE _trade SET idty=?, logs=? WHERE id=?";
                    $stmt = $pdo->prepare($sql);
                    try {
                        $stmt->execute([$p_idty, $logs_enc, $id]);
                        $swal_json["action"]   = "success";
                        $swal_json["content"] .= $content_text_ext.'成功';
                    }catch(PDOException $e){
                        echo $e->getMessage();
                        $swal_json["action"]   = "error";
                        $swal_json["content"] .= $content_text_ext.'失敗';
                    }
                    break;

                default:        // 預定失效 -- 不處理
                    break;
            }

        }else{
            // echo "<script>alert('入賬、回補功能：error')</script>";
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '入賬、回補功能失敗...請洽管理員';
        }
        return $swal_json;
    }
// // // Trade CRUD -- end

// // // Create表單會用到
    // 20230825 單選出貨區域
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

    // 20230724 在Create時秀出所有的衛材清單
    function show_local_stock($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _stk.*, _stk.id AS stk_id
                        , _l.local_title, _l.local_remark
                        , _f.id AS fab_id, _f.fab_title, _f.fab_remark
                        , _s.id AS site_id, _s.site_title, _s.site_remark
                        , _cata.*
                        , _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no 
                FROM `_stock` _stk 
                LEFT JOIN _local _l ON _stk.local_id = _l.id 
                LEFT JOIN _fab _f   ON _l.fab_id = _f.id 
                LEFT JOIN _site _s  ON _f.site_id = _s.id 
                LEFT JOIN _cata     ON _stk.cata_SN = _cata.SN 
                LEFT JOIN _cate     ON _cata.cate_no = _cate.cate_no 
                WHERE _l.id=? AND cata_SN IS NOT null 
                -- ORDER BY catalog_id, lot_num ASC
                ORDER BY _stk.cata_SN ASC ";
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
    // 秀出catalog全部
    // 20230721 開啟show需求單時，先讀取local衛材存量，供填表單時參考
    function show_catalogs(){
        $pdo = pdo();
        $sql = "SELECT _cata.*, _cate.cate_title, _cate.cate_no , _cate.id AS cate_id
                FROM _cata
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no
                WHERE _cata.flag = 'On'
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
// // // Create表單會用到 -- end

// // // process fun
    // 20230724 處理交易事件--所屬器材數量之加減
    // ** 20230725 處理貨單時 已在stock內的只能用ID，不要用SN...因為可能有多筆同SN，而導致錯亂 ??!!
    // $idty 2=退貨、3=取消/入帳、1送出/待收
    
    function process_trade($process){
        $pdo = pdo();
        extract($process);
        // 先把舊資料叫出來，進行加扣數量參考基準
        $sql_stk_check = "SELECT _stk.* , _l.low_level , _l.id AS local_id , _l.local_title , _f.id AS fab_id , _f.fab_title , _s.id AS site_id
                      FROM `_stock` _stk
                      LEFT JOIN _local _l ON _stk.local_id = _l.id 
                      LEFT JOIN _fab _f ON _l.fab_id = _f.id 
                      LEFT JOIN _site _s ON _f.site_id = _s.id 
                      WHERE _stk.local_id = ? AND cata_SN = ? ";          
                    //   WHERE _stk.local_id = ? AND cata_SN = ? AND lot_num = ? AND po_no=? ";          
        $stmt_stk_check = $pdo -> prepare($sql_stk_check);
        // $stmt_stk_check -> execute([$p_local, $cata_SN, $lot_num, $po_no]);
        $stmt_stk_check -> execute([$p_local, $cata_SN]);

        if($stmt_stk_check -> rowCount() >0){       // 已有紀錄
            // echo "<script>alert('process_trade:已有紀錄~')</script>";            // deBug
            $row_stk = $stmt_stk_check -> fetch();
            // 交易狀態：0完成/1待收/2退貨/3取消
            switch($idty){
                case "0":       // 0完成
                case "2":       // 2退貨/待收
                case "3":       // 3取消/入帳
                    // echo "<script>alert('0完成2退貨3取消:$p_amount')</script>";   // deBug
                    $row_stk['amount'] += $p_amount; 
                    $cama = array(
                        'icon'  => ' + ',     // log交易訊息中加減號
                        'title' => ' 入帳 '   // log交易訊息 動作
                    );
                    break;
                case "1":       // 1送出/待收
                    // echo "<script>alert('1待收:$p_amount')</script>";            // deBug
                    $row_stk['amount'] -= $p_amount;
                    $cama = array(
                        'icon'  => ' - ',     // log交易訊息中加減號
                        'title' => ' 扣帳 '   // log交易訊息 動作
                    );
                    break;
                default:
                    $cama = array(
                        'icon'  => ' ? ',     // log交易訊息中加減號
                        'title' => ' 錯誤 '   // log交易訊息 動作
                    );
                    return;
            }

            $sql = "UPDATE _stock SET amount=?, updated_at=now() WHERE id=? ";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$row_stk['amount'], $row_stk['id']]);
                $process_result['result'] = $row_stk['fab_title'] . "_" . $row_stk['local_title'] . " " . $row_stk['cata_SN'] . $cama['icon'] . $p_amount . " = " . $row_stk['amount'];      // 回傳 True: id + amount

            }catch(PDOException $e){
                echo $e->getMessage();
                $process_result['error'] = $row_stk['fab_title'] . "_" . $row_stk['local_title'] . " " . $cama['title'] . "id:".($row_stk['id'] * -1);               // 回傳 False: - id

            }
            return $process_result;
        
        }else{      // 開新紀錄
            // echo "<script>alert('process_trade:開新紀錄~')</script>";             // deBug
            // step-1 先把local資料叫出來，抓取low_level數量
                $row_local="SELECT _l.* , _l.local_title , _f.fab_title
                            FROM `_local` _l
                            LEFT JOIN _fab _f ON _l.fab_id = _f.id  
                            WHERE _l.id=? ";          
                $row_local_stmt = $pdo -> prepare($row_local);
                $row_local_stmt -> execute([$p_local]);

            if( $row_local_stmt -> rowCount() >0){                                  // 有取得local資料
                
                $row_local = $row_local_stmt->fetch();
                $row_lowLevel = json_decode($row_local["low_level"]);                   // 將local.low_level解碼
                if(is_object($row_lowLevel)) { $row_lowLevel = (array)$row_lowLevel; }  // 將物件轉成陣列
                if(isset($row_lowLevel[$cata_SN])){
                    $low_level = $row_lowLevel[$cata_SN];                           // 取得該目錄品項的安全存量值
                }else{
                    $low_level = 0;                                                 // 未取得local資料時，給他一個0
                }
            }else{
                $low_level = 0;                                                     // 未取得local資料時，給他一個0
            }

            switch($idty){
                case "0":       // 0完成
                case "2":       // 2退貨/待收
                case "3":       // 3取消/入帳
                    // echo "<script>alert('0完成2退貨3取消:$p_amount')</script>";   // deBug
                    $cama = array(
                        'icon'  => ' + ',     // log交易訊息中加減號
                        'title' => ' 入帳 '   // log交易訊息 動作
                    );
                    break;
                case "1":       // 1送出/待收
                    // echo "<script>alert('1待收:$p_amount')</script>";            // deBug
                    $cama = array(
                        'icon'  => ' - ',     // log交易訊息中加減號
                        'title' => ' 扣帳 '   // log交易訊息 動作
                    );
                    break;
                default:
                    $cama = array(
                        'icon'  => ' ? ',     // log交易訊息中加減號
                        'title' => ' 錯誤 '   // log交易訊息 動作
                    );
                    return;
            }

            $lot_num        = "9999-12-31";                                             // 0.批號/效期
            $stock_remark   = " *".$cama["title"]."：".$cama["icon"].$p_amount;  // 0.備註
                // // $stock_remark .= $stk_row_list[$i]['stock_remark'];
            
            // step-2 建立新紀錄到資料庫
            $sql = "INSERT INTO _stock(local_id, cata_SN, standard_lv, amount, stock_remark, pno, po_no, lot_num, updated_user, created_at, updated_at)
                    VALUES(?,?,?,?,?,?,?,?,?,now(),now())";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$p_local, $cata_SN, $low_level, $p_amount, $stock_remark, '', $po_no, $lot_num, $updated_user]);
                $process_result['result'] = $row_local['fab_title'] . "_" . $row_local['local_title'] . " +新 ". $cata_SN . $cama['icon'] . $p_amount . " = " . $p_amount;                   // 回傳 True: id - amount
            }catch(PDOException $e){
                echo $e->getMessage();
                $process_result['error'] = $row_local['fab_title'] . "_" . $row_local['local_title'] . " -新 ". $cata_SN . $cama['icon'] . $p_amount . " = " . $p_amount;                   // 回傳 False: - id
            }
        }
        return $process_result;
    }


// // // process fun -- end

// // // Log tools
    // 製作記錄JSON_Log檔
    function toLog($request){
        extract($request);
        // log資料前處理
        // 交易狀態：0完成/1待收/2退貨/3取消
        switch($idty){
            case "0":   $action = '同意 (Approve)';       break;
            case "1":   $action = '送出 (Submit)';        break;
            case "2":   $action = '退回 (Reject)';        break;
            case "3":   $action = '作廢 (Abort)';         break;
            case "4":   $action = '編輯 (Edit)';          break;
            case "5":   $action = '轉呈 (Transmit)';      break;
            case "6":   $action = '暫存 (Save)';          break;
            case "10":  $action = '同意 (Approve)';       break;    // 結案 (Close)
            case "11":  $action = '轉PR';                 break;
            case "12":  $action = '待收發貨 (Awaiting collection)';   break;
            case "13":  $action = '交貨 (Delivery)';      break;
            case "14":  $action = '庫存-扣帳 (Debit)';     break;
            case "15":  $action = '庫存-回補 (Replenish)'; break;
            case "16":  $action = '庫存-入賬 (Account)';   break;
            default  :  $action = '錯誤 (Error)';         return;
        }

        if(!isset($logs)){
            $logs = [];
            $logs_arr =[];
        }else{
            $logs_dec = json_decode($logs);
            $logs_arr = (array) $logs_dec;
        }

        $app = [];                                  // 定義app陣列
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
        $sql = "SELECT _trade.*
                FROM `_trade`
                WHERE _trade.id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $trade = $stmt->fetch();
            return $trade;
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
        $trade_row = showLogs($query);
        //這個就是JSON格式轉Array新增字串==搞死我
        $logs_dec = json_decode($trade_row['logs']);
        $logs_arr = (array) $logs_dec;
        // unset($logs_arr[$log_id]);  // 他會產生index導致原本的表亂掉
        array_splice($logs_arr, $log_id, 1);  // 用這個不會產生index

        $logs = json_encode($logs_arr);
        $sql = "UPDATE _trade 
                SET logs=? 
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$logs, $id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
// // // Log tools -- end

    // 20230724 找出自己的資料 
    function showMe($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT * FROM _users WHERE emp_id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$emp_id]);
            $user = $stmt->fetch();
            return $user;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // deBug專用 20230919 edition
    function deBug($request){
        // extract($request);
        echo "<pre>";
        print_r($request);
        echo '</pre><hr>';
    }

