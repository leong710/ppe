<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    if(isset($_POST["delete"])){
        deleteUser($_REQUEST);
        header("refresh:0;url=../auth/");
        exit;
    }
    if(isset($_POST["submit"])){
        updateUser($_REQUEST);
        header("refresh:0;url=../auth/");
        exit;
    }
    // $sites = show_site();
    $fabs = show_fab();
    $user = editUser($_REQUEST);

    if(empty($user) || ($_SESSION[$sys_id]["role"] >=2 && $_SESSION[$sys_id]["id"] != $user["id"])){
        header("location:../auth/");    // 用這個，因為跳太快
        exit;
    }
?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<style>
    table,td {
        border: 0px;
        border-collapse: collapse;
        padding: 5px;
        text-align: left;
        /* background-color: #DEDEDE; */
    }
</style>
<div class="container my-2">
    <div class="row justify-content-center">
        <div class="col-8 bg-light border p-4 rounded">
            <div class="row">
                <div class="col-12 col-md-6 py-0">
                    <h4>Edit local user role</h4>
                </div>
                <div class="col-12 col-md-6 py-0 text-end">
                    <?php if($user["role"] == "" && ($_SESSION[$sys_id]["role"] <= 1)){ ?>
                        <form action="" method="post">
                            <input type="hidden" name="id" value="<?php echo $user["id"];?>">
                            <input type="submit" name="delete" value="刪除" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">
                        </form>
                    <?php } ?>
                </div>
            </div>
            <hr>
            <form action="" method="post" enctype="multipart/form-data" 
                    onsubmit="this.user.disabled=false, this.emp_id.disabled=false, this.cname.disabled=false, this.idty.disabled=false, this.role.disabled=false, this.sys_id.disabled=false";>
                <div class="px-4">
                    <div class="row">
                        <div class="col-12 col-md-6 py-2">
                            <div class="form-floating">
                                <input type="text" name="user" class="form-control" id="floatingUser" value="<?php echo $user["user"];?>" <?php echo ($_SESSION[$sys_id]["role"] >= 1) ? "readonly":"required";?>>
                                <label for="floatingUser" class="form-label">user/帳號：<sup class='text-danger'><?php echo ($_SESSION[$sys_id]["role"] >= 1) ? " - disabled":" *";?></sup></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 py-2">
                            <div class="form-floating">
                                <input type="text" name="sys_id" id="sys_id" class="form-control" value="<?php echo $sys_id;?>" required readOnly>
                                <label for="sys_id" class="form-label">sys_id：<sup class="text-danger"> - readOnly</sup></label>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 py-2">
                            <div class="form-floating">
                                <input type="text" name="emp_id" class="form-control" id="floatingEmp_id" value="<?php echo $user["emp_id"];?>" <?php echo ($_SESSION[$sys_id]["role"] >= 1) ? "readonly":"required";?>>
                                <label for="floatingEmp_id" class="form-label">emp_id/工號：<sup class='text-danger'><?php echo ($_SESSION[$sys_id]["role"] >= 1) ? " - disabled":" *";?></sup></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 py-2">
                            <div class="form-floating">
                                <input type="text" name="cname" class="form-control" id="floatingCname" value="<?php echo $user["cname"];?>" <?php echo ($_SESSION[$sys_id]["role"] >= 1) ? "readonly":"required";?>>
                                <label for="floatingCname" class="form-label">cname/中文姓名：<sup class='text-danger'><?php echo ($_SESSION[$sys_id]["role"] >= 1) ? " - disabled":" *";?></sup></label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 col-md-6 py-2">
                            <div class="form-floating">
                                <select name="idty" id="idty" class="form-select" <?php echo ($_SESSION[$sys_id]["role"] >= 2) ? "disabled":"";?>>
                                    <option value="" <?php  echo ($user["idty"] == "" ) ? "selected":"";?> >停用</option>
                                    <option value="1" <?php echo ($user["idty"] == "1" ) ? "selected":"";?> >1_工程師</option>
                                    <option value="2" <?php echo ($user["idty"] == "2" ) ? "selected":"";?> >2_課副理</option>
                                    <option value="3" <?php echo ($user["idty"] == "3" ) ? "selected":"";?> >3_部經理層</option>
                                    <option value="4" <?php echo ($user["idty"] == "4" ) ? "selected":"";?> >4_廠處長層</option>
                                </select>
                                <label for="idty" class="form-label">idty/身份定義：<sup class="text-danger"><?php echo ($_SESSION[$sys_id]["role"] >= 2) ? " - disabled":" *";?></sup></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 py-2">
                            <div class="form-floating">
                                <select name="role" id="role" class="form-select" <?php echo ($_SESSION[$sys_id]["role"] >= 2) ? "disabled":"";?>>
                                    <option value=""  for="role" <?php echo ($user["role"] == "") ? "selected":"";?>>停用</option>
                                    <option value="0" for="role" <?php echo ($user["role"] == "0") ? "selected":"";?>
                                                                <?php echo ($_SESSION[$sys_id]["role"] >= 1) ? "hidden":"";?>>0_管理</option>
                                    <option value="1" for="role" <?php echo ($user["role"] == "1") ? "selected":"";?>>1_PM</option>
                                    <option value="2" for="role" <?php echo ($user["role"] == "2") ? "selected":"";?>>2_siteUser</option>
                                    <option value="3" for="role" <?php echo ($user["role"] == "3") ? "selected":"";?>>3_noBody</option>
                                </select>
                                <label for="role" class="form-label">role/權限：<sup class='text-danger'><?php echo ($_SESSION[$sys_id]["role"] >= 2) ? " - disabled":" *";?></sup></label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6 py-2">
                            <div class="form-floating">
                                <select name="fab_id" id="fab_id" class="form-select" aria-label="Floating label select example" required <?php echo ($_SESSION[$sys_id]["role"] >= 2) ? "disabled":"";?>>
                                    <option value="" hidden>--請選擇主fab--</option>
                                    <?php foreach($fabs as $fab){ ?>
                                        <option value="<?php echo $fab["id"];?>" for="fab_id" <?php echo ($fab["id"] == $user["fab_id"]) ? "selected":"";?>>
                                            <?php echo $fab["id"].": ".$fab["fab_title"]." (".$fab["fab_remark"].")"; echo ($fab["flag"] == "Off") ? "--(已關閉)":"";?></option>
                                    <?php } ?>
                                </select>
                                <label for="fab_id" class="form-label">主fab_id：<sup class='text-danger'><?php echo ($_SESSION[$sys_id]["role"] >= 2) ? " - disabled":" *";?></sup></label>
                            </div>
                        </div>

                        <div class="col-12 py-2">
                            <?php 
                                $user["sfab_id"] = explode(",",$user["sfab_id"]);       //資料表是字串，要炸成陣列
                                $i = 0; 
                            ?>
                            <label for="" class="form-label">副sfab_id：<sup class="text-danger"><?php echo ($_SESSION[$sys_id]["role"] >= 2 ) ? " - disabled":" 選填" ?></sup></label>
                            <div class="border rounded p-2">
                                <table>
                                    <tbody>
                                        <tr>
                                            <?php foreach($fabs as $fab){ ?>
                                                <td>
                                                    <input type="checkbox" name="sfab_id[]" value="<?php echo $fab["id"];?>" id="<?php echo $fab["id"];?>" class="form-check-input" 
                                                        <?php echo in_array($fab["id"], $user["sfab_id"]) ? "checked":""; ?> <?php echo $_SESSION[$sys_id]["role"] >= 2 ? "disabled":"" ?>>
                                                    <label for="<?php echo $fab["id"];?>" class="form-check-label">&nbsp<?php echo $fab["fab_title"];?></label>
                                                </td>
                                                <?php $i++; if($i%6 == 0){?> </tr> <?php }  ?> 
                                            <?php } ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-end">
                    <input type="hidden" value="<?php echo $user["id"];?>" name="id">
                    <input type="submit" value="儲存" name="submit" class="btn btn-primary">
                    <input type="button" value="取消" class="btn btn-secondary" onclick="history.back()">
                </div>
            </form>
        </div>
    </div>
</div>
 
<?php include("../template/footer.php"); ?>