<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied_sys($sys_id);

    header("refresh:0;url=../");
    exit;