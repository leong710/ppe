<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../user_info.php");
    require_once("function.php");
    accessDenied($sys_id);

    // add module function --
        $swal_json = array();
        if(isset($_POST["submit"])){ $swal_json = store_stock($_REQUEST); }             // 新增add
        if(isset($_POST["edit_stock_submit"])){ $swal_json = update_stock($_REQUEST); } // 編輯Edit
        if(isset($_POST["delete_stock"])){ $swal_json = delete_stock($_REQUEST); }      // 刪除delete

    $sort_sn = (isset($_REQUEST["sn"])) ? $_REQUEST["sn"] : NULL;   // 有帶查詢

    $catalog = edit_catalog($_REQUEST);                             // 上半部 器材資訊

    if(empty($catalog)){                                            // 查無資料時返回指定頁面
        echo "<script>history.back()</script>";                     // 用script導回上一頁。防止崩煃
    }

    $catalogStocks  = show_catalogStock($_REQUEST);                 // 下半部 庫存資訊=各廠庫存量
    $locals         = show_allLocal();                              // for器材撥補

    // 初始化半年後日期，讓系統判斷與highLight
    $toDay          = date('Y-m-d');
    $half_month     = date('Y-m-d', strtotime($toDay."+6 month -1 day"));

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
        #PIC img {
            max-height: 300px;
            /* text-align: center; */
            display: block; 
            margin: auto;
            transition: transform .6s ease-in-out;
        }
        #PIC:hover img {
            transform: scale(1.5);
        }
        .active {
            opacity: 1;
            transition: opacity 1s;
            animation: fadeIn 1s;
        }
        @keyframes fadeIn {
            from { opacity: 0;}
            to { opacity: 1;}
        }
        .unblock {
            opacity: 0;
            display: none;
            transition: opacity 1s;
            animation: none;
        }
        /* 凸顯可編輯欄位 */
            .fix_amount:hover {
                /* font-size: 1.05rem; */
                font-weight: bold;
                text-shadow: 3px 3px 5px rgba(0,0,0,.5);
            }
        /* 警示項目 amount、lot_num */
            .alert_amount {
                background-color: #FFBFFF;
                color: red;
                font-size: 1.2em;
            }
            .alert_lot_num {
                background-color: #FFBFFF;
                color: red;
            }
    </style>
