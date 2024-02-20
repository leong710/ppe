<?php

// // // ptstock CRUD
    // 新增
    function store_ptstock($request){
        $pdo = pdo();
        extract($request);
        // 數量不是0的正數，捨去關閉功能
        if($amount >= 1){
            $flag = '';
        }else{
            $flag = 'Off';
        }
        // 20240122 新增確認同local_ld+catalog_id+lot_num的單子合併計算
        $sql_check = "SELECT * 
                      FROM pt_stock 
                      WHERE local_id =? AND cata_SN =? AND lot_num =? AND po_no=? ";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$local_id, $cata_SN, $lot_num, $po_no]);

        if($stmt_check -> rowCount() >0){     
            // 確認no編號是否已經被註冊掉，用rowCount最快~不要用fetch
            echo "<script>alert('local同批號衛材已存在，將進行合併計算~')</script>";
            $row = $stmt_check -> fetch();
            $amount += $row["amount"];

            $sql = "UPDATE pt_stock
                    SET standard_lv=?, amount=?, stock_remark=CONCAT(stock_remark, CHAR(10), ?), po_no=?, lot_num=?, flag=?, updated_cname=?, updated_at=now()
                    WHERE id=?";
            $stmt = $pdo->prepare($sql);
            try{
                $stmt->execute([$standard_lv, $amount, $stock_remark, $po_no, $lot_num, $flag, $updated_cname, $row["id"]]);
                $swal_json = array(
                    "fun" => "store_ptstock",
                    "action" => "success",
                    "content" => '合併套用成功'
                );
            }catch(PDOException $e){
                echo $e->getMessage();
                $swal_json = array(
                    "fun" => "store_ptstock",
                    "action" => "error",
                    "content" => '合併套用失敗'
                );
            }
        }else{
            // echo "<script>alert('local器材只有單一筆，不用合併計算~')</script>";
            $sql = "INSERT INTO pt_stock(local_id, cata_SN, standard_lv, amount, stock_remark, pno, po_no, lot_num, updated_cname, created_at, updated_at)VALUES(?,?,?,?,?,?,?,?,?,now(),now())";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$local_id, $cata_SN, $standard_lv, $amount, $stock_remark, $pno, $po_no, $lot_num, $updated_cname]);
                $swal_json = array(
                    "fun" => "store_ptstock",
                    "action" => "success",
                    "content" => '新增套用成功'
                );
            }catch(PDOException $e){
                echo $e->getMessage();
                $swal_json = array(
                    "fun" => "store_ptstock",
                    "action" => "error",
                    "content" => '新增套用失敗'
                );
            }
        }
        return $swal_json;
    }
    // 依ID找出要修改的stock內容            //from edit.php
    function edit_ptstock($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT pt_s.* ,_l.local_title, _l.local_remark, _f.fab_title, _f.fab_remark, _f.buy_ty , _s.site_title, _s.site_remark
                FROM pt_stock pt_s
                LEFT JOIN pt_local _l   ON  pt_s.local_id = _l.id
                LEFT JOIN _fab _f       ON _l.fab_id = _f.id
                LEFT JOIN _site _s      ON _f.site_id = _s.id
                WHERE pt_s.id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $ptstock = $stmt->fetch();
            return $ptstock;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    //修改完成的editPTStock 進行Update        //from editPTStock call updatePTStock
    function update_ptstock($request){
        $pdo = pdo();
        extract($request);
        // 數量不是0的正數，捨去關閉功能
        if($amount >= 1){
            $flag = '';
        }else{
            $flag = 'Off';
        }
            // 20240122 新增確認同local_ld+catalog_id+lot_num的單子合併計算
            $sql_check = "SELECT * 
                          FROM pt_stock 
                          WHERE local_id =? AND cata_SN =? AND lot_num =? AND po_no=? AND id <>? ";
            $stmt_check = $pdo -> prepare($sql_check);
            $stmt_check -> execute([$local_id, $cata_SN, $lot_num, $po_no, $id]);

            if($stmt_check -> rowCount() >0){     
                // 確認no編號是否已經被註冊掉，用rowCount最快~不要用fetch
                echo "<script>alert('local同批號衛材已存在，將進行合併計算~')</script>";
                $row = $stmt_check -> fetch();
                $amount += $row["amount"];
    
                $sql = "UPDATE pt_stock
                        SET standard_lv=?, amount=?, stock_remark=CONCAT(stock_remark, CHAR(10), ?), po_no=?, lot_num=?, flag=?, updated_cname=?, updated_at=now()
                        WHERE id=?";
                $stmt = $pdo->prepare($sql);
                try{
                    $stmt->execute([$standard_lv, $amount, $stock_remark, $po_no, $lot_num, $flag, $updated_cname, $row["id"]]);
                    $swal_json = array(
                        "fun"       => "update_ptstock",
                        "action"    => "success",
                        "content"   => '合併套用成功'
                    );
                    delete_ptstock($request);     // 已合併到另一儲存項目，故需要刪除舊項目

                }catch(PDOException $e){
                    echo $e->getMessage();
                    $swal_json = array(
                        "fun"       => "update_ptstock",
                        "action"    => "error",
                        "content"   => '合併套用失敗'
                    );
                }
            }else{
                // echo "<script>alert('local器材只有單一筆，不用合併計算~')</script>";
                $sql = "UPDATE pt_stock
                        SET local_id=?, cata_SN=?, standard_lv=?, amount=?, stock_remark=?, pno=?, po_no=?, lot_num=?, flag=?, updated_cname=?, updated_at=now()
                        WHERE id=?";
                $stmt = $pdo->prepare($sql);
                try {
                    $stmt->execute([$local_id, $cata_SN, $standard_lv, $amount, $stock_remark, $pno, $po_no, $lot_num, $flag, $updated_cname, $id]);
                    $swal_json = array(
                        "fun"       => "update_ptstock",
                        "action"    => "success",
                        "content"   => '更新套用成功'
                    );
                }catch(PDOException $e){
                    echo $e->getMessage();
                    $swal_json = array(
                        "fun"       => "update_ptstock",
                        "action"    => "error",
                        "content"   => '更新套用失敗'
                    );
                }
            }
        return $swal_json;
    }

    function delete_ptstock($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM pt_stock WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $swal_json = array(
                "fun" => "delete_ptstock",
                "action" => "success",
                "content" => '刪除成功'
            );
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json = array(
                "fun" => "delete_ptstock",
                "action" => "error",
                "content" => '刪除失敗'
            );
        }
        return $swal_json;
    }

