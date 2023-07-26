<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDeniedAdmin($sys_id);

    // 切換指定NAV分頁
    if(isset($_REQUEST["activeTab"])){
        $activeTab = $_REQUEST["activeTab"];
    }else{
        $activeTab = "1";       // 1 = fab
    }

    if(isset($_POST["delete"])){
        delete_fab($_REQUEST);
        header("location:../local/?activeTab={$activeTab}");
        exit;
    }

    if(isset($_POST["submit"])){
        update_fab($_REQUEST);
        header("location:../local/?activeTab={$activeTab}");
        exit;
    }

    $sites = show_site();
    $fab = edit_fab($_REQUEST);

    if(empty($fab)){
        // header("location:../local/");                    // 用這個，因為跳太快
        echo "<script>history.back()</script>";         // 用script導回上一頁。防止崩煃

    }
?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6 bg-light border p-4 rounded my-2">
            <div class="row">
                <div class="col-12 col-md-6 py-0">
                    <h5>編輯fab廠處級資訊</h5>
                </div> 
                <div class="col-12 col-md-6 py-0 text-end">
                    <?php if($_SESSION[$sys_id]["role"] == 0 && $fab["flag"] == "Off"){ ?>
                        <form action="" method="post">
                            <input type="hidden" name="id" value="<?php echo $fab["id"];?>">
                            <input type="submit" name="delete" value="刪除fab廠處級分類" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">
                        </form>
                    <?php } ?>
                </div> 
            </div>
            <hr>
            <form action="" method="post">
                <div class="row px-3">
                    <div class="mb-3">
                        <div class="form-floating">
                            <select name="site_id" id="site_id" class="form-select" required <?php if($_SESSION[$sys_id]["role"] > 0){ ?> disabled <?php } ?>>
                                <option value="" selected hidden>--請選擇site類別--</option>
                                <?php foreach($sites as $site){ ?>
                                    <option value="<?php echo $site["id"];?>" 
                                            <?php echo $site["id"] == $fab["site_id"] ? "selected":"";?>
                                            <?php echo $site["flag"] == "Off" ? "hidden":"";?>>
                                        <?php echo $site["id"].": ".$site["site_title"]." (".$site["site_remark"]; echo ($site["flag"] == "Off") ? "-已關閉":"".")";?></option>
                                <?php } ?>
                            </select>
                            <label for="site_id" class="form-label">site_id：<?php if($_SESSION[$sys_id]["role"] > 0){ ?><sup class="text-danger"> - disabled </sup><?php } ?></label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-floating">
                            <input type="text" name="fab_title" class="form-control" id="fab_title" required placeholder="部門名稱" value="<?php echo $fab["fab_title"];?>">
                            <label for="fab_title" class="form-label">fab/部門名稱：<sup class="text-danger"> *</sup></label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-floating">
                            <input type="text" name="fab_remark" class="form-control" name="fab_remark" required placeholder="備註說明" value="<?php echo $fab["fab_remark"];?>">
                            <label for="fab_remark" class="form-label">fab_remark/備註說明：<sup class="text-danger"> *</sup></label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <table>
                            <tr>
                                <td style="text-align: right;">
                                    <label for="ppty" class="form-label">廠區規模(限購規範)：</label>
                                </td>
                                <td style="text-align: left;">
                                    <input type="radio" name="buy_ty" value="a" id="buy_a" class="form-check-input" <?php echo $fab["buy_ty"] == "a" ? "checked":"";?>>
                                    <label for="buy_a" class="form-check-label">&nbspa.3千人以下</label>
                                </td>
                                <td style="text-align: left;">
                                    <input type="radio" name="buy_ty" value="b" id="buy_b" class="form-check-input" <?php echo $fab["buy_ty"] == "b" ? "checked":"";?>>
                                    <label for="buy_b" class="form-check-label">&nbspb.3千人以上</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">
                                    <label for="flag" class="form-label">flag/顯示開關：</label>
                                </td>
                                <td style="text-align: left;">
                                    <input type="radio" name="flag" value="On" id="fab_On" class="form-check-input" <?php echo $fab["flag"] == "On" ? "checked":"";?>>&nbsp
                                    <label for="fab_On" class="form-check-label">On</label>
                                </td>
                                <td style="text-align: left;">
                                    <input type="radio" name="flag" value="Off" id="fab_Off" class="form-check-input" <?php echo $fab["flag"] == "Off" ? "checked":"";?>>&nbsp
                                    <label for="fab_Off" class="form-check-label">Off</label>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div style="font-size: 6px;" class="text-end">
                        updated_at：<?php echo $fab["updated_at"]." /by:".$fab["updated_user"];?>
                    </div>
                </div>
                <hr>
                <div class="text-end">
                    <input type="hidden" name="id"           value="<?php echo $fab["id"];?>">
                    <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                    <input type="hidden" name="activeTab"    value="<?php echo $activeTab;?>">
                    <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                        <input type="submit" value="儲存" name="submit" class="btn btn-primary">
                    <?php } ?>
                    <input type="button" value="取消" class="btn btn-danger" onclick="history.back()">
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("../template/footer.php"); ?>