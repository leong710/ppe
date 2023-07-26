<?php
    // 清空輸出流，防止有別的資訊
    ob_end_clean();
    // 開啟一個輸出流
    ob_start();
    include_once ("../pdo.php"); //連線資料庫 
    require_once("function.php");

    // 參考這裡： https://codertw.com/%E7%A8%8B%E5%BC%8F%E8%AA%9E%E8%A8%80/215402/

    $action = $_GET['action']; 
    if($action == 'export'){ //匯出CSV 
    //匯出處理 All
        $pdo = pdo();
        extract($_REQUEST);
        // 前段-初始查詢語法：全廠+全狀態
        $sql = "SELECT _issue.*,
                    _local_i.local_title as local_i_title, _local_i.remark as local_i_remark,              
                    _fab_i.fab_title as fab_i_title, _fab_i.remark as fab_i_remark, 
                    _site_i.id as site_i_id, _site_i.site_title as site_i_title, _site_i.remark as site_i_remark 
                FROM `_issue`
                LEFT JOIN _local _local_i ON _issue.in_local = _local_i.id
                LEFT JOIN _site _site_i ON _site_i.id = _local_i.site_id 
                LEFT JOIN _fab _fab_i ON  _fab_i.id = _site_i.fab_id ";
        // 設定狀態文字 for filename串接
        // 後段-堆疊查詢語法：需求類別=0臨時
        if($ppty == '0'){
            $sql .= " WHERE ppty =? AND _issue.idty=0";
            $csv_title = "臨時需求";
        }
        // 後段-堆疊查詢語法：需求類別=1定期
        if($ppty == '1'){
            $sql .= " WHERE ppty =? AND _issue.idty=0";
            $csv_title = "定期需求";
        }
        // 後段-堆疊查詢語法：需求類別=All
        if($ppty == 'All'){
            $sql .= " WHERE _issue.idty=0";
            $csv_title = "全部需求";
        }

        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _issue.create_date";
        
            // 讀取所有catalog
            $catalogs = show_catalogs();
                // 20230627-針對品項錯位bug進行調整 -- 取消插入
                // $space = [];                        // 因為陣列是從0開始，必須插入一空白陣列到前面，讓正確資料由1開始
                // array_unshift($catalogs, $space);   // 插到前面

            // 20230627-針對品項錯位bug進行調整 -- 重新建構目錄清單，以id作為object.key
                $obj_catalogs = [];
                foreach($catalogs as $cata){
                    $obj_catalogs[$cata["id"]] = [
                        'id' => $cata["id"],
                        'title' => $cata["title"],
                        'cover' => $cata["cover"],
                        'remark' => $cata["remark"],
                        '_content' => $cata["_content"],
                        '_deploy' => $cata["_deploy"],
                        'buy_a' => $cata["buy_a"],
                        'buy_b' => $cata["buy_b"],
                        'unit' => $cata["unit"],
                        'cname' => $cata["cname"],
                        'cate_title' => $cata["cate_title"],
                        'cate_id' => $cata["cate_id"]
                    ];
                }
                
            $all_item = []; $all_amount = []; $issue_site = []; $issue_ppty_arr = [];

        $stmt = $pdo->prepare($sql);
        // 依據全廠或site來帶入查詢
        try {
            if($ppty == 'All'){
                $stmt->execute();               //匯出處理 byAll
            }else{
                $stmt->execute([$ppty]);     //匯出處理 bySite
            }
            if($stmt -> rowCount() > 0){ 
                // 倒出資料大於0筆 = 有資料
                $issues = $stmt->fetchAll();

                foreach($issues as $row){ 
                    $item_str = $row["item"];                     // 把item整串(未解碼)存到$item_str
                    $item_arr = explode("_," ,$item_str);           // 把字串轉成陣列進行後面的應用
                    $item_dec = json_decode($item_arr[0]);          // 解碼後存到$item_dec
                    $amount_dec = json_decode($item_arr[1]);        // 解碼後存到$amount_dec
                    //PHP stdClass Object轉array 
                    if(is_object($item_dec)) { $item_dec = (array)$item_dec; } 
                    if(is_object($amount_dec)) { $amount_dec = (array)$amount_dec; } 

                    foreach($item_dec as $it){
                        $all_item[$it] = $item_dec[$it];
                        if(empty($all_amount[$it])){
                            $all_amount[$it] = $amount_dec[$it];
                        }else{
                            $all_amount[$it] = $all_amount[$it] + $amount_dec[$it];
                        }
                        if(empty($issue_site[$it])){
                            $issue_site[$it] = $row['site_i_title'].": ".$amount_dec[$it];
                        }else{
                            $issue_site[$it] = $issue_site[$it]." ".$row['site_i_title'].": ".$amount_dec[$it];
                        }
                        if(empty($issue_ppty_arr[$it])){
                            $issue_ppty_arr[$it] = $row['site_i_title'].": ".$amount_dec[$it];
                            if($row["ppty"]==0){$issue_ppty_arr[$it]="臨時";}
                            if($row["ppty"]==1){$issue_ppty_arr[$it]="定期";}
                        }
                    }
                }

                $str = "需求類別,衛材分類,No.,衛材名稱,單位,需求數量,需求細項\n"; 
                $str = iconv('utf-8','BIG5',$str); 
                foreach($all_item as $ai){ 
                    $issue_ppty =   mb_convert_encoding($issue_ppty_arr[$ai],"big5","utf-8");
                    $cate_title =   mb_convert_encoding($obj_catalogs[$ai]['cate_title'],"big5","utf-8");
                    $id =           mb_convert_encoding($obj_catalogs[$ai]['id'],"big5","utf-8"); // utf8 to big5
                    $catalog_title= mb_convert_encoding($obj_catalogs[$ai]['title'],"big5","utf-8");
                    $unit =         mb_convert_encoding($obj_catalogs[$ai]['unit'],"big5","utf-8");
                    $amount =       $all_amount[$ai];
                    $detail_reim =  str_replace(array("\r\n","\r","\n"), " ", $issue_site[$ai]);
                    $detail =       mb_convert_encoding($detail_reim,"big5","utf-8");
                    $str .= $issue_ppty.",".$cate_title.",".$id.",".$catalog_title.",".$unit.",".$amount.",".$detail."\n"; //用引文逗號分開 
                } 
                // 輸出檔案命名
                $filename = 'tn衛材需求總表('.$csv_title.')_'.date('Ymd').'.csv'; //設定檔名 
                export_csv($filename, $str); //匯出 
                
            }else{
                // 倒出資料不大於0筆 = 沒有資料
                echo "<script>alert('查無資料，無法導出CSV!!');</script>";
            }

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    if($action == 'export_review'){ //匯出CSV 
    //匯出處理 export_review
        $pdo = pdo();
        if(isset($_GET["pr_no"])){
            $pr_no = $_GET["pr_no"];
        }else{
            echo "<script>alert('引用參數有誤，請再確認~謝謝!');</script>";
            return;
        }
        // 前段語法
        $sql = "SELECT _issue.*,
                    _local_i.local_title as local_i_title, _local_i.remark as local_i_remark,              
                    _site_i.id as site_i_id, _site_i.site_title as site_i_title, _site_i.remark as site_i_remark 
                FROM `_issue`
                LEFT JOIN _local _local_i ON _issue.in_local = _local_i.id
                LEFT JOIN _site _site_i ON _local_i.site_id = _site_i.id
                WHERE _issue._ship = ? ";

        $csv_title = $pr_no;

            // 讀取所有catalog
            $catalogs = show_catalogs();
                // 20230627-針對品項錯位bug進行調整 -- 取消插入
                // $space = [];                        // 因為陣列是從0開始，必須插入一空白陣列到前面，讓正確資料由1開始
                // array_unshift($catalogs, $space);   // 插到前面
        
            // 20230627-針對品項錯位bug進行調整 -- 重新建構目錄清單，以id作為object.key
                $obj_catalogs = [];
                foreach($catalogs as $cata){
                    $obj_catalogs[$cata["id"]] = [
                        'id' => $cata["id"],
                        'title' => $cata["title"],
                        'cover' => $cata["cover"],
                        'remark' => $cata["remark"],
                        '_content' => $cata["_content"],
                        '_deploy' => $cata["_deploy"],
                        'buy_a' => $cata["buy_a"],
                        'buy_b' => $cata["buy_b"],
                        'unit' => $cata["unit"],
                        'cname' => $cata["cname"],
                        'cate_title' => $cata["cate_title"],
                        'cate_id' => $cata["cate_id"]
                    ];
                }
        
            $all_item = []; $all_amount = []; $issue_site = []; $issue_ppty_arr = [];

        $stmt = $pdo->prepare($sql);
        // 依據全廠或site來帶入查詢
        try {
            $stmt->execute([$pr_no]);     //匯出處理 bypr_no
            if($stmt -> rowCount() > 0){ 
                // 倒出資料大於0筆 = 有資料
                $issues = $stmt->fetchAll();

                foreach($issues as $row){ 
                    $item_str = $row["item"];                     // 把item整串(未解碼)存到$item_str
                    $item_arr = explode("_," ,$item_str);           // 把字串轉成陣列進行後面的應用
                    $item_dec = json_decode($item_arr[0]);          // 解碼後存到$item_dec
                    $amount_dec = json_decode($item_arr[1]);        // 解碼後存到$amount_dec
                    //PHP stdClass Object轉array 
                    if(is_object($item_dec)) { $item_dec = (array)$item_dec; } 
                    if(is_object($amount_dec)) { $amount_dec = (array)$amount_dec; } 

                    foreach($item_dec as $it){
                        $all_item[$it] = $item_dec[$it];
                        if(empty($all_amount[$it])){
                            $all_amount[$it] = $amount_dec[$it];
                        }else{
                            $all_amount[$it] = $all_amount[$it] + $amount_dec[$it];
                        }
                        if(empty($issue_site[$it])){
                            $issue_site[$it] = $row['site_i_title'].": ".$amount_dec[$it];
                        }else{
                            $issue_site[$it] = $issue_site[$it]." ".$row['site_i_title'].": ".$amount_dec[$it];
                        }
                        if(empty($issue_ppty_arr[$it])){
                            $issue_ppty_arr[$it] = $row['site_i_title'].": ".$amount_dec[$it];
                            if($row["ppty"]==0){$issue_ppty_arr[$it]="臨時";}
                            if($row["ppty"]==1){$issue_ppty_arr[$it]="定期";}
                        }
                    }
                }

                $str = "需求類別,衛材分類,No.,衛材名稱,單位,需求數量,需求細項\n"; 
                $str = iconv('utf-8','BIG5',$str); 
                foreach($all_item as $ai){ 
                    $issue_ppty =   mb_convert_encoding($issue_ppty_arr[$ai],"big5","utf-8");
                    $cate_title =   mb_convert_encoding($obj_catalogs[$ai]['cate_title'],"big5","utf-8");
                    $id =           mb_convert_encoding($obj_catalogs[$ai]['id'],"big5","utf-8"); // utf8 to big5
                    $catalog_title= mb_convert_encoding($obj_catalogs[$ai]['title'],"big5","utf-8");
                    $unit =         mb_convert_encoding($obj_catalogs[$ai]['unit'],"big5","utf-8");
                    $amount =       $all_amount[$ai];
                    $detail_reim =  str_replace(array("\r\n","\r","\n"), " ", $issue_site[$ai]);
                    $detail =       mb_convert_encoding($detail_reim,"big5","utf-8");
                    $str .= $issue_ppty.",".$cate_title.",".$id.",".$catalog_title.",".$unit.",".$amount.",".$detail."\n"; //用引文逗號分開 
                } 
                // 輸出檔案命名
                $filename = 'tn衛材需求總表('.$csv_title.')_'.date('Ymd').'.csv'; //設定檔名 
                export_csv($filename, $str); //匯出 
                
            }else{
                // 倒出資料不大於0筆 = 沒有資料
                echo "<script>alert('查無資料，無法導出CSV!!');</script>";

            }

        }catch(PDOException $e){
            echo $e->getMessage();
        }

    }

    header("refresh:1;url=index.php");

    // 釋放資源
    ob_end_flush();
    // flush();
    // ob_end_clean();