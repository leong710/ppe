<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../catalog/function.php");
    require_once("function.php");
    accessDenied($sys_id);

    if(isset($_POST["update_submit"])){
        update_trade($_REQUEST);
        // header("refresh:0;url=index.php");
        // exit;
    }
    if(isset($_POST["delete"])){
        deleteTrade($_REQUEST);
        header("location:../trade/");       // 用這個，因為跳太快
        exit;
    }
    // 更新log
    if(isset($_POST["delete_log"])){
        updateLogs($_REQUEST);
    }
    // 帶入該筆紀錄內容
    $trade = showTrade($_REQUEST);
    if(empty($trade)){                                  // 查無資料時返回指定頁面
        echo "<script>history.back()</script>";         // 用script導回上一頁。防止崩煃
    }


    $check_OL_isPO = strtolower(substr($trade["out_local"],0,2));    // strtolower() = 英文字串轉小寫；substr($out_local,0,2) = 取自串段落
        // 是po才做進貨處理
        if($check_OL_isPO == "po"){        
            $check_OL_isPO = true;
        }else{
            $check_OL_isPO = false;
        }

    // 讀取所有catalog
        $sort_category = array(
            'cate_no' => "All"
        );
        $catalogs = show_catalogs($sort_category);
    // 因為陣列是從0開始，必須插入一空白陣列到前面，讓正確資料由1開始
        // $space =[];
        // array_unshift($catalogs, $space);   // 插到前面

    $myFab_id = $_SESSION[$sys_id]["fab_id"];
    $mySfab_id = $_SESSION[$sys_id]["sfab_id"];
        // 判定是否是我的入庫範圍
        if($trade['fab_i_id'] == $myFab_id || in_array($trade['fab_i_id'], $mySfab_id)){
            $myFab_i = true;
        }else{
            $myFab_i = false;
        }
        // 判定是否是我的出庫範圍
        if($trade['fab_o_id'] == $myFab_id || in_array($trade['fab_o_id'], $mySfab_id)){
            $myFab_o = true;
        }else{
            $myFab_o = false;
        }

    if(isset($_REQUEST['action']) && ($_REQUEST['action'] == "acceptance")){
        if(($trade['idty'] == '1') && $myFab_i){
            echo "<script>alert('此撥補單與您的廠區不相符，您無權驗收!!');</script>";
            header("refresh:0;url=index.php");
            exit;
        }else if(($trade['idty'] == '2') && $myFab_o){
            echo "<script>alert('此撥補單與您的廠區不相符，您無權驗退!!');</script>";
            header("refresh:0;url=index.php");
            exit;
        }
    }




?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <style>
        .fa-check {
            /* 將圖示的背景色設置為透明並添加陰影 */
            background-color: transparent; 
            /* text-shadow: 0px 0px 5px #fff; */
            /* box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); */
            /* color: #00ff00; */
            color: blue;
        }
    </style>
</head>
<!-- 針對item儲存狀況快照的內容進行反解處理 -->
<?php 
    $item_str = $trade["item"];                     // 把item整串(未解碼)存到$item_str
    $item_arr = explode("_," ,$item_str);           // 把字串轉成陣列進行後面的應用
    $stock_id_dec = json_decode($item_arr[0]);          // 解碼後存到$item_dec
    $po_no_dec = json_decode($item_arr[1]);          // 解碼後存到$item_dec = po編號
    $item_dec = json_decode($item_arr[2]);          // 解碼後存到$item_dec = 項目
    $amount_dec = json_decode($item_arr[3]);        // 解碼後存到$amount_dec = 數量
    $lot_num_dec = json_decode($item_arr[4]);        // 解碼後存到$lot_num_dec = 批號校期

    //PHP stdClass Object轉array 
    if(is_object($stock_id_dec)) { $stock_id_dec = (array)$stock_id_dec; } 
    if(is_object($po_no_dec)) { $po_no_dec = (array)$po_no_dec; } 
    if(is_object($item_dec)) { $item_dec = (array)$item_dec; } 
    if(is_object($amount_dec)) { $amount_dec = (array)$amount_dec; } 
    if(is_object($lot_num_dec)) { $lot_num_dec = (array)$lot_num_dec; } 

    // 20230627-針對品項錯位bug進行調整 -- 重新建構目錄清單，以SN作為object.key
        $obj_catalogs = [];
        foreach($catalogs as $cata){
            $obj_catalogs[$cata["SN"]] = [
                'id' => $cata["id"],
                'SN' => $cata["SN"],
                'pname' => $cata["pname"],
                'PIC' => $cata["PIC"],
                'cata_remark' => $cata["cata_remark"],
                'OBM' => $cata["OBM"],
                'model' => $cata["model"],
                'size' => $cata["size"],
                'unit' => $cata["unit"],
                'SPEC' => $cata["SPEC"],
                'part_no' => $cata["part_no"],
                'scomp_no' => $cata["scomp_no"],
                'buy_a' => $cata["buy_a"],
                'buy_b' => $cata["buy_b"],
                'flag' => $cata["flag"],
                'updated_user' => $cata["updated_user"],
                'cate_title' => $cata["cate_title"],
                'cate_no' => $cata["cate_no"],
                'cate_id' => $cata["cate_id"]
            ];
        }
?>
<!-- log紀錄鋪設前處理 -->
<?php
    $logs_dec = json_decode($trade["logs"]);
    $logs_arr = (array) $logs_dec;
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 border rounded p-4 my-2" style="background-color: #D4D4D4;">
            <!-- 表單表頭功能鍵 -->
            <div class="row px-2">
                <div class="col-12 col-md-6">
                    <div class=""  style="display:inline-block;">
                        <h3>檢視調撥單</h3>
                    </div> 
                    <div class=""  style="display:inline-block;">
                        <?php if($_SESSION[$sys_id]["role"] == 0){ ?>
                            <form action="" method="post">
                                <input type="hidden" name="id" value="<?php echo $trade["id"];?>">
                                <input type="submit" name="delete" value="刪除" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">
                            </form>
                        <?php } ?>
                    </div> 
                </div> 
                <div class="col-12 col-md-6 text-end">
                    <?php if($trade['idty'] == 1 && ($trade['fab_i_id'] == $myFab_id || $_SESSION[$sys_id]['role'] <= 1 )){?>  
                        <a href="#" target="_blank" title="入庫確認" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#doSign"><i class="fa-solid fa-gift"></i> 簽核調撥單</a>
                    <?php }?>
                    <!-- 交易狀態 0完成/1待收/2退貨/3取消 -->
                    <?php if($myFab_i && ($trade['idty'] === '1')){ ?> 
                        <a href="#" target="_blank" title="Submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveSubmit"><i class="fa fa-check" aria-hidden="true"></i> 驗收</a>
                    <?php }else if($myFab_o && ($trade['idty'] === '2')){ ?>
                        <a href="#" target="_blank" title="Submit" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#saveSubmit"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> 驗退</a>
                    <?php }else if($myFab_o && ($trade['idty'] === '1')){ ?>
                        <a href="#" target="_blank" title="Submit" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#saveSubmit"><i class="fa fa-times" aria-hidden="true"></i> 取消</a>
                    <?php }?>                    

                    <div class=""  style="display:inline-block;">
                        <button class="btn btn-info" onclick="history.back()"><i class="fa fa-external-link" aria-hidden="true"></i> 返回</button>
                    </div>
                </div> 
            </div>
            <!-- 文件表頭 -->
            <div class="col-xl-12 col-12 py-0">
                <div class="row">
                    <div class="col-md-6">
                        <div>
                            調撥單號：<?php echo $trade["id"];?></br>
                            出庫日期：<?php echo $trade["out_date"];?></br>
                            出庫人員：<?php echo $trade["cname_o"];?></br>
                            出庫廠區：
                                <?php if(!empty($trade["fab_o_title"])){ 
                                    echo $trade["fab_o_title"].'('.$trade['fab_o_remark'].')'.'_'.$trade["local_o_title"];
                                }else{
                                    echo ($trade["out_local"]);
                                }?>
                                <?php if($myFab_o){?>&nbsp<i class="fa fa-check" aria-hidden="true"></i><?php };?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            表單狀態：<?php echo $trade['idty'];?>
                                    <?php if($trade['idty'] == '10'){?>結案<?php ;}?>
                                    <?php if($trade['idty'] == '0'){?>完成<?php ;}?>
                                    <?php if($trade['idty'] == '1'){?>待收<?php ;}?>
                                    <?php if($trade['idty'] == '2'){?>退貨<?php ;}?>
                                    <?php if($trade['idty'] == '3'){?>取消<?php ;}?>
                            </br>入庫日期：<?php echo $trade["in_date"];?></br>
                            入庫人員：<?php echo $trade["cname_i"];?></br>
                            入庫廠區：
                                <?php if(!empty($trade["fab_i_title"])){ 
                                    echo $trade["fab_i_title"].'('.$trade['fab_i_remark'].')'.'_'.$trade["local_i_title"];
                                }else{
                                    echo ($trade["in_local"]);
                                }?>
                                <?php if($myFab_i){?>&nbsp<i class="fa fa-check" aria-hidden="true"></i><?php };?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 撥補清單table -->
            <div class="col-xl-12 col-12 rounded bg-light mb-3">
                <table class="for-table">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>分類</th>
                            <th>名稱</th>
                            <th>調撥數量</th>
                            <th>單位</th>
                            <th>批號/效期</th>
                            <th>PO編號</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 鋪設內容 -->
                        <?php foreach(array_keys($stock_id_dec) as $stockID){?>
                            <tr>
                                <td class="t-left"><?php echo $obj_catalogs[$item_dec[$stockID]]['SN'];?></td>
                                <td><?php echo $obj_catalogs[$item_dec[$stockID]]['cate_no'].".".$obj_catalogs[$item_dec[$stockID]]['cate_title'];?></td>
                                <td class="t-left"><?php echo $obj_catalogs[$item_dec[$stockID]]['pname'];?></td>
                                <td><?php echo $amount_dec[$stockID];?></td>
                                <td><?php echo $obj_catalogs[$item_dec[$stockID]]['unit'];?></td>
                                <td><?php echo $lot_num_dec[$stockID];?></td>
                                <td><?php echo $po_no_dec[$stockID];?></td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
            
            <!-- 尾段2：表單logs訊息 -->
            <div class="col-12 pt-0 rounded bg-light">
                <div class="row">
                    <div class="col-12 col-md-6">
                        表單Log記錄：
                    </div>
                    <div class="col-12 col-md-6">
                   
                    </div>
                </div>
                <table class="for-table logs table table-sm table-hover">
                    <thead>
                        <tr class="table-dark">
                            <th>id</th>
                            <th>Signer</th>
                            <th>Time Signed</th>
                            <th>Status</th>
                            <th>Comment</th>
                            <?php if($_SESSION[$sys_id]["role"] == "0"){ ?><th>action</th><?php } ?>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div style="font-size: 6px;" class="text-end">
                    衛材訊息text-end
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 彈出畫面模組 doSign-->
    <div class="modal fade" id="doSign" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <form action="" method="post" onsubmit="this.p_id.disabled=false; this.b_idty.disabled=false; this.p_in_user_id.disabled=false;">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">調撥驗收：</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4">
                        <?php if(($trade['idty'] == '1') || ($trade['idty'] == '2')) {?>

                            <div class="row">
                                <div class="col-12 py-0">
                                    <label for="p_idty" class="form-label"><b>idty/驗收結果：</b></label></br>
                                </div>
                                <div class="col-12 pt-1 text-center">
                                        <input type="radio" name="p_idty" value="0" id="p_idty_0" class="form-check-input" required <?php echo ($check_OL_isPO || $myFab_i) ? "":"hidden";?>>
                                            <label for="p_idty_0" class="form-check-label" <?php echo ($check_OL_isPO || $myFab_i) ? "":"hidden";?>>&nbsp驗收/入庫&nbsp&nbsp</label>
                
                                        <input type="radio" name="p_idty" value="2" id="p_idty_2" class="form-check-input" required <?php echo $myFab_i ? "":"hidden";?>>
                                            <label for="p_idty_2" class="form-check-label" <?php echo $myFab_i ? "":"hidden";?>>&nbsp退件/返貨&nbsp&nbsp</label>
                
                                        <input type="radio" name="p_idty" value="3" id="p_idty_3" class="form-check-input" required <?php echo $check_OL_isPO || $myFab_o ? "":"hidden"; ?>>
                                            <label for="p_idty_3" class="form-check-label" <?php echo $check_OL_isPO || $myFab_o ? "":"hidden";?>>&nbsp取消<?php echo $check_OL_isPO ? "PO":"調撥";?>單</label>
                                </div>
                                <div class="col-12">
                                    <label for="remark" class="form-check-label" ><b>remark/備註說明：</b></label>
                                    <textarea name="remark" id="remark" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                            
                        <?php } ?>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" value="<?php echo $trade["id"];?>" name="p_id">
                        <input type="hidden" value="<?php echo $_SESSION["AUTH"]["emp_id"];?>" name="p_in_user_id">
                        <input type="submit" value="Submit" name="update_submit" class="btn btn-primary">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>

            </form>    
        </div>
    </div>

    <div class="modal fade" id="doSign_old" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <form action="" method="post" onsubmit="this.p_id.disabled=false; this.b_idty.disabled=false; this.p_in_user_id.disabled=false;">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">調撥驗收：</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-5">
                        <label for="p_idty" class="form-label">idty/驗收結果：</label></br>

                        <input type="radio" name="p_idty" value="0" id="p_idty_0" class="form-check-input" required 
                            <?php echo (($trade['idty'] == '1') && ($trade['fab_i_id'] != $myFab_id)) ? "hidden":"";?>>
                        <label for="p_idty_0" class="form-check-label" 
                            <?php echo (($trade['idty'] == '1') && ($trade['fab_i_id'] != $myFab_id)) ? "hidden":"";?>>&nbsp驗收 / 入庫&nbsp&nbsp</label>

                        <input type="radio" name="p_idty" value="2" id="p_idty_2" class="form-check-input" required 
                            <?php echo ((($trade['idty'] == '1') || ($trade['idty'] == '2')) && ($trade['fab_o_id'] == $myFab_id)) ? "hidden":"";?>>
                        <label for="p_idty_2" class="form-check-label" 
                            <?php echo ((($trade['idty'] == '1') || ($trade['idty'] == '2')) && ($trade['fab_o_id'] == $myFab_id)) ? "hidden":"";?>>&nbsp退件 / 返貨&nbsp&nbsp</label>

                        <input type="radio" name="p_idty" value="3" id="p_idty_3" class="form-check-input"  required
                            <?php echo (($trade['idty'] == '1') && ($trade['fab_o_id'] != $myFab_id)) ? "hidden":""; echo (($trade['idty'] == '2') && ($trade['fab_o_id'] == $myFab_id)) ? "hidden":"";?>>
                        <label for="p_idty_3" class="form-check-label" 
                            <?php echo (($trade['idty'] == '1') && ($trade['fab_o_id'] != $myFab_id)) ? "hidden":""; echo (($trade['idty'] == '2') && ($trade['fab_o_id'] == $myFab_id)) ? "hidden":"";?>>&nbsp取消</label>
                        <br>
                        <br>
                        <label for="remark" class="form-check-label" >備註說明：</label>
                        <textarea name="remark" id="remark" class="form-control" rows="5"></textarea>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" value="<?php echo $trade["id"];?>" name="p_id">
                        <input type="hidden" value="<?php echo $_SESSION["AUTH"]["emp_id"];?>" name="p_in_user_id">
                        <input type="submit" value="Submit" name="update_submit" class="btn btn-primary">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>

            </form>    
        </div>
    </div>
<!-- goTop滾動畫面DIV 2/4-->
<div id="gotop">
    <i class="fas fa-angle-up fa-1x"></i>
</div>
<!-- 鋪設logs紀錄 -->
<script>    
    var json = JSON.parse('<?=json_encode($logs_arr)?>');
    var id = <?=$trade["id"]?>;
    var forTable = document.querySelector('.logs tbody');
    console.log(json);
    for (var i = 0, len = json.length; i < len; i++) {
        forTable.innerHTML += 
            '<tr>' +
                '<td>' + [i] + '</td>' +
                '<td>' + json[i].cname + '</td>' +
                '<td>' + json[i].datetime + '</td>' +
                '<td>' + json[i].action + '</td>' +
                '<td>' + json[i].remark + '</td>' +
                '<?php if($_SESSION[$sys_id]["role"] == "0"){ ?><td>' + '<form action="" method="post">'+
                    `<input type="hidden" name="log_id" value="` + [i] + `";>` +
                    `<input type="hidden" name="id" value="` + id + `";>` +
                    `<input type="submit" name="delete_log" value="刪除" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">` +
                '</form>'  + '</td><?php } ?>' +
            '</tr>';
    }
</script>

<!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>

<?php include("../template/footer.php"); ?>