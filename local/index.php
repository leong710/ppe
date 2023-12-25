<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    require_once("function_dept.php");
    accessDenied($sys_id);
    
    // 新增
    if(isset($_POST["site_submit"])){
        store_site($_REQUEST);
        }
        if(isset($_POST["fab_submit"])){
            store_fab($_REQUEST);
        }
        if(isset($_POST["local_submit"])){
            store_local($_REQUEST);
    }
    
    // 調整flag ==> 20230712改用AJAX
    
    // 更新
    if(isset($_POST["edit_site_submit"])){
        update_site($_REQUEST);
        }
        if(isset($_POST["edit_fab_submit"])){
            update_fab($_REQUEST);
        }
        if(isset($_POST["edit_local_submit"])){
            update_local($_REQUEST);
    }
    // 刪除
    if(isset($_POST["delete_site"])){
        delete_site($_REQUEST);
        }
        if(isset($_POST["delete_fab"])){
            delete_fab($_REQUEST);
        }
        if(isset($_POST["delete_local"])){
            delete_local($_REQUEST);
    }

        if(isset($_POST["local_sort_submit"])){
            $locals = show_local($_REQUEST);
        }else{
            $showAll_local = array('fab_id' => 'All');
            $locals = show_local($showAll_local);
        }

    // 切換指定NAV分頁
    if(isset($_REQUEST["activeTab"])){
        $activeTab = $_REQUEST["activeTab"];
    }else{
        $activeTab = "2";       // 2 = local
    }

        // 3.組合查詢陣列
        $basic_query_arr = array(
            'sys_id' => $sys_id
        );

    $fabs = show_fab($basic_query_arr);
    $sites = show_site($basic_query_arr);

    $dept_lists = show_dept();
