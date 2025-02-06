<?php

    $str = "10055241,薛家蓁, 10008048,陳建良";

    $pm_emp_id_arr = explode(",", $str);       //資料表是字串，要炸成陣列

    echo "<pre>";
    print_r($pm_emp_id_arr);
    echo $pm_emp_id_arr[0];
    echo $pm_emp_id_arr[1];
    echo "</pre>";