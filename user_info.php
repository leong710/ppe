<?php
    if(!isset($_SESSION)){                                              // 確認session是否啟動
        session_start();
    }
    // 取出$_session引用
    $auth_pass      = isset($_SESSION["AUTH"]["pass"])        ? $_SESSION["AUTH"]["pass"]      : false ;
    $auth_emp_id    = isset($_SESSION["AUTH"]["emp_id"])      ? $_SESSION["AUTH"]["emp_id"]    : false ;
    $auth_user      = isset($_SESSION["AUTH"]["user"])        ? $_SESSION["AUTH"]["user"]      : false ;
    $auth_cname     = isset($_SESSION["AUTH"]["cname"])       ? $_SESSION["AUTH"]["cname"]     : false ;
    $auth_sign_code = isset($_SESSION["AUTH"]["sign_code"])   ? $_SESSION["AUTH"]["sign_code"] : false ;
    $sys_role       = isset($_SESSION[$sys_id]["role"])       ? $_SESSION[$sys_id]["role"]     : false ;
    $sys_auth       = isset($_SESSION[$sys_id])               ? true                           : false ; 
    $sys_fab_id     = isset($_SESSION[$sys_id]["fab_id"])     ? $_SESSION[$sys_id]["fab_id"]   : "1" ;     
    $sys_sfab_id    = isset($_SESSION[$sys_id]["sfab_id"])    ? $_SESSION[$sys_id]["sfab_id"]  : "" ;  

    // 複製本頁網址藥用
    $up_href = (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   // 回上頁 // 回本頁

    
    function pp($parm){
        if(!empty($parm)){
            echo "<span class='text-white'><pre>";
                print_r($parm);
                    // echo "<hr>";
            echo "</pre></span>";
        }
    }