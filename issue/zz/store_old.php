<?php
    require_once("../pdo.php");
    require_once("function.php");
    extract($_REQUEST);

        // 資料判斷 
        if(empty($catalog_SN) || empty($amount)){
            echo "<script>alert('參數錯誤_1 !!! (你沒有勾選項 或是 沒填數量)');</script>";
            header("refresh:0;url=create.php");
            return;

        }else{

            // 資料前處理
            $catalog_SN = array_filter($catalog_SN);                                    // 去除陣列中空白元素
                if(is_object($catalog_SN)) { $catalog_SN = (array)$catalog_SN; }        // Obj轉Array
            $amount = array_filter($amount);                                            // 去除陣列中空白元素
                if(is_object($amount)) { $amount = (array)$amount; }                    // Obj轉Array
            // 建立新陣列群組
            $n_amount = [];
            
            foreach(array_keys($catalog_SN) as $c_SN){
                if(empty($amount[$c_SN])){
                    echo "<script>alert('參數錯誤_2 !!! (你的勾選項 沒填數量 或 不完整)');</script>";
                    header("refresh:0;url=create.php");
                    return;
                }
                // 趁機會把有勾選的數值繞出來
                $n_amount[$c_SN] = $amount[$c_SN];
            }

            // 再把繞出來的陣列倒回去
            $_REQUEST['amount'] = $n_amount;
            // 倒入簽核紀錄
            $_REQUEST['logs'] = toLog($_REQUEST);

            store_issue($_REQUEST);
            // echo "<script>alert('交易已新增');</script>";
        }
        
        header("refresh:0;url=index.php");







