<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);
    // accessDeniedAdmin();

    // 新增
    if(isset($_POST["submit"])){
        store_category($_REQUEST);
    }
    
    // 調整flag ==> 20230712改用AJAX

    // 更新
    if(isset($_POST["edit_cate_submit"])){
        update_category($_REQUEST);
    }
    // 刪除
    if(isset($_POST["delete_cate"])){
        delete_category($_REQUEST);
    }

    $categories = show_categories();
?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<style>
    .box {
        box-sizing: border-box;  
        /* width: calc(100%/2);    總長除以等份，完全不用自己算 */
        text-align: center;
        /* display: inline-block; */
        display: inline-flex;
        /* background-color: #fffc4a; */
        /* font-size: 16px; */
        vertical-align: top;        /*div框向上對齊*/
        /* top, middle, bottom, baseline */
        margin: 0px;
        padding:0px;               /*div框的內距，為了不讓兩框文字相連*/
    }
    table,td,th {
        border: 0px solid #aaa;
        border-collapse: collapse;
        padding: 5px;
        position: relative;
        font-size: 18px;
        text-align: center;
        vertical-align: middle; 
    }
    table thead > tr > th{
        /* font-size: 14px; */
        /* background-color: rgb(122, 162, 238); */
        color: blue;
        text-align: center;
        vertical-align: top; 
        word-break: break-all; 
        background-color: white;
    }
    table .btn {
        /* padding:2px 2px; */
        margin: 5px;
    }
    a {
        text-decoration: none;
    }
    a:hover {
        /* font-size: 1.05rem; */
        font-weight:bold;
        text-shadow: 3px 3px 5px rgba(0,0,0,.5);
    }
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
                        <a href="#" target="_blank" title="for新增分類" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_cate"> <i class="fa fa-plus"></i> 新增分類</a>
                    <?php } ?>
                    <a href="index.php" title="回上層列表" class="btn btn-success"><i class="fa fa-external-link" aria-hidden="true"></i> 返回管理</a>
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

<!-- 彈出畫面模組 新增-->
<div class="modal fade" id="add_cate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">新增分類</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-5">
                    <div class="row">
                        <div class="col-12 py-1">
                            <div class="form-floating">
                                <input type="text" name="cate_no" id="cate_no" class="form-control" required placeholder="分類代號">
                                <label for="cate_no" class="form-label">cate_no/分類代號：</label>
                            </div>
                        </div>
                        <div class="col-12 py-1">
                            <div class="form-floating">
                                <input type="text" name="cate_title" id="cate_title" class="form-control" required placeholder="分類名稱">
                                <label for="cate_title" class="form-label">cate_title/分類名稱：</label>
                            </div>
                        </div>
                        <div class="col-12 py-1">
                            <div class="form-floating">
                                <input type="text" name="cate_remark" id="cate_remark" class="form-control" required placeholder="備註說明">
                                <label for="cate_remark" class="form-label">cate_remark/備註說明：</label>
                            </div>
                        </div>
                        <div class="col-12 py-1">
                            <table>
                                <tr>
                                    <td style="text-align: right;">
                                        <label for="flag" class="form-label">flag/顯示開關：</label>
                                    </td>
                                    <td style="text-align: left;">
                                        <input type="radio" name="flag" value="On" id="site_On" class="form-check-input" checked>&nbsp
                                        <label for="site_On" class="form-check-label">On</label>
                                    </td>
                                    <td style="text-align: left;">
                                        <input type="radio" name="flag" value="Off" id="site_Off" class="form-check-input">&nbsp
                                        <label for="site_Off" class="form-check-label">Off</label>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <div class="text-end">
                        <input type="hidden" value="<?php echo $_SESSION["AUTH"]["cname"];?>" name="updated_user">
                        <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                            <input type="submit" value="儲存" name="submit" class="btn btn-primary">
                        <?php } ?>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- 彈出畫面模組 編輯-->
<div class="modal fade" id="edit_cate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">編輯分類</h5>
                <form action="" method="post">
                    <input type="hidden" name="id" id="cate_delete_id">
                    <?php if($_SESSION[$sys_id]["role"] == 0){ ?>
                        &nbsp&nbsp&nbsp&nbsp&nbsp
                        <input type="submit" name="delete_cate" value="刪除cate" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">
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
                        <input type="hidden" value="<?php echo $_SESSION["AUTH"]["cname"];?>" name="updated_user">
                        <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                            <input type="submit" value="儲存" name="edit_cate_submit" class="btn btn-primary">
                        <?php } ?>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
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

    // fun-1.鋪編輯畫面
    function edit_module(to_module, row_id){
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