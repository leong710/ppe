<?php
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

        $sql = "SELECT c.*, cate.cate_title, cate.id AS cate_id, ps.*
                FROM _cata c
                LEFT JOIN _cate cate ON c.cate_no = cate.cate_no
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
// // // Create表單會用到 -- end

// // // issue需求單 CRUD
    // 儲存交易表單
    function store_issue($request){
        $pdo = pdo();
        extract($request);
        // item資料前處理
        $item_str = json_encode(array_filter($item));   // 去除陣列中空白元素再要編碼

        // 製作log紀錄前處理：塞進去製作元素
            $logs_request["idty"] = $idty;
            $logs_request["cname"] = $cname;
            $logs_request["step"] = "填單人";                   // 節點
            $logs_request["logs"] = "";   
            $logs_request["remark"] = $sin_comm;   
        // 呼叫toLog製作log檔
            $logs = toLog($logs_request);

        //// **** 儲存issue表單
        $sql = "INSERT INTO _issue(create_date, item, in_user_id, in_local, idty, logs, ppty)VALUES(now(),?,?,?,?,?,?)";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$item_str, $in_user_id, $in_local, $idty, $logs, $ppty]);
            $swal_json = array(
                "fun" => "store_issue",
                "action" => "success",
                "content" => '請購需求單--送出成功'
            );
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json = array(
                "fun" => "store_issue",
                "action" => "error",
                "content" => '請購需求單--送出失敗'
            );
        }
        return $swal_json;
    }
    // 顯示被選定的issue表單
    function show_issue($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _issue.*, users_o.cname as cname_o, users_i.cname as cname_i
                        -- , _local_o.local_title as local_o_title, _local_o.local_remark as local_o_remark
                        -- , _local_i.local_title as local_i_title, _local_i.local_remark as local_i_remark
                        -- , _fab_o.fab_title as fab_o_title, _fab_o.fab_remark as fab_o_remark
                        -- , _fab_i.fab_title as fab_i_title, _fab_i.fab_remark as fab_i_remark
                        -- , _site_o.id as site_o_id, _site_o.site_title as site_o_title, _site_o.site_remark as site_o_remark
                        -- , _site_i.id as site_i_id, _site_i.site_title as site_i_title, _site_i.site_remark as site_i_remark
                FROM `_issue`
                LEFT JOIN _users users_o ON _issue.out_user_id = users_o.emp_id
                LEFT JOIN _users users_i ON _issue.in_user_id = users_i.emp_id
                -- LEFT JOIN _local _local_o ON _issue.out_local = _local_o.id
                -- LEFT JOIN _local _local_i ON _issue.in_local = _local_i.id
                -- LEFT JOIN _fab _fab_o ON _local_o.fab_id = _fab_o.id
                -- LEFT JOIN _fab _fab_i ON _local_i.fab_id = _fab_i.id
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
        }
    }
    // 驗收動作的update表單
    function update_issue($request){
        $pdo = pdo();
        extract($request);
        // item資料前處理
        $item_str = json_encode(array_filter($item));   // 去除陣列中空白元素再要編碼

        // 把_issue表單logs叫近來處理
            $query = array('id'=> $id );
            $issue_logs = showLogs($query);
            if($action == "edit"){
                $idty = 4;
            }
        // 製作log紀錄前處理：塞進去製作元素
            $logs_request["action"] = $action;
            $logs_request["step"] = '填單人';
            $logs_request["idty"] = $idty;
            $logs_request["cname"] = $created_cname;
            $logs_request["logs"] = $issue_logs["logs"];   
            $logs_request["remark"] = $sin_comm;   
        // 呼叫toLog製作log檔
            $logs_enc = toLog($logs_request);

        // 更新_issue表單
        $sql = "UPDATE _issue 
                SET item=?, in_user_id=?, in_local=?, idty=?, logs=?, ppty=?
                WHERE id=? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$item_str, $in_user_id, $in_local, $idty, $logs_enc, $ppty, $id]);
            $swal_json = array(
                "fun" => "update_issue",
                "action" => "success",
                "content" => '請購需求單--更新成功'
            );
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json = array(
                "fun" => "update_issue",
                "action" => "error",
                "content" => '請購需求單--更新失敗'
            );
        }
        return $swal_json;
    }
    // 刪除單筆Issue紀錄
    function delete_issue($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM _issue WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // sign動作的_issue表單 20230807
    function sign_issue($request){
        $pdo = pdo();
        extract($request);
    
        // 把_issue表單logs叫近來處理
            $query = array('id'=> $id );
            $issue_logs = showLogs($query);
        // 製作log紀錄前處理：塞進去製作元素
            $logs_request["idty"] = $idty;   
            $logs_request["cname"] = $updated_user;
            $logs_request["logs"] = $issue_logs["logs"];   
            $logs_request["remark"] = $sin_comm;   
        // 呼叫toLog製作log檔
            $logs_enc = toLog($logs_request);

        // 更新_issue表單
        $sql = "UPDATE _issue
                SET idty = ? , logs = ?
                WHERE id = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$idty, $logs_enc, $id]);
            $swal_json = array(
                "fun" => "sign_issue",
                "action" => "success",
                "content" => '請購需求單--sign成功'
            );
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json = array(
                "fun" => "sign_issue",
                "action" => "error",
                "content" => '請購需求單--sign失敗'
            );
        }
        return $swal_json;
    }
    // 驗收動作的update表單  ==> 這是舊的要修正
    function sign_update_issue($request){
        $pdo = pdo();
        extract($request);
        // item資料前處理
        $item_str = json_encode(array_filter($item));   // 去除陣列中空白元素再要編碼

        $issue_id = array('id' => $p_id);               // 指定issue_id
        $issue = showIssue($issue_id);                  // 把issue~原表單叫近來處理
            $b_ppty = $issue['ppty'];                       // 指定~原表單需求類別ppty
            $in_local = $issue['in_local'];                 // 指定~原收件區in_local
            $out_local = $issue['out_local'];               // 指定~原發貨區out_local
            $logs = $issue['logs'];                         // 指定~原表單記錄檔logs
            $b_idty = $issue['idty'];                       // 指定~原表單狀態b_idty
            $in_date = $issue['in_date'];                   // 指定~原表單狀態in_date
            $item_str = $issue["item"];                     // 把item整串(未解碼)存到$item_str
            $item_arr = explode("_," ,$item_str);           // 把字串轉成陣列進行後面的應用
            $item_dec = json_decode($item_arr[0]);          // 解碼後存到$item_dec     = catalog_id
            $amount_dec = json_decode($item_arr[1]);        // 解碼後存到$amount_dec   = amount
        //PHP stdClass Object轉array 
            if(is_object($item_dec)) { $item_dec = (array)$item_dec; } 
            if(is_object($amount_dec)) { $amount_dec = (array)$amount_dec; } 

        // V2 判斷前單$b_idty不是1待簽、12待領，就返回        
        if($b_idty == 1 || $b_idty == 12){
            $idty = $p_idty;
        }else{    
            echo "<script>alert('$b_idty.此表單在您簽核前已被改變成[非待簽核]狀態，請再確認，謝謝!');</script>";
            return;
        }
        
        // 12待收 => 10結案
        if($b_ppty == 1 && $b_idty == 12 && $p_idty == 10){
            // 逐筆呼叫處理
            foreach(array_keys($item_dec) as $it){
                // 假如po_num是空的，給他NA
                if(empty($po_num_dec[$it])){
                    $po_num_dec[$it] = 'NA';
                }
        
                $process = [];  // 清空預設值
                $process = array('stock_id' => $it,
                                'lot_num' => $in_date,             // lot_num = 批號/期限；因PM發貨時會把發貨日寫入in_date，所以只能暫時先吃他
                                'po_num' => $out_local,            // po_num = 採購編號；因PM發貨時會把PO_num寫入out_local
                                'catalog_id' => $item_dec[$it],    // catalog_id = 器材目錄id
                                'p_amount' => $amount_dec[$it],    // p_amount = 正常數量
                                'p_local' => $in_local,             // p_local = local單位id
                                'idty' => $b_idty);                // idty = 交易狀態
                process_issue($process);
            }
        }

        // 把原本沒有的塞進去
        $request['idty'] = $idty;   
        $request['cname'] = $_SESSION["AUTH"]["cname"];
        $request['logs'] = $logs;   
        
        // 呼叫toLog製作log檔
        $logs_enc = toLog($request);

        // 更新trade表單
        $sql = "UPDATE _issue 
                SET idty=?, logs=?, in_date=now() 
                WHERE id=? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$p_idty, $logs_enc, $p_id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }

    }
