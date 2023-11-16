<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

// add module function --
    $swal_json = array();
    // 新增add
    if(isset($_POST["submit"])){
        $swal_json = store_stock($_REQUEST);
    }
    // 編輯Edit
    if(isset($_POST["edit_stock_submit"])){
        $swal_json = update_stock($_REQUEST);
    }
    // 刪除delete
    if(isset($_POST["delete_stock"])){
        $swal_json = delete_stock($_REQUEST);
    }

    if(isset($_REQUEST["sn"])){ 
        $sort_sn = $_REQUEST["sn"];                     // 有帶查詢
    }else{
        $sort_sn = NULL;
    }

    $catalog = edit_catalog($_REQUEST);                 // 上半部 器材資訊

    if(empty($catalog)){                                // 查無資料時返回指定頁面
        echo "<script>history.back()</script>";         // 用script導回上一頁。防止崩煃
    }

    $catalogStocks = show_catalogStock($_REQUEST);      // 下半部 庫存資訊=各廠庫存量
    $locals = show_allLocal();                          // for器材撥補

    // 初始化半年後日期，讓系統判斷與highLight
    $toDay = date('Y-m-d');
    $half_month = date('Y-m-d', strtotime($toDay."+6 month -1 day"));

?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
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
        /*眼睛*/
        #checkEye {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
        }
        /* 凸顯可編輯欄位 */
            .fix_amount:hover {
                /* font-size: 1.05rem; */
                font-weight: bold;
                text-shadow: 3px 3px 5px rgba(0,0,0,.5);
            }
        /* 新增與編輯 module表頭顏色 */
            .add_mode_bgc {          
                background-color: #ADD8E6;
            }
            .edit_mode_bgc {
                background-color: #FFFACD;
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

<div class="col-12">
    <div class="row justify-content-center">
        <!-- 上半部 器材資訊 -->
        <div class="col-10 bg-light border p-4 rounded my-2">
            <div class="row">
                <div class="col-12 col-md-6 py-0">
                    <h4>器材資訊</h4>
                </div>
                <div class="col-12 col-md-6 py-0 text-end">
                    <?php if($_SESSION[$sys_id]["role"] <= 1 ){ ?>
                        <a href="#" target="_blank" title="管理員限定" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#add_stock"><i class="fa fa-upload" aria-hidden="true"></i> 單品撥補</a>
                    <?php } ?>
                    <a class="btn btn-success" onclick="history.back()"><i class="fa fa-external-link" aria-hidden="true"></i> 返回目錄</a>
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

                <div style="font-size: 6px;" class="text-end">
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
                    <a class="btn btn-success" onclick="history.back()"><i class="fa fa-external-link" aria-hidden="true"></i> 返回目錄</a>
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
                                <?php if($_SESSION[$sys_id]["role"] <= 1 ){ ?>
                                    <!-- <a href="../stock/edit.php?id=<php echo $catalogStock['id'];?>&from2=repo" class="btn btn-sm btn-xs btn-info">編輯</a> -->
                                    <button type="button" id="edit_stock_btn" value="<?php echo $catalogStock["id"];?>" data-bs-toggle="modal" data-bs-target="#edit_stock" 
                                        class="btn btn-sm btn-xs bg-info" onclick="edit_module('stock', this.value)" >編輯</button>
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

<!-- 彈出畫面說明模組 圖片瀏覽-->
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

<!-- 彈出畫面說明模組 器材提撥-->
    <div class="modal fade" id="add_stock" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">器材撥補：<?php echo $catalog["SN"]."_".$catalog["pname"];?></h4><sup class="text-danger"> ~ 此撥補功能不扣任何存量，請謹慎使用!!!</sup>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
    
                <form action="" method="post" onsubmit="this.standard_lv.disabled=false">
                    <div class="modal-body p-4 pb-0">
                        <!-- 第一列 儲存區/器材名稱 -->
                        <div class="col-12 rounded py-1" style="background-color: #D3FF93;">
                            <div class="row">
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <select name="local_id" id="local_id" class="form-control" required onchange="select_local(this.value)">
                                            <option value="" selected hidden>-- 請選擇儲存點 --</option>
                                            <?php foreach($locals as $local){ ?>
                                            <option value="<?php echo $local["id"];?>" >
                                                <?php echo $local["id"]."：".$local["site_title"]."&nbsp".$local["fab_title"]."_".$local["local_title"]; echo ($local["flag"] == "Off") ? " - (已關閉)":"";?></option>
                                            <?php } ?>
                                        </select>
                                        <label for="local_id" class="form-label">local/儲存位置：<sup class="text-danger">*</sup></label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 py-1">
                                    <!-- <input type="hidden" name="cata_id" id="cata_id" value="<php echo $catalog["id"];?>"> -->
                                    <!-- <img src="images/<php echo $catalog["PIC"]; ?>" height="100px" class="img-thumbnail"> -->
                                    <div class="form-floating">
                                        <input type="hidden" name="cata_SN" id="add_cata_SN" value="<?php echo $catalog["SN"];?>">
                                        <input type="text" id="cata_SN" class="form-control" value="<?php echo $catalog["SN"]."：".$catalog["pname"];?>" placeholder="器材名稱" readonly>
                                        <label for="cata_SN" class="form-label">cata_SN/器材名稱：<sup class="text-danger"> - disabled</sup></label>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- 第二排數據 -->
                        <div class="col-12 rounded bg-light">
                            <div class="row">
                                <div class="col-12 col-md-6 py-0">
                                    <div class="form-floating">
                                        <input type="number" name="standard_lv" id="standard_lv" class="form-control t-center" placeholder="標準數量(管理員填)" min="1" max="9999"
                                            <?php echo $_SESSION[$sys_id]["role"] >= 2 ? "readonly":""; ?> >
                                        <label for="standard_lv" class="form-label">standard_lv/安全存量：<sup class="text-danger"><?php echo ($_SESSION[$sys_id]["role"] >= 2) ? " - disabled":" *";?></sup></label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 py-0">
                                    <div class="form-floating">
                                        <input type="number" name="amount" id="amount" class="form-control t-center" required placeholder="正常數量" min="0" max="999">
                                        <label for="amount" class="form-label">amount/現場存量：<sup class="text-danger">*</sup></label>
                                    </div>
                                </div>
    
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <input type="text" name="po_no" id="po_no" class="form-control" placeholder="PO採購編號">
                                        <label for="po_no" class="form-label">po_no/PO採購編號：</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <input type="text" name="pno" id="pno" class="form-control" placeholder="料號">
                                        <label for="pno" class="form-label">pno/料號：</label>
                                    </div>
                                </div>
    
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <textarea name="stock_remark" id="stock_remark" class="form-control" style="height: 100px"></textarea>
                                        <label for="stock_remark" class="form-label">stock_remark/備註說明：</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating pb-0">
                                        <input type="date" name="lot_num" id="add_lot_num" class="form-control" required>
                                        <label for="add_lot_num" class="form-label">lot_num/批號/期限：<sup class="text-danger">*</sup></label>
                                    </div>
                                    <div class="col-12 pt-0 text-end">
                                        <button type="button" id="add_toggle_btn" class="btn btn-sm btn-xs btn-warning text-dark" onclick="chenge_lot_num('add')">永久</button>
                                    </div>
                                </div>
    
    
                            </div>
                        </div>
                        <!-- 第三排提示 -->
                        <div class="col-12 rounded bg-light pt-0">
                            *.注意：相同 儲存位置、器材、採購編號、批號期限 將合併計算!
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                            <input type="submit" name="submit" class="btn btn-primary" value="儲存">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">取消</button>

                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>

<!-- 彈出畫面模組 編輯stock品項 -->
    <div class="modal fade" id="edit_stock" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">編輯儲存品</h4>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="stock_delete_id">
                        <?php if($_SESSION[$sys_id]["role"] == 0){ ?>
                            &nbsp&nbsp&nbsp&nbsp&nbsp
                            <input type="submit" name="delete_stock" value="刪除stock儲存品" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">
                        <?php } ?>
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
                                                <?php if($_SESSION[$sys_id]["role"] <= 1 || $local["fab_id"] == $_SESSION[$sys_id]["fab_id"] || (in_array($local["fab_id"], $_SESSION[$sys_id]["sfab_id"]))){ ?>  
                                                    <option value="<?php echo $local["id"];?>">
                                                        <?php echo $local["id"]."：".$local["site_title"]."&nbsp".$local["fab_title"]."_".$local["local_title"]; echo ($local["flag"] == "Off") ? " - (已關閉)":"";?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <label for="edit_local_id" class="form-label">local_id/儲存位置：<sup class="text-danger">*</sup></label>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 py-1">
                                    <div class="form-floating">
                                        <input type="hidden" name="cata_SN" id="edit_cata_SN" value="<?php echo $catalog["SN"];?>">
                                        <input type="text" id="edit_cata" class="form-control" value="<?php echo $catalog["SN"]."：".$catalog["pname"];?>" placeholder="器材名稱" readonly>
                                        <label for="edit_cata" class="form-label">cata_id/器材名稱：<sup class="text-danger"> - disabled</sup></label>
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
                                            <?php echo $_SESSION[$sys_id]["role"] >= 2 ? "readonly":""; ?> >
                                        <label for="edit_standard_lv" class="form-label">standard_lv/安全存量：<sup class="text-danger"><?php echo ($_SESSION[$sys_id]["role"] >= 2) ? " - disabled":" *";?></sup></label>
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
                        <!-- 最後編輯資訊 -->
                        <div class="col-12 text-end p-0" id="edit_stock_info"></div>
                    </div>

                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="id" id="stock_edit_id" >
                            <input type="hidden" name="fab_id" value="<?php echo $select_local["fab_id"];?>">
                            <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                            <input type="submit" name="edit_stock_submit" class="btn btn-primary" value="儲存" >
                            <input type="reset" value="清除" class="btn btn-info">
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

<!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>
<!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
<script src="../../libs/sweetalert/sweetalert.min.js"></script>

<script>
    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
// // // swl function    
    var swal_json = <?=json_encode($swal_json);?>;                      // 引入swal_json值
    if(swal_json.length != 0){
        swal(swal_json['fun'] ,swal_json['content'] ,swal_json['action'], {buttons: false, timer:1000});
    }

// // //<!-- 顯示更多 more_info -->
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

// // // add mode function
    var locals = <?=json_encode($locals);?>;                      // 引入所有local的locals值
    var low_level = [];                                                 // 宣告low_level變數

    // 主技能：選擇local時，取得該local的low_level
    function select_local(local_id){
        Object(locals).forEach(function(aLocal){
            if(aLocal['id'] == local_id){
                low_level = JSON.parse(aLocal['low_level']);            // 引入所選local的low_level值
                console.log(local_id,"_low_level:",low_level);
            }
        })
        // 預防已經先選了器材，進行防錯
        var select_cata_SN = document.getElementById('add_cata_SN').value;  // 取得器材的選項值
        console.log('select_cata_SN:',select_cata_SN);
        if(select_cata_SN != null){                                     // 假如器材已經選擇了
            update_standard_lv(select_cata_SN);                         // 就執行取得low_level對應值
        }
    }
    // 子技能：選擇器材，並更新low_level值
    function update_standard_lv(catalog_SN){
        var standard_lv = document.getElementById('standard_lv');       // 定義standard_lv主體
        if(low_level[catalog_SN] == null){
            standard_lv.value = 0;                                      // 預防low_level對應值是null
        }else{
            standard_lv.value = low_level[catalog_SN];                  // 套用對應cata_SN的low_level值
        }
    }

// // // edit mode function
    var stock = <?=json_encode($catalogStocks);?>;                        // 引入catalogStocks資料
    var stock_item = ['id','local_id','cata_SN','standard_lv','amount','po_no','pno','stock_remark','lot_num'];    // 交給其他功能帶入 delete_supp_id

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
                    }else{
                        document.querySelector('#edit_'+to_module+' #edit_'+item_key).value = row[item_key]; 
                    }
                })

                // 鋪上最後更新
                let to_module_info = '最後更新：'+row['updated_at']+' / by '+row['updated_user'];
                document.querySelector('#edit_'+to_module+'_info').innerHTML = to_module_info;

            }
        })
    }

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

    // 20230131 新增保存日期為'永久'    20230714 升級合併'永久'、'清除'
    $(function(){

        // 監聽lot_num是否有輸入值，跟著改變樣態
        $('#add_lot_num').on('input', function() {
            change_btn('add');
        });
        $('#edit_lot_num').on('input', function() {
            change_btn('edit');
        });

    });

</script>

<?php include("../template/footer.php"); ?>