</head>
<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <!-- 上半部 器材資訊 -->
            <div class="col-10 bg-light border p-4 rounded my-2">
                <div class="row">
                    <div class="col-12 col-md-6 py-0">
                        <h4>器材資訊</h4>
                    </div>
                    <div class="col-12 col-md-6 py-0 text-end">
                        <?php if($sys_role <= 1 ){ ?>
                            <button type="button" id="add_stock_btn" title="管理員限定" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#edit_stock" onclick="add_module('stock')"><i class="fa fa-upload" aria-hidden="true"></i> 單品撥補</button>
                        <?php } ?>
                        <button class="btn btn-secondary rtn_btn" onclick="history.back()"><i class="fa fa-external-link" aria-hidden="true"></i> 返回目錄</button>
                    </div>
                </div>
                <hr>
                <!-- 表頭：catalog訊息 -->
                <div class="col-12 mb-0 py-0">
                    <div class="row">
                        <div class="col-12 col-md-5 py-0 bg-white rounded">
                            <label>PIC/圖片：</label>
                            <div id="PIC" style="text-align:center;">
                                <a href="#" target="_blank" title="img preView" class="" data-bs-toggle="modal" data-bs-target="#about-1">
                                    <img src="images/<?php echo $catalog["PIC"]; ?>" class="img-thumbnail"></a>
                            </div>
                        </div>
                        <div class="col-12 col-md-7 py-0 rounded">
                            <table>
                                <tr>
                                    <th colspan="2">品項資訊 <i id="checkEye" class="fa-sharp fa-solid fa-lg fa-caret-up" title="查看更多"></i></th>
                                </tr>
                                <!-- 一般訊息 -->
                                    <tr>
                                        <td style="text-align:right;" width="20%">SN/編號：</td>
                                        <td style="text-align:left;" width="40%" title="id=<?php echo $catalog["id"];?>">
                                            <?php echo $catalog["SN"];?>&nbsp&nbsp
                                            <span class="badge <?php echo $catalog['flag'] == 'On' ? 'bg-success':'bg-warning text-dark';?>"><?php echo $catalog['flag'] == 'On' ? '上架':'下架';?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;">category/分類：</td>
                                        <td style="text-align: left;"><?php echo $catalog["cate_no"]."_".$catalog["cate_title"];?></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;">pname/品名：</td>
                                        <td style="text-align: left; word-break: break-all;"><?php echo $catalog["pname"];?></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;">cata_remark/敘述說明：</td>
                                        <td style="text-align: left;">
                                            <textarea name="cata_remark" id="cata_remark" <?php echo $catalog["cata_remark"] ? 'style="height: 90px;"':'';?> disabled><?php echo $catalog["cata_remark"];?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;">OBM/品牌/製造商：</td>
                                        <td style="text-align: left; word-break: break-all;" colspan="3"><?php echo $catalog["OBM"];?></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;">model/型號：</td>
                                        <td style="text-align: left; word-break: break-all;"><?php echo $catalog["model"];?></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;">size/尺寸：</td>
                                        <td style="text-align: left; word-break: break-all;"><?php echo $catalog["size"];?></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;">unit/單位：</td>
                                        <td style="text-align: left; word-break: break-all;"><?php echo $catalog["unit"];?></td>
                                    </tr>
                                <!-- 更多資訊 -->
                                    <tr class="more_info unblock">
                                        <td style="text-align: right;">SPEC/規格：</td>
                                        <td style="text-align: left;">
                                            <textarea name="SPEC" id="SPEC" <?php echo $catalog["SPEC"] ? 'style="height: 90px;"':'';?> disabled><?php echo $catalog["SPEC"];?></textarea>
                                        </td>
                                    </tr>
                                    <tr class="more_info unblock">
                                        <td style="text-align: right;">scomp_no/供應商：</td>
                                        <td style="text-align: left; word-break: break-all;"><?php echo $catalog["scomp_no"];?></td>
                                    </tr>
                            </table>
                        </div>
                    </div>

                    <div style="font-size: 12px;" class="text-end">
                        updated_at：<?php echo $catalog["updated_at"];?> / by：<?php echo $catalog["updated_user"];?>
                    </div>
                </div>
            </div>

            <!-- 下半部 庫存資訊 -->
            <div class="col-10 bg-light border p-4 rounded">
                <div class="row">
                    <div class="col-12 col-md-6 py-0">
                        <h4>庫存資訊</h4>
                    </div>
                    <div class="col-12 col-md-6 py-0 text-end">
                        <a class="btn btn-secondary rtn_btn" onclick="history.back()"><i class="fa fa-external-link" aria-hidden="true"></i> 返回目錄</a>
                    </div>
                </div>
                <hr>
                <!-- 各廠庫存量 -->
                <div class="col-12 mb-0">
                    <table>
                        <tr><th colspan="6">各廠庫存量</th></tr>
                        <tr>
                            <th style="font-weight:bold;">site 廠處_儲存點位置(說明)</th>
                            <th style="font-weight:bold;" data-toggle="tooltip" data-placement="bottom" title="相同儲區&相同品項將安全存量合併成一筆計算">安全 <i class="fa fa-info-circle" aria-hidden="true"></i></th>
                            <th style="font-weight:bold;">存量</th>
                            <th style="font-weight:bold;" data-toggle="tooltip" data-placement="bottom" title="效期小於6個月將highlight">批號/期限 <i class="fa fa-info-circle" aria-hidden="true"></i></th>
                            <th style="font-weight:bold;">PO num</th>
                            <th style="font-weight:bold;">action</th>
                        </tr>
                        <?php 
                            $sum = "0";         // 總量
                            $sum_s = "0";       // 安全存量
                            $sum_0 = "0";       // 現場存輛
                            $check_item =""; 
                            $check_local0 ="";
                        ?>
                        <?php foreach($catalogStocks as $catalogStock){ ?>
                            <tr <?php if($check_item != $catalogStock['fab_title']){?>style="border-top:3px #FFD382 solid;"<?php } ?>>
                                <td style="text-align: left;"><?php echo $catalogStock["site_title"]."&nbsp".$catalogStock["fab_title"]."_".$catalogStock["local_title"]."(".$catalogStock["local_remark"].")";?></td>
                                <td><?php echo $catalogStock["standard_lv"];?></td>
                                    <?php if($check_local0 != $catalogStock["site_title"]."_".$catalogStock["local_title"]){
                                            $sum_s += (int)$catalogStock["standard_lv"];    // 相同local+相同catelog將standard_lv(low_level)合併成一筆計算
                                        }?>
                                <td class="<?php echo ($catalogStock["amount"] < $catalogStock['standard_lv']) ? "alert_amount":"" ;?> " >
                                    <?php echo $catalogStock['amount'];?></td><?php $sum_0 += (int)$catalogStock["amount"];?>
                                <td <?php if($catalogStock["lot_num"] < $half_month){ ?> class="alert_lot_num" data-toggle="tooltip" data-placement="bottom" title="有效期限小於：<?php echo $half_month;?>" <?php } ?>>
                                    <?php echo $catalogStock["lot_num"];?></td>
                                <td><?php echo $catalogStock["po_no"];?></td>
                                <td>
                                    <?php if($sys_role <= 1 ){ ?>
                                        <button type="button" id="edit_stock_btn" value="<?php echo $catalogStock["id"];?>" data-bs-toggle="modal" data-bs-target="#edit_stock" 
                                            class="btn btn-sm btn-xs btn-info" onclick="edit_module('stock', this.value)" >編輯</button>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php $check_item = $catalogStock["fab_title"]; $check_local0 = $catalogStock["fab_title"]."_".$catalogStock["local_title"] ;?>
                        <?php }?>
                        <tr style="font-weight:bold; text-align:right; background-color: #D3FF93;">
                            <td style="text-align: right;">Sum:</td>
                            <td><?php echo $sum_s;?></td>
                            <td><?php echo $sum_0;?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

<!-- 模組 圖片瀏覽-->
    <div class="modal fade" id="about-1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">preView 預覽：<?php echo $catalog["PIC"]; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5 bg-light" style="text-align:center;" >
                    <img src="images/<?php echo $catalog["PIC"]; ?>" style="height: 100%;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<!-- 模組 器材提撥、編輯stock品項 -->
    <div class="modal fade" id="edit_stock" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="modal_action"></span>&nbsp儲存品</h4><span id="modal_sup"></span>
                    &nbsp&nbsp&nbsp&nbsp&nbsp
                    <form action="" method="post">
                        <input type="hidden" name="id" id="stock_delete_id">
                        <span id="modal_delect_btn" class="<?php echo ($sys_role == 0) ? "":" unblock ";?>"></span>
                    </form>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="" method="post" onsubmit="this.standard_lv.disabled=false">
                    <div class="modal-body p-4 pb-0">
                        <!-- 第一列 儲存區/器材名稱 -->
                        <div class="col-12 rounded py-1" style="background-color: #D3FF93;">
                            <div class="row" >
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <select name="local_id" id="edit_local_id" class="form-control" required onchange="select_local(this.value)">
                                            <option value="" selected hidden>-- 請選擇儲存點 --</option>
                                            <?php foreach($locals as $local){ ?>
                                                <option value="<?php echo $local["id"];?>">
                                                    <?php echo $local["id"]."：".$local["site_title"]."&nbsp".$local["fab_title"]."_".$local["local_title"]; echo ($local["flag"] == "Off") ? " - (已關閉)":"";?></option>
                                            <?php } ?>
                                        </select>
                                        <label for="edit_local_id" class="form-label">local_id/儲存位置：<sup class="text-danger">*</sup></label>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <input type="hidden" name="cata_SN" id="edit_cata_SN" value="<?php echo $catalog["SN"];?>">
                                        <input type="text" id="edit_cata" class="form-control" value="<?php echo $catalog["SN"]."：".$catalog["pname"];?>" placeholder="器材名稱" readonly>
                                        <label for="edit_cata" class="form-label">cata_SN/器材名稱：<sup class="text-danger"> - disabled</sup></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <!-- 第二排數據 -->
                        <div class="col-12 rounded bg-light">
                            <div class="row">
                                <!-- 左側-數量 -->
                                <div class="col-12 col-md-6 py-0">
                                    <div class="form-floating">
                                        <input type="number" name="standard_lv" id="edit_standard_lv" class="form-control t-center" placeholder="標準數量(管理員填)" min="1" max="400"
                                            <?php echo $sys_role >= 2 ? "readonly":""; ?> >
                                        <label for="edit_standard_lv" class="form-label">standard_lv/安全存量：<sup class="text-danger"><?php echo ($sys_role >= 2) ? " - disabled":" *";?></sup></label>
                                    </div>
                                </div>
                                <!-- 右側-批號 -->
                                <div class="col-12 col-md-6 py-0">
                                    <div class="form-floating">
                                        <input type="number" name="amount" id="edit_amount" class="form-control t-center" required placeholder="正常數量" min="0" max="999">
                                        <label for="edit_amount" class="form-label">amount/現場存量：<sup class="text-danger">*</sup></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <input type="text" name="po_no" id="edit_po_no" class="form-control" placeholder="PO採購編號">
                                        <label for="edit_po_no" class="form-label">po_no/PO採購編號：</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <input type="text" name="pno" id="edit_pno" class="form-control" placeholder="料號">
                                        <label for="edit_pno" class="form-label">pno/料號：</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <textarea name="stock_remark" id="edit_stock_remark" class="form-control" style="height: 100px"></textarea>
                                        <label for="edit_stock_remark" class="form-label">stock_remark/備註說明：</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating pb-0">
                                        <input type="date" name="lot_num" id="edit_lot_num" class="form-control" required>
                                        <label for="edit_lot_num" class="form-label">lot_num/批號/期限：<sup class="text-danger">*</sup></label>
                                    </div>
                                    <div class="col-12 pt-0 text-end">
                                        <button type="button" id="edit_toggle_btn" class="btn btn-sm btn-xs btn-warning text-dark" onclick="chenge_lot_num('edit')">永久</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 第三排提示 -->
                        <div class="col-12 rounded bg-light pt-0">
                            *.注意：相同 儲存位置、器材、採購編號、批號期限 將合併計算!
                        </div>
                        <div class="col-12 text-end p-0" id="edit_stock_info"></div>
                    </div>

                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="id" id="stock_edit_id" >
                            <input type="hidden" name="sn" value="<?php echo isset($_REQUEST['sn']) ? $_REQUEST['sn'] : '' ;?>">
                            <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                            <span id="modal_button" class="<?php echo ($sys_role <= 1) ? "":" unblock ";?>"></span>
                            <input type="reset" class="btn btn-info" id="reset_btn" value="清除">
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
    
    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
</body>

<script src="../../libs/aos/aos.js"></script>
<script src="../../libs/aos/aos_init.js"></script>
<script src="../../libs/sweetalert/sweetalert.min.js"></script>
<script src="../../libs/openUrl/openUrl.js"></script>       <!-- 彈出子畫面 -->

<script>
// // // 開局導入設定檔
    var locals      = <?=json_encode($locals)?>;                            // 引入所有local的locals值
    var low_level   = [];                                                   // 宣告low_level變數
    var stock       = <?=json_encode($catalogStocks)?>;                     // 引入catalogStocks資料
    var stock_item  = ['id','local_id','cata_SN','standard_lv','amount','po_no','pno','stock_remark','lot_num'];    // 交給其他功能帶入 delete_supp_id
    var swal_json   = <?=json_encode($swal_json)?>;                         // 引入swal_json值

    // swl function    
    if(swal_json.length != 0){
        swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:1000});
    }