// // // 領用單 CRUD 20240126
    // 儲存pt_receive領用申請表單 20240126
    function store_ptreceive($request){
        $pdo = pdo();
        extract($request);

        $swal_json = array(                                 // for swal_json
            "fun"       => "store_ptreceive",
            "content"   => "領用申請--",
            "msg"       => ""
        );

        // item資料前處理
        $item_enc = json_encode(array_filter($item));   // 去除陣列中空白元素再要編碼
            
        //// **** 儲存receive表單
        $sql = "INSERT INTO pt_receive(emp_id, cname, fab_id, ppty, receive_remark, item, idty, app_date, updated_cname
                            , created_at, updated_at) VALUES(?,?,?,?,?, ?,?,?,?, now(),now() )";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$emp_id, $created_cname, $select_fab_id, $ppty, $receive_remark, $item_enc, $idty, $app_date, $created_cname ]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= "送出成功";
            $swal_json["msg"]     .= $cname." 在 ".$fab_title." 申請領用除汙器材，請大PM即時確認!";

        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '送出失敗';
        }

        //// **** process receive表單
        if($swal_json["action"] == "success"){
            // 逐筆呼叫處理
            foreach(array_keys($item) as $item_key){
                $item_key_arr = explode(",", $item_key);
                    if($item_key_arr[0]){ $t_cata_SN = $item_key_arr[0]; } else { $t_cata_SN = ""; }            // cata_SN 序號
                    if($item_key_arr[1]){ $t_stk_id  = $item_key_arr[1]; } else { $t_stk_id  = ""; }            // stock_id 儲存id
                    
                // item資料前處理...因為追加可編輯數量功能
                // item[need]= po_no,lot_num 、 item[pay]= amount
                $item_value = $item[$item_key];
                    if($item_value["pay"]){ $t_amount  = $item_value["pay"]; } else { $t_amount  = ""; }        // amount 數量       
                $item_value_arr = explode(",", $item_value["need"]);
                    if($item_value_arr[0]){ $t_po_no   = $item_value_arr[0]; } else { $t_po_no   = ""; }        // po_no po號碼
                    if($item_value_arr[1]){ $t_lot_num = $item_value_arr[1]; } else { $t_lot_num = ""; }        // lot_num 批號

                if(!empty($t_stk_id)){
                    $qlocal_arr = array("id"=>$t_stk_id);
                    $stk_info = edit_ptstock($qlocal_arr);
                }

                // 使用DateTime类解析日期字符串
                $dateTime = new DateTime($app_date);
                // 使用format方法将日期格式化为所需的格式
                $app_date = $dateTime->format('Y-m-d H:i');

                $process = [];              // 清空預設值
                // 打包處理訊息process
                $process = array('updated_cname'   => $created_cname,
                                 'stock_id'       => $t_stk_id,
                                 'lot_num'        => $t_lot_num,
                                 'po_no'          => $t_po_no,
                                 'cata_SN'        => $t_cata_SN,
                                 'p_amount'       => $t_amount,
                                 'app_date'       => $app_date,        // 處理庫存id存量
                                 'receive_remark' => $receive_remark,
                                 'idty'           => $idty );         // idty:3 = 取消
                // 後續要加入預扣原本數量功能=呼叫process_trade($request)
                $process_result = process_ptreceive($process);
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
        }
        return $swal_json;
    }
    // 顯示被選定的_receive表單 20240129
    function edit_ptreceive($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT pt_r.* , _f.fab_title , _f.fab_remark , _f.sign_code AS fab_sign_code , _f.pm_emp_id
                    -- , _l.fab_id , _l.id AS local_id , _l.local_title , _l.local_remark , _s.site_title , _s.site_remark
                FROM `pt_receive` pt_r
                LEFT JOIN _fab _f ON pt_r.fab_id = _f.id
                    -- LEFT JOIN _local _l ON _r.local_id = _l.id
                    -- LEFT JOIN _site _s ON _f.site_id = _s.id
                WHERE pt_r.id = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $ptreceive_row = $stmt->fetch();
            return $ptreceive_row;
        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }
    // edit動作的_receive表單 20240129
    function update_ptreceive($request){
        $pdo = pdo();
        extract($request);

        $swal_json = array(                                 // for swal_json
            "fun"       => "update_ptreceive",
            "content"   => "更新表單--"
        );

        $ptreceive_row = edit_ptreceive($request);            // 調閱原表單
        // 20231207 加入同時送出被覆蓋的錯誤偵測
        if(isset($old_idty) && ($old_idty != $ptreceive_row["idty"])){
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '送出失敗'.' !! 注意 !! 當您送出表單的同時，該表單型態已被修改，送出無效，請返回確認 ~';
            return $swal_json;
        }

        // item資料前處理
        $item_enc = json_encode(array_filter($item));   // 去除陣列中空白元素，再要編碼
    
        // 更新_receive表單
        $sql = "UPDATE pt_receive
                SET emp_id = ?, cname = ?, fab_id = ?, ppty = ?, receive_remark = ?, item = ?, idty = ?, app_date = ?, updated_cname = ?
                    , updated_at = now()
                WHERE id = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$emp_id, $created_cname, $select_fab_id, $ppty, $receive_remark, $item_enc, $idty, $app_date, $created_cname, $id]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '更新成功';
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '更新失敗';
        }
        return $swal_json;
    }
    // 刪除單筆_receive紀錄 20240129
    function delete_ptreceive($request){
        $pdo = pdo();
        extract($request);
        
        $swal_json = array(                                 // for swal_json
            "fun"       => "delete_ptreceive",
            "content"   => "刪除表單--"
        );

        $ptreceive_row = edit_ptreceive($request);            // 調閱原表單
        // 20231207 加入同時送出被覆蓋的錯誤偵測
        if(isset($old_idty) && ($old_idty != $ptreceive_row["idty"])){
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '送出失敗'.' !! 注意 !! 當您送出表單的同時，該表單型態已被修改，送出無效，請返回確認 ~';
            return $swal_json;
        }

        $sql = "DELETE FROM pt_receive WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            return true;
        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }

// // // process fun
    // 20230724 處理交易事件--所屬器材數量之加減
    // ** 20230725 處理貨單時 已在stock內的只能用ID，不要用SN...因為可能有多筆同SN，而導致錯亂 ??!!
    // $idty 2=退貨、3=取消/入帳、1送出/待收
    
    function process_ptreceive($process){
        $pdo = pdo();
        extract($process);
        $qptstock_arr = array("id"=>$stock_id);
        $row_stk = edit_ptstock($qptstock_arr);

        if(count($row_stk) >0){       // 已有紀錄
            // echo "<script>alert('process_trade:已有紀錄~')</script>";            // deBug
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
            if($row_stk['amount'] > 0){
                $flag = "";
            }else{
                $flag = "Off";
            }

            $sql = "UPDATE pt_stock SET stock_remark=?, amount=?, flag=?, updated_cname=?, updated_at=now() WHERE id=? ";
            $stmt = $pdo->prepare($sql);
            if($row_stk["stock_remark"]){ $row_stk["stock_remark"] .= "<br>"; } 
            $row_stk["stock_remark"] .= $receive_remark." // ".$app_date."：".$row_stk['cata_SN'] . $cama['icon'] . $p_amount . " = " . $row_stk['amount'];
            try {
                $stmt->execute([$row_stk["stock_remark"], $row_stk['amount'], $flag, $updated_cname, $row_stk['id']]);
                $process_result['result'] = $row_stk['fab_title'] . "_" . $row_stk['local_title'] . " " . $row_stk['cata_SN'] . $cama['icon'] . $p_amount . " = " . $row_stk['amount'];      // 回傳 True: id + amount

            }catch(PDOException $e){
                echo $e->getMessage();
                $process_result['error'] = $row_stk['fab_title'] . "_" . $row_stk['local_title'] . " " . $cama['title'] . "id:".($row_stk['id'] * -1);               // 回傳 False: - id

            }
            return $process_result;
        
        }else{      // 開新紀錄 --- 先暫停
        echo "<script>alert('process_ptreceive:暫停開新紀錄~')</script>";             // deBug
            //     // step-1 先把local資料叫出來，抓取low_level數量
                    
            //     $row_local = select_ptlocal($p_local);                                  // call fun get local's info & low_level
                
            //     if(!empty($row_local)){                                  // 有取得local資料
            //         $row_lowLevel = json_decode($row_local["low_level"]);                   // 將local.low_level解碼
            //         if(is_object($row_lowLevel)) { $row_lowLevel = (array)$row_lowLevel; }  // 將物件轉成陣列
            //         if(isset($row_lowLevel[$cata_SN])){
            //             $low_level = $row_lowLevel[$cata_SN];                           // 取得該目錄品項的安全存量值
            //         }else{
            //             $low_level = 0;                                                 // 未取得local資料時，給他一個0
            //         }
            //     }else{
            //         $low_level = 0;                                                     // 未取得local資料時，給他一個0
            //     }

            //     switch($idty){
            //         case "0":       // 0完成
            //         case "2":       // 2退貨/待收
            //         case "3":       // 3取消/入帳
            //             // echo "<script>alert('0完成2退貨3取消:$p_amount')</script>";   // deBug
            //             $cama = array(
            //                 'icon'  => ' + ',     // log交易訊息中加減號
            //                 'title' => ' 入帳 '   // log交易訊息 動作
            //             );
            //             break;
            //         case "1":       // 1送出/待收
            //             // echo "<script>alert('1待收:$p_amount')</script>";            // deBug
            //             $cama = array(
            //                 'icon'  => ' - ',     // log交易訊息中加減號
            //                 'title' => ' 扣帳 '   // log交易訊息 動作
            //             );
            //             break;
            //         default:
            //             $cama = array(
            //                 'icon'  => ' ? ',     // log交易訊息中加減號
            //                 'title' => ' 錯誤 '   // log交易訊息 動作
            //             );
            //             return;
            //     }

            //     $lot_num        = "9999-12-31";                                          // 0.批號/效期
            //     $stock_remark   = " *".$cama["title"]."：".$cama["icon"].$p_amount;      // 0.備註
            //         // // $stock_remark .= $stk_row_list[$i]['stock_remark'];
                
            //     // step-2 建立新紀錄到資料庫
            //     $store_ptstock_arr = array (
            //         "p_local"      => $p_local, 
            //         "cata_SN"      => $cata_SN, 
            //         "low_level"    => $low_level, 
            //         "p_amount"     => $p_amount, 
            //         "stock_remark" => $stock_remark, 
            //         "pno"          => $pno, 
            //         "po_no"        => $po_no, 
            //         "lot_num"      => $lot_num, 
            //         "updated_user" => $updated_user
            //     );
            //     try {
            //         store_ptstock($store_ptstock_arr);
            //         $process_result['result'] = $row_local['fab_title'] . "_" . $row_local['local_title'] . " +新 ". $cata_SN . $cama['icon'] . $p_amount . " = " . $p_amount;                   // 回傳 True: id - amount
            //     }catch(PDOException $e){
            //         echo $e->getMessage();
            //         $process_result['error'] = $row_local['fab_title'] . "_" . $row_local['local_title'] . " -新 ". $cata_SN . $cama['icon'] . $p_amount . " = " . $p_amount;                   // 回傳 False: - id
            //     }
        }
        return $process_result;
    }


