<?php
    require_once("function.php");

    $webroot = "..";

    if(isset($_SESSION[$sys_id])){
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
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- <li class="nav-item"> -->
                    <!-- <a class="nav-link active" href="<php echo $webroot;?>/post/"><i class="fa fa-rss" aria-hidden="true"></i>&nbsp公佈欄</a> -->
                <!-- </li> -->

                <?php if(isset($_SESSION[$sys_id])){ ?>
                    <!-- <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<php echo $webroot;?>/stock/index.php">Local存量管理</a>
                    </li> -->
                    <?php if($_SESSION[$sys_id]["role"] >= 0){ ?>
                        <!-- 下拉式選單 -->
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDarkDropdown" aria-controls="navbarNavDarkDropdown" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
                            <ul class="navbar-nav">

                                <!-- <php if($_SESSION[$sys_id]["role"] == "0" || $_SESSION[$sys_id]["role"] == "1"){ ?> -->
                                    <!-- <li class="nav-item dropdown"> -->
                                        <!-- <a class="nav-link active dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false"> -->
                                            <!-- 公佈欄管理</a> -->
                                        <!-- <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink"> -->
                                            <!-- <li><a class="dropdown-item" href="../post/list.php">公告清單</a></li> -->
                                            <!-- <li><a class="dropdown-item" href="../category/index.php">公告分類管理</a></li> -->
                                            <!-- <li><hr class="dropdown-divider"></li> -->
                                            <!-- <li><a class="dropdown-item" href="../post/create.php"><i class="fa fa-edit"></i> 新增公告</a></li> -->
                                        <!-- </ul> -->
                                    <!-- </li> -->
                                <!-- <php } ?> -->

                                <li class="nav-item dropdown">
                                    <a class="nav-link active dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-warehouse"></i>&nbsp庫存管理
                                        <?php if($numChecked == 0){?>
                                            <span class="badge rounded-pill bg-danger"><i class="fa-solid fa-bell"></i></span>
                                        <?php }?>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDarkDropdownMenuLink">
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/stock/index.php"><i class="fa-solid fa-boxes-stacked"></i>&nbsp庫存管理</a></li>
                                        <!-- <li><a class="dropdown-item" href="<php echo $webroot;?>/stock/byCatalog.php">器材存量管理</a></li> -->
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/checked/index.php"><i class="fa-solid fa-list-check"></i>&nbsp半年檢紀錄
                                                <?php if($numChecked == 0){?>
                                                    <span class="badge rounded-pill bg-danger"><i class="fa-solid fa-car-on"></i></span>
                                                <?php }?>
                                            </a>
                                        </li>
                                        
                                        <?php if($_SESSION[$sys_id]["role"] == "0" ){ ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="#"><i class="fa fa-check" aria-hidden="true"></i>x&nbsp商品庫存</a></li>
                                            <li><a class="dropdown-item" href="#">x&nbsp倉庫庫存</a></li>
                                            <li><a class="dropdown-item" href="#">x&nbsp出庫單</a></li>
                                            <li><a class="dropdown-item" href="#">x&nbsp入庫單</a></li>
                                            <li><a class="dropdown-item" href="#">x&nbsp調撥作業</a></li>
                                        <?php } ?>

                                    </ul>
                                </li>

                                <li class="nav-item dropdown">
                                    <a class="nav-link active dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-cart-shopping"></i>&nbsp需求與調撥&nbsp
                                        <?php if($num !=0){?>
                                            <span class="badge rounded-pill bg-danger"><?php echo $num; ?></span>
                                        <?php }?>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDarkDropdownMenuLink">
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/issue/">
                                                <i class="fa-solid fa-list"></i>&nbsp1需求單總表&nbsp
                                                <?php if($numIssue !=0){?>
                                                    <span class="badge rounded-pill bg-danger"><?php echo $numIssue; ?></span>
                                                <?php }?>
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/trade/">
                                                <i class="fa-solid fa-suitcase"></i>&nbsp2調撥作業&nbsp
                                                <?php if($numTrade !=0){?>
                                                    <span class="badge rounded-pill bg-danger"><?php echo $numTrade; ?></span>
                                                <?php }?>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="nav-item dropdown">
                                    <a class="nav-link active dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-sliders"></i>&nbsp進階設定</a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDarkDropdownMenuLink">
                                        <!-- <li><a class="dropdown-item" href="<php echo $webroot;?>/local/index.php?fab_id=<php echo $_SESSION[$sys_id]["fab_id"];?>">儲存點管理</a></li> -->
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/pno/"><i class="fa-solid fa-list"></i>&nbsp料號管理</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/catalog/index.php"><i class="fas fa-th-large"></i>&nbsp器材目錄管理</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/local/"><i class="fa-solid fa-location-dot"></i>&nbsp儲存點管理</a></li>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/supp/"><i class="fa-solid fa-address-book"></i>&nbsp供應商聯絡人管理</a></li>
                                        <!-- <li><hr class="dropdown-divider"></li> -->
                                        <!-- <li><a class="dropdown-item" href="<php echo $webroot;?>/checked/siteList.php">半年檢紀錄總表</a></li> -->
                                    </ul>
                                </li>

                            </ul>
                        </div>
                    <?php } ?>
                    <!-- 下拉式選單 -->
                <?php } ?>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if(!isset($_SESSION["AUTH"])){ ?>
                    <li class="nav-item mx-1">
                        <a href="<?php echo $webroot;?>/auth/login.php" class=""><i class="fa fa-sign-in" aria-hidden="true"></i> 登入</a>
                    </li>
                    <!-- <li class="nav-item mx-1">
                        <a href="<php echo $webroot;?>/auth/register.php" class="btn btn-success">註冊</a>
                    </li> -->
                <?php } else { ?>
                    <!-- 下拉式選單 -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDarkDropdown" aria-controls="navbarNavDarkDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                            <a class="nav-link active dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php if($_SESSION["AUTH"]["pass"] == "ldap"){?>
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                <?php } else {?>
                                    <i class="fa fa-user-secret" aria-hidden="true"></i>
                                <?php } ;?>
                                <?php if(isset($_SESSION[$sys_id]["site_title"])){
                                        echo "(".$_SESSION[$sys_id]["site_title"].") ";
                                      }
                                    echo $_SESSION["AUTH"]["cname"]; 
                                        if(isset($_SESSION[$sys_id])){ 
                                            echo '<sup class="text-danger"> - '.$_SESSION[$sys_id]["role"].'</sup>'; 
                                        }
                                    ?> 你好
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                                <?php if(isset($_SESSION[$sys_id])){ ?>
                                    <li><a class="dropdown-item" href="<?php echo $webroot;?>/auth/edit.php?user=<?php echo $_SESSION["AUTH"]["user"];?>"><i class="fa fa-user-circle" aria-hidden="true"></i> 編輯User資訊</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <?php if($_SESSION[$sys_id]["role"] <= "1"){ ?>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/auth/index.php"><i class="fa fa-address-card" aria-hidden="true"></i> 管理使用者</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                    <?php } ?>
                                    <?php } else {?>
                                        <li><a class="dropdown-item" href="<?php echo $webroot;?>/auth/login.php"><i class="fa fa-sign-in" aria-hidden="true"></i> SSO登入</a></li>
                                <?php } ?>
                                <li><a class="dropdown-item" href="<?php echo $webroot;?>/auth/logout.php" class=""><i class="fa fa-sign-out" aria-hidden="true"></i> 登出</a></li>
                            </ul>
                            </li>
                        </ul>
                    </div>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>