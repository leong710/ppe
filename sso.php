<?php
// // // *** none function group
    function accessDenied($sys_id){
        $url='http://'.$_SERVER['HTTP_HOST'].'/';                           // 複製本頁網址藥用
        if(!isset($_SESSION)){                                              // 確認session是否啟動
            session_start();
        }
        if(!isset($_SESSION["AUTH"]) || !isset($_SESSION[$sys_id])){
            // header("refresh:0;url={$url}tnESH_SSO/login.php?sys_id={$sys_id}");     // 手動
            header("refresh:0;url={$url}tnESH_SSO/?sys_id={$sys_id}");              // SSOv4自動
            exit;
        }
        return;
    }

            //  * 取得目前頁面的 base URL
            function getBaseUrl() {
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
                return $protocol . $_SERVER['HTTP_HOST'] . '/';
            }
    // reload sys_id & local user role
    function accessDenied_sys($sys_id){
        //  * 由外部提取 signCode 條件
        function loadSignCode($filename = "../sign_code.json") {
            if (!file_exists($filename)) {
                $sign_code = [
                    "dept_no"         => ["9O061500"],   // 9O車用
                    "KOSTL"           => ["9O061500"],   
                    "dept_plant_code" => ["9O061500","9L090500"],   // 處
                ];
                return $sign_code;
            } else {
                $json = file_get_contents($filename);
                return json_decode($json, true);
            }
        }
        // 取得PM權限資料
        function getPmRole($pdo, $emp_id) {
            $sql = "SELECT GROUP_CONCAT(_fab.id SEPARATOR ',') AS _fab_scope , GROUP_CONCAT(CONCAT(_site.site_remark, '_' ,_fab.fab_title) SEPARATOR ',') AS _fab_title
                    FROM _fab 
                    LEFT JOIN _site ON _fab.site_id = _site.id 
                    WHERE _fab.flag = 'On' AND ( FIND_IN_SET( ? , _fab.pm_emp_id) > 0 )";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$emp_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        // 取得使用者角色
        function getUserRole($pdo, $emp_id) { // 確保fab是否為On
            $sql = "SELECT u.id, u.emp_id, u.cname, u.sfab_id, u.role, u.idty ,_fab.id AS fab_id, _fab.fab_title, _fab.fab_remark
                    FROM _users u 
                    LEFT JOIN _fab ON u.fab_id = _fab.id AND _fab.flag = 'On'
                    WHERE u.emp_id = ? ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$emp_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        // 取得hrdb的使用者資料
        function getHrdbUser($pdo_hrdb, $user) {
            // $sql_hrdb = "SELECT _e.PERNR AS emp_id, _e.COMID1 AS 'user', CONCAT(_e.NACHN, _e.VORNA) AS cname, _e.PGTXT AS emp_group, _e.PKTXT AS emp_type, _e.PTEXT AS emp_scope, _e.BTEXT AS emp_sub_scope
            //                 , _e.OSHORT AS dept_no, _e.OSTEXT AS emp_dept , _e.ZLBSUPTXT AS vendor, _e.ZLBSUP AS dormitory , _e.OMAGER 
            //                 , _e.ZJOBCODE2TXT , _e.CSTEXT , _e.STAT2 , _e.STAT2TXT , _e.COMID2 , _e.COMID3 , _e.SCHKZ , _e.SCHKZTXT 
            //                 , _d.OSTEXT_10 AS dept_center, _d.ODEPNO_30 AS dept_plant_code , _d.OSTEXT_30 AS plant , _d.OSTEXT_20 AS dept_a, _d.OSTEXT_30 AS dept_b, _d.OSTEXT_40 AS dept_c, _d.OSTEXT_50 AS dept_d 
            //                 , _d.OFTEXT , _e.GESCH , _e.NATIO , _e.NATIOTXT , _e.BTRTL , _d.KOSTL
            //             FROM [hrDB].[dbo].[HCM_VW_EMP01_hiring] _e
            //             LEFT JOIN [hrDB].[dbo].[HCM_VW_DEPT01] _d ON _e.OSHORT = _d.OSHORT
            //             WHERE _e.COMID1 = ? ";
            $sql_hrdb = "SELECT _e.PERNR AS emp_id, _e.COMID1 AS 'user', CONCAT(_e.NACHN, _e.VORNA) AS cname, _e.PGTXT AS emp_group, _e.PKTXT AS emp_type, _e.PTEXT AS emp_scope, _e.BTEXT AS emp_sub_scope
                            , _e.OSHORT AS dept_no, _e.OSTEXT AS emp_dept , _e.ZLBSUPTXT AS vendor, _e.ZLBSUP AS dormitory , _e.OMAGER 
                            , _e.ZJOBCODE2TXT , _e.CSTEXT , _e.STAT2 , _e.STAT2TXT , _e.COMID2 , _e.COMID3 , _e.SCHKZ , _e.SCHKZTXT 
                            , _d.OSTEXT_10 AS dept_center, _d.ODEPNO_30 AS dept_plant_code , _d.OSTEXT_30 AS plant , _d.OSTEXT_20 AS dept_a, _d.OSTEXT_30 AS dept_b, _d.OSTEXT_40 AS dept_c, _d.OSTEXT_50 AS dept_d 
                            , _d.OFTEXT , _e.GESCH , _e.NATIO , _e.NATIOTXT , _e.BTRTL , _d.KOSTL
                        FROM HCM_VW_EMP01_hiring _e
                        LEFT JOIN HCM_VW_DEPT01 _d ON _e.OSHORT = _d.OSHORT
                        WHERE _e.COMID1 = ? ";
            $stmt = $pdo_hrdb->prepare($sql_hrdb);
            $stmt->execute([$user]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        if(!isset($_SESSION)){                                              // 確認session是否啟動
            session_start();
        }
        $url = getBaseUrl();
        
        // 未通過認證或密碼不正確，直接導向登入頁
        if(!isset($_SESSION["AUTH"]) && empty($_SESSION["AUTH"]["pass"])){  // 確認是否完成AUTH/pass => True
            header("refresh:0;url={$url}tnESH_SSO/login.php?sys_id={$sys_id}");     // 手動
            exit;
        }
        
        // 已經設置該系統權限，直接回傳
        if(isset($_SESSION[$sys_id])){                                  // 如果sys_id已經建立，就返回
            return;
        }
            
        $pdo = pdo();
        $pdo_hrdb = pdo_hrdb();
        
        // 取得用戶資訊與部門權限資料
        $emp_id = $_SESSION["AUTH"]["emp_id"];
        $user = strtoupper($_SESSION["AUTH"]["user"]);
        
        // 取得所有資料
        $sys_pm_row   = getPmRole($pdo, $emp_id);
        $sys_user_row = getUserRole($pdo, $emp_id);
        $hrdb_mb      = getHrdbUser($pdo_hrdb, $user) ?? [];

        // hrdb.BTRTL處理
        if ($hrdb_mb) {
            $_SESSION["AUTH"]["emp_scope"] = $_SESSION["AUTH"]["emp_scope"] ?? ($hrdb_mb["emp_scope"] ?? "");
            if (!empty($hrdb_mb["BTRTL"])) {
                $cur = $_SESSION["AUTH"]["BTRTL"] ?? "";
                $arr = $cur ? array_unique(array_merge(explode(",", $cur), [$hrdb_mb["BTRTL"]])) : [$hrdb_mb["BTRTL"]];
                $_SESSION["AUTH"]["BTRTL"] = implode(",", $arr);
            }
        }

        // 權限初始值
        $login_inf = [
            "id"         => $sys_user_row["id"] ?? false,
            "role"       => 3,
            "BTRTL"      => $hrdb_mb["BTRTL"] ?? "",
            "fab_id"     => $sys_user_row["fab_id"] ?? 0,
            "fab_title"  => $sys_user_row["fab_title"] ?? 0,
            "fab_remark" => $sys_user_row["fab_remark"] ?? 0,
            "sfab_id"    => [],
            "isEshMb"    => FALSE,
        ];
        
        // PM範圍$sys_pm_row
        if ($sys_pm_row && !empty($sys_pm_row["_fab_scope"])) {
            $pm_fab_scope = explode(",", $sys_pm_row["_fab_scope"]);
            $pm_fab_scope = array_diff($pm_fab_scope, array("",null));  // 去除空陣列
            $login_inf["sfab_id"] = array_merge($login_inf["sfab_id"], $pm_fab_scope);
            $login_inf["role"] = count($pm_fab_scope) > 0 ? 2 : 2.5; // 權限分級 1=大PM ,1.5=esh主管 ,2=siteUser ,2.5=eshUser ,3=otherUser
            $sys_pm_row["role"] = $login_inf["role"];
            
            if($login_inf["fab_id"] == "") {
                $login_inf["fab_id"] = $pm_fab_scope[0];
            } 
            if($login_inf["fab_title"] == ""){
                $pm_fab_title = explode(",", $sys_pm_row["_fab_title"]);
                $login_inf["fab_title"] = $pm_fab_title[0];
            }
        }

        // signCode判斷環安部門
        $signCode_arr = loadSignCode();
        foreach ($signCode_arr as $key => $codes) {
            if (isset($hrdb_mb[$key]) && in_array($hrdb_mb[$key], $codes)) {
                $login_inf["isEshMb"] = TRUE;
                $login_inf["role"]    = 2.5;
                break;
            }
        }

        // local user_role強化
        if ($sys_user_row) {
            $user_fab_scope = ($sys_user_row["fab_id"] ?? 0) . "," . ($sys_user_row["sfab_id"] ?? 0);
            $user_fab_scope = !empty($user_fab_scope) ? explode(",", $user_fab_scope) : [];
            $login_inf["sfab_id"] = array_merge($login_inf["sfab_id"], $user_fab_scope );
            $login_inf["sfab_id"] = array_diff($login_inf["sfab_id"], array("",null));  // 去除空陣列

            if (isset($sys_user_row["id"], $sys_user_row["role"]) && $sys_user_row["role"] !== "") {
                $login_inf["role"] = min($sys_user_row["role"], $login_inf["role"]);
                if ($login_inf["isEshMb"] && ($_SESSION["AUTH"]["idty"] ?? null) == "M") {
                    $login_inf["role"] = min($login_inf["role"], 1.5);
                }
            } else {
                echo "<script>alert('{$sys_user_row["cname"]} Local帳號停用，請洽管理員')</script>";
                $login_inf["role"] = '';
            }

        } else {
            if(!empty($sys_pm_row) && isset($sys_pm_row["role"])){
                $login_inf["role"] = $sys_pm_row["role"] ?? $login_inf["role"];
            }else{
                // 未登錄local
                $login_inf["role"] = $login_inf["isEshMb"] ? 
                    (($_SESSION["AUTH"]["idty"] ?? null) == "M" ? 1.5 : $login_inf["role"]) :
                    ( $login_inf["isEshMb"] ? 2.5 : 3);
            }
            $login_inf["sfab_id_str"] = implode(",", $login_inf["sfab_id"]);
            $_SESSION[$sys_id] = $login_inf;    // *** 帶入_session

            header("refresh:0;url={$url}{$sys_id}");
            exit;
        }
        $login_inf["sfab_id_str"] = implode(",", $login_inf["sfab_id"]);
        $_SESSION[$sys_id] = $login_inf;    // *** 帶入_session
        
        // Log
        header("refresh:0;url={$url}tnESH_SSO/autoLog.php?sys_id={$sys_id}");
        exit;
    }
    
    function accessDeniedAdmin($sys_id){
        if(!isset($_SESSION)){                                              // 確認session是否啟動
            session_start();
        }
        if(!isset($_SESSION["AUTH"]) || !isset($_SESSION[$sys_id]) || $_SESSION[$sys_id]["role"] == "" || $_SESSION[$sys_id]["role"] >= 2){
            header('location:../');
            exit;
        }
        return;
    }

?>