<?php 
    require_once("../pdo.php");
    require_once("function.php");

    // api function --- start
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

            // fun-2.store_mytodo 將個人業務預設行程 儲存到myTodo
            case 'store_mytodo':
                // 宣告查詢陣列內容
                $su['emp_id'] = (isset($_POST['emp_id'])) ? $_POST['emp_id'] : $_GET['emp_id'];                     // user的id
                $su['sys_name'] = (isset($_POST['sys_name'])) ? $_POST['sys_name'] : $_GET['sys_name'];         
                $su['table_name'] = (isset($_POST['table_name'])) ? $_POST['table_name'] : $_GET['table_name'];
                $su['table_id'] = (isset($_POST['table_id'])) ? $_POST['table_id'] : $_GET['table_id'];
                $su['pm_task'] = (isset($_POST['pm_task'])) ? $_POST['pm_task'] : $_GET['pm_task'];
                $su['item'] = (isset($_POST['item'])) ? $_POST['item'] : $_GET['item'];
                $su['remark'] = (isset($_POST['remark'])) ? $_POST['remark'] : $_GET['remark'];
                $su['content'] = (isset($_POST['content'])) ? $_POST['content'] : $_GET['content'];
                $su['schedule'] = (isset($_POST['schedule'])) ? $_POST['schedule'] : $_GET['schedule'];
                $su['level'] = (isset($_POST['level'])) ? $_POST['level'] : $_GET['level'];
                $su['flag'] = (isset($_POST['flag'])) ? $_POST['flag'] : $_GET['flag'];
                $su['link'] = (isset($_POST['link'])) ? $_POST['link'] : $_GET['link'];
                $su['thisYear'] = (isset($_POST['thisYear'])) ? $_POST['thisYear'] : $_GET['thisYear'];
                $su['thisMonth'] = (isset($_POST['thisMonth'])) ? $_POST['thisMonth'] : $_GET['thisMonth'];

                if(empty($su['emp_id'])) {
                    $aResult['error'] = $function.' - 參數錯誤!';
                } else {
                    $store_mytodo = store_mytodo($su);
                    $aResult['result'] = $store_mytodo;
                }
                break;

            // fun-3.edit_myTodo 編輯myTodo，起始前讀取該內容
            case 'edit_myTodo':
                // 宣告查詢陣列內容
                $su['id'] = (isset($_POST['id'])) ? $_POST['id'] : $_GET['id'];

                if(empty($su['id'])) {
                    $aResult['error'] = $function.' - 參數錯誤!';
                } else {
                    $edit_myTodo = edit_myTodo($su);
                    $aResult['result'] = $edit_myTodo;
                }
                break;

            // fun-4.cheng_flag 編輯myTodo，快速切換上架On/下架Off
            case 'cheng_flag2':
                // 宣告查詢陣列內容
                $su['id'] = (isset($_POST['id'])) ? $_POST['id'] : $_GET['id'];
                $su['flag'] = (isset($_POST['flag'])) ? $_POST['flag'] : $_GET['flag'];

                if(empty($su['id'])) {
                    $aResult['error'] = $function.' - 參數錯誤!';
                } else {
                    $cheng_flag = change_myTodo_flag($su);
                    $aResult['result'] = $cheng_flag;
                }
                break;
            // fun-5.delete_mytodo 將個人myTodo刪除
            case 'delete_mytodo':
                // 宣告查詢陣列內容
                $su['emp_id'] = (isset($_POST['emp_id'])) ? $_POST['emp_id'] : $_GET['emp_id'];                     // user的id
                $su['sys_name'] = (isset($_POST['sys_name'])) ? $_POST['sys_name'] : $_GET['sys_name'];         
                $su['table_name'] = (isset($_POST['table_name'])) ? $_POST['table_name'] : $_GET['table_name'];
                $su['table_id'] = (isset($_POST['table_id'])) ? $_POST['table_id'] : $_GET['table_id'];

                if(empty($su['emp_id'])) {
                    $aResult['error'] = $function.' - 參數錯誤!';
                } else {
                    $delete_mytodo = delete_mytodo($su);
                    $aResult['result'] = $delete_mytodo;
                }
                break;
            // fun-6.update_mytodo 將個人myTodo更新
            case 'update_mytodo':
                // 宣告查詢陣列內容
                $su['id'] = (isset($_POST['id'])) ? $_POST['id'] : $_GET['id'];                     // user的id
                $su['cname'] = (isset($_POST['cname'])) ? $_POST['cname'] : $_GET['cname'];                     // user的cname
                $su['content'] = (isset($_POST['content'])) ? $_POST['content'] : $_GET['content'];
                $su['content_new'] = (isset($_POST['content_new'])) ? $_POST['content_new'] : $_GET['content_new'];
                $su['flag'] = (isset($_POST['flag'])) ? $_POST['flag'] : $_GET['flag'];

                if(empty($su['id'])) {
                    $aResult['error'] = $function.' - 參數錯誤!';
                } else {
                    $update_mytodo = update_mytodo($su);
                    $aResult['result'] = $update_mytodo;
                }
                break;

            // fun-7.load_user_mytodo_list 將個人myTodo_list讀出來
            case 'load_user_mytodo_list':
                // 宣告查詢陣列內容
                $su['emp_id'] = (isset($_POST['emp_id'])) ? $_POST['emp_id'] : $_GET['emp_id'];                     // user的emp_id
    
                if(empty($su['emp_id'])) {
                    $aResult['error'] = $function.' - 參數錯誤!';
                } else {
                    $user_mytodo_list = load_user_mytodo_list($su);
                    $aResult['result'] = $user_mytodo_list;
                }
                break;
                
            // fun-8.20230411 swap/index tab-3:預覽與查詢
            case 'review_mytodo':
                // 宣告查詢陣列內容
                $su['emp_id'] = (isset($_POST['emp_id'])) ? $_POST['emp_id'] : $_GET['emp_id'];                         // user的emp_id
                $su['reviewYear'] = (isset($_POST['reviewYear'])) ? $_POST['reviewYear'] : $_GET['reviewYear'];         // reviewYear
                $su['reviewMonth'] = (isset($_POST['reviewMonth'])) ? $_POST['reviewMonth'] : $_GET['reviewMonth'];     // reviewMonth
    
                if(empty($su['emp_id']) || empty($su['reviewYear']) || empty($su['reviewMonth'])) {
                    $aResult['error'] = $function.' - 參數錯誤!';
                } else {
                    $review_mytodo = review_mytodo($su);
                    $aResult['result'] = $review_mytodo;
                }
                break;
            // fun-9.20230503 pmView/index tab-3:預覽與查詢
            case 'review_pmtodo':
                // 宣告查詢陣列內容
                $su['pm_id'] = (isset($_POST['pm_id'])) ? $_POST['pm_id'] : $_GET['pm_id'];                             // user的pm_id
                $su['reviewYear'] = (isset($_POST['reviewYear'])) ? $_POST['reviewYear'] : $_GET['reviewYear'];         // reviewYear
                $su['reviewMonth'] = (isset($_POST['reviewMonth'])) ? $_POST['reviewMonth'] : $_GET['reviewMonth'];     // reviewMonth
    
                if(empty($su['pm_id']) || empty($su['reviewYear']) || empty($su['reviewMonth'])) {
                    $aResult['error'] = $function.' - 參數錯誤!';
                } else {
                    $review_pmtodo = review_pmtodo($su);
                    $aResult['result'] = $review_pmtodo;
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
            default:
                unset($aResult['success']);
                $aResult['error'] = 'Not found function '.$function.'!';
                break;
        }
        // 尋到物件行為進行記錄
        // toLog($_REQUEST);
    }
    // echo json_encode($aResult);
    // 參數：JSON_UNESCAPED_UNICODE 中文不編碼
    echo json_encode($aResult , JSON_UNESCAPED_UNICODE );

    // api function --- end
?>