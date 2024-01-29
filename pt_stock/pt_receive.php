<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    // accessDenied($sys_id);
    accessDeniedAdmin($sys_id);

    // 複製本頁網址藥用
    if(isset($_SERVER["HTTP_REFERER"])){
        $up_href = $_SERVER["HTTP_REFERER"];            // 回上頁
    }else{
        $up_href = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; // 回本頁
    }

    $auth_emp_id = $_SESSION["AUTH"]["emp_id"];     // 取出$_session引用
    $sys_role    = $_SESSION[$sys_id]["role"];      // 取出$_session引用

    // CRUD module function --
        if(isset($_POST["ptreceive_store"])) { store_ptreceive($_REQUEST); }   // 新增
        if(isset($_POST["ptreceive_update"])){ update_ptreceive($_REQUEST); }  // 更新
        if(isset($_POST["ptreceive_delete"])){ delete_ptreceive($_REQUEST); }  // 刪除
        // 調整flag ==> 20230712改用AJAX

    // 組合查詢陣列 -- 把fabs讀進來作為[篩選]的select option
        // 1-1a 將fab_id加入sfab_id
        $sfab_id_str = get_sfab_id($sys_id, "str");     // 1-1c sfab_id是陣列，要轉成字串str

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
            'sfab_id'       => $sfab_id_str
        );

    // init.1_index fab_list：role <=1 ? All+all_fab : sFab_id+allMy => select_fab_id
        $fabs = show_fab_list($query_arr);               // index FAB查詢清單用
    // init.2_create：local by select_fab_id / edit：local by All/allMy
        $ptlocals = show_fabs_local($query_arr);
        $ptreceives = show_ptreceive($query_arr);
    // init.3_create/edit catalog by cate_no = J
        // $ptcatalogs  = show_ptcatalogs();                   // 取得所有catalog - J項目，供create使用
    // init.4_
        $ptstocks     = show_ptstock($query_arr);            // 依查詢條件儲存點顯示存量
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

    // echo "<pre>";
    // print_r($_REQUEST);
    // print_r($query_arr);
    // print_r($select_fab_id);
    // // print_r($select_locals_id);
    // print_r($sort_sfab_id);
    // print_r($ptstocks);
    // echo "</pre>";

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
                        <li class="nav-item"><a class="nav-link active" href="pt_receive.php">領用記錄</span></a></li>
                        <?php if($sys_role <= 1){?>
                            <li class="nav-item"><a class="nav-link " href="pt_local.php">除汙儲存點管理</span></a></li>
                            <li class="nav-item"><a class="nav-link " href="low_level.php">儲存點安量管理</span></a></li>
                        <?php } ?>
                    </ul>
                </div>
                <!-- 內頁 -->
                <div class="col-12 bg-white">
                    <!-- by各Local儲存點： -->
                    <div class="row">
                        <div class="col-md-4 pb-0">
                            <h5><?php echo isset($select_fab["id"]) ? $select_fab["id"].".".$select_fab["fab_title"]." (".$select_fab["fab_remark"].")":"$select_fab_id";?>_領用記錄管理： </h5>
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
                                <!-- <button type="button" id="add_ptlocal_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_ptlocal" onclick="add_module('ptlocal')" > <i class="fa fa-plus"></i> 新增除汙儲存點</button> -->
                            <?php } ?>
                        </div>
                        <!-- Bootstrap Alarm -->
                        <div id="liveAlertPlaceholder" class="col-12 mb-0 pb-0"></div>
                    </div>
                    <hr>
                    <!-- 這裡開始抓SQL裡的紀錄來這裡放上 -->
                    <table id="receive_list" class="table table-striped table-hover">
                        <thead>
                            <tr class="">
                                <th>領用日期</th>
                                <th>開單人</th>
                                <th>需求類別</th>
                                <th>領用說明</th>
                                    <th>fab_local</th>
                                    <th>SN/名稱</th>
                                    <th>使用量</th>
                                    <th>批號/期限</th>
                                <th>最後更新</th>
                                <?php if($sys_role <= 1){ ?>    
                                    <th>action</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ptreceives as $ptreceive){ 
                                    $item = (array) json_decode($ptreceive['item']);    // 
                                    $item_count = count($item);
                                ?>
                                <tr>
                                    <td <?php echo $item_count >1 ? "rowspan='{$item_count}'":"";?> title="<?php echo $ptreceive['id'].'-'.$item_count;?>"><?php echo $ptreceive['app_date'];?></td>
                                    <td <?php echo $item_count >1 ? "rowspan='{$item_count}'":"";?>><?php echo $ptreceive['cname']."</br>(".$ptreceive['emp_id'].")";?></td>
                                    <td <?php echo $item_count >1 ? "rowspan='{$item_count}'":"";?>><?php echo $ptreceive['ppty']; ?></td>
                                    <td <?php echo $item_count >1 ? "rowspan='{$item_count}'":"";?>><?php echo $ptreceive['receive_remark']; ?></td>
                                    <?php
                                        $i = 0 ;
                                        foreach(array_keys($item) as $item_key){
                                            $item_key_arr = explode(",", $item_key);
                                                if($item_key_arr[0]){ $t_cata_SN = $item_key_arr[0]; } else { $t_cata_SN = ""; }            // cata_SN 序號
                                                if($item_key_arr[1]){ $t_stk_id  = $item_key_arr[1]; } else { $t_stk_id  = ""; }            // stock_id 儲存id
                                                
                                            $item_value = (array) $item[$item_key];
                                                if($item_value["pay"]){ $t_amount  = $item_value["pay"]; } else { $t_amount  = ""; }        // amount 數量       
                                            $item_value_arr = explode(",", $item_value["need"]);
                                                if($item_value_arr[0]){ $t_po_no   = $item_value_arr[0]; } else { $t_po_no   = ""; }        // po_no po號碼
                                                if($item_value_arr[1]){ $t_lot_num = $item_value_arr[1]; } else { $t_lot_num = ""; }        // lot_num 批號
                                            
                                            // stk_id & cata_id
                                            foreach($ptstocks as $ptstock){
                                                if($ptstock["id"] ==  $t_stk_id){
                                                    echo '<td class="text-start">'.$ptstock["fab_title"].'_'.$ptstock["local_title"].'</td>';
                                                    echo '<td class="text-start">'.$ptstock["cata_SN"].'_'.$ptstock["pname"].'</td>';
                                                    break;
                                                }
                                            }
                                            echo "<td>".$t_amount."</td><td>".$t_lot_num."</td>";

                                            if($i == 0){ ?>
                                                <td <?php echo $item_count >1 ? "rowspan='{$item_count}'":"";?> style="font-size: 10px;"><?php echo $ptreceive['updated_at']."</br>".$ptreceive['updated_cname'];?></td>
                                                <td <?php echo $item_count >1 ? "rowspan='{$item_count}'":"";?> ><?php if($sys_role <= 1){ ?>    
                                                    <button type="button" id="edit_ptreceive_btn" value="<?php echo $ptreceive['id'];?>" class="btn btn-sm btn-xs btn-info" 
                                                        data-bs-toggle="modal" data-bs-target="#edit_ptreceive" onclick="edit_module('ptreceive',this.value)" >編輯</button>
                                                <?php } ?></td></tr>
                                            <?php } else {
                                                echo "</td>";
                                            }
                                            $i++;
                                        } 
                                    ?>
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

<!-- 彈出畫面模組 除汙器材領用 品項 -->
    <div class="modal fade" id="edit_ptreceive" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header add_mode_bgc">
                    <h4 class="modal-title"><span id="modal_action"></span>&nbsp除汙器材領用&nbsp</h4><span id="shopping_count" class="badge rounded-pill bg-danger"></span>

                    <form action="" method="post">
                        <input type="hidden" name="id" id="ptreceive_delete_id">
                        <?php if($sys_role <= 1){ ?>
                            &nbsp&nbsp&nbsp&nbsp&nbsp
                            <span id="modal_delect_btn"></span>
                        <?php } ?>
                    </form>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="" method="post">
                    <div class="modal-body p-4 pb-0">
                        <!-- 第一列 購物車 -->
                        <div class="col-12 rounded p-0" id="receive_shopping_cart">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>select</th>
                                        <th>儲存點位置</th>
                                        <th>名稱</th>
                                        <th>數量</th>
                                        <th>批號/期限</th>
                                    </tr>
                                </thead>
                                <tbody id="shopping_cart_tbody">
                                </tbody>
                            </table>
                        </div>
                
                        <!-- 第二排 申請數據 -->
                        <div class="col-12 rounded " style="background-color: #D3FF93;">
                            <div class="row">
                                <div class="col-6 col-md-6 py-1">
                                    <div class="form-floating pb-0">
                                        <input type="datetime-local" class="form-control" name="app_date" id="app_date" value="<?php echo date('Y-m-d\TH:i');?>" required>
                                        <label for="receive_app_date" class="form-label">領用日期：<sup class="text-danger">*</sup></label>
                                    </div>
                                </div>
                                <div class="col-6 col-md-6 py-1">
                                    <div style="display: flex;">
                                        <label for="ppty" class="form-label">ppty/需求類別：</label></br>&nbsp
                                        <input type="radio" name="ppty" value="0" id="ppty_0" class="form-check-input" >
                                        <label for="ppty_0" class="form-check-label">&nbsp臨時&nbsp&nbsp</label>
                                        <input type="radio" name="ppty" value="1" id="ppty_1" class="form-check-input" required checked >
                                        <label for="ppty_1" class="form-check-label">&nbsp定期&nbsp&nbsp</label>
                                        <input type="radio" name="ppty" value="3" id="ppty_3" class="form-check-input" >
                                        <label for="ppty_3" class="form-check-label text-danger" data-toggle="tooltip" data-placement="bottom" title="注意：事故須先通報防災!!">&nbsp緊急</label>
                                    </div>
                                </div>
                                <div class="col-12 py-1">
                                    <div class="form-floating">
                                        <input type="text" name="receive_remark" id="receive_remark" class="form-control" required>
                                        <label for="receive_remark" class="form-label">receive_remark/領用說明：<sup class="text-danger">*</sup></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 第三排提示 -->
                        <div class="col-12 rounded bg-light pt-0">
                            *** 注意：事故請務必填寫詳細案件名稱~!
                        </div>
                        <!-- 最後編輯資訊 -->
                        <div class="col-12 text-end p-0" id="edit_ptreceive_info"></div>
                    </div>

                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="id"            id="ptreceive_edit_id" >
                            <input type="hidden" name="select_fab_id" id="fab_id" value="<?php echo $select_fab_id;?>">
                            <input type="hidden" name="idty"          id="idty"   value="1"> <!-- idty:1 扣帳 -->
                            <input type="hidden" name="emp_id"        id="emp_id" value="<?php echo $auth_emp_id;?>">
                            <input type="hidden" name="created_cname" id="cname"  value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                            <input type="hidden" name="updated_cname" id="updated_cname" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                            <?php if($sys_role <= 2){ ?>   
                                <span id="modal_button">
                                    <input type="submit" class="btn btn-primary disabled" name="ptreceive_store" value="送出" id="receive_submit">
                                </span>
                            <?php } ?>
                            <input type="reset" class="btn btn-info" id="reset_btn" value="清除">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
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
    // // // 開局導入設定檔
    var ptreceive     = <?=json_encode($ptreceives);?>;                       // 引入div_stocks資料
    var ptreceive_item = ["id", "emp_id", "cname", "fab_id", "ppty", "receive_remark", "item", "idty", "app_date", "updated_cname"]    // 定義要抓的key, "created_at", "updated_at",
    var ptstock       = <?=json_encode($ptstocks);?>;                       // 引入div_stocks資料
    var swal_json     = [];                                                   // 引入swal_json值
    var action        = 'review';
    
</script>

<script src="pt_stock.js?v=<?=time();?>"></script>

<?php include("../template/footer.php"); ?>