?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <!-- mloading JS -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <!-- mloading CSS -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <script>    
        // loading function
        function mloading(){
            $("body").mLoading({
                icon: "../../libs/jquery/Wedges-3s-120px.gif",
            }); 
        }
        // mloading();    // 畫面載入時開啟loading
    </script>
    <style>
        .tab-content.active {
            /* display: block; */
            animation: fadeIn 1s;
        }
        .nav-tabs .nav-link.active {
            /* color: #FFFFFF; */
            background-color: #84C1FF;
        }
        #result_table tr > th {
            color: blue;
            text-align: center;
            /* vertical-align: top;  */
            /* word-break: break-all;  */
        }
        #result_table tr > td {
            text-align: center;
            /* vertical-align: top;  */
            /* word-break: break-all;  */
        }

        .input-group {
            /* height: auto; */
            height: 100%;
        }
        .input-group .form-control,
        .input-group .btn {
            /* padding: 0.5rem; */
            /* line-height: 1.5; */
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <!-- <div class="col_xl_8 col-8 rounded my-0 p-3  bg-white rounded" style="background-color: rgba(255, 255, 255, .6);"> -->
            <div class="col_xl_8 col-8 bg-light rounded my-0 p-3" >
                <!-- NAV title -->
                <div class="row">
                    <!-- 分頁標籤 -->
                    <div class="col-12">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link" id="nav-site-tab"  data-bs-toggle="tab" data-bs-target="#nav-site_table"  type="button" role="tab" aria-controls="nav-site"  aria-selected="false">Site</button>
                                <button class="nav-link" id="nav-fab-tab"   data-bs-toggle="tab" data-bs-target="#nav-fab_table"   type="button" role="tab" aria-controls="nav-fab"   aria-selected="false">Fab</button>
                                <!-- class="active" aria-selected="true" -->
                                <button class="nav-link" id="nav-local-tab" data-bs-toggle="tab" data-bs-target="#nav-local_table" type="button" role="tab" aria-controls="nav-local" aria-selected="false">Local</button>
                                <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                                    <a class="nav-link" href="low_level.php" title="fab_安全存量設定"><i class="fa-solid fa-ban"></i>&nbsp安全存量設定</a>
                                <?php } ?>
                            </div>
                        </nav>
                    </div>
                </div>
                <!-- 內頁 -->
                <!-- <div id="table"> -->
                <div class="tab-content" id="nav-tabContent">
                    
                    <!-- site -->
                    <div id="nav-site_table" class="tab-pane fade" role="tabpanel" aria-labelledby="nav-site-tab">
                        <div class="row">
                            <div class="col-12 col-md-6 py-0">
                                <h3>site管理</h3>
                            </div>
                            <div class="col-12 col-md-6 py-0 text-end">
                                <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                    <button type="button" id="add_site_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_site" onclick="add_module('site')" > <i class="fa fa-plus"></i> 新增site</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="col-12 p-0">
                                <table>
                                    <thead>
                                        <tr class="">
                                            <th>ai</th>
                                            <th>site_title</th>
                                            <th>site_remark</th>
                                            <th>flag</th>
                                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>    
                                                <th>action</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <!-- 這裡開始抓SQL裡的紀錄來這裡放上 -->
                                    <tbody>
                                        <?php foreach($sites as $site){ ?>
                                            <tr>
                                                <td style="font-size: 6px;"><?php echo $site['id']; ?></td>
                                                <td style="text-align:left;"><?php echo $site['site_title']; ?></td>
                                                <td style="text-align:left;"><?php echo $site['site_remark']; ?></td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                                    <button type="button" name="site" id="<?php echo $site['id'];?>" class="btn btn-sm btn-xs flagBtn <?php echo $site['flag'] == 'On' ? 'btn-success':'btn-warning';?>" value="<?php echo $site['flag'];?>"><?php echo $site['flag'];?></button>
                                                    <?php }else{ ?>
                                                        <span class="btn btn-sm btn-xs <?php echo $site['flag'] == 'On' ? 'btn-success':'btn-warning';?>">
                                                            <?php echo $site['flag'] == 'On' ? '顯示':'隱藏';?>
                                                        </span>
                                                    <?php } ?></td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                                    <button type="button" id="edit_site_btn" value="<?php echo $site['id'];?>" class="btn btn-sm btn-xs btn-info" 
                                                        data-bs-toggle="modal" data-bs-target="#edit_site" onclick="edit_module('site',this.value)" >編輯</button>
                                                <?php } ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- fab -->
                    <div id="nav-fab_table" class="tab-pane fade" role="tabpanel" aria-labelledby="nav-fab-tab">
                        <div class="row">
                            <div class="col-12 col-md-8 py-0">
                                <h3>fab管理</h3>
                            </div>
                            <div class="col-12 col-md-4 py-0 text-end">
                                <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                    <button type="button" id="add_fab_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_fab" onclick="add_module('fab')" > <i class="fa fa-plus"></i> 新增fab</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="col-12 p-0">
                                <table>
                                    <thead>
                                        <tr class="">
                                            <th>ai</th>
                                            <th>site_id</th>
                                            <th>fab_title (remark)</th>
                                            <th>buy_ty</th>
                                            <th>sign_code</th>
                                            <th>pm_emp_id</th>
                                            <th>flag</th>
                                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>    
                                                <th>action</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <!-- 這裡開始抓SQL裡的紀錄來這裡放上 -->
                                    <tbody>
                                        <?php foreach($fabs as $fab){ ?>
                                            <tr>
                                                <td style="font-size: 6px;"><?php echo $fab['id']; ?></td>
                                                <td style="text-align:left;"><?php echo $fab['site_id']."_".$fab['site_title']." (".$fab['site_remark'].")"; if($fab["site_flag"] == "Off"){ ?><sup class="text-danger">-已關閉</sup><?php }  ?></td>
                                                <td style="text-align:left;"><?php echo $fab['fab_title']." (".$fab['fab_remark'].")"; ?></td>
                                                <td <?php echo $fab['buy_ty'] !='a' ? 'style="background-color: yellow;"':'';?>><?php echo $fab['buy_ty']; ?></td>
                                                <td style="text-align:center;"><?php echo $fab['sign_code']; ?></td>
                                                <td style="text-align:left; word-break: break-all;"><?php echo $fab['pm_emp_id']; ?></td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>   
                                                        <button type="button" name="fab" id="<?php echo $fab['id'];?>" class="btn btn-sm btn-xs flagBtn <?php echo $fab['flag'] == 'On' ? 'btn-success':'btn-warning';?>" value="<?php echo $fab['flag'];?>"><?php echo $fab['flag'];?></button>
                                                    <?php }else{ ?>
                                                        <span class="btn btn-sm btn-xs <?php echo $fab['flag'] == 'On' ? 'btn-success':'btn-warning';?>">
                                                            <?php echo $fab['flag'] == 'On' ? '顯示':'隱藏';?></span>
                                                    <?php } ?></td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>    
                                                    <button type="button" id="edit_fab_btn" value="<?php echo $fab['id'];?>" class="btn btn-sm btn-xs btn-info" 
                                                        data-bs-toggle="modal" data-bs-target="#edit_fab" onclick="edit_module('fab',this.value)" >編輯</button>
                                                <?php } ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Local class="show active" -->
                    <div id="nav-local_table" class="tab-pane fade" role="tabpanel" aria-labelledby="nav-local-tab">
                        <div class="row">
                            <div class="col-12 col-md-8 py-0">
                                <h3>Local儲存點管理</h3>
                            </div>
                            <div class="col-12 col-md-4 py-0 text-end">
                                <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                    <button type="button" id="add_local_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_local" onclick="add_module('local')" > <i class="fa fa-plus"></i> 新增Local</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="col-12 p-0">
                                <table>
                                    <thead>
                                        <tr class="">
                                            <th>ai</th>
                                            <th>fab_id</th>
                                            <th>local_title (remark)</th>
                                            <th>儲存點聯絡人</th>
                                            <th>low_level</th>
                                            <th>flag</th>
                                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>    
                                                <th>action</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <!-- 這裡開始抓SQL裡的紀錄來這裡放上 -->
                                    <tbody>
                                        <?php foreach($locals as $local){ ?>
                                            <tr>
                                                <td style="font-size: 6px;"><?php echo $local['id']; ?></td>
                                                <td style="text-align:left;"><?php echo $local['fab_id']."_".$local['fab_title']." (".$local['fab_remark'].")"; if($local["fab_flag"] == "Off"){ ?><sup class="text-danger">-已關閉</sup><?php } ?></td>
                                                <td style="text-align:left;"><?php echo $local['local_title']." (".$local['local_remark'].")"; ?></td>
                                                <td><?php echo $local['cname'];?></td>
                                                <td><a href="low_level.php?local_id=<?php echo $local['id'];?>" class="btn btn-sm btn-xs <?php echo !empty($local['low_level']) ? "btn-success":"btn-warning";?>">
                                                    <?php echo !empty($local['low_level']) ? "已設定":"未設定";?></a></td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>  
                                                        <button type="button" name="local" id="<?php echo $local['id'];?>" class="btn btn-sm btn-xs flagBtn <?php echo $local['flag'] == 'On' ? 'btn-success':'btn-warning';?>" value="<?php echo $local['flag'];?>"><?php echo $local['flag'];?></button>
                                                    <?php }else{ ?>
                                                        <span class="btn btn-sm btn-xs <?php echo $local['flag'] == 'On' ? 'btn-success':'btn-warning';?>">
                                                            <?php echo $local['flag'] == 'On' ? '顯示':'隱藏';?></span>
                                                    <?php } ?></td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>    
                                                    <button type="button" id="edit_local_btn" value="<?php echo $local['id'];?>" class="btn btn-sm btn-xs btn-info" 
                                                        data-bs-toggle="modal" data-bs-target="#edit_local" onclick="edit_module('local',this.value)" >編輯</button>
                                                <?php } ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                </div>
            </div>
        </div>
    </div>

