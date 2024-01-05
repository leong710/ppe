<?php
// // // index +統計數據

    // 20230719 在issue_list秀出所有issue清單
    function show_issue_list($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _issue.*, users_o.cname as cname_o, users_i.cname as cname_i
                       , _local_o.local_title as local_o_title, _local_o.local_remark as local_o_remark
                       , _local_i.local_title as local_i_title, _local_i.local_remark as local_i_remark
                       , _fab_o.id as fab_o_id, _fab_o.fab_title as fab_o_title, _fab_o.fab_remark as fab_o_remark
                       , _fab_i.id as fab_i_id, _fab_i.fab_title as fab_i_title, _fab_i.fab_remark as fab_i_remark
                       , _site_o.id as site_o_id, _site_o.site_title as site_o_title, _site_o.site_remark as site_o_remark
                       , _site_i.id as site_i_id, _site_i.site_title as site_i_title, _site_i.site_remark as site_i_remark
                FROM `_issue`
                LEFT JOIN _users users_o ON _issue.out_user_id = users_o.emp_id
                LEFT JOIN _users users_i ON _issue.in_user_id = users_i.emp_id
                LEFT JOIN _local _local_o ON _issue.out_local = _local_o.id
                LEFT JOIN _local _local_i ON _issue.in_local = _local_i.id
                LEFT JOIN _fab _fab_o ON _local_o.fab_id = _fab_o.id
                LEFT JOIN _fab _fab_i ON _local_i.fab_id = _fab_i.id
                LEFT JOIN _site _site_o ON _fab_o.site_id = _site_o.id
                LEFT JOIN _site _site_i ON _fab_i.site_id = _site_i.id ";
        if($emp_id != 'All'){
            if($_SESSION[$sys_id]["role"] <= 1){
                $sql .= " WHERE _issue.out_user_id=? OR _issue.in_user_id=? OR _issue.idty=1 ";      //處理 byUser
            }else{
                $sql .= " WHERE _issue.out_user_id=? OR _issue.in_user_id=? ";      //處理 byUser
            }
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY create_date DESC";
        // 決定是否採用 page_div 20230803
            if(isset($start) && isset($per)){
                $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料=分頁
            }else{
                $stmt = $pdo->prepare($sql);                // 讀取全部=不分頁
            }
     
        try {
            if($emp_id != 'All'){
                $stmt->execute([$emp_id, $emp_id]);         //處理 by User 
            }else{
                $stmt->execute();                           //處理 by All
            }
            $issues = $stmt->fetchAll();
            return $issues;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // 20230724 在trad_list秀出所有Trad清單 // 20230818 嵌入分頁工具
    function show_trade_list($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _trade.*, users_o.cname as cname_o, users_i.cname as cname_i
                        , _local_o.local_title as local_o_title, _local_o.local_remark as local_o_remark
                        , _local_i.local_title as local_i_title, _local_i.local_remark as local_i_remark
                        , _fab_o.id as fab_o_id, _fab_o.fab_title as fab_o_title, _fab_o.fab_remark as fab_o_remark
                        , _fab_i.id as fab_i_id, _fab_i.fab_title as fab_i_title, _fab_i.fab_remark as fab_i_remark
                        , _site_o.id as site_o_id, _site_o.site_title as site_o_title, _site_o.site_remark as site_o_remark
                        , _site_i.id as site_i_id, _site_i.site_title as site_i_title, _site_i.site_remark as site_i_remark
                FROM `_trade`
                LEFT JOIN _users users_o ON _trade.out_user_id = users_o.emp_id
                LEFT JOIN _users users_i ON _trade.in_user_id = users_i.emp_id 
                LEFT JOIN _local _local_o ON _trade.out_local = _local_o.id
                LEFT JOIN _local _local_i ON _trade.in_local = _local_i.id
                LEFT JOIN _fab _fab_o ON _local_o.fab_id = _fab_o.id
                LEFT JOIN _fab _fab_i ON _local_i.fab_id = _fab_i.id
                LEFT JOIN _site _site_o ON _fab_o.site_id = _site_o.id
                LEFT JOIN _site _site_i ON _fab_i.site_id = _site_i.id
                ";
        if($emp_id != 'All'){
            $sql .= " WHERE _trade.out_user_id=? OR _trade.in_user_id=? OR _fab_o.id=? OR _fab_i.id=? ";      //處理 byUser
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY out_date DESC";
        // 決定是否採用 page_div 20230803
            if(isset($start) && isset($per)){
                $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料=分頁
            }else{
                $stmt = $pdo->prepare($sql);                // 讀取全部=不分頁
            }

        try {
            if($emp_id != 'All'){
                $stmt->execute([$emp_id, $emp_id, $fab_id, $fab_id]);       //處理 byUser
            }else{
                $stmt->execute();               //處理 byAll
            }
            $trades = $stmt->fetchAll();
            return $trades;

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // 20231019 在index表頭顯示我的待簽清單：    // 統計看板--左上：我的待簽清單 / 轄區申請單
        // 參數說明：
        //     1 $fun == 'myReceive'    => 我的申請單
        //     2 $fun == 'inSign'       => 我的待簽清單     不考慮 $fab_id
        //     3 $fun == 'myFab'        => 我的轄區申請單   需搭配 $fab_id
        //          $fab_id == 'All'     就是show全部廠區
        //          $fab_id == 'allMy'   我的所屬轄區 fab & sfab
        //          $fab_id == 'fab_id'  指定單一廠區
        // 
    function show_receive_list($request){
        $pdo = pdo();
        extract($request);

        $sql = "SELECT DISTINCT _r.* , _l.local_title , _l.local_remark , _f.id AS fab_id , _f.fab_title , _f.fab_remark , _f.sign_code AS fab_sign_code , _f.pm_emp_id , _s.site_title , _s.site_remark 
                FROM `_receive` _r
                LEFT JOIN _local _l ON _r.local_id = _l.id
                LEFT JOIN _fab _f ON _l.fab_id = _f.id
                LEFT JOIN _site _s ON _f.site_id = _s.id ";

        if($fun == 'myReceive'){                                            // 處理 $_1我申請單  
            $sql .= " WHERE ? IN (_r.emp_id, _r.created_emp_id) ";

        }else if($fun == 'inSign'){                                         // 處理 $_2我待簽清單  idty = 1申請送出、11發貨後送出、13發貨
            // $sql .= " WHERE (_r.idty IN (1, 11, 13) AND _r.in_sign = ? ) ";
            $sql .= " WHERE (_r.idty IN (1, 11) AND _r.in_sign = ? ) OR (_r.idty = 13 AND FIND_IN_SET({$emp_id}, _f.pm_emp_id)) ";

        }else if($fun == 'myFab'){                                          // 處理 $_3轄區申請單  
            if($fab_id != "All"){                                           // 處理 fab_id != All 進行二階                  
                if($fab_id == "allMy"){                                     // 處理 fab_id = allMy 我的轄區
                    $sql .= " LEFT JOIN _users _u ON _l.fab_id = _u.fab_id OR FIND_IN_SET(_l.fab_id, _u.sfab_id)
                              WHERE (FIND_IN_SET(_l.fab_id, _u.sfab_id) OR (_l.fab_id = _u.fab_id)) AND (_u.emp_id = ?) ";
                }else{                                                      // 處理 fab_id != allMy 就是單點fab_id
                    $sql .= " WHERE _l.fab_id = ? ";
                }
            }                                                               // 處理 fab_id = All 就不用套用，反之進行二階

            if($is_emp_id != "All"){                                        // 處理過濾 is_emp_id != All  
                if($fab_id != "All"){                                       // 處理 fab_id != All 進行二階                  
                    $sql .= " AND ( '{$is_emp_id}' IN (_r.emp_id, _r.created_emp_id)) ";     // 申請單加上查詢對象的is_emp_id
                }else{
                    $sql .= " WHERE ( '{$is_emp_id}' IN (_r.emp_id, _r.created_emp_id)) ";     // 申請單加上查詢對象的is_emp_id
                }
            }

        } else if($fun == 'myCollect'){                                     // 處理 $_5我的待領清單  
                // $sql .= " WHERE ? IN (_r.emp_id, _r.created_emp_id) AND _r.idty = 0 ";
                // $sql .= " WHERE _l.fab_id IN ('{$sfab_id}') AND _r.idty = 0 ";
                // $sql .= " LEFT JOIN _users _u ON _l.fab_id = _u.fab_id OR FIND_IN_SET(_l.fab_id, _u.sfab_id)
                //           WHERE (FIND_IN_SET(_l.fab_id, _u.sfab_id) OR (_l.fab_id = _u.fab_id) OR _l.fab_id IN ({$sfab_id})) AND _u.emp_id = ? AND _r.idty = 12 ";
                // $sql .= " LEFT JOIN _users _u ON _l.fab_id = _u.fab_id OR FIND_IN_SET(_l.fab_id, _u.sfab_id)
                //           WHERE (FIND_IN_SET(_l.fab_id, _u.sfab_id) OR (_l.fab_id = _u.fab_id) OR _l.fab_id IN ({$sfab_id})) AND _r.idty = 12 ";
                $sql .= " LEFT JOIN _users _u ON _l.fab_id = _u.fab_id OR FIND_IN_SET(_l.fab_id, _u.sfab_id)
                          WHERE _l.fab_id IN ({$sfab_id}) AND _r.idty = 12 ";
        }
        
        // 後段-堆疊查詢語法：加入排序
        if($fun == 'myCollect'){
            $sql .= " ORDER BY _l.fab_id ASC, _r.created_at ASC";
        }else{
            $sql .= " ORDER BY _r.created_at DESC";
        }

        // 決定是否採用 page_div 20230803
        if(isset($start) && isset($per)){
            $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per);   // 讀取選取頁的資料=分頁
        }else{
            $stmt = $pdo->prepare($sql);                                // 讀取全部=不分頁
        }
        try {
            if(in_array( $fun , ['inSign', 'myReceive'])){           // 處理 $_2我待簽清單inSign、$_1我申請單myReceive
                $stmt->execute([$emp_id]);

            }else if ($fun == 'myFab' && $fab_id != 'All') {
                if($fab_id == 'allMy'){
                    $stmt->execute([$emp_id]);
                }else{
                    $stmt->execute([$fab_id]);
                }
                // $stmt->execute([$fab_id == 'allMy' ? $emp_id : $fab_id]);
                
            } else {                                                // $_5我的待領清單myCollect 'myCollect'
                $stmt->execute();
            }

            $my_receive_lists = $stmt->fetchAll();
            // if($fun == 'inSign'){  
            //     echo "</br>{$fun}/{$is_emp_id}：".$sql."</br><hr>";
            // }
            return $my_receive_lists;

        }catch(PDOException $e){
            echo $e->getMessage();
            // echo "</br>err:{$fun}/{$is_emp_id}：".$sql."</br><hr>";
        }
    }

// // // index +統計數據 -- end

    // 20231019 在index表頭顯示allFab區域       // 已包在 4 我的轄區中的fab_id中
    function show_allFab_lists(){
        $pdo = pdo();
        $sql = "SELECT _f.*
                FROM _fab AS _f 
                ORDER BY _f.id ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $allFab_lists = $stmt->fetchAll();
            return $allFab_lists;

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20231026 在index表頭顯示my_coverFab區域 = 使用signCode去搜尋
    function show_coverFab_lists($request){
        $pdo = pdo();
        extract($request);

            $sign_code = substr($sign_code, 0, -2);     // 去掉最後兩個字 =>
            $sign_code = "%".$sign_code."%";            // 加上模糊包裝

        $sql = "SELECT _f.*
                FROM _fab AS _f 
                WHERE _f.sign_code LIKE ?
                ORDER BY _f.id ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$sign_code]);
            $coverFab_lists = $stmt->fetchAll();
            // echo "</br>success:{$sign_code}：".$sql."</br><hr>";
            return $coverFab_lists;

        }catch(PDOException $e){
            echo $e->getMessage();
            // echo "</br>err:{$sign_code}：".$sql."</br><hr>";
        }

    }
    // 20231019 在index表頭顯示自己fab區域      // 處理 4我的轄區
    function show_myFab_lists($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _f.id , _f.fab_title , _f.fab_remark , _f.flag , _f.sign_code AS fab_sign_code , _f.pm_emp_id
                FROM _fab AS _f ";
            
        if($fab_id != "All"){
            $sql .= " WHERE _f.id IN ({$sfab_id}) ";
            if($fab_id != "allMy"){
                $sql .= " OR _f.id = ? ";
            }
        }

        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _f.id ASC ";
        $stmt = $pdo->prepare($sql);
                
        try {
            if($fab_id != "All"){
                if($fab_id != "allMy"){
                    $stmt->execute([$fab_id]);
                }else{
                    $stmt->execute();
                }
            }else{
                $stmt->execute();
            }
            $myFab_lists = $stmt->fetchAll();
            return $myFab_lists;

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

