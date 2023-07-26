<?php
    require_once("../pdo.php");
    require_once("function.php");

    update_trade($_REQUEST);
    // echo "<script>alert('資訊已更新');</script>";
    header("refresh:0;url=index.php");

 