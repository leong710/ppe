<?php
// // // Trade index
    // 20230724 在trad_list秀出所有Trad清單 // 20230818 嵌入分頁工具
    function show_trade_list($request){
        $pdo = pdo();
        extract($request);
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
                LEFT JOIN _site _site_i ON _fab_i.site_id = _site_i.id
                ";
        if($emp_id != 'All'){
            $sql .= " WHERE _trade.out_user_id=? OR _trade.in_user_id=? OR _fab_o.id=? OR _fab_i.id=? ";      //處理 byUser
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
                $stmt->execute([$emp_id, $emp_id, $fab_id, $fab_id]);       //處理 byUser
            }else{
                $stmt->execute();               //處理 byAll
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
// // // Trade index -- end

// // // Trade CRUD
    // 儲存交易表單 20230919 edition
    function store_trade($request){
        $pdo = pdo();
        extract($request);

        // **** 預扣功能    
            // StdObject轉換成Array
            if(is_object($item)) { $item = (array)$item; } 
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

                $process = [];  // 清空預設值
                $process = array('stock_id' => $stk_id,
                                 'lot_num'  => $lot_num,
                                 'po_no'    => $po_no,
                                 'cata_SN'  => $cata_SN,
                                 'p_amount' => $amount,
                                 'p_local'  => $out_local,
                                 'idty'     => $idty );
                // 後續要加入預扣原本數量功能=呼叫process_trade($request)
                $process_result = process_trade($process);
            }
        // **** 預扣功能 end   

        if($process_result){
            // echo "<script>alert('預扣功能：success')</script>";

            // item資料前處理
                $item_enc = json_encode(array_filter($item));          // 去除陣列中空白元素再要編碼
    
                // 製作log紀錄前處理：塞進去製作元素
                    $logs_request["idty"]   = $idty;                   // 表單狀態
                    $logs_request["cname"]  = $cname;                  // 開單人
                    $logs_request["step"]   = "填單人";                 // 節點-簽單人角色
                    $logs_request["logs"]   = "";   
                    $logs_request["remark"] = $sign_comm;   
                // 呼叫toLog製作log檔
                    $logs_enc = toLog($logs_request);
    
                // 設定表單狀態idty=1待領、簽核中
                    $idty = '1';
    
            //// **** 儲存Trade表單
                $sql = "INSERT INTO _trade(out_date, item, out_user_id, out_local, in_local, idty, logs)VALUES(now(),?,?,?,?,?,?)";
                $stmt = $pdo->prepare($sql);
                try {
                    $stmt->execute([$item_enc, $out_user_id, $out_local, $in_local, $idty, $logs_enc]);
                    $swal_json = array(
                        "fun" => "store_trade",
                        "action" => "success",
                        "content" => '批量調撥申請--送出成功'
                    );
                }catch(PDOException $e){
                    echo $e->getMessage();
                    $swal_json = array(
                        "fun" => "store_trade",
                        "action" => "error",
                        "content" => '批量調撥申請--送出失敗'
                    );
                }

        }else{
            // echo "<script>alert('預扣功能：error')</script>";
            $swal_json = array(
                "fun" => "store_trade",
                "action" => "error",
                "content" => '批量調撥申請--預扣功能失敗'
            );
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
                LEFT JOIN _fab _fab_o ON _local_o.fab_id = _fab_o.id
                LEFT JOIN _site _site_o ON _fab_o.site_id = _site_o.id
                LEFT JOIN _local _local_i ON _trade.in_local = _local_i.id
                LEFT JOIN _fab _fab_i ON _local_i.fab_id = _fab_i.id
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
    // 驗收動作的update表單
    function update_trade($request){
        $pdo = pdo();
        extract($request);
        $trade_id = array('id' => $p_id);               // 指定trade_id
        $trade = show_trade($trade_id);                  // 把trade表單叫近來處理
            $in_local = $trade['in_local'];                 // 指定收件區in_local
            $out_local = $trade['out_local'];               // 指定發貨區out_local
            $logs = $trade['logs'];                         // 指定表單記錄檔logs
            $b_idty = $trade['idty'];                       // 指定前一版表單狀態b_idty
            $item_str = $trade["item"];                     // 把item整串(未解碼)存到$item_str
            $item_arr = explode("_," ,$item_str);           // 把字串轉成陣列進行後面的應用
            $stock_id_dec = json_decode($item_arr[0]);      // 解碼後存到$stock_id_dec = 儲存單編號
            $po_no_dec = json_decode($item_arr[1]);         // 解碼後存到$po_no_dec = 儲存單編號中的po_no
            $item_dec = json_decode($item_arr[2]);          // 解碼後存到$item_dec = 儲存單編號中的衛材編號
            $amount_dec = json_decode($item_arr[3]);        // 解碼後存到$amount_dec = 儲存單編號中衛材的調撥數量
            $lot_num_dec = json_decode($item_arr[4]);       // 解碼後存到$lot_num_dec = 儲存單編號中的批號/效期
        //PHP stdClass Object轉array 
        if(is_object($stock_id_dec)) { $stock_id_dec = (array)$stock_id_dec; } 
        if(is_object($po_no_dec)) { $po_no_dec = (array)$po_no_dec; } 
        if(is_object($item_dec)) { $item_dec = (array)$item_dec; } 
        if(is_object($amount_dec)) { $amount_dec = (array)$amount_dec; } 
        if(is_object($lot_num_dec)) { $lot_num_dec = (array)$lot_num_dec; } 
        // V2 判斷前單$b_idty不是1待簽、12待領，就返回        
        if($b_idty != 1 || $b_idty == 12){
            echo "<script>alert('$b_idty.此表單在您簽核前已被改變成[非待簽核]狀態，請再確認，謝謝!');</script>";
            return;
        }
        // 0完成/1待簽/2退件/3取消
        // 判斷前單$b_idty不是1代收 和 $p_idty更新成不是2退貨或3取消
        // 預設：收件 1 => 0	撥入in
        $p_local = $in_local;
        $idty = 0;

        if(($b_idty == 2 && $p_idty == 0) || ($b_idty == 1 && $p_idty == 3) || ($b_idty == 1 && $p_idty == 2)){ 
            // b2 退件 => 0 核准 = 撥入out  // b1 待收 => 3 取消 = 撥入out
        // if($b_idty == 1 && ($p_idty == 2 || $p_idty == 3)){ 
            // b1待收 => p2退件 或 p3取消 = 撥入out
            $p_local = $out_local; 
            // echo "<script>alert('正在走 退收/取消程序 返回out_local：$p_local~')</script>";
            switch($p_idty){
                case "0":       // 0 核准
                    $b_idty = 2;
                    $idty = 0;
                    break;
                case "2":       // 2 退件
                    $b_idty = 2;
                    $idty = 2;
                    break;
                case "3":       // 3 取消
                    $b_idty = 3;
                    $idty = 3;
                    break;
                default:
                    return;
            }
        }else{
            // 這個是 1 => 0 的驗收
            $b_idty = 0;
        }
            // 逐筆呼叫處理
            foreach(array_keys($stock_id_dec) as $st){
                // 假如po_no是空的，給他NA...20230725=空
                if(empty($po_no_dec[$st])){
                    $po_no_dec[$st] = '';
                }
                $process = [];  // 清空預設值
                $process = array('stock_id' => $st,
                                 'lot_num' => $lot_num_dec[$st],
                                 'po_no' => $po_no_dec[$st],
                                 'cata_SN' => $item_dec[$st],
                                 'p_amount' => $amount_dec[$st],
                                 'p_local' => $p_local,
                                 'user_id' => $p_in_user_id,
                                 'cname' => $_SESSION["AUTH"]["cname"],
                                 'idty' => $b_idty);
                process_trade($process);
            }
        // 把原本沒有的塞進去
        $request['idty'] = $idty;   
        $request['cname'] = $_SESSION["AUTH"]["cname"];
        $request['logs'] = $logs;   
        
        // 呼叫toLog製作log檔
        $logs_enc = toLog($request);

        // 更新trade表單
        $sql = "UPDATE _trade 
                SET in_user_id=?, idty=?, logs=?, in_date=now() 
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$p_in_user_id, $p_idty, $logs_enc, $p_id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 刪除單筆Trade紀錄 20230919 edition
    function delete_trade($request){
        $pdo = pdo();
        extract($request);

        // $trade = show_trade($request);                  // 把trade表單叫近來處理 預扣回補

        // // **** 預扣回補功能    
        //     // StdObject轉換成Array
        //     if(is_object($item)) { $item = (array)$trade["item"]; } 
        //     // 逐筆呼叫處理
        //     foreach(array_keys($item) as $item_key){
        //         $item_key_arr = explode(",", $item_key);
        //             if($item_key_arr[0]){ $cata_SN = $item_key_arr[0]; } else { $cata_SN = ""; }
        //             if($item_key_arr[1]){ $stk_id  = $item_key_arr[1]; } else { $stk_id  = ""; }

        //         $item_value = $item[$item_key];
        //         $item_value_arr = explode(",", $item_value);
        //             if($item_value_arr[0]){ $amount  = $item_value_arr[0]; } else { $amount  = ""; }
        //             if($item_value_arr[1]){ $po_no   = $item_value_arr[1]; } else { $po_no   = ""; }
        //             if($item_value_arr[2]){ $lot_num = $item_value_arr[2]; } else { $lot_num = ""; }

        //         $process = [];              // 清空預設值
        //         $process = array('stock_id' => $stk_id,
        //                          'lot_num'  => $lot_num,
        //                          'po_no'    => $po_no,
        //                          'cata_SN'  => $cata_SN,
        //                          'p_amount' => $amount,
        //                          'p_local'  => $trade["out_local"],
        //                          'idty'     => '3' );   // idty:3 = 取消
        //         // 後續要加入預扣原本數量功能=呼叫process_trade($request)
        //         $process_result = process_trade($process);
        //     }
        // // **** 預扣回補功能 end   
            $process_result = true; // 預設已完持

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
        $sys_id = "ppe";
        
        $trade = show_trade($request);                  // 把trade表單叫近來處理 預扣回補
        $item = json_decode($trade["item"]);  
            // StdObject轉換成Array
            if(is_object($item)) { $item = (array)$item; } 
        // 指向入賬、回補local_id
        $t_local  = $trade["out_local"];       // 退回2(回補)出庫廠區

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
            }
        // **** 預扣回補功能 end   

        if($process_result){    // 預扣回補:success
            // echo "<script>alert('預扣回補功能：success')</script>";
            // 梳理表單資料製作logs前處理
                // $item_enc = json_encode(array_filter($item));          // 去除陣列中空白元素再要編碼

                // 表單簽單人角色
                if($trade["out_user_id"] == $_SESSION["AUTH"]["emp_id"]){ $step_role = "填單人";}
                if($trade["in_local"] == $_SESSION[$sys_id]["fab_id"] || (in_array($trade["in_local"], $_SESSION[$sys_id]["sfab_id"])) ){ $step_role = "收貨人";}
                if(!isset($step_role) && ($_SESSION[$sys_id]["role"] <= 1)){ $step_role = "管你圓";}
            // 製作log紀錄前處理：塞進去製作元素
                $logs_request["idty"]   = $idty;                    // 表單狀態 4編輯
                $logs_request["cname"]  = $updated_user;            // 簽單人
                $logs_request["step"]   = $step_role;               // 節點-簽單人角色
                $logs_request["logs"]   = $trade["logs"];           // 帶入舊logs
                $logs_request["remark"] = $sign_comm;               // 簽核command
            // 呼叫toLog製作log檔
                $logs_enc = toLog($logs_request);

            // 執行更新表單
            // $p_idty = '4';    // 返回退回-編輯狀態
            $p_idty = $idty;    // 返回退回-編輯狀態
            $sql = "UPDATE _trade SET idty=?, logs=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$p_idty, $logs_enc, $id]);
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
    // 儲存交易表單-PR請購進貨
    function restock_store($request){
        $pdo = pdo();
        extract($request);
        // item資料前處理
            $item = array_filter($item);          // 去除陣列中空白元素
            $amount = array_filter($amount);        // 去除陣列中空白元素
            // 小陣列要先編碼才能塞進去大陣列
                $item_enc = json_encode($item);
                $po_no_enc = json_encode($po_no);
                $item_enc = json_encode($item);
                $amount_enc = json_encode($amount);
                $lot_num_enc = json_encode($lot_num);
            //陣列合併
                $item_arr = [];
                $item_arr['stock'] = $item_enc;
                $item_arr['po_no'] = $po_no_enc;
                $item_arr['item'] = $item_enc;
                $item_arr['amount'] = $amount_enc;
                $item_arr['lot_num'] = $lot_num_enc;
            // implode()把陣列元素組合為字串：
                $item_str = implode("_," , $item_arr);               // 陣列轉成字串進行儲存到mySQL

            // 設定表單狀態idty=1待領
            if(!isset($idty)){
                $idty = '1';            // 1 代收
            }

        // 判斷是否是PR請購進貨
        $check_tradr_local = strtolower(substr($out_local,0,2));       // strtolower() = 英文字串轉小寫；substr($out_local,0,2) = 取自串段落

        if($check_tradr_local == "po"){        // 是po才做進貨處理
        //// 收貨+結案log更新
            $idty = '10';               // 10 結案
            $add_log = array(
                "cname" => $cname,
                "idty" => $idty,
                "remark" => $remark,
                "logs" => $logs
            ); 
            $logs = toLog($add_log);

        //// **** 儲存Trade表單
            $sql = "INSERT INTO _trade(item, out_user_id, out_local, in_local, in_user_id, idty, logs, in_date, out_date)VALUES(?,?,?,?,?,?,?,now(),now())";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$item_str, $out_user_id, $out_local, $in_local, $out_user_id, $idty, $logs]);
            }catch(PDOException $e){
                echo $e->getMessage();
            }

        //// **** 預扣功能    
            // StdObject轉換成Array
            if(is_object($item)) { $item = (array)$item; } 
            if(is_object($po_no)) { $po_no = (array)$po_no; } 
            if(is_object($amount)) { $amount = (array)$amount; } 
            if(is_object($lot_num)) { $lot_num = (array)$lot_num; } 
            $idty = '0';        // 0 完成 = 必須切回狀態0，讓process_trade才可以收入

            // 逐筆呼叫處理
            foreach(array_keys($item) as $it){
                $process = [];  // 清空預設值
                $process = array('stock_id' => $it,
                                'lot_num' => $lot_num[$it],
                                'po_no' => $po_no[$it],
                                'cata_SN' => $item[$it],
                                'p_amount' => $amount[$it],
                                'p_local' => $in_local,
                                'cname' => $cname,
                                'idty' => $idty);
                // 後續要加入預扣原本數量功能=呼叫process_trade($request)
                process_trade($process);
            }
        }else{
        //// **** 一般：儲存Trade表單 & 不處理數量
            $sql = "INSERT INTO _trade(out_date, item, out_user_id, out_local, in_local, idty, logs)VALUES(now(),?,?,?,?,?,?)";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$item_str, $out_user_id, $out_local, $in_local, $idty, $logs]);
            }catch(PDOException $e){
                echo $e->getMessage();
            }
        }
    }
    // 20230724 處理交易事件--所屬器材數量之加減
    // ** 20230725 處理貨單時 已在stock內的只能用ID，不要用SN...因為可能有多筆同SN，而導致錯亂 ??!!
    function process_trade($process){
        $pdo = pdo();
        extract($process);
        // 先把舊資料叫出來，進行加扣數量參考基準
        $sql_check = "SELECT _stk.* , _l.low_level , _l.id AS local_id , _f.id AS fab_id , _s.id AS site_id
                        FROM `_stock` _stk
                        LEFT JOIN _local _l ON _stk.local_id = _l.id 
                        LEFT JOIN _fab _f ON _l.fab_id = _f.id 
                        LEFT JOIN _site _s ON _f.site_id = _s.id 
                        WHERE _stk.local_id = ? AND cata_SN = ? AND lot_num = ? AND po_no=? ";          
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$p_local, $cata_SN, $lot_num, $po_no]);

        if($stmt_check -> rowCount() >0){       // 已有紀錄
            // echo "<script>alert('process_trade:已有紀錄~')</script>";            // deBug
            $row = $stmt_check -> fetch();
            // 交易狀態：0完成/1待收/2退貨/3取消
            switch($idty){
                case "0":       // 0完成
                case "3":       // 3取消/入帳
                    // echo "<script>alert('0完成2退貨3取消:$p_amount')</script>";   // deBug
                    $row['amount'] += $p_amount; 
                    break;
                case "1":       // 1送出/待收
                    // echo "<script>alert('1待收:$p_amount')</script>";            // deBug
                    $row['amount'] -= $p_amount; 
                    break;
                case "2":       // 2退貨/待收
                default:
                    return;
            }
            $sql = "UPDATE _stock SET amount=?, updated_at=now()
                    WHERE id=?";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$row['amount'], $row['id']]);
                $process_result = true;

            }catch(PDOException $e){
                echo $e->getMessage();
                $process_result = false;

            }
            return $process_result;
        
        }else{      // 開新紀錄
            // echo "<script>alert('process_trade:開新紀錄~')</script>";            // deBug
            // step-1 先把local資料叫出來，抓取low_level數量
            $row_check="SELECT _local.* 
                        FROM `_local`
                        WHERE _local.id=? ";          
            $row = $pdo -> prepare($row_check);
            try {
                $row -> execute([$p_local]);
                $row_local = $row->fetch();
                $process_result = true;
            }catch(PDOException $e){
                echo $e->getMessage();
                $process_result = false;
            }

            if( $row -> rowCount() >0){                                                 // 有取得local資料
                $row_lowLevel = json_decode($row_local["low_level"]);                   // 將local.low_level解碼
                if(is_object($row_lowLevel)) { $row_lowLevel = (array)$row_lowLevel; }  // 將物件轉成陣列
                $low_level = $row_lowLevel[$cata_SN];                                   // 取得該目錄品項的安全存量值
            }else{
                $low_level = 0;                                                         // 未取得local資料時，給他一個0
            }
            
            // step-2 建立新紀錄到資料庫
            $sql = "INSERT INTO _stock(local_id, cata_SN, standard_lv, amount, stock_remark, pno, po_no, lot_num, updated_user, created_at, updated_at)VALUES(?,?,?,?,?,?,?,?,?,now(),now())";
            $stmt = $pdo->prepare($sql);
            try {
                // $stmt->execute([$p_local, $cata_SN, $low_level, $p_amount, '       ',         , $po_no, $lot_num, $user_id]);
                $stmt->execute([$p_local, $cata_SN, $low_level, $p_amount, '', '', $po_no, $lot_num, $updated_user]);
                $process_result = true;
            }catch(PDOException $e){
                echo $e->getMessage();
                $process_result = false;
            }
        }
        return $process_result;
    }

    // 簽核動作處理：
    function sign_trade($request){
        $pdo = pdo();
        extract($request);
        $sys_id = "ppe";

        $trade = show_trade($request);                  // 把trade表單叫近來處理 入賬或回補

        // **** 入賬、回補功能
            $item = json_decode($trade["item"]);  
            // StdObject轉換成Array
            if(is_object($item)) { $item = (array)$item; } 

            // 指向入賬、回補local_id
            switch($idty){      // idty 同意0(入賬)、退回2(回補)、作廢3(回補) 進行選擇性套用!!!!
                case "0":       // 0同意(入賬)
                    $t_local  = $trade["in_local"];        // 入庫廠區
                    break;
                case "3":       // 3作廢(回補)
                    $t_local  = $trade["out_local"];       // 出庫廠區
                    break;
                case "2":       // 2退回(不處理)
                default:        // 預定失效 
                    $t_local  = "";                        // 不處理
                    break;
            }
            
            if($idty != 2){    // 排除簽核2退件，暫時無須處理!
                // 逐筆呼叫處理
                foreach(array_keys($item) as $item_key){
                    $item_key_arr = explode(",", $item_key);
                        if($item_key_arr[0]){ $t_cata_SN = $item_key_arr[0]; } else { $t_cata_SN = ""; }            // cata_SN
                        if($item_key_arr[1]){ $t_stk_id  = $item_key_arr[1]; } else { $t_stk_id  = ""; }            // stock_id
    
                    $item_value = $item[$item_key];
                    $item_value_arr = explode(",", $item_value);
                        if($item_value_arr[0]){ $t_amount  = $item_value_arr[0]; } else { $t_amount  = ""; }        // amount        
                        if($item_value_arr[1]){ $t_po_no   = $item_value_arr[1]; } else { $t_po_no   = ""; }        // po_no
                        if($item_value_arr[2]){ $t_lot_num = $item_value_arr[2]; } else { $t_lot_num = ""; }        // lot_num
    
                    $process = [];              // 清空預設值
                    // 打包處理訊息process
                    $process = array('stock_id' => $t_stk_id,
                                     'updated_user' => $updated_user,
                                     'lot_num'  => $t_lot_num,
                                     'po_no'    => $t_po_no,
                                     'cata_SN'  => $t_cata_SN,
                                     'p_amount' => $t_amount,
                                     'p_local'  => $t_local,
                                     'idty'     => $idty );   // idty:3 = 取消
                    // 後續要加入預扣原本數量功能=呼叫process_trade($request)
                    $process_result = process_trade($process);
                }
            } else {
                $process_result = true; // 因為排除簽核2退件，暫時無須處理! 但需標示處理已完成

            }
        // **** 入賬、回補功能 end  

        if($process_result){
            // echo "<script>alert('入賬、回補功能：success')</script>";

            // 梳理表單資料製作logs前處理
                // $item_enc = json_encode(array_filter($item));          // 去除陣列中空白元素再要編碼

                // 表單簽單人角色
                    if($trade["out_user_id"] == $_SESSION["AUTH"]["emp_id"]){ $step_role = "填單人";}
                    if($trade["in_local"] == $_SESSION[$sys_id]["fab_id"] || (in_array($trade["in_local"], $_SESSION[$sys_id]["sfab_id"])) ){ $step_role = "收貨人";}
                    if(!isset($step_role) && ($_SESSION[$sys_id]["role"] <= 1)){ $step_role = "管你圓";}
                // 製作log紀錄前處理：塞進去製作元素
                    $logs_request["idty"]   = $idty;                    // 表單狀態
                    $logs_request["cname"]  = $updated_user;            // 簽單人
                    $logs_request["step"]   = $step_role;               // 節點-簽單人角色
                    $logs_request["logs"]   = $trade["logs"];           // 帶入舊logs
                    $logs_request["remark"] = $sign_comm;               // 簽核command
                // 呼叫toLog製作log檔
                    $logs_enc = toLog($logs_request);
    
            // 儲存更新trade表單
                // 決定表單儲存時的狀態
                switch($idty){      // idty 同意0(入賬)、退回2(不處理)、作廢3(回補) 進行選擇性套用!!!!
                    case "0":       // 0同意(入賬)
                        $p_idty = '10';    // 指定結案狀態
                        $p_in_user_id = $_SESSION["AUTH"]["emp_id"];
                        $sql = "UPDATE _trade 
                                SET in_user_id=?, idty=?, logs=?, in_date=now() 
                                WHERE id=?";
                        $stmt = $pdo->prepare($sql);
                        try {
                            $stmt->execute([$p_in_user_id, $p_idty, $logs_enc, $id]);
                            $swal_json = array(
                                "fun" => "sign_trade",
                                "action" => "success",
                                "content" => '批量調撥簽核--同意成功'
                            );
                        }catch(PDOException $e){
                            echo $e->getMessage();
                            $swal_json = array(
                                "fun" => "sign_trade",
                                "action" => "error",
                                "content" => '批量調撥簽核--同意失敗'
                            );
                        }
                        break;

                    case "2":       // 2退回(回補)
                        $p_idty = '2';    // 返回退回狀態
                        $sql = "UPDATE _trade SET idty=?, logs=? WHERE id=?";
                        $stmt = $pdo->prepare($sql);
                        try {
                            $stmt->execute([$p_idty, $logs_enc, $id]);
                            $swal_json = array(
                                "fun" => "sign_trade",
                                "action" => "success",
                                "content" => '批量調撥簽核--退回成功'
                            );
                        }catch(PDOException $e){
                            echo $e->getMessage();
                            $swal_json = array(
                                "fun" => "sign_trade",
                                "action" => "error",
                                "content" => '批量調撥簽核--退回失敗'
                            );
                        }
                        break;

                    case "3":       // 3作廢(回補)
                        $p_idty = '3';    // 指定作廢狀態
                        $sql = "UPDATE _trade SET idty=?, logs=? WHERE id=?";
                        $stmt = $pdo->prepare($sql);
                        try {
                            $stmt->execute([$p_idty, $logs_enc, $id]);
                            $swal_json = array(
                                "fun" => "sign_trade",
                                "action" => "success",
                                "content" => '批量調撥簽核--作廢成功'
                            );
                        }catch(PDOException $e){
                            echo $e->getMessage();
                            $swal_json = array(
                                "fun" => "sign_trade",
                                "action" => "error",
                                "content" => '批量調撥簽核--作廢失敗'
                            );
                        }
                        break;

                    default:        // 預定失效 -- 不處理
                        break;
                }

        }else{
            // echo "<script>alert('入賬、回補功能：error')</script>";
            $swal_json = array(
                "fun" => "store_trade",
                "action" => "error",
                "content" => '批量調撥簽核--入賬、回補功能失敗'
            );
        }
        return $swal_json;

    }
