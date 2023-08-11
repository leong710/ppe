<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    if(isset($_POST["pr2fab_submit"])){    // 發貨 => 12
        update_pr2fab($_REQUEST);
        header("refresh:0;url=index.php");
        exit;
    }

    if(isset($_GET["pr_no"])){    // 抓取參數
        $pr_no = $_GET["pr_no"];
        $issues = review_issueAmount($pr_no); // 查詢PR
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
    
    $all_item = [];  $all_amount = []; $issue_fab = []; $issue_SN_list = [];

?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
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
            $issue_fab[$it] = $is['fab_i_title'].': '.$item_dec[$it];
        }else{
            $issue_fab[$it] = $issue_fab[$it].'</br>'.$is['fab_i_title'].': '.$item_dec[$it];
        }
        array_push($issue_SN_list, $is['id']);
    }
}?>

<div class="container">
    <div class="row justify-content-center">
        <!-- <div class="col-11 border rounded p-4 my-2" style="background-color: #D4D4D4;"> -->
        <div class="col-12 rounded p-4 my-2" style="background-color: rgba(200, 255, 255, .6);">
            <!-- 表單表頭功能鍵 -->
            <div class="row px-2">
                <div class="col-12 col-md-6">
                    <div class="">
                        <h3><b>請購需求單已開PR：</b><?php echo $pr_no;?></h3>
                    </div> 
                    <div class="">

                    </div> 
                </div> 
                <div class="col-12 col-md-6 text-end">
                    <div class="">
                        <?php if($_SESSION[$sys_id]['role'] <= 1){?>  
                            <a href="../trade/restock.php?pr_no=<?php echo $pr_no;?>" target="_blank" title="發貨確認" class="btn btn-primary" ><i class="fa-solid fa-arrow-right-to-bracket"></i> PR請購進貨</a>
                        <?php }?>
                        <a href="docsv.php?action=export_review&pr_no=<?php echo $pr_no;?>" title="匯出CSV" class="btn btn-success"> <i class="fa fa-download" aria-hidden="true"></i> 匯出CSV</a>
                        <a href="../issue/" title="返回" class="btn btn-info"><i class="fa fa-external-link" aria-hidden="true"></i> 返回</a>
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
                        <table class="for-table">
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
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>    
        </div>
    </div>

<!-- 彈出畫面模組2 匯出CSV-->
    <div class="modal fade" id="doCSV" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">匯出需求總表(csv)</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- 20220606測試匯出csv -->
                <form id="addform" action="docsv.php?action=export" method="post"> 
                    <div class="modal-body p-4" >
                        <div class="col-12">
                            <label for="" class="form-label">請選擇您要匯出的需求類別：<sup class="text-danger"> *</sup></label>
                            <select name="ppty" id="ppty" class="form-control" required >
                                <option value="0" hidden>0_臨時需求</option>
                                <option value="1" selected>1_定期需求</option>
                                <option value="All" hidden>All_全部</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="submit" class="btn btn-success" value="匯出CSV" onclick="doCSV.hide()"> 
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form> 
            </div>
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

<?php include("../template/footer.php"); ?>