<?php
// // // _formplan CRUD
    function store_formplan($request){
        $pdo = pdo();
        extract($request);
        $sql = "INSERT INTO _formplan(_type, remark, flag, start_time, end_time, _inplan, updated_user, updated_at, created_at)
                -- VALUES(?, ?, ?, STR_TO_DATE(DATE_FORMAT( ?, '%Y-%m-%d %H:%i'), '%Y-%m-%d %H:%i'), STR_TO_DATE(DATE_FORMAT( ?, '%Y-%m-%d %H:%i'), '%Y-%m-%d %H:%i'), ?, ?, now(), now()) 
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

    function show_formplan(){
        $pdo = pdo();
        $sql = "SELECT * ,
                    CASE
                        WHEN NOW() BETWEEN start_time AND end_time THEN 'true'
                        ELSE 'false'
                    END AS onGoing 
                FROM _formplan 
                ORDER BY id ASC";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $formplans = $stmt->fetchAll();
            return $formplans;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }





