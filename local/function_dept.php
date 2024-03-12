<?php
// // // dept_CRUD group

    // 20231004-改用msSQL-hrDB -R
    function show_dept(){
        $pdo = pdo_hrdb();
        $sql = "SELECT DISTINCT dp.* , d1.OSSTEXT AS up_sign_dept , u.cname AS dept_sir
                  FROM DEPT dp
                  LEFT JOIN HCM_VW_DEPT08 d1 ON dp.up_dep = d1.OSDEPNO
                  LEFT JOIN STAFF u ON dp.emp_id = u.emp_id 
                  ORDER BY dp.sign_code ASC ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $depts = $stmt->fetchAll();
            return $depts;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 20231004-改用msSQL-hrDB 查詢部門主管
    function show_deptSir(){
        $pdo = pdo_hrdb();
        // $sql = "SELECT DISTINCT * FROM tnesh_mb  WHERE idty > 1 AND role <> '' ORDER BY sign_code,id DESC ";
        $sql = "SELECT u.*
                FROM [STAFF] u
                LEFT JOIN [HCM_VW_DEPT08] d ON u.dept_no = d.OSHORT
                where d.ODEPNO_30 = '9T040500' AND u.zjobcode2txt = 'M' ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $sir = $stmt->fetchAll();
            return $sir;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    // 查詢部門清單
    function load_dept($request){
        $pdo = pdo();
        extract($request);
        $sql = "SELECT dp.sign_code, dp.sign_dept, d1.sign_code AS up_sign_code, d1.sign_dept AS up_sign_dept
                FROM dept dp
                LEFT JOIN dept d1 ON dp.up_dep = d1.sign_code
                WHERE dp.sign_code =? OR d1.sign_code =?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$sign_code, $sign_code]);
            $depts = $stmt->fetchAll();
            return $depts;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    // 查詢部門清單及主官管
    function xshow_deptList(){
        $pdo = pdo();
        $sql = "SELECT dept.sign_code 1sc,dept.sign_dept 1sd,u1.cname 1cname,d1.sign_code 2sc,d1.sign_dept 2sd,u2.cname 2cname 
                FROM `dept`
                LEFT JOIN tnesh_mb u1 ON dept.emp_id = u1.emp_id
                INNER JOIN dept d1 ON dept.sign_code = d1.up_dep
                LEFT JOIN tnesh_mb u2 ON d1.emp_id = u2.emp_id
                ORDER by dept.sign_code,d1.sign_code ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $deptLists = $stmt->fetchAll();
            return $deptLists;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
