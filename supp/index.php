<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 切換指定NAV分頁
    if(isset($_REQUEST["activeTab"])){
        $activeTab = $_REQUEST["activeTab"];
    }else{
        $activeTab = "1";       // 1 = _contact
    }
    // 新增
    if(isset($_POST["supp_submit"])){
        store_supp($_REQUEST);
        }
        if(isset($_POST["contact_submit"])){
            store_contact($_REQUEST);
    }

    // 調整flag ==> 20230712改用AJAX

    // 更新
    if(isset($_POST["edit_supp_submit"])){
        update_supp($_REQUEST);
        }
        if(isset($_POST["edit_contact_submit"])){
            update_contact($_REQUEST);
    }
    // 刪除
    if(isset($_POST["delete_supp"])){
        delete_supp($_REQUEST);
        }
        if(isset($_POST["delete_contact"])){
            delete_contact($_REQUEST);
    }

    $contacts = show_contact();
    $supps = show_supp();

    $count_contact = count($contacts);
    $count_supp = count($supps);

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
        .word_bk {
            text-align: left; 
            vertical-align: top; 
            word-break: break-all;
        }
        /* 新增與編輯 module表頭顏色 */
        .add_mode_bgc {          
            background-color: #ADD8E6;
        }
        .edit_mode_bgc {
            background-color: #FFFACD;
        }
        #excelFile{    
            margin-bottom: 0px;
            /* text-align: center; */
        }
        #excel_iframe{
            height: 320px;
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
                                <button class="nav-link" id="nav-supp-tab"  data-bs-toggle="tab" data-bs-target="#nav-supp_table"  type="button" role="tab" aria-controls="nav-supp" aria-selected="false">
                                    供應商&nbsp<span class="badge bg-secondary"><?php echo $count_supp;?></span></button>
                                <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact_table" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">
                                    聯絡人&nbsp<span class="badge bg-secondary"><?php echo $count_contact;?></span></button>
                                <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                                    <!-- <a class="nav-link" href="" title="fab_安全水位設定"><i class="fa-solid fa-ban"></i>&nbsp安全水位設定</a> -->
                                <?php } ?>
                            </div>
                        </nav>
                    </div>
                </div>
                <!-- 內頁 -->
                <!-- <div id="table"> -->
                <div class="tab-content" id="nav-tabContent">
                    
                    <!-- supp -->
                    <div id="nav-supp_table" class="tab-pane fade" role="tabpanel" aria-labelledby="nav-supp-tab">
                        <div class="row">
                            <div class="col-12 col-md-6 py-0">
                                <h3>supp/供應商管理</h3>
                            </div>
                            <div class="col-12 col-md-6 py-0 text-end">
                                <div class="row">
                                    <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                        <div class="col-12 col-md-6 py-0">
                                            <?php if($count_supp != 0){ ?>
                                                <!-- 下載EXCEL的觸發 -->
                                                <form id="supp_myForm" method="post" action="../_Format/download_excel.php">
                                                    <input type="hidden" name="htmlTable" id="supp_htmlTable" value="">
                                                    <button type="submit" name="submit" class="btn btn-success" value="supp" onclick="submitDownloadExcel('supp')" >
                                                        <i class="fa fa-download" aria-hidden="true"></i> 下載Excel</button>
                                                </form>
                                            <?php } ?>
                                        </div>

                                        <div class="col-12 col-md-6 py-0">
                                            <button type="button" id="edit_supp_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_supp" onclick="add_module('supp')"><i class="fa fa-plus"></i> 單筆新增</button>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#load_excel" onclick="excel_module('supp')"><i class="fa fa-upload" aria-hidden="true"></i> 上傳Excel檔</button>
                                        </div>
                                    <?php } ?>
                                </div>
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
                                                <td style="font-size: 6px;"><?php echo $supp["id"]; ?></td>
                                                <td style="text-align:left;"><?php echo $supp["scname"]; ?></td>
                                                <td style="text-align:left;"><?php echo $supp["sname"]; ?></td>
                                                <td style="text-align:left;"><?php echo $supp["supp_remark"]; ?></td>
                                                <td style="text-align:left;">
                                                    <?php 
                                                        echo "發票抬頭：".$supp["inv_title"]; 
                                                        echo "</br>統一編號：".$supp["comp_no"]; 
                                                        echo "</br>發票地址：".$supp["_address"]; 
                                                    ?>
                                                </td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?> 
                                                    <button type="button" name="supp" id="<?php echo $supp['id'];?>" class="btn btn-sm btn-xs flagBtn <?php echo $supp['flag'] == 'On' ? 'btn-success':'btn-warning';?>" value="<?php echo $supp['flag'];?>"><?php echo $supp['flag'];?></button>
                                                    <?php }else{ ?>
                                                        <span class="btn btn-sm btn-xs <?php echo $supp["flag"] == "On" ? "btn-success":"btn-warning";?>">
                                                            <?php echo $supp["flag"] == "On" ? "顯示":"隱藏";?>
                                                        </span>
                                                    <?php } ?></td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                                    <button type="button" value="<?php echo $supp["id"];?>" class="btn btn-sm btn-xs btn-info" 
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
                            <div class="col-12 col-md-6 py-0">
                                <h3>contact/聯絡人管理</h3>
                            </div>
                            <div class="col-12 col-md-6 py-0 text-end">
                                <div class="row">
                                    <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                        <div class="col-12 col-md-6 py-0">
                                            <?php if($count_contact != 0){ ?>
                                                <!-- 下載EXCEL的觸發 -->
                                                <form id="contact_myForm" method="post" action="../_Format/download_excel.php">
                                                    <input type="hidden" name="htmlTable" id="contact_htmlTable" value="">
                                                    <button type="submit" name="submit" class="btn btn-success" value="contact" onclick="submitDownloadExcel('contact')" >
                                                        <i class="fa fa-download" aria-hidden="true"></i> 下載Excel</button>
                                                </form>
                                            <?php } ?>
                                        </div>
                                        <div class="col-12 col-md-6 py-0">
                                            <button type="button" id="edit_contact_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_contact" onclick="add_module('contact')"><i class="fa fa-plus"></i> 單筆新增</button>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#load_excel" onclick="excel_module('contact')"><i class="fa fa-upload" aria-hidden="true"></i> 上傳Excel檔</button>
                                        </div>
                                    <?php } ?>
                                </div>
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
                                                <td style="font-size: 6px;"><?php echo $contact["id"]; ?></td>
                                                <td style="text-align:left;"><?php echo $contact["cname"]; ?></td>
                                                <td style="text-align:left;"><?php echo $contact["phone"]; ?></td>
                                                <td style="text-align:left;"><?php echo $contact["email"]; ?></td>
                                                <td style="text-align:left;"><?php echo $contact["fax"]; ?></td>
                                                <td style="text-align:left;"><?php echo $contact["comp_no"] ? $contact["comp_no"]." (".$contact["scname"].")":"-- 無 --"; 
                                                                                   echo ($contact["supp_flag"] == "Off") ? "<sup class='text-danger'>-已關閉</sup>":"";?></td>
                                                <td style="text-align:left;"><?php echo $contact["contact_remark"]; ?></td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                                        <button type="button" name="contact" id="<?php echo $contact['id'];?>" class="btn btn-sm btn-xs flagBtn <?php echo $contact['flag'] == 'On' ? 'btn-success':'btn-warning';?>" value="<?php echo $contact['flag'];?>"><?php echo $contact['flag'];?></button>
                                                    <?php }else{ ?>
                                                        <span class="btn btn-sm btn-xs <?php echo $contact["flag"] == "On" ? "btn-success":"btn-warning";?>">
                                                            <?php echo $contact["flag"] == "On" ? "顯示":"隱藏";?></span>
                                                    <?php } ?></td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                                    <button type="button" value="<?php echo $contact["id"];?>" class="btn btn-sm btn-xs btn-info" 
                                                        data-bs-toggle="modal" data-bs-target="#edit_contact" onclick="edit_module('contact',this.value)" >編輯</button>
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

