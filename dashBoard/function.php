<?php
    // dashBoard用，秀出全部計數
    function show_count(){
        $pdo = pdo();
        $sql = "SELECT COUNT(*) AS 'l.count' 
                    ,(SELECT COUNT(*) FROM `_site` si WHERE si.flag = 'On') AS 'si.count'
                    ,(SELECT COUNT(*) FROM `_fab` f WHERE f.flag = 'On') AS 'f.count'
                    ,(SELECT COUNT(*) FROM `_catalog` c WHERE c.flag = 'On') AS 'c.count'
                    ,(SELECT COUNT(*) FROM `stock` st ) AS 'st.count'
                FROM `_local` l
                WHERE l.flag = 'On'";
                // DISTINCT 過濾重複
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $Allcount = $stmt->fetch();
            return $Allcount;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // dashBoard-表頭數據用，秀出-site短缺/器材短缺 table-0 左
    function show_stock_db1(){
        $pdo = pdo();
        $sql = "SELECT count(DISTINCT(_fab.id)) AS fab_num, count(DISTINCT(_stock.cata_SN)) AS cata_num 
                FROM `_stock` 
                LEFT JOIN _cata  ON _stock.cata_SN  = _cata.SN	
                LEFT JOIN _local ON _stock.local_id = _local.id	
                LEFT JOIN _fab   ON _local.fab_id   = _fab.id	
                LEFT JOIN (	
                    SELECT concat_ws('_',_local.fab_id, _stock.local_id, _stock.cata_SN) AS tcc
                        , sum(_s.standard_lv) AS _stock_stand	
                        , sum(_stock.amount)-sum(_s.standard_lv) AS sqty
                    FROM `_stock`	
                    LEFT JOIN _local ON _stock.local_id = _local.id		
                    LEFT JOIN (	
                        SELECT _stock.id, _stock.standard_lv	
                        FROM `_stock`	
                        LEFT JOIN _local _l ON _stock.local_id = _l.id	
                        GROUP BY local_id, cata_SN	
                        ) _s ON _stock.id = _s.id
                    GROUP BY _stock.local_id, _stock.cata_SN
                    ) s ON concat_ws('_',_local.fab_id, _stock.local_id, _stock.cata_SN) = tcc
                WHERE s.sqty <= 0 ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $stock_db1 = $stmt->fetch();
            return $stock_db1;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // dashBoard-表頭數據用，秀出-點檢達成率/未完成site數 table-0 右    20240116
    function show_stock_db2($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT COUNT(CASE WHEN checked_log.checked_year = ? AND checked_log.half = ? THEN _fab.id END) AS 'check_num',
                       COUNT(DISTINCT(CASE WHEN _fab.flag = 'On' THEN _fab.id END)) AS 'fab_num',
                       ROUND((COUNT(CASE WHEN checked_log.checked_year = ? AND checked_log.half = ? THEN _fab.id END) / COUNT(DISTINCT _fab.id) * 100), 1) AS 'percentage'
                FROM `_fab`
                LEFT JOIN checked_log ON checked_log.fab_id = _fab.id
                WHERE _fab.flag = 'On' ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$checked_year, $half, $checked_year, $half]);
            $stock_db1 = $stmt->fetch();
            return $stock_db1;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }



    // table-1 驗證用：各廠器材存量百分比(缺點：截長補短的問題) table-1 驗證用
    function show_fab_percentage(){
        $pdo = pdo();
        // 各廠器材存量百分比(找出有缺的部分取最小值)
        $sql = "SELECT _s.*
                FROM (
                    SELECT 	
                        _fab.id AS fab_id, _fab.fab_title
                        , s.stock_stand	
                        , sum(stock.amount) AS stock_amount 	
                        , s.sqty
                        , MIN(round(((s.stock_stand + s.sqty )/(s.stock_stand * 2) * 100), 1)) AS percentage 
                    FROM `_stock` stock
                    LEFT JOIN _cata ON stock.cata_SN = _cata.SN	
                    LEFT JOIN _local ON stock.local_id = _local.id	
                    LEFT JOIN _fab ON _local.fab_id = _fab.id	
                    LEFT JOIN (	
                        SELECT concat_ws('_',_local.fab_id, stock.local_id, stock.cata_SN) AS tcc	
                            , sum(_s.standard_lv) AS stock_stand
                            , sum(stock.amount)-sum(_s.standard_lv) AS sqty	
                        FROM `_stock` stock	
                        LEFT JOIN _local ON stock.local_id = _local.id	
                        LEFT JOIN (	
                                SELECT stock.id, stock.standard_lv	
                                FROM `_stock` stock	
                                LEFT JOIN _local _l ON stock.local_id = _l.id	
                                GROUP BY local_id, cata_SN	
                                ) _s ON stock.id = _s.id	
                        GROUP BY stock.local_id, stock.cata_SN	
                        ) s ON concat_ws('_',_local.fab_id, stock.local_id, stock.cata_SN) = tcc
                    WHERE s.sqty <= 0	
                    GROUP BY stock.local_id, stock.cata_SN
                    ORDER BY _fab.id , percentage ASC) _s
                GROUP BY fab_id ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $stock_percentage = $stmt->fetchAll();
            return $stock_percentage;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // table-2：dashBoard用，秀出全部計數 by器材 table-2     // 20231215
    function show_stock_byCatalog(){
        $pdo = pdo();
        $sql = "SELECT _local.fab_id, stock.local_id
                    , stock.cata_SN, _cata.pname AS cata_pname
                    , sum(s.standard_lv) AS stock_stand
                    , sum(stock.amount) AS stock_amount 
                    , sum(stock.amount)-sum(s.standard_lv) AS qty
                FROM `_stock` stock
                LEFT JOIN _cata ON stock.cata_SN = _cata.SN
                LEFT JOIN _local ON stock.local_id = _local.id
                LEFT JOIN _fab ON _local.fab_id = _fab.id
                LEFT JOIN (
                    SELECT _s.id, _s.standard_lv
                    FROM `_stock` _s
                    LEFT JOIN _local _l ON _s.local_id = _l.id
                    GROUP BY local_id, cata_SN
                ) s ON stock.id = s.id
                GROUP BY _fab.site_id, stock.cata_SN;";
                // DISTINCT 過濾重複
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $stocks = $stmt->fetchAll();
            return $stocks;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // table-3：各廠有缺少的清單 table-3
    function show_stock_lost(){
        $pdo = pdo();
        $sql = "SELECT _local.fab_id, _fab.fab_title, _local.local_title, _cata.SN AS cata_SN, _cata.pname AS cata_pname,
                        s.stock_stand, SUM(stock.amount) AS stock_amount, s.sqty AS qty
                FROM _stock stock
                LEFT JOIN _cata ON stock.cata_SN = _cata.SN
                LEFT JOIN _local ON stock.local_id = _local.id
                LEFT JOIN _fab ON _local.fab_id = _fab.id
                LEFT JOIN (
                    SELECT CONCAT_WS('',_local.fab_id, _stock.local_id, _stock.cata_SN) AS tcc,
                        SUM(_s.standard_lv) AS stock_stand,
                        SUM(_stock.amount)-SUM(_s.standard_lv) AS sqty
                    FROM _stock 
                    LEFT JOIN _local ON _stock.local_id = _local.id
                    LEFT JOIN (
                        SELECT _ss.id, _ss.standard_lv
                        FROM _stock _ss
                        LEFT JOIN _local _l ON _ss.local_id = _l.id
                        GROUP BY local_id, cata_SN
                        ) _s ON _stock.id = _s.id
                    GROUP BY _stock.local_id, _stock.cata_SN
                    ) s ON CONCAT_WS('',_local.fab_id, stock.local_id, stock.cata_SN) = tcc
                WHERE s.sqty <= 0
                GROUP BY _local.fab_id, _local.local_title, _cata.SN, s.stock_stand, s.sqty ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $stock_losts = $stmt->fetchAll();
            return $stock_losts;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }




