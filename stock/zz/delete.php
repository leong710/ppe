<?php
    require_once("../pdo.php");
    require_once("function.php");

    delete_stock($_REQUEST);
    // 把來源讀進來，作為返回的依據
    $from2 = $_REQUEST["from2"];
    switch($from2){
        case "repo":
            $catalog_id = $_REQUEST["catalog_id"];
            header("refresh:0;url=../catalog/repo.php?id=$catalog_id");
            break;
        case "catalog":
            header("refresh:0;url=byCatalog.php");
            break;
        case "index":
            header("refresh:0;url=index.php");
            break;
        default:
            header("refresh:0;url=index.php");
            return;
    }
