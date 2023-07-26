<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    if(isset($_POST["site_submit"])){
        store_site($_REQUEST);
        }
        if(isset($_POST["fab_submit"])){
            store_fab($_REQUEST);
        }
        if(isset($_POST["local_submit"])){
            store_local($_REQUEST);
    }
    
    if(isset($_POST["changeSite_flag"])){
        changeSite_flag($_REQUEST);
        }
        if(isset($_POST["changeFab_flag"])){
            changeFab_flag($_REQUEST);
        }
        if(isset($_POST["changeLocal_flag"])){
            changeLocal_flag($_REQUEST);
    }

    if(isset($_POST["edit_site_submit"])){
        update_site($_REQUEST);
        }
        if(isset($_POST["edit_fab_submit"])){
            update_fab($_REQUEST);
        }
        if(isset($_POST["edit_local_submit"])){
            update_local($_REQUEST);
    }

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

    $fabs = show_fab();
    $sites = show_site();
?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <style>
        .unblock{
            display: none;
        }
        .tab-content.active {
            /* display: block; */
            animation: fadeIn 1s;
        }
        .nav-tabs .nav-link.active {
            /* color: #FFFFFF; */
            background-color: #84C1FF;
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
                                    <a class="nav-link" href="low_level.php" title="fab_安全水位設定"><i class="fa-solid fa-ban"></i>&nbsp安全水位設定</a>
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
                                    <a href="#" target="_blank" title="新增site" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_site"> <i class="fa fa-plus"></i> 新增site</a>
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
                                                    <form action="" method="post" class="d-inline-block">
                                                        <input type="hidden" name="id" value="<?php echo $site['id'];?>">
                                                        <input type="hidden" name="activeTab" value="0">
                                                        <input type="submit" name="changeSite_flag" value="<?php echo $site['flag'] == 'On' ? '顯示':'隱藏';?>" 
                                                            class="btn btn-sm btn-xs <?php echo $site['flag'] == 'On' ? 'btn-success':'btn-warning';?>"></form>
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
                                    <a href="#" target="_blank" title="新增fab" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_fab"> <i class="fa fa-plus"></i> 新增fab</a>
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
                                            <th>fab_title</th>
                                            <th>fab_remark</th>
                                            <th>buy_ty</th>
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
                                                <td style="text-align:left;"><?php echo $fab['fab_title']; ?></td>
                                                <td style="text-align:left;"><?php echo $fab['fab_remark']; ?></td>
                                                <td <?php echo $fab['buy_ty'] !='a' ? 'style="background-color: yellow;"':'';?>><?php echo $fab['buy_ty']; ?></td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>    
                                                        <form action="" method="post" class="d-inline-block">
                                                            <input type="hidden" name="id" value="<?php echo $fab['id'];?>">
                                                            <input type="hidden" name="activeTab" value="1">
                                                            <input type="submit" name="changeFab_flag" value="<?php echo $fab['flag'] == 'On' ? '顯示':'隱藏';?>" 
                                                                class="btn btn-sm btn-xs <?php echo $fab['flag'] == 'On' ? 'btn-success':'btn-warning';?>" ></form>
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
                                <h3>Local存放單位管理</h3>
                            </div>
                            <div class="col-12 col-md-4 py-0 text-end">
                                <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                    <a href="#" target="_blank" title="新增Local" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_local"> <i class="fa fa-plus"></i> 新增Local</a>
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
                                            <th>local_title</th>
                                            <th>local_remark</th>
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
                                                <td><?php echo $local['local_title']; ?></td>
                                                <td style="text-align:left;"><?php echo $local['local_remark'];?></td>
                                                <td><a href="low_level.php?local_id=<?php echo $local['id'];?>" class="btn btn-sm btn-xs <?php echo !empty($local['low_level']) ? "btn-success":"btn-warning";?>">
                                                    <?php echo !empty($local['low_level']) ? "已設定":"未設定";?></a></td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>    
                                                        <form action="" method="post" class="d-inline-block">
                                                            <input type="hidden" name="id" value="<?php echo $local['id'];?>">
                                                            <input type="hidden" name="activeTab" value="2">
                                                            <input type="submit" name="changeLocal_flag" value="<?php echo $local['flag'] == 'On' ? '顯示':'隱藏';?>" 
                                                                class="btn btn-sm btn-xs <?php echo $local['flag'] == 'On' ? 'btn-success':'btn-warning';?>"></form>
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

<!-- 彈出畫面模組 新增site-->
    <div class="modal fade" id="add_site" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">新增site</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="" method="post">
                    <div class="modal-body px-4">
                        <div class="row">

                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <input type="text" name="site_title" class="form-control" id="site_title" required placeholder="site名稱">
                                    <label for="site_title" class="form-label">site_title/Site名稱：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <input type="text" name="site_remark" class="form-control" id="site_remark" required placeholder="註解說明">
                                    <label for="site_remark" class="form-label">site_remark/註解說明：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 py-1">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="flag" class="form-label">flag/顯示開關：</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="On" id="site_On" class="form-check-input" checked>&nbsp
                                            <label for="site_On" class="form-check-label">On</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="Off" id="site_Off" class="form-check-input">&nbsp
                                            <label for="site_Off" class="form-check-label">Off</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="activeTab" value="0">
                            <input type="hidden" value="<?php echo $_SESSION["AUTH"]["cname"];?>" name="updated_user">
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>   
                                <input type="submit" value="新增" name="site_submit" class="btn btn-primary">
                            <?php } ?>
                            <input type="reset" value="清除" class="btn btn-info">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>
