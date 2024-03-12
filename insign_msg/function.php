<?php
// // // 查詢待簽名單for send MAPP
    function inSign_list(){
        $pdo = pdo();
        $sql = "SELECT emp_id, cname, SUM(issue_waiting) AS issue_waiting, SUM(receive_waiting) AS receive_waiting, SUM(issue_waiting + receive_waiting) AS total_waiting
                FROM (
                        SELECT _i.in_sign AS emp_id, _i.in_signName AS cname, COUNT(_i.in_sign) AS issue_waiting, 0 AS receive_waiting
                        FROM _issue _i
                        WHERE _i.in_sign IS NOT NULL
                        GROUP BY _i.in_sign
                        HAVING _i.in_sign IS NOT NULL
                    UNION ALL
                        SELECT _r.in_sign AS emp_id, _r.in_signName AS cname, 0 AS issue_waiting, COUNT(_r.in_sign) AS receive_waiting
                        FROM _receive _r
                        WHERE _r.in_sign IS NOT NULL
                        GROUP BY _r.in_sign
                        HAVING _r.in_sign IS NOT NULL
                    ) AS merged_results
                GROUP BY emp_id, cname; ";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute();
            $inSign_list = $stmt->fetchAll();
            return $inSign_list;

        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }
// // // 查詢待簽名單 -- end

// // // mapp init -- end
    function check_ip($request){
        extract($request);
        $local_pc = array(                      // 建立local_pc查詢陣列
            '127.0.0.1'   => '7132e2545d301024dfb18da07cceccedb41b4864',   // 127.0.0.1
            'tw059332n_1' => 'a2e9ef3a208c4882a99ec708d09cedc7ebb92bb6',   // tw059332n-10.53.90.184
            'tw059332n_2' => 'dc7f33a2a06752e87d62a7e75bd0feedbddf1cbd',   // tw059332n-169.254.69.80
            'tw059332n_3' => '0afa7ce76ab41ba4845e719d3246c48dadb611fd',   // tw059332n-10.53.110.83
            'tw074163p'   => 'c2cb37acb2c9eb3e4068ac55d278ac7d9bea85e3'    // tw074163p-10.53.90.114
        );
        $ip = sha1(md5($ip));
        
        if(in_array($ip, $local_pc)){
            return true;
        }else{
            return false;
        }
    }



