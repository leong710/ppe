<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);
    // accessDeniedAdmin();

    $auth_emp_id = $_SESSION["AUTH"]["emp_id"];     // 取出$_session引用
    $sys_role    = $_SESSION[$sys_id]["role"];      // 取出$_session引用

    // CRUD
        if(isset($_POST["add_submit"])){              // 新增
            store_formplan($_REQUEST); }
        if(isset($_POST["edit_submit"])){             // 更新
            update_formplan($_REQUEST); }
        if(isset($_POST["delete_submit"])){           // 刪除
            delete_formplan($_REQUEST); }
        // 調整flag ==> 20230712改用AJAX

    $formplans = show_formplan();
?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<style>

</style>
<div class="container my-2">
    <div class="row justify-content-center">
        <div class="col-xl-12 col-10 border rounded bg-white p-4 ">
            <div class="row">
                <div class="col-md-6 pb-0">
                    <div>
                        <h5>formPlan 表單計畫列表 - 共 <?php echo count($formplans);?> 筆</h5>
                    </div>
                </div>
                <div class="col-md-6 pb-0 text-end">
                    <?php if($sys_role <= 1){ ?>
                        <button type="button" id="add_formplan_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_modal" onclick="add_module()" > <i class="fa fa-plus"></i> 新增</button>
                    <?php } ?>
                    <a href="index.php" title="回上層列表" class="btn btn-secondary"><i class="fa fa-external-link" aria-hidden="true"></i> 返回管理</a>
                </div>
            </div>
            <!-- 分類列表 -->
            <hr>
            <div class="px-4">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>_type</th>
                            <th>remark</th>
                            <th>start_time</th>
                            <th>end_time</th>
                            <th>_inplan</th>
                            <th>flag</th>
                            <?php if($sys_role <= 1){ ?>
                                <th>action</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($formplans as $plan){ ?>
                            <tr>
                                <td><?php echo $plan["id"];?></td>
                                <td><?php echo $plan["_type"];?></td>
                                <td><?php echo $plan["remark"];?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($plan["start_time"]));?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($plan["end_time"]));?></td>
                                <td><?php echo $plan["_inplan"];?></td>
                                <td><?php if($sys_role <= 1){ ?>
                                        <button type="button" name="_formplan" id="<?php echo $plan['id'];?>" class="btn btn-sm btn-xs flagBtn <?php echo $plan['flag'] == 'On' ? 'btn-success':'btn-warning';?>" value="<?php echo $plan['flag'];?>"><?php echo $plan['flag'];?></button>
                                    <?php }else{ ?>
                                        <span class="btn btn-sm btn-xs <?php echo $plan['flag'] == 'On' ? 'btn-success':'btn-warning';?>">
                                            <?php echo $plan['flag'] == 'On' ? '顯示':'隱藏';?>
                                        </span>
                                    <?php } ?></td>
                                <td><?php if($sys_role <= 1){ ?>    
                                    <button type="button" id="edit_modal_btn" value="<?php echo $plan['id'];?>" class="btn btn-sm btn-xs btn-info" 
                                        data-bs-toggle="modal" data-bs-target="#edit_modal" onclick="edit_module(this.value)" >編輯</button>
                                <?php } ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <hr>
        </div>
    </div>
</div>

<!-- 彈出畫面模組 編輯、新增-->
    <div class="modal fade" id="edit_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" aria-modal="true" role="dialog" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span id="modal_action"></span>計畫</h5>

                    <form action="" method="post">
                        <input type="hidden" name="id" id="delete_id">
                        <?php if($sys_role == 0){ ?>
                            &nbsp&nbsp&nbsp&nbsp&nbsp
                            <span id="modal_delect_btn"></span>
                        <?php } ?>
                    </form>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="" method="post">
                    <div class="modal-body px-3">
                        <div class="row">
                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <select name="_type" id="_type" class="form-select" required>
                                        <option for="_type" value="" hidden >-- 選擇計畫表單 --</option>
                                        <option for="_type" value="issue"   >1.issue請購需求</option>
                                        <option for="_type" value="trade"   >2.trade出入作業</option>
                                        <option for="_type" value="receive" >3.receive領用</option>
                                    </select>
                                    <label for="_type" class="form-label">type/表單類別：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <input type="text" name="remark"  id="remark" class="form-control" placeholder="(由申請單位填寫用品/器材請領原由)" required >
                                    <label for="remark" class="form-label">remark/用途說明：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating">
                                    <input type="datetime-local" class="form-control" id="start_time" name="start_time" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                                    <label for="start_time" class="form-label">start_time/開始時間：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating">
                                    <input type="datetime-local" class="form-control" id="end_time" name="end_time" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                                    <label for="end_time" class="form-label">end_time/結束時間：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 py-1 px-0">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="_inplan" class="form-label">inplan/計畫期間：<sup class="text-danger"> *</sup></label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="_inplan" value="On" id="_inplan_On" class="form-check-input" >
                                            <label for="_inplan_On" class="form-check-label">On</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="_inplan" value="Off" id="_inplan_Off" class="form-check-input" >
                                            <label for="_inplan_Off" class="form-check-label">Off</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-12 col-md-6 py-1 px-0">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="flag" class="form-label">flag/啟用開關：<sup class="text-danger"> *</sup></label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="On" id="flag_On" class="form-check-input" >
                                            <label for="flag_On" class="form-check-label">On</label>
                                        </td>
                                        <td style="text-align: left;">
                                            <input type="radio" name="flag" value="Off" id="flag_Off" class="form-check-input">
                                            <label for="flag_Off" class="form-check-label">Off</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- 最後編輯資訊 -->
                            <div class="col-12 text-end py-0" id="edit_info" style="font-size: 10px;"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="id" id="edit_id" >
                            <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                            <?php if($sys_role <= 1){ ?>
                                <span id="modal_button"></span>
                            <?php } ?>
                            <input type="reset" class="btn btn-info" id="reset_btn" value="清除">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

