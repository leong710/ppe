<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    if(!isset($_SESSION)){                          // 確認session是否啟動
		session_start();
	}
    // $uuid = "4602736a-779e-11ee-a2d0-2cfda183ef4f";
    // $process_result = process_receive($uuid);

// // // //
    // 1.決定開啟表單的功能：
        if(isset($_REQUEST["fun"]) && $_REQUEST["fun"] != "myReceive"){
            // $fun = $_REQUEST["fun"];                         // 有帶fun，套查詢參數
            $fun = "myFab";                                     // 有帶fun，直接套用 myFab = 3轄區申請單 (管理頁面)
        }else{
            $fun = "myReceive";                                 // 沒帶fun，預設套 myReceive = 2我的申請單 (預設頁面)
        }

    // 2-1.身分選擇功能：定義user進來要看到的項目
        // if($_SESSION[$sys_id]["role"] <= 1 ){                     
        //     if(isset($_REQUEST["emp_id"])){         
        //         $is_emp_id = $_REQUEST["emp_id"];               // pm、管理員 有帶emp_id，套查詢參數
        //     } else{
        //         $is_emp_id = "All";                             // pm、管理員 沒帶emp_id，套用 emp_id = All   => 看全部的申請單
        //     }
        // }else{
        //     $is_emp_id = $_SESSION["AUTH"]["emp_id"];           // 其他人、site_user含2以上 = 套自身emp_id    => 只看關於自己的申請單
        // }
        if(isset($_REQUEST["emp_id"])){         
            $is_emp_id = $_REQUEST["emp_id"];                   // 有帶emp_id，套查詢參數
        } else{
            if($_SESSION[$sys_id]["role"] <= 1 ){                     
                $is_emp_id = "All";                             // 沒帶emp_id，pm、管理員 套用 emp_id = All   => 看全部的申請單
            }else{
                $is_emp_id = $_SESSION["AUTH"]["emp_id"];       // 沒帶emp_id，其他人、site_user含2以上 = 套自身emp_id    => 只看關於自己的申請單
            }
        }
        
    // 2-2.檢視廠區內表單
        if(isset($_REQUEST["fab_id"])){
            $is_fab_id = $_REQUEST["fab_id"];                   // 有帶查詢fab_id，套查詢參數   => 只看要查詢的單一廠
        }else{
            $is_fab_id = "allMy";                               // 其他預設值 = allMy   => 有關於我的轄區廠(fab_id + sfab_is)
        }
    // 3.組合查詢陣列
    $basic_query_arr = array(
        'sys_id'    => $sys_id,
        'role'      => $_SESSION[$sys_id]["role"],
        'sign_code' => $_SESSION["AUTH"]["sign_code"],
        'fab_id'    => $is_fab_id,
        'emp_id'    => $_SESSION["AUTH"]["emp_id"],
        'is_emp_id' => $is_emp_id
    );

    
    $myFab_lists = show_coverFab_lists($basic_query_arr);                   // myCover show_coverFab_lists用$sign_code模糊搜尋

    if(!empty($myFab_lists)){                                               // 當noBody登入，把她所屬的部門代號廠區套入sfab
        foreach($myFab_lists as $myFab){ 
            if(!in_array($myFab["id"], $_SESSION[$sys_id]["sfab_id"])){
                array_push($_SESSION[$sys_id]["sfab_id"], $myFab["id"]);
            }
        }
    }
    
    $fab_id = $_SESSION[$sys_id]["fab_id"];
        if(!in_array($fab_id, $_SESSION[$sys_id]["sfab_id"])){
            array_push($_SESSION[$sys_id]["sfab_id"], $fab_id);
        }

    $sfab_id = $_SESSION[$sys_id]["sfab_id"];
        $sfab_id = implode(",",$sfab_id);               //副pm是陣列，要儲存前要轉成字串

    $basic_query_arr["sfab_id"] = $sfab_id;

// // // // // // //
    $receive_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   // 複製本頁網址藥用
    if(isset($_SERVER["HTTP_REFERER"])){
        $up_href = $_SERVER["HTTP_REFERER"];            // 回上頁
    }else{
        $up_href = $receive_url;                        // 回本頁
    }
    // echo `<button class="btn btn-secondary" onclick="location.href='{$up_href}'"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回上頁</button>`;
    echo "<a href='{$up_href}'>[ 回上頁 ]</a>";
    echo '&nbsp deBug專用：<hr>';
    // deBug專用
        // extract($request);
        echo $_SESSION["AUTH"]["cname"]."<hr>";

        echo ">> sfab_id： ";

        echo "<pre>";
        // print_r($_REQUEST);
        print_r($sfab_id);
        echo "</pre>";
        echo '<hr>';
        echo ">> basic_query_arr： ";
        echo "<pre>";
        // print_r($_REQUEST);
        print_r($basic_query_arr);
        echo "</pre>";
        echo '<hr>';