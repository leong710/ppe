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
    $activeTab = (isset($_REQUEST["activeTab"])) ? $_REQUEST["activeTab"] : "nav_btn_1";       // nav_btn_1= PM名單

    // 這裡讀取狀態：none正常、new新人、pause停用
    $showAllUsers = showAllUsers("");

    // $sites = show_site();
    $fabs = show_fab();

?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
    <script src="../../libs/sweetalert/sweetalert.min.js"></script>                         <!-- 引入 SweetAlert -->
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
                            <button type="button" id="nav_btn_1" class="nav-link" value="0,1,2" onclick=" groupBy_role(this.value)" ><i class="fa-solid fa-circle-user"></i>&nbspPM名單
                                <span id="none" class="badge bg-success"></span></button>
                        </li>
                        <li class="nav-item">
                            <button type="button" id="nav_btn_2" class="nav-link" value="3" onclick=" groupBy_role(this.value)" ><i class="fa-solid fa-ghost"></i>&nbsp一般使用者
                                <span id="new" class="badge bg-danger"></span></button>
                        </li>
                        <li class="nav-item">
                            <button type="button" id="nav_btn_3" class="nav-link" value=" " onclick=" groupBy_role(this.value)" ><i class="fa-solid fa-ban"></i>&nbsp停用
                                <span id="pause" class="badge bg-secondary"></span></button>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" id="role_info_btn" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#role_info" > <i class="fa fa-info-circle" aria-hidden="true"></i> 權限說明</button>
                    <button type="button" id="add_usere_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#user_modal" onclick="add_module('user')" > <i class="fa fa-user-plus"></i> 新增</button>
                </div>
            </div>
            <!-- dataTable -->
            <div class="col-12 p-4 pt-0  ">
                <table id="user_table" class="table table-striped table-hover">
                    <thead> 
                        <tr>
                            <th>id</th>
                            <th class="unblock">role</th>
                            <th data-toggle="tooltip" data-placement="bottom" title="** 紅色字體為非在職名單 ~">emp_id / cName / user</th>
                            <th>fab_id</th>
                            <th>sfab_id</th>
                            <th>role▼</th>
                            <th>idty</th>
                            <th>created_at</th>
                            <th>action</th>
                        </tr>
                    </thead>
                    <!-- user list -->
                    <tbody>
                        <?php foreach($showAllUsers as $user_row){ ?>
                            <tr>
                                <td><?php echo $user_row["id"];?></td>
                                <td class="unblock"><?php echo $user_row["role"];?></td>
                                <td class="t_left" id="<?php echo 'emp_id_'.$user_row["emp_id"];?>"><?php echo $user_row["emp_id"]." / ".$user_row["cname"]." / ";?><a href="#" data-bs-toggle="modal" data-bs-target="#user_modal" onclick="edit_module('user',<?php echo $user_row['id'];?>)"><?php echo $user_row["user"]; ?></a></td>
                                <td class="t_left" title="<?php echo $user_row["fab_remark"];?>"><?php echo $user_row["fab_id"]."_".$user_row["fab_title"]; if($user_row["fab_flag"] == "Off"){ ?><sup class="text-danger">-已關閉</sup><?php } ?></td>
                                <td><?php echo $user_row["sfab_id"]; ?></td>
                                <td class="text-start" <?php if($user_row["role"] == "0"){ ?> style="background-color:yellow" <?php } ?>>
                                    <?php switch($user_row["role"]){
                                        case "0": echo "0.&nbsp管理"; break;
                                        case "1": echo "1.&nbspPM"; break;
                                        case "2": echo "2.&nbspsiteUser"; break;
                                        case "3": echo "3.&nbspnoBody"; break;
                                        default: echo "【&nbsp停用&nbsp】";} ?></td>
                                <td class="text-start"><?php echo $user_row["idty"];?>
                                    <?php switch($user_row["idty"]){
                                        case "0": echo ".&nbsp管理"; break;
                                        case "1": echo ".&nbsp工程師"; break;
                                        case "2": echo ".&nbsp課副理"; break;
                                        case "3": echo ".&nbsp部經理"; break;
                                        case "4": echo ".&nbsp廠處長"; break;
                                        default: echo "【&nbsp停用&nbsp】";} ?></td>
                                <td title="<?php echo $user_row["created_at"];?>"><?php echo substr($user_row["created_at"],0,10);?></td>
                                <td>
                                    <button type="button" value="<?php echo $user_row["id"];?>" class="btn btn-sm btn-xs btn-secondary" title="編輯"
                                        data-bs-toggle="modal" data-bs-target="#user_modal" onclick="edit_module('user',this.value)" ><i class="fa-solid fa-pen-to-square"></i></button>
                                    <?php if($user_row["role"] == ""){ ?>
                                        <form action="" method="post" style="display: inline-block;">
                                            <input type="hidden" name="id" value="<?php echo $user_row["id"];?>">
                                            <button type="submit" name="submit_delete_user" class="btn btn-sm btn-xs btn-danger" title="刪除" onclick="return confirm('確認刪除？')"><i class="fa-solid fa-user-xmark"></i></button>
                                        </form>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <hr>
                <div class="row">
                    <div class="col-6 col-md-6 py-0">
                        <input type="hidden" name="emp_id" id="recheck_user" >
                    </div>
                    <div class="col-6 col-md-6 py-0 text-end" style="font-size: 12px;">
                        202403 updated modal
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- 模組-權限說明 -->
    <div class="modal fade" id="role_info" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border rounded bg-success text-white p-3 m-2">
                    <h5 class="modal-title"><i class="fa-solid fa-circle-info"></i> role權限說明</h5>
                    <button type="button" class="btn-close border rounded mx-1" data-bs-dismiss="modal" aria-label="Close"></button>
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
    <div class="modal fade" id="user_modal" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border rounded p-3 m-2">
                    <h5 class="modal-title"><i class="fa-solid fa-circle-info"></i> <span id="user_modal_action"></span> local user role</h5>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="user_delete_id">&nbsp&nbsp&nbsp&nbsp&nbsp
                        <span id="user_modal_delect_btn" class="<?php echo ($_SESSION[$sys_id]["role"] == 0) ? "":" unblock ";?>"></span>
                    </form>
                    <button type="button" class="btn-close border rounded mx-1" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="" method="post" class="needs-validation">
                    <div class="modal-body px-3">
                        <!-- line 1 -->
                        <div class="row">
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating input-group">
                                    <input type="text" name="user" id="user" class="form-control" data-toggle="tooltip" data-placement="bottom" title="請輸入查詢對象 工號、姓名或NT帳號" required  onchange="search_fun('search');">
                                    <label for="user" class="form-label">user ID：<sup class="text-danger"> *</sup></label>
                                    <button type="button" class="btn btn-outline-primary" onclick="search_fun('search')"><i class="fa-solid fa-magnifying-glass"></i> 搜尋</button>
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
                                <span class="form-label">副sfab_id：<sup class="text-danger"><?php echo ($_SESSION["AUTH"]["role"] >= 2 ) ? " - disabled":" 選填" ?></sup></span>
                                <div class="border rounded p-2">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <?php $i = 0; foreach($fabs as $fab){ ?>
                                                    <td class="text-start">
                                                        <input type="checkbox" name="sfab_id[]" value="<?php echo $fab["id"];?>" id="sfab_id_<?php echo $fab["id"];?>" class="form-check-input" >
                                                        <label for="sfab_id_<?php echo $fab["id"];?>" class="form-check-label">&nbsp<?php echo $fab["id"].".&nbsp".$fab["fab_title"];?></label>
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
                            
                            <span id="activeTab" ></span>
                            <input type="hidden" name="id" id="user_edit_id" >
                            
                            <span id="user_modal_button" class="<?php echo ($_SESSION[$sys_id]["role"] <= 1) ? "":" unblock ";?>"></span>
                            <input type="reset" class="btn btn-info" id="user_reset_btn" onclick="$('#emp_id, #cname, #user, #idty').removeClass('autoinput');" value="清除">
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
<script src="../../libs/aos/aos.js"></script>               <!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/aos/aos_init.js"></script>          <!-- goTop滾動畫面script.js 4/4-->
<script>    
    // modal
    var user_modal  = new bootstrap.Modal(document.getElementById('user_modal'), { keyboard: false });
    var searchUser_modal = new bootstrap.Modal(document.getElementById('searchUser'), { keyboard: false });
    var user        = <?=json_encode($showAllUsers)?>;
    var user_item   = ['id','user','cname','emp_id','idty','role','fab_id','sfab_id'];          // 交給其他功能帶入
    var tags        = [];                                                                       // fun3-1：search Key_word
    var swal_json   = <?=json_encode($sw_json)?>;
    var activeTab   = '<?=$activeTab?>';                                                        //设置要自动选中的选项卡的索引（从0开始）

    $(function () {
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();
        // Alex menu
        var navs = Array.from(document.querySelectorAll(".head > ul > li > button"));
        navs.forEach((nav)=>{
            nav.addEventListener('mousedown',function(){
                // 標籤
                document.querySelector(".head > ul > li > button.active").classList.remove('active');
                this.classList.add('active');
                show_activeTab(this.id);          // 呼叫fun竄改activeTab按鈕+數值
            })
        })

        // 監聽表單內 input 變更事件
        $('#emp_id, #cname, #user, #idty').change(function() {
            $(this).removeClass('autoinput');   // 當有變更時，對該input加上指定的class
        });

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
    function search_fun(fun){
        mloading("show");                                               // 啟用mLoading
        const uuid = '752382f7-207b-11ee-a45f-2cfda183ef4f';            // ppe

        if(fun=='search'){
            var search = $('#user').val().trim();                       // search keyword取自user欄位
            if(!search || (search.length < 2)){
                $("body").mLoading("hide");
                alert("查詢字數最少 2 個字以上!!");
                return false;
            } 
            var request = {
                functionname : 'search',                                // 操作功能
                uuid         : uuid,                                    // ppe
                search       : search                                   // 查詢對象key_word
            }

        }else if(fun=='showStaff'){
            var search = $('#recheck_user').val().trim();               // search keyword取自user欄位
            if(!search || (search.length < 2)){
                $("body").mLoading("hide");
                alert("查詢字數最少 2 個字以上!!");
                return false;
            } 
            var request = {
                functionname : 'showStaff',                             // 操作功能
                uuid         : uuid,                                    // ppe
                emp_id       : search                                   // 查詢對象key_word
            }

        }else{
            return false;
        }

        $.ajax({
            // url:'http://tneship.cminl.oa/hrdb/api/index.php',        // 正式舊版
            url: 'http://tneship.cminl.oa/api/hrdb/index.php',          // 正式2024新版
            method: 'post',
            dataType: 'json',
            data: request,
            success: function(res){
                var res_r = res["result"];
                if(fun=='search'){
                    postList(res_r);                                        // 將結果轉給postList進行渲染
                }else{
                    var emp_id_search = document.querySelector('#emp_id_'+search);
                    if(res_r['emp_id'] == undefined && emp_id_search){
                        emp_id_search.classList.add('alert_it');
                    }
                }
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
            let user_json = '{"emp_id":"'+res_r[i].emp_id+'","cname":"'+ res_r[i].cname+'","user":"'+ res_r[i].user+'","cstext":"'+ res_r[i].cstext+'"}';
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
        $("body").mLoading("hide");                                 // 關閉mLoading
        // document.getElementById("searchUser_btn").click();       // 切到searchUser頁面
        user_modal.hide();
        searchUser_modal.show();                                    // 切到searchUser頁面

    }
    // 第二階段：點選、渲染模組
    function tagsInput_me(val) {
        if (val !== '') {
            let obj_val = JSON.parse(val);                  // 將JSON字串轉成Object物件
            // 渲染
            Object.entries(obj_val).forEach(function([user_key, user_value]){
                if(user_key == "cstext"){
                    // 使用正则表达式的 exec 方法来查找目标字符串中的匹配项
                    var idty = document.getElementById('idty');
                    if(idty){
                        // 创建正则表达式模式和对应的数值映射
                        const patterns = {
                            "副理": 2,
                            "經理": 3,
                            "處長": 4
                        };
                        let match;
                        for (const [pattern, value] of Object.entries(patterns)) {
                            const regex = new RegExp(pattern, 'gi');
                            if ((match = regex.exec(obj_val.cstext)) !== null) {
                                document.querySelector('#user_modal #idty').value = value; // 将字段带入值 = 职称.副理
                                break;          // 找到匹配项后，跳出循环
                            }
                        }
                        $("#idty").addClass("autoinput");
                    }
                }else{
                    var tag_key = document.getElementById(user_key);
                    if(tag_key){
                        tag_key.value = user_value;
                        $("#"+user_key).addClass("autoinput");
                    }
                }
            })
            resetMain()                                                             // 清除搜尋頁面資料
            searchUser_modal.hide();      // 切到searchUser頁面
            user_modal.show();            // 切換返回到addUser新增頁面
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
        // var del_btn = '<input type="submit" name="submit_delete_'+to_module+'" value="刪除'+to_module+'" class="btn btn-sm btn-xs btn-danger" onclick="return confirm(`確認刪除？`)">';
        var del_btn ='<button type="submit" name="submit_delete_'+to_module+'" title="刪除" class="btn btn-sm btn-xs btn-danger" onclick="return confirm(`確認刪除？`)"><i class="fa-solid fa-user-xmark"></i></button>';
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

    function show_swal(swal_json){
        swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:3000});         // 3秒
    }

    // 空值遮蔽：On、Off
    function groupBy_role(role_value){
        mloading("show");                                               // 啟用mLoading
        const arr_role = role_value.split(',').map(item => parseInt(item));
        var table_tr = document.querySelectorAll('#user_table > tbody > tr');
        table_tr.forEach(function(row){
            var row_role = parseInt(row.children[1].innerText); // 將字串轉換為數字
            if(arr_role.includes(row_role)){
                row.classList.remove('unblock');
            } else {
                row.classList.add('unblock');
            }
        })  
        $("body").mLoading("hide");
    }
    // user分類算人頭
    function count_role(){
        var count_role_arr = {
            "none"  : 0,
            "new"   : 0,
            "pause" : 0
        };
        Object(user).forEach(function(row){
            var row_role = parseInt(row['role']); // 將字串轉換為數字
            if(row_role >= 0 && row_role <= 2 ){
                count_role_arr["none"]++;
            }else if(row_role == 3){
                count_role_arr["new"]++;
            }else{
                count_role_arr["pause"]++;
            }
        })
        // 渲染
        Object.entries(count_role_arr).forEach(function([key, value]){
            $('#'+key).append(value);                   // 填上數量
        })
    }
    // 竄改user_modal activeTab按鈕+數值
    function show_activeTab(active_no){
        let activeTab_input = '<input type="hidden" name="activeTab" value="'+active_no+'"></input>';
        $('#activeTab').empty();
        $('#activeTab').append(activeTab_input);
    }
    // recheck user
    function recheck_user(){
        Object(user).forEach(function(row){
            let emp_id = row['emp_id'];
            $('#recheck_user').empty();                             // 清除recheck_user input功能
            document.getElementById('recheck_user').value = emp_id;
            search_fun('showStaff');
        })
    }

    $(document).ready(function(){
        // show swal
        if(swal_json.length != 0){ show_swal(swal_json); }
        // recheck user
        recheck_user();
        // user分類算人頭
        count_role();
        // NAV select 1
        // groupBy_role('0,1,2');
        // 切換指定NAV分頁btn
        // document.querySelector(".head > ul > li > button.active").classList.remove('active');       // 移除激活
        document.querySelector("#"+activeTab).classList.add('active');                              // 激活选项卡
        $("#"+activeTab).click();                                                                   // 點選選項卡以便套用groupBy_role(...)
        show_activeTab(activeTab);                                                                  // 呼叫fun竄改user_modal activeTab按鈕+數值

    });
    
</script>

<?php include("../template/footer.php"); ?>