<!-- 彈出畫面模組 新增fab廠處級-->
    <div class="modal fade" id="add_fab" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">新增fab廠級分類</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="" method="post">
                    <div class="modal-body px-5">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-floating">
                                    <select name="site_id" id="site_id" class="form-select" required>
                                        <option value="" selected hidden>-- 請選擇site級別 --</option>
                                        <?php foreach($sites as $site){ ?>
                                            <option value="<?php echo $site["id"];?>" title="<?php echo $site["site_remark"];?>" >
                                                <!-- <php echo $site["flag"] == "Off" ? "hidden":"";?>> -->
                                            <?php echo $site["id"]."_".$site["site_title"]."(".$site["site_remark"].")"; echo ($site["flag"] == "Off") ? ' -- 已關閉':'';?></option>
                                        <?php } ?>
                                    </select> 
                                    <label for="site_id" class="form-label">site_id/所屬site：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" name="fab_title" class="form-control" id="fab_title" required placeholder="Fab名稱">
                                    <label for="fab_title" class="form-label">fab_title/廠級分類名稱：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" name="fab_remark" class="form-control" id="fab_remark" required placeholder="註解說明">
                                    <label for="fab_remark" class="form-label">fab_remark/註解說明：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>

                            <div class="col-12">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="ppty" class="form-label">廠區規模(限購類別)：</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="buy_ty" value="a" id="buy_a" class="form-check-input" required checked>
                                            <label for="buy_a" class="form-check-label">&nbspa.3千人以下</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="buy_ty" value="b" id="buy_b" class="form-check-input" required>
                                            <label for="buy_b" class="form-check-label">&nbspb.3千人以上</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="flag" class="form-label">flag/顯示開關：</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="On" id="fab_On" class="form-check-input" checked>&nbsp
                                            <label for="fab_On" class="form-check-label">On</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="Off" id="fab_Off" class="form-check-input">&nbsp
                                            <label for="fab_Off" class="form-check-label">Off</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="activeTab" value="1">
                            <input type="hidden" value="<?php echo $_SESSION["AUTH"]["cname"];?>" name="updated_user">
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>   
                                <input type="submit" value="新增" name="fab_submit" class="btn btn-primary">
                            <?php } ?>
                            <input type="reset" value="清除" class="btn btn-info">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>
<!-- 彈出畫面模組 新增Local-->
    <div class="modal fade" id="add_local" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">新增local存放單位</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body px-5">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-floating">
                                    <select name="fab_id" id="fab_id" class="form-select" required <?php echo ($_SESSION[$sys_id]["role"] > 1) ? "disabled":"";?>>
                                        <option value="" hidden>--請選擇fab廠別--</option>
                                        <?php foreach($fabs as $fab){ ?>
                                            <option value="<?php echo $fab["id"];?>" 
                                                    <?php if($fab["id"] == $_SESSION[$sys_id]["fab_id"]){ ?> selected <?php } ?>>
                                                    <!-- <php if($fab["flag"] == "Off"){ ?> hidden <php } ?>> -->
                                                <?php echo $fab["id"]."_".$fab["fab_title"]."(".$fab["fab_remark"].")"; echo ($fab["flag"] == "Off") ? ' -- 已關閉':''; ?></option>
                                        <?php } ?>
                                    </select>
                                    <label for="fab_id" class="form-label">fab_id：<sup class="text-danger"><?php echo ($_SESSION[$sys_id]["role"] > 1) ? " - disabled":" *"; ?></sup></label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" name="local_title" id="local_title" class="form-control" required placeholder="local名稱">
                                    <label for="local_title" class="form-label">local_title/名稱：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" name="local_remark" id="local_remark" class="form-control" required placeholder="註解說明">
                                    <label for="local_remark" class="form-label">local_remark/備註說明：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <!-- <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" name="low_level" id="low_level" class="form-control" required placeholder="安全水位">
                                    <label for="low_level" class="form-label">low_level/安全水位：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div> -->
                            <div class="col-12">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="flag" class="form-label">flag/顯示開關：</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="On" id="site_On" class="form-check-input" checked>&nbsp
                                            <label for="site_On" class="form-check-label">On</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="Off" id="site_Off" class="form-check-input">&nbsp
                                            <label for="site_Off" class="form-check-label">Off</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="activeTab" value="2">
                            <input type="hidden" value="<?php echo $_SESSION["AUTH"]["cname"];?>" name="updated_user">
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>   
                                <input type="submit" value="新增" name="local_submit" class="btn btn-primary">
                            <?php } ?>
                            <input type="reset" value="清除" class="btn btn-info">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>
    
<!-- 彈出畫面模組 編輯site-->
    <div class="modal fade" id="edit_site" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">編輯site資訊</h4>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="site_delete_id">
                        <?php if($_SESSION[$sys_id]["role"] == 0){ ?>
                            &nbsp&nbsp&nbsp&nbsp&nbsp
                            <input type="submit" name="delete_site" value="刪除site" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">
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
                                <input type="submit" value="儲存" name="edit_site_submit" class="btn btn-primary">
                            <?php } ?>
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>
<!-- 彈出畫面模組 編輯fab-->
    <div class="modal fade" id="edit_fab" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">編輯fab資訊</h4>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="fab_delete_id">
                        <?php if($_SESSION[$sys_id]["role"] == 0){ ?>
                            &nbsp&nbsp&nbsp&nbsp&nbsp
                            <input type="submit" name="delete_fab" value="刪除fab" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">
                        <?php } ?>
                    </form>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="" method="post">
                    <div class="modal-body px-5">
                        <div class="row">
                            <div class="col-12">
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

                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" name="fab_title" class="form-control" id="edit_fab_title" required placeholder="Fab名稱">
                                    <label for="edit_fab_title" class="form-label">fab_title/廠級分類名稱：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" name="fab_remark" class="form-control" id="edit_fab_remark" required placeholder="註解說明">
                                    <label for="edit_fab_remark" class="form-label">fab_remark/註解說明：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>

                            <div class="col-12">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="edit_buy_ty" class="form-label">廠區規模(限購類別)：</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="buy_ty" value="a" id="edit_buy_a" class="form-check-input" required >
                                            <label for="edit_buy_a" class="form-check-label">&nbspa.3千人以下</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="buy_ty" value="b" id="edit_buy_b" class="form-check-input" required >
                                            <label for="edit_buy_b" class="form-check-label">&nbspb.3千人以上</label>
                                        </td>
                                    </tr>
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
                            <!-- 最後編輯資訊 -->
                            <div class="col-12 text-end p-0" id="edit_fab_info"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="activeTab" value="1">
                            <input type="hidden" name="id" id="fab_edit_id" >
                            <input type="hidden" value="<?php echo $_SESSION["AUTH"]["cname"];?>" name="updated_user">
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>   
                                <input type="submit" value="儲存" name="edit_fab_submit" class="btn btn-primary">
                            <?php } ?>
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>
<!-- 彈出畫面模組 編輯Local-->
    <div class="modal fade" id="edit_local" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">編輯local資訊</h4>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="local_delete_id">
                        <?php if($_SESSION[$sys_id]["role"] == 0){ ?>
                            &nbsp&nbsp&nbsp&nbsp&nbsp
                            <input type="submit" name="delete_local" value="刪除local" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">
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
                            <input type="hidden" value="<?php echo $_SESSION["AUTH"]["cname"];?>" name="updated_user">
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>   
                                <input type="submit" value="儲存" name="edit_local_submit" class="btn btn-primary">
                            <?php } ?>
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
<script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>

<script>
    function resetMain(){
        $("#result").removeClass("border rounded bg-white");
        $('#result_table').empty();
        document.querySelector('#key_word').value = '';
    }

    var site   = <?=json_encode($sites);?>;                                // 引入sites資料
    var fab    = <?=json_encode($fabs);?>;                                 // 引入fabs資料
    var local  = <?=json_encode($locals);?>;                               // 引入locals資料
    var site_item   = ['id','site_title','site_remark','flag'];                     // 交給其他功能帶入 delete_site_id
    var fab_item    = ['id','site_id','fab_title','fab_remark','buy_ty','flag'];    // 交給其他功能帶入 delete_fab_id
    var local_item  = ['id','fab_id','local_title','local_remark','flag'];          // 交給其他功能帶入 delete_local_id

    // fun-1.鋪編輯畫面
    function edit_module(to_module, row_id){
        // remark: to_module = 來源與目的 site、fab、local
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
            }
        })
    }

    // 切換上架/下架開關
    let flagBtns = [...document.querySelectorAll('.flagBtn')];
    for(let flagBtn of flagBtns){
        flagBtn.onclick = e => {
            $.ajax({
                url:'../api/index.php',
                method:'post',
                dataType:'json',
                data:{
                    function: 'cheng_flag',           // 操作功能
                    table: 'myjob',
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
                },
                error: function(e){
                    console.log("error");
                }
            });
        }
    }

    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
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