// // // process fun -- end

// // // Log tools
    // 製作記錄JSON_Log檔
    function toLog($request){
        extract($request);
        // log資料前處理
        // 交易狀態：0完成/1待收/2退貨/3取消
        switch($idty){
            // case "0":   $action = '核准 (Approve)';       break;
            // case "2":   $action = '駁回 (Disapprove)';    break;
            case "0":   $action = '同意 (Approve)';       break;
            case "1":   $action = '送出 (Submit)';        break;
            case "2":   $action = '退回 (Reject)';        break;
            case "3":   $action = '作廢 (Abort)';         break;
            case "4":   $action = '編輯 (Edit)';          break;
            case "5":   $action = '轉呈 (Transmit)';      break;
            case "6":   $action = '暫存 (Save)';          break;
            case "10":  $action = '結案';                 break;
            case "11":  $action = '轉PR';                 break;
            case "12":  $action = '發貨/待收';            break;
            case "13":  $action = 'PR請購進貨';           break;
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
        $trade = showLogs($query);
        //這個就是JSON格式轉Array新增字串==搞死我
        $logs_dec = json_decode($trade['logs']);
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

        // $trade = show_trade($request);                  // 把trade表單叫近來處理 入賬或回補

        // // **** 入賬、回補功能
        //     $item = json_decode($trade["item"]);
        //     if(is_object($item)) { $item = (array)$item; } 

        //     echo "<pre>";
        //     print_r($item);
        //     echo '</pre><hr>';
    }