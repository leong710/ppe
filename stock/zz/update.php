<?php
    require_once("../pdo.php");
    require_once("function.php");

    update_stock($_REQUEST);

    // 把來源讀進來，作為返回的依據
    $from2 = $_REQUEST["from2"];

    // 考慮用switch來符合
    switch($from2){
        case "repo":
            $catalog_id = $_REQUEST["catalog_id"];
            header("refresh:0;url=../catalog/repo.php?id=$catalog_id");
            break;
        case "index":
            header("refresh:0;url=index.php");
            break;
        default:
            header("refresh:0;url=index.php");
        return;
    }