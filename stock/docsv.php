<?php
ob_end_clean();                 // 清空輸出流，防止有別的資訊
ob_start();                     // 開啟一個輸出流
include_once ("../pdo.php");    //連線資料庫 
require_once("function.php");

    // 20230718 去除標籤、換行符號+轉碼(UTF-8=>BIG5)
    function process_tool($process_arr){
        $temp = [];
        $keys = array_keys((array)$process_arr);        // 取陣列或物件的key值
        
        foreach($keys as $key){
            // 因為textarea會包含html符號，必須用strip_tags移除html標籤
            $temp[$key] = strip_tags($process_arr[$key]);
            // 因為textarea會包含換行符號，必須用str_replace置換/n標籤
            $process_arr[$key] = str_replace(array("\r\n","\r","\n"), "", $temp[$key]);
            // utf8 to big5
            $process_arr[$key] = mb_convert_encoding($process_arr[$key],"big5","utf-8"); 
        }
        return $process_arr;
    }

// 參考文獻： https://codertw.com/%E7%A8%8B%E5%BC%8F%E8%AA%9E%E8%A8%80/215402/

if(!empty($_REQUEST)){          // 確認有帶數值才執行
    $action = (isset($_POST['action'])) ? $_POST['action'] : $_GET['action'];     // 操作功能   
}

$aResult = array();             // 定義結果陣列
if(empty($action)){ $aResult['error'] = '未指定 action function!'; }

