<?php
    function calculateOrderQuantity($a, $b) {
        $b = ($b < 0 || empty($b)) ? 1 : $b;
        // 設定初始的計算量
        $c = $b; // 最少出貨數量
    
        // 如果需求數量 a 大於或等於 MOQ 並且不是剛好等於 MOQ
        if ($a >= $b) {
            // 計算需要的整批數量
            $multiple = ceil($a / $b); // 向上取整
            $c = $multiple * $b; // 計算出基於 MOQ 的數量
        }
        return $c;
    }


    echo calculateOrderQuantity(10, 0) . "\n";   // 1 (測試 MOQ 為 0 的情況)
    echo calculateOrderQuantity(10, -5) . "\n";  // 1 (測試 MOQ 為負數的情況)