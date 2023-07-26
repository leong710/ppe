<?php
    require_once("../pdo.php");
    require_once("function.php");

    store_stock($_REQUEST);
    extract($_REQUEST);

    // echo "<script>alert('已新增');</script>";
    if(isset($site_id)){
        header("refresh:0;url=index.php?site_id=$site_id");

    }else{
        header("refresh:0;url=index.php");

    }   