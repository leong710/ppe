<?php 
    // 20240306_優化結構
    $aResult = array();  // 定義結果陣列

    // 加上安全性檢查，檢查請求的方法是否是 POST、驗證資料等
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        require_once("../pdo.php");
        require_once("function.php");

        // step-1.確認基本數值
            $function = !empty($_REQUEST['function']) ? $_REQUEST['function'] : NULL;     // 操作功能
            if(empty($function)){
                if(!empty($aResult['error'])){ 
                    $aResult['error'] .= ' 未指定function!'; 
                }else{
                    $aResult['error'] = ' 未指定function!'; 
                }
            }

        // step-2.組合查詢參數陣列
            // 接收來自前端的資料
            $su = array (
                "thisDay"   => !empty($_REQUEST["thisDay"])   ? $_REQUEST["thisDay"]   : NULL,    // 日期
                "sys"       => !empty($_REQUEST["sys"])       ? $_REQUEST["sys"]       : NULL,    // 系統id  
                "logs"      => !empty($_REQUEST["logs"])      ? $_REQUEST["logs"]      : NULL,    // 訊息內容
                "t_stamp"   => !empty($_REQUEST["t_stamp"])   ? $_REQUEST["t_stamp"]   : NULL,    // 時間戳  
                "remark"    => !empty($_REQUEST["remark"])    ? $_REQUEST["remark"]    : NULL,    // 工號    
                "id"        => !empty($_REQUEST["id"])        ? $_REQUEST["id"]        : NULL,    // log's id

                "user"      => !empty($_REQUEST["user"])      ? $_REQUEST["user"]      : NULL,    // 帳號    
                "cname"     => !empty($_REQUEST["cname"])     ? $_REQUEST["cname"]     : NULL,    // 姓名    
                "sign_code" => !empty($_REQUEST["sign_code"]) ? $_REQUEST["sign_code"] : NULL,    // 部門代號
                "ip_add"    => !empty($_REQUEST["ip_add"])    ? $_REQUEST["ip_add"]    : NULL,    // IP位址  
            );
            
        // step-3.確認基本數值具備且無誤 => 執行function
            if( !isset($aResult['error']) ) {
                $aResult['success'] = 'Run function '.$function.'!';    // 預先定義回傳內容。
        
                switch($function) {
                    // fun-1.storeLog 儲存log = C
                    case 'storeLog':
                        if(empty($su['thisDay']) || empty($su['sys']) || empty($su['logs'])) {
                            $aResult['error'] = $function.' - 參數錯誤!';
                        } else {
                            $aResult['result'] = storeLog($su);
                        }
                        break;
        
                    // fun-2.showLog 讀取log = R
                    case 'showLog':
                        if(empty($su['id'])) {
                            $aResult['error'] = $function.' - 參數錯誤!';
                        } else {
                            $aResult['result'] = showLog($su);
                        }
                        break;
        
                    // fun-3.deleteLog 刪除log = D
                    case 'deleteLog':
                        if(empty($su['id'])) {
                            $aResult['error'] = $function.' - 參數錯誤!';
                        } else {
                            $aResult['result'] = deleteLog($su);
                        }
                        break;
        
                    // fun-4.updateLog 更新log = U
                    case 'updateLog':
                        if(empty($su['id']) || empty($su['sys']) || empty($su['remark'])) {
                            $aResult['error'] = $function.' - 參數錯誤!';
                        } else {
                            $aResult['result'] = updateLog($su);
                        }
                        break;
                    
                    // 無選項反饋
                    default:
                        $aResult['error'] = 'Not found function '.$function.'!';
                        break;
                }
            }
            if( isset($aResult['error']) ) { unset($aResult['success']); }
                
        // api function --- start
        header("Access-Control-Allow-Origin: *");   // 允許跨網域!!
        header("Content-Type: application/json");

    } else {
        // 如果不是 POST 請求，返回錯誤訊息
        $aResult['error'] = 'Method Not Allowed'; 
        header('HTTP/1.1 405 Method Not Allowed');
    }
    // 將回傳的結果返回給前端
    // 參數：JSON_UNESCAPED_UNICODE 中文不編碼
    echo json_encode($aResult , JSON_UNESCAPED_UNICODE );
    // api function --- end
