<?php
    require_once("function.php");

    $webroot = "..";
    
    if(isset($_SESSION[$sys_id])){
        // 取出$_session引用
        $auth_emp_id    = $_SESSION["AUTH"]["emp_id"];
        $auth_cname     = $_SESSION["AUTH"]["cname"];
        $auth_sign_code = $_SESSION["AUTH"]["sign_code"];
        $sys_role       = $_SESSION[$sys_id]["role"];
        $sys_fab_id     = $_SESSION[$sys_id]["fab_id"];     
        $sys_sfab_id    = $_SESSION[$sys_id]["sfab_id"];  
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
        if(date('m') <= 6 ){
            $half = "H1";
        }else{
            $half = "H2";
        }
    // 組合查詢陣列
        $query_arr = array(
            'fab_id'       => isset($sys_fab_id    ) ? $sys_fab_id     : "",
            'emp_id'       => isset($auth_emp_id   ) ? $auth_emp_id    : "",
            'sign_code'    => isset($auth_sign_code) ? $auth_sign_code : "",
            'checked_year' => $today_year,              // 建立查詢陣列for顯示今年點檢表
            'half'         => $half                     // 建立查詢陣列for顯示今年點檢表
        );

    // 2023/12/14 這邊待處理
    if($sys_auth == true && $sys_role <= 2){
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
        //// 0檢點表
            $sort_check_list = sort_check_list($query_arr);  // 查詢自己的點檢紀錄
            $numChecked = count($sort_check_list);           // 計算自己的點檢紀錄筆數
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
                        <li class="nav-item dropdown">
                            <a class="nav-link active dropdown-toggle" id="navbarDD_1" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-cart-plus"></i>&nbsp領用管理
                                <?php echo ($num3 !=0) ? '<span class="badge rounded-pill bg-danger">'.$num3.'</span>':''; ?></a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDD_1">
                                <li><a class="dropdown-item" href="<?php echo $webroot;?>/receive/form.php"><i class="fa fa-edit"></i>&nbsp領用申請</a></li>
                                <li><a class="dropdown-item" href="<?php echo $webroot;?>/receive/"><i class="fa-solid fa-3"></i>&nbsp<b>領用申請總表</b>
                                    <?php if($numReceive !=0){?>
                                        &nbsp<span class="badge rounded-pill bg-danger"><?php echo $numReceive; ?></span>
                                    <?php }?></a></li>
                            </ul>
                        </li>

                        <?php if($sys_role <= 2.5 ){ ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link active dropdown-toggle" id="navbarDD_2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-warehouse"></i>&nbsp庫存管理
                                        <?php echo ($numChecked == 0) ? '<span class="badge rounded-pill bg-danger"><i class="fa-solid fa-bell"></i></span>':'';
                                              echo ($num12 !=0) ? '<span class="badge rounded-pill bg-danger">'.$num12.'</span>':''; ?></a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDD_2">
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/stock/"><i class="fa-solid fa-boxes-stacked"></i>&nbsp<b>倉庫庫存</b>
                                        <?php if($numChecked == 0){?>
                                            <span class="badge rounded-pill bg-danger"><i class="fa-solid fa-car-on"></i></span>
                                        <?php }?></a></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/dashBoard/sum_report.php"><i class="fa-solid fa-list"></i><i class="fa-solid fa-truck"></i>&nbsp進出量與成本匯總</a></li>

                                    <li><hr class="dropdown-divider"></li>
                                    <?php if($sys_role <= 2 ){ ?>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/trade/form.php"><i class="fa-solid fa-upload"></i>&nbsp調撥出庫</a></li>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/trade/restock.php"><i class="fa-solid fa-download"></i>&nbsp其他入庫</a></li>
                                    <?php }?>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/trade/"><i class="fa-solid fa-2"></i>&nbsp<b>出入作業總表</b>
                                        <?php if($numTrade !=0){?>
                                            &nbsp<span class="badge rounded-pill bg-danger"><?php echo $numTrade; ?></span>
                                        <?php }?></a></li>

                                    <li><hr class="dropdown-divider"></li>
                                    <?php if($sys_role <= 2 ){ ?>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/issue/form.php"><i class="fa fa-edit" aria-hidden="true"></i>&nbsp請購需求</a></li>
                                    <?php }?>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/issue/"><i class="fa-solid fa-1"></i>&nbsp<b>請購需求總表</b>
                                        <?php if($numIssue !=0){?>
                                            &nbsp<span class="badge rounded-pill bg-danger"><?php echo $numIssue; ?></span>
                                        <?php }?></a></li>

                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/checked/"><i class="fa-solid fa-list-check"></i>&nbsp<b>半年檢紀錄表</b></a></li>

                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/pt_stock/"><i class="fa-solid fa-kit-medical"></i>&nbsp<b>除汙器材管理</b></a></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/pt_stock/sum_report.php"><i class="fa-solid fa-chart-column"></i> 除汙器材管控清單</span></b></a></li>
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
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/formplan/"><i class="fa-solid fa-address-book"></i>&nbsp表單計畫</a></li>
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
                            <?php if(isset($_SESSION["AUTH"]["pass"]) && $_SESSION["AUTH"]["pass"] == "ldap"){
                                        echo '<i class="fa fa-user" aria-hidden="true"></i>';
                                    } else {
                                        echo '<i class="fa fa-user-secret" aria-hidden="true"></i>';
                                    } 
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
