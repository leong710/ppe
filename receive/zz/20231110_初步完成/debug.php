<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    if(!isset($_SESSION)){                          // 確認session是否啟動
		session_start();
	}

    extract($_REQUEST);

// 把_receive表單叫近來處理
    $query = array('uuid'=> $uuid );
    $receive_row = show_receive($query);            // 調閱原表單
    $query_fab_omager = query_fab_omager($receive_row["fab_sign_code"]);    // 尋找FAB的環安主管。
    $in_sign = $query_fab_omager['OMAGER'];                                 // 由 存換成 NULL ==> 業務負責人/負責人主管


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
        echo '<hr>';
        echo ">> REQUEST： ";
        // echo ">> basic_query_arr： ";
        echo "<pre>";
        print_r($_REQUEST);
        // print_r($query_fab_omager);
        echo "</pre>";
        echo '<hr>';