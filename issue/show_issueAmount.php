<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    if(isset($_POST["issue2pr_submit"])){    // 轉PR => 11
        update_issue2pr($_REQUEST);
        echo "<script>alert('PR開單已完成!')</script>";
        header("refresh:0;url=index.php");
        exit;
    }

    // 帶入該筆紀錄內容
    $w_ppty = "All";                    // 全部
    $issues = show_issueAmount($w_ppty);
    // 讀取所有catalog
    $catalogs = show_catalogs();

    // 20230724-針對品項錯位bug進行調整 -- 重新建構目錄清單，以SN作為object.key
        $obj_catalogs = [];
        foreach($catalogs as $cata){
            $obj_catalogs[$cata["SN"]] = [
                'id'            => $cata["id"],
                'SN'            => $cata["SN"],
                'pname'         => $cata["pname"],
                'PIC'           => $cata["PIC"],
                'cata_remark'   => $cata["cata_remark"],
                'OBM'           => $cata["OBM"],
                'model'         => $cata["model"],
                'size'          => $cata["size"],
                'unit'          => $cata["unit"],
                'SPEC'          => $cata["SPEC"],
                'part_no'       => $cata["part_no"],
                'scomp_no'      => $cata["scomp_no"],
                'buy_a'         => $cata["buy_a"],
                'buy_b'         => $cata["buy_b"],
                'flag'          => $cata["flag"],
                'updated_user'  => $cata["updated_user"],
                'cate_title'    => $cata["cate_title"],
                'cate_no'       => $cata["cate_no"],
                'cate_id'       => $cata["cate_id"]
            ];
        }
        
    $all_item = [];  $all_amount = []; $issue_fab = []; $issue_SN_list = [];

?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <style>
        .unblock{
            display: none;
        }
        /* 新增與編輯 module表頭顏色 */
        .add_mode_bgc {          
            background-color: #ADD8E6;
        }
        .edit_mode_bgc {
            background-color: #FFFACD;
        }
    </style>
</head>
<!-- 針對item儲存狀況快照的內容進行反解處理 -->
<?php foreach($issues as $is){
    $item_str = $is["item"];                     // 把item整串(未解碼)存到$item_str
    $item_dec = json_decode($item_str);          // 解碼後存到$item_dec
    //PHP stdClass Object轉array 
    if(is_object($item_dec)) { $item_dec = (array)$item_dec; } 
    $item_key = array_keys((array)$item_dec);   // 取得診列key
    foreach($item_key as $it){
        $all_item[$it] = $it;
        if(empty($all_amount[$it])){
            $all_amount[$it] = $item_dec[$it];
        }else{
            $all_amount[$it] = $all_amount[$it] + $item_dec[$it];
        }
        if(empty($issue_fab[$it])){
            $issue_fab[$it] = ($is['fab_i_title'].': '.$item_dec[$it]);
        }else{
            $issue_fab[$it] = $issue_fab[$it].'</br>'.$is['fab_i_title'].': '.$item_dec[$it];
        }
        array_push($issue_SN_list, $is['id']);
    }

}?>

