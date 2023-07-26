<?php

// // // stock
    // 新增
    function store_stock($request){
        $pdo = pdo();
        extract($request);
        // 20220706 新增確認同local_ld+catalog_id+lot_num的單子合併計算
        $sql_check = "SELECT * 
                      FROM _stock 
                      WHERE local_id =? AND cata_SN =? AND lot_num =? AND po_no=? ";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$local_id, $cata_SN, $lot_num, $po_no]);

        if($stmt_check -> rowCount() >0){     
            // 確認no編號是否已經被註冊掉，用rowCount最快~不要用fetch
            echo "<script>alert('local同批號衛材已存在，將進行合併計算~')</script>";
            $row = $stmt_check -> fetch();
            $amount += $row["amount"];

            $sql = "UPDATE _stock
                    SET standard_lv=?, amount=?, stock_remark=CONCAT(stock_remark, CHAR(10), ?), po_no=CONCAT(po_no, CHAR(10), ?), lot_num=?, updated_user=?, updated_at=now()
                    WHERE id=?";
            $stmt = $pdo->prepare($sql);
            try{
                $stmt->execute([$standard_lv, $amount, $stock_remark, $po_no, $lot_num, $updated_user, $row["id"]]);
                $swal_json = array(
                    "fun" => "store_stock",
                    "action" => "success",
                    "content" => '合併套用成功'
                );
            }catch(PDOException $e){
                echo $e->getMessage();
                $swal_json = array(
                    "fun" => "store_stock",
                    "action" => "error",
                    "content" => '合併套用失敗'
                );
            }
        }else{
            // echo "<script>alert('local器材只有單一筆，不用合併計算~')</script>";
            $sql = "INSERT INTO _stock(local_id, cata_SN, standard_lv, amount, stock_remark, pno, po_no, lot_num, updated_user, created_at, updated_at)VALUES(?,?,?,?,?,?,?,?,?,now(),now())";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$local_id, $cata_SN, $standard_lv, $amount, $stock_remark, $pno, $po_no, $lot_num, $updated_user]);
                $swal_json = array(
                    "fun" => "store_stock",
                    "action" => "success",
                    "content" => '新增套用成功'
                );
            }catch(PDOException $e){
                echo $e->getMessage();
                $swal_json = array(
                    "fun" => "store_stock",
                    "action" => "error",
                    "content" => '新增套用失敗'
                );
            }
        }
        return $swal_json;
    }
    // 依ID找出要修改的stock內容            //from edit.php
    function edit_stock($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _stock.* FROM _stock WHERE _stock.id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $stock = $stmt->fetch();
            return $stock;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    //修改完成的editStock 進行Update        //from editStock call updateStock
    function update_stock($request){
        $pdo = pdo();
        extract($request);
        $sql = "UPDATE _stock
                SET local_id=?, cata_SN=?, standard_lv=?, amount=?, stock_remark=?, pno=?, po_no=?, lot_num=?, updated_user=?, updated_at=now()
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$local_id, $cata_SN, $standard_lv, $amount, $stock_remark, $pno, $po_no, $lot_num, $updated_user, $id]);
            $swal_json = array(
                "fun" => "update_stock",
                "action" => "success",
                "content" => '更新套用成功'
            );
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json = array(
                "fun" => "update_stock",
                "action" => "error",
                "content" => '更新套用失敗'
            );
        }
        return $swal_json;
    }

    function delete_stock($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM _stock WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $swal_json = array(
                "fun" => "delete_stock",
                "action" => "success",
                "content" => '刪除成功'
            );
        }catch(PDOException $e){
            echo $e->getMessage();
            $swal_json = array(
                "fun" => "delete_stock",
                "action" => "error",
                "content" => '刪除失敗'
            );
        }
        return $swal_json;
    }

    // --- stock index  20230713
    function show_stock($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _stk.*, 
                        _l.local_title, _l.local_remark, _f.id AS fab_id, _f.fab_title, _f.fab_remark, _s.id as site_id, _s.site_title, _s.site_remark, 
                        _cata.pname, _cata.cata_remark, _cata.SN, _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no 
                FROM `_stock` _stk 
                LEFT JOIN _local _l ON _stk.local_id = _l.id 
                LEFT JOIN _fab _f ON _l.fab_id = _f.id 
                LEFT JOIN _site _s ON _f.site_id = _s.id 
                LEFT JOIN _cata ON _stk.cata_SN = _cata.SN 
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no ";
        if($cate_no != "All"){
            $sql .= " WHERE _f.id = ? AND _cate.cate_no = ? ";
        }else{
            $sql .= " WHERE _f.id = ? ";
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY fab_id, local_id, cata_SN, _stk.lot_num ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            if($cate_no != "All"){
                $stmt->execute([$fab_id, $cate_no]);
            }else{
                $stmt->execute([$fab_id]);
            }
            $stocks = $stmt->fetchAll();
            return $stocks;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // stock index分頁工具  20230713
    function stock_page_div($start, $per, $request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _stk.*, 
                        _l.local_title, _l.local_remark, _f.id AS fab_id, _f.fab_title, _f.fab_remark, _s.id as site_id, _s.site_title, _s.site_remark, 
                        _cata.pname, _cata.cata_remark, _cata.SN, _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no  
                FROM `_stock` _stk 
                LEFT JOIN _local _l ON _stk.local_id = _l.id 
                LEFT JOIN _fab _f ON _l.fab_id = _f.id 
                LEFT JOIN _site _s ON _f.site_id = _s.id 
                LEFT JOIN _cata ON _stk.cata_SN = _cata.SN 
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no ";
        if($cate_no != "All"){
            $sql .= " WHERE _f.id = ? AND _cate.cate_no = ? ";
        }else{
            $sql .= " WHERE _f.id = ? ";
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY fab_id, local_id, cata_SN, _stk.lot_num ASC ";
        $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料
        try {
            if($cate_no != "All"){
                $stmt->execute([$fab_id, $cate_no]);
            }else{
                $stmt->execute([$fab_id]);
            }
            $rs = $stmt->fetchAll();
            return $rs;
        }catch(PDOException $e){
            echo $e->getMessage(); 
        }
    }
    // 依衛材名稱顯示   20230713 
    function show_stock_byCatalog($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _stk.*, 
                        _l.local_title, _l.local_remark, _f.id AS fab_id, _f.fab_title, _f.fab_remark, _s.id as site_id, _s.site_title, _s.site_remark, 
                        _cata.pname, _cata.cata_remark, _cata.SN, _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no  
                FROM `_stock` _stk 
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
            $stocks = $stmt->fetchAll();
            return $stocks;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
// // // stock  -- end

// // // _cata & _cate
    // catalog Function   20230713
    function show_catalogs(){
        $pdo = pdo();
        $sql = "SELECT * 
                FROM _cata
                -- WHERE _cata.flag = 'On'
                ORDER BY id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $catalogs = $stmt->fetchAll();
            return $catalogs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // category Function    20230713
    function show_categories(){
        $pdo = pdo();
        $sql = "SELECT * 
                FROM _cate 
                ORDER BY id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $categories = $stmt->fetchAll();
            return $categories;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 在index表頭顯示各類別的數量：   20230713
    function show_sum_category($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _cate.id AS cate_id, _cate.cate_no, _cate.cate_title, _cate.cate_remark, 
                        ( SELECT COUNT(*) 
                          FROM `_stock` s
                          LEFT JOIN _local l ON s.local_id = l.id
                          LEFT JOIN _cata cta ON s.cata_SN = cta.SN 
                          LEFT JOIN _cate cte ON cta.cate_no = cte.cate_no 
                          WHERE l.fab_id = _l.fab_id AND cta.cate_no = _cate.cate_no ) 
                        AS 'stock_count'
                FROM `_stock` stk
                LEFT JOIN _local _l ON stk.local_id = _l.id 
                LEFT JOIN _cata ON stk.cata_SN = _cata.SN 
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no 
                WHERE _l.fab_id = ?
                GROUP BY _cate.id ";
        $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$fab_id]);
                $sum_category = $stmt->fetchAll();
                return $sum_category;
            }catch(PDOException $e){
                echo $e->getMessage();
            }
    }

// // // _cata & _cate  -- end

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

    function show_site($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT * 
                FROM _site ";
        if($site_id != 'All'){
            $sql .= " WHERE _site.id=? ";
        }

        $sql .= " ORDER BY _site.id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            if($site_id != 'All'){
                $stmt->execute([$site_id]);
                $sites = $stmt->fetch();
            }else{
                $stmt->execute();
                $sites = $stmt->fetchAll();
            }
            return $sites;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function show_fab($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _f.*, _site.site_title 
                FROM _fab _f
                LEFT JOIN _site ON _f.site_id = _site.id ";
        if($fab_id != 'All'){
            $sql .= " WHERE _f.id=? ";
        }  
        $sql .= " ORDER BY _f.id ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            if($fab_id != 'All'){
                $stmt->execute([$fab_id]);
                $fabs = $stmt->fetch();
            }else{
                $stmt->execute();
                $fabs = $stmt->fetchAll();
            }
            return $fabs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // create時用自己區域
    function show_local($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _local.*, _site.site_title, _site.site_remark, _fab.fab_title, _fab.fab_remark, _fab.flag AS fab_flag
                FROM `_local`
                LEFT JOIN _fab ON _local.fab_id = _fab.id
                LEFT JOIN _site ON _fab.site_id = _site.id
                WHERE _local.flag='On' AND _local.fab_id=?
                ORDER BY _fab.id, _local.id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$fab_id]);
            $locals = $stmt->fetchAll();
            return $locals;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // edit時用全區域~ 但user鎖編輯權限
    function show_allLocal(){
        $pdo = pdo();
        $sql = "SELECT _local.*, _site.site_title, _site.site_remark, _fab.fab_title, _fab.fab_remark, _fab.flag AS fab_flag
                FROM `_local`
                LEFT JOIN _fab ON _local.fab_id = _fab.id
                LEFT JOIN _site ON _fab.site_id = _site.id
                WHERE _local.flag='On'
                ORDER BY _site.id, _fab.id, _local.id ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $locals = $stmt->fetchAll();
            return $locals;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 設定low_level時用選則區域 for create
    function select_local($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _local.*, _site.site_title, _site.site_remark, _fab.fab_title, _fab.fab_remark, _fab.flag AS fab_flag
                FROM `_local`
                LEFT JOIN _fab ON _local.fab_id = _fab.id
                LEFT JOIN _site ON _fab.site_id = _site.id
                WHERE _local.id=?
                ORDER BY _fab.id, _local.id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$local_id]);
            $local = $stmt->fetch();
            return $local;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 找出自己的資料 
    function show_User($request){
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



    // 分拆--損壞--遺失--更新原紀錄
    function spinOff_stock($request){
        $pdo = pdo();
        extract($request);

        // step1:更新=先扣除原表單數量+註解
        if($spinOff_damage > $damage){
            $amount -= ($spinOff_damage - $damage);
        }else{
            $amount += ($damage - $spinOff_damage);
        }

        if($spinOff_loss > $loss){
            $amount -= ($spinOff_loss - $loss);
        }else{
            $amount += ($loss - $spinOff_loss);
        }

        $sql = "UPDATE stock
                SET amount=?, damage=?, loss=?, d_remark=?, user_id=?, updated_at=now()
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$amount, $spinOff_damage, $spinOff_loss, $spinOff_d_remark, $user_id, $id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }

    }

    // 20220829 儲存PR請購進貨單
    function store_restock($request){
        $pdo = pdo();
        extract($request);
        // item資料前處理
            // $item = $request['item'];                // 陣列指向
                $item = array_filter($item);            // 去除陣列中空白元素
                $amount = array_filter($amount);        // 去除陣列中空白元素
                $lot_num = array_filter($lot_num);      // 去除陣列中空白元素
                $po_num = array_filter($po_num);        // 去除陣列中空白元素=因為很多都沒有資料，過濾會導致錯誤。
            // 小陣列要先編碼才能塞進去大陣列
                $item_enc = json_encode($item);
                $amount_enc = json_encode($amount);
                $lot_num_enc = json_encode($lot_num);
                $po_num_enc = json_encode($po_num);
            //陣列合併
                $item_arr = [];
                $item_arr['lot_num'] = $lot_num_enc;
                $item_arr['po_num'] = $po_num_enc;
                $item_arr['item'] = $item_enc;
                $item_arr['amount'] = $amount_enc;
            // implode()把陣列元素組合為字串：
                $item_str = implode("_," , $item_arr);               // 陣列轉成字串進行儲存到mySQL

            // 設定表單狀態idty=1待領
                $idty = '1';

        //// **** 儲存Trade表單
            $sql = "INSERT INTO trade(out_date, item, out_user_id, out_local, in_local, idty, logs)VALUES(now(),?,?,?,?,?,?)";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$item_str, $out_user_id, $out_local, $in_local, $idty, $logs]);
            }catch(PDOException $e){
                echo $e->getMessage();
            }

        //// **** 預扣功能    
        // StdObject轉換成Array
        if(is_object($item)) { $item = (array)$item; } 
        if(is_object($amount)) { $amount = (array)$amount; } 
        if(is_object($lot_num)) { $lot_num = (array)$lot_num; } 
        if(is_object($po_num)) { $po_num = (array)$po_num; } 
        // 逐筆呼叫處理
        foreach($item as $it){
            $process = [];  // 清空預設值
            $process = array('local_id' => $in_local, 
                             'catalog_id' => $item[$it], 
                             'standard_lv' => '0', 
                             'amount' => $amount[$it], 
                             'remark' => $remark, 
                             'lot_num' => $lot_num[$it], 
                             'po_num' => $po_num[$it], 
                             'd_remark' => $d_remark, 
                             'user_id' => $out_user_id );
            // 後續要加入預扣原本數量功能=呼叫process_trade($request)
            store_stock($process);
        }
    }



