<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    if(isset($_POST["delete"])){            // 刪除表單
        deleteIssue($_REQUEST);
        header("location:../issue/");       // 用這個，因為跳太快
        exit;
    }
    if(isset($_POST["delete_log"])){        // 刪除log
        updateLogs($_REQUEST);
    }

    if(isset($_POST["pr2fab_submit"])){    // 發貨 => 12
        update_pr2fab($_REQUEST);
        header("refresh:0;url=index.php");
        exit;
    }

    if(isset($_POST["getIssue_submit"])){    // 收貨 => 10
        update_getIssue($_REQUEST);
        header("refresh:0;url=index.php");
        exit;
    }
    
    $issue = showIssue($_REQUEST);          // 帶入該筆紀錄內容
    if(empty($issue)){                      // 查無資料時返回指定頁面
        header("location:../issue/");       // 用這個，因為跳太快
        exit;
    }
    
    $catalogs = show_catalogs();            // 讀取所有catalog
        // 20230627-針對品項錯位bug進行調整 -- 取消插入
        // $space = [];                        // 因為陣列是從0開始，必須插入一空白陣列到前面，讓正確資料由1開始
        // array_unshift($catalogs, $space);   // 插到前面

    $myfab_id = $_SESSION[$sys_id]['fab_id'];

?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
</head>
<!-- 針對item儲存狀況快照的內容進行反解處理 -->
<?php 
    $item_str = $issue["item"];                     // 把item整串(未解碼)存到$item_str
    $item_arr = explode("_," ,$item_str);           // 把字串轉成陣列進行後面的應用
    $item_dec = json_decode($item_arr[0]);          // 解碼後存到$item_dec
    $amount_dec = json_decode($item_arr[1]);        // 解碼後存到$amount_dec

    //PHP stdClass Object轉array 
    if(is_object($item_dec)) { $item_dec = (array)$item_dec; } 
    if(is_object($amount_dec)) { $amount_dec = (array)$amount_dec; } 
        
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
    $logs_dec = json_decode($issue["logs"]);
    $logs_arr = (array) $logs_dec;
?>

