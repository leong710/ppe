<?php
    require_once("function.php");

    $webroot = "..";
    
    if(isset($_SESSION[$sys_id])){
        $auth_emp_id = $_SESSION["AUTH"]["emp_id"];    // 取出$_session引用
        $auth_cname  = $_SESSION["AUTH"]["cname"];     // 取出$_session引用
        $sys_id_auth = true; 
        $sys_id_role = $_SESSION[$sys_id]["role"];     // 取出$_session引用
    }else{
        $sys_id_auth = false; 
        $sys_id_role = false;                          // 取出$_session引用
    }

    // 2023/12/14 這邊待處理
    if($sys_id_auth == true && $sys_id_role <= 2){
        //// 2調撥
            $fab_id = $_SESSION[$sys_id]["fab_id"];        // 先給預設值
            $myTrade = show_myTrade($fab_id);              // 查詢器材調撥
            if(!empty($myTrade)) { 
                $numTrade = $myTrade["idty_count"];
            }else{
                $numTrade = 0;
            } 
        //// 1需求
            if($_SESSION[$sys_id]['role'] <= 1 ){    // 需求單限pm以上
                $myIssue = show_myIssue();          // 查詢需求單
            }
            if(!empty($myIssue)) { 
                $numIssue = $myIssue["idty_count"];
            }else{
                $numIssue = 0;
            }
        //// 檢點表
            // 先給預設值
            $sort_fab_id = $fab_id = $_SESSION[$sys_id]["fab_id"];
            $sort_emp_id = $emp_id = $_SESSION["AUTH"]["emp_id"];
            // 今年年份
                $today_year = date('Y');
                // 半年分界線
                if(date('m') <= 6 ){
                    $half = "H1";
                }else{
                    $half = "H2";
                }

            $list_setting = array(    // 組合查詢陣列
                'fab_id' => $sort_fab_id,
                'emp_id' => $sort_emp_id,
                'checked_year' => $today_year,      // 建立查詢陣列for顯示今年點檢表
                'half' => $half                     // 建立查詢陣列for顯示今年點檢表
            );

            $sort_check_list = sort_check_list($list_setting);      // 查詢自己的點檢紀錄
            $numChecked = count($sort_check_list);         // 計算自己的點檢紀錄筆數

    }else{
        $numTrade = 0;
        $numIssue = 0;
        $numChecked = 0;
    }
    $num = $numTrade + $numIssue;
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo $webroot;?>/dashBoard/index.php">tnESH-PPE防護具管理</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto   my-2 my-lg-0 navbar-nav-scroll">
                <?php if($sys_id_auth){ ?>
                    <!-- <li class="nav-item"><a class="nav-link active" aria-current="page" href="#"><i class="fa-regular fa-square-plus"></i>&nbsp外層Link</a></li> -->
                    <!-- 下拉式選單 -->
                    <?php if($sys_id_role >= 0){ ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link active dropdown-toggle" href="#" id="navbarDD_1" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-cart-plus"></i>&nbsp領用管理</a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDD_1">
                                <li><a class="dropdown-item" href="<?php echo $webroot;?>/receive/form.php"><i class="fa fa-edit"></i>&nbsp領用申請</a></li>
                                <li><a class="dropdown-item" href="<?php echo $webroot;?>/receive/"><i class="fa-solid fa-3"></i>&nbsp<b>我的領用申請</b></a></li>
                                <?php if($sys_id_role <= 2 ){ ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/dashBoard/sum_report.php"><i class="fa-solid fa-list"></i><i class="fa-solid fa-truck"></i>&nbsp進出量與成本匯總</a></li>
                                <?php } ?>
                            </ul>
                        </li>

                        <?php if($sys_id_role <= 2 ){ ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link active dropdown-toggle" href="#" id="navbarDD_2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-warehouse"></i>&nbsp庫存管理
                                        <?php echo ($numChecked == 0) ? '<span class="badge rounded-pill bg-danger"><i class="fa-solid fa-bell"></i></span>':'';
                                              echo ($num !=0) ? '<span class="badge rounded-pill bg-danger">'.$num.'</span>':''; ?></a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDD_2">
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/stock/index.php"><i class="fa-solid fa-boxes-stacked"></i>&nbsp<b>倉庫庫存</b>
                                    <?php if($numChecked == 0){?>
                                            <span class="badge rounded-pill bg-danger"><i class="fa-solid fa-car-on"></i></span>
                                        <?php }?></a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/trade/form.php"><i class="fa-solid fa-upload"></i>&nbsp調撥出庫</a></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/trade/restock.php"><i class="fa-solid fa-download"></i>&nbsp其他入庫</a></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/trade/"><i class="fa-solid fa-2"></i>&nbsp<b>出入作業總表</b>
                                        <?php if($numTrade !=0){?>
                                            &nbsp<span class="badge rounded-pill bg-danger"><?php echo $numTrade; ?></span>
                                        <?php }?></a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/issue/form.php"><i class="fa fa-edit" aria-hidden="true"></i>&nbsp請購需求</a></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/issue/"><i class="fa-solid fa-1"></i>&nbsp<b>請購需求總表</b>
                                        <?php if($numIssue !=0){?>
                                            &nbsp<span class="badge rounded-pill bg-danger"><?php echo $numIssue; ?></span>
                                        <?php }?></a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/checked/index.php"><i class="fa-solid fa-list-check"></i>&nbsp<b>半年檢紀錄表</b></a></li>
                                </ul>
                            </li>
                        <?php } ?>

                        <?php if($sys_id_role <= 1 ){ ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link active dropdown-toggle" href="#" id="navbarDD_3" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-sliders"></i>&nbsp進階設定</a>
                                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDD_3">
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/pno/"><i class="fa-solid fa-list"></i>&nbsp料號管理</a></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/catalog/index.php"><i class="fas fa-th-large"></i>&nbsp器材目錄管理</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/local/"><i class="fa-solid fa-location-dot"></i>&nbsp儲存點管理</a></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/local/low_level.php"><i class="fa-solid fa-retweet"></i>&nbsp安全存量設定</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/supp/"><i class="fa-solid fa-address-book"></i>&nbsp供應商聯絡人管理</a></li>
                                </ul>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDD_4" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-gear"></i>&nbsp管理員專區</a>
                                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDD_4">
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/autolog/"><i class="fa-regular fa-rectangle-list"></i>&nbspMAPP發報記錄管理</a></li>
                                    <?php if($sys_id_role <= 0 ){ ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/receive/auto.php"><i class="fa-solid fa-comment-sms"></i>&nbspMAPP待簽發報</a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                    <?php } ?>
                    <!-- 下拉式選單 -->
                <?php } ?>
            </ul>
            
            <!-- .navbar-toggler, .navbar-collapse 和 .navbar-expand{-sm|-md|-lg|-xl} -->
            <ul class="navbar-nav ms-auto   my-2 my-lg-0 navbar-nav-scroll">
                <?php if(!$sys_id_auth){ ?>
                    <li class="nav-item mx-1"><a href="<?php echo $webroot;?>/auth/login.php" class=""><i class="fa fa-sign-in" aria-hidden="true"></i> 登入</a></li>
                    <!-- <li class="nav-item mx-1 disabled"><a href="<php echo $webroot;?>/auth/register.php" class="btn btn-success">註冊</a></li> -->
                <?php } else { ?>
                    <!-- 下拉式選單 -->
                    <li class="nav-item dropdown">
                        <a class="nav-link active dropdown-toggle" href="#" id="navbarDD_reg" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php   if(isset($_SESSION["AUTH"]["pass"]) && $_SESSION["AUTH"]["pass"] == "ldap"){
                                        echo '<i class="fa fa-user" aria-hidden="true"></i>';
                                    } else {
                                        echo '<i class="fa fa-user-secret" aria-hidden="true"></i>';
                                    } 
                                    echo (isset($_SESSION[$sys_id]["site_title"])) ? "(".$_SESSION[$sys_id]["site_title"].") ":"";
                                    echo $sys_id_auth ? $auth_cname.'<sup class="text-danger"> - '.$sys_id_role.'</sup>':""; 
                            ?> 你好</a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDD_reg">
                            <?php   
                                if($sys_id_auth){  
                                    if($sys_id_role <= 2){ ?>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/auth/edit.php?user=<?php echo $_SESSION["AUTH"]["user"];?>"><i class="fa fa-user-circle" aria-hidden="true"></i> 編輯User資訊</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                <?php } 
                                    if($sys_id_role <= 1){ ?>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/auth/index.php"><i class="fa fa-address-card" aria-hidden="true"></i> 管理使用者</a></li>
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