<!-- 彈出畫面模組 新增編輯site-->
    <div class="modal fade" id="edit_site" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="site_modal_action"></span>site資訊</h4>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="site_delete_id">
                        <?php if($_SESSION[$sys_id]["role"] == 0){ ?>
                            &nbsp&nbsp&nbsp&nbsp&nbsp
                            <span id="site_modal_delect_btn"></span>
                        <?php } ?>
                    </form>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="" method="post">
                    <div class="modal-body px-4">
                        <div class="row">

                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <input type="text" name="site_title" class="form-control" id="edit_site_title" required placeholder="site名稱">
                                    <label for="edit_site_title" class="form-label">site_title/Site名稱：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <input type="text" name="site_remark" class="form-control" id="edit_site_remark" required placeholder="註解說明">
                                    <label for="edit_site_remark" class="form-label">site_remark/註解說明：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 py-1">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="edit_flag" class="form-label">flag/顯示開關：</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="On" id="edit_site_On" class="form-check-input" checked>&nbsp
                                            <label for="edit_site_On" class="form-check-label">On</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="Off" id="edit_site_Off" class="form-check-input">&nbsp
                                            <label for="edit_site_Off" class="form-check-label">Off</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <!-- 最後編輯資訊 -->
                            <div class="col-12 text-end p-0" id="edit_site_info"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="activeTab" value="0">
                            <input type="hidden" name="id" id="site_edit_id" >
                            <input type="hidden" value="<?php echo $_SESSION["AUTH"]["cname"];?>" name="updated_user">
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>   
                            <?php } ?>
                            <input type="reset" class="btn btn-info" id="site_reset_btn" value="清除">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>
