<?php
    require_once("../pdo.php");
    require_once("function.php");

    $cate = edit_category($_REQUEST);
    if(empty($cate)){                                   // 查無資料時返回指定頁面
        echo "<script>history.back()</script>";         // 用script導回上一頁。防止崩煃
    }

    if(isset($_POST["delete"])){
        delete_category($_REQUEST);
        header("refresh:0;url=category.php");
        exit;
    }
    if(isset($_POST["submit"])){
        update_category($_REQUEST);
        header("refresh:0;url=category.php");
        exit;
    }
?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<div class="container my-2">
    <div class="row justify-content-center">
        <div class="col-6 bg-white border p-4 rounded">
            <div class="row">
                <div class="col-12 col-md-6 py-0">
                    <h5>編輯分類資訊</h5>
                </div> 
                <div class="col-12 col-md-6 py-0 text-end">
                    <?php if($_SESSION["AUTH"]["role"] == 0){ ?>
                        <form action="" method="post">
                            <input type="hidden" name="id" value="<?php echo $cate["id"];?>">
                            <input type="submit" name="delete" value="刪除分類" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('我們不建議您刪除! 如果可以請用[flag顯示開關將]其關閉即可!\n\n確認刪除？？')">
                        </form>
                    <?php } ?>
                </div> 
            </div>
            <hr>
            <form action="" method="post">
                <div class="row px-3 py-4">
                    <div class="col-12 py-1">
                        <div class="form-floating">
                            <input type="text" name="cate_no" id="cate_no" class="form-control" required placeholder="分類代號" value="<?php echo $cate["cate_no"];?>">
                            <label for="cate_no" class="form-label">cate_no/分類代號：</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-floating">
                            <input type="text" name="cate_title" id="cate_title" class="form-control" value="<?php echo $cate["cate_title"];?>">
                            <label for="cate_title" class="form-label">cate_title/分類名稱：</label>
                        </div>
                    </div>
                    <div class="">
                        <div class="form-floating">
                            <input type="text" name="cate_remark" id="cate_remark" class="form-control" required value="<?php echo $cate["cate_remark"];?>">
                            <label for="cate_remark" class="form-label">cate_remark/備註說明：</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <table>
                            <tr>
                                <td style="text-align: right;">
                                    <label for="flag" class="form-label">flag/顯示開關：</label>
                                </td>
                                <td style="text-align: left;">
                                    <input type="radio" name="flag" value="On" id="cate_On" class="form-check-input" <?php echo $cate["flag"] == "On" ? "checked":"";?>>&nbsp
                                    <label for="cate_On" class="form-check-label">On</label>
                                </td>
                                <td style="text-align: left;">
                                    <input type="radio" name="flag" value="Off" id="cate_Off" class="form-check-input" <?php echo $cate["flag"] == "Off" ? "checked":"";?>>&nbsp
                                    <label for="cate_Off" class="form-check-label">Off</label>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="text-end">
                    <input type="hidden" value="<?php echo $cate["id"];?>" name="id">
                    <?php if($_SESSION["AUTH"]["role"] <= 1){ ?>
                        <input type="submit" value="儲存" name="submit" class="btn btn-primary">
                    <?php } ?>
                    <input type="button" value="取消" class="btn btn-danger" onclick="history.back()">
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("../template/footer.php"); ?>