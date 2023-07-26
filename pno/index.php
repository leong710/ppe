<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 切換指定NAV分頁
    if(isset($_REQUEST["activeTab"])){
        $activeTab = $_REQUEST["activeTab"];
    }else{
        $activeTab = "0";       // 0 = PNO
    }

    // 新增
    if(isset($_POST["pno_submit"])){
        store_pno($_REQUEST);
    }
    // 調整flag ==> 20230712改用AJAX
    // 更新
    if(isset($_POST["edit_pno_submit"])){
        update_pno($_REQUEST);
    }
    // 刪除
    if(isset($_POST["delete_pno"])){
        delete_pno($_REQUEST);
    }

    // // *** PNO篩選組合項目~~
        if(isset($_REQUEST["_year"])){
            $_year = $_REQUEST["_year"];
        }else{
            // $_year = date('Y');              // 今年
            $_year = "All";                     // 全部
        }
        $sort_PNO_year = array(
            '_year' => $_year
        );
    $pnos = show_pno($sort_PNO_year);
        

    $count_pno = count($pnos);

            // 新增料號時，需提供對應的器材選項
            $sort_category = array(
                'cate_no' => "all"                        // 預設全部器材類別
            );
            $catalogs = show_catalogs($sort_category);    // 讀取器材清單 by all
            // 取出PNO年份清單 => 供Part_NO料號頁面篩選
            $pno_years = show_PNO_GB_year();
    
    $thisYear = date('Y');                      // 取今年值 for 新增料號預設年度
    $url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"];


