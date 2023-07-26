<?php
ob_end_clean();     // 清空輸出流，防止有別的資訊
ob_start();         // 開啟一個輸出流
include_once ("../pdo.php"); //連線資料庫 
require_once("function.php");

$action = $_GET['action']; 

if($action == 'export_xls'){ //匯出XLS 
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
            // $str = "stock_No,fab廠處,site_儲存點位置,衛材分類,衛材名稱,安全存量,現場存量,備註說明,批號/效期,PO_num,其他說明,最後更新,最後編輯\n"; 
            $str = "
                <table>
                    <tr>
                        <th>stock_No</th>
                        <th>fab廠處</th>
                        <th>site_儲存點位置</th>
                        <th>衛材分類</th>
                        <th>衛材名稱</th>
                        <th>安全存量</th>
                        <th>現場存量</th>
                        <th>備註說明</th>
                        <th>批號/效期</th>
                        <th>PO_num</th>
                        <th>其他說明</th>
                        <th>最後更新</th>
                        <th>最後編輯</th>
                    </tr> "; 
            // $str = iconv('utf-8','BIG5',$str); 
            foreach($stocks as $row){ 
                // 因為remark=textarea會包含html符號，必須用strip_tags移除html標籤
                $s_remark = strip_tags($row['remark']);
                $s_d_remark = strip_tags($row['d_remark']);
                // 因為remark=textarea會包含換行符號，必須用str_replace置換/n標籤
                $sn_remark = str_replace(array("\r\n","\r","\n"), "", $s_remark);
                $sn_d_remark = str_replace(array("\r\n","\r","\n"), "", $s_d_remark);
    
                $id =           $row['id'];
                $fab_title =    $row['fab_title'];
                $site_title =   $row['site_title'];
                $local_title =  $row['local_title'];
                $cate_title =   $row['cate_title'];
                $catalog_title= $row['catalog_title'];
                $standard_lv =  $row['standard_lv'];
                $amount =       $row['amount'];
                $remark =       $sn_remark;
                $lot_num =      $row['lot_num'];
                $po_num =       $row['po_num'];
                $d_remark =     $sn_d_remark;
                $updated_at =   $row['updated_at'];
                $cname =        $row['cname'];

                $str .= "
                    <tr>
                        <td>$id</td>
                        <td>$fab_title</td>
                        <td>$site_title _ $local_title</td>
                        <td>$cate_title</td>
                        <td>$catalog_title</td>
                        <td>$standard_lv</td>
                        <td>$amount</td>
                        <td>$remark</td>
                        <td>$lot_num</td>
                        <td>$po_num</td>
                        <td>$d_remark</td>
                        <td>$updated_at</td>
                        <td>$cname</td>
                    </tr> "; 
            } 
            $str .= '</table>';

            // 輸出檔案命名選擇器
            if($site_id == 'All'){
                $filename = 'tn衛材存量總表('.$state_title.')_'.date('Ymd').'.xls'; //設定檔名 
            }else{
                $filename = $row['site_title'].'衛材存量總表('.$state_title.')_'.date('Ymd').'.xls'; //設定檔名 
            }
            header('Content-Type: application/xls');
            header('Content-Disposition: attachment; filename='.$filename );
            echo $str;

        }else{
            // 倒出資料不大於0筆 = 沒有資料
            echo "<script>alert('查無資料，無法導出xls!!');</script>";
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