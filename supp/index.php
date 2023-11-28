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
                                <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                    <button type="button" id="edit_supp_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_supp" onclick="add_module('supp')"><i class="fa fa-plus"></i> 單筆新增供應商</button>
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
                            <div class="col-12 col-md-4 py-0">
                                <h3>contact/聯絡人管理</h3>
                            </div>
                            <div class="col-12 col-md-8 py-0 text-end">
                                <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                    <button type="button" id="edit_contact_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_contact" onclick="add_module('contact')"><i class="fa fa-plus"></i> 單筆新增聯絡人</button>
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
                        <input type="hidden" name="id" id="supp_delete_id">
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
                        <input type="hidden" name="id" id="contact_delete_id">
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
<!-- 互動視窗 load_excel -->
    <div class="modal fade" id="load_excel" tabindex="-1" aria-labelledby="load_excel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">上傳Excel檔：</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form name="excelInput" action="../_Format/upload_excel.php" method="POST" enctype="multipart/form-data" target="api" onsubmit="return restockExcelForm()">
                    <div class="modal-body px-4">
                        <div class="row">
                            <div class="col-12 col-md-6 py-0">
                                <label for="excelFile" class="form-label">需求清單 <span>&nbsp<a href="../_Format/restock_example.xlsx" target="_blank">上傳格式範例</a></span> 
                                    <sup class="text-danger"> * 限EXCEL檔案</sup></label>
                                <div class="input-group">
                                    <input type="file" name="excelFile" id="excelFile" style="font-size: 16px; max-width: 250px;" class="form-control form-control-sm" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                                    <button type="submit" name="excelUpload" id="excelUpload" class="btn btn-outline-secondary">上傳</button>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-0">
                                <p id="warningText" name="warning" >＊請上傳需求單Excel檔</p>
                                <p id="sn_list" name="warning" >＊請確認Excel中的資料</p>
                            </div>
                        </div>
                            
                        <div class="row">
                            <iframe id="api" name="api" width="100%" height="30" style="display: none;" onclick="restockExcelForm()"></iframe>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="import_excel_btn" class="btn btn-success unblock" data-bs-dismiss="modal">載入</button>
                        <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">返回</button>
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
<!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
<script src="../../libs/sweetalert/sweetalert.min.js"></script>

<script>

    var supp       = <?=json_encode($supps);?>;                                // 引入supps資料
    var contact    = <?=json_encode($contacts);?>;                             // 引入contacts資料
    var supp_item       = ['id','scname','sname','supp_remark','inv_title','comp_no','_address','flag'];    // 交給其他功能帶入 delete_supp_id
    var contact_item    = ['id','comp_no','cname','phone','email','fax','contact_remark','flag'];           // 交給其他功能帶入 delete_contact_id

    function resetMain(){
        $("#result").removeClass("border rounded bg-white");
        $('#result_table').empty();
        document.querySelector('#key_word').value = '';
    }

// // // add mode function
    function add_module(to_module){     // 啟用新增模式
        $('#'+to_module+'_modal_action, #'+to_module+'_modal_delect_btn, #'+to_module+'_modal_submit_btn, #edit_'+to_module+'_info').empty();     // 清除model功能
        $('#'+to_module+'_reset_btn').click();                                                              // reset清除表單

        $('#'+to_module+'_modal_action').append('新增');                                                    // model標題文字
        document.querySelector('#edit_'+to_module+' .modal-header').classList.remove('edit_mode_bgc');      // model標題底色--去除編輯底色
        document.querySelector('#edit_'+to_module+' .modal-header').classList.add('add_mode_bgc');          // model標題底色--增加新增底色

        var add_btn = '<input type="submit" name="add_'+to_module+'_submit" class="btn btn-primary" value="新增">';
        $('#'+to_module+'_modal_submit_btn').append(add_btn);                                               // 添加儲存鈕

        var reset_btn = document.getElementById(to_module+'_reset_btn');                                    // 指定清除按鈕
        reset_btn.classList.remove('unblock');                                                              // 新增模式 = 解除
        if(to_module == 'supp'){
            var activeTab = 0;
        }else if(to_module == 'contact'){
            var activeTab = 1;
        }else{
            var activeTab = 0;
        }
        document.getElementById(to_module+'_activeTab') = activeTab;
    }


    // fun-1.鋪編輯畫面
    function edit_module(to_module, row_id){
        $('#'+to_module+'_modal_action, #'+to_module+'_modal_delect_btn, #'+to_module+'_modal_submit_btn, #edit_'+to_module+'_info').empty();     // 清除model功能
        $('#'+to_module+'_reset_btn').click();                                                              // reset清除表單

        $('#'+to_module+'_modal_action').append('編輯');                                                    // model標題
        document.querySelector('#edit_'+to_module+' .modal-header').classList.remove('add_mode_bgc');       // model標題底色--去除新增底色
        document.querySelector('#edit_'+to_module+' .modal-header').classList.add('edit_mode_bgc');         // model標題底色--新增編輯底色

        var reset_btn = document.getElementById(to_module+'_reset_btn');                                    // 指定清除按鈕
        reset_btn.classList.add('unblock');                                                                 // 編輯模式 = 隱藏

        var add_btn = '<input type="submit" name="add_'+to_module+'_submit" class="btn btn-primary" value="儲存">';
        $('#'+to_module+'_modal_submit_btn').append(add_btn);                                               // 添加儲存鈕

        var del_btn = '<input type="submit" name="delete_stock" value="刪除stock儲存品" class="btn btn-sm btn-xs btn-danger" onclick="return confirm(`確認刪除？`)">';
        $('#modal_delect_btn').append(del_btn);     // 刪除鈕
        
        '<input type="submit" name="delete_supp" value="刪除supp供應商" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">'
        '<input type="submit" name="delete_contact" value="刪除contact聯絡人" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">'

        // remark: to_module = 來源與目的 supp、contact
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
                    }else{
                        document.querySelector('#edit_'+to_module+' #edit_'+item_key).value = row[item_key]; 
                    }
                })

                // 鋪上最後更新
                let to_module_info = '最後更新：'+row['updated_at']+' / by '+row['updated_user'];
                document.querySelector('#edit_'+to_module+'_info').innerHTML = to_module_info;


                return;
            }
        })
    }

    // 切換上架/下架開關
    let flagBtns = [...document.querySelectorAll('.flagBtn')];
    for(let flagBtn of flagBtns){
        flagBtn.onclick = e => {
            let swal_content = e.target.name+'_id:'+e.target.id+'=';
            // console.log('e:',e.target.name,e.target.id,e.target.value);
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