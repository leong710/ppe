<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("pt_local_function.php");
    accessDenied($sys_id);

    // 複製本頁網址藥用
    if(isset($_SERVER["HTTP_REFERER"])){
        $up_href = $_SERVER["HTTP_REFERER"];            // 回上頁
    }else{
        $up_href = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; // 回本頁
    }

    $auth_emp_id = $_SESSION["AUTH"]["emp_id"];     // 取出$_session引用
    $sys_role    = $_SESSION[$sys_id]["role"];      // 取出$_session引用

    // CRUD module function --
        if(isset($_POST["ptlocal_submit"])){ store_ptlocal($_REQUEST); }        // 新增
        if(isset($_POST["edit_ptlocal_submit"])){ update_ptlocal($_REQUEST); }  // 更新
        if(isset($_POST["delete_ptlocal"])){ delete_ptlocal($_REQUEST); }       // 刪除
        // 調整flag ==> 20230712改用AJAX

    // 組合查詢陣列 -- 把fabs讀進來作為[篩選]的select option
        // 1-1a 將fab_id加入sfab_id
        if(isset($_SESSION[$sys_id]["fab_id"])){
            $fab_id = $_SESSION[$sys_id]["fab_id"];              // 1-1.取fab_id
        }else{
            $fab_id = "0";
        }
        $sfab_id = $_SESSION[$sys_id]["sfab_id"];                // 1-1.取sfab_id
        if(!in_array($fab_id, $sfab_id)){                        // 1-1.當fab_id不在sfab_id，就把部門代號id套入sfab_id
            array_push($sfab_id, $fab_id);
        }
        // 1-1b 將sign_code涵蓋的fab_id加入sfab_id
        if(isset($_SESSION["AUTH"]["sign_code"])){
            $auth_sign_code["sign_code"] = $_SESSION["AUTH"]["sign_code"];
            $coverFab_lists = show_coverFab_lists($auth_sign_code);
            if(!empty($coverFab_lists)){
                foreach($coverFab_lists as $coverFab){
                    array_push($sfab_id, $coverFab["id"]);
                }
            }
        }
        // 1-1c sfab_id是陣列，要轉成字串
        $sfab_id_str = implode(",", $sfab_id);                   // 1-1c sfab_id是陣列，要轉成字串

    // 1-2 組合查詢條件陣列
        if($sys_role <=1 ){
            $sort_sfab_id = "All";                // All = admin/pm
            // $sort_sfab_id = $sfab_id_str;         // test = user
        }else{
            $sort_sfab_id = $sfab_id_str;         // allMy 1-2.將字串sfab_id加入組合查詢陣列中
        }

    // 查詢篩選條件：fab_id
        if(isset($_REQUEST["select_fab_id"])){     // 有帶查詢，套查詢參數
            $select_fab_id = $_REQUEST["select_fab_id"];
        }else{                              // 先給預設值
            if($sys_role <=1 ){
                $select_fab_id = "All";                // All
            }else{
                $select_fab_id = "allMy";         // allMy 1-2.將字串sfab_id加入組合查詢陣列中
            }
        }
    
    // 3.組合查詢陣列
        $query_arr = array(
            'select_fab_id' => $select_fab_id,
            'sfab_id'       => $sort_sfab_id
        );

    // init.1_index fab_list：role <=1 ? All+all_fab : sFab_id+allMy => select_fab_id
        $fabs = show_fab_list($query_arr);               // index FAB查詢清單用
    // init.2_create：local by select_fab_id / edit：local by All/allMy
        $ptlocals = show_fabs_local($query_arr);
    // init.7_
        $select_fab = [];
        if($select_fab_id != 'All' && $select_fab_id != "allMy"){
            $select_fab = show_select_fab($query_arr);                   // 查詢fab的細項結果
        }

    // 切換指定NAV分頁
    if(isset($_REQUEST["activeTab"])){
        $activeTab = $_REQUEST["activeTab"];
    }else{
        $activeTab = "2";       // 2 = local
    }

?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <!-- Jquery -->
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
    <!-- dataTable參照 https://ithelp.ithome.com.tw/articles/10230169 -->
        <!-- data table CSS+JS -->
        <link rel="stylesheet" type="text/css" href="../../libs/dataTables/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="../../libs/dataTables/jquery.dataTables.js"></script>
    <!-- mloading JS -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <!-- mloading CSS -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <style>
        .body > ul {
            padding-left: 0px;
        }
    </style>
    <script>    
        // loading function
        function mloading(){
            $("body").mLoading({
                icon: "../../libs/jquery/Wedges-3s-120px.gif",
            }); 
        }
        // finished loading關閉mLoading提示
        window.addEventListener("load", function(event) {
            $("body").mLoading("hide");
        });
        mloading();    // 畫面載入時開啟loading
    </script>
