<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDeniedAdmin($sys_id);

    // 切換指定NAV分頁
    if(isset($_REQUEST["activeTab"])){
        $activeTab = $_REQUEST["activeTab"];
    }else{
        $activeTab = "0";       // 0 = site
    }

    if(isset($_POST["delete"])){
        delete_site($_REQUEST);
        header("location:../local/?activeTab={$activeTab}");    // 用這個，因為跳太快
        exit;
    }

    if(isset($_POST["submit"])){
        update_site($_REQUEST);
        header("location:../local/?activeTab={$activeTab}");    // 用這個，因為跳太快
        exit;
    }

    // $fabs = show_fab();
    $site = edit_site($_REQUEST);

    if(empty($site)){
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
                    <h5>編輯site資訊</h5>
                </div> 
                <div class="col-12 col-md-6 py-0 text-end">
                    <?php if($_SESSION[$sys_id]["role"] == 0 && $site["flag"] == "Off"){ ?>
                        <form action="" method="post">
                            <input type="hidden" name="id" value="<?php echo $site["id"];?>">
                            <input type="submit" name="delete" value="刪除site分類" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">
                        </form>
                    <?php } ?>
                </div> 
            </div>
            <hr>
            <form action="" method="post">
                <div class="row px-3">
               
                    <div class="mb-3">
                        <div class="form-floating">
                            <input type="text" name="site_title" id="site_title" class="form-control" required placeholder="site名稱" value="<?php echo $site["site_title"];?>">
                            <label for="site_title" class="form-label">site_title/site名稱：<sup class="text-danger"> *</sup></label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-floating">
                            <input type="text" name="site_remark" id="site_remark" class="form-control" required placeholder="備註說明" value="<?php echo $site["site_remark"];?>">
                            <label for="site_remark" class="form-label">site_remark/備註說明：<sup class="text-danger"> *</sup></label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <table>
                            <tr>
                                <td style="text-align: right;">
                                    <label for="flag" class="form-label">flag/顯示開關：</label>
                                </td>
                                <td style="text-align: left;">
                                    <input type="radio" name="flag" value="On" id="site_On" class="form-check-input" <?php echo $site["flag"] == "On" ? "checked":"";?>>&nbsp
                                    <label for="site_On" class="form-check-label">On</label>
                                </td>
                                <td style="text-align: left;">
                                    <input type="radio" name="flag" value="Off" id="site_Off" class="form-check-input" <?php echo $site["flag"] == "Off" ? "checked":"";?>>&nbsp
                                    <label for="site_Off" class="form-check-label">Off</label>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div style="font-size: 6px;" class="text-end">
                        updated_at：<?php echo $site["updated_at"]." /by:".$site["updated_user"];?>
                    </div>
                </div>
                <hr>
                <div class="text-end">
                    <input type="hidden" name="id"           value="<?php echo $site["id"];?>">
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