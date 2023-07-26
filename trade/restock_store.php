<?php
    require_once("../pdo.php");
    require_once("function.php");
    extract($_REQUEST);

        // 資料判斷 
        if(empty($item) || empty($amount)){
            echo "<script>alert('參數錯誤_1 !!! (你沒有勾選項 或是 沒填數量)');</script>";
            header("refresh:0;url=restock.php");
            return;

        }else{
            // 資料前處理
            $item = array_filter($item);                              // 去除陣列中空白元素
                if(is_object($item)) { $item = (array)$stock; }       // Obj轉Array
            $amount = array_filter($amount);                            // 去除陣列中空白元素
                if(is_object($amount)) { $amount = (array)$amount; }    // Obj轉Array
            // 建立新陣列群組 n_
            $n_amount = [];
            $n_lot_num = [];
            $n_po_num = [];

            if(empty($out_local)){
                $out_local = $po_no;
            }

            foreach($item as $it){
                if(empty($amount[$it])){
                    echo "<script>alert('參數錯誤_2 !!! (你的勾選項 沒填數量 或 不完整)');</script>";
                    header("refresh:0;url=restock.php");
                    return;
                }
                if(empty($lot_num[$it])){
                    echo "<script>alert('參數錯誤_3 !!! (你的選項沒填 批號/期限)');</script>";
                    header("refresh:0;url=restock.php");
                    return;
                }
                // 趁機會把有勾選的數值繞出來
                $n_amount[$it] = $amount[$it];
                $n_lot_num[$it] = $lot_num[$it];
                $n_po_no[$it] = $po_no;
            }
            // 再把繞出來的陣列倒回去
            $_REQUEST['amount'] = $n_amount;
            $_REQUEST['lot_num'] = $n_lot_num;
            $_REQUEST['po_no'] = $n_po_no;
            $_REQUEST['out_local'] = $out_local;
            // 倒入簽核紀錄
            $_REQUEST['logs'] = toLog($_REQUEST);
            
            restock_store($_REQUEST);
        }
        header("refresh:0;url=index.php");







