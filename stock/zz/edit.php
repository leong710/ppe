<?php
    require_once("../pdo.php");
    require_once("function.php");
    accessDenied();

    $stock = edit_stock($_REQUEST);
    if(empty($stock)){              // 查無資料時返回指定頁面
        // header("location:../");     // 用這個，因為跳太快
        echo "<script>history.back()</script>";     // 用script導回上一頁。防止崩煃
    }

    // edit時用全區域~ 但user鎖編輯權限
    $locals = show_allLocal();
    $catalogs = show_catalogs();    // 衛材名稱
    
    $stock_siteID = "";
    $i = 0;    
    $j = 0;

    // accessDeniedAdmin();
    // $sites = show_site();
    // $fabs = show_fab();
    // if(isset($_POST["delete"])){
    //     delete_stock($_REQUEST);
    // }
    // if(isset($_POST["spinOff_submit"])){
    //     spinOff_stock($_REQUEST);
    //     if($_REQUEST["from2"] == "repo"){
    //         header("refresh:0;url=../catalog/".$_REQUEST["from2"].".php?id=".$_REQUEST["catalog_id"]);
    //     }else{
    //         header("refresh:0;url=".$_REQUEST["from2"].".php");
    //     }
    // }
?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-8 bg-white border rounded p-4 my-2">
            <div class="row">
                <div class="col-12 col-md-6 py-0">
                    <h5>編輯local單位衛材存量</h5>
                </div> 
                <div class="col-12 col-md-6 py-0 text-end">
                    <div>
                        <?php if($stock["amount"] == 0){ ?>
                            <form action="delete.php" method="post">
                                <input type="hidden" name="id" value="<?php echo $stock["id"];?>">
                                <input type="hidden" name="from2" value="<?php echo $_REQUEST["from2"];?>">
                                <input type="submit" name="delete" value="刪除此紀錄" class="btn btn-danger" onclick="return confirm('確認刪除？')">
                            </form>
                        <?php } ?>
                    </div>
                 </div> 
            </div>
            <hr>
            <form action="update.php" method="post"   onsubmit="this.local_id.disabled=false, this.catalog_id.disabled=false, this.standard_lv.disabled=false, this.amount.disabled=false, 
                                                                this.remark.disabled=false, this.lot_num.disabled=false, this.po_num.disabled=false, this.d_remark.disabled=false">
                <div class="px-2">
                    <!-- 第一列 儲存區/衛材名稱 -->
                    <div class="col-12 rounded py-1" style="background-color: #FFB5B5;">
                        <div class="row">
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <select name="local_id" id="local_id" class="form-control" <?php echo ($_SESSION["AUTH"]["role"] > 0) ? " disabled ":" required ";?>>
                                        <!-- <option value="" selected hidden>--請選擇儲存點--</option> -->
                                        <?php foreach($locals as $local){ ?>
                                            <option value="<?php echo $local["id"];?>" title="<?php echo $local["site_title"];?>"
                                                <?php if($local["id"] == $stock["local_id"]){ $stock_siteID = $local["site_id"];?> selected <?php } ?><?php echo $local["flag"] == "Off" ? " hidden ":"";?>>
                                                    <?php echo $local["id"].":".$local["fab_title"]."&nbsp".$local["site_title"]."_".$local["local_title"]; echo $local["flag"] == "Off" ? " (( 已關閉 ))":"";?></option>
                                        <?php } ?>
                                    </select>
                                    <label for="local_id" class="form-label">local/儲存點位置：<sup class="text-danger"><?php echo ($_SESSION["AUTH"]["role"] > 0) ? " - disabled":" *";?></sup></label>
                                </div>
                            </div>
                            
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <select name="catalog_id" id="catalog_id" class="form-control" <?php echo ($_SESSION["AUTH"]["role"] > 0) ? " disabled ":" required ";?> >
                                        <!-- <option value="" selected hidden>--請選擇衛材--</option> -->
                                        <?php foreach($catalogs as $catalog){ ?>
                                            <option value="<?php echo $catalog["id"];?>" title="<?php echo $catalog["remark"];?>"
                                                <?php echo ($catalog["id"] == $stock["catalog_id"]) ? " selected":""; echo $catalog["flag"] == "Off" ? " hidden ":"";?>>
                                                    <?php echo $catalog["id"].":".$catalog["title"]; echo $catalog["flag"] == "Off" ? " (( 已下架 ))":"";?></option>
                                        <?php } ?>
                                    </select>
                                    <label for="catalog_id" class="form-label">catalog/衛材名稱：<sup class="text-danger"><?php echo ($_SESSION["AUTH"]["role"] > 0) ? " - disabled":" *";?></sup></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 第二列 -->
                    <div class="col-12 rounded bg-light">
                        <div class="row">
                            <!-- 左側-數量 -->
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating">
                                    <input type="number" name="standard_lv" id="standard_lv" class="form-control t-center" placeholder="標準數量(管理員填)" min="1" max="400"
                                        <?php echo ($_SESSION["AUTH"]["role"] >= 1) ? " readonly ":""; ?> value="<?php echo $stock["standard_lv"];?>" >
                                    <label for="standard_lv" class="form-label">standard_lv/安全存量：<sup class="text-danger"><?php echo ($_SESSION["AUTH"]["role"] >= 1) ? " - disabled":" *";?></sup></label>
                                </div>
                            </div>
                            <!-- 右側-批號 -->
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating pb-0">
                                    <input type="date" name="lot_num" id="lot_num" class="form-control" required value="<?php echo $stock["lot_num"];?>" <?php echo ($_SESSION["AUTH"]["role"] > 1) ? " readonly ":"";?>>
                                    <label for="lot_num" class="form-label">lot_num/批號/期限：<sup class="text-danger"><?php echo ($_SESSION["AUTH"]["role"] > 1) ? " - disabled":" *";?></sup></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating">
                                    <input type="number" name="amount" id="amount" class="form-control t-center" required placeholder="正常數量" min="0" max="9999"
                                        value="<?php echo $stock["amount"];?>" >
                                    <label for="amount" class="form-label">amount/現場存量：<sup class="text-danger">*</sup></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating">
                                    <input type="text" name="po_num" id="po_num" class="form-control" placeholder="PO編號" value="<?php echo $stock["po_num"];?>" <?php echo ($_SESSION["AUTH"]["role"] > 1) ? " readonly ":"";?> >
                                    <label for="po_num" class="form-label">po_num/PO編號：<sup class="text-danger"><?php echo ($_SESSION["AUTH"]["role"] > 1) ? " - disabled":" *";?></sup></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating">
                                    <textarea name="remark" id="remark" class="form-control" style="height: 100px"><?php echo $stock["remark"];?></textarea>
                                    <label for="remark" class="form-label">remark/備註說明：</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating">
                                    <textarea name="d_remark" id="d_remark" class="form-control" style="height: 100px" <?php if($_SESSION["AUTH"]["role"] > 1){?>readonly="true"<?php }?>><?php echo $stock["d_remark"];?></textarea>
                                    <label for="d_remark" class="form-label">d_remark/其他說明：</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-end">
                    <input type="hidden" value="<?php echo $_REQUEST["from2"];?>" name="from2">
                    <input type="hidden" value="<?php echo $stock["id"];?>" name="id">
                    <input type="hidden" value="<?php echo $_SESSION["AUTH"]["id"];?>" name="user_id">
                    <?php if($_SESSION["AUTH"]["role"] <= 1 || ( $_SESSION["AUTH"]["role"] <= 2 && 
                            ($_SESSION["AUTH"]["site_id"] == $stock_siteID || in_array($stock_siteID, $_SESSION["AUTH"]["ssite_id"])) )){ ?>
                        <input type="submit" value="儲存" name="submit" class="btn btn-primary">
                    <?php } ?>
                    <input type="button" value="取消" class="btn btn-danger" onclick="history.back()">
                    <!-- <a href="index.php" class="btn btn-info">回首頁</a> -->
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("../template/footer.php"); ?>