<!-- 彈出畫面模組 編輯、新增supp供應商 -->
    <div class="modal fade" id="edit_supp" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="supp_modal_action"></span>&nbspsupp供應商</h4>

                    <form action="" method="post">
                        <input type="hidden" name="activeTab"   id="supp_delete_activeTab"  value="">
                        <input type="hidden" name="id"          id="supp_delete_id">
                        <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                            <span id="supp_modal_delect_btn"></span>
                        <?php } ?>
                    </form>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="" method="post">
                    <div class="modal-body px-5">
                        <div class="row">

                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text" name="scname" id="edit_scname" class="form-control" required placeholder="供應商名稱">
                                    <label for="edit_scname" class="form-label">scname/供應商名稱：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text" name="comp_no" id="edit_comp_no" class="form-control" required placeholder="統一編號">
                                    <label for="edit_comp_no" class="form-label">comp_no/統一編號：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text" name="sname" id="edit_sname" class="form-control" placeholder="供應商英文名稱">
                                    <label for="edit_sname" class="form-label">sname/供應商英文名稱：</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text" name="inv_title" id="edit_inv_title" class="form-control" placeholder="發票抬頭">
                                    <label for="edit_inv_title" class="form-label">inv_title/發票抬頭：</label>
                                </div>
                            </div>

                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <input type="text" name="_address" id="edit__address" class="form-control" placeholder="發票地址">
                                    <label for="edit__address" class="form-label">_address/發票地址：</label>
                                </div>
                            </div>

                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <textarea name="supp_remark" id="edit_supp_remark" class="form-control" style="height: 90px;" placeholder="敘述說明"></textarea>
                                    <label for="edit_supp_remark" class="form-label">supp_remark/註解說明：</label>
                                </div>
                            </div>

                            <div class="col-12 py-1">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="flag" class="form-label">flag/顯示開關：</label>
                                        </td>
                                        <td>
                                            <input type="radio" name="flag" id="edit_supp_On" class="form-check-input" value="On" checked>&nbsp
                                            <label for="edit_supp_On" class="form-check-label">On</label>
                                        </td>
                                        <td>
                                            <input type="radio" name="flag" id="edit_supp_Off" class="form-check-input" value="Off">&nbsp
                                            <label for="edit_supp_Off" class="form-check-label">Off</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <!-- 最後編輯資訊 -->
                            <div class="col-12 text-end p-0" id="edit_supp_info"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="activeTab"  id="supp_activeTab"  value="">
                            <input type="hidden" name="id"         id="supp_edit_id"    value="">
                            <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>   
                                <span id="supp_modal_submit_btn"></span>
                            <?php } ?>
                            <input type="reset" class="btn btn-info" id="supp_reset_btn" value="清除">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>
