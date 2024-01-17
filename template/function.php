<?php

    // 在NAV找出自己的資料forAlarm 
    function show_myReceive($request){           // 3.查詢領用申請
        $pdo = pdo();
        extract($request);
        // $sql = "SELECT DISTINCT _r.* 
        //                 , _l.local_title , _l.local_remark 
        //                 , _f.id AS fab_id , _f.fab_title , _f.fab_remark , _f.sign_code AS fab_sign_code , _f.pm_emp_id 
        //                 , _s.site_title , _s.site_remark 
        //         FROM `_receive` _r 
        //         LEFT JOIN _local _l ON _r.local_id = _l.id 
        //         LEFT JOIN _fab _f ON _l.fab_id = _f.id 
        //         LEFT JOIN _site _s ON _f.site_id = _s.id 
        //         WHERE (_r.idty IN (1, 11) AND _r.in_sign = ? ) OR (_r.idty = 13 AND FIND_IN_SET( {$emp_id} , _f.pm_emp_id)) 
        //         ORDER BY _r.created_at DESC ";
        $sql = "SELECT DISTINCT _l.fab_id 
                    ,(SELECT COUNT(*)
                        FROM `_receive` 
                        LEFT JOIN _local ON _receive.local_id = _local.id 
                        LEFT JOIN _fab   ON _l.fab_id = _fab.id 
                        WHERE (_r.idty IN (1, 11) AND _r.in_sign = ? ) OR (_r.idty = 13 AND FIND_IN_SET( {$emp_id} , _fab.pm_emp_id)) 
                    ) AS idty_count
                FROM `_receive` _r 
                LEFT JOIN _local _l ON _r.local_id = _l.id 
                ORDER BY _r.created_at DESC  ";
        $stmt = $pdo->prepare($sql);
        try {
            echo "</br>".$sql."</br><hr>";
            $stmt->execute([$emp_id]);
            $myReceive = $stmt->fetchAll();
            return $myReceive;
        }catch(PDOException $e){
            echo $e->getMessage();
        }



    }

    function show_myTrade($request){             // 2.查詢入出庫申請
        $pdo = pdo();
        extract($request);
        $sql = "SELECT DISTINCT i_local.fab_id AS i_sid,
                    (SELECT COUNT(*) FROM `_trade` t2 WHERE t2.in_local = _trade.in_local AND t2.idty = _trade.idty) AS idty_count
                FROM `_trade`
                LEFT JOIN _local i_local ON _trade.in_local = i_local.id
                WHERE _trade.idty = 1 AND i_local.fab_id = ?" ;
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$fab_id]);
            $myTrade = $stmt->fetch();
            return $myTrade;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    function show_myIssue(){                    // 1.查詢請購需求單
        $pdo = pdo();
        $sql = "SELECT COUNT(*) AS idty_count
                FROM `_issue`
                WHERE _issue.idty = 1";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $myIssue = $stmt->fetch();
            return $myIssue;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // fab user index查詢自己的點檢紀錄for顯示點檢表功能
    function sort_check_list($request){         // 庫存點檢
        $pdo = pdo();
        extract($request);
        $sql = "SELECT *
                FROM `checked_log`
                WHERE checked_log.fab_id = ? AND checked_log.checked_year = ? AND checked_log.half = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$fab_id, $checked_year, $half]);
            $check_yh_list = $stmt->fetchAll();
            return $check_yh_list;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }



