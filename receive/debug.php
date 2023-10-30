<?php
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
        echo "<pre>";
        print_r($_REQUEST);
        echo "</pre>";
        echo '<hr>';