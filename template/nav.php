<?php
   require_once("../user_info.php");
   require_once("function.php");

    $webroot = "..";
    
    if(isset($_SESSION[$sys_id])){
        // 取出$_session引用
        // $auth_emp_id    = !empty($_SESSION["AUTH"]["emp_id"])    ? $_SESSION["AUTH"]["emp_id"]    : "";
        // $auth_cname     = !empty($_SESSION["AUTH"]["cname"])     ? $_SESSION["AUTH"]["cname"]     : "";
        // $auth_sign_code = !empty($_SESSION["AUTH"]["sign_code"]) ? $_SESSION["AUTH"]["sign_code"] : "";
        // $sys_role       = !empty($_SESSION[$sys_id]["role"])     ? $_SESSION[$sys_id]["role"]     : "";
        // $sys_fab_id     = !empty($_SESSION[$sys_id]["fab_id"])   ? $_SESSION[$sys_id]["fab_id"]   : "1";  
        // $sys_sfab_id    = !empty($_SESSION[$sys_id]["sfab_id"])  ? $_SESSION[$sys_id]["sfab_id"]  : [];  
        $sys_auth = true; 
    }else{
        $sys_auth = false; 
        $sys_role = false; 
    }

    // init
        $numReceive = 0; $numTrade = 0; $numIssue = 0; $numChecked = 0; 
        // 今年年份
        $today_year = date('Y');
        // 半年分界線
        $half = (date('m') <= 6 ) ? "H1" : "H2";

        // 1-1 將sys_fab_id加入sfab_id
        if(empty($sfab_id_str)){
            $sfab_id_str = get_sfab_id2($sys_id, "str");     // 1-1c sfab_id是陣列，要轉成字串str
        }
        if(isset($_SESSION["AUTH"]) && empty($_SESSION[$sys_id]["sfab_id_str"])){
            $_SESSION[$sys_id]["sfab_id_str"] = $sfab_id_str;
        }

    // 組合查詢陣列
        $query_arr = array(
            'fab_id'       => isset($sys_fab_id    ) ? $sys_fab_id     : "",
            'emp_id'       => isset($auth_emp_id   ) ? $auth_emp_id    : "",
            'sign_code'    => isset($auth_sign_code) ? $auth_sign_code : "",
            'checked_year' => $today_year,              // 建立查詢陣列for顯示今年點檢表
            'half'         => $half,                    // 建立查詢陣列for顯示今年點檢表
            'sfab_id'      => $sfab_id_str,
        );

    // 2023/12/14 這邊待處理
    if($sys_auth == true && ($sys_role <= 2 && $sys_role >= 0 )){
        //// 3領用
            $myReceive = show_myReceive($query_arr);   // 3.查詢領用申請
            if(!empty($myReceive)) { 
                $numReceive = $myReceive["idty_count"];
            } 
        //// 2調撥
            $myTrade = show_myTrade($query_arr);       // 2.查詢入出庫申請
            if(!empty($myTrade)) { 
                $numTrade = $myTrade["idty_count"];
            } 
        //// 1需求
            $myIssue = show_myIssue();                  // 1.查詢請購需求單
            if(!empty($myIssue)) { 
                $numIssue = $myIssue["idty_count"];
            }
        //// 0檢點表/查詢表單計畫 20240313
            $show_plan_key = array_keys(nav_show_plan($query_arr)); // 呈現表單計畫中正在執行的表單_type array_key
            $sort_check_list = sort_check_list($query_arr);         // 查詢自己的點檢紀錄
            $sfab_id_cunt  = count(explode(",", $sfab_id_str));     // 取得自己部轄區名單id，字串轉陣列 + 算個數
        //// init
            $checked_type = array (                                 // 先定義出有需要執行點檢的表單名稱 ***
                "stock"     => array (                              // stock
                                "onGoing" => false,                 // 把執行中的plan，預設false
                                "cunt"    => 0 ,                    // 已完成件數防止崩潰，起始值0
                                "checked" => $sfab_id_cunt          // 等待完成件數，預設值是廠區數量
                            ) ,
                "ptstock"   => array (                              // ptstock
                                "onGoing" => false,
                                "cunt"    => 0 ,
                                "checked" => $sfab_id_cunt
                            ) 
            );
            
            foreach($show_plan_key AS $plan_type){
                $checked_type[$plan_type]["onGoing"] = true;                        // 把執行中的plan標示出來
            }

            if($sort_check_list){
                foreach($sort_check_list AS $row){                                      // 把自己轄區已完成的點檢表繞出來
                    if(in_array($row["form_type"], array_keys($checked_type))){         // 檢點表form_type有在需要執行點檢的array_keys中...then
                        $checked_type[$row["form_type"]]["cunt"]    = $row["cunt"];     // 已完成件數
                        $checked_type[$row["form_type"]]["checked"] -= $row["cunt"];    // 計算自己的點檢紀錄筆數 用廠的數量-已點檢的數量，>0:沒檢完，=0:已檢完
                    }
                }
            }
    }

    $num3 = $numReceive;
    $num12 = $numIssue + $numTrade;
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo $webroot;?>/dashBoard/">tnESH-PPE防護具管理</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto   my-2 my-lg-0 navbar-nav-scroll">
                <?php if($sys_auth){ ?>
                    <!-- <li class="nav-item"><a class="nav-link active" aria-current="page" href="#"><i class="fa-regular fa-square-plus"></i>&nbsp外層Link</a></li> -->
                    <!-- 下拉式選單 -->
                    <?php if($sys_role >= 0){ ?>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="<?php echo $webroot;?>/receive/"><i class="fa-solid fa-cart-plus"></i>&nbsp領用管理
                                <?php echo ($num3 !=0) ? '<span class="badge rounded-pill bg-danger">'.$num3.'</span>':''; ?></a></li>

                        <?php if($sys_role <= 2.5 ){ ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link active dropdown-toggle" id="navbarDD_2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-warehouse"></i>&nbsp庫存管理
                                        <?php 
                                            if(($checked_type["stock"]["onGoing"] === true && $checked_type["stock"]["checked"] != 0) ||
                                                ($checked_type["ptstock"]["onGoing"] === true && $checked_type["ptstock"]["checked"] != 0)){ 
                                                    echo "<span class='badge rounded-pill bg-danger'><i class='fa-solid fa-bell'></i></span>";
                                            } 
                                            echo (($num12+$num3) !=0) ? '<span class="badge rounded-pill bg-danger">'.($num12+$num3).'</span>':''; ?></a>
                                              
                                <ul class="dropdown-menu" aria-labelledby="navbarDD_2">
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/stock/"><i class="fa-solid fa-boxes-stacked"></i>&nbsp<b>倉庫庫存</b>
                                        <?php if($checked_type["stock"]["onGoing"] === true && $checked_type["stock"]["checked"] != 0){ 
                                            echo "<span class='badge rounded-pill bg-danger'><i class='fa-solid fa-car-on'></i></span>";
                                         }?></a></li>

                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/stock/sum_report.php"><i class="fa-solid fa-chart-column"></i>&nbsp<b>PPE器材管控清單</b></a></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/dashBoard/sum_report.php"><i class="fa-solid fa-list"></i><i class="fa-solid fa-truck"></i>&nbsp進出量與成本匯總</a></li>
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/receive/"><i class="fa-solid fa-3"></i>&nbsp<b>領用申請總表</b>
                                        <?php if($numReceive !=0){
                                            echo "&nbsp<span class='badge rounded-pill bg-danger'>{$numReceive}</span>";
                                        }?></a></li>

                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/trade/"><i class="fa-solid fa-2"></i>&nbsp<b>出入作業總表</b>
                                        <?php if($numTrade !=0){
                                            echo "&nbsp<span class='badge rounded-pill bg-danger'>{$numTrade}</span>";
                                        }?></a></li>

                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/issue/"><i class="fa-solid fa-1"></i>&nbsp<b>請購需求總表</b>
                                        <?php if($numIssue !=0){
                                            echo "&nbsp<span class='badge rounded-pill bg-danger'>{$numIssue}</span>";
                                        }?></a></li>

                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/checked/"><i class="fa-solid fa-list-check"></i>&nbsp<b>半年檢紀錄表</b></a></li>

                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/pt_stock/"><i class="fa-solid fa-kit-medical"></i>&nbsp<b>除汙器材管理</b>
                                        <?php if($checked_type["ptstock"]["onGoing"] === true && $checked_type["ptstock"]["checked"] != 0){ 
                                            echo "<span class='badge rounded-pill bg-danger'><i class='fa-solid fa-car-on'></i></span>";
                                        }?></a></li>

                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/pt_stock/sum_report.php"><i class="fa-solid fa-chart-column"></i>&nbsp<b>除汙器材管控清單</b></a></li>
                                </ul>
                            </li>
                        <?php } ?>

                        <?php if($sys_role <= 1 ){ ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link active dropdown-toggle" id="navbarDD_3" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-sliders"></i>&nbsp進階設定</a>
                                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDD_3">
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/pno/"><i class="fa-solid fa-list"></i>&nbsp料號管理</a></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/catalog/"><i class="fas fa-th-large"></i>&nbsp器材目錄管理</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/local/"><i class="fa-solid fa-location-dot"></i>&nbsp儲存點管理</a></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/local/low_level.php"><i class="fa-solid fa-retweet"></i>&nbsp安全存量設定</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/supp/"><i class="fa-solid fa-address-book"></i>&nbsp供應商聯絡人管理</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/formplan/"><i class="fa-regular fa-calendar-days"></i>&nbsp表單計畫</a></li>
                                </ul>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" id="navbarDD_4" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-gear"></i>&nbsp管理員專區</a>
                                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDD_4">
                                    <?php if($sys_role <= 1 ){ ?>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/insign_msg/"><i class="fa-solid fa-comment-sms"></i>&nbsp待簽清單統計</a></li>
                                    <?php } ?>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/autolog/"><i class="fa-regular fa-rectangle-list"></i>&nbspMAPP發報記錄管理</a></li>
                                </ul>
                            </li>
                        <?php } ?>
                    <?php } ?>
                    <!-- 下拉式選單 -->
                <?php } ?>
            </ul>
            
            <!-- .navbar-toggler, .navbar-collapse 和 .navbar-expand{-sm|-md|-lg|-xl} -->
            <ul class="navbar-nav ms-auto   my-2 my-lg-0 navbar-nav-scroll">
                <?php if(!$sys_auth){ ?>
                    <li class="nav-item mx-1"><a href="<?php echo $webroot;?>/auth/login.php" class=""><i class="fa fa-sign-in" aria-hidden="true"></i> 登入</a></li>
                    <!-- <li class="nav-item mx-1 disabled"><a href="<php echo $webroot;?>/auth/register.php" class="btn btn-success">註冊</a></li> -->
                <?php } else { ?>
                    <!-- 下拉式選單 -->
                    <li class="nav-item dropdown">
                        <a class="nav-link active dropdown-toggle" href="#" id="navbarDD_reg" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                            title="<?php echo $sys_auth ? 'sys_role：'.$sys_role:'';?>">
                            <?php
                                    echo (isset($_SESSION["AUTH"]["pass"]) && $_SESSION["AUTH"]["pass"] == "ldap") ? '<i class="fa fa-user" aria-hidden="true"></i>':'<i class="fa fa-user-secret" aria-hidden="true"></i>';
                                    // echo (isset($_SESSION[$sys_id]["site_title"])) ? "(".$_SESSION[$sys_id]["site_title"].") ":"";
                                    echo (isset($_SESSION["AUTH"]["dept"])) ? "&nbsp(".$_SESSION["AUTH"]["dept"].")":"";
                                    echo $sys_auth ? "&nbsp".$auth_cname:""; 
                                    echo $sys_auth ? '<sup class="text-danger"> - '.$sys_role.'</sup>':""; 
                            ?> 你好</a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDD_reg">
                            <?php   
                                if($sys_auth){  
                                    if($sys_role <= 2){ ?>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/auth/edit.php?user=<?php echo $_SESSION["AUTH"]["user"];?>"><i class="fa fa-user-circle" aria-hidden="true"></i> 編輯User資訊</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                <?php } 
                                    if($sys_role <= 1){ ?>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/auth/"><i class="fa fa-address-card" aria-hidden="true"></i> 管理使用者</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                <?php } 
                                } else {?>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/auth/login.php"><i class="fa fa-sign-in" aria-hidden="true"></i> SSO登入</a></li>
                                <?php } ?>
                            <li><a class="dropdown-item" href="<?php echo $webroot;?>/auth/logout.php" class=""><i class="fa fa-sign-out" aria-hidden="true"></i> 登出</a></li>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>
