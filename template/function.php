<?php

    // 在NAV找出自己的資料forAlarm 
    function show_myReceive($request){           // 3.查詢領用申請
        $pdo = pdo();
        extract($request);
    }

    function show_myTrade($request){             // 2.查詢入出庫申請
        $pdo = pdo();
        extract($request);
        $sql = "SELECT DISTINCT i_local.fab_id AS i_sid,
                    (SELECT COUNT(*) FROM `_trade` t2 WHERE t2.in_local = _trade.in_local AND t2.idty = _trade.idty) AS idty_count
                FROM `_trade`
                LEFT JOIN _local i_local ON _trade.in_local = i_local.id
                WHERE _trade.idty = 1 AND i_local.fab_id = ?";
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



