<?php
    require_once("../pdo.php");
    require_once("function.php");

    store_checked($_REQUEST);
    echo "<script>alert('點檢紀錄已新增');</script>";
    header("refresh:0;url=index.php");