<?php
// // // dept_CRUD group

        //  * 由外部提取 signCode 條件
        function loadSignCode($filename = "../../sign_code.json") {
            if (!file_exists($filename)) {
                $sign_code = ["dept_no"  => "9O061500"];  // carux台灣esh
                return $sign_code;
            } else {
                $json = file_get_contents($filename);
                return json_decode($json, true);
            }
        }

    // 20231004-改用msSQL-hrDB -R // 251014 改版
    function show_dept(){
        $pdo = pdo_hrdb();
        $_sign_code = loadSignCode();
        $sql = "SELECT d01.OOBJID AS sign_dep_id, d01.OSTEXT_10 AS center, d01.OSTEXT_05 AS central_plant 
                    , d01.OSTEXT_30 AS plant
                    , d01.OSTEXT_40 AS dept
                    , d01.OSTEXT_50 AS Section
                    , d01.OSHORT AS sign_code
                    , d01.OSTEXT AS sign_dept
                    , d01.OMAGER AS emp_id
                    , d01.KOSTL AS up_dep
                    , d01.OSSTEXT AS up_sign_dept , staff.cname AS dept_sir
                FROM `hcm_vw_dept01` d01
                LEFT JOIN staff ON d01.OMAGER = staff.emp_id
                WHERE d01.ODEPNO_40 = ? ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$_sign_code["dept_no"]]);
            $depts = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                FROM `STAFF` u
                LEFT JOIN `HCM_VW_DEPT08` d ON u.dept_no = d.OSHORT
                WHERE d.ODEPNO_30 = '9O061500' AND u.zjobcode2txt = 'M' ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $sir = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            $depts = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            $deptLists = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $deptLists;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
