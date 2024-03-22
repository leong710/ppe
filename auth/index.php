<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDeniedAdmin($sys_id);

    $sw_json = [];       // 

    // CRUD
    if(isset($_POST["submit_add_user"]))   { $sw_json = storeUser($_REQUEST); }
    if(isset($_POST["submit_edit_user"]))  { $sw_json = updateUser($_REQUEST); }
    if(isset($_POST["submit_delete_user"])){ $sw_json = deleteUser($_REQUEST); }

    // 切換指定NAV分頁
    $activeTab = (isset($_REQUEST["activeTab"])) ? $_REQUEST["activeTab"] : "0";       // 0= PM名單

    // 這裡讀取狀態：none正常、new新人、pause停用
    $showAllUsers = showAllUsers("");
    $showAllUsers_none = showAllUsers("none");
    $showAllUsers_new = showAllUsers("new");
    $showAllUsers_pause = showAllUsers("pause");

    $count_users_new = count($showAllUsers_new);
    $count_users_pause = count($showAllUsers_pause);
    // $sites = show_site();
    $fabs = show_fab();

?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <script src="../../libs/jquery/mloading_init.js"></script>
    <style>
        table,td {
            border: 0px;
            border-collapse: collapse;
            padding: 5px;
            /* text-align: left; */
            /* background-color: #DEDEDE; */
        }
        tr > th {
            color: blue;
            text-align: center;
            vertical-align: top; 
            word-break: break-all; 
            background-color: white;
            font-size: 16px;
        }
        /* 互動視窗Modal中加入Form時，要注意擺放位置，因為引響滾軸的功能!! */
            /* .modal-dialog{
                overflow-y: initial !important
            } */
            /* .modal-body{
                height: 450px;
                overflow-y: auto;
            } */
        .unblock{
            display: none;
            /* transition: 3s; */
        }
        /*眼睛*/
        #checkEye {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
        }        
        .t_left {
            text-align: left;
            padding-left: 20px;
        }
        #key_word, #user{    
            margin-bottom: 0px;
            text-align: center;
        }
        .autoinput {
            /* background-color: greenyellow; */
            border: 2px solid greenyellow;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="" id="top"></div>
    <div class="container my-2">
        <div class="justify-content-center rounded bg-light">
            <!-- head -->
            <div class="row px-4 pb-0">
                <div class="col-12 pb-0 pt-5">
                    <h5><?php echo $sys_id." local User資料庫 - 共 ".count($showAllUsers);?> 筆</h5>
                </div>
                <!-- nav tab -->
                <div class="col-md-6 head">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" title="none" id="none"><i class="fa-solid fa-circle-user"></i>&nbspPM名單</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" title="new" id="new"><i class="fa-solid fa-ghost"></i>&nbsp一般使用者
                                <?php if($count_users_new !=0){?>
                                    <span class="badge bg-danger"><?php echo $count_users_new;?></span>
                                <?php }?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" title="pause" id="pause"><i class="fa-solid fa-ban"></i>&nbsp停用
                                <?php if($count_users_pause !=0){?>
                                    <span class="badge bg-secondary"><?php echo $count_users_pause;?></span>
                                <?php }?>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" id="role_info_btn" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#role_info" > <i class="fa fa-info-circle" aria-hidden="true"></i> 權限說明</button>
                    <button type="button" id="add_usere_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#user_modal" onclick="add_module('user')" > <i class="fa fa-user-plus"></i> 新增</button>
                </div>
            </div>
            <!-- dataTable -->
            <div class="col-12 p-4 pt-0">
                <table id="table" class="table table-striped table-hover">
                    <thead> 
                        <tr>
                            <th>id</th>
                            <th>emp_id / cName / user</th>
                            <th>fab_id</th>
                            <th>sfab_id</th>
                            <th>role▼</th>
                            <th>idty</th>
                            <th>created_at</th>
                            <th>action</th>
                        </tr>
                    </thead>
                    <!-- PM名單 -->
                    <tbody id="none" class="">
                        <?php foreach($showAllUsers_none as $user_none){ ?>
                            <tr>
                                <td><?php echo $user_none["id"]; ?></td>
                                <td class="t_left"><?php echo $user_none["emp_id"]." / ".$user_none["cname"]." / ";?><a href="edit.php?user=<?php echo $user_none["user"];?>"><?php echo $user_none["user"]; ?></a></td>
                                <td class="t_left" title="<?php echo $user_none["fab_remark"];?>"><?php echo $user_none["fab_id"]."_".$user_none["fab_title"]; if($user_none["fab_flag"] == "Off"){ ?><sup class="text-danger">-已關閉</sup><?php } ?></td>
                                <td><?php echo $user_none["sfab_id"]; ?></td>
                                <td <?php if($user_none["role"] == "0"){ ?> style="background-color:yellow" <?php } ?>>
                                    <?php switch($user_none["role"]){
                                        case "0": echo "0_管理"; break;
                                        case "1": echo "1_PM"; break;
                                        case "2": echo "2_siteUser"; break;
                                        case "3": echo "3_noBody"; break;
                                        default: echo "停用";} ?></td>
                                <td><?php echo $user_none["idty"];?>
                                    <?php switch($user_none["idty"]){
                                        case "0": echo "_管理"; break;
                                        case "1": echo "_工程師"; break;
                                        case "2": echo "_課副理"; break;
                                        case "3": echo "_部經理層"; break;
                                        case "4": echo "_廠處長層"; break;
                                        default: echo "停用";} ?></td>
                                <td title="<?php echo $user_none["created_at"];?>"><?php echo substr($user_none["created_at"],0,10);?></td>
                                <td>
                                    <!-- <a href="edit.php?user=<php echo $user_none["user"];?>" class="btn btn-sm btn-xs btn-info">編輯</a> -->
                                    <button type="button" value="<?php echo $user_none["id"];?>" class="btn btn-sm btn-xs btn-info" 
                                        data-bs-toggle="modal" data-bs-target="#user_modal" onclick="edit_module('user',this.value)" >編輯</button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <!-- 一般使用者名單 -->
                    <tbody id="new" class="unblock">
                        <?php foreach($showAllUsers_new as $user_new){ ?>
                            <tr>
                                <td><?php echo $user_new["id"]; ?></td>
                                <td class="t_left"><?php echo $user_new["emp_id"]." / ".$user_new["cname"]." / ";?><a href="edit.php?user=<?php echo $user_new["user"];?>"><?php echo $user_new["user"]; ?></a></td>
                                <td class="t_left" title="<?php echo $user_new["fab_remark"];?>"><?php echo $user_new["fab_id"]."_".$user_new["fab_title"]; if($user_new["fab_flag"] == "Off"){ ?><sup class="text-danger">-已關閉</sup><?php } ?></td>
                                <td><?php echo $user_new["sfab_id"]; ?></td>
                                <td <?php if($user_new["role"] == "0"){ ?> style="background-color:yellow" <?php } ?>>
                                    <?php switch($user_new["role"]){
                                        case "0": echo "0_管理"; break;
                                        case "1": echo "1_PM"; break;
                                        case "2": echo "2_siteUser"; break;
                                        case "3": echo "3_noBody"; break;
                                        default: echo "停用";} ?></td>
                                <td><?php echo $user_new["idty"];?>
                                    <?php switch($user_new["idty"]){
                                        case "0": echo "_管理"; break;
                                        case "1": echo "_工程師"; break;
                                        case "2": echo "_課副理"; break;
                                        case "3": echo "_部經理層"; break;
                                        case "4": echo "_廠處長層"; break;
                                        default: echo "停用";} ?></td>
                                <td title="<?php echo $user_new["created_at"];?>"><?php echo substr($user_new["created_at"],0,10); ?></td>
                                <td>
                                    <!-- <a href="edit.php?user=<php echo $user_new["user"];?>" class="btn btn-sm btn-xs btn-info">編輯</a> -->
                                    <button type="button" value="<?php echo $user_new["id"];?>" class="btn btn-sm btn-xs btn-info" 
                                        data-bs-toggle="modal" data-bs-target="#user_modal" onclick="edit_module('user',this.value)" >編輯</button>
                                </td>
                            </tr>
                        <?php }?>
                    </tbody>
                    <!-- 除權名單 -->
                    <tbody id="pause" class="unblock">
                        <?php foreach($showAllUsers_pause as $user_pause){ ?>
                            <tr>
                                <td><?php echo $user_pause["id"]; ?></td>
                                <td class="t_left"><?php echo $user_pause["emp_id"]." / ".$user_pause["cname"]." / ";?><a href="edit.php?user=<?php echo $user_pause["user"];?>"><?php echo $user_pause["user"]; ?></a></td>
                                <td class="t_left" title="<?php echo $user_pause["fab_remark"];?>"><?php echo $user_pause["fab_id"]."_".$user_pause["fab_title"]; if($user_pause["fab_flag"] == "Off"){ ?><sup class="text-danger">-已關閉</sup><?php } ?></td>
                                <td><?php echo $user_pause["sfab_id"]; ?></td>
                                <td <?php if($user_pause["role"] == "0"){ ?> style="background-color:yellow" <?php } ?>>
                                    <?php switch($user_pause["role"]){
                                        case "0": echo "0_管理"; break;
                                        case "1": echo "1_PM"; break;
                                        case "2": echo "2_siteUser"; break;
                                        case "3": echo "3_noBody"; break;
                                        default: echo "停用";} ?></td>
                                <td><?php echo $user_pause["idty"];?>
                                    <?php switch($user_pause["idty"]){
                                        case "0": echo "_管理"; break;
                                        case "1": echo "_工程師"; break;
                                        case "2": echo "_課副理"; break;
                                        case "3": echo "_部經理層"; break;
                                        case "4": echo "_廠處長層"; break;
                                        default: echo "停用";} ?></td>
                                <td title="<?php echo $user_pause["created_at"];?>"><?php echo substr($user_pause["created_at"],0,10); ?></td>
                                <td>
                                    <div class="">
                                        <!-- <a href="edit.php?user=<php echo $user_pause["user"];?>" class="btn btn-sm btn-xs btn-info">編輯</a> -->
                                        <button type="button" value="<?php echo $user_pause["id"];?>" class="btn btn-sm btn-xs btn-info" 
                                            data-bs-toggle="modal" data-bs-target="#user_modal" onclick="edit_module('user',this.value)" >編輯</button>
                                        <?php if($user_pause["role"] == ""){ ?>
                                            <form action="" method="post">
                                                <input type="hidden" name="id" value="<?php echo $user_pause["id"];?>">
                                                <input type="submit" name="submit_delete_user" value="刪除" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">
                                            </form>
                                        <?php } ?>
                                    </div>    
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<!-- 模組-權限說明 -->
    <div class="modal fade" id="role_info" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header rounded bg-success text-white p-2 m-2">
                    <h5 class="modal-title"><i class="fa-solid fa-circle-info"></i> role權限說明</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="col-12 py-0 px-4">
                        <table>
                            <thead> 
                                <tr>
                                    <th>role</th>
                                    <th>定義名稱</th>
                                    <th>權限說明</th>
                                    <th>適用對象</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>null</td>
                                    <td>停用</td>
                                    <td>停止該User使用權利</td>
                                    <td>離職或其他不被授予權限之對象</td>
                                </tr>
                                <tr>
                                    <td>0</td>
                                    <td>管理</td>
                                    <td>系統管理人員</td>
                                    <td>細部設定、最大權限之管理人</td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>PM</td>
                                    <td>大部分管理與審核功能</td>
                                    <td>系統負責人</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>siteUser</td>
                                    <td>廠區業務人員</td>
                                    <td>各site指定業務窗口</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>noBody</td>
                                    <td>一般使用者</td>
                                    <td>一次性使用者</td>
                                </tr>
                            </tbody>
                        </table>
                        <hr>
                        <h4>user身份設定：</h4>
                        <table>
                            <thead> 
                                <tr>
                                    <th>使用環境</th>
                                    <th>1. PM設定</th>
                                    <th>2. 所屬部課級</th>
                                    <th>使用環境 PM / Site</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="t-center">PM</td>
                                    <td class="t-center">指定PM或副PM</td>
                                    <td class="t-center">依需求設定</td>
                                    <td class="t-center">V / V</td>
                                </tr>
                                <tr>
                                    <td class="t-center">Site</td>
                                    <td class="t-center">限用 tnESH(一般用戶)</td>
                                    <td class="t-center">依需求設定</td>
                                    <td class="t-center">X / V</td>
                                </tr>
          
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-end">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">關閉</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- 模組-user modal -->
    <div class="modal fade" id="user_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg"> 
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span id="user_modal_action"></span> local user role</h5>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="user_delete_id">&nbsp&nbsp&nbsp&nbsp&nbsp
                        <span id="user_modal_delect_btn" class="<?php echo ($_SESSION[$sys_id]["role"] == 0) ? "":" unblock ";?>"></span>
                    </form>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="" method="post" class="needs-validation">
                    <div class="modal-body px-3">
                        <!-- line 1 -->
                        <div class="row">
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating input-group">
                                    <input type="text" name="user" id="user" class="form-control" data-toggle="tooltip" data-placement="bottom" title="請輸入查詢對象 工號、姓名或NT帳號" required  onchange="search_fun();">
                                    <label for="user" class="form-label">user ID：<sup class="text-danger"> *</sup></label>
                                    <button type="button" class="btn btn-outline-primary" onclick="search_fun()"><i class="fa-solid fa-magnifying-glass"></i> 搜尋</button>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text" name="sys_id" id="sys_id" class="form-control" value="<?php echo $sys_id;?>" required readOnly>
                                    <label for="sys_id" class="form-label">sys_id：<sup class="text-danger"> - readOnly</sup></label>
                                </div>
                            </div>
                        </div>
                        <!-- line 2 -->
                        <div class="row">
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text" name="emp_id" id="emp_id" class="form-control" required>
                                    <label for="emp_id" class="form-label">emp_id/工號：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text" name="cname" id="cname" class="form-control" required>
                                    <label for="cname" class="form-label">中文姓名：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                        </div>
                        <!-- line 3 -->
                        <div class="row">
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <select name="idty" id="idty" class="form-select">
                                        <option value=""  >停用</option>
                                        <option value="1" selected >1_工程師</option>
                                        <option value="2" >2_課副理</option>
                                        <option value="3" >3_部經理層</option>
                                        <option value="4" >4_廠處長層</option>
                                    </select>
                                    <label for="idty" class="form-label">身份定義：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <select name="role" id="role" class="form-select">
                                        <option value=""  for="role">停用</option>
                                        <option value="0" for="role" <?php echo $_SESSION[$sys_id]["role"] > 0 ? "hidden":"";?>>0_管理</option>
                                        <option value="1" for="role" <?php echo $_SESSION[$sys_id]["role"] > 1 ? "hidden":"";?>>1_PM</option>
                                        <option value="2" for="role" selected >2_siteUser</option>
                                        <option value="3" for="role" >3_noBody</option>
                                    </select>
                                    <label for="role" class="form-label">權限：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                        </div>
                        <!-- line 4 -->
                        <div class="row">
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <select name="fab_id" id="fab_id" class="form-control" required >
                                        <option value="" selected hidden>-- 請選擇主fab --</option>
                                        <?php foreach($fabs as $fab){ ?>
                                            <option value="<?php echo $fab["id"];?>">
                                                <?php echo $fab["id"].": ".$fab["fab_title"]." (".$fab["fab_remark"].")"; echo ($fab["flag"] == "Off") ? "--(已關閉)":"";?></option>
                                        <?php } ?>
                                    </select>
                                    <label for="fab_id" class="form-label">主fab_id：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                        </div>
                        <!-- line 5 -->
                        <div class="row">
                            <div class="col-12 py-1">
                                <label for="" class="form-label">副sfab_id：<sup class="text-danger"><?php echo ($_SESSION["AUTH"]["role"] >= 2 ) ? " - disabled":" 選填" ?></sup></label>
                                <div class="border rounded p-2">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <?php $i = 0; foreach($fabs as $fab){ ?>
                                                    <td>
                                                        <input type="checkbox" name="sfab_id[]" value="<?php echo $fab["id"];?>" id="sfab_id_<?php echo $fab["id"];?>" class="form-check-input" >
                                                        <label for="sfab_id_<?php echo $fab["id"];?>" class="form-check-label">&nbsp<?php echo $fab["fab_title"];?></label>
                                                    </td>
                                                    <?php $i++; if($i%6 == 0){?> </tr> <?php }  ?> 
                                                <?php } ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 text-end p-0" id="edit_user_info"></div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            
                            <input type="hidden" name="activeTab" value="1">
                            <input type="hidden" name="id" id="user_edit_id" >
                            
                            <span id="user_modal_button" class="<?php echo ($_SESSION[$sys_id]["role"] <= 1) ? "":" unblock ";?>"></span>
                            <input type="reset" class="btn btn-info" id="user_reset_btn" onclick="$('#emp_id, #cname, #user').removeClass('autoinput');" value="清除">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!--模組-查詢user -->
    <div class="modal fade" id="searchUser" aria-hidden="true" aria-labelledby="searchUser" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="searchUser">searchUser</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 p-3 border rounded" id="selectScomp_no">
                            <div class="row">
                                <!-- 第三排的功能 : 放查詢結果-->
                                <div class="result" id="result">
                                    <table id="result_table" class="table table-striped table-hover"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="bt_addUser" class="btn btn-secondary" data-bs-target="#user_modal" data-bs-toggle="modal">Back to addUser</button>
                </div>
            </div>
        </div>
    </div>

    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
</body>
<script src="../../libs/aos/aos.js"></script>
<script src="../../libs/aos/aos_init.js"></script>
<script>    
    // modal
    var user_modal = new bootstrap.Modal(document.getElementById('user_modal'), { keyboard: false });
    var searchUser_modal = new bootstrap.Modal(document.getElementById('searchUser'), { keyboard: false });
    var user = <?=json_encode($showAllUsers)?>;
    var user_item   = ['id','user','cname','emp_id','idty','role','fab_id','sfab_id'];          // 交給其他功能帶入
    var tags        = [];                                                                       // fun3-1：search Key_word
    var sw_json     = '<?=json_encode($sw_json)?>';
    var activeTab   = '<?=$activeTab?>';                                                        //设置要自动选中的选项卡的索引（从0开始）

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();

        // Alex menu
        var navs = Array.from(document.querySelectorAll(".head > ul > li > a"));
        var tbodys = Array.from(document.querySelectorAll("#table > tbody"));
        navs.forEach((nav)=>{
            nav.addEventListener('mousedown',function(){
                // 標籤
                document.querySelector(".head > ul > li > a.active").classList.remove('active')
                this.classList.add('active')
                // tbody
                document.querySelector("#table > tbody:not(.unblock)").classList.add('unblock')
                let index = navs.indexOf(this)
                tbodys[index].classList.remove('unblock')
            })
        })

        // 監聽表單內 input 變更事件
        $('#emp_id, #cname, #user').change(function() {
            $(this).removeClass('autoinput');   // 當有變更時，對該input加上指定的class
        });

        // // 遍歷表單內所有 input
            // $('#add_emp_id, #add_cname, #add_user').each(function() {
            //     // 如果input已有value，則對該input加上指定的class
            //     if ($(this).val()) {
            //         $(this).removeClass('autoinput');
            //     }
            // });

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

    function resetMain(){
        $("#result").removeClass("border rounded bg-white");
        $('#result_table').empty();
        // document.querySelector('#key_word').value = '';
    }
    // 第一-階段：search Key_word
    function search_fun(){
        mloading("show");                       // 啟用mLoading
        let search = $('#user').val().trim();       // search keyword取自user欄位
        if(!search || (search.length < 2)){
            $("body").mLoading("hide");
            alert("查詢字數最少 2 個字以上!!");
            return false;
        } 
        $.ajax({
            // url:'http://tneship.cminl.oa/hrdb/api/index.php',        // 正式舊版
            url:'http://tneship.cminl.oa/api/hrdb/index.php',           // 正式2024新版
            method:'post',
            dataType:'json',
            data:{
                functionname: 'search',                                 // 操作功能
                uuid: '752382f7-207b-11ee-a45f-2cfda183ef4f',           // ppe
                search: search                                          // 查詢對象key_word
            },
            success: function(res){
                var res_r = res["result"];
                postList(res_r);                                        // 將結果轉給postList進行渲染
                $("body").mLoading("hide");
                // document.getElementById("searchUser_btn").click();      // 切到searchUser頁面
                user_modal.hide();
                searchUser_modal.show();      // 切到searchUser頁面
            },
            error (err){
                console.log("search error:", err);
                $("body").mLoading("hide");
                alert("查詢錯誤!!");
            }
        })
    }
    // 第一階段：渲染功能
    function postList(res_r){
        // 清除表頭
        $('#result_table').empty();
        // $("#result").addClass("border rounded bg-white");
        $("#result").addClass("bg-white");
        // 定義表格頭段
        var div_result_table = document.querySelector('.result table');
        var Rinner = "<thead><tr>"+
                        "<th>員工編號</th>"+"<th>員工姓名</th>"+"<th>職稱</th>"+"<th>user_ID</th>"+"<th>部門代號</th>"+"<th>部門名稱</th>"+"<th>select</th>"+
                    "</tr></thead>" + "<tbody id='tbody'>"+"</tbody>";
        // 鋪設表格頭段thead
        div_result_table.innerHTML += Rinner;
        // 定義表格中段tbody
        var div_result_tbody = document.querySelector('.result table tbody');
        $('#tbody').empty();
        var len = res_r.length;
        for (let i=0; i < len; i++) {
            // 把user訊息包成json字串以便夾帶
            let user_json = '{"emp_id":"'+res_r[i].emp_id+'","cname":"'+ res_r[i].cname+'","user":"'+ res_r[i].user+'"}';
            div_result_tbody.innerHTML += 
                '<tr>' +
                    '<td>' + res_r[i].emp_id +'</td>' +
                    '<td>' + res_r[i].cname + '</td>' +
                    '<td>' + res_r[i].cstext + '</td>' +
                    '<td>' + res_r[i].user + '</td>' +
                    '<td>' + res_r[i].dept_no + '</td>' +
                    '<td>' + res_r[i].dept_c +'/'+ res_r[i].dept_d + '</td>' +
                    '<td>' + '<button type="button" class="btn btn-default btn-xs" id="'+res_r[i].emp_id
                        +'" value='+user_json+' onclick="tagsInput_me(this.value);">'+
                    '<i class="fa-regular fa-circle"></i></button>' + '</td>' +
                '</tr>';
        }
        $("body").mLoading("hide");                 // 關閉mLoading

    }
    // 第二階段：點選、渲染模組
    function tagsInput_me(val) {
        if (val !== '') {
            let obj_val = JSON.parse(val);                                          // 將JSON字串轉成Object物件
            console.log(obj_val);
            document.querySelector('#user_modal #emp_id').value = obj_val.emp_id;   // 將欄位帶入數值 = emp_id
            document.querySelector('#user_modal #cname').value = obj_val.cname;     // 將欄位帶入數值 = cname
            document.querySelector('#user_modal #user').value = obj_val.user;       // 將欄位帶入數值 = user

                // 创建一个正则表达式模式，用于模糊匹配包含"test"的单词
                const pattern_2 = /副理/gi; // g 表示全局匹配，i 表示不区分大小写
                const pattern_3 = /經理/gi; // g 表示全局匹配，i 表示不区分大小写
                const pattern_4 = /處長/gi; // g 表示全局匹配，i 表示不区分大小写

                // 使用正则表达式的 exec 方法来查找目标字符串中的匹配项
                let match;
                while ((match = pattern_2.exec(obj_val.cstext)) !== null) {
                    document.querySelector('#user_modal #idty').value = 2;       // 將欄位帶入數值 = 職稱
                }
                while ((match = pattern_3.exec(obj_val.cstext)) !== null) {
                    document.querySelector('#user_modal #idty').value = 3;       // 將欄位帶入數值 = 職稱
                }
                while ((match = pattern_4.exec(obj_val.cstext)) !== null) {
                    document.querySelector('#user_modal #idty').value = 4;       // 將欄位帶入數值 = 職稱
                }
            // document.querySelector('#user_modal #idty').value = idty;       // 將欄位帶入數值 = 職稱

            $("#emp_id, #cname, #user").addClass("autoinput");
            resetMain()                                                             // 清除搜尋頁面資料
            // document.getElementById("bt_addUser").click();                       // 切換返回到addUser新增頁面
            searchUser_modal.hide();      // 切到searchUser頁面
            user_modal.show();
        }
    }

    // user_modal：新增模式
    function add_module(to_module){     
        $('#'+to_module+'_modal_action, #'+to_module+'_modal_button, #'+to_module+'_modal_delect_btn, #edit_'+to_module+'_info').empty();   // 清除model功能
        $('#'+to_module+'_reset_btn').click();                                  // reset清除表單

        var add_btn = '<input type="submit" name="submit_add_'+to_module+'" class="btn btn-primary" value="新增'+to_module+'">';
        $('#'+to_module+'_modal_button').append(add_btn);                       // 填上儲存鈕

        $('#'+to_module+'_modal_action').append('新增');                        // 更新model標題
        var reset_btn = document.getElementById(to_module+'_reset_btn');        // 指定清除按鈕
        reset_btn.classList.remove('unblock');                                  // 新增模式 = 顯示清除按鈕
        document.querySelector("#"+to_module+"_modal .modal-header").classList.remove('edit_mode_bgc');
        document.querySelector("#"+to_module+"_modal .modal-header").classList.add('add_mode_bgc');
    }
    // user_modal：編輯模式
    function edit_module(to_module, row_id){
        $('#'+to_module+'_modal_action, #'+to_module+'_modal_button, #'+to_module+'_modal_delect_btn, #edit_'+to_module+'_info').empty();   // 清除model功能
        $('#'+to_module+'_reset_btn').click();                                  // reset清除表單
        
        var add_btn = '<input type="submit" name="submit_edit_'+to_module+'" class="btn btn-primary" value="儲存'+to_module+'">';
        $('#'+to_module+'_modal_button').append(add_btn);                       // 填上儲存鈕
        var del_btn = '<input type="submit" name="submit_delete_'+to_module+'" value="刪除'+to_module+'" class="btn btn-sm btn-xs btn-danger" onclick="return confirm(`確認刪除？`)">';
        $('#'+to_module+'_modal_delect_btn').append(del_btn);                   // 填上刪除鈕

        $('#'+to_module+'_modal_action').append('編輯');                        // 更新model標題
        var reset_btn = document.getElementById(to_module+'_reset_btn');        // 指定清除按鈕
        reset_btn.classList.add('unblock');                                     // 編輯模式 = 隱藏清除按鈕
        document.querySelector("#"+to_module+"_modal .modal-header").classList.remove('add_mode_bgc');
        document.querySelector("#"+to_module+"_modal .modal-header").classList.add('edit_mode_bgc');

        // 參數說明: to_module = 來源與目的 user_item
        tags = [];                                                              // 清除tag名單陣列
        // step1.將原排程陣列逐筆繞出來
        Object(window[to_module]).forEach(function(row){  
            if(row['id'] == row_id){
                // step2.鋪畫面到module
                var user_item    = ['id','user','cname','emp_id','idty','role','fab_id','sfab_id'];    // 交給其他功能帶入
                Object(window[to_module+'_item']).forEach(function(item_key){
                    if(item_key == 'id'){
                        document.querySelector('#'+to_module+'_delete_id').value = row['id'];       // 鋪上delete_id = this id.no for delete form
                        document.querySelector('#'+to_module+'_edit_id').value = row['id'];         // 鋪上edit_id = this id.no for edit form
                    }else if(item_key == 'flag'){
                        document.querySelector('#edit_'+to_module+' #edit_'+to_module+'_'+row[item_key]).checked = true;
                    }else if(item_key == 'sfab_id'){                          // 20231108_pm_emp_id多名單
                        // 第0階段：套用既有數據
                        var intt_val_str = row['sfab_id'];                    // 引入PM資料
                        var intt_val = [];
                        // if(intt_val_str.length !== 0){                       // 過濾原本pm字串不能為空
                        if(intt_val_str){                                       // 過濾原本pm字串不能為空
                            intt_val = intt_val_str.split(',');                 // 直接使用 split 方法得到陣列
                            intt_val.forEach(function(sfab_val){
                                document.querySelector('#'+to_module+'_modal #sfab_id_'+sfab_val).checked = true;
                            })
                        }
                    }else{
                        document.querySelector('#'+to_module+'_modal #'+item_key).value = row[item_key]; 
                    }
                })
                // 鋪上最後更新
                // let to_module_info = '最後更新：'+row['updated_at']+' / by '+row['updated_user'];
                // document.querySelector('#edit_'+to_module+'_info').innerHTML = to_module_info;
                // step3-3.開啟 彈出畫面模組 for user編輯
                // edit_myTodo_btn.click();
                return;
            }
        })
    }

    $(document).ready(function(){
        // 切換指定NAV分頁
        $('.nav-tabs button:eq(' + activeTab + ')').tab('show');    //激活选项卡


    });
    
</script>

<?php include("../template/footer.php"); ?>