<!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
<!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
<script src="../../libs/sweetalert/sweetalert.min.js"></script>

<script>

    var formplans     = <?=json_encode($formplans);?>;                                // 引入formplans資料
    var formplan_item = ['id','_type','remark','start_time','end_time','_inplan','flag'];           // 交給其他功能帶入 delete_cate_id

    function add_module(to_module){     // 啟用新增模式
        $('#modal_action, #modal_button, #modal_delect_btn, #edit_info').empty();   // 清除model功能
        $('#reset_btn').click();                                                        // reset清除表單
        var reset_btn = document.getElementById('reset_btn');   // 指定清除按鈕
        reset_btn.classList.remove('unblock');                  // 新增模式 = 解除
        document.querySelector("#edit_modal .modal-header").classList.remove('edit_mode_bgc');
        document.querySelector("#edit_modal .modal-header").classList.add('add_mode_bgc');
        var add_btn = '<input type="submit" name="add_submit" class="btn btn-primary" value="新增">';
        $('#modal_action').append('新增');                      // model標題
        $('#modal_button').append(add_btn);                     // 儲存鈕
    }
    // fun-1.鋪編輯畫面
    function edit_module(row_id){
        $('#modal_action, #modal_button, #modal_delect_btn, #edit_info').empty();   // 清除model功能
        $('#reset_btn').click();                                                        // reset清除表單
        var reset_btn = document.getElementById('reset_btn');   // 指定清除按鈕
        reset_btn.classList.add('unblock');                     // 編輯模式 = 隱藏
        document.querySelector("#edit_modal .modal-header").classList.remove('add_mode_bgc');
        document.querySelector("#edit_modal .modal-header").classList.add('edit_mode_bgc');
        // remark: to_module = 來源與目的 site、fab、local
        // step1.將原排程陣列逐筆繞出來
        Object(formplans).forEach(function(row){   
            if(row['id'] == row_id){
                
                // step2.鋪畫面到module
                Object(formplan_item).forEach(function(item_key){
                    // console.log(row_id, item_key, row[item_key]);       
                    if(item_key == 'id'){
                        document.querySelector('#delete_id').value = row['id'];       // 鋪上delete_id = this id.no for delete form
                        document.querySelector('#edit_id').value = row['id'];         // 鋪上edit_id = this id.no for edit form
                    }else if(item_key == '_inplan'){
                        document.querySelector('#edit_modal #_inplan_'+row[item_key]).checked = true;
                    }else if(item_key == 'flag'){
                        document.querySelector('#edit_modal #flag_'+row[item_key]).checked = true;
                    }else{
                        document.querySelector('#edit_modal #'+item_key).value = row[item_key]; 
                    }
                })

                // 鋪上最後更新
                let to_module_info = '最後更新：'+row['updated_at']+' / by '+row['updated_user'];
                document.querySelector('#edit_info').innerHTML = to_module_info;

                // step3-3.開啟 彈出畫面模組 for user編輯
                // edit_myTodo_btn.click();
                var add_btn = '<input type="submit" name="edit_submit" class="btn btn-primary" value="儲存">';
                var del_btn = '<input type="submit" name="delete_submit" value="刪除formplan" class="btn btn-sm btn-xs btn-danger" onclick="return confirm(`確認刪除？`)">';
                $('#modal_action').append('編輯');          // model標題
                $('#modal_delect_btn').append(del_btn);     // 刪除鈕
                $('#modal_button').append(add_btn);         // 儲存鈕
                return;
            }
        })
    }

    // catalog 切換上架/下架開關 20230712
    let flagBtns = [...document.querySelectorAll('.flagBtn')];
    for(let flagBtn of flagBtns){
        flagBtn.onclick = e => {
            let swal_content = e.target.name+'_id:'+e.target.id+'=';
            $.ajax({
                url:'api.php',
                method:'post',
                async: false,                         // ajax取得數據包後，可以return的重要參數
                dataType:'json',
                data:{
                    function: 'cheng_flag',           // 操作功能
                    table: e.target.name,
                    id: e.target.id,
                    flag: e.target.value
                },
                success: function(res){
                    let res_r = res["result"];
                    let res_r_flag = res_r["flag"];
                    if(res_r_flag == 'Off'){
                        e.target.classList.remove('btn-success');
                        e.target.classList.add('btn-warning');
                        e.target.value = 'Off';
                        e.target.innerText = 'Off';
                    }else{
                        e.target.classList.remove('btn-warning');
                        e.target.classList.add('btn-success');
                        e.target.value = 'On';
                        e.target.innerText = 'On';
                    }
                    swal_action = 'success';
                    swal_content += res_r_flag+' 套用成功';

                },
                error: function(e){
                    swal_action = 'error';
                    swal_content += res_r_flag+' 套用失敗';
                    console.log("error");
                }
            });
            swal('change_flag' ,swal_content ,swal_action, {buttons: false, timer:1000});

        }
    }

</script>

<?php include("../template/footer.php"); ?>