<div class="col-12">
    <div class="row justify-content-center">
        <div class="col-11 border rounded p-4" style="background-color: #D4D4D4;">
            <!-- 表單表頭功能鍵 -->
            <div class="row px-2">
                <div class="col-12 col-md-6">
                    <div class="" style="display:inline-block;">
                        <h3>檢視需求單</h3>
                    </div> 
                    <div class="" style="display:inline-block;">
                        <?php if($_SESSION[$sys_id]["role"] == 0){ ?>
                            <form action="" method="post">
                                <input type="hidden" name="id" value="<?php echo $issue["id"];?>">
                                <input type="submit" name="delete" value="刪除表單" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">
                            </form>
                        <?php } ?>
                    </div> 
                </div> 
                <div class="col-12 col-md-6 text-end">
                    <!-- 收發貨回歸Trad衛材撥補作業 -->
                    <?php if($issue['idty'] == 12 && ($issue['in_user_id'] == $_SESSION[$sys_id]['id'] || $_SESSION[$sys_id]['role'] <= 1 )){?>  
                        <!-- <a href="#" target="_blank" title="收貨確認" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#doSign"><i class="fa-solid fa-gift"></i> 收貨確認</a> -->
                    <?php }?>
                    <!-- 收發貨回歸Trad衛材撥補作業 -->
                    <?php if($_SESSION[$sys_id]['role'] <= 1 && $issue['idty'] == 11){?>  
                        <!-- <a href="#" target="_blank" title="發貨確認" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pr2site"><i class="fa-solid fa-truck-fast"></i> 發貨確認</a> -->
                    <?php }?>

                    <?php if( $issue['idty'] == 1 && ($_SESSION[$sys_id]['role'] <= 1 || $issue['in_user_id'] == $_SESSION[$sys_id]['id'])){ ?> 
                        <!-- 簽核：PM+管理員功能 -->
                        <a href="#" target="_blank" title="Submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#doSign"><i class="fa fa-check" aria-hidden="true"></i> 
                            <?php echo $issue['in_user_id'] == $_SESSION[$sys_id]['id'] ? "調整":"簽核";?></a>
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
                            需求單號：<?php echo $issue["id"];?></br>
                            開單日期：<?php echo $issue["create_date"];?></br>
                            開單人員：<?php echo $issue["cname_i"];?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            表單狀態：<?php echo $issue['idty'];?>
                                    <?php if($issue['idty'] == '0'){?><span class="badge rounded-pill bg-warning text-dark">待轉</span><?php ;}?>
                                    <?php if($issue['idty'] == '1'){?><span class="badge rounded-pill bg-danger">待簽</span><?php ;}?>
                                    <?php if($issue['idty'] == '2'){?>退件<?php ;}?>
                                    <?php if($issue['idty'] == '3'){?>取消<?php ;}?>
                                    <?php if($issue['idty'] == '10'){?>結案<?php ;}?>
                                    <?php if($issue['idty'] == '11'){?>轉PR<?php ;}?>
                                    <?php if($issue['idty'] == '12'){?><span class="badge rounded-pill bg-success">待收</span><?php ;}?>
                            </br>需求類別：<?php echo $issue['ppty'];?>
                                    <?php if($issue['ppty'] == '0'){?>臨時<?php ;}?>
                                    <?php if($issue['ppty'] == '1'){?>定期<?php ;}?>
                            </br>需求廠區：<?php echo $issue["site_i_title"].'&nbsp'. $issue["fab_i_title"].'('.$issue["fab_i_remark"].')'.'_'.$issue["local_i_title"];?>
                            
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
                            <th>單位</th>
                            <th>需求數量</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 鋪設內容 -->
                        <!-- 20230627-針對品項錯位bug進行調整 -- 以id作為object.key -->
                        <?php foreach($item_dec as $itemID){?>
                            <tr>
                                <td class="t-left"><?php echo $obj_catalogs[$itemID]['SN'];?></td>
                                <td><?php echo $obj_catalogs[$itemID]['cate_no'].".".$obj_catalogs[$itemID]['cate_title'];?></td>
                                <td class="t-left"><?php echo $obj_catalogs[$itemID]['pname'];?></td>
                                <td><?php echo $obj_catalogs[$itemID]['unit'];?></td>
                                <td><?php echo $amount_dec[$itemID];?></td>
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
<!-- 彈出畫面說明模組 doSign-->
    <div class="modal fade" id="doSign" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <form action="update.php" method="post" onsubmit="">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">衛材需求單簽核：</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-5">
                        <label for="p_idty" class="form-label">idty/核決：</label></br>
                        
                        <!-- 簽核：PM+管理員功能 -->
                        <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                            <?php if($issue['idty'] == 1){ ?>
                                <input type="radio" name="p_idty" value="0" id="p_idty_0" class="form-check-input" required>
                                <label for="p_idty_0" class="form-check-label">核准</label>&nbsp&nbsp

                                <input type="radio" name="p_idty" value="2" id="p_idty_2" class="form-check-input" required>
                                <label for="p_idty_2" class="form-check-label">駁回</label>&nbsp&nbsp
                            <?php }?>
                        <?php }?> 
                        <!-- 簽核：申請人功能 -->
                        <?php if($issue['idty'] == 1){
                            if($issue['in_user_id'] == $_SESSION[$sys_id]['id'] || $_SESSION[$sys_id]['role'] <= 1 ){ ?>
                                <input type="radio" name="p_idty" value="3" id="p_idty_3" class="form-check-input" required>
                                <label for="p_idty_3" class="form-check-label">作廢</label>
                        <?php } }?>
                        <!-- 驗收 -->
                        <?php if($issue['idty'] == 12 && ($issue['in_user_id'] == $_SESSION[$sys_id]['id'] || $_SESSION[$sys_id]['role'] <= 1 )){ ?>
                            <input type="radio" name="p_idty" value="10" id="p_idty_10" class="form-check-input" required checked>
                            <label for="p_idty_10" class="form-check-label">驗收</label>
                        <?php }?>                    
                        <br>
                        <br>
                        <div class="form-floating">
                            <textarea name="remark" id="remark" class="form-control" style="height: 100px"></textarea>
                            <label for="remark" class="form-check-label" >備註說明：<sup class="text-danger"> *</sup></label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="p_id" value="<?php echo $issue["id"];?>">
                        <input type="hidden" name="p_in_user_id" value="<?php echo $_SESSION[$sys_id]["id"];?>">
                        <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>

            </form>    
        </div>
    </div>
<!-- 彈出畫面說明模組 PM出貨給各廠-->
    <div class="modal fade" id="pr2site" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <form action="" method="post" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">PM發貨確認：</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-5">
                        <div class="form-floating">
                            <input type="text" name="remark" id="remark" class="form-control" required placeholder="請填入PO單號" maxlength="12">
                            <label for="remark" class="form-label">remark/請填入PO單號：<sup class="text-danger"> *</sup></label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="idty" value="12">
                        <input type="hidden" name="out_user_id" value="<?php echo $_SESSION[$sys_id]['id'];?>">
                        <input type="hidden" name="pr2site" value="<?php echo $issue['id'];?>">
                        <input type="submit" name="pr2site_submit" value="Submit" class="btn btn-primary">
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
    var id = <?=$issue["id"]?>;
    var forTable = document.querySelector('.logs tbody');
    console.log(json);
    for (var i = 0, len = json.length; i < len; i++) {
        forTable.innerHTML += 
            '<tr>' +
                '<td>' + [i] + '</td>' +
                '<td>' + json[i].cname + '</td>' +
                '<td>' + json[i].datetime + '</td>' +
                '<td>' + json[i].action + '</td>' +
                '<td style="word-break: break-all;">' + json[i].remark + '</td>' +
                '<?php if($_SESSION[$sys_id]["role"] == "0"){ ?><td>' + '<form action="" method="post">'+
                    `<input type="hidden" name="log_id" value="` + [i] + `";>` +
                    `<input type="hidden" name="id" value="` + id + `";>` +
                    `<input type="submit" name="delete_log" value="刪除Log" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">` +
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