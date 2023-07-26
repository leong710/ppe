<?php

// PNO
    // pno料號項目--新增 230703
    function store_pno($request){
        $pdo = pdo();
        extract($request);
        $sql = "INSERT INTO _pno(part_no, pno_remark, _year, cata_SN, size, flag, updated_user, created_at, updated_at)VALUES(?,?,?,?,?,?,?,now(),now())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$part_no, $pno_remark, $_year, $cata_SN, $size, $flag, $updated_user]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // from edit_pno.php 依ID找出要修改的pno內容
    function edit_pno($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT * FROM _pno WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $pno = $stmt->fetch();
            return $pno;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    //from edit_pno.php call update_pno 修改完成的edit_pno 進行Update
    function update_pno($request){
        $pdo = pdo();
        extract($request);
        $sql = "UPDATE _pno
                SET part_no=?, pno_remark=?, _year=?, cata_SN=?, size=?, flag=?, updated_user=?, updated_at=now()
                WHERE id=? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$part_no, $pno_remark, $_year, $cata_SN, $size, $flag, $updated_user, $id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function delete_pno($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM _pno WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 隱藏或開啟
    function changePno_flag($request){
        $pdo = pdo();
        extract($request);

        $sql_check = "SELECT _pno.* FROM _pno WHERE id=?";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$id]);
        $row = $stmt_check -> fetch();

        if($row['flag'] == "Off" || $row['flag'] == "chk"){
            $flag = "On";
        }else{
            $flag = "Off";
        }

        $sql = "UPDATE _pno SET flag=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$flag, $id]);
            $Result = array(
                'table' => $table, 
                'id' => $id,
                'flag' => $flag
            );
            return $Result;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function show_pno($request){
        $pdo = pdo();
        extract($request);
        // 前段-初始查詢語法：全廠+全狀態
        $sql = "SELECT _pno.*, _cata.SN, _cata.pname, _cata.model, _cata.flag AS cata_flag, _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no
                FROM `_pno`
                LEFT JOIN _cata ON _pno.cata_SN = _cata.SN
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no 
                -- WHERE _pno.flag = 'On'
                ";

        if($_year != 'All'){
            $sql .= " WHERE _pno._year=? ";
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _pno.id DESC ";
        $stmt = $pdo->prepare($sql);
        try {
            if($_year == 'All'){
                $stmt->execute();               //處理 byAll
            }else{
                $stmt->execute([$_year]);      //處理 by_year
            }
            $pnos = $stmt->fetchAll();
            return $pnos;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 分頁工具
    function page_div_pno($start, $per, $request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT _pno.*, _cata.SN, _cata.pname, _cata.model, _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no
        FROM `_pno`
        LEFT JOIN _cata ON _pno.cata_SN = _cata.SN
        LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no 
                -- WHERE _pno.flag = 'On'
                ";
        if($_year != 'All'){
            $sql .= " WHERE _pno._year=? ";
        }
        // 後段-堆疊查詢語法：加入排序
        $sql .= " ORDER BY _pno.id DESC ";
        $stmt = $pdo -> prepare($sql.' LIMIT '.$start.', '.$per); //讀取選取頁的資料
        try {
            if($_year -= 'All'){
                $stmt->execute();
            }else{
                $stmt->execute([$_year]);
            }
            $rs = $stmt->fetchAll();
            return $rs;
        }catch(PDOException $e){
            echo $e->getMessage(); 
        }
    }
// PNO

    // 秀出catalog全部 20230707 for 新增Part_NO料號
    function show_catalogs(){
        $pdo = pdo();
        $sql = "SELECT _cata.*, _cate.id AS cate_id, _cate.cate_title, _cate.cate_remark, _cate.cate_no, _cate.flag AS cate_flag
                FROM _cata 
                LEFT JOIN _cate ON _cata.cate_no = _cate.cate_no 
                ORDER BY _cate.id, _cata.id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $catalogs = $stmt->fetchAll();
            return $catalogs;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // 取出PNO年份清單 => 供Part_NO料號頁面篩選
    function show_PNO_GB_year(){
        $pdo = pdo();
        $sql = "SELECT _year
                FROM `_pno`
                GROUP BY _year
                ORDER BY _year DESC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $checked_years = $stmt->fetchAll();
            return $checked_years;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