<!-- 彈出畫面模組 編輯、新增contact聯絡人 -->
    <div class="modal fade" id="edit_contact" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog" >
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="contact_modal_action"></span>&nbspcontact聯絡人</h4>

                    <form action="" method="post">
                        <input type="hidden" name="activeTab"   id="contact_delete_activeTab"  value="">
                        <input type="hidden" name="id"          id="contact_delete_id"         value="">
                        <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                            <span id="contact_modal_delect_btn"></span>
                        <?php } ?>
                    </form>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="" method="post">
                    <div class="modal-body px-5">
                        <div class="row">
                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <select name="comp_no" id="edit_comp_no" class="form-select">
                                        <option value="" >-- 請選擇供應商 --</option>
                                        <?php foreach($supps as $supp){ ?>
                                            <option value="<?php echo $supp["comp_no"];?>" title="<?php echo $supp["sname"];?>" >
                                                <!-- <php echo $supp["flag"] == "Off" ? "hidden":"";?>> -->
                                            <?php echo $supp["id"]."_".$supp["comp_no"]." (".$supp["scname"].")"; echo ($supp["flag"] == "Off") ? " -- 已關閉":"";?></option>
                                        <?php } ?>
                                    </select> 
                                    <label for="edit_comp_no" class="form-label">comp_no/供應商(統編)：</label>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text" name="cname" id="edit_cname" class="form-control" required placeholder="聯絡人名稱">
                                    <label for="edit_cname" class="form-label">cname/聯絡人名稱：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="tel" name="phone" id="edit_phone" class="form-control" required placeholder="聯絡電話">
                                    <label for="edit_phone" class="form-label">phone/聯絡電話：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="email" name="email" id="edit_email" class="form-control" placeholder="name@example.com">
                                    <label for="edit_email" class="form-label">email/電子信箱：</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="tel" name="fax" id="edit_fax" class="form-control" placeholder="傳真">
                                    <label for="edit_fax" class="form-label">fax/傳真：</label>
                                </div>
                            </div>

                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <textarea name="contact_remark" id="edit_contact_remark" class="form-control" style="height: 100px" placeholder="註解說明"></textarea>
                                    <label for="edit_contact_remark" class="form-label">contact_remark/註解說明：</label>
                                </div>
                            </div>

                            <div class="col-12 py-1">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="flag" class="form-label">flag/顯示開關：</label>
                                        </td>
                                        <td>
                                            <input type="radio" name="flag" id="edit_contact_On" class="form-check-input" value="On">&nbsp
                                            <label for="edit_contact_On" class="form-check-label">On</label>
                                        </td>
                                        <td>
                                            <input type="radio" name="flag" id="edit_contact_Off" class="form-check-input" value="Off">&nbsp
                                            <label for="edit_contact_Off" class="form-check-label">Off</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <!-- 最後編輯資訊 -->
                            <div class="col-12 text-end p-0" id="edit_contact_info"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="activeTab"   id="contact_activeTab"  value="">
                            <input type="hidden" name="id"          id="contact_edit_id"    value="">
                            <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?> 
                                <span id="contact_modal_submit_btn"></span>
                            <?php } ?>
                            <input type="reset" class="btn btn-info" id="contact_reset_btn" value="清除">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>
