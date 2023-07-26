<?php
ob_end_clean();     // 清空輸出流，防止有別的資訊
ob_start();         // 開啟一個輸出流
include_once ("../pdo.php"); //連線資料庫 
require_once("function.php");

// 參考這裡： https://codertw.com/%E7%A8%8B%E5%BC%8F%E8%AA%9E%E8%A8%80/215402/

$action = $_GET['action']; 
if ($action == 'import'){ //匯入CSV 
//匯入處理 
    $filename = $_FILES['file']['tmp_name']; 
    if(empty($filename)){ 
        // echo '請選擇要匯入的CSV檔案！'; 
        echo "<script>alert('請選擇要匯入的CSV檔案！');</script>";
        header("refresh:0;url=../log/index.php");
        exit; 
    } 
    $handle = fopen($filename, 'r'); 
    $result = input_csv($handle); //解析csv 
    $len_result = count($result); 
    if($len_result==0){ 
        // echo '沒有任何資料！'; 
        echo "<script>alert('CSV沒有任何資料！');</script>";
        header("refresh:0;url=../log/index.php");
        exit; 
    } 

    fclose($handle); //關閉指標 

    $pdo = pdo();
    $sql = "INSERT INTO log_record(skill, uuid, time_st, time_use)VALUES(?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    try {

        for($i = 1; $i < $len_result; $i++ ){ //迴圈獲取各欄位值 
            $skill = mb_convert_encoding($result[$i][0],"utf-8","auto"); // auto to utf8
            $uuid = mb_convert_encoding($result[$i][1],"utf-8","auto"); 
            $time_st = mb_convert_encoding($result[$i][2],"utf-8","auto"); 
            $time_use = mb_convert_encoding($result[$i][3],"utf-8","auto"); 
            $stmt->execute([$skill, $uuid, $time_st, $time_use]);
        } 
  
    }catch(PDOException $e){
        echo $e->getMessage();
    }

}else if($action == 'export'){ //匯出CSV 
//匯出處理 All
    $pdo = pdo();
    extract($_REQUEST);
    // 前段-初始查詢語法：全廠+全狀態
    $sql = "SELECT stock.*, users.cname
                , _fab.fab_title, _fab.remark as fab_remark
                , _site.id as site_id, _site.site_title, _site.remark as site_remark
                , _local.local_title, _local.remark as loccal_remark
                , categories.cate_title
                , _catalog.title as catalog_title
            FROM `stock`
            LEFT JOIN _catalog ON stock.catalog_id = _catalog.id
            LEFT JOIN categories ON _catalog.category_id = categories.id
            LEFT JOIN users ON stock.user_id = users.id
            LEFT JOIN _local ON stock.local_id = _local.id
            LEFT JOIN _site ON _local.site_id = _site.id
            LEFT JOIN _fab ON _site.fab_id = _fab.id ";
    // 設定狀態文字 for filename串接
    $state_title = "全部";
    // 後段-堆疊查詢語法：全廠+損壞遺失
    if($site_id == 'All' && $state == '1'){
        $sql .= " WHERE damage != 0 OR loss != 0 ";
        $state_title = "損壞+遺失";
    }
    // 後段-堆疊查詢語法：site+全狀態
    if($site_id != 'All' && $state == '0'){
        $sql .= " WHERE _site.id=? ";
    }
    // 後段-堆疊查詢語法：site+損壞遺失
    if($site_id != 'All' && $state == '1'){
        $sql .= " WHERE _site.id=? AND (damage != 0 OR loss != 0) ";
        $state_title = "損壞+遺失";
    }
    // 後段-堆疊查詢語法：加入排序
    $sql .= " ORDER BY fab_id, site_id, local_id, category_id ,catalog_id ASC ";
    $stmt = $pdo->prepare($sql);
    // 依據全廠或site來帶入查詢
    try {
        if($site_id == 'All'){
            $stmt->execute();               //匯出處理 byAll
        }else{
            $stmt->execute([$site_id]);     //匯出處理 bySite
        }
        if($stmt -> rowCount() > 0){ 
            // 倒出資料大於0筆 = 有資料
            $stocks = $stmt->fetchAll();
            $str = "stock_No,fab廠處,site_儲存點位置,衛材分類,衛材名稱,安全存量,現場存量,備註說明,批號/效期,PO_num,其他說明,最後更新,最後編輯\n"; 
            $str = iconv('utf-8','BIG5',$str); 
            foreach($stocks as $row){ 
                // 因為remark=textarea會包含html符號，必須用strip_tags移除html標籤
                $s_remark = strip_tags($row['remark']);
                $s_d_remark = strip_tags($row['d_remark']);
                // 因為remark=textarea會包含換行符號，必須用str_replace置換/n標籤
                $sn_remark = str_replace(array("\r\n","\r","\n"), "", $s_remark);
                $sn_d_remark = str_replace(array("\r\n","\r","\n"), "", $s_d_remark);
    
                $id =           mb_convert_encoding($row['id'],"big5","utf-8"); // utf8 to big5
                $fab_title =    mb_convert_encoding($row['fab_title'],"big5","utf-8");
                $site_title =   mb_convert_encoding($row['site_title'],"big5","utf-8");
                $local_title =  mb_convert_encoding($row['local_title'],"big5","utf-8");
                $cate_title =   mb_convert_encoding($row['cate_title'],"big5","utf-8");
                $catalog_title= mb_convert_encoding($row['catalog_title'],"big5","utf-8");
                $standard_lv =  mb_convert_encoding($row['standard_lv'],"big5","utf-8");
                $amount =       mb_convert_encoding($row['amount'],"big5","utf-8");
                $remark =       mb_convert_encoding($sn_remark,"big5","utf-8");
                $lot_num =      mb_convert_encoding($row['lot_num'],"big5","utf-8");
                $po_num =       mb_convert_encoding($row['po_num'],"big5","utf-8");
                $d_remark =     mb_convert_encoding($sn_d_remark,"big5","utf-8");
                $updated_at =   mb_convert_encoding($row['updated_at'],"big5","utf-8");
                $cname =        mb_convert_encoding($row['cname'],"big5","utf-8");
                $str .= $id.",".$fab_title.",".$site_title."_".$local_title.",".$cate_title.",".$catalog_title.",".$standard_lv.",".$amount.",".$remark.",".$lot_num.",".$po_num.",".$d_remark.",".$updated_at.",".$cname."\n"; //用引文逗號分開 
            } 
            // 輸出檔案命名選擇器
            if($site_id == 'All'){
                $filename = 'tn衛材存量總表('.$state_title.')_'.date('Ymd').'.csv'; //設定檔名 
            }else{
                $filename = $row['site_title'].'衛材存量總表('.$state_title.')_'.date('Ymd').'.csv'; //設定檔名 
            }
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