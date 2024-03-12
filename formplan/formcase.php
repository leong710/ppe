<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    // accessDenied($sys_id);
    accessDeniedAdmin($sys_id);

    $auth_cname = $_SESSION["AUTH"]["cname"];     // 取出$_session引用
    $sys_role   = $_SESSION[$sys_id]["role"];     // 取出$_session引用

    // CRUD
        if(isset($_POST["submit_formcase"])){ store_formcase($_REQUEST); }  // 新增
        if(isset($_POST["edit_formcase"]))  { update_formcase($_REQUEST); } // 更新
        if(isset($_POST["delete_formcase"])){ delete_formcase($_REQUEST); } // 刪除
        // 調整flag ==> 20230712改用AJAX

    $formcases = show_formcase();
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
                        <h5>計畫表單 列表 - 共 <?php echo count($formcases);?> 筆</h5>
                    </div>
                </div>
                <div class="col-md-6 pb-0 text-end">
                    <?php if($sys_role <= 1){ ?>
                        <button type="button" id="add_formcase_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_modal" onclick="add_module('formcase')" > <i class="fa fa-plus"></i> 新增表單</button>
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
                            <th>title</th>
                            <th>flag</th>
                            <th>created/updated</th>
                            <th>updated_user/action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($formcases as $formcase){ ?>
                            <tr>
                                <td><?php echo $formcase["id"];?></td>
                                <td><?php echo $formcase["_type"];?></td>
                                <td><?php echo $formcase["title"];?></td>
                                <td><button type="button" name="_formcase" id="<?php echo $formcase['id'];?>" value="<?php echo $formcase['flag'];?>"
                                        class="btn btn-sm btn-xs flagBtn <?php echo $formcase['flag'] == 'On' ? 'btn-success':'btn-warning';?>"><?php echo $formcase['flag'];?></button>
                                </td>
                                <td><?php echo $formcase["created_at"]."</br>".$formcase["updated_at"];?></td>
                                <td><?php echo $formcase["updated_user"]."&nbsp";?>    
                                    <button type="button" id="edit_formcase_btn" value="<?php echo $formcase['id'];?>" class="btn btn-sm btn-xs btn-info" 
                                        data-bs-toggle="modal" data-bs-target="#edit_modal" onclick="edit_module('formcase',this.value)" >編輯</button>
                                </td>
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
                <h5 class="modal-title"><span id="modal_action"></span>表單</h5>

                <form action="" method="post">
                    <input type="hidden" name="id" id="formcase_delete_id">
                    &nbsp&nbsp&nbsp&nbsp&nbsp
                    <span id="modal_delect_btn" class="<?php echo ($sys_role == 0) ? "":" unblock ";?>"></span>
                </form>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="" method="post">
                <div class="modal-body px-5">
                    <div class="row">

                        <div class="col-12 py-1">
                            <div class="form-floating">
                                <input type="text" name="title" id="formcase_title" class="form-control" required placeholder="表單名稱">
                                <label for="formcase_title" class="form-label">title/表單名稱：</label>
                            </div>
                        </div>

                        <div class="col-12 py-1">
                            <div class="form-floating">
                                <input type="text" name="_type" id="formcase__type" class="form-control" required placeholder="表單代號">
                                <label for="formcase__type" class="form-label">_type/表單代號：</label>
                            </div>
                        </div>

                        <div class="col-12 py-1">
                            <table>
                                <tr>
                                    <td style="text-align: right;">
                                        <label for="flag" class="form-label">flag/顯示開關：</label>
                                    </td>
                                    <td style="text-align: left;">
                                        <input type="radio" name="flag" value="On" id="formcase_On" class="form-check-input" checked >&nbsp
                                        <label for="formcase_On" class="form-check-label">On</label>
                                    </td>
                                    <td style="text-align: left;">
                                        <input type="radio" name="flag" value="Off" id="formcase_Off" class="form-check-input">&nbsp
                                        <label for="formcase_Off" class="form-check-label">Off</label>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- 最後編輯資訊 -->
                        <div class="col-12 text-end p-0" id="formcase_info"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="text-end">
                        <input type="hidden" name="id" id="formcase_edit_id" >
                        <input type="hidden" name="updated_user" value="<?php echo $auth_cname;?>">
                            <span id="modal_button" class="<?php echo ($sys_role <= 1) ? "":" unblock ";?>"></span>
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

    var formcase        = <?=json_encode($formcases)?>;               // 引入formcases資料
    var formcase_item   = ['id', '_type', 'title', 'flag'];           // 交給其他功能帶入 delete_cate_id

    function add_module(to_module){     // 啟用新增模式
        $('#modal_action, #modal_button, #modal_delect_btn, #formcase_info').empty();     // 清除model功能
        $('#reset_btn').click();                                                                    // reset清除表單
        var add_btn = '<input type="submit" name="submit_formcase" value="新增" class="btn btn-primary">';
        $('#modal_action').append('新增');                      // model標題
        $('#modal_button').append(add_btn);                     // 儲存鈕
        var reset_btn = document.getElementById('reset_btn');   // 指定清除按鈕
        reset_btn.classList.remove('unblock');                  // 新增模式 = 解除
        document.querySelector("#edit_modal .modal-header").classList.remove('edit_mode_bgc');
        document.querySelector("#edit_modal .modal-header").classList.add('add_mode_bgc');
    }
    // fun-1.鋪編輯畫面
    function edit_module(to_module, row_id){
        $('#modal_action, #modal_button, #modal_delect_btn, #formcase_info').empty();   // 清除model功能
        $('#reset_btn').click();                                                        // reset清除表單
        var reset_btn = document.getElementById('reset_btn');   // 指定清除按鈕
        reset_btn.classList.add('unblock');                     // 編輯模式 = 隱藏
        document.querySelector("#edit_modal .modal-header").classList.remove('add_mode_bgc');
        document.querySelector("#edit_modal .modal-header").classList.add('edit_mode_bgc');
        // step1.將原排程陣列逐筆繞出來
        Object(window[to_module]).forEach(function(row){          
            if(row['id'] == row_id){
                // step2.鋪畫面到module
                Object(window[to_module+'_item']).forEach(function(item_key){
                    if(item_key == 'id'){
                        document.querySelector('#'+to_module+'_delete_id').value = row['id'];       // 鋪上delete_id = this id.no for delete form
                        document.querySelector('#'+to_module+'_edit_id').value = row['id'];         // 鋪上edit_id = this id.no for edit form
                    }else if(item_key == 'flag'){
                        document.querySelector('#edit_modal #'+to_module+'_'+row[item_key]).checked = true;
                    }else{
                        document.querySelector('#edit_modal #'+to_module+'_'+item_key).value = row[item_key]; 
                    }
                })

                // 鋪上最後更新
                let to_module_info = '最後更新：'+row['updated_at']+' / by '+row['updated_user'];
                document.querySelector('#'+to_module+'_info').innerHTML = to_module_info;

                // step3-3.開啟 彈出畫面模組 for user編輯
                // edit_myTodo_btn.click();
                var add_btn = '<input type="submit" name="edit_formcase" value="儲存" class="btn btn-primary">';
                var del_btn = '<input type="submit" name="delete_formcase" value="刪除" class="btn btn-sm btn-xs btn-danger" onclick="return confirm(`確認刪除？`)">';
                $('#modal_action').append('編輯');          // model標題
                $('#modal_delect_btn').append(del_btn);     // 刪除鈕
                $('#modal_button').append(add_btn);         // 儲存鈕
                return;
            }
        })
    }

    // flag 切換上架/下架開關 20230712
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