<!-- 彈出畫面模組 新增編輯fab-->
    <div class="modal fade" id="edit_fab" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="fab_modal_action"></span>fab資訊</h4>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="fab_delete_id">
                        <?php if($_SESSION[$sys_id]["role"] == 0){ ?>
                            &nbsp&nbsp&nbsp&nbsp&nbsp
                            <span id="fab_modal_delect_btn"></span>
                        <?php } ?>
                    </form>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="" method="post">
                    <div class="modal-body px-4">
                        <!-- 第一 -->
                        <div class="row">
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <select name="site_id" id="edit_site_id" class="form-select" required>
                                        <option value="" selected hidden>-- 請選擇site級別 --</option>
                                        <?php foreach($sites as $site){ ?>
                                            <option value="<?php echo $site["id"];?>">
                                                <?php echo $site["id"]."_".$site["site_title"]."(".$site["site_remark"].")"; echo ($site["flag"] == "Off") ? ' -- 已關閉':'';?></option>
                                        <?php } ?>
                                    </select> 
                                    <label for="edit_site_id" class="form-label">site_id/所屬site：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <select name="sign_code" id="edit_sign_code" class="form-select" required>
                                        <option value="" hidden selected>-- 選擇管理單位 --</option>
                                        <?php foreach($dept_lists as $dept_li){ ?>
                                            <option value="<?php echo $dept_li['sign_code'];?>" <?php echo ($dept_li['up_dep'] == '') ? "selected":"";?> >
                                                <?php echo $dept_li['up_sign_dept'] != "" ? $dept_li['up_sign_dept']." / ":""; echo $dept_li['sign_dept']." (".$dept_li['sign_code'].") ".$dept_li['dept_sir'];?></option>
                                        <?php } ?>
                                    </select>
                                    <label for="edit_sign_code" class="form-label">sign_code/上層組織：<sup class='text-danger'> *</sup></label>
                                </div>
                            </div>
                        </div>
                        <!-- 第二 -->
                        <div class="row">
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text" name="fab_title" class="form-control" id="edit_fab_title" required placeholder="Fab名稱">
                                    <label for="edit_fab_title" class="form-label">fab_title/廠級分類名稱：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text" name="fab_remark" class="form-control" id="edit_fab_remark" required placeholder="註解說明">
                                    <label for="edit_fab_remark" class="form-label">fab_remark/註解說明：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                        </div>
                        <!-- 第三 -->
                        <div class="row">
                            <div class="col-12 col-md-6 py-1">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="edit_buy_ty" class="form-label">安量倍數(限購)：</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="buy_ty" value="a" id="edit_buy_a" class="form-check-input" required >
                                            <label for="edit_buy_a" class="form-check-label">&nbspa.安量倍數</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="buy_ty" value="b" id="edit_buy_b" class="form-check-input" required >
                                            <label for="edit_buy_b" class="form-check-label">&nbspb.安量倍數</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-12 col-md-6 py-1">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="edit_flag" class="form-label">flag/顯示開關：</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="On" id="edit_fab_On" class="form-check-input" >&nbsp
                                            <label for="edit_fab_On" class="form-check-label">On</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="Off" id="edit_fab_Off" class="form-check-input">&nbsp
                                            <label for="edit_fab_Off" class="form-check-label">Off</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- 第四 -->
                        <div class="row">
                            <div class="col-12">
                                <label for="" class="from-label">fab PM管理員：(-- 請選擇業務承辦 --)</label><br>
                                <div class="col-12 p-3 border rounded" id="selectUser">
                                    <div class="row">
                                        <!-- 第一排的功能 : 顯示已加入名單+input -->
                                        <div class="col-12 px-4 py-0">
                                            <div id="selectUserItem"></div>
                                            <input type="hidden" class="form-control" name="pm_emp_id[]" id="edit_pm_emp_id" placeholder="已加入的姓名">
                                        </div>
                                        <!-- 第二排的功能 : 搜尋功能 -->
                                        <div class="col-12 px-4 py-0">
                                            <div class="input-group search" id="selectUserForm">
                                                <input type="text" class="form-control" id="key_word" placeholder="請輸入工號" aria-label="請輸入工號" aria-describedby="button-addon2" >
                                                <button class="btn btn-outline-secondary" type="button" onclick="search_fun();">查詢</button>
                                                <button class="btn btn-outline-secondary" type="button" onclick="resetMain();">清除</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 最後編輯資訊 -->
                        <div class="col-12 text-end p-0" id="edit_fab_info"></div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="activeTab" value="1">
                            <input type="hidden" name="id" id="fab_edit_id" >
                            <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>   
                                <span id="fab_modal_button"></span>
                            <?php } ?>
                            <input type="reset" class="btn btn-info" id="fab_reset_btn" value="清除">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>
