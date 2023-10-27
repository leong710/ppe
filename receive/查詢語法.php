<?php

    // 1. 我的申請單：開單人、申請人 = my_emp_id

    // 2. 我待簽清單：
    //          idty = 1申請送出、11發貨後送出
    //          in_sign = my_emp_id

    // 3. 轄區申請單：
    //          local.fab_id = fab.id = user.fab_id


    $_1我的申請單 = "SELECT _r.*
                        , _l.local_title , _l.local_remark , _f.fab_title , _f.fab_remark , _s.site_title , _s.site_remark 
                    FROM `_receive` _r
                    LEFT JOIN _local _l ON _r.local_id = _l.id
                    LEFT JOIN _fab _f ON _l.fab_id = _f.id
                    LEFT JOIN _site _s ON _f.site_id = _s.id

                    WHERE '10008048' IN (_r.emp_id, _r.created_emp_id) ";

    $_2我待簽清單 = "SELECT _r.*
                        , _l.local_title , _l.local_remark , _f.fab_title , _f.fab_remark , _s.site_title , _s.site_remark 
                    FROM `_receive` _r
                    LEFT JOIN _local _l ON _r.local_id = _l.id
                    LEFT JOIN _fab _f ON _l.fab_id = _f.id
                    LEFT JOIN _site _s ON _f.site_id = _s.id

                    WHERE _r.idty IN (1, 11) AND _r.in_sign = '10008048'; ";

    $_3轄區申請單 = "SELECT _r.*
                        , _l.local_title , _l.local_remark , _f.fab_title , _f.fab_remark , _s.site_title , _s.site_remark 
                    FROM `_receive` _r
                    LEFT JOIN _local _l ON _r.local_id = _l.id
                    LEFT JOIN _fab _f ON _l.fab_id = _f.id
                    LEFT JOIN _site _s ON _f.site_id = _s.id
                    
                    LEFT JOIN _users _u ON _l.fab_id = _u.fab_id OR FIND_IN_SET(_l.fab_id, _u.sfab_id)
                    WHERE (FIND_IN_SET(_l.fab_id, _u.sfab_id) OR (_l.fab_id = _u.fab_id)) AND (_u.emp_id = '10008048') ";
                    // WHERE _u.emp_id = '10008048' OR FIND_IN_SET(_l.fab_id, _u.sfab_id);";

    $_4我的轄區 = "SELECT _f.id AS fab_id, _f.fab_title, _f.fab_remark, _f.flag
                  FROM _fab AS _f
                  LEFT JOIN _users AS _u ON FIND_IN_SET(_f.id, _u.sfab_id) OR _f.id = _u.fab_id
                  WHERE _u.emp_id = '10008048'
                  ORDER BY _f.id ";

    $_5待領清單 = "SELECT _r.* 
                      , _l.local_title , _l.local_remark , _f.fab_title , _f.fab_remark , _s.site_title , _s.site_remark 
                  FROM `_receive` _r 
                  LEFT JOIN _local _l ON _r.local_id = _l.id 
                  LEFT JOIN _fab _f ON _l.fab_id = _f.id 
                  LEFT JOIN _site _s ON _f.site_id = _s.id 

                  LEFT JOIN _users _u ON _l.fab_id = _u.fab_id OR FIND_IN_SET(_l.fab_id, _u.sfab_id)
                  WHERE (FIND_IN_SET(_l.fab_id, _u.sfab_id) OR (_l.fab_id = _u.fab_id)) AND _u.emp_id = ? AND _r.idty = 0
                  ORDER BY _r.created_at DESC ";            // 1.我的申請單的改良

    $sql = "SELECT _r.id, _r.idty, _r.cname, _r.in_sign, _r.local_id
                , _l.local_title , _l.local_remark , _f.fab_title , _f.fab_remark  , _s.site_title , _s.site_remark 
                , _u.cname AS fab_cname         , _u.emp_id AS fab_emp_id, _u.cname AS fab_cname
            FROM `_receive` _r
            LEFT JOIN _local _l ON _r.local_id = _l.id
            LEFT JOIN _fab _f ON _l.fab_id = _f.id
            LEFT JOIN _site _s ON _f.site_id = _s.id
            -- LEFT JOIN (SELECT u.* FROM _users u WHERE u.emp_id = '10008048' ) AS _u ON _f.id = _u.fab_id
            LEFT JOIN _users _u ON _f.id = _u.fab_id
            WHERE _r.emp_id = '10008048' OR _r.created_emp_id = '10008048' OR _r.in_sign = '10008048'
            -- WHERE _u.emp_id = '10008048' AND (_r.emp_id = '10008048' OR _r.created_emp_id = '10008048' OR _r.in_sign = '10008048')
    ";
    $sql .= " UNION
            SELECT u.emp_id, _f.*
            FROM `_users` AS u
            LEFT JOIN _fab AS _f ON FIND_IN_SET(_f.id, u.sfab_id)
            WHERE u.emp_id = ? ";
            
    $sql_這個可以用 .= "SELECT _f.*
                       FROM _fab AS _f
                       LEFT JOIN _users AS _u ON FIND_IN_SET(_f.id, _u.sfab_id) OR _f.id = _u.fab_id
                       WHERE _u.emp_id = '10008048'
                       ORDER BY _f.id";

    // Base64可以將二進位制轉碼成可見字元方便進行http傳輸，但是base64轉碼時會生成「+」，「/」，「=」這些被URL進行轉碼的特殊字元，導致兩方面資料不一致。
    // 我們可以在傳送前將「+」，「/」，「=」替換成URL不會轉碼的字元，接收到資料後，再將這些字元替換回去，再進行解碼。

    // 一、URL安全的字串編碼：
    function urlsafe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

    //  二、URL安全的字串解碼：
    function urlsafe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }



