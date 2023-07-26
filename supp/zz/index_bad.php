<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    if(isset($_POST["supp_submit"])){
        store_supp($_REQUEST);
        }
        if(isset($_POST["contact_submit"])){
            store_contact($_REQUEST);
        }
        if(isset($_POST["local_submit"])){
            store_local($_REQUEST);
    }
    
    if(isset($_POST["changeSupp_flag"])){
        changeSupp_flag($_REQUEST);
        }
        if(isset($_POST["changecontact_flag"])){
            changecontact_flag($_REQUEST);
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
        $activeTab = "0";       // 2 = local
    }

    $contacts = show_contact();
    $supps = show_supp();
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
            <div class="col_xl_11 col-11 bg-light rounded my-0 p-3" >
                <!-- NAV title -->
                <div class="row">
                    <!-- 分頁標籤 -->
                    <div class="col-12">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link" id="nav-supp-tab"  data-bs-toggle="tab" data-bs-target="#nav-supp_table"  type="button" role="tab" aria-controls="nav-supp"  aria-selected="false">供應商管理</button>
                                <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact_table" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">聯絡人</button>
                                <!-- class="active" aria-selected="true" -->
                                <button class="nav-link" id="nav-local-tab" data-bs-toggle="tab" data-bs-target="#nav-local_table" type="button" role="tab" aria-controls="nav-local" aria-selected="false">Local</button>
                                <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                                    <a class="nav-link" href="low_level.php" title="fab_報價單管理"><i class="fa-solid fa-ban"></i>&nbsp報價單管理</a>
                                <?php } ?>
                            </div>
                        </nav>
                    </div>
                </div>
                <!-- 內頁 -->
                <!-- <div id="table"> -->
                <div class="tab-contact" id="nav-tabcontact">
                    
                    <!-- supp -->
                    <div id="nav-supp_table" class="tab-pane fade" role="tabpanel" aria-labelledby="nav-supp-tab">
                        <div class="row">
                            <div class="col-12 col-md-6 py-0">
                                <h3>supp/供應商管理</h3>
                            </div>
                            <div class="col-12 col-md-6 py-0 text-end">
                                <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                    <a href="#" target="_blank" title="新增supp供應商" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_supp"> <i class="fa fa-plus"></i> 新增supp供應商</a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="col-12 p-0">
                                <table>
                                    <thead>
                                        <tr class="">
                                            <th>ai</th>
                                            <th>scname</br>供應商名稱</th>
                                            <th>sname</br>供應商英文名稱</th>
                                            <th>supp_remark</br>備註說明</th>
                                            <th>info</br>其他說明</th>
                                            <th>flag</th>
                                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>    
                                                <th>action</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <!-- 這裡開始抓SQL裡的紀錄來這裡放上 -->
                                    <tbody>
                                        <?php foreach($supps as $supp){ ?>
                                            <tr>
                                                <td style="font-size: 6px;"><?php echo $supp['id']; ?></td>
                                                <td style="text-align:left;"><?php echo $supp['scname']; ?></td>
                                                <td style="text-align:left;"><?php echo $supp['sname']; ?></td>
                                                <td style="text-align:left;"><?php echo $supp['supp_remark']; ?></td>
                                                <td style="text-align:left;">
                                                    <?php 
                                                        echo '發票抬頭：'.$supp['inv_title']; 
                                                        echo '</br>統一編號：'.$supp['comp_no']; 
                                                        echo '</br>發票地址：'.$supp['_address']; 
                                                    ?>
                                                </td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>    
                                                    <form action="" method="post" class="d-inline-block">
                                                        <input type="hidden" name="id" value="<?php echo $supp['id'];?>">
                                                        <input type="hidden" name="activeTab" value="0">
                                                        <input type="submit" name="changeSupp_flag" value="<?php echo $supp['flag'] == 'On' ? '顯示':'隱藏';?>" 
                                                            class="btn btn-sm btn-xs <?php echo $supp['flag'] == 'On' ? 'btn-success':'btn-warning';?>"></form>
                                                    <?php }else{ ?>
                                                        <span class="btn btn-sm btn-xs <?php echo $supp['flag'] == 'On' ? 'btn-success':'btn-warning';?>">
                                                            <?php echo $supp['flag'] == 'On' ? '顯示':'隱藏';?>
                                                        </span>
                                                    <?php } ?></td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                                    <button type="button" id="edit_supp_btn" value="<?php echo $supp['id'];?>" class="btn btn-sm btn-xs btn-info" 
                                                        data-bs-toggle="modal" data-bs-target="#edit_supp" onclick="edit_module('supp',this.value)" >編輯</button>
                                                <?php } ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- _contact -->
                    <div id="nav-contact_table" class="tab-pane fade" role="tabpanel" aria-labelledby="nav-contact-tab">
                        <div class="row">
                            <div class="col-12 col-md-8 py-0">
                                <h3>contact/聯絡人管理</h3>
                            </div>
                            <div class="col-12 col-md-4 py-0 text-end">
                                <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                    <a href="#" target="_blank" title="新增聯絡人" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_contact"> <i class="fa fa-plus"></i> 新增聯絡人</a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="col-12 p-0">
                                <table>
                                    <thead>
                                        <tr class="">
                                            <th>ai</th>
                                            <th>cname</br>聯絡人名稱</th>
                                            <th>phone</br>聯絡電話</th>
                                            <th>email</br>電子信箱</th>
                                            <th>fax</br>傳真</th>
                                            <th>comp_no</br>供應商統編</th>
                                            <th>contact_remark</br>註解說明</th>
                                            <th>flag</th>
                                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>    
                                                <th>action</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <!-- 這裡開始抓SQL裡的紀錄來這裡放上 -->
                                    <tbody>
                                        <?php foreach($contacts as $contact){ ?>
                                            <tr>
                                                <td style="font-size: 6px;"><?php echo $contact['id']; ?></td>
                                                <td style="text-align:left;"><?php echo $contact['cname']; ?></td>
                                                <td style="text-align:left;"><?php echo $contact['phone']; ?></td>
                                                <td style="text-align:left;"><?php echo $contact['email']; ?></td>
                                                <td style="text-align:left;"><?php echo $contact['fax']; ?></td>
                                                <td style="text-align:left;"><?php echo $contact['comp_no']; ?></td>
                                                <td style="text-align:left;"><?php echo $contact['contact_remark']; ?></td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>    
                                                        <form action="" method="post" class="d-inline-block">
                                                            <input type="hidden" name="id" value="<?php echo $contact['id'];?>">
                                                            <input type="hidden" name="activeTab" value="1">
                                                            <input type="submit" name="changeContact_flag" value="<?php echo $contact['flag'] == 'On' ? '顯示':'隱藏';?>" 
                                                                class="btn btn-sm btn-xs <?php echo $contact['flag'] == 'On' ? 'btn-success':'btn-warning';?>" ></form>
                                                    <?php }else{ ?>
                                                        <span class="btn btn-sm btn-xs <?php echo $contact['flag'] == 'On' ? 'btn-success':'btn-warning';?>">
                                                            <?php echo $contact['flag'] == 'On' ? '顯示':'隱藏';?></span>
                                                    <?php } ?></td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                                    <button type="button" id="edit_contact_btn" value="<?php echo $contact['id'];?>" class="btn btn-sm btn-xs btn-info" 
                                                        data-bs-toggle="modal" data-bs-target="#edit_contact" onclick="edit_module('contact',this.value)" >編輯</button>
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
                                                    <a href="edit_local.php?id=<?php echo $local['id'];?>" class="btn btn-sm btn-xs btn-info">編輯</a>
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

<!-- 彈出畫面模組 新增supp供應商-->
    <div class="modal fade" id="add_supp" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog" >
        <div class="modal-dialog ">
            <div class="modal-contact">
                <div class="modal-header">
                    <h4 class="modal-title">新增supp供應商</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="" method="post">
                    <div class="modal-body px-4">
                        <div class="row">

                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <input type="text" name="scname" class="form-control" id="scname" required placeholder="供應商名稱">
                                    <label for="scname" class="form-label">scname/供應商名稱：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <input type="text" name="sname" class="form-control" id="sname" placeholder="供應商英文名稱">
                                    <label for="sname" class="form-label">sname/供應商英文名稱：</label>
                                </div>
                            </div>
                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <textarea name="supp_remark" id="supp_remark" class="form-control" style="height: 90px;" placeholder="敘述說明"></textarea>
                                    <label for="supp_remark" class="form-label">supp_remark/註解說明：</label>
                                </div>
                            </div>
                            
                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <input type="text" name="comp_no" class="form-control" id="comp_no" required placeholder="統一編號">
                                    <label for="comp_no" class="form-label">comp_no/統一編號：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <input type="text" name="inv_title" class="form-control" id="inv_title" placeholder="發票抬頭">
                                    <label for="inv_title" class="form-label">inv_title/發票抬頭：</label>
                                </div>
                            </div>
                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <input type="text" name="_address" class="form-control" id="_address" placeholder="發票地址">
                                    <label for="_address" class="form-label">_address/發票地址：</label>
                                </div>
                            </div>

                            <div class="col-12 py-1">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="flag" class="form-label">flag/顯示開關：</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="On" id="supp_On" class="form-check-input" checked>&nbsp
                                            <label for="supp_On" class="form-check-label">On</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="Off" id="supp_Off" class="form-check-input">&nbsp
                                            <label for="supp_Off" class="form-check-label">Off</label>
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
                                <input type="submit" value="新增" name="supp_submit" class="btn btn-primary">
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
            <div class="modal-contact">
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
            <div class="modal-contact">
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
            <div class="modal-contact">
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
<!-- 彈出畫面模組 編輯fab廠處級-->
    <div class="modal fade" id="edit_fab" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-contact">
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
            <div class="modal-contact">
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

    var supp       = <?=json_encode($supps);?>;                                // 引入supps資料
    var contact    = <?=json_encode($contacts);?>;                             // 引入contacts資料
    // var local  = <=json_encode($locals);?>;                               // 引入locals資料
    var supp_item   = ['id','scname','sname','supp_remark','inv_title','comp_no','_address','flag'];                     // 交給其他功能帶入 delete_site_id
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