// // // utility fun
    // 顯示更多 more_info
    var checkEye = document.getElementById("checkEye");
    var more_info = document.querySelectorAll(".more_info");
    function change_checkEye(){
        if(checkEye.classList == "fa-sharp fa-solid fa-lg fa-caret-up"){
            checkEye.classList.remove("fa-caret-up");
            checkEye.classList.add("fa-caret-down");
        }else{
            checkEye.classList.remove("fa-caret-down");
            checkEye.classList.add("fa-caret-up");
        }

        Object(more_info).forEach(function(row){          
            if (row.classList == "more_info unblock") {
                row.classList.remove("unblock");
                row.classList.add("active");
            } else {
                row.classList.remove("active");
                row.classList.add("unblock");
            }
        })
    }
    checkEye.addEventListener('click',function(){
        change_checkEye();
    })
    // fun3-3：吐司顯示字條 // init toast
    function inside_toast(sinn){
        var toastLiveExample = document.getElementById('liveToast');
        var toast = new bootstrap.Toast(toastLiveExample);
        var toast_body = document.getElementById('toast-body');
        toast_body.innerHTML = sinn;
        toast.show();
    }
    // 主技能：選擇local時，取得該local的low_level
    function select_local(local_id){
        Object(locals).forEach(function(aLocal){
            if(aLocal['id'] == local_id){
                low_level = JSON.parse(aLocal['low_level']);            // 引入所選local的low_level值
            }
        })
        // 預防已經先選了器材，進行防錯
        var select_cata_SN = document.getElementById('edit_cata_SN').value;  // 取得器材的選項值
        if(select_cata_SN != null){                                     // 假如器材已經選擇了
            update_standard_lv(select_cata_SN);                         // 就執行取得low_level對應值
        }
    }
    // 子技能：選擇器材，並更新low_level值
    function update_standard_lv(catalog_SN){
        var standard_lv = document.getElementById('edit_standard_lv');       // 定義standard_lv主體
        if(low_level[catalog_SN] == null){
            standard_lv.value = 0;                                      // 預防low_level對應值是null
        }else{
            standard_lv.value = low_level[catalog_SN];                  // 套用對應cata_SN的low_level值
        }
    }
    // 變更lot_num數值
    function chenge_lot_num(target){
        var lot_num = document.getElementById(target+'_lot_num');
        if(lot_num.value =='') {
            lot_num.value = '9999-12-31';
        }else{
            lot_num.value = '';
        }
        change_btn(target);
    };
    // 變更按鈕樣態
    function change_btn(target){
        var toggle_btn = document.getElementById(target+'_toggle_btn');
        var lot_num = document.getElementById(target+'_lot_num');

        if (lot_num.value == '') {
            // 输入字段为空
            toggle_btn.innerText = '永久';
            toggle_btn.classList.remove('btn-secondary');
            toggle_btn.classList.add('btn-warning', 'text-dark');
        } else {
            // 输入字段有值
            toggle_btn.innerText = '清除';
            toggle_btn.classList.remove('btn-warning', 'text-dark');
            toggle_btn.classList.add('btn-secondary');
        }
    }

    $(function(){
        // 在任何地方啟用工具提示框
        $('[data-toggle="tooltip"]').tooltip();

        // 20230131 新增保存日期為'永久'    20230714 升級合併'永久'、'清除'
        // 監聽lot_num是否有輸入值，跟著改變樣態
        $('#add_lot_num').on('input', function() {
            change_btn('add');
        });
        $('#edit_lot_num').on('input', function() {
            change_btn('edit');
        });

    });