// // // issue  -- end

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
            case "10":  $action = '結案';                   break;
            case "11":  $action = '轉PR';                   break;
            case "12":  $action = '發貨/待收';              break;
            case "13":  $action = 'PR請購進貨';             break;
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

// // // index 統計數據
    // 20230719 在issue_list秀出所有issue清單
    function show_issue_list($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _issue.*, users_o.cname as cname_o, users_i.cname as cname_i
                       , _local_o.local_title as local_o_title, _local_o.local_remark as local_o_remark
                       , _local_i.local_title as local_i_title, _local_i.local_remark as local_i_remark
                       , _fab_o.fab_title as fab_o_title, _fab_o.fab_remark as fab_o_remark
                       , _fab_i.fab_title as fab_i_title, _fab_i.fab_remark as fab_i_remark
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
        if($emp_id != 'All'){
            if($_SESSION[$sys_id]["role"] <= 1){
                $sql .= " WHERE _issue.out_user_id=? OR _issue.in_user_id=? OR _issue.idty=1 ";      //處理 byUser
            }else{
                $sql .= " WHERE _issue.out_user_id=? OR _issue.in_user_id=? ";      //處理 byUser
            }
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY create_date DESC";
        // 決定是否採用 page_div 20230803
            if(isset($start) && isset($per)){
                $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料=分頁
            }else{
                $stmt = $pdo->prepare($sql);                // 讀取全部=不分頁
            }
     
        try {
            if($emp_id != 'All'){
                $stmt->execute([$emp_id, $emp_id]);         //處理 by User 
            }else{
                $stmt->execute();                           //處理 by All
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
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _issue.ppty, _issue.idty ASC";
        $stmt = $pdo->prepare($sql);
        try {
            if($emp_id != 'All'){
                $stmt->execute([$emp_id, $emp_id, $emp_id, $emp_id]);       //處理 byUser
            }else{
                $stmt->execute();                           //處理 byAll
            }
            $sum_issue = $stmt->fetchAll();
            return $sum_issue;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20230719 在index表頭顯示各類別的數量：    // 統計看板--下：轉PR單
    function show_sum_issue_ship($request){
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
        $stmt = $pdo->prepare($sql.' LIMIT 10');    // TOP 10
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
// // // index 統計數據 -- end


// // // process_issue處理交易事件
    // 處理交易事件--所屬器材數量之加減
    function process_issue($process){
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
        // echo "<script>alert('process_trade:已有紀錄~')</script>";            // deBug
        $row = $stmt_check -> fetch();
            // 交易狀態：0完成/1待收/2退貨/3取消
            switch($idty){
                case "0":
                    // echo "<script>alert('0完成')</script>";                      // deBug
                    $row['amount'] += $p_amount; 
                    break;
                case "1":
                    // echo "<script>alert('1待收:$p_amount')</script>";            // deBug
                    $row['amount'] -= $p_amount; 
                    break;
                case "2":
                    // echo "<script>alert('2退貨:$p_amount')</script>";            // deBug
                    $row['amount'] += $p_amount; 
                    break;
                case "3":
                    // echo "<script>alert('3取消:$p_amount')</script>";            // deBug
                    // echo "<script>alert('local器材已存在，將進行合併計算~')</script>";
                    $row['amount'] += $p_amount; 
                    break;
                case "12":
                    // echo "<script>alert('12收貨:$p_amount_local器材已存在，將進行合併計算~')</script>";            // deBug
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
            // echo "<script>alert('process_trade:開新紀錄~')</script>";            // deBug
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
        // extract($request);
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
        $issue2pr = explode(",",$issue2pr);       //資料表是字串，要炸成陣列
        $issue2pr = array_unique($issue2pr);      //去除重複值，例如：一個廠有申請6項，以前跑6次log，現在改1次

        if(empty($issue2pr)){   // 假如要處理的issue id是空的，就返回
            echo "<script>alert('issue表單id有誤，請再確認~謝謝!');</script>";
            return;
        }
        
        $sql = "UPDATE _issue
                SET idty=?, _ship=?, logs=?, in_date=now()
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            foreach($issue2pr as $issue2pr_id){

                $issue_id = array('id' => $issue2pr_id);      // 指定issue_id
                $row = show_issue($issue_id);                  // 把issue表單叫進來處理
                $logs = $row['logs'];                         // 指定表單記錄檔logs
                // 把原本沒有的塞進去
                $request['cname'] = $_SESSION["AUTH"]["cname"];
                $request['logs'] = $logs;   
        
                // 呼叫toLog製作log檔
                $logs_enc = toLog($request);
                $stmt->execute([$idty, $remark, $logs_enc, $issue2pr_id]);
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
                $row = showIssue($issue_id);                  // 把issue表單叫進來處理
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
            $row = showIssue($issue_id);                  // 把issue表單叫進來處理
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





