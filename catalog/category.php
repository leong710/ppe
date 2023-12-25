<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);
    // accessDeniedAdmin();

    // CRUD
        if(isset($_POST["submit"])){                // 新增
            store_category($_REQUEST); }
        if(isset($_POST["edit_cate_submit"])){      // 更新
            update_category($_REQUEST); }
        if(isset($_POST["delete_cate"])){           // 刪除
            delete_category($_REQUEST); }
        // 調整flag ==> 20230712改用AJAX

    $categories = show_categories();
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
                        <h5>Category 分類列表 - 共 <?php echo count($categories);?> 筆</h5>
                    </div>
                </div>
                <div class="col-md-6 pb-0 text-end">
                    <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                        <button type="button" id="add_cate_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_cate" onclick="add_module('cate')" > <i class="fa fa-plus"></i> 新增分類</button>
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
                            <th>cate_no</th>
                            <th>cate_title</th>
                            <th>cate_remark</th>
                            <th>flag</th>
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                <th>action</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories as $cate){ ?>
                            <tr>
                                <td><?php echo $cate["id"];?></td>
                                <td><?php echo $cate["cate_no"];?></td>
                                <td><?php echo $cate["cate_title"];?></td>
                                <td><?php echo $cate["cate_remark"];?></td>
                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                        <button type="button" name="cate" id="<?php echo $cate['id'];?>" class="btn btn-sm btn-xs flagBtn <?php echo $cate['flag'] == 'On' ? 'btn-success':'btn-warning';?>" value="<?php echo $cate['flag'];?>"><?php echo $cate['flag'];?></button>
                                    <?php }else{ ?>
                                        <span class="btn btn-sm btn-xs <?php echo $cate['flag'] == 'On' ? 'btn-success':'btn-warning';?>">
                                            <?php echo $cate['flag'] == 'On' ? '顯示':'隱藏';?>
                                        </span>
                                    <?php } ?></td>
                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>    
                                    <button type="button" id="edit_cate_btn" value="<?php echo $cate['id'];?>" class="btn btn-sm btn-xs btn-info" 
                                        data-bs-toggle="modal" data-bs-target="#edit_cate" onclick="edit_module('cate',this.value)" >編輯</button>
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
<div class="modal fade" id="edit_cate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" aria-modal="true" role="dialog" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span id="modal_action"></span>分類</h5>

                <form action="" method="post">
                    <input type="hidden" name="id" id="cate_delete_id">
                    <?php if($_SESSION[$sys_id]["role"] == 0){ ?>
                        &nbsp&nbsp&nbsp&nbsp&nbsp
                        <span id="modal_delect_btn"></span>
                    <?php } ?>
                </form>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="" method="post">
                <div class="modal-body px-5">
                    <div class="row">
                        <div class="col-12 py-1">
                            <div class="form-floating">
                                <input type="text" name="cate_no" id="edit_cate_no" class="form-control" required placeholder="分類代號">
                                <label for="edit_cate_no" class="form-label">cate_no/分類代號：</label>
                            </div>
                        </div>
                        <div class="col-12 py-1">
                            <div class="form-floating">
                                <input type="text" name="cate_title" id="edit_cate_title" class="form-control" required placeholder="分類名稱">
                                <label for="edit_cate_title" class="form-label">cate_title/分類名稱：</label>
                            </div>
                        </div>
                        <div class="col-12 py-1">
                            <div class="form-floating">
                                <input type="text" name="cate_remark" id="edit_cate_remark" class="form-control" required placeholder="備註說明">
                                <label for="edit_cate_remark" class="form-label">cate_remark/備註說明：</label>
                            </div>
                        </div>
                        <div class="col-12 py-1">
                            <table>
                                <tr>
                                    <td style="text-align: right;">
                                        <label for="flag" class="form-label">flag/顯示開關：</label>
                                    </td>
                                    <td style="text-align: left;">
                                        <input type="radio" name="flag" value="On" id="edit_cate_On" class="form-check-input">&nbsp
                                        <label for="edit_cate_On" class="form-check-label">On</label>
                                    </td>
                                    <td style="text-align: left;">
                                        <input type="radio" name="flag" value="Off" id="edit_cate_Off" class="form-check-input">&nbsp
                                        <label for="edit_cate_Off" class="form-check-label">Off</label>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- 最後編輯資訊 -->
                        <div class="col-12 text-end p-0" id="edit_cate_info"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="text-end">
                        <input type="hidden" name="id" id="cate_edit_id" >
                        <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                        <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
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

    var cate   = <?=json_encode($categories);?>;                                         // 引入cates資料
    var cate_item   = ['id','cate_no','cate_title','cate_remark','flag'];           // 交給其他功能帶入 delete_cate_id

    function add_module(to_module){     // 啟用新增模式
        $('#modal_action, #modal_button, #modal_delect_btn, #edit_'+to_module+'_info').empty();   // 清除model功能
        $('#reset_btn').click();                                                        // reset清除表單
        var add_btn = '<input type="submit" name="cate_submit" class="btn btn-primary" value="新增">';
        $('#modal_action').append('新增');                      // model標題
        $('#modal_button').append(add_btn);                     // 儲存鈕
        var reset_btn = document.getElementById('reset_btn');   // 指定清除按鈕
        reset_btn.classList.remove('unblock');                  // 新增模式 = 解除
        document.querySelector("#edit_"+to_module+" .modal-header").classList.remove('edit_mode_bgc');
        document.querySelector("#edit_"+to_module+" .modal-header").classList.add('add_mode_bgc');
    }
    // fun-1.鋪編輯畫面
    function edit_module(to_module, row_id){
        $('#modal_action, #modal_button, #modal_delect_btn, #edit_'+to_module+'_info').empty();   // 清除model功能
        $('#reset_btn').click();                                                        // reset清除表單
        var reset_btn = document.getElementById('reset_btn');   // 指定清除按鈕
        reset_btn.classList.add('unblock');                     // 編輯模式 = 隱藏
        document.querySelector("#edit_"+to_module+" .modal-header").classList.remove('add_mode_bgc');
        document.querySelector("#edit_"+to_module+" .modal-header").classList.add('edit_mode_bgc');
        // remark: to_module = 來源與目的 site、fab、local
        // step1.將原排程陣列逐筆繞出來
        Object(window[to_module]).forEach(function(row){          
            if(row['id'] == row_id){
                // step2.鋪畫面到module
                Object(window[to_module+'_item']).forEach(function(item_key){
                    if(item_key == 'id'){
                        document.querySelector('#'+to_module+'_delete_id').value = row['id'];       // 鋪上delete_id = this id.no for delete form
                        document.querySelector('#'+to_module+'_edit_id').value = row['id'];         // 鋪上edit_id = this id.no for edit form
                    }else if(item_key == 'flag'){
                        document.querySelector('#edit_'+to_module+' #edit_'+to_module+'_'+row[item_key]).checked = true;
                    }else{
                        document.querySelector('#edit_'+to_module+' #edit_'+item_key).value = row[item_key]; 
                    }
                })

                // 鋪上最後更新
                let to_module_info = '最後更新：'+row['updated_at']+' / by '+row['updated_user'];
                document.querySelector('#edit_'+to_module+'_info').innerHTML = to_module_info;

                // step3-3.開啟 彈出畫面模組 for user編輯
                // edit_myTodo_btn.click();
                var add_btn = '<input type="submit" name="edit_cate_submit" class="btn btn-primary" value="儲存">';
                var del_btn = '<input type="submit" name="delete_cate" value="刪除cate" class="btn btn-sm btn-xs btn-danger" onclick="return confirm(`確認刪除？`)">';
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