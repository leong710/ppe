<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDeniedAdmin($sys_id);

    if(isset($_POST["submit"])){
        storeUser($_REQUEST);
    }
    if(isset($_POST["delete"])){
        deleteUser($_REQUEST);
    }

    // 這裡讀取狀態：none正常、new新人、pause停用
    $showAllUsers = showAllUsers("");
    $showAllUsers_none = showAllUsers("none");
    $showAllUsers_new = showAllUsers("new");
    $showAllUsers_pause = showAllUsers("pause");

    $count_users_new = count($showAllUsers_new);
    $count_users_pause = count($showAllUsers_pause);
    // $sites = show_site();
    $fabs = show_fab();
    // echo "<pre>";
    // print_r($showAllUsers);
    // echo "</pre>";
?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
        <!-- mloading CSS -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
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
        .modal-dialog{
            overflow-y: initial !important
        }
        .modal-body{
            height: 450px;
            overflow-y: auto;
        }
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
        #key_word {    
            margin-bottom: 0px;
            text-align: center;
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
                <div class="col-md-6 head">
                    <ul class="nav nav-pills">
                    <!-- <ul class="nav nav-tabs"> -->
                        <li class="nav-item">
                            <a class="nav-link active" href="#" title="none" id="none"><i class="fa-solid fa-circle-user"></i>&nbsp正常名單</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" title="new" id="new"><i class="fa-solid fa-ghost"></i>&nbsp新人
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
                    <a href="#role_info" target="_blank" title="權限說明" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#role_info"> <i class="fa fa-info-circle" aria-hidden="true"></i> 權限說明</a>
                    <a href="#" target="_blank" title="for新增user" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_user_modal" onclick="change_modal_mode('add')"> <i class="fa fa-user-plus"></i> 新增</a>
                </div>
            </div>
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
                    <!-- 正常名單 -->
                    <tbody id="none" class="">
                        <?php foreach($showAllUsers_none as $user_none){ ?>
                            <tr>
                                <td><?php echo $user_none["id"]; ?></td>
                                <td class="t_left"><?php echo $user_none["emp_id"]." / ".$user_none["cname"]." / ";?><a href="edit.php?user=<?php echo $user_none["user"];?>"><?php echo $user_none["user"]; ?></a></td>
                                <td title="<?php echo $user_none["fab_remark"];?>"><?php echo $user_none["fab_id"]."_".$user_none["fab_title"];?></td>
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
                                <td><a href="edit.php?user=<?php echo $user_none["user"];?>" class="btn btn-sm btn-xs btn-info">編輯</a>
                                    <button type="button" id="edit_user_btn" value="<?php echo $user_none["user"];?>" class="btn btn-sm btn-xs btn-info" 
                                            data-bs-toggle="modal" data-bs-target="#add_user_modal" onclick="edit_module('edit',this.value)" >編輯</button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <!-- 新人名單 -->
                    <tbody id="new" class="unblock">
                        <?php foreach($showAllUsers_new as $user_new){ ?>
                            <tr>
                                <td><?php echo $user_new["id"]; ?></td>
                                <td class="t_left"><?php echo $user_new["emp_id"]." / ".$user_new["cname"]." / ";?><a href="edit.php?user=<?php echo $user_new["user"];?>"><?php echo $user_new["user"]; ?></a></td>
                                <td title="<?php echo $user_new["fab_remark"];?>"><?php echo $user_new["fab_id"]."_".$user_new["fab_title"];?></td>
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
                                <td><a href="edit.php?user=<?php echo $user_new["user"];?>" class="btn btn-sm btn-xs btn-info">編輯</a></td>
                            </tr>
                        <?php }?>
                    </tbody>
                    <!-- 除權名單 -->
                    <tbody id="pause" class="unblock">
                        <?php foreach($showAllUsers_pause as $user_pause){ ?>
                            <tr>
                                <td><?php echo $user_pause["id"]; ?></td>
                                <td class="t_left"><?php echo $user_pause["emp_id"]." / ".$user_pause["cname"]." / ";?><a href="edit.php?user=<?php echo $user_pause["user"];?>"><?php echo $user_pause["user"]; ?></a></td>
                                <td title="<?php echo $user_pause["fab_remark"];?>"><?php echo $user_pause["fab_id"]."_".$user_pause["fab_title"];?></td>
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
                                        <a href="edit.php?user=<?php echo $user_pause["user"];?>" class="btn btn-sm btn-xs btn-info">編輯</a>
                                        <?php if($user_pause["role"] == ""){ ?>
                                            <form action="" method="post">
                                                <input type="hidden" name="id" value="<?php echo $user_pause["id"];?>">
                                                <input type="submit" name="delete" value="刪除" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">
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
<!-- 彈出畫面模組-權限說明 -->
    <div class="modal fade" id="role_info" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">role權限說明</h4>
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
                                    <td>廠區一般人員</td>
                                    <td>各site指定業務窗口</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>noBody</td>
                                    <td>新註冊之人員</td>
                                    <td>待審查賦予對應權限之使用者</td>
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
<!-- 彈出畫面-新增模組 -->
    <div class="modal fade" id="add_user_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                
                <div class="modal-header">
                    <h4 class="modal-title"><span id="modal_mode"></span> local user role</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
    
                <form action="" method="post">
                    <div class="modal-body p-4">
                        <div class="px-2">
                            <div class="row">
                                <div class="col-12 py-1 text-end">
                                    <button id="searchUser_btn" class="btn btn-sm btn-xs btn-warning" data-bs-target="#searchUser" data-bs-toggle="modal">searchUser</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <input type="text" name="user" id="add_user" class="form-control" required>
                                        <label for="add_user" class="form-label">user ID：<sup class="text-danger"> *</sup></label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <input type="text" name="sys_id" id="sys_id" class="form-control" value="<?php echo $sys_id;?>" required readOnly>
                                        <label for="sys_id" class="form-label">sys_id：<sup class="text-danger"> - readOnly</sup></label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <input type="text" name="emp_id" id="add_emp_id" class="form-control" required>
                                        <label for="add_emp_id" class="form-label">emp_id/工號：<sup class="text-danger"> *</sup></label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <input type="text" name="cname" id="add_cname" class="form-control" required>
                                        <label for="add_cname" class="form-label">中文姓名：<sup class="text-danger"> *</sup></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <select name="idty" id="add_idty" class="form-select">
                                            <option value=""  >停用</option>
                                            <option value="1" selected >1_工程師</option>
                                            <option value="2" >2_課副理</option>
                                            <option value="3" >3_部經理層</option>
                                            <option value="4" >4_廠處長層</option>
                                        </select>
                                        <label for="add_idty" class="form-label">身份定義：<sup class="text-danger"> *</sup></label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <select name="role" id="add_role" class="form-select">
                                            <option value=""  for="role">停用</option>
                                            <option value="0" for="role" <?php echo $_SESSION[$sys_id]["role"] > 0 ? "hidden":"";?>>0_管理</option>
                                            <option value="1" for="role" <?php echo $_SESSION[$sys_id]["role"] > 1 ? "hidden":"";?>>1_PM</option>
                                            <option value="2" for="role" selected >2_siteUser</option>
                                            <option value="3" for="role" >3_noBody</option>
                                        </select>
                                        <label for="add_role" class="form-label">權限：<sup class="text-danger"> *</sup></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <select name="fab_id" id="add_fab_id" class="form-control" required >
                                            <option value="" selected hidden>-- 請選擇主fab --</option>
                                            <?php foreach($fabs as $fab){ ?>
                                                <option value="<?php echo $fab["id"];?>">
                                                    <?php echo $fab["id"].": ".$fab["fab_title"]." (".$fab["fab_remark"].")"; echo ($fab["flag"] == "Off") ? "--(已關閉)":"";?></option>
                                            <?php } ?>
                                        </select>
                                        <label for="add_fab_id" class="form-label">主fab_id：<sup class="text-danger"> *</sup></label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <?php $i = 0; ?>
                                    <label for="" class="form-label">副sfab_id：<sup class="text-danger"><?php echo ($_SESSION["AUTH"]["role"] >= 2 ) ? " - disabled":" 選填" ?></sup></label>
                                    <div class="border rounded p-2">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <?php foreach($fabs as $fab){ ?>
                                                        <td>
                                                            <input type="checkbox" name="sfab_id[]" value="<?php echo $fab["id"];?>" id="add_sfab_id_<?php echo $fab["id"];?>" class="form-check-input" >
                                                            <label for="add_sfab_id_<?php echo $fab["id"];?>" class="form-check-label">&nbsp<?php echo $fab["fab_title"];?></label>
                                                        </td>
                                                        <?php $i++; if($i%6 == 0){?> </tr> <?php }  ?> 
                                                    <?php } ?>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="submit" value="儲存" name="submit" class="btn btn-primary">
                            <input type="reset" value="清除" class="btn btn-info">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- 彈出畫面-查詢user模組 -->
    <div class="modal fade" id="searchUser" aria-hidden="true" aria-labelledby="searchUser" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="searchUser">searchUser</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 p-3 border rounded" id="selectScomp_no">
                            <div class="row">
                                <!-- 第一排的功能 : 顯示已加入名單+input -->
                                <div class="col-12 px-4 py-0">
                                    <div id="selectScomp_noItem"></div>
                                    <input type="hidden" class="form-control" name="scomp_no[]" id="scomp_no" placeholder="已加入的">
                                </div>
                                <!-- 第二排的功能 : 搜尋功能 -->
                                <div class="col-12 px-4">
                                    <div class="input-group search" id="selectScomp_noForm">
                                        <input type="text" class="form-control" id="key_word" placeholder="請輸入工號、姓名或NT帳號" aria-label="請輸入查詢對象">
                                        <button type="button" class="btn btn-outline-success" onclick="resetMain()">清除</button>
                                        <button type="button" class="btn btn-outline-primary" onclick="search_fun()"><i class="fa-solid fa-magnifying-glass"></i> 搜尋</button>
                                    </div>
                                </div>
                                <!-- 第三排的功能 : 放查詢結果-->
                                <div class="result" id="result">
                                    <table id="result_table" class="table table-striped table-hover"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="bt_addUser" class="btn btn-secondary" data-bs-target="#add_user_modal" data-bs-toggle="modal" onclick="change_modal_mode('add')">Back to addUser</button>
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
<!-- mloading JS -->
<script src="../../libs/jquery/jquery.mloading.js"></script>

<script>    // Alex menu
    var navs = Array.from(document.querySelectorAll(".head > ul > li > a"));
    var tbodys = Array.from(document.querySelectorAll("#table > tbody"))
    var AllUsers = <?=json_encode($showAllUsers);?>;
    // console.log('AllUsers:', AllUsers);

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

    // mloading function
    function mloading(){
        $("body").mLoading({    // 開啟loading
            icon: "../../libs/jquery/Wedges-3s-120px.gif",
        }); 
    }

    function resetMain(){
        $("#result").removeClass("border rounded bg-white");
        $('#result_table').empty();
        document.querySelector('#key_word').value = '';
    }
    // 第一-階段：search Key_word
    function search_fun(){
        mloading("show");                       // 啟用mLoading
        let search = $('.search > input').val().trim();
        if(!search || (search.length < 2)){
            alert("查詢字數最少 2 個字以上!!");
            return false;
        } 
        $.ajax({
            url:'http://tw059332n/hrdb/api/index.php',
            method:'get',
            dataType:'json',
            data:{
                functionname: 'search',                     // 操作功能
                uuid: '752382f7-207b-11ee-a45f-2cfda183ef4f',
                search: search                              // 查詢對象key_word
            },
            success: function(res){
                var res_r = res["result"];
                postList(res_r);                            // 將結果轉給postList進行渲染
            },
            error (){
                console.log("search error");
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
                        "<th>員工編號</th>"+"<th>員工姓名</th>"+"<th>user_ID</th>"+"<th>部門代號</th>"+"<th>部門名稱</th>"+"<th>select</th>"+
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
                    '<td>' + res_r[i].user + '</td>' +
                    '<td>' + res_r[i].dept_no + '</td>' +
                    '<td>' + res_r[i].dept_c +'/'+ res_r[i].dept_d + '</td>' +
                    '<td>' + '<button type="button" class="btn btn-default btn-xs" id="'+res_r[i].emp_id
                        +'" value='+user_json+' onclick="tagsInput_me(this.value);">'+
                    '<i class="fa-regular fa-circle"></i></button>' + '</td>' +
                '</tr>';
        }
        // edit_pm.handleUpdate();
        $("body").mLoading("hide");                 // 關閉mLoading

    }
    // 第二階段：點選、渲染模組
    function tagsInput_me(val) {
        if (val !== '') {
            let obj_val = JSON.parse(val);                                              // 將JSON字串轉成Object物件
            document.querySelector('#add_user_modal #add_emp_id').value = obj_val.emp_id;     // 將欄位帶入數值 = emp_id
            document.querySelector('#add_user_modal #add_cname').value = obj_val.cname;       // 將欄位帶入數值 = cname
            document.querySelector('#add_user_modal #add_user').value = obj_val.user;         // 將欄位帶入數值 = user
            resetMain()                                                                 // 清除搜尋頁面資料
            document.getElementById("bt_addUser").click();                              // 切換返回到addUser新增頁面
        }
    }

    function change_modal_mode(mode){
        $('#modal_mode').empty();
        if(mode == 'edit'){
            $("#searchUser_btn").addClass("unblock");
            $('#modal_mode').append(mode);
        }else{
            $("#searchUser_btn").removeClass("unblock");
            $('#modal_mode').append('add');
        }
    }

    // fun-1.鋪編輯畫面
    function edit_module(mode, edit_user_id){
        // remark: to_module = 來源與目的 site、fab、local
        change_modal_mode(mode);

        // step1.將原排程陣列逐筆繞出來
        $('#info_append, #pic_append').empty();

        Object(AllUsers).forEach(function(row){          
            if(row['user'] == edit_user_id){
                // step2.鋪畫面到module
                Object.keys(row).forEach(function(item_key){
                    if(item_key === 'cate_no'){
                        item_value = row['cate_no']+'.'+row['cate_title'];
                    }else{
                        // item_value = row[item_key]; 
                        // $('#item_key').append(row[item_key]);
                        // document.getElementsByName(item_key).value = row[item_key];
                        var row_key = document.querySelector('#add_'+item_key);
                        if(row_key){
                            row_key.value = row[item_key]; 
                        }

                    }
                    console.log(item_key, row[item_key]);
                    // $('#info_append').append('<b>'+window[to_module+'_item'][item_key]+'：</b> '+item_value+'</br>');
                })
                // $('#pic_append').append('<img src="../catalog/images/'+row['PIC']+'" style="width: 100%;" class="img-thumbnail">');     // 圖片
                return; // 假設每個<cata_SN>只會對應到一筆資料，找到後就可以結束迴圈了
            }
        })

    }

    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
</script>

<?php include("../template/footer.php"); ?>