?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <style>
        .unblock{
            display: none;
        }
        .tab-content.active {
            /* display: block; */
            animation: fadeIn 1s;
        }
        .nav-tabs .nav-link.active {
            /* color: #FFFFFF; */
            background-color: #84C1FF;
        }
        .word_bk {
            text-align: left; 
            vertical-align: top; 
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_11 col-11 bg-light rounded my-0 p-3" >
                <!-- NAV title -->
                <div class="row">
                    <!-- 分頁標籤 -->
                    <div class="col-12">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link" id="nav-PNO-tab" data-bs-toggle="tab" data-bs-target="#nav-PNO_table" type="button" role="tab" aria-controls="nav-PNO" aria-selected="false">
                                    料號&nbsp<span class="badge bg-secondary"><?php echo $count_pno;?></span></button>
                            </div>
                        </nav>
                    </div>
                </div>
                <!-- 內頁 -->
                <!-- <div id="table"> -->
                <div class="tab-content" id="nav-tabContent">
                    
                    <!-- _PNO -->
                    <div id="nav-PNO_table" class="tab-pane fade" role="tabpanel" aria-labelledby="nav-PNO-tab">
                        <div class="row">
                            <div class="col-12 col-md-4 py-0">
                                <h3>Part_NO料號管理</h3>
                            </div>
                            <div class="col-12 col-md-4 py-0">
                                <form action="<?php echo $url;?>" method="post">
                                    <input type="hidden" name="activeTab" value="0">
                                    <div class="input-group">
                                        <span class="input-group-text">篩選</span>
                                        <select name="_year" id="groupBy_cate" class="form-select">
                                            <option value="All" selected >-- 年度 / All --</option>
                                            <?php foreach($pno_years as $pno_year){ ?>
                                                <option value="<?php echo $pno_year["_year"];?>" <?php if($pno_year["_year"] == $_year){ ?>selected<?php } ?>>
                                                    <?php echo $pno_year["_year"];?></option>
                                            <?php } ?>
                                        </select>
                                        <button type="submit" class="btn btn-outline-secondary">查詢</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-12 col-md-4 py-0 text-end">
                                <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                    <a href="#" target="_blank" title="新增Part_NO料號" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_pno"> <i class="fa fa-plus"></i> 新增Part_NO料號</a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="col-12 p-0">
                                <table>
                                    <thead>
                                        <tr class="">
                                            <th>ai</th>
                                            <th>_year</br>年度</th>
                                            <th>part_no</br>料號</th>
                                            <th>size</br>尺寸</th>
                                            <th>cate_no</br>器材分類</th>
                                            <th>cata_SN</br>器材編號(名稱)</th>
                                            <th>part_remark</br>註解說明</th>
                                            <th>flag</th>
                                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>    
                                                <th>action</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <!-- 這裡開始抓SQL裡的紀錄來這裡放上 -->
                                    <tbody>
                                        <?php foreach($pnos as $pno){ ?>
                                            <tr>
                                                <td style="font-size: 6px;"><?php echo $pno["id"]; ?></td>
                                                <td><?php echo $pno["_year"]; ?></td>
                                                <td style="text-align:left;"><?php echo $pno["part_no"];?></td>
                                                <td><?php echo $pno["size"]; ?></td>
                                                <td><?php echo $pno["cate_no"] ? $pno["cate_no"].".".$pno["cate_remark"]:""; ?></td>
                                                <td style="width: 25%" class="word_bk"><?php echo $pno["cata_SN"] ? $pno["cata_SN"]."-".$pno["pname"]:"-- 無 --";
                                                                                   echo $pno["model"] ? "</br>[".$pno["model"]."]" :""; 
                                                                                   echo ($pno["cata_flag"] == "Off") ? "<sup class='text-danger'>-已關閉</sup>":"";?></td>
                                                <td style="width: 25%" class="word_bk"><?php echo $pno["pno_remark"];?></td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                                        <button type="button" name="pno" id="<?php echo $pno['id'];?>" class="btn btn-sm btn-xs flagBtn <?php echo $pno['flag'] == 'On' ? 'btn-success':'btn-warning';?>" value="<?php echo $pno['flag'];?>"><?php echo $pno['flag'];?></button>
                                                    <?php }else{ ?>
                                                        <span class="btn btn-sm btn-xs <?php echo $pno["flag"] == "On" ? "btn-success":"btn-warning";?>">
                                                            <?php echo $pno["flag"] == "On" ? "On":"Off";?></span>
                                                    <?php } ?></td>
                                                <td><?php if($_SESSION[$sys_id]["role"] <= 1){ ?>    
                                                    <button type="button" id="edit_pno_btn" value="<?php echo $pno["id"];?>" class="btn btn-sm btn-xs btn-info" 
                                                        data-bs-toggle="modal" data-bs-target="#edit_pno" onclick="edit_module('pno',this.value)" >編輯</button>
                                                <?php } ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                </div>
            </div>
        </div>
    </div>

<!-- 彈出畫面模組 新增PNO料號 -->
    <div class="modal fade" id="add_pno" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">新增Part_NO料號</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body px-5">
                        <div class="row">

                            <div class="col-12 col-md-4">
                                <div class="form-floating">
                                    <input type="text" name="_year" id="_year" class="form-control" required placeholder="_year年度" value="<?php echo $thisYear;?>">
                                    <label for="_year" class="form-label">_year/年度：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-floating">
                                    <input type="text" name="part_no" id="part_no" class="form-control" required placeholder="part_no料號">
                                    <label for="part_no" class="form-label">part_no/料號：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-floating">
                                    <input type="text" name="size" id="size" class="form-control" required placeholder="size尺寸">
                                    <label for="size" class="form-label">size/尺寸：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <select name="cata_SN" id="cata_SN" class="form-select" required <?php echo ($_SESSION[$sys_id]["role"] > 1) ? "disabled":"";?>>
                                        <option value="" hidden>-- 請選擇對應品項 --</option>
                                        <?php foreach($catalogs as $cata){ ?>
                                            <option value="<?php echo $cata["SN"];?>" >
                                                    <!-- <php if($cata["flag"] == "Off"){ ?> hidden <php } ?>> -->
                                                <?php echo $cata["cate_no"].".".$cata["cate_remark"]."_".$cata["SN"]."_".$cata["pname"]; 
                                                      echo $cata["model"] ? " [".$cata["model"]."]" :"";
                                                      echo ($cata["flag"] == "Off") ? " -- 已關閉":"";?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <label for="cata_SN" class="form-label">SN/對應品項：<sup class="text-danger"><?php echo ($_SESSION[$sys_id]["role"] > 1) ? " - disabled":" *"; ?></sup></label>
                                </div>
                            </div>
   
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea name="pno_remark" id="pno_remark" class="form-control" style="height: 100px" placeholder="註解說明"></textarea>
                                    <label for="pno_remark" class="form-label">pno_remark/備註說明：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>

                            <div class="col-12">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="flag" class="form-label">flag/顯示開關：</label>
                                        </td>
                                        <td>
                                            <input type="radio" name="flag" value="On" id="pno_On" class="form-check-input" checked>&nbsp
                                            <label for="pno_On" class="form-check-label">On</label>
                                        </td>
                                        <td>
                                            <input type="radio" name="flag" value="Off" id="pno_Off" class="form-check-input">&nbsp
                                            <label for="pno_Off" class="form-check-label">Off</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="activeTab" value="0">
                            <input type="hidden" value="<?php echo $_SESSION["AUTH"]["cname"];?>" name="updated_user">
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>   
                                <input type="submit" value="新增" name="local_submit" class="btn btn-primary">
                            <?php } ?>
                            <input type="reset" value="清除" class="btn btn-info">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>

<!-- 彈出畫面模組 編輯PNO料號 -->
    <div class="modal fade" id="edit_pno" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">編輯Part_NO料號</h4>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="pno_delete_id">
                        <?php if($_SESSION[$sys_id]["role"] == 0){ ?>
                            &nbsp&nbsp&nbsp&nbsp&nbsp
                            <input type="submit" name="delete_pno" value="刪除pno料號" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">
                        <?php } ?>
                    </form>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body px-5">
                        <div class="row">

                            <div class="col-12 col-md-4">
                                <div class="form-floating">
                                    <input type="text" name="_year" id="edit__year" class="form-control" required placeholder="_year年度" value="">
                                    <label for="edit__year" class="form-label">_year/年度：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-floating">
                                    <input type="text" name="part_no" id="edit_part_no" class="form-control" required placeholder="part_no料號">
                                    <label for="edit_part_no" class="form-label">part_no/料號：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-floating">
                                    <input type="text" name="size" id="edit_size" class="form-control" placeholder="size尺寸">
                                    <label for="edit_size" class="form-label">size/尺寸：</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <select name="cata_SN" id="edit_cata_SN" class="form-select">
                                        <option value="" >-- 請選擇對應品項 --</option>
                                        <?php foreach($catalogs as $cata){ ?>
                                            <option value="<?php echo $cata["SN"];?>" >
                                                    <!-- <php if($cata["flag"] == "Off"){ ?> hidden <php } ?>> -->
                                                <?php echo $cata["cate_no"].".".$cata["cate_remark"]."_".$cata["SN"]."_".$cata["pname"]; 
                                                    echo $cata["model"] ? " [".$cata["model"]."]" :"";
                                                    echo ($cata["flag"] == "Off") ? " -- 已關閉":"";?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <label for="edit_cata_SN" class="form-label">cata_SN/對應品項：<sup class="text-danger"></label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea name="pno_remark" id="edit_pno_remark" class="form-control" style="height: 100px" placeholder="註解說明"></textarea>
                                    <label for="edit_pno_remark" class="form-label">pno_remark/備註說明：</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="flag" class="form-label">flag/顯示開關：</label>
                                        </td>
                                        <td>
                                            <input type="radio" name="flag" value="On" id="edit_pno_On" class="form-check-input">&nbsp
                                            <label for="edit_pno_On" class="form-check-label">On</label>
                                        </td>
                                        <td>
                                            <input type="radio" name="flag" value="Off" id="edit_pno_Off" class="form-check-input">&nbsp
                                            <label for="edit_pno_Off" class="form-check-label">Off</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <!-- 最後編輯資訊 -->
                            <div class="col-12 text-end p-0" id="edit_pno_info"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="activeTab" value="0">
                            <input type="hidden" name="id" id="pno_edit_id" >
                            <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>   
                                <input type="submit" name="edit_pno_submit" class="btn btn-primary" value="儲存">
                            <?php } ?>
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>

<!-- goTop滾動畫面DIV 2/4-->
    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
</body>

<!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>
<!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
<script src="../../libs/sweetalert/sweetalert.min.js"></script>

<script>
    function resetMain(){
        $("#result").removeClass("border rounded bg-white");
        $('#result_table').empty();
        document.querySelector('#key_word').value = '';
    }

    var pno        = <?=json_encode($pnos);?>;                                 // 引入pnos資料
    var pno_item        = ['id','_year','part_no','size','cata_SN','pno_remark','flag'];                         // 交給其他功能帶入 delete_pno_id

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

    // 切換上架/下架開關
    let flagBtns = [...document.querySelectorAll('.flagBtn')];
    for(let flagBtn of flagBtns){
        flagBtn.onclick = e => {
            let swal_content = e.target.name+'_id:'+e.target.id+'=';
            // console.log('e:',e.target.name,e.target.id,e.target.value);
            $.ajax({
                url:'api.php',
                method:'post',
                async: false,                                           // ajax取得數據包後，可以return的重要參數
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
                    // console.log(res_r_flag);
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

            // swal('套用人事資料' ,swal_content ,swal_action, {buttons: false, timer:2000}).then(()=>{location.href = url;});     // deley3秒，then自動跳轉畫面
            swal('change_flag' ,swal_content ,swal_action, {buttons: false, timer:1000});

        }
    }

    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })

    $(document).ready(function(){
        // 切換指定NAV分頁
            //设置要自动选中的选项卡的索引（从0开始）
            var activeTab = '<?=$activeTab;?>';
            //激活选项卡
            $('.nav-tabs button:eq(' + activeTab + ')').tab('show');
    });

</script>

<?php include("../template/footer.php"); ?>