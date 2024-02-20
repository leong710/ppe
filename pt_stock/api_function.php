<?php

    // API更新amount
    function update_amount($request){
        $pdo = pdo();
        extract($request);
        if($amount >= 1){
            $flag = '';
        }else{
            $flag = 'Off';
        }
        $sql = "UPDATE pt_stock
                SET amount=?, flag=?, updated_at=now()
                WHERE id=? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$amount, $flag, $id]);
            return "mySQL寫入 - 成功";
        }catch(PDOException $e){
            echo $e->getMessage();
            return "mySQL寫入 - 失敗";
        }
    }
    // 隱藏或開啟
    function changePTLocal_flag($request){
        $pdo = pdo();
        extract($request);

        $sql_check = "SELECT pt_local.* FROM pt_local WHERE id=?";
        $stmt_check = $pdo -> prepare($sql_check);
        $stmt_check -> execute([$id]);
        $row = $stmt_check -> fetch();

        if($row['flag'] == "Off" || $row['flag'] == "chk"){
            $flag = "On";
        }else{
            $flag = "Off";
        }

        $sql = "UPDATE pt_local SET flag=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$flag, $id]);
            $Result = array(
                'table' => $table, 
                'id'    => $id,
                'flag'  => $flag
            );
            return $Result;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }