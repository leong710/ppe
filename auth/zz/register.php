<?php 
    require_once("../pdo.php");
    require_once("function.php");

    include("../template/header.php");
    include("../template/nav.php");

    if(!isset($_REQUEST["user"])){
        $user = "your NT ID";
        $bPass = false;
    }else{
        $user = $_REQUEST["user"];
        $bPass = true;
    }

    echo "<script>alert('err:2-1 > 帳號資料不存在，請接續完成資料更新~')</script>";

?>
<head>
    <style>
        .unblock{
            display: none;
            /* transition: 3s; */
        }
    </style>
</head>

<div class="container my-2">
    <div class="row justify-content-center">
        <div class="col-8 bg-light border p-5 rounded">
            <h3>更新User資訊</h3>
            <hr>
            <form action="store.php" method="post" enctype="multipart/form-data" 
                    onsubmit="this.user.disabled=false, this.role.disabled=false, this.cname.disabled=false, this.emp_id.disabled=false, this.site_id.disabled=false">
                <div class="row">
                    <div class="mb-3 col-12 col-md-6">
                        <div class="form-floating">
                            <input type="text" name="user" class="form-control" id="floatingUser" required value="<?php echo $user;?>" readonly>
                            <label for="floatingUser" class="form-label">user ID：<sup class="text-danger"> *</sup></label>
                        </div>
                    </div>
                    <div class="mb-3 col-12 col-md-6">
                        <div class="form-floating">
                            <input type="text" name="sys_id" id="sys_id" class="form-control" value="<?php echo $sys_id;?>" required readOnly>
                            <label for="sys_id" class="form-label">sys_id：<sup class="text-danger"> - readOnly</sup></label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3 col-12 col-md-6">
                        <div class="form-floating">
                            <input type="text" name="emp_id" class="form-control" id="floatingEmp_id" required>
                            <label for="floatingEmp_id" class="form-label">emp_id/工號：<sup class="text-danger"> *</sup></label>
                        </div>
                    </div>
                    <div class="mb-3 col-12 col-md-6">
                        <div class="form-floating">
                            <input type="text" name="cname" id="floatingCname" class="form-control" required>
                            <label for="floatingCname" class="form-label">中文姓名：<sup class="text-danger"> *</sup></label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3 col-12 col-md-6">
                        <div class="form-floating">
                            <select name="idty" id="idty" class="form-select" required>
                                <option value="" for="idty" hiddel selected>-- 請選擇職稱 --</option>
                                <option value="1" for="idty" >1_工程師</option>
                                <option value="2" for="idty" >2_課副理</option>
                                <option value="3" for="idty" >3_部經理層</option>
                                <option value="4" for="idty" >4_廠處長層</option>
                            </select>
                            <label for="idty" class="form-label">idty/身份定義：<sup class="text-danger"> *</sup></label>
                        </div>
                    </div>
                    <div class="mb-3 col-12 col-md-6">
                        <div class="form-floating">
                            <select name="role" id="role" class="form-select" disabled>
                                <option value=""  for="role" hidden>停用</option>
                                <option value="0" for="role" hidden>0_管理</option>
                                <option value="1" for="role" hidden>1_PM</option>
                                <option value="2" for="role" hidden>2_siteUser</option>
                                <option value="3" for="role" selected>3_noBody</option>
                            </select>
                            <label for="role" class="form-label">role/權限：<sup class="text-danger"> - disabled </sup></label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-12 col-md-6">
                        <div class="form-floating">
                            <select name="site_id" id="site_id" class="form-select" disabled>
                                <option value="" hidden>-- 請選擇主site別 --</option>
                                <option value="0" selected >0_觀察員</option>
                            </select>
                            <label for="site_id" class="form-label">site_id：<sup class="text-danger"> - disabled </sup></label>
                        </div>
                    </div>
                    <div class="mb-3 col-12 col-md-6">
                        <input type="hidden" value="" name="ssite_id">
                    </div>
                </div>
                <hr>
                <div class="text-end">
                    <input type="submit" value="儲存" name="submit" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("../template/footer.php"); ?>