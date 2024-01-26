<?php 
    require_once("../pdo.php");
    require_once("api_function.php");

    // api function --- start
    header('Access-Control-Allow-Origin: *');    // 添加這行，允許跨網域!!
    header('Content-Type: application/json');
    
    if(!empty($_REQUEST) && (isset($_REQUEST['function']))){
        $function = $_REQUEST['function'];      // 操作功能
    }

    $aResult = array();         // 定義結果陣列
    if(empty($function)){ $aResult['error'] = '未指定function!'; }

    if( !isset($aResult['error']) ) {

        $su = [];       // 宣告查詢陣列起始
        $aResult['success'] = 'Run function '.$function.'!';    // 預先定義回傳內容。
        switch($function) {
            // fun-1.cheng_flag myjob快速切換上架On/下架Off 
            case 'cheng_flag':
                // 宣告查詢陣列內容
                $su['table'] = (isset($_REQUEST['table'])) ? $_REQUEST['table'] : "";
                $su['id']    = (isset($_REQUEST['id']))    ? $_REQUEST['id']    : "";  
                $su['flag']  = (isset($_REQUEST['flag']))  ? $_REQUEST['flag']  : "";

                if(empty($su['id']) || empty($su['table'])) {
                    $aResult['error'] = $function.' - 參數錯誤!';
                } else {
                    if($su['table'] == "pt_local"){
                        $cheng_flag = changePTLocal_flag($su);
                    }else{
                        $cheng_flag = array(
                            'table' => $su['table'], 
                            'id'    => $su['id'],
                            'flag'  => $su['flag']
                        );
                    }
                    $aResult['result'] = $cheng_flag;
                }
                break;
                
            case 'update_amount':
                // 宣告查詢陣列內容
                if(isset($_REQUEST['_id'])){
                    $su['id'] = $_REQUEST['_id'];}                      // 操作功能

                if(isset($_REQUEST['_amount'])){
                    $su['amount'] = $_REQUEST['_amount'];}              // 操作功能

                if(empty($su['id']) || empty($su['amount'])) {
                    unset($aResult['success']);
                    $aResult['error'] = $function.' - 參數錯誤!';
                    $aResult['result'] = $su;
                } else {
                    $update_amount = update_amount($su);
                    $aResult['result'] = $update_amount;
                }
                break;

            default:
                unset($aResult['success']);
                $aResult['error'] = 'Not found function '.$function.'!';
                break;
        }
        // 尋到物件行為進行記錄
        // toLog($_REQUEST);
    }
    // 參數：JSON_UNESCAPED_UNICODE 中文不編碼
    echo json_encode($aResult , JSON_UNESCAPED_UNICODE );

    // api function --- end
?>