if( !isset($aResult['error']) ) {

    $aResult['success'] = 'Run '.$action.' function !';    // 預先定義回傳內容。
    switch($action) {
        // fun-1.CSV匯入處理 **** 20230718 這部分需要調整，因為暫時用不到，所以尚未更新!!
        case 'import':
            //匯入處理 
            $filename = $_FILES['file']['tmp_name']; 
                if(empty($filename)){ 
                    echo "<script>alert('請選擇要匯入的CSV檔案！');</script>";
                    header("refresh:0;url=./index.php");
                    exit; 
                } 
            $handle = fopen($filename, 'r'); 
            // $result = input_csv($handle);   //解析csv 暫時沒用到，因為會有function錯誤，所以暫時mark
            $result = import_csv($handle);   //解析csv
            $len_result = count($result); 
                if($len_result==0){ 
                    echo "<script>alert('CSV沒有任何資料！');</script>";
                    header("refresh:0;url=./index.php");
                    exit; 
                } 

            fclose($handle);                //關閉指標 

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
            
            break;

        // fun-2.CSV匯出處理
        case 'export':
            //匯出處理 All
            $pdo = pdo();
            extract($_REQUEST);
            // 前段-初始查詢語法：全廠+全狀態
            $sql = "SELECT _stk.*, 
                            _l.local_title, _l.local_remark, _f.id AS fab_id, _f.fab_title, _f.fab_remark, _s.id as site_id, _s.site_title, _s.site_remark, 
                            _cata.pname, _cata.cata_remark, _cata.SN, _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark 
                    FROM `_stock` _stk 
                    LEFT JOIN _local _l ON _stk.local_id = _l.id 
                    LEFT JOIN _fab _f ON _l.fab_id = _f.id 
                    LEFT JOIN _site _s ON _f.site_id = _s.id 
                    LEFT JOIN _cata ON _stk.cata_SN = _cata.SN 
                    LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no ";
            // 設定狀態文字 for filename串接
            $state_title = "全部";
            // 後段-堆疊查詢語法：site+全狀態
            if($fab_id != 'All' && $state == '0'){
                $sql .= " WHERE _f.id=? ";
            }
            // 後段-堆疊查詢語法：全廠+損壞遺失 ==> 暫時沒用到 
                // if($site_id == 'All' && $state == '1'){
                //     $sql .= " WHERE damage != 0 OR loss != 0 ";
                //     $state_title = "損壞+遺失";
                // }
            // 後段-堆疊查詢語法：site+損壞遺失 ==> 暫時沒用到 
                // if($site_id != 'All' && $state == '1'){
                //     $sql .= " WHERE _site.id=? AND (damage != 0 OR loss != 0) ";
                //     $state_title = "損壞+遺失";
                // }
            // 後段-堆疊查詢語法：加入排序
            $sql .= " ORDER BY site_id, fab_id, local_id, cate_id ,cata_SN ASC ";
            $stmt = $pdo->prepare($sql);
            // 依據全廠或site來帶入查詢
            try {
                if($fab_id == 'All'){
                    $stmt->execute();                   //匯出處理 byAll
                }else{
                    $stmt->execute([$fab_id]);          //匯出處理 byFab
                }
                if($stmt -> rowCount() > 0){ 
                    // 倒出資料大於0筆 = 有資料
                    $stocks = $stmt->fetchAll();
                    $str = "stock_No,site_fab廠處,local儲存點位置,分類,SN,器材名稱,安全存量,現場存量,備註說明,批號/效期,PO_no,料號,最後更新,最後編輯\n"; 
                    $str = iconv('utf-8','BIG5',$str); 
                    foreach($stocks as $row){ 
                        // 使用 function process_tool 取代
                            // // 因為remark=textarea會包含html符號，必須用strip_tags移除html標籤
                            // $s_remark = strip_tags($row['stock_remark']);
                            // $s_po_no = strip_tags($row['po_no']);
                            // // 因為remark=textarea會包含換行符號，必須用str_replace置換/n標籤
                            // $sn_remark = str_replace(array("\r\n","\r","\n"), "", $s_remark);
                            // $sn_po_no = str_replace(array("\r\n","\r","\n"), "", $s_po_no);
                            // // utf8 to big5
                            // $id =           mb_convert_encoding($row['id'],"big5","utf-8"); 
                            // $site_title =   mb_convert_encoding($row['site_title'],"big5","utf-8");
                            // $fab_title =    mb_convert_encoding($row['fab_title'],"big5","utf-8");
                            // $local_title =  mb_convert_encoding($row['local_title'],"big5","utf-8");
                            // $cate_title =   mb_convert_encoding($row['cate_title'],"big5","utf-8");
                            // $cata_title =   mb_convert_encoding($row['pname'],"big5","utf-8");
                            // $standard_lv =  mb_convert_encoding($row['standard_lv'],"big5","utf-8");
                            // $amount =       mb_convert_encoding($row['amount'],"big5","utf-8");
                            // $remark =       mb_convert_encoding($sn_remark,"big5","utf-8");
                            // $lot_num =      mb_convert_encoding($row['lot_num'],"big5","utf-8");
                            // $po_no =        mb_convert_encoding($sn_po_no,"big5","utf-8");
                            // $pno =          mb_convert_encoding($row['pno'],"big5","utf-8");
                            // $updated_at =   mb_convert_encoding($row['updated_at'],"big5","utf-8");
                            // $cname =        mb_convert_encoding($row['updated_user'],"big5","utf-8");
                        $p_row = process_tool($row);
                        $str .= $p_row["id"].",".$p_row["site_title"]."_".$p_row["fab_title"].",".$p_row["local_title"].",".$p_row["cate_title"].",";
                        $str .= $p_row["cata_SN"].",".$p_row["pname"].",".$p_row["standard_lv"].",".$p_row["amount"].",".$p_row["stock_remark"].",".$p_row["lot_num"].",";
                        $str .= $p_row["po_no"].",".$p_row["pno"].",".$p_row["updated_at"].",".$p_row["updated_user"]."\n";     //用引文逗號分開 
                    } 
                    // 輸出檔案命名選擇器
                    if($fab_id == 'All'){
                        $filename = 'tnPPE存量總表('.$state_title.')_'.date('Ymd').'.csv';                  // 設定檔名 
                    }else{
                        $filename = $row['fab_title'].'_PPE存量總表('.$state_title.')_'.date('Ymd').'.csv';  // 設定檔名 
                    }
                    export_csv($filename, $str);                                                           // 匯出 
                }else{
                    // 倒出資料不大於0筆 = 沒有資料
                    unset($aResult['success']);
                    $aResult['error'] = $action.' - 查無資料，無法導出CSV!!';
                    echo "<script>alert('查無資料，無法導出CSV!!');</script>";
                }
            }catch(PDOException $e){
                echo $e->getMessage();
                unset($aResult['success']);
                $aResult['error'] = $action.' : '.$e->getMessage();
            }
            break;
        default:
            unset($aResult['success']);
            $aResult['error'] = 'Not found function '.$function.'!';
            break;
    }

}

header("refresh:1;url=index.php");

// 釋放資源
ob_end_flush();
// flush();
// ob_end_clean();