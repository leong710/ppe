<?php
    require_once("../pdo.php");
    require_once("function.php");
    extract($_REQUEST);

        // 資料判斷 
        if(empty($local_id) || empty($low_level)){
            echo "<script>alert('參數錯誤_1 !!! (你沒有選Local或填數量)');</script>";
            header("refresh:0;url=low_level.php");
            return;

        }else{

            // // 資料前處理
            // $catalog_SN = array_filter($catalog_SN);                                    // 去除陣列中空白元素
            //     if(is_object($catalog_SN)) { $catalog_SN = (array)$catalog_SN; }        // Obj轉Array
            // $amount = array_filter($amount);                                            // 去除陣列中空白元素
            //     if(is_object($amount)) { $amount = (array)$amount; }                    // Obj轉Array
            // $n_amount = [];                                                             // 建立新陣列群組
            
            // foreach(array_keys($catalog_SN) as $cata_SN){
            //     if(empty($amount[$cata_SN])){
            //         echo "<script>alert('參數錯誤_2 !!! (你的勾選項 沒填數量 或 不完整)');</script>";
            //         header("refresh:0;url=low_level.php");
            //         return;
            //     }
            //     // 趁機會把有勾選的數值繞出來
            //     $n_amount[$cata_SN] = $amount[$cata_SN];
            // }
            // // 再把繞出來的陣列倒回去
            // $_REQUEST['amount'] = $n_amount;
 
            store_lowLevel($_REQUEST);
            // echo "<script>alert('交易已新增');</script>";
        }
        header("refresh:0;url=index.php");







