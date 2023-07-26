<?php 
    require_once("function.php");
    extract($_REQUEST);
    $img_name = uploadCover($_FILES["img"]);
    header("refresh:0;url=edit.php?id=$id&img=$img_name");