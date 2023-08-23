<?php
// // // catalog = _cata CRUD
    function store_catalog($request){
        $pdo = pdo();
        extract($request);

        $SN = strtoupper(trim($SN));
        // 檢查user是否已經被註冊
            $sql_check = "SELECT * FROM _cata WHERE SN = ?";
            $stmt_check = $pdo -> prepare($sql_check);
            $stmt_check -> execute([$SN]);
            if($stmt_check -> rowCount() >0){     
                // 確認SN編號是否已經被註冊掉，用rowCount最快~不要用fetch
                echo "<script>alert('SN編號{$SN}已存在，請重新選擇SN編號~')</script>";
                return false;
            }

        $PIC_check = substr($PIC,0,7);  // 檢查PIC前端是否含有 'images/' 這7個字
        if($PIC_check == "images/"){
            $PIC = substr($PIC,7);  //取圖片字串7位數以後的文字
        }
        if(isset($scomp_no)){
            $scomp_no = implode(",",$scomp_no);       //scomp_no供應商 是陣列，要儲存前要轉成字串
        }else{
            $scomp_no = "";
        }

        $sql = "INSERT INTO _cata(cate_no, SN, pname, PIC, cata_remark, OBM, model, size, unit, SPEC, part_no, scomp_no, buy_a, buy_b, flag, updated_user, updated_at, created_at)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,now(),now())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$cate_no, $SN, $pname, $PIC, $cata_remark, $OBM, $model, $size, $unit, $SPEC, $part_no, $scomp_no, $buy_a, $buy_b, $flag, $updated_user]);
            return true;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    function edit_catalog($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _cata.*, _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no, _cate.flag AS cate_flag
                FROM _cata 
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no 
                WHERE _cata.SN = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$sn]);
            $catalogr = $stmt->fetch();
            return $catalogr;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    function update_catalog($request){
        $pdo = pdo();
        extract($request);
        
        $SN = strtoupper(trim($SN));
        $PIC_check = substr($PIC,0,7);  // 檢查PIC前端是否含有 'images/' 這7個字
        if($PIC_check == "images/"){
            $PIC = substr($PIC,7);  //取圖片字串7位數以後的文字
        }
        if(isset($scomp_no)){
            $scomp_no = implode(",",$scomp_no);       //scomp_no供應商 是陣列，要儲存前要轉成字串
        }else{
            $scomp_no = "";
        }

        $sql = "UPDATE _cata SET cate_no=?, SN=?, pname=?, PIC=?, cata_remark=?, OBM=?, model=?, size=?, unit=?, SPEC=?, part_no=?, scomp_no=?, buy_a=?, buy_b=?, flag=?, updated_user=?, updated_at=now()
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$cate_no, $SN, $pname, $PIC, $cata_remark, $OBM, $model, $size, $unit, $SPEC, $part_no, $scomp_no, $buy_a, $buy_b, $flag, $updated_user, $id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    function delete_catalog($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM _cata WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // catalog 隱藏或開啟
    function changeCatalog_flag($request){
        $pdo = pdo();
        extract($request);

        $sql_check = "SELECT _cata.* FROM _cata WHERE id=?";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$id]);
        $row = $stmt_check -> fetch();

        if($row['flag'] == "Off" || $row['flag'] == "chk"){
            $flag = "On";
        }else{
            $flag = "Off";
        }

        $sql = "UPDATE _cata SET flag=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$flag, $id]);
            $Result = array(
                // 'table' => $table, 
                'id' => $id,
                'flag' => $flag
            );
            return $Result;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function show_catalogs($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _cata.*, _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no, _cate.flag AS cate_flag
                FROM _cata 
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no ";

        if($cate_no != "All"){
            $sql .= " WHERE _cata.cate_no = ? ";
        }

        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _cata.cate_no, _cata.id ASC";       // <=就是這個造成BUG
        // $sql .= " ORDER BY _cata.id ASC";
        // 20230327 BUG FIX：調撥單選39手套變成36驅血帶，主因是ORDER BY時多加上了_cata.cate_no
        $stmt = $pdo->prepare($sql);
        try {
            if($cate_no != "All"){
                $stmt->execute([$cate_no]);
            }else{
                $stmt->execute();
            }
            $catalogs = $stmt->fetchAll();
            return $catalogs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // catalog 分頁工具
    function page_div_catalogs($start, $per, $request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _cata.*, _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no, _cate.flag AS cate_flag
                FROM _cata 
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no ";

        if($cate_no != "All"){
            $sql .= " WHERE _cata.cate_no = ? ";
        }

        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _cata.cate_no, _cata.id ASC";
        $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料
        try {
            if($cate_no != "All"){
                $stmt->execute([$cate_no]);
            }else{
                $stmt->execute();
            }
            $rs = $stmt->fetchAll();
            return $rs;
        }catch(PDOException $e){
            echo $e->getMessage(); 
        }
    }
    // 秀出單一品項器材庫存量for repo
    function show_catalogStock($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _stk.*, 
                        _l.local_title, _l.local_remark, _f.id AS fab_id, _f.fab_title, _f.fab_remark, _s.id as site_id, _s.site_title, _s.site_remark, 
                        _cata.pname, _cata.cata_remark, _cata.SN, _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark 
                FROM `_stock` _stk 
                LEFT JOIN _local _l ON _stk.local_id = _l.id 
                LEFT JOIN _fab _f ON _l.fab_id = _f.id 
                LEFT JOIN _site _s ON _f.site_id = _s.id 
                LEFT JOIN _cata ON _stk.cata_SN = _cata.SN 
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no 
                WHERE _cata.SN=?
                ORDER BY _s.id, _f.id, local_id, cata_SN ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$sn]);
            $catalogStock = $stmt->fetchAll();
            return $catalogStock;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
// // // catalog = _cata COVER function
// ******** 需要參考圖書館comm的圖片新功能!!! >>> 待更新
    //上傳圖片_create
    function uploadCover($files){
        extract($files);

        $cover_to = "./images/";
        if(!is_dir($cover_to)){
            mkdir($cover_to);
        }

        switch($type){
            case "image/jpeg":
                $ext = ".jpg";
                break;
            case "image/png":
                $ext = ".png";
                break;
            case "image/gif":
                $ext = ".gif";
                break;
            default:
                // echo "<script>alert('請使用正確的圖檔')</script>";
                $msg = "請使用正確的圖檔";
                return $msg;
        }
        $img_name = md5(time()).$ext;
    
        if($error == 0){
            if(move_uploaded_file($tmp_name, $cover_to.$img_name)){
                return $img_name;
            }else{
                echo "上傳失敗_err-{$error}";
            }
        }else{
            echo "上傳錯誤_err-{$error}";
        }
    }
    //上傳圖片_edit
    function uploadCover_edit($files, $request){
        extract($files);

        $cover_to = "./images/";
        if(!is_dir($cover_to)){
            mkdir($cover_to);
        }

        switch($type){
            case "image/jpeg":
                $ext = ".jpg";
                break;
            case "image/png":
                $ext = ".png";
                break;
            case "image/gif":
                $ext = ".gif";
                break;
            default:
                // echo "<script>alert('請使用正確的圖檔')</script>";
                $msg = "請使用正確的圖檔";
                return $msg;
        }
        $img_name = md5(time()).$ext;
  
        extract($request);
        
        if($error == 0){
            if(move_uploaded_file($tmp_name, $cover_to.$img_name)){
                return $img_name;
            }else{
                echo "上傳失敗_err-{$error}";
            }
        }else{
            echo "上傳錯誤_err-{$error}";
        }
    }

// // // Categories = _cate CRUD
    function store_category($request){
        $pdo = pdo();
        extract($request);
        $sql = "INSERT INTO _cate(cate_title, cate_remark, cate_no, flag, updated_user, created_at, updated_at)VALUES(?,?,?,?,?,now(),now())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$cate_title, $cate_remark, $cate_no, $flag, $updated_user]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    function edit_category($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT * FROM _cate WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $cate = $stmt->fetch();
            return $cate;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    function update_category($request){
        $pdo = pdo();
        extract($request);
            // $sql_check = "SELECT * FROM _cate WHERE id=?";
            // $stmt_check = $pdo -> prepare($sql_check);
            // $stmt_check -> execute([$id]);
            // $row = $stmt_check -> fetch();
        $sql = "UPDATE _cate SET cate_title=?, cate_remark=?, cate_no=?, flag=?, updated_user=?, updated_at=now() WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$cate_title, $cate_remark, $cate_no, $flag, $updated_user, $id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    function delete_category($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM _cate WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // category 隱藏或開啟
    function changeCategory_flag($request){
        $pdo = pdo();
        extract($request);

        $sql_check = "SELECT _cate.* FROM _cate WHERE id=?";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$id]);
        $row = $stmt_check -> fetch();

        if($row['flag'] == "Off" || $row['flag'] == "chk"){
            $flag = "On";
        }else{
            $flag = "Off";
        }

        $sql = "UPDATE _cate SET flag=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$flag, $id]);
            $Result = array(
                // 'table' => $table, 
                'id' => $id,
                'flag' => $flag
            );
            return $Result;
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

// // // stock
    // 新增stock項目--新增 for 撥補 form stock function
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
    //修改完成的editStock 進行Update        //from editStock call updateStock
    function update_stock($request){
        $pdo = pdo();
        extract($request);
            // 20230809 新增確認同local_ld+catalog_id+lot_num的單子合併計算
            $sql_check = "SELECT * 
                          FROM _stock 
                          WHERE local_id =? AND cata_SN =? AND lot_num =? AND po_no=? AND id <>? ";
            $stmt_check = $pdo -> prepare($sql_check);
            $stmt_check -> execute([$local_id, $cata_SN, $lot_num, $po_no, $id]);

            if($stmt_check -> rowCount() >0){     
                // 確認no編號是否已經被註冊掉，用rowCount最快~不要用fetch
                echo "<script>alert('local同批號衛材已存在，將進行合併計算~')</script>";
                $row = $stmt_check -> fetch();
                $amount += $row["amount"];
    
                $sql = "UPDATE _stock
                        SET standard_lv=?, amount=?, stock_remark=CONCAT(stock_remark, CHAR(10), ?), po_no=?, lot_num=?, updated_user=?, updated_at=now()
                        WHERE id=?";
                $stmt = $pdo->prepare($sql);
                try{
                    $stmt->execute([$standard_lv, $amount, $stock_remark, $po_no, $lot_num, $updated_user, $row["id"]]);
                    $swal_json = array(
                        "fun" => "update_stock",
                        "action" => "success",
                        "content" => '合併套用成功'
                    );
                    delete_stock($request);     // 已合併到另一儲存項目，故需要刪除舊項目

                }catch(PDOException $e){
                    echo $e->getMessage();
                    $swal_json = array(
                        "fun" => "update_stock",
                        "action" => "error",
                        "content" => '合併套用失敗'
                    );
                }
            }else{
                // echo "<script>alert('local器材只有單一筆，不用合併計算~')</script>";
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
// // // stock  -- end

// // // other function
    // 
    function show_site(){
        $pdo = pdo();
        $sql = "SELECT * FROM _site
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
    // create時用自己區域
    function show_local($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _local.*, _site.site_title, _site.site_remark, _fab.fab_title, _fab.fab_remark
                FROM `_local`
                LEFT JOIN _fab ON _local.fab_id = _fab.id
                LEFT JOIN _site ON _fab.site_id = _site.id
                WHERE _local.flag='On' AND _local.fab_id=?
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
    // 單選自己區域
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
    // 撥補時用全區域
    function show_allLocal(){
        $pdo = pdo();
        $sql = "SELECT _l.*, _s.site_title, _s.site_remark, _f.fab_title, _f.fab_remark
                FROM `_local` _l
                LEFT JOIN _fab _f ON  _l.fab_id = _f.id
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
    // 取得所有Part_no清單
    function show_pno($request){
        $pdo = pdo();
        extract($request);
        // 前段-初始查詢語法：全廠+全狀態
        $sql = "SELECT _pno.*, _cata.SN, _cata.pname, _cata.model, _cata.flag AS cata_flag, _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no
                FROM `_pno`
                LEFT JOIN _cata ON _pno.cata_SN = _cata.SN
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no 
                -- WHERE _pno.flag = 'On'
                ";

        if($_year != 'All'){
            $sql .= " WHERE _pno._year=? ";
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _pno.id DESC ";
        $stmt = $pdo->prepare($sql);
        try {
            if($_year == 'All'){
                $stmt->execute();               //處理 byAll
            }else{
                $stmt->execute([$_year]);      //處理 by_year
            }
            $pnos = $stmt->fetchAll();
            return $pnos;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 取得供應商
    function searchSupp($request){
        $pdo = pdo();
        extract($request);
        $key_word = "%".$key_word."%";      // 採用模糊搜尋
        $sql = "SELECT _supp.*
                FROM _supp
                WHERE ( _supp.comp_no LIKE ? OR _supp.scname LIKE ? OR _supp.sname LIKE ? OR _supp.supp_remark LIKE ? ) 
                ORDER BY _supp.comp_no ASC";
        $stmt = $pdo->prepare($sql);
        try{
            $stmt->execute([$key_word ,$key_word ,$key_word ,$key_word]);
            $searchResult = $stmt->fetchAll();
            return $searchResult;

        }catch(PDOException $e){
            echo $e->getMessage(); 
        }
    }