// // // add mode function
    function add_module(to_module){     // 啟用新增模式
        $('#modal_action, #modal_button, #modal_delect_btn, #edit_stock_info, #modal_sup').empty();     // 清除model功能
        $('#reset_btn').click();                                                                        // reset清除表單
        var add_btn = '<input type="submit" name="add_stock_submit" class="btn btn-primary" value="新增">';
        $('#modal_action').append('新增');                                                              // model標題
        $('#modal_sup').append('：<sup class="text-danger"> ~ 此撥補功能不扣任何存量，請謹慎使用!!!</sup>'); // model標題
        $('#modal_button').append(add_btn);                                                             // 儲存鈕
        var reset_btn = document.getElementById('reset_btn');                                           // 指定清除按鈕
        reset_btn.classList.remove('unblock');                                                          // 新增模式 = 解除
        document.querySelector("#edit_stock .modal-header").classList.remove('edit_mode_bgc');
        document.querySelector("#edit_stock .modal-header").classList.add('add_mode_bgc');
    }
// // // edit mode function
    // fun-1.鋪編輯畫面
    function edit_module(to_module, row_id){
        $('#modal_action, #modal_button, #modal_delect_btn, #edit_stock_info, #modal_sup').empty();     // 清除model功能
        $('#reset_btn').click();                                                                        // reset清除表單
        var reset_btn = document.getElementById('reset_btn');                                           // 指定清除按鈕
        reset_btn.classList.add('unblock');                                                             // 編輯模式 = 隱藏
        document.querySelector("#edit_stock .modal-header").classList.remove('add_mode_bgc');
        document.querySelector("#edit_stock .modal-header").classList.add('edit_mode_bgc');
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
                    }else{
                        document.querySelector('#edit_'+to_module+' #edit_'+item_key).value = row[item_key]; 
                    }
                })

                // 鋪上最後更新
                let to_module_info = '最後更新：'+row['updated_at']+' / by '+row['updated_user'];
                document.querySelector('#edit_'+to_module+'_info').innerHTML = to_module_info;

                var add_btn = '<input type="submit" name="edit_stock_submit" class="btn btn-primary" value="儲存">';
                var del_btn = '<input type="submit" name="delete_stock" value="刪除stock儲存品" class="btn btn-sm btn-xs btn-danger" onclick="return confirm(`確認刪除？`)">';
                $('#modal_action').append('編輯');                                                  // model標題
                $('#modal_delect_btn').append(del_btn);                                             // 刪除鈕
                $('#modal_button').append(add_btn);                                                 // 儲存鈕
                return;
            }
        })
    }

    // 20240529 確認自己是否為彈出視窗 !! 只在完整url中可運行 = tw123456p.cminl.oa
    function checkPopup() {
        var urlParams = new URLSearchParams(window.location.search);
        if ((urlParams.has('popup') && urlParams.get('popup') === 'true') || (window.opener) || (sessionStorage.getItem('isPopup') === 'true')) {
            console.log('popup');
            sessionStorage.removeItem('isPopup');

            let nav = document.querySelector('nav');                // 獲取 <nav> 元素
                nav.classList.add('unblock');                           // 添加 'unblock' class

            let rtn_btns = document.querySelectorAll('.rtn_btn');   // 獲取所有帶有 'rtn_btn' class 的按鈕
                rtn_btns.forEach(function(btn) {                        // 遍歷這些按鈕，並設置 onclick 事件
                    btn.onclick = function() {
                        closeWindow();                                  // true=更新 / false=不更新
                    };
                });
        }else{
            console.log('main');
        }
    }

    $(document).ready(function(){
        
        checkPopup();
        
    })

</script>

<?php include("../template/footer.php"); ?>