<!-- 互動視窗 upload_excel -->
    <div class="modal fade" id="load_excel" tabindex="-1" aria-labelledby="load_excel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">上傳<span id="excel_modal_action"></span>&nbspExcel檔：</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4">
                    <form name="excelInput" action="../_Format/upload_excel.php" method="POST" enctype="multipart/form-data" target="api" onsubmit="return checkExcelForm()">
                        <div class="row">
                            <div class="col-6 col-md-8 py-0">
                                <label for="excelFile" class="form-label">需求清單 <span id="excel_example"></span> 
                                    <sup class="text-danger"> * 限EXCEL檔案</sup></label>
                                <div class="input-group">
                                    <input type="file" name="excelFile" id="excelFile" style="font-size: 16px; max-width: 400px;" class="form-control form-control-sm" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                                    <button type="submit" name="excelUpload" id="upload_excel_btn" class="btn btn-outline-secondary" value="">上傳</button>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 py-0">
                                <p id="warningText" name="warning" >＊請上傳需求單Excel檔</p>
                                <p id="warningData" name="warning" >＊請確認Excel中的資料</p>
                            </div>
                        </div>
                                
                        <div class="row" id="excel_iframe">
                            <iframe id="api" name="api" width="100%" height="auto" style="display: none;" onclick="checkExcelForm()"></iframe>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <form action="import_excel.php" method="POST">
                        <input  type="hidden" name="excelTable"   id="excelTable"       value="">
                        <input  type="hidden" name="updated_user" id="updated_user"     value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                        <button type="submit" name="import_excel" id="import_excel_btn" value="" class="btn btn-success unblock" data-bs-dismiss="modal">載入</button>
                    </form>
                    <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">返回</button>
                </div>
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
<!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
<script src="../../libs/sweetalert/sweetalert.min.js"></script>

<script>

    var supp            = <?=json_encode($supps);?>;                                // 引入supps資料
    var contact         = <?=json_encode($contacts);?>;                             // 引入contacts資料
    var supp_item       = ['id','scname','sname','supp_remark','inv_title','comp_no','_address','flag'];    // 交給其他功能帶入 delete_supp_id
    var contact_item    = ['id','comp_no','cname','phone','email','fax','contact_remark','flag'];           // 交給其他功能帶入 delete_contact_id

// 以下為控制 iframe
    var realName         = document.getElementById('realName');           // 上傳後，JSON存放處(給表單儲存使用)
    var iframe           = document.getElementById('api');                // 清冊的iframe介面
    var warningText      = document.getElementById('warningText');        // 提示-檔案上傳
    var warningData      = document.getElementById('warningData');        // 提示-檔案內容
    var excel_json       = document.getElementById('excel_json');         // excel內容轉json
    var excelFile        = document.getElementById('excelFile');          // 上傳檔案名稱
    var upload_excel_btn = document.getElementById('upload_excel_btn');   // 按鈕-上傳
    var import_excel_btn = document.getElementById('import_excel_btn');   // 按鈕-載入

    $(document).ready(function(){
        // 切換指定NAV分頁
            //设置要自动选中的选项卡的索引（从0开始）
            var activeTab = '<?=$activeTab;?>';
            //激活选项卡
            $('.nav-tabs button:eq(' + activeTab + ')').tab('show');
    });

</script>

<script src="supp.js?v=<?=time();?>"></script>

<?php include("../template/footer.php"); ?>