</head>
<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_12 col-12 rounded" style="background-color: rgba(255, 255, 255, .8);">
                <!-- NAV分頁標籤與統計 -->
                <div class="col-12 pb-0 px-0">
                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a class="nav-link " href="index.php">除汙器材庫存管理</span></a></li>
                        <?php if($sys_role <= 1){?>
                            <li class="nav-item"><a class="nav-link active " href="pt_local.php">除汙儲存點管理</span></a></li>
                            <li class="nav-item"><a class="nav-link " href="low_level.php">儲存點安量管理</span></a></li>
                            <li class="nav-item">
                                <button type="button" id="doCSV_btn" class="nav-link" data-bs-toggle="modal" data-bs-target="#checkList">
                                    <i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>&nbsp打開點檢表</button>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <!-- 內頁 -->
                <div class="col-12 bg-white">
                    <!-- by各Local儲存點： -->
                    <div class="row">
                        <div class="col-md-4 pb-0">
                            <h5><?php echo isset($select_fab["id"]) ? $select_fab["id"].".".$select_fab["fab_title"]." (".$select_fab["fab_remark"].")":"$select_fab_id";?>_除汙儲存點管理： </h5>
                        </div>
                        <!-- sort/groupBy function -->
                        <div class="col-md-4 pb-0">
                            <form action="" method="POST">
                                <div class="input-group">
                                    <span class="input-group-text">篩選</span>
                                    <select name="select_fab_id" id="groupBy_fab_id" class="form-select" onchange="this.form.submit()">
                                        <option value="" hidden selected >-- 請選擇local --</option>
                                        <?php if($sys_role <= 1 ){ ?>
                                            <option for="select_fab_id" value="All" <?php echo $select_fab_id == "All" ? "selected":"";?>>-- All fab --</option>
                                        <?php } ?>
                                        <option for="select_fab_id" value="allMy" <?php echo $select_fab_id == "allMy" ? "selected":"";?> title="<?php echo $sort_sfab_id;?>">
                                            -- All my fab <?php echo $sfab_id_str ? "(".$sfab_id_str.")":"";?> --</option>
                                        <?php foreach($fabs as $fab){ ?>
                                            <option value="<?php echo $fab["id"];?>" <?php echo $fab["id"] == $select_fab_id ? "selected":"";?>>
                                                <?php echo $fab["id"]."：".$fab["site_title"]."&nbsp".$fab["fab_title"]."( ".$fab["fab_remark"]." )"; echo ($fab["flag"] == "Off") ? " - (已關閉)":"";?></option>
                                        <?php } ?>
                                    </select>
                                    <!-- <button type="submit" class="btn btn-outline-secondary">查詢</button> -->
                                </div>
                            </form>
                        </div>
                        <!-- 表頭按鈕 -->
                        <div class="col-md-4 pb-0 text-end">
                            <?php if($sys_role <= 1){ ?>
                                <button type="button" id="add_ptlocal_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_ptlocal" onclick="add_module('ptlocal')" > <i class="fa fa-plus"></i> 新增除汙儲存點</button>
                            <?php } ?>
                        </div>
                        <!-- Bootstrap Alarm -->
                        <div id="liveAlertPlaceholder" class="col-12 mb-0 pb-0"></div>
                    </div>
                    <hr>
                    <!-- 這裡開始抓SQL裡的紀錄來這裡放上 -->
                    <table id="local_list" class="table table-striped table-hover">
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
                                    <td style="font-size: 6px;"><?php echo $ptlocal['id']; ?></td>
                                    <td class="text-start"><?php echo $ptlocal['fab_id']."_".$ptlocal['fab_title']." (".$ptlocal['fab_remark'].")"; if($ptlocal["fab_flag"] == "Off"){ ?><sup class="text-danger">-已關閉</sup><?php } ?></td>
                                    <td class="text-start"><?php echo $ptlocal['local_title']." (".$ptlocal['local_remark'].")"; ?></td>
                                    <td><a href="low_level.php?ptlocal_id=<?php echo $ptlocal['id'];?>" class="btn btn-sm btn-xs <?php echo !empty($ptlocal['low_level']) ? "btn-success":"btn-warning";?>">
                                        <?php echo !empty($ptlocal['low_level']) ? "已設定":"未設定";?></a></td>
                                    <td><?php if($sys_role <= 1){ ?>  
                                            <button type="button" name="pt_local" id="<?php echo $ptlocal['id'];?>" class="btn btn-sm btn-xs flagBtn <?php echo $ptlocal['flag'] == 'On' ? 'btn-success':'btn-warning';?>" 
                                                value="<?php echo $ptlocal['flag'];?>"><?php echo $ptlocal['flag'];?></button>
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
                </br>
                <!-- 尾段：debug訊息 -->
                <?php if(isset($_REQUEST["debug"])){
                    echo "<hr>";
                    include("debug_board.php"); 
                } ?>
            </div>
        </div>
    </div>

<!-- 彈出畫面模組 新增編輯ptLocal 20240122 -->
    <div class="modal fade" id="edit_ptlocal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="ptlocal_modal_action"></span>除汙儲存點資訊</h4>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="ptlocal_delete_id">
                        <?php if($sys_role == 0){ ?>
                            &nbsp&nbsp&nbsp&nbsp&nbsp
                            <span id="ptlocal_modal_delect_btn"></span>
                        <?php } ?>
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
                                            <option value="<?php echo $fab["id"];?>" for="edit_fab_id" <?php echo ($fab["id"] == $select_fab_id) ? "selected":"";?>>
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
                            <!-- 最後編輯資訊 -->
                            <div class="col-12 text-end p-0" id="edit_ptlocal_info"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="activeTab" value="3">
                            <input type="hidden" name="id" id="ptlocal_edit_id" >
                            <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                            <input type="hidden" name="select_fab_id" value="<?php echo $select_fab_id;?>">
                            <?php if($sys_role <= 1){ ?>   
                                <span id="ptlocal_modal_button"></span>
                            <?php } ?>
                            <input type="reset" class="btn btn-info" id="ptlocal_reset_btn" value="清除">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>

<!-- toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast align-items-center bg-warning" role="alert" aria-live="assertive" aria-atomic="true" autohide="true" delay="1000">
            <div class="d-flex">
                <div class="toast-body" id="toast-body"></div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
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

    var ptlocal  = <?=json_encode($ptlocals);?>;                                                    // 引入locals資料
    var ptlocal_item = ['id','fab_id','local_title','local_remark','flag'];                      // 交給其他功能帶入 delete_local_id
    
</script>

<script src="pt_local.js?v=<?=time();?>"></script>

<?php include("../template/footer.php"); ?>