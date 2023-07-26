<?php
    require_once("../pdo.php");
    require_once("function.php");

    update_issue($_REQUEST);
    header("refresh:0;url=index.php");

 