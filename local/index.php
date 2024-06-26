<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../user_info.php");
    require_once("function.php");
    require_once("function_dept.php");
    require_once("service_window_api.php");             // service window
    accessDenied($sys_id);
    
    // 新增C
    if(isset($_POST["site_submit"])){ store_site($_REQUEST); }
    if(isset($_POST["fab_submit"])){ store_fab($_REQUEST); }
    if(isset($_POST["local_submit"])){ store_local($_REQUEST); }
    if(isset($_POST["ptlocal_submit"])){ store_ptlocal($_REQUEST); }
    // 更新U
    if(isset($_POST["edit_site_submit"])){ update_site($_REQUEST); }
    if(isset($_POST["edit_fab_submit"])){ update_fab($_REQUEST); }
    if(isset($_POST["edit_local_submit"])){ update_local($_REQUEST); }
    if(isset($_POST["edit_ptlocal_submit"])){ update_ptlocal($_REQUEST); }
    // 刪除D
    if(isset($_POST["delete_site"])){ delete_site($_REQUEST); }
    if(isset($_POST["delete_fab"])){ delete_fab($_REQUEST); }
    if(isset($_POST["delete_local"])){ delete_local($_REQUEST); }
    if(isset($_POST["delete_ptlocal"])){ delete_ptlocal($_REQUEST); }
    // 調整flag ==> 20230712改用AJAX
    
    // 3.組合查詢陣列
    $query_arr = array(
        'sys_id' => $sys_id,
        'fab_id' => 'All'
    );

    $locals = (isset($_POST["local_sort_submit"])) ? show_local($_REQUEST) : show_local($query_arr);

    $ptlocals   = show_ptlocal($query_arr);
    $fabs       = show_fab($query_arr);
    $sites      = show_site($query_arr);
    $dept_lists = show_dept();

    // 切換指定NAV分頁
    $activeTab = (isset($_REQUEST["activeTab"])) ? $_REQUEST["activeTab"] : "2";       // 2 = local

    if(isset($_GET["make_server_window"])){ make_server_window($fabs); }

    $sw_arr = (array) json_decode($sw_json);                // service window 物件轉陣列

?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <script src="../../libs/jquery/mloading_init.js"></script>
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
            <div class="col_xl_8 col-8 bg-light rounded my-0 p-3" >
                <!-- NAV title -->
                <div class="row">
                    <!-- 分頁標籤 -->
                    <div class="col-12">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link" id="nav-site-tab"  data-bs-toggle="tab" data-bs-target="#nav-site_table"  type="button" role="tab" aria-controls="nav-site"  aria-selected="false">Site</button>
                                <button class="nav-link" id="nav-fab-tab"   data-bs-toggle="tab" data-bs-target="#nav-fab_table"   type="button" role="tab" aria-controls="nav-fab"   aria-selected="false">Fab</button>
                                <button class="nav-link" id="nav-local-tab" data-bs-toggle="tab" data-bs-target="#nav-local_table" type="button" role="tab" aria-controls="nav-local" aria-selected="false">Local</button>
                                <?php if($sys_role <= 2){ ?>
                                    <a class="nav-link" href="low_level.php" title="fab_安全存量設定"><i class="fa-solid fa-ban"></i>&nbsp安全存量設定</a>
                                    <?php } ?>
                                <button class="nav-link" id="nav-ptlocal-tab" data-bs-toggle="tab" data-bs-target="#nav-ptlocal_table" type="button" role="tab" aria-controls="nav-ptlocal" aria-selected="false">除汙儲存點</button>
                            </div>
                        </nav>
                    </div>
                </div>
                <div class="tab-content" id="nav-tabContent">
                    <!-- site -->
                    <div id="nav-site_table" class="tab-pane fade" role="tabpanel" aria-labelledby="nav-site-tab">
                        <div class="row">
                            <div class="col-12 col-md-6 py-0">
                                <h3>site管理</h3>
                            </div>
                            <div class="col-12 col-md-6 py-0 text-end">
                                <?php if($sys_role <= 1){ ?>
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
                                            <?php if($sys_role <= 1){ ?>    
                                                <th>action</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($sites as $site){ ?>
                                            <tr>
                                                <td style="font-size: 12px;"><?php echo $site['id']; ?></td>
                                                <td class="text-start"><?php echo $site['site_title']; ?></td>
                                                <td class="text-start"><?php echo $site['site_remark']; ?></td>
                                                <td><?php if($sys_role <= 1){ ?>
                                                    <button type="button" name="site" id="<?php echo $site['id'];?>" class="btn btn-sm btn-xs flagBtn <?php echo $site['flag'] == 'On' ? 'btn-success':'btn-warning';?>" value="<?php echo $site['flag'];?>"><?php echo $site['flag'];?></button>
                                                    <?php }else{ ?>
                                                        <span class="btn btn-sm btn-xs <?php echo $site['flag'] == 'On' ? 'btn-success':'btn-warning';?>">
                                                            <?php echo $site['flag'] == 'On' ? '顯示':'隱藏';?>
                                                        </span>
                                                    <?php } ?></td>
                                                <td><?php if($sys_role <= 1){ ?>
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
                                <?php if($sys_role <= 1){ ?>
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
                                            <th style="width: 25%;">pm_emp_id</th>
                                            <th>flag</th>
                                            <?php if($sys_role <= 1){ ?>    
                                                <th>action</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($fabs as $fab){ ?>
                                            <tr>
                                                <td style="font-size: 12px;"><?php echo $fab['id']; ?></td>
                                                <td class="text-start"><?php echo $fab['site_id']."_".$fab['site_title']." (".$fab['site_remark'].")"; if($fab["site_flag"] == "Off"){ ?><sup class="text-danger">-已關閉</sup><?php }  ?></td>
                                                <td class="text-start"><?php echo $fab['fab_title']." (".$fab['fab_remark'].")"; ?></td>
                                                <td <?php echo $fab['buy_ty'] !='a' ? 'style="background-color: yellow;"':'';?>><?php echo $fab['buy_ty']; ?></td>
                                                <td class="text-center"><?php echo $fab['sign_code']; ?></td>
                                                <td class="word_bk"><?php echo $fab['pm_emp_id']; ?></td>
                                                <td><?php if($sys_role <= 1){ ?>   
                                                        <button type="button" name="fab" id="<?php echo $fab['id'];?>" class="btn btn-sm btn-xs flagBtn <?php echo $fab['flag'] == 'On' ? 'btn-success':'btn-warning';?>" value="<?php echo $fab['flag'];?>"><?php echo $fab['flag'];?></button>
                                                    <?php }else{ ?>
                                                        <span class="btn btn-sm btn-xs <?php echo $fab['flag'] == 'On' ? 'btn-success':'btn-warning';?>">
                                                            <?php echo $fab['flag'] == 'On' ? '顯示':'隱藏';?></span>
                                                    <?php } ?></td>
                                                <td><?php if($sys_role <= 1){ ?>    
                                                    <button type="button" id="edit_fab_btn" value="<?php echo $fab['id'];?>" class="btn btn-sm btn-xs btn-info" 
                                                        data-bs-toggle="modal" data-bs-target="#edit_fab" onclick="edit_module('fab',this.value)" >編輯</button>
                                                <?php } ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12 col-md-8 ">
                                <h5>Service Window清單</h5><snap> >>> 上表有更新時，請記得套用更新!! 此清單會顯示在receive領用總表上，供民眾查詢使用 ~</snap>
                            </div>
                            <div class="col-12 col-md-4  text-end">
                                <?php if($sys_role <= 1){ ?>
                                    <button type="button" id="add_fab_btn" class="btn btn-primary" onclick="return confirm(`確認更新service window清單嗎？`) && update_sw();" ><i class="fa-solid fa-arrows-rotate"></i> 套用更新</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-12 border rounded p-3 ">
                        <!-- <div class="col-12"> -->
                            <table id="service_window">
                                <thead>
                                    <tr>
                                        <th>FAB</th>
                                        <th>窗口姓名</th>
                                        <th>分機</th>
                                        <th>email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Local class="show active" -->
                    <div id="nav-local_table" class="tab-pane fade" role="tabpanel" aria-labelledby="nav-local-tab">
                        <div class="row">
                            <div class="col-12 col-md-8 py-0">
                                <h3>Local儲存點管理</h3>
                            </div>
                            <div class="col-12 col-md-4 py-0 text-end">
                                <?php if($sys_role <= 1){ ?>
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
                                            <th>low_level</th>
                                            <th>flag</th>
                                            <?php if($sys_role <= 1){ ?>    
                                                <th>action</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($locals as $local){ ?>
                                            <tr>
                                                <td style="font-size: 12px;"><?php echo $local['id']; ?></td>
                                                <td class="text-start"><?php echo $local['fab_id']."_".$local['fab_title']." (".$local['fab_remark'].")"; if($local["fab_flag"] == "Off"){ ?><sup class="text-danger">-已關閉</sup><?php } ?></td>
                                                <td class="text-start"><?php echo $local['local_title']." (".$local['local_remark'].")"; ?></td>
                                                <td><a href="low_level.php?local_id=<?php echo $local['id'];?>" class="btn btn-sm btn-xs <?php echo !empty($local['low_level']) ? "btn-success":"btn-warning";?>">
                                                    <?php echo !empty($local['low_level']) ? "已設定":"未設定";?></a></td>
                                                <td><?php if($sys_role <= 1){ ?>  
                                                        <button type="button" name="local" id="<?php echo $local['id'];?>" class="btn btn-sm btn-xs flagBtn <?php echo $local['flag'] == 'On' ? 'btn-success':'btn-warning';?>" value="<?php echo $local['flag'];?>"><?php echo $local['flag'];?></button>
                                                    <?php }else{ ?>
                                                        <span class="btn btn-sm btn-xs <?php echo $local['flag'] == 'On' ? 'btn-success':'btn-warning';?>">
                                                            <?php echo $local['flag'] == 'On' ? '顯示':'隱藏';?></span>
                                                    <?php } ?></td>
                                                <td><?php if($sys_role <= 1){ ?>    
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

                    <!-- pt Local 20240122 -->
                    <div id="nav-ptlocal_table" class="tab-pane fade" role="tabpanel" aria-labelledby="nav-local-tab">
                        <div class="row">
                            <div class="col-12 col-md-8 py-0">
                                <h3>除汙儲存點管理</h3>
                            </div>
                            <div class="col-12 col-md-4 py-0 text-end">
                                <?php if($sys_role <= 1){ ?>
                                    <button type="button" id="add_ptlocal_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_ptlocal" onclick="add_module('ptlocal')" > <i class="fa fa-plus"></i> 新增除汙儲存點</button>
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
                                            <th>low_level</th>
                                            <th>flag</th>
                                            <?php if($sys_role <= 1){ ?>    
                                                <th>action</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($ptlocals as $ptlocal){ ?>
                                            <tr>
                                                <td style="font-size: 12px;"><?php echo $ptlocal['id']; ?></td>
                                                <td class="text-start"><?php echo $ptlocal['fab_id']."_".$ptlocal['fab_title']." (".$ptlocal['fab_remark'].")"; if($ptlocal["fab_flag"] == "Off"){ ?><sup class="text-danger">-已關閉</sup><?php } ?></td>
                                                <td class="text-start"><?php echo $ptlocal['local_title']." (".$ptlocal['local_remark'].")"; ?></td>
                                                <td><a href="low_level.php?ptlocal_id=<?php echo $ptlocal['id'];?>" class="btn btn-sm btn-xs <?php echo !empty($ptlocal['low_level']) ? "btn-success":"btn-warning";?>">
                                                    <?php echo !empty($ptlocal['low_level']) ? "已設定":"未設定";?></a></td>
                                                <td><?php if($sys_role <= 1){ ?>  
                                                        <button type="button" name="ptlocal" id="<?php echo $ptlocal['id'];?>" class="btn btn-sm btn-xs flagBtn <?php echo $ptlocal['flag'] == 'On' ? 'btn-success':'btn-warning';?>" value="<?php echo $ptlocal['flag'];?>"><?php echo $ptlocal['flag'];?></button>
                                                    <?php }else{ ?>
                                                        <span class="btn btn-sm btn-xs <?php echo $ptlocal['flag'] == 'On' ? 'btn-success':'btn-warning';?>">
                                                            <?php echo $ptlocal['flag'] == 'On' ? '顯示':'隱藏';?></span>
                                                    <?php } ?></td>
                                                <td><?php if($sys_role <= 1){ ?>    
                                                    <button type="button" id="edit_ptlocal_btn" value="<?php echo $ptlocal['id'];?>" class="btn btn-sm btn-xs btn-info" 
                                                        data-bs-toggle="modal" data-bs-target="#edit_ptlocal" onclick="edit_module('ptlocal',this.value)" >編輯</button>
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

<!-- 模組 新增編輯site-->
    <div class="modal fade" id="edit_site" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="site_modal_action"></span>site資訊</h4>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="site_delete_id">
                        &nbsp&nbsp&nbsp&nbsp&nbsp
                        <span id="site_modal_delect_btn" class="<?php echo ($sys_role == 0) ? "":" unblock ";?>"></span>
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
                            <div class="col-12 text-end p-0" id="edit_site_info"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="activeTab" value="0">
                            <input type="hidden" name="id" id="site_edit_id" >
                            <input type="hidden" value="<?php echo $_SESSION["AUTH"]["cname"];?>" name="updated_user">
                                <span id="site_modal_button" class="<?php echo ($sys_role <= 1) ? "":" unblock ";?>"></span>
                            <input type="reset" class="btn btn-info" id="site_reset_btn" value="清除">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>
<!-- 模組 新增編輯fab-->
    <div class="modal fade" id="edit_fab" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="fab_modal_action"></span>fab資訊</h4>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="fab_delete_id">
                        &nbsp&nbsp&nbsp&nbsp&nbsp
                        <span id="fab_modal_delect_btn" class="<?php echo ($sys_role == 0) ? "":" unblock ";?>"></span>
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
                        <div class="col-12 text-end p-0" id="edit_fab_info"></div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="activeTab" value="1">
                            <input type="hidden" name="id" id="fab_edit_id" >
                            <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                <span id="fab_modal_button" class="<?php echo ($sys_role <= 1) ? "":" unblock ";?>"></span>
                            <input type="reset" class="btn btn-info" id="fab_reset_btn" value="清除">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>
<!-- 模組 新增編輯Local-->
    <div class="modal fade" id="edit_local" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="local_modal_action"></span>local資訊</h4>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="local_delete_id">
                        &nbsp&nbsp&nbsp&nbsp&nbsp
                        <span id="local_modal_delect_btn" class="<?php echo ($sys_role == 0) ? "":" unblock ";?>"></span>
                    </form>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body px-5">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-floating">
                                    <select name="fab_id" id="edit_fab_id" class="form-select" required <?php echo ($sys_role > 1) ? "disabled":"";?>>
                                        <option value="" hidden>--請選擇fab廠別--</option>
                                        <?php foreach($fabs as $fab){ ?>
                                            <option value="<?php echo $fab["id"];?>" >
                                                <?php echo $fab["id"]."_".$fab["fab_title"]."(".$fab["fab_remark"].")"; echo ($fab["flag"] == "Off") ? ' -- 已關閉':''; ?></option>
                                        <?php } ?>
                                    </select>
                                    <label for="edit_fab_id" class="form-label">fab_id：<sup class="text-danger"><?php echo ($sys_role > 1) ? " - disabled":" *"; ?></sup></label>
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
                            <div class="col-12">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="edit_flag" class="form-label">flag/顯示開關：</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="On" id="edit_local_On" class="form-check-input" checked>&nbsp
                                            <label for="edit_local_On" class="form-check-label">On</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="Off" id="edit_local_Off" class="form-check-input">&nbsp
                                            <label for="edit_local_Off" class="form-check-label">Off</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-12 text-end p-0" id="edit_local_info"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="activeTab" value="2">
                            <input type="hidden" name="id" id="local_edit_id" >
                            <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                <span id="local_modal_button" class="<?php echo ($sys_role <= 1) ? "":" unblock ";?>"></span>
                            <input type="reset" class="btn btn-info" id="local_reset_btn" value="清除">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>
<!-- 模組 新增編輯ptLocal 20240122 -->
    <div class="modal fade" id="edit_ptlocal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="ptlocal_modal_action"></span>除汙儲存點資訊</h4>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="ptlocal_delete_id">
                        &nbsp&nbsp&nbsp&nbsp&nbsp
                        <span id="ptlocal_modal_delect_btn" class="<?php echo ($sys_role == 0) ? "":" unblock ";?>"></span>
                    </form>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body px-5">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-floating">
                                    <select name="fab_id" id="edit_fab_id" class="form-select" required <?php echo ($sys_role > 1) ? "disabled":"";?>>
                                        <option value="" hidden>--請選擇fab廠別--</option>
                                        <?php foreach($fabs as $fab){ ?>
                                            <option value="<?php echo $fab["id"];?>" for="edit_fab_id">
                                                <?php echo $fab["id"]."_".$fab["fab_title"]."(".$fab["fab_remark"].")"; echo ($fab["flag"] == "Off") ? ' -- 已關閉':''; ?></option>
                                        <?php } ?>
                                    </select>
                                    <label for="edit_fab_id" class="form-label">fab_id：<sup class="text-danger"><?php echo ($sys_role > 1) ? " - disabled":" *"; ?></sup></label>
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
                            <div class="col-12">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="edit_flag" class="form-label">flag/顯示開關：</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="On" id="edit_ptlocal_On" class="form-check-input" checked>&nbsp
                                            <label for="edit_ptlocal_On" class="form-check-label">On</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="Off" id="edit_ptlocal_Off" class="form-check-input">&nbsp
                                            <label for="edit_ptlocal_Off" class="form-check-label">Off</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-12 text-end p-0" id="edit_ptlocal_info"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="activeTab" value="3">
                            <input type="hidden" name="id" id="ptlocal_edit_id" >
                            <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                <span id="ptlocal_modal_button" class="<?php echo ($sys_role <= 1) ? "":" unblock ";?>"></span>
                            <input type="reset" class="btn btn-info" id="ptlocal_reset_btn" value="清除">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>

    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
</body>

<script src="../../libs/aos/aos.js"></script>
<script src="../../libs/aos/aos_init.js"></script>
<script src="../../libs/sweetalert/sweetalert.min.js"></script>

<script>

    var site        = <?=json_encode($sites)?>;                                                 // 引入sites資料
    var fab         = <?=json_encode($fabs)?>;                                                  // 引入fabs資料
    var local       = <?=json_encode($locals)?>;                                                // 引入locals資料
    var ptlocal     = <?=json_encode($ptlocals)?>;                                              // 引入locals資料
    var site_item   = ['id','site_title','site_remark','flag'];                                 // 交給其他功能帶入 delete_site_id
    var fab_item    = ['id','site_id','fab_title','fab_remark','sign_code','pm_emp_id','buy_ty','flag'];    // 交給其他功能帶入 delete_fab_id
    var local_item  = ['id','fab_id','local_title','local_remark','flag'];                      // 交給其他功能帶入 delete_local_id
    var ptlocal_item= ['id','fab_id','local_title','local_remark','flag'];                      // 交給其他功能帶入 delete_local_id
    
    var tags        = [];                                                                       // fun3-1：search Key_word
    var activeTab   = '<?=$activeTab?>';                                                        //设置要自动选中的选项卡的索引（从0开始）
    var sw_json     = '<?=$sw_json?>';

</script>

<script src="local.js?v=<?=time()?>"></script>
<script src="service_window_maker.js?v=<?=time()?>"></script>

<?php include("../template/footer.php"); ?>