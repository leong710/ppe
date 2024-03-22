<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDeniedAdmin($sys_id);

    if(isset($_POST["submit"])){ storeUser($_REQUEST); }
    if(isset($_POST["delete"])){ deleteUser($_REQUEST); }

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
        #key_word, #add_user{    
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
                    <a href="#role_info" target="_blank" title="權限說明" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#role_info"> <i class="fa fa-info-circle" aria-hidden="true"></i> 權限說明</a>
                    <a href="#" target="_blank" title="for新增user" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_user_modal"> <i class="fa fa-user-plus"></i> 新增</a>
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
                                <td><a href="edit.php?user=<?php echo $user_none["user"];?>" class="btn btn-sm btn-xs btn-info">編輯</a></td>
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
<!-- 模組-權限說明 -->
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
<!-- 模組-新增 -->
    <div class="modal fade" id="add_user_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg"> 
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">add local user role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-3">
                    <form action="" method="post" class="needs-validation">
                        <div class="row">
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating input-group">
                                    <input type="text" name="user" id="add_user" class="form-control" data-toggle="tooltip" data-placement="bottom" title="請輸入查詢對象 工號、姓名或NT帳號" required>
                                    <label for="add_user" class="form-label">user ID：<sup class="text-danger"> *</sup></label>
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
                        </div>
                        
                        <div class="row">
                            <div class="col-12 py-1">
                                <label for="" class="form-label">副sfab_id：<sup class="text-danger"><?php echo ($_SESSION["AUTH"]["role"] >= 2 ) ? " - disabled":" 選填" ?></sup></label>
                                <div class="border rounded p-2">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <?php $i = 0; foreach($fabs as $fab){ ?>
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
                        
                        <div class="text-end">
                            <button type="button" id="searchUser_btn" class="btn btn-warning unblock" data-bs-target="#searchUser" data-bs-toggle="modal">searchUser</button>
                            <input type="submit" name="submit" class="btn btn-primary" value="儲存" >
                            <input type="reset" name="reset" class="btn btn-info" onclick="$('#add_emp_id, #add_cname, #add_user').removeClass('autoinput');" value="清除">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        </div>
                    </form>
                </div>
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
                                <!-- 第三排的功能 : 放查詢結果-->
                                <div class="result" id="result">
                                    <table id="result_table" class="table table-striped table-hover"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="bt_addUser" class="btn btn-secondary" data-bs-target="#add_user_modal" data-bs-toggle="modal">Back to addUser</button>
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
<script>    // Alex menu
    var navs = Array.from(document.querySelectorAll(".head > ul > li > a"));
    var tbodys = Array.from(document.querySelectorAll("#table > tbody"))

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

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();

        // 監聽表單內 input 變更事件
        $('#add_emp_id, #add_cname, #add_user').change(function() {
            // 當有變更時，對該input加上指定的class
            $(this).removeClass('autoinput');
        });

        // // 遍歷表單內所有 input
            // $('#add_emp_id, #add_cname, #add_user').each(function() {
            //     // 如果input已有value，則對該input加上指定的class
            //     if ($(this).val()) {
            //         $(this).removeClass('autoinput');
            //     }
            // });
    })

    function resetMain(){
        $("#result").removeClass("border rounded bg-white");
        $('#result_table').empty();
        // document.querySelector('#key_word').value = '';
    }
    // 第一-階段：search Key_word
    function search_fun(){
        mloading("show");                       // 啟用mLoading
        let search = $('#add_user').val().trim();       // search keyword取自user欄位
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
                document.getElementById("searchUser_btn").click();      // 切到searchUser頁面
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
        $("body").mLoading("hide");                 // 關閉mLoading

    }
    // 第二階段：點選、渲染模組
    function tagsInput_me(val) {
        if (val !== '') {
            let obj_val = JSON.parse(val);                                                  // 將JSON字串轉成Object物件
            document.querySelector('#add_user_modal #add_emp_id').value = obj_val.emp_id;   // 將欄位帶入數值 = emp_id
            document.querySelector('#add_user_modal #add_cname').value = obj_val.cname;     // 將欄位帶入數值 = cname
            document.querySelector('#add_user_modal #add_user').value = obj_val.user;       // 將欄位帶入數值 = user
            $("#add_emp_id, #add_cname, #add_user").addClass("autoinput");
            resetMain()                                                                     // 清除搜尋頁面資料
            document.getElementById("bt_addUser").click();                                  // 切換返回到addUser新增頁面
        }
    }

</script>

<?php include("../template/footer.php"); ?>