<!-- 彈出畫面模組 新增編輯Local-->
    <div class="modal fade" id="edit_local" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="local_modal_action"></span>local資訊</h4>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="local_delete_id">
                        <?php if($_SESSION[$sys_id]["role"] == 0){ ?>
                            &nbsp&nbsp&nbsp&nbsp&nbsp
                            <span id="local_modal_delect_btn"></span>
                        <?php } ?>
                    </form>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body px-5">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-floating">
                                    <select name="fab_id" id="edit_fab_id" class="form-select" required <?php echo ($_SESSION[$sys_id]["role"] > 1) ? "disabled":"";?>>
                                        <option value="" hidden>--請選擇fab廠別--</option>
                                        <?php foreach($fabs as $fab){ ?>
                                            <option value="<?php echo $fab["id"];?>" >
                                                <?php echo $fab["id"]."_".$fab["fab_title"]."(".$fab["fab_remark"].")"; echo ($fab["flag"] == "Off") ? ' -- 已關閉':''; ?></option>
                                        <?php } ?>
                                    </select>
                                    <label for="edit_fab_id" class="form-label">fab_id：<sup class="text-danger"><?php echo ($_SESSION[$sys_id]["role"] > 1) ? " - disabled":" *"; ?></sup></label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" name="local_title" id="edit_local_title" class="form-control" required placeholder="local名稱">
                                    <label for="edit_local_title" class="form-label">local_title/名稱：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" name="local_remark" id="edit_local_remark" class="form-control" required placeholder="註解說明">
                                    <label for="edit_local_remark" class="form-label">local_remark/備註說明：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <!-- <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" name="low_level" id="edit_low_level" class="form-control" required placeholder="安全水位">
                                    <label for="edit_low_level" class="form-label">low_level/安全水位：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div> -->
                            <div class="col-12">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="edit_flag" class="form-label">flag/顯示開關：</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="On" id="edit_local_On" class="form-check-input" >&nbsp
                                            <label for="edit_local_On" class="form-check-label">On</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="Off" id="edit_local_Off" class="form-check-input">&nbsp
                                            <label for="edit_local_Off" class="form-check-label">Off</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <!-- 最後編輯資訊 -->
                            <div class="col-12 text-end p-0" id="edit_local_info"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="activeTab" value="2">
                            <input type="hidden" name="id" id="local_edit_id" >
                            <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>   
                                <span id="local_modal_button"></span>
                            <?php } ?>
                            <input type="reset" class="btn btn-info" id="local_reset_btn" value="清除">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>

