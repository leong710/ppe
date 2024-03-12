<?php 
    // 20240306_優化結構
    $aResult = array();  // 定義結果陣列

    // 加上安全性檢查，檢查請求的方法是否是 POST、驗證資料等
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        require_once("../pdo.php");
        require_once("function.php");

        // step-1.確認基本數值
            $functionname = !empty($_REQUEST['functionname']) ? $_REQUEST['functionname'] : NULL;     // 操作功能

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
                "search"   => !empty($_REQUEST['search'])   ? $_REQUEST['search']   : NULL,    // 查詢對象id或name
                "emp_id"   => !empty($_REQUEST['emp_id'])   ? $_REQUEST['emp_id']   : NULL,    // 查詢對象id或name
                "user"     => !empty($_REQUEST['user'])     ? $_REQUEST['user']     : NULL,    // 查詢對象id或name

                "table"    => !empty($_REQUEST['table'])    ? $_REQUEST['table']    : NULL,    // cheng_flag
                "id"       => !empty($_REQUEST['id'])       ? $_REQUEST['id']       : NULL,    // cheng_flag
                "flag"     => !empty($_REQUEST['flag'])     ? $_REQUEST['flag']     : NULL,    // cheng_flag

                "key_word" => !empty($_REQUEST['key_word']) ? $_REQUEST['key_word'] : NULL,    // searchUser
            );

        // step-3.確認基本數值具備且無誤 => 執行function
        if( !isset($aResult['error']) ) {
            $aResult['success'] = 'Run function '.$functionname.'!';    // 預先定義回傳內容。
            
            switch($functionname) {
                // fun-1.cheng_flag myjob快速切換上架On/下架Off 
                case 'cheng_flag':
                    // 宣告查詢陣列內容
                    if(empty($su['table']) || empty($su['id']) || empty($su['flag'])) {
                        $aResult['error'] = $functionname.' - 參數錯誤!';
                    } else {
                        $aResult['result'] = changePlan_flag($su);
                    }
                    break;

                case 'searchUser': // key_word
                    if(empty($su['key_word'])) {
                        $aResult['error'] = $functionname.' - 參數錯誤!';
                    } else {
                        $aResult['result'] = searchUser($su);
                    }
                    break;

                // step-1.大方向搜尋_msSQL
                case 'search': // search
                    if(empty($su['search'])) {
                        $aResult['error'] = $functionname.' - 參數錯誤!';
                    } else {
                        $aResult['result'] = search($su);
                    }
                    break;

                // step-2.聚焦對象查詢_msSQL
                case 'showStaff': // emp_id
                    if(empty($su['emp_id'])) {
                        $aResult['error'] = $functionname.' - 參數錯誤!';
                    } else {
                        $aResult['result'] = showStaff($su);
                    }
                    break;
                    
                // 無選項反饋
                default:
                    $aResult['error'] = 'Not found function '.$functionname.'!';
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
