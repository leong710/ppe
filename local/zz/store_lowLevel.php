<?php
    require_once("../pdo.php");
    require_once("function.php");
    extract($_REQUEST);

        // 資料判斷 
        if(empty($catalog_id) || empty($amount)){
            echo "<script>alert('參數錯誤_1 !!! (你沒有勾選項 或是 沒填數量)');</script>";
            header("refresh:0;url=low_level.php");
            return;

        }else{

            // 資料前處理
            $catalog_id = array_filter($catalog_id);                                    // 去除陣列中空白元素
                if(is_object($catalog_id)) { $catalog_id = (array)$catalog_id; }        // Obj轉Array
            $amount = array_filter($amount);                                            // 去除陣列中空白元素
                if(is_object($amount)) { $amount = (array)$amount; }                    // Obj轉Array
            $n_amount = [];                                                             // 建立新陣列群組
            
            foreach(array_keys($catalog_id) as $cata_id){
                if(empty($amount[$cata_id])){
                    echo "<script>alert('參數錯誤_2 !!! (你的勾選項 沒填數量 或 不完整)');</script>";
                    header("refresh:0;url=low_level.php");
                    return;
                }
                // 趁機會把有勾選的數值繞出來
                $n_amount[$cata_id] = $amount[$cata_id];
            }
            // 再把繞出來的陣列倒回去
            $_REQUEST['amount'] = $n_amount;
 
            store_lowLevel($_REQUEST);
            // echo "<script>alert('交易已新增');</script>";
        }
        header("refresh:0;url=index.php");