<!-- goTop滾動畫面DIV 2/4-->
    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
</body>

<!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>
<!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
<script src="../../libs/sweetalert/sweetalert.min.js"></script>

<script>

    var site   = <?=json_encode($sites);?>;                                                     // 引入sites資料
    var fab    = <?=json_encode($fabs);?>;                                                      // 引入fabs資料
    var local  = <?=json_encode($locals);?>;                                                    // 引入locals資料
    var site_item   = ['id','site_title','site_remark','flag'];                                 // 交給其他功能帶入 delete_site_id
    var fab_item    = ['id','site_id','fab_title','fab_remark','sign_code','pm_emp_id','buy_ty','flag'];    // 交給其他功能帶入 delete_fab_id
    var local_item  = ['id','fab_id','local_title','local_remark','flag'];                      // 交給其他功能帶入 delete_local_id

    function add_module(to_module){     // 啟用新增模式
        $('#'+to_module+'_modal_action, #'+to_module+'_modal_button, #'+to_module+'_modal_delect_btn, #edit_'+to_module+'_info').empty();   // 清除model功能
        $('#'+to_module+'_reset_btn').click();                                                        // reset清除表單
        var add_btn = '<input type="submit" name="'+to_module+'_submit" class="btn btn-primary" value="新增">';
        $('#'+to_module+'_modal_action').append('新增');                      // model標題
        $('#'+to_module+'_modal_button').append(add_btn);                     // 儲存鈕
        var reset_btn = document.getElementById(to_module+'_reset_btn');   // 指定清除按鈕
        reset_btn.classList.remove('unblock');                  // 新增模式 = 解除
        document.querySelector("#edit_"+to_module+" .modal-header").classList.remove('edit_mode_bgc');
        document.querySelector("#edit_"+to_module+" .modal-header").classList.add('add_mode_bgc');
    }
    // fun-1.鋪編輯畫面
    function edit_module(to_module, row_id){
        $('#'+to_module+'_modal_action, #'+to_module+'_modal_button, #'+to_module+'_modal_delect_btn, #edit_'+to_module+'_info').empty();   // 清除model功能
        $('#'+to_module+'_reset_btn').click();                                                        // reset清除表單
        var reset_btn = document.getElementById(to_module+'_reset_btn');   // 指定清除按鈕
        reset_btn.classList.add('unblock');                     // 編輯模式 = 隱藏
        document.querySelector("#edit_"+to_module+" .modal-header").classList.remove('add_mode_bgc');
        document.querySelector("#edit_"+to_module+" .modal-header").classList.add('edit_mode_bgc');
        // 參數說明: to_module = 來源與目的 site、fab、local
        $('#edit_pm_emp_id').value = '';
        $('#selectUserItem').empty();
        tags = [];                                                      // 清除tag名單陣列
        // step1.將原排程陣列逐筆繞出來
        Object(window[to_module]).forEach(function(row){          
            if(row['id'] == row_id){
                // step2.鋪畫面到module
                Object(window[to_module+'_item']).forEach(function(item_key){
                    if(item_key == 'id'){
                        document.querySelector('#'+to_module+'_delete_id').value = row['id'];       // 鋪上delete_id = this id.no for delete form
                        document.querySelector('#'+to_module+'_edit_id').value = row['id'];         // 鋪上edit_id = this id.no for edit form
                    }else if(item_key == 'flag'){
                        document.querySelector('#edit_'+to_module+' #edit_'+to_module+'_'+row[item_key]).checked = true;
                    }else if(item_key == 'pm_emp_id'){                          // 20231108_pm_emp_id多名單
                        // 第0階段：套用既有數據
                        var intt_val_str = row['pm_emp_id'];                    // 引入PM資料
                        var intt_val = [];
                        // if(intt_val_str.length !== 0){                          // 過濾原本pm字串不能為空
                        if(intt_val_str){                                       // 過濾原本pm字串不能為空
                            intt_val = intt_val_str.split(',');                 // 直接使用 split 方法得到陣列
                            for(let i=0; i < intt_val.length; i=i+2){   
                                tagsInput_me(intt_val[i]+','+intt_val[i+1]);    // 利用合併帶入
                            }
                        }

                    }else if(item_key == 'buy_ty'){
                        document.querySelector('#edit_'+to_module+' #edit_buy_'+row[item_key]).checked = true;
                    }else{
                        document.querySelector('#edit_'+to_module+' #edit_'+item_key).value = row[item_key]; 
                    }
                })

                // 鋪上最後更新
                let to_module_info = '最後更新：'+row['updated_at']+' / by '+row['updated_user'];
                document.querySelector('#edit_'+to_module+'_info').innerHTML = to_module_info;

                // step3-3.開啟 彈出畫面模組 for user編輯
                // edit_myTodo_btn.click();
                var add_btn = '<input type="submit" name="edit_'+to_module+'_submit" class="btn btn-primary" value="儲存">';
                var del_btn = '<input type="submit" name="delete_'+to_module+'" value="刪除'+to_module+'" class="btn btn-sm btn-xs btn-danger" onclick="return confirm(`確認刪除？`)">';
                $('#'+to_module+'_modal_action').append('編輯');          // model標題
                $('#'+to_module+'_modal_delect_btn').append(del_btn);     // 刪除鈕
                $('#'+to_module+'_modal_button').append(add_btn);         // 儲存鈕
            }
        })
    }

    // 切換上架/下架開關
    let flagBtns = [...document.querySelectorAll('.flagBtn')];
    for(let flagBtn of flagBtns){
        flagBtn.onclick = e => {
            let swal_content = e.target.name+'_id:'+e.target.id+'=';
            // console.log('e:',e.target.name,e.target.id);
            $.ajax({
                url:'api.php',
                method:'post',
                async: false,                                           // ajax取得數據包後，可以return的重要參數
                dataType:'json',
                data:{
                    function: 'cheng_flag',           // 操作功能
                    table: e.target.name,
                    id: e.target.id,
                    flag: e.target.value
                },
                success: function(res){
                    let res_r = res["result"];
                    let res_r_flag = res_r["flag"];
                    // console.log(res_r_flag);
                    if(res_r_flag == 'Off'){
                        e.target.classList.remove('btn-success');
                        e.target.classList.add('btn-warning');
                        e.target.value = 'Off';
                        e.target.innerText = 'Off';
                    }else{
                        e.target.classList.remove('btn-warning');
                        e.target.classList.add('btn-success');
                        e.target.value = 'On';
                        e.target.innerText = 'On';
                    }
                    swal_action = 'success';
                    swal_content += res_r_flag+' 套用成功';
                },
                error: function(e){
                    swal_action = 'error';
                    swal_content += res_r_flag+' 套用失敗';
                    console.log("error");
                }
            });

            // swal('套用人事資料' ,swal_content ,swal_action, {buttons: false, timer:2000}).then(()=>{location.href = url;});     // deley3秒，then自動跳轉畫面
            swal('change_flag' ,swal_content ,swal_action, {buttons: false, timer:1000});

        }
    }



