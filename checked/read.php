<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    $checked_item = show_checked_item($_REQUEST);

    if(empty($checked_item)){                       // 查無資料時返回指定頁面
        echo "<script>history.back()</script>";     // 用script導回上一頁。防止崩煃
    }

    $num = count($checked_item);

    if(isset($_POST["delete"])){
        delete_checked_item($_REQUEST);
        header("refresh:0;url=".$_REQUEST["from2"].".php");
        exit;
    }
?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
</head>

<!-- container -->
<div class="col-12"> 
    <div class="row justify-content-center px-4">
        <div class="col-11 border rounded p-4 my-2" style="background-color: #D4D4D4;">
            <div class="row px-2">
                <div class="col-12 col-md-6">
                    <h3>檢視點檢紀錄</h3>
                </div> 
                <div class="col-12 col-md-6 text-end">
                    <div class=""  style="display:inline-block;">
                        <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                            <form action="" method="post">
                                <input type="hidden" name="id" value="<?php echo $checked_item["id"];?>">
                                <input type="submit" name="delete" value="刪除紀錄" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">
                            </form>
                        <?php } ?>
                    </div> 
                    <div class=""  style="display:inline-block;">
                        <a href="<?php echo $_REQUEST["from2"]?>.php" class="btn btn-info">回首頁</a>
                    </div>
                </div> 
            </div>
            <!-- 文件表頭 -->
            <div class="col-xl-12 col-12 py-0">
                <div class="row">
                    <div class="col-md-6">
                        <div>
                            點檢單號：<?php echo $checked_item["id"];?></br>
                            點檢日期：<?php echo $checked_item["created_at"];?></br>
                            點檢人員：<?php echo $checked_item["updated_user"];?>
                            <!-- // 這裡的updated_user指的是點檢表單儲存人 -->
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            fab廠區：<?php echo $checked_item["fab_title"]; echo $checked_item["fab_remark"] ? " (".$checked_item["fab_remark"].")":"";?></br>
                            點檢年度：<?php echo $checked_item["checked_year"];?></br>
                            上下年度：<?php echo $checked_item["half"];?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 歷史儲存狀況table -->
            <div class="col-xl-12 col-12 rounded bg-light mb-3">
                <table class="for-table">
                    <thead>
                        <tr>
                            <th>儲存點位置</th>
                            <th>分類</th>
                            <th>名稱</th>
                            <th>size</th>
                            <th>安全存量</th>
                            <th>現場存量</th>
                            <th>備註說明</th>
                            <th>批號/期限</th>
                            <th>PO no</th>
                            <th>更新日期</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 鋪設內容 -->
                    </tbody>
                </table>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <label for="checked_remark" class="form-check-label" >點檢備註說明：</label>
                        <textarea name="checked_remark" id="checked_remark" class="form-control" rows="5" readonly ><?php echo $checked_item["checked_remark"];?></textarea>
                    </div>
                </div>
            </div>
            <div class="col-12 text-end"  style="display:inline-block;">
                <a href="<?php echo $_REQUEST["from2"]?>.php" class="btn btn-info">回首頁</a>
            </div>
        </div>
    </div>
</div>
<!-- goTop滾動畫面DIV 2/4-->
<div id="gotop">
    <i class="fas fa-angle-up fa-2x"></i>
</div>

<!-- 針對stocks_log儲存狀況快照的內容進行反解處理 -->
<?php 
    $stocks_str = $checked_item["stocks_log"];      // 把stocks_log整串(未解碼)存到$stocks_str
    $stocks_arr = explode("_," ,$stocks_str);       // 把字串轉成陣列進行後面的應用
    $num_stocks = count($stocks_arr);               // 算出這一陣列的筆數
    $checked_table = [];                            // 定義空陣列備用
    for($i=0; $i<$num_stocks; $i++){                // 用迴圈繞出來做解碼
        $item_dec = JSON_decode($stocks_arr[$i]);   // 解碼後存到$item_dec
        $checked_table[$i] = $item_dec;             // 把$item_dec插到陣列後面
    }
    if(is_object($checked_table)) { $checked_table = (array)$checked_table; }       // Obj轉Array
?>

<!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>
<!-- 把所有存量快照json鋪上去 -->
<script>
    var json = <?=json_encode($checked_table);?>;
    var forTable = document.querySelector('.for-table tbody');
    for (var i = 0, len = json.length; i < len; i++) {
        forTable.innerHTML += 
            '<tr>' +
                '<td>' + json[i].fab_title + '_' + json[i].local_title + '</td>' +
                '<td>' + json[i].cate_no + '.' + json[i].cate_title + '</td>' +
                '<td style="text-align: left;">' + json[i].cata_SN + '</br>' + json[i].pname + '</td>' +
                '<td>' + json[i].size + '</td>' +
                '<td>' + json[i].standard_lv + '</td>' +
                '<td>' + json[i].amount + '</td>' +
                '<td style="text-align: left; word-break: break-all;">' + json[i].stock_remark + '</td>' +
                '<td style="font-size: 12px;">' + json[i].lot_num + '</td>' +
                '<td style="font-size: 12px;">' + json[i].po_no + '</td>' +
                '<td style="font-size: 12px;">' + json[i].updated_at + '</br>by：' + json[i].updated_user + '</td>' +
                // 這裡的updated_user指的是儲存物編輯人
            '</tr>';
    }
</script>
            
<?php include("../template/footer.php"); ?>