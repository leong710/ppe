<?php
// // // _formplan CRUD
    function store_formplan($request){
        $pdo = pdo();
        extract($request);
        $sql = "INSERT INTO _formplan(_type, remark, flag, start_time, end_time, _inplan, updated_user, updated_at, created_at)
                VALUES(?, ?, ?, STR_TO_DATE(DATE_FORMAT( ?, '%Y-%m-%d %H:%i'), '%Y-%m-%d %H:%i'), STR_TO_DATE(DATE_FORMAT( ?, '%Y-%m-%d %H:%i'), '%Y-%m-%d %H:%i'), ?, ?, now(), now()) ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$_type, $remark, $flag, $start_time, $end_time, $_inplan, $updated_user]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    function edit_formplan($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT * FROM _formplan WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $formplan = $stmt->fetch();
            return $formplan;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    function update_formplan($request){
        $pdo = pdo();
        extract($request);
        $sql = "UPDATE _formplan 
                SET _type=?, remark=?, flag=?, start_time=STR_TO_DATE(DATE_FORMAT( ?, '%Y-%m-%d %H:%i'), '%Y-%m-%d %H:%i'), end_time=STR_TO_DATE(DATE_FORMAT( ?, '%Y-%m-%d %H:%i'), '%Y-%m-%d %H:%i'), _inplan=?, updated_user=?, updated_at=now() 
                WHERE id=? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$_type, $remark, $flag, $start_time, $end_time, $_inplan, $updated_user, $id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    function delete_formplan($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM _formplan WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // formplan 隱藏或開啟
    function changeFormplan_flag($request){
        $pdo = pdo();
        extract($request);
        $sql_check = "SELECT _formplan.* FROM _formplan WHERE id=?";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$id]);
        $row = $stmt_check -> fetch();
        if($row['flag'] == "Off" || $row['flag'] == "chk"){
            $flag = "On";
        }else{
            $flag = "Off";
        }
        $sql = "UPDATE _formplan SET flag=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$flag, $id]);
            $Result = array(
                'id'   => $id,
                'flag' => $flag
            );
            return $Result;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 在index表頭顯示清單：
    function show_formplan(){
        $pdo = pdo();
        // 20240205 -- 年不列入考量
        $sql = "SELECT _plan.*,
                    CASE
                        WHEN DATE_FORMAT(NOW(), '%m-%d %H:%i') BETWEEN DATE_FORMAT(_plan.start_time, '%m-%d %H:%i') AND DATE_FORMAT(_plan.end_time, '%m-%d %H:%i') THEN 'true' 
                        ELSE 'false'
                    END AS onGoing
                    , _case.title AS case_title
                FROM _formplan _plan
                LEFT JOIN _formcase _case ON _plan._type = _case._type
                ORDER BY _plan.id ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $formplans = $stmt->fetchAll();
            return $formplans;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }


// // // _formcase CRUD
    function store_formcase($request){
        $pdo = pdo();
        extract($request);
        $sql = "INSERT INTO _formcase(_type, title, flag, updated_user, created_at, updated_at)VALUES(?,?,?,? ,now(),now())";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$_type, $title, $flag, $updated_user]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    function edit_formcase($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT * FROM _formcase WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
            $formcase = $stmt->fetch();
            return $formcase;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    function update_formcase($request){
        $pdo = pdo();
        extract($request);
        $sql = "UPDATE _formcase SET _type=?, title=?, flag=?, updated_user=?, updated_at=now() WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$_type, $title, $flag, $updated_user, $id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    function delete_formcase($request){
        $pdo = pdo();
        extract($request);
        $sql = "DELETE FROM _formcase WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$id]);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // formcase 隱藏或開啟
    function changeformcase_flag($request){
        $pdo = pdo();
        extract($request);

        $sql_check = "SELECT _formcase.* FROM _formcase WHERE id=?";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$id]);
        $row = $stmt_check -> fetch();

        if($row['flag'] == "Off" || $row['flag'] == "chk"){
            $flag = "On";
        }else{
            $flag = "Off";
        }

        $sql = "UPDATE _formcase SET flag=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$flag, $id]);
            $Result = array(
                'id'   => $id,
                'flag' => $flag
            );
            return $Result;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 在index表頭顯示清單：
    function show_formcase(){
        $pdo = pdo();
        $sql = "SELECT * FROM _formcase ORDER BY id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $formcase = $stmt->fetchAll();
            return $formcase;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }



