<?php
    require_once("../pdo.php");
    require_once("function.php");
    extract($_REQUEST);

        // 資料判斷 
        if(empty($stock) || empty($amount)){
            echo "<script>alert('參數錯誤_1 !!! (你沒有勾選項 或是 沒填數量)');</script>";
            header("refresh:0;url=create.php");
            return;

        }else{

            // 資料前處理
            $stock = array_filter($stock);                              // 去除陣列中空白元素
                if(is_object($stock)) { $stock = (array)$stock; }       // Obj轉Array
            $amount = array_filter($amount);                            // 去除陣列中空白元素
                if(is_object($amount)) { $amount = (array)$amount; }    // Obj轉Array
                if(is_object($lot_num)) { $lot_num = (array)$lot_num; }    // Obj轉Array
            // 建立新陣列群組
            $n_item = [];
            $n_amount = [];
            $n_lot_num = [];
            $n_po_no = [];
            
            foreach(array_keys($stock) as $st){
                if(empty($amount[$st])){
                    echo "<script>alert('參數錯誤_2 !!! (你的勾選項 沒填數量 或 不完整)');</script>";
                    header("refresh:0;url=create.php");
                    return;
                }
                // 趁機會把有勾選的數值繞出來
                $n_item[$st] = $item[$st];
                $n_amount[$st] = $amount[$st];
                $n_lot_num[$st] = $lot_num[$st];
                $n_po_no[$st] = $po_no[$st];
            }
            // 再把繞出來的陣列倒回去
            $_REQUEST['amount'] = $n_amount;
            $_REQUEST['lot_num'] = $n_lot_num;
            $_REQUEST['po_no'] = $n_po_no;
            $_REQUEST['item'] = $n_item;
            // 倒入簽核紀錄
            $_REQUEST['logs'] = toLog($_REQUEST);

            store_trade($_REQUEST);
            // echo "<script>alert('交易已新增');</script>";
        }
        
        header("refresh:0;url=index.php");







