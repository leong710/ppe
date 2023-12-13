<?php 
    require_once("../pdo.php");
    require_once("function.php");

    // api function --- start
    header('Access-Control-Allow-Origin: *');    // 添加這行，允許跨網域!!
    header('Content-Type: application/json');
    
    if(!empty($_REQUEST)){      // 確認有帶數值才執行
        $function = (isset($_POST['function'])) ? $_POST['function'] : $_GET['function'];     // 操作功能   
    }

    $aResult = array();         // 定義結果陣列
    if(empty($function)){ $aResult['error'] = '未指定function!'; }

    if( !isset($aResult['error']) ) {
        $su = [];       // 宣告查詢陣列起始
        $aResult['success'] = 'Run function '.$function.'!';    // 預先定義回傳內容。

        switch($function) {
            // fun-1.storeLog 儲存log = C
            case 'storeLog':
                // 宣告查詢陣列內容
                $su['thisDay']  = (isset($_POST['thisDay'])) ? $_POST['thisDay'] : $_GET['thisDay'];
                $su['sys']      = (isset($_POST['sys']))     ? $_POST['sys']     : $_GET['sys'];
                $su['logs']     = (isset($_POST['logs']))    ? $_POST['logs']    : $_GET['logs'];
                $su['t_stamp']  = (isset($_POST['t_stamp'])) ? $_POST['t_stamp'] : $_GET['t_stamp'];

                if(empty($su['thisDay']) || empty($su['sys']) || empty($su['logs'])) {
                    $aResult['error'] = $function.' - 參數錯誤!';
                } else {
                    $storeLog = storeLog($su);
                    $aResult['result'] = $storeLog;
                }
                break;

            // fun-2.showLog 讀取log = R
            case 'showLog':
                // 宣告查詢陣列內容
                $su['id'] = (isset($_POST['id'])) ? $_POST['id'] : $_GET['id'];

                if(empty($su['id'])) {
                    $aResult['error'] = $function.' - 參數錯誤!';
                } else {
                    $showLog = showLog($su);
                    $aResult['result'] = $showLog;
                }
                break;

            // fun-3.deleteLog 刪除log = D
            case 'deleteLog':
                // 宣告查詢陣列內容
                $su['id'] = (isset($_POST['id'])) ? $_POST['id'] : $_GET['id'];                     // user的id

                if(empty($su['id'])) {
                    $aResult['error'] = $function.' - 參數錯誤!';
                } else {
                    $deleteLog = deleteLog($su);
                    $aResult['result'] = $deleteLog;
                }
                break;

            // fun-4.updateLog 更新log = U
            case 'updateLog':
                // 宣告查詢陣列內容
                $su['id'] = (isset($_POST['id'])) ? $_POST['id'] : $_GET['id'];                     // user的id
                $su['t_stamp'] = (isset($_POST['t_stamp'])) ? $_POST['t_stamp'] : $_GET['t_stamp'];
                $su['sys'] = (isset($_POST['sys'])) ? $_POST['sys'] : $_GET['sys'];         
                $su['remark'] = (isset($_POST['remark'])) ? $_POST['remark'] : $_GET['remark'];

                if(empty($su['id']) || empty($su['sys']) || empty($su['remark'])) {
                    $aResult['error'] = $function.' - 參數錯誤!';
                } else {
                    $updateLog = updateLog($su);
                    $aResult['result'] = $update_log;
                }
                break;
            
            // 無選項反饋
            default:
                unset($aResult['success']);
                $aResult['error'] = 'Not found function '.$function.'!';
                break;
        }
        
    }
    // 參數：JSON_UNESCAPED_UNICODE 中文不編碼
    echo json_encode($aResult , JSON_UNESCAPED_UNICODE );

    // api function --- end
?>