// // // 第三頁：searchUser function 
    // // fun3-1：search Key_word
    var tags = [];
    function search_fun(){
        mloading("show");                       // 啟用mLoading
        let search = $('.search > input').val().trim();
        search = search.trim();
        if(!search || (search.length < 8)){
            alert("查詢工號字數最少 8 個字以上!!");
            $("body").mLoading("hide");
            return false;
        } 

        $.ajax({
            url:'http://tneship.cminl.oa/hrdb/api/index.php',       // 正式
            method:'get',
            dataType:'json',
            data:{
                functionname: 'showStaff',                          // 操作功能
                uuid: '39aad298-a041-11ed-8ed4-2cfda183ef4f',
                search: search                                      // 查詢對象key_word
            },
            success: function(res){
                var res_r = res["result"];
                // 將結果進行渲染
                if (res_r !== '') {
                    var obj_val = res_r;                                         // 取Object物件0
                    if(obj_val){     
                        // $('#selectUserItem').append('<div class="tag">' + obj_val.cname + '<span class="remove">x</span></div>');
                        // tags.push(obj_val.emp_id, obj_val.cname);
                        // let edit_pm_emp_id = document.getElementById('edit_pm_emp_id');
                        // if(edit_pm_emp_id){
                        //     edit_pm_emp_id.value = tags;
                        // }
                        var com_val = obj_val.emp_id+','+obj_val.cname;
                        tagsInput_me(com_val);

                    }else{
                        alert('查無工號：'+ search +' !!');
                    }
                }
            },
            error (){
                console.log("search error");
            }
        })
        document.querySelector('#key_word').value = '';
        $("body").mLoading("hide");
    }
    // // fun3-2：移除單項模組
    $('#selectUserItem').on('click', '.remove', function() {
        var tagIndex = $(this).closest('.tag').index();
        let tagg = tags[tagIndex];                       // 取得目標數值 emp_id,cname
        let emp_id = tagg.substr(0, tagg.search(','));   // 指定 emp_id
        let tag_user = document.getElementById(emp_id);
        if(tag_user){
            tag_user.value = tagg;
        }
        tags.splice(tagIndex, 1);           // 自陣列中移除
        $(this).closest('.tag').remove();   // 自畫面中移除
        let edit_pm_emp_id = document.getElementById('edit_pm_emp_id');
        if(edit_pm_emp_id){
            edit_pm_emp_id.value = tags;
        }
    });
    // // fun3-3：清除search keyWord
    function resetMain(){
        $("#result").removeClass("border rounded bg-white");
        $('#result_table').empty();
        document.querySelector('#key_word').value = '';
    }
    // // fun3-3：選染功能
    function tagsInput_me(val) {
        let emp_id = val.substr(0, val.search(','));    // 取第1位 指定emp_id
        let cname = val.substr(val.search(',',)+1);     // 取第2位 指定cname
        if (val !== '') {
            tags.push(val);
            $('#selectUserItem').append('<div class="tag">' + cname + '<span class="remove">x</span></div>');
            let tag_user = document.getElementById(emp_id);
            if(tag_user){
                tag_user.value = '';
            }
            let edit_pm_emp_id = document.getElementById('edit_pm_emp_id');
            if(edit_pm_emp_id){
                edit_pm_emp_id.value = tags;
            }
        }
    }
// // // 第三頁：searchUser function 


    $(function () {
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();

        // 20230817 禁用Enter鍵表單自動提交 
        document.onkeydown = function(event) { 
            var target, code, tag; 
            if (!event) { 
                event = window.event;       //針對ie瀏覽器 
                target = event.srcElement; 
                code = event.keyCode; 
                if (code == 13) { 
                    tag = target.tagName; 
                    if (tag == "TEXTAREA") { return true; } 
                    else { return false; } 
                } 
            } else { 
                target = event.target;      //針對遵循w3c標準的瀏覽器，如Firefox 
                code = event.keyCode; 
                if (code == 13) { 
                    tag = target.tagName; 
                    if (tag == "INPUT") { return false; } 
                    else { return true; } 
                } 
            } 
        };
    })

    $(document).ready(function(){
        // 切換指定NAV分頁
            //设置要自动选中的选项卡的索引（从0开始）
            var activeTab = '<?=$activeTab;?>';
            //激活选项卡
            $('.nav-tabs button:eq(' + activeTab + ')').tab('show');
    });



</script>

<?php include("../template/footer.php"); ?>