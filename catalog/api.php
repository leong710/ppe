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
            // fun-1.cheng_flag myjob快速切換上架On/下架Off 
            case 'cheng_flag':
                // 宣告查詢陣列內容
                $su['table'] = (isset($_POST['table'])) ? $_POST['table'] : $_GET['table'];
                $su['id'] = (isset($_POST['id'])) ? $_POST['id'] : $_GET['id'];  
                $su['flag'] = (isset($_POST['flag'])) ? $_POST['flag'] : $_GET['flag'];

                if(empty($su['id'])) {
                    $aResult['error'] = $function.' - 參數錯誤!';
                } else {
                    if($su['table'] == "catalog"){
                        $cheng_flag = changeCatalog_flag($su);
                    }else if($su['table'] == "cate"){
                        $cheng_flag = changeCategory_flag($su);
                    }else{
                        $cheng_flag = array(
                            'table' => $su['table'], 
                            'id' => $su['id'],
                            'flag' => $su['flag']
                        );
                    }
                    $aResult['result'] = $cheng_flag;
                }
                break;
            case 'searchSupp':
                    // 宣告查詢陣列內容
                    $su['key_word'] = (isset($_POST['key_word'])) ? $_POST['key_word'] : $_GET['key_word'];                             // user的key_word
                    if(empty($su['key_word'])) {
                        $aResult['error'] = $function.' - 參數錯誤!';
                    } else {
                        $searchs = searchSupp($su);
                        $aResult['result'] = $searchs;
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