// // // process fun -- end

// // // index
    // --- ptstock index  20240122
    function show_ptstock($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _stk.*, _stk.id AS stk_id ,_cata.PIC
                        ,_l.local_title, _l.local_remark, _f.id AS fab_id, _f.fab_title, _f.fab_remark, _s.id as site_id, _s.site_title, _s.site_remark
                        ,_cata.pname, _cata.cata_remark, _cata.SN, _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no, _cata.flag AS cata_flag 
                FROM `pt_stock` _stk 
                LEFT JOIN pt_local _l ON _stk.local_id = _l.id 
                LEFT JOIN _fab _f ON _l.fab_id = _f.id 
                LEFT JOIN _site _s ON _f.site_id = _s.id 
                LEFT JOIN _cata ON _stk.cata_SN = _cata.SN 
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no 
                WHERE _cata.cate_no = 'J' 
                 ";
        if($select_fab_id == "allMy" && $sfab_id != "All"){
            $sql .= " AND _l.fab_id IN ({$sfab_id}) ";
        }else if($select_fab_id != "All"){
            $sql .= " AND _l.fab_id = ? ";
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY fab_id, local_id, cata_SN, _stk.lot_num ASC ";
        // 決定是否採用 page_div 20230803
        if(isset($start) && isset($per)){
            $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per);   // 讀取選取頁的資料=分頁
        }else{
            $stmt = $pdo->prepare($sql);                                // 讀取全部=不分頁
        }
        try {
            if($select_fab_id == "allMy" && $sfab_id != "All"){
                $stmt->execute();
            }else if($select_fab_id != "All"){
                $stmt->execute([$select_fab_id]);
            }else{
                $stmt->execute();
            }
            $ptstocks = $stmt->fetchAll();
            return $ptstocks;
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
    // 20240123 index fab_list：role <=1 ? All+all_fab : sFab_id+allMy => select_fab_id
    function show_fab_list($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _f.id, _f.fab_title, _f.fab_remark, _f.flag, _site.site_title 
                FROM _fab _f
                LEFT JOIN _site ON _f.site_id = _site.id 
                WHERE _f.flag = 'On' ";
        // if($select_fab_id == 'allMy'){
        if($fab_scope != 'All' ){
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
    // 設定low_level時用選則區域 20230707_updated
    function select_ptlocal($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _l.*, _f.fab_title, _f.fab_remark, _f.buy_ty , _s.site_title, _s.site_remark
                FROM `pt_local` _l
                LEFT JOIN _fab _f ON _l.fab_id = _f.id
                LEFT JOIN _site _s ON _f.site_id = _s.id
                WHERE _l.id=? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$local_id]);
            $local = $stmt->fetch();
            return $local;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // --- ptreceive index  20240129
    function show_ptreceive($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT pt_r.* , _f.fab_title, _f.fab_remark
                FROM `pt_receive` pt_r 
                LEFT JOIN _fab _f ON pt_r.fab_id = _f.id ";
        if($select_fab_id == "allMy"){
            $sql .= " WHERE pt_r.fab_id IN ({$sfab_id}) ";
        }else if($select_fab_id != "All"){
            $sql .= " WHERE pt_r.fab_id = ? ";
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY app_date DESC ";
        // 決定是否採用 page_div 20230803
        if(isset($start) && isset($per)){
            $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per);   // 讀取選取頁的資料=分頁
        }else{
            $stmt = $pdo->prepare($sql);                                // 讀取全部=不分頁
        }
        try {
            if($select_fab_id != "All" && $select_fab_id != "allMy"){
                $stmt->execute([$select_fab_id]);
            }else{
                $stmt->execute();
            }
            $ptstocks = $stmt->fetchAll();
            return $ptstocks;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // PM顯示全部by年份 => 供查詢年份使用
    function show_ptreceive_yy(){
        $pdo = pdo();
        $sql = "SELECT DISTINCT DATE_FORMAT(created_at, '%Y') as yy 
                -- SELECT DISTINCT DATE_FORMAT(created_at, '%Y-%m') as ym 
                FROM pt_receive 
                ORDER BY yy DESC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $ptreceive_yys = $stmt->fetchAll();
            return $ptreceive_yys;
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
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _plan.updated_at DESC ";
        $stmt = $pdo->prepare($sql);
        try {
            // echo $sql;
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
    // 20240220 查出ppe的大PM名單 role = 1
    function show_PPE_PM(){
        $pdo = pdo();
        $sql = "SELECT _u.emp_id 
                FROM `_users` _u
                WHERE _u.role = '1' ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $PPE_PMs = $stmt->fetchAll();
            return $PPE_PMs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
// // // Create & Edit

    // 20240123 create時用自己選定Fab區域的Local list
    // 20240123 edit時用role <=0 ? all全區域 : user sFab_id
    function show_fabs_local($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _l.*, _f.fab_title, _f.fab_remark, _f.flag AS fab_flag, _s.site_title, _s.site_remark
                FROM `pt_local` _l
                LEFT JOIN _fab _f ON _l.fab_id = _f.id
                LEFT JOIN _site _s ON _f.site_id = _s.id
                WHERE _l.flag='On' ";
        if($select_fab_id == "allMy" && $sfab_id != "All"){
            $sql .= " AND _l.fab_id IN ({$sfab_id}) ";
        }else if($select_fab_id != "All"){
            $sql .= " AND _l.fab_id = ? ";
        }
        $sql .= " ORDER BY _s.id, _f.id, _l.id ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            if($select_fab_id == "allMy" && $sfab_id != "All"){
                $stmt->execute();
            }else if($select_fab_id != "All"){
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

// // // Check_log
    // 20231222 doCheck_log() ：半年檢
    function store_checked($request){
        $pdo = pdo();
        extract($request);

        $swal_json = array(                                 // for swal_json
            "fun"       => "store_checked",
            "content"   => "存量點檢單--"
        );

        // $logs = JSON_encode($stocks_log);
        $sql = "INSERT INTO checked_log(fab_id, stocks_log, emp_id, updated_cname, checked_remark, checked_year, half, created_at, updated_at)VALUES(?,?,?,?,?,?,?,now(),now())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$fab_id, $stocks_log, $emp_id, $cname, $checked_remark, $checked_year, $half]);
            $swal_json["action"]   = "success";
            $swal_json["content"] .= '送出成功';
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json["action"]   = "error";
            $swal_json["content"] .= '送出失敗';
        }
        return $swal_json;
    }
    // --- stock index  20230725：半年檢
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
    // fab user index查詢自己的點檢紀錄for顯示點檢表功能
    function check_yh_list($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT cl.*
                FROM `checked_log` cl
                WHERE cl.fab_id = ? AND cl.checked_year = ? AND cl.half = ? AND cl.form_type = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$fab_id, $checked_year, $half, $form_type]);
            $check_yh_list = $stmt->fetchAll();
            return $check_yh_list;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

// // // doCSV function
    // 匯出CSV
    function export_csv($filename, $data){ 
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Content-Disposition: attachment;filename="' . $filename . '";');
        header('Content-Type: application/csv; charset=UTF-8');
        echo $data; 
    } 
    // 匯入CSV
    function import_csv($filename) {
        $csvData = array();
    
        if (($handle = fopen($filename, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $csvData[] = $data;
            }
            fclose($handle);
        }
        return $csvData;
    }
    
// // // doCSV -- end

    // API更新amount
    // function update_amount($request)  // move to api_function.php

// // // stock  -- end


    // 依衛材名稱顯示   20240122
    function show_ptstock_byCatalog($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _stk.*, 
                        _l.local_title, _l.local_remark, _f.id AS fab_id, _f.fab_title, _f.fab_remark, _s.id as site_id, _s.site_title, _s.site_remark, 
                        _cata.pname, _cata.cata_remark, _cata.SN, _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no  
                FROM `pt_stock` _stk 
                LEFT JOIN _local _l ON _stk.local_id = _l.id 
                LEFT JOIN _fab _f ON _l.fab_id = _f.id 
                LEFT JOIN _site _s ON _f.site_id = _s.id 
                LEFT JOIN _cata ON _stk.cata_SN = _cata.SN 
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no 
                WHERE _f.id=?
                ORDER BY cata_SN, fab_id, local_id, lot_num ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$fab_id]);
            $ptstocks = $stmt->fetchAll();
            return $ptstocks;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    // --- stock index  20231218 領用量
    function show_my_receive($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT DISTINCT _r.* , _l.local_title , _l.local_remark , _f.id AS fab_id , _f.fab_title , _f.fab_remark , _f.sign_code AS fab_sign_code , _f.pm_emp_id , _s.site_title , _s.site_remark 
                FROM `_receive` _r
                LEFT JOIN _local _l ON _r.local_id = _l.id
                LEFT JOIN _fab _f ON _l.fab_id = _f.id
                LEFT JOIN _site _s ON _f.site_id = _s.id
                WHERE _l.fab_id = ? AND DATE_FORMAT(_r.created_at, '%Y') = ? AND ( _r.idty = 10 OR _r.idty = 11 OR _r.idty = 13)";  // 10=結案
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$fab_id, $thisYear]);
            $my_receive_lists = $stmt->fetchAll();
            return $my_receive_lists;

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }



// // // _cata & _cate
    // // category Function    20230713
    // function show_categories(){
    //     $pdo = pdo();
    //     $sql = "SELECT * 
    //             FROM _cate 
    //             ORDER BY id ASC";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         $stmt->execute();
    //         $categories = $stmt->fetchAll();
    //         return $categories;
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }
    // // 在index表頭顯示各類別的數量：   20230713
    // function show_sum_category($request){
    //     $pdo = pdo();
    //     extract($request);
    //     $sql = "SELECT _cate.id AS cate_id, _cate.cate_no, _cate.cate_title, _cate.cate_remark, 
    //                     ( SELECT COUNT(*) 
    //                       FROM `_stock` s
    //                       LEFT JOIN _local l ON s.local_id = l.id
    //                       LEFT JOIN _cata cta ON s.cata_SN = cta.SN 
    //                       LEFT JOIN _cate cte ON cta.cate_no = cte.cate_no 
    //                       WHERE l.fab_id = _l.fab_id AND cta.cate_no = _cate.cate_no ) 
    //                     AS 'stock_count'
    //             FROM `_stock` stk
    //             LEFT JOIN _local _l ON stk.local_id = _l.id 
    //             LEFT JOIN _cata ON stk.cata_SN = _cata.SN 
    //             LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no 
    //             WHERE _l.fab_id = ?
    //             GROUP BY _cate.id ";
    //     $stmt = $pdo->prepare($sql);
    //         try {
    //             $stmt->execute([$fab_id]);
    //             $sum_category = $stmt->fetchAll();
    //             return $sum_category;
    //         }catch(PDOException $e){
    //             echo $e->getMessage();
    //         }
    // }

// // // _cata & _cate  -- end
    // // 20240123 create時用自己選定Fab區域的Local list
    // function show_local2create($request){
    //     $pdo = pdo();
    //     extract($request);
    //     $sql = "SELECT pt_local.*, _site.site_title, _site.site_remark, _fab.fab_title, _fab.fab_remark, _fab.flag AS fab_flag
    //             FROM `pt_local`
    //             LEFT JOIN _fab ON pt_local.fab_id = _fab.id
    //             LEFT JOIN _site ON _fab.site_id = _site.id
    //             WHERE pt_local.flag='On' AND pt_local.fab_id=?
    //             ORDER BY _fab.id, pt_local.id ASC";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         $stmt->execute([$select_fab_id]);
    //         $locals = $stmt->fetchAll();
    //         return $locals;
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }
    // // 20240123 edit時用role <=0 ? all全區域 : user sFab_id
    // function show_local2edit($request){
    //     $pdo = pdo();
    //     extract($request);
    //     $sql = "SELECT pt_local.*, _site.site_title, _site.site_remark, _fab.fab_title, _fab.fab_remark, _fab.flag AS fab_flag
    //             FROM `pt_local`
    //             LEFT JOIN _fab ON pt_local.fab_id = _fab.id
    //             LEFT JOIN _site ON _fab.site_id = _site.id
    //             WHERE pt_local.flag='On' ";
    //     if($sfab_id != 'All'){
    //         $sql .= "  AND pt_local.fab_id IN ({$sfab_id}) ";
    //     }  
    //     $sql .= " ORDER BY _site.id, _fab.id, pt_local.id ASC ";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         $stmt->execute();
    //         $ptlocals = $stmt->fetchAll();
    //         return $ptlocals;
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }

    // // 顯示全部by年月 => 供查詢年月份使用
    // function show_receive_list_yy(){
    //     $pdo = pdo();
    //     $sql = "SELECT DISTINCT SUBSTRING(al.thisDay, 1, 4) AS yy
    //             FROM `autolog` al
    //             ORDER BY yy DESC";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         $stmt->execute();
    //         $receive_list_yy = $stmt->fetchAll();
    //         return $receive_list_yy;
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }

    // function show_site($request){
    //     $pdo = pdo();
    //     extract($request);
    //     $sql = "SELECT * 
    //             FROM _site ";
    //     if($site_id != 'All'){
    //         $sql .= " WHERE _site.id=? ";
    //     }

    //     $sql .= " ORDER BY _site.id ASC";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         if($site_id != 'All'){
    //             $stmt->execute([$site_id]);
    //             $sites = $stmt->fetch();
    //         }else{
    //             $stmt->execute();
    //             $sites = $stmt->fetchAll();
    //         }
    //         return $sites;
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }

    // // 設定low_level時用選則區域 for create
    // function select_local($request){
    //     $pdo = pdo();
    //     extract($request);
    //     $sql = "SELECT _local.*, _site.site_title, _site.site_remark, _fab.fab_title, _fab.fab_remark, _fab.flag AS fab_flag
    //             FROM `_local`
    //             LEFT JOIN _fab ON _local.fab_id = _fab.id
    //             LEFT JOIN _site ON _fab.site_id = _site.id
    //             WHERE _local.id=?
    //             ORDER BY _fab.id, _local.id ASC";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         $stmt->execute([$local_id]);
    //         $local = $stmt->fetch();
    //         return $local;
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }
    // }
    // // 找出自己的資料 
    // function show_User($request){
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

    // // 分拆--損壞--遺失--更新原紀錄
    // function spinOff_stock($request){
    //     $pdo = pdo();
    //     extract($request);

    //     // step1:更新=先扣除原表單數量+註解
    //     if($spinOff_damage > $damage){
    //         $amount -= ($spinOff_damage - $damage);
    //     }else{
    //         $amount += ($damage - $spinOff_damage);
    //     }

    //     if($spinOff_loss > $loss){
    //         $amount -= ($spinOff_loss - $loss);
    //     }else{
    //         $amount += ($loss - $spinOff_loss);
    //     }

    //     $sql = "UPDATE stock
    //             SET amount=?, damage=?, loss=?, d_remark=?, user_id=?, updated_at=now()
    //             WHERE id=?";
    //     $stmt = $pdo->prepare($sql);
    //     try {
    //         $stmt->execute([$amount, $spinOff_damage, $spinOff_loss, $spinOff_d_remark, $user_id, $id]);
    //     }catch(PDOException $e){
    //         echo $e->getMessage();
    //     }

    // }

    // // 20220829 儲存PR請購進貨單
    // function store_restock($request){
    //     $pdo = pdo();
    //     extract($request);
    //     // item資料前處理
    //         // $item = $request['item'];                // 陣列指向
    //             $item = array_filter($item);            // 去除陣列中空白元素
    //             $amount = array_filter($amount);        // 去除陣列中空白元素
    //             $lot_num = array_filter($lot_num);      // 去除陣列中空白元素
    //             $po_num = array_filter($po_num);        // 去除陣列中空白元素=因為很多都沒有資料，過濾會導致錯誤。
    //         // 小陣列要先編碼才能塞進去大陣列
    //             $item_enc = json_encode($item);
    //             $amount_enc = json_encode($amount);
    //             $lot_num_enc = json_encode($lot_num);
    //             $po_num_enc = json_encode($po_num);
    //         //陣列合併
    //             $item_arr = [];
    //             $item_arr['lot_num'] = $lot_num_enc;
    //             $item_arr['po_num'] = $po_num_enc;
    //             $item_arr['item'] = $item_enc;
    //             $item_arr['amount'] = $amount_enc;
    //         // implode()把陣列元素組合為字串：
    //             $item_str = implode("_," , $item_arr);               // 陣列轉成字串進行儲存到mySQL

    //         // 設定表單狀態idty=1待領
    //             $idty = '1';

    //     //// **** 儲存Trade表單
    //         $sql = "INSERT INTO trade(out_date, item, out_user_id, out_local, in_local, idty, logs)VALUES(now(),?,?,?,?,?,?)";
    //         $stmt = $pdo->prepare($sql);
    //         try {
    //             $stmt->execute([$item_str, $out_user_id, $out_local, $in_local, $idty, $logs]);
    //         }catch(PDOException $e){
    //             echo $e->getMessage();
    //         }

    //     //// **** 預扣功能    
    //     // StdObject轉換成Array
    //     if(is_object($item)) { $item = (array)$item; } 
    //     if(is_object($amount)) { $amount = (array)$amount; } 
    //     if(is_object($lot_num)) { $lot_num = (array)$lot_num; } 
    //     if(is_object($po_num)) { $po_num = (array)$po_num; } 
    //     // 逐筆呼叫處理
    //     foreach($item as $it){
    //         $process = [];  // 清空預設值
    //         $process = array('local_id' => $in_local, 
    //                          'catalog_id' => $item[$it], 
    //                          'standard_lv' => '0', 
    //                          'amount' => $amount[$it], 
    //                          'remark' => $remark, 
    //                          'lot_num' => $lot_num[$it], 
    //                          'po_num' => $po_num[$it], 
    //                          'd_remark' => $d_remark, 
    //                          'user_id' => $out_user_id );
    //         // 後續要加入預扣原本數量功能=呼叫process_trade($request)
    //         store_stock($process);
    //     }
    // }

