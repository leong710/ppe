<?php

    session_start();
    session_destroy();

    echo "<script>alert('已登出');</script>";
    header("refresh:0;url=../dashBoard/index.php");
