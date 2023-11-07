<?php 
    require_once("../pdo.php");
    require_once("function.php");

    // api function --- start
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');    // 添加這行，允許跨網域!!
    
    // 確認有帶數值才執行
    $functionname = !empty($_REQUEST['functionname']) ? $_REQUEST['functionname'] : NULL;     // 操作功能

    $aResult = array();         // 定義結果陣列
    if(empty($functionname)){ $aResult['error'] = '未指定function!'; }

    if( !isset($aResult['error']) ) {

        if(!empty($_REQUEST['search'])){      // 確認有帶數值才執行
            $su = [];       // 宣告查詢陣列起始
            $su['search']   = !empty($_REQUEST['search']) ? $_REQUEST['search'] : NULL;                        // 查詢對象id或name
            $su['user']     = !empty($_REQUEST['search']) ? $_REQUEST['search'] : NULL;                        // 查詢對象id或name
            $su['emp_id']   = !empty($_REQUEST['search']) ? $_REQUEST['search'] : NULL;                        // 查詢對象id或name
        }

        if(empty($su['search'])) {
            $aResult['error'] = $functionname.' - 參數NULL !';

        } else {

            $aResult['success'] = 'Run function '.$functionname.'!';    // 預先定義回傳內容。
            switch($functionname) {
                // fun-1.cheng_flag myjob快速切換上架On/下架Off 
                case 'cheng_flag':
                    // 宣告查詢陣列內容
                    $su['table'] = (isset($_POST['table'])) ? $_POST['table'] : $_GET['table'];                                        // user的id
                    $su['id'] = (isset($_POST['id'])) ? $_POST['id'] : $_GET['id'];  
                    $su['flag'] = (isset($_POST['flag'])) ? $_POST['flag'] : $_GET['flag'];

                    if(empty($su['id']) || empty($su['table'])) {
                        $aResult['error'] = $function.' - 參數錯誤!';
                    } else {
                        $cheng_flag = changePlan_flag($su);
                        $aResult['result'] = $cheng_flag;
                    }
                    break;
                case 'searchUser':
                        // 宣告查詢陣列內容
                        $su['key_word'] = (isset($_POST['key_word'])) ? $_POST['key_word'] : $_GET['key_word'];                             // user的key_word
                        if(empty($su['key_word'])) {
                            $aResult['error'] = $function.' - 參數錯誤!';
                        } else {
                            $searchs = searchUser($su);
                            $aResult['result'] = $searchs;
                        }
                        break;
                // step-1.大方向搜尋_msSQL
                case 'search':
                    $aResult['result'] = search($su);
                    break;

                // step-2.聚焦對象查詢_msSQL
                case 'showStaff':
                    $aResult['result'] = showStaff($su);
                    break;
                default:
                    unset($aResult['success']);
                    $aResult['error'] = 'Not found function '.$functionname.'!';
                    break;
            }
        }
    }
    // 參數：JSON_UNESCAPED_UNICODE 中文不編碼
    echo json_encode($aResult , JSON_UNESCAPED_UNICODE );
    // api function --- end
?>