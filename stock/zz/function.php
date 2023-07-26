<?php
    function show_stock($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT stock.*, users.cname, _site.id as site_id, _site.site_title, _site.remark as site_remark, 
                        _fab.id AS fab_id, _fab.fab_title, _fab.remark as fab_remark, 
                        _local.local_title, _local.remark as loccal_remark, _catalog.title as catalog_title, 
                        categories.id AS cate_id, categories.cate_title
                FROM `stock`
                LEFT JOIN users ON stock.user_id = users.id
                LEFT JOIN _local ON stock.local_id = _local.id
                LEFT JOIN _site ON _local.site_id = _site.id
                LEFT JOIN _fab ON _site.fab_id = _fab.id
                LEFT JOIN _catalog ON stock.catalog_id = _catalog.id
                LEFT JOIN categories ON _catalog.category_id = categories.id ";
        if($category_id != "All"){
            $sql .= " WHERE _site.id=? AND categories.id = ? ";
        }else{
            $sql .= " WHERE _site.id=? ";
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY fab_id, local_id, catalog_id, lot_num ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            if($category_id != "All"){
                $stmt->execute([$site_id, $category_id]);
            }else{
                $stmt->execute([$site_id]);
            }
            $stocks = $stmt->fetchAll();
            return $stocks;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    //分頁工具
    function stock_page_div($start,$per,$request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT stock.*, users.cname, _site.id as site_id, _site.site_title, _site.remark as site_remark, 
                        _fab.fab_title, _fab.remark as fab_remark, 
                        _local.local_title, _local.remark as loccal_remark, _catalog.title as catalog_title,
                        categories.id AS cate_id, categories.cate_title
                FROM `stock`
                LEFT JOIN users ON stock.user_id = users.id
                LEFT JOIN _local ON stock.local_id = _local.id
                LEFT JOIN _site ON _local.site_id = _site.id
                LEFT JOIN _fab ON _site.fab_id = _fab.id
                LEFT JOIN _catalog ON stock.catalog_id = _catalog.id
                LEFT JOIN categories ON _catalog.category_id = categories.id ";
        if($category_id != "All"){
            $sql .= " WHERE _site.id=? AND categories.id = ? ";
        }else{
            $sql .= " WHERE _site.id=? ";
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY fab_id, local_id, catalog_id, lot_num ASC ";
        $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料
        try {
            if($category_id != "All"){
                $stmt->execute([$site_id, $category_id]);
            }else{
                $stmt->execute([$site_id]);
            }
            $rs = $stmt->fetchAll();
            return $rs;
        }catch(PDOException $e){
            echo $e->getMessage(); 
        }
    }
    // stock項目--新增 220602
    function store_stock($request){
        $pdo = pdo();
        extract($request);
        // 20220706 新增確認同local_ld+catalog_id+lot_num的單子合併計算
        $sql_check = "SELECT * FROM stock WHERE local_id =? AND catalog_id =? AND lot_num =? AND po_num=?";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$local_id, $catalog_id, $lot_num, $po_num]);

        if($stmt_check -> rowCount() >0){     
            // 確認no編號是否已經被註冊掉，用rowCount最快~不要用fetch
            echo "<script>alert('local同批號衛材已存在，將進行合併計算~')</script>";
            $row = $stmt_check -> fetch();
            $amount += $row["amount"];

            $sql = "UPDATE stock
                    SET standard_lv=?, amount=?, remark=CONCAT(remark, CHAR(10), ?), lot_num=?, po_num=?, d_remark=CONCAT(d_remark, CHAR(10), ?), user_id=?, updated_at=now()
                    WHERE id=?";
            $stmt = $pdo->prepare($sql);
            try{
                $stmt->execute([$standard_lv, $amount, $remark, $lot_num, $po_num, $d_remark, $user_id, $row["id"]]);
            }catch(PDOException $e){
                echo $e->getMessage();
            }

            return;

        }else{
            // echo "<script>alert('local器材只有單一筆，不用合併計算~')</script>";
            $sql = "INSERT INTO stock(local_id, catalog_id, standard_lv, amount, remark, lot_num, po_num, d_remark, user_id, created_at, updated_at)VALUES(?,?,?,?,?,?,?,?,?,now(),now())";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$local_id, $catalog_id, $standard_lv, $amount, $remark, $lot_num, $po_num, $d_remark, $user_id]);
            }catch(PDOException $e){
                echo $e->getMessage();
            }
        }
    }
    function delete_stock($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM stock WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    //from edit.php
    //依ID找出要修改的stock內容
    function edit_stock($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT stock.*
                FROM stock
                WHERE stock.id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $stock = $stmt->fetch();
            return $stock;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    //from editStock call updateStock
    //修改完成的editStock 進行Update
    function update_stock($request){
        $pdo = pdo();
        extract($request);
        $sql = "UPDATE stock
                SET local_id=?, catalog_id=?, standard_lv=?, amount=?, remark=?, lot_num=?, po_num=?, d_remark=?, user_id=?, updated_at=now()
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$local_id, $catalog_id, $standard_lv, $amount, $remark, $lot_num, $po_num, $d_remark, $user_id, $id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    function accessDenied(){
        session_start();
        if(!isset($_SESSION["AUTH"])){
            header('location:../dashBoard/index.php');
            return;
        }
    }
    function accessDeniedAdmin(){
        session_start();
        if(!isset($_SESSION["AUTH"]) || $_SESSION["AUTH"]["role"] != 0){
            header('location:../dashBoard/index.php');
            return;
        }
    }

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
    function show_fab(){
        $pdo = pdo();
        $sql = "SELECT * FROM _fab
                ORDER BY id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $fabs = $stmt->fetchAll();
            return $fabs;
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
    // edit時用全區域~ 但user鎖編輯權限
    function show_allLocal(){
        $pdo = pdo();
        $sql = "SELECT _local.*, _site.site_title, _site.remark as site_remark, _fab.fab_title, _fab.remark as fab_remark
                FROM `_local`
                LEFT JOIN _site ON _local.site_id = _site.id
                LEFT JOIN _fab ON _site.fab_id = _fab.id
                WHERE _local.flag='On'
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

    function show_catalogs(){
        $pdo = pdo();
        $sql = "SELECT * FROM _catalog
                -- WHERE _catalog.flag = 'On'
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

    function show_stock_byCatalog($request){              // 依衛材名稱顯示
        $pdo = pdo();
        extract($request);
        $sql = "SELECT stock.*, users.cname, 
                        _site.site_title, _site.remark as site_remark, _site.id as site_id, 
                        _fab.fab_title, _fab.remark as fab_remark, 
                        _local.local_title, _local.remark as loccal_remark, 
                        _catalog.title as catalog_title, categories.id AS cate_id, categories.cate_title
                FROM `stock`
                LEFT JOIN users ON stock.user_id = users.id
                LEFT JOIN _local ON stock.local_id =  _local.id
                LEFT JOIN _site ON _local.site_id = _site.id
                LEFT JOIN _fab ON _site.fab_id = _fab.id
                LEFT JOIN _catalog ON _catalog.id = stock.catalog_id
                LEFT JOIN categories ON _catalog.category_id = categories.id
                WHERE _site.id=?
                ORDER BY catalog_id, fab_id, local_id, lot_num ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$site_id]);
            $stocks = $stmt->fetchAll();
            return $stocks;
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

    // 匯出CSV
    function export_csv($filename,$data){ 
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Content-Disposition: attachment;filename="' . $filename . '";');
        header('Content-Type: application/csv; charset=UTF-8');
        echo $data; 
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
    // 設定low_level時用選則區域
    function select_local($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _local.*, _site.site_title, _site.remark as site_remark, _site.buy_ty, _fab.fab_title, _fab.remark as fab_remark
                FROM `_local`
                LEFT JOIN _site ON _local.site_id = _site.id
                LEFT JOIN _fab ON _site.fab_id = _fab.id
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

// category dunction
    function showAllCategories(){
        $pdo = pdo();
        $sql = "SELECT * FROM categories ORDER BY id ASC";
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
    function show_sum_category($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT categories.id AS cate_id, categories.cate_title
                        ,(SELECT COUNT(*) 
                        FROM `stock` _s
                        LEFT JOIN _local _l ON _s.local_id = _l.id
                        LEFT JOIN _catalog _c ON _s.catalog_id = _c.id
                        LEFT JOIN categories _cate ON _c.category_id = _cate.id
                        WHERE _l.site_id = _local.site_id AND _c.category_id = categories.id) AS 'stock_count'
                FROM `stock`
                LEFT JOIN _local ON stock.local_id = _local.id
                LEFT JOIN _catalog ON stock.catalog_id = _catalog.id
                LEFT JOIN categories ON _catalog.category_id = categories.id
                WHERE _local.site_id = ?
                GROUP BY categories.id";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$site_id]);
            $sum_category = $stmt->fetchAll();
            return $sum_category;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
