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

                    WHERE _r.emp_id = '10008048' OR _r.created_emp_id = '10008048'; ";

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
                    
                    LEFT JOIN _users _u ON _l.fab_id = _u.fab_id
                    WHERE _u.emp_id = '10008048' OR FIND_IN_SET(_l.fab_id, _u.sfab_id);";



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