<div class="container">
    <div class="row justify-content-center">
        <!-- <div class="col-12 border rounded p-4 my-2" style="background-color: #D4D4D4;"> -->
        <div class="col-12 rounded p-4 my-2" style="background-color: rgba(200, 255, 255, .6);">
            <!-- 表單表頭功能鍵 -->
            <div class="row px-2">
                <div class="col-12 col-md-4">
                    <div class="">
                        <h3><b>請購需求單待轉PR</b></h3>
                    </div> 
                </div> 
                <div class="col-12 col-md-8 text-end">
                    <div class="">
                        <?php if($_SESSION[$sys_id]["role"] <=1 && count($issues) > 0){ ?>
                            <a href="#" target="_blank" title="PR開單確認" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#issue2pr"> <i class="fa fa-edit" aria-hidden="true"></i> PR開單確認</a>
                            <!-- 20231128 下載Excel -->
                            <form id="myForm" method="post" action="../_Format/download_excel.php" style="display:inline-block;">
                                <input type="hidden" name="htmlTable" id="htmlTable" value="">
                                <button type="submit" name="submit" class="btn btn-success" value="issueAmount" onclick="submitDownloadExcel(this.value)" >
                                    <i class="fa fa-download" aria-hidden="true"></i> 匯出&nbspExcel</button>
                            </form>
                        <?php } ?>
                        <a href="docsv.php?action=export&ppty=All" title="匯出CSV" class="btn btn-success"> <i class="fa fa-download" aria-hidden="true"></i> CSV</a>
                        <a href="../issue/" title="返回" class="btn btn-secondary"><i class="fa fa-external-link" aria-hidden="true"></i> 返回</a>
                    </div>
                </div> 
            </div>
            <div>
                <!-- 需求清單table -->
                <div class="col-xl-12 col-12 rounded bg-light mb-3">
                    <div class="row">
                        <div class="col-12 col-md-6 pb-0">
                            請購需求單數量：<?php echo count($issues)."件";?>
                        </div>
                        <div class="col-12 col-md-6 pb-0">
                       
                        </div>
                    </div>
                    <hr>
                    <div class="px-3">
                        <table class="for-table" id="issueAmount_table">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>分類</th>
                                    <th>品名</th>
                                    <th>型號</th>
                                    <th>尺寸</th>
                                    <th>數量</th>
                                    <th>單位</th>
                                    <th>細項</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- 鋪設內容 -->
                                <?php foreach($all_item as $it){?>
                                    <tr>
                                        <td class="t-left"><?php echo $obj_catalogs[$it]['SN'];?></td>
                                        <td><?php echo $obj_catalogs[$it]['cate_no'].".".$obj_catalogs[$it]['cate_title'];?></td>
                                        <td class="t-left"><?php echo $obj_catalogs[$it]['pname'];?></td>
                                        <td><?php echo $obj_catalogs[$it]['model'];?></td>
                                        <td><?php echo $obj_catalogs[$it]['size'];?></td>
                                        <td><?php echo $all_amount[$it];?></td>
                                        <td><?php echo $obj_catalogs[$it]['unit'];?></td>
                                        <td class="t-left"><?php echo $issue_fab[$it];?></td>
                                    </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div style="font-size: 6px;" class="text-end">
                        器材訊息text-end
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <!-- 尾段：debug訊息 -->
        <?php if(isset($_REQUEST["debug"])){
            include("debug_board.php"); 
        } ?>
    </div>
</div>
<!-- 彈出畫面說明模組 PR開單確認-->
    <div class="modal fade" id="issue2pr" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <form action="" method="post" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">PR開單確認：</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-5">
                        <div class="form-floating">
                            <input type="text" name="remark" id="remark" class="form-control" required placeholder="請填入PR單號" maxlength="12">
                            <label for="remark" class="form-label">remark/請填入PR單號：<sup class="text-danger"> *</sup></label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="idty" value="11">
                        <input type="hidden" name="step" value="PR開單">
                        <input type="hidden" name="issue2pr" value="<?php echo implode(',',$issue_SN_list);?>">
                        <input type="submit" name="issue2pr_submit" value="Submit" class="btn btn-primary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>    
        </div>
    </div>
<!-- goTop滾動畫面DIV 2/4-->
<div id="gotop">
    <i class="fas fa-angle-up fa-1x"></i>
</div>
<!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>
<script>

    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })

    // 20231128_下載Excel
    function submitDownloadExcel() {
        // 定義畫面上Table範圍
        var ia_table = document.getElementById("issueAmount_table");
        var rows = ia_table.getElementsByTagName("tr");
        var rowData = [];
        // 获取表格的标题行数据
        var headerRow = ia_table.getElementsByTagName("thead")[0].getElementsByTagName("tr")[0];
        var headerCells = headerRow.getElementsByTagName("th");
        // 逐列導出
        for (var i = 1; i < rows.length; i++) {
            var cells = rows[i].getElementsByTagName("td");
            rowData[i-1] = {};
            // 逐欄導出：thead-th = tbod-td
            for (var j = 0; j < cells.length; j++) {
                rowData[i-1][headerCells[j].innerHTML] = cells[j].innerHTML.replace(/<br\s*\/?>/gi, "\r\n");
            }
        }
        var htmlTableValue = JSON.stringify(rowData);
        document.getElementById('htmlTable').value = htmlTableValue;
    }
</script>

<?php include("../template/footer.php"); ?>