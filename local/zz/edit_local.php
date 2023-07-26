<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 切換指定NAV分頁
    if(isset($_REQUEST["activeTab"])){
        $activeTab = $_REQUEST["activeTab"];
    }else{
        $activeTab = "2";       // 2 = local
    }

    if(isset($_POST["delete"])){
        delete_local($_REQUEST);
        header("location:../local/?activeTab={$activeTab}");    // 用這個，因為跳太快
        exit;
    }

    if(isset($_POST["submit"])){
        update_local($_REQUEST);
        header("location:../local/?activeTab={$activeTab}");    // 用這個，因為跳太快
        exit;
    }

    // $sites = show_site();
    $fabs = show_fab();
    $local = edit_local($_REQUEST);

    if(empty($local)){
        echo "<script>history.back()</script>";         // 用script導回上一頁。防止崩煃
    }
?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6 bg-light border rounded p-4 my-2">
            <div class="row">
                <div class="col-12 col-md-6 py-0 ">
                    <h5>編輯Local存放單位資訊</h5>
                </div> 
                <div class="col-12 col-md-6 py-0  text-end">
                    <?php if($_SESSION[$sys_id]["role"] == 0 && $local["flag"] == "Off"){ ?>
                        <form action="" method="post" onsubmit="this.site_id.disabled=false">
                            <input type="hidden" name="id" value="<?php echo $local["id"];?>">
                            <input type="submit" name="delete" value="刪除Local" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">
                        </form>
                    <?php } ?>
                </div> 
            </div>
            <hr>
            <form action="" method="post">
                <div class="row px-4">
                    <div class="col-12">
                        <div class="form-floating">
                            <select name="fab_id" id="fab_id" class="form-select" required>
                                <option value="" selected hidden>-- 請選擇fab廠別 --</option>
                                <?php foreach($fabs as $fab){ ?>
                                    <option value="<?php echo $fab["id"];?>" title="<?php echo $fab["fab_remark"];?>"
                                            <?php echo $fab["id"] == $local["fab_id"]  ? "selected":""; ?>
                                            <?php echo $fab["flag"] == "Off" ? "hidden":"";?>>
                                        <?php echo $fab["id"]."_".$fab["fab_title"]."(".$fab["fab_remark"].")";?></option>
                                <?php } ?>
                            </select> 
                            <label for="fab_id" class="form-label">fab_id/所屬廠別：<sup class="text-danger"> *</sup></label>
                        </div>
                    </div>     
                    <div class="col-12">
                        <div class="form-floating">
                            <input type="text" name="local_title" id="local_title" class="form-control" required placeholder="local名稱" value="<?php echo $local["local_title"];?>">
                            <label for="local_title" class="form-label">local_title/名稱：</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating">
                            <input type="text" name="local_remark" id="local_remark" class="form-control" placeholder="註解說明" value="<?php echo $local["local_remark"];?>">
                            <label for="local_remark" class="form-label">local_remark/備註說明：</label>
                        </div>
                    </div>
                    <!-- <div class="col-12">
                        <div class="form-floating">
                            <input type="text" name="low_level" id="low_level" class="form-control" placeholder="安全水位" value="<php echo $local["low_level"];?>" readonly>
                            <label for="low_level" class="form-label">low_level/安全水位：<sup class="text-danger"> *</sup></label>
                        </div>
                    </div> -->
                    <div class="col-12">
                        <table>
                            <tr>
                                <td style="text-align: right;">
                                    <label for="flag" class="form-label">flag/顯示開關：</label>
                                </td>
                                <td style="text-align: left;">
                                    <input type="radio" name="flag" value="On" id="local_On" class="form-check-input" <?php echo $local["flag"] == "On" ? "checked":"";?>>&nbsp
                                    <label for="local_On" class="form-check-label">On</label>
                                </td>
                                <td style="text-align: left;">
                                    <input type="radio" name="flag" value="Off" id="local_Off" class="form-check-input" <?php echo $local["flag"] == "Off" ? "checked":"";?>>&nbsp
                                    <label for="local_Off" class="form-check-label">Off</label>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="text-end">
                    <input type="hidden" name="id"           value="<?php echo $local["id"];?>">
                    <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                    <input type="hidden" name="activeTab"    value="<?php echo $activeTab;?>">
                    <?php if($_SESSION[$sys_id]["fab_id"] == $local["fab_id"] || $_SESSION[$sys_id]["role"] <= 1 ){ ?>
                        <input type="submit" value="儲存" name="submit" class="btn btn-primary">
                    <?php } ?>
                    <input type="button" value="取消" class="btn btn-danger" onclick="history.back()">
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("../template/footer.php"); ?>