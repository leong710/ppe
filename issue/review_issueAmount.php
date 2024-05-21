<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../user_info.php");
    require_once("function.php");
    accessDenied($sys_id);

    if(isset($_POST["pr2fab_submit"])){         // 發貨 => 12
        update_pr2fab($_REQUEST);
        header("refresh:0;url=index.php");
        exit;
    }

    if(isset($_GET["pr_no"])){                  // 抓取參數
        $pr_no = $_GET["pr_no"];
        $issues = review_issueAmount($pr_no);   // 查詢PR
    }else{
        header("refresh:0;url=index.php");
        exit;
    }

    if(empty($issues)){                                 // 查無資料時返回指定頁面
        echo "<script>history.back()</script>";         // 用script導回上一頁。防止崩煃
    }

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

    // 針對item儲存狀況快照的內容進行反解處理
    foreach($issues as $is){
        $item_str = $is["item"];                                    // 把item整串(未解碼)存到$item_str
        $item_dec = json_decode($item_str);                         // 解碼後存到$item_dec
        if(is_object($item_dec)) { $item_dec = (array)$item_dec; }  // PHP stdClass Object轉array 
        // $item_key = array_keys((array)$item_dec);                   // 取得診列key
        $item_key = array_keys($item_dec);                          // 取得診列key
    
        foreach($item_key as $ikey){
            $all_item[$ikey] = $ikey;
            $item_dec_amount = (array) $item_dec[$ikey];
    
            if(empty($all_amount[$ikey])){
                $all_amount[$ikey] = $item_dec_amount["need"];
            }else{
                $all_amount[$ikey] = $all_amount[$ikey] + $item_dec_amount["need"];
            }
            if(empty($issue_fab[$ikey])){
                $issue_fab[$ikey] = $is['fab_i_title'].': '.$item_dec_amount["need"];
            }else{
                $issue_fab[$ikey] = $issue_fab[$ikey].'</br>'.$is['fab_i_title'].': '.$item_dec_amount["need"];
            }
            array_push($issue_SN_list, $is['id']);
        }
    }

?>
<?php include("../template/header.php"); ?>
<!-- <php include("../template/nav.php"); ?> -->

<head>
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <script src="../../libs/jquery/mloading_init.js"></script>

</head>

<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-12 rounded p-4" style="background-color: rgba(200, 255, 255, .6);">
                <!-- 表單表頭功能鍵 -->
                <div class="row px-2">
                    <div class="col-12 col-md-6">
                        <h3><b>請購需求單已開PR：</b><?php echo $pr_no;?></h3>
                    </div> 

                    <div class="col-12 col-md-6 text-end">
                        <div class="">
                            <?php if($sys_role <= 1  ){
                                // <a href="../trade/restock.php?pr_no=<?php echo $pr_no;>" target="_blank" title="發貨確認" class="btn btn-primary" ><i class="fa-solid fa-arrow-right-to-bracket"></i>&nbspPR請購進貨</a>
                                echo "<button type='button' value='../trade/restock.php?pr_no={$pr_no}' onclick='closeWindow(); openUrl(this.value)' title='發貨確認' class='btn btn-primary' ><i class='fa-solid fa-arrow-right-to-bracket'></i>&nbspPR請購進貨</button>";
                                if(count($issues) > 0) { ?> 
                                    <!-- 20231128 下載Excel -->
                                    <form id="myForm" method="post" action="../_Format/download_excel.php" style="display:inline-block;">
                                        <input type="hidden" name="htmlTable"   id="htmlTable"  value="">
                                        <input type="hidden" name="pr_no"       value="<?php echo $pr_no;?>">
                                        <button type="submit" name="submit" class="btn btn-success" value="issueAmount_PR" onclick="submitDownloadExcel(this.value)" >
                                            <i class="fa fa-upload" aria-hidden="true"></i> 匯出&nbspExcel</button>
                                    </form>
                                <?php } ?>
                            <?php } ?>
                            <!-- <a href="../issue/" title="返回" class="btn btn-secondary"><i class="fa fa-external-link" aria-hidden="true"></i> 返回</a> -->
                            <button type="button" class="btn btn-secondary" onclick="closeWindow()"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回首頁</button>
                        </div>
                    </div> 
                </div>
                <div id="table">
                    <!-- 需求清單table -->
                    <div id="table" class="col-xl-12 col-12 rounded bg-light mb-3">
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
                                        <th>Part_NO</th>
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
                                    <?php foreach($all_item as $it){?>
                                        <tr>
                                            <td class="t-left"><?php echo $obj_catalogs[$it]['part_no'];?></td>
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
                        <div style="font-size: 12px;" class="text-end">
                            器材訊息text-end
                        </div>
                    </div>
                    
                </div>
            </div>
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
                        <h4>因為整批發貨與單一申請單發貨會有抄寫衝突，暫時不開放整批發貨~</h4>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="idty" value="12">
                        <input type="hidden" name="out_user_id" value="<?php echo $_SESSION["AUTH"]['id'];?>">
                        <input type="hidden" name="pr2site" value="<?php echo implode(',',$issue_SN_list);?>">
                        <input type="submit" name="pr2site_submit" value="Submit" class="btn btn-primary" hidden>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>    
        </div>
    </div>

    <div id="gotop">
        <i class="fas fa-angle-up fa-1x"></i>
    </div>
    
</body>

<script src="../../libs/aos/aos.js"></script>
<script src="../../libs/aos/aos_init.js"></script>
<script src="../../libs/openUrl/openUrl.js"></script>           <!-- 彈出子畫面 -->
<script>

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