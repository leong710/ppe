<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);
       
    $catalogs = show_catalogs();
    $allLocals = show_allLocal();
    
?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-8 bg-white border rounded p-4 my-2">
            <div class="row">
                <div class="col-12 col-md-6 py-0">
                    <h5>建立local單位衛材存量</h5>
                </div>
                <div class="col-12 col-md-6 py-0">

                </div>
            </div>
            <hr>
            <form action="store.php" method="post" onsubmit="this.site_id.disabled=false,this.standard_lv.disabled=false">
                <div class="px-2">
                    <!-- 第一列 儲存區/衛材名稱 -->
                    <div class="col-12 rounded py-1" style="background-color: #D3FF93;">
                        <div class="row" >
                            <div class="col-12 col-md-6 py-1">
                                <!-- select local 目的是要取得該local buy_ty & low_level -->
                                <!-- <form action="" method="get"> -->
                                    <div class="form-floating">
                                        <!-- <select name="local_id" id="local_id" class="form-control" required onchange="this.form.submit()"> -->
                                        <select name="local_id" id="local_id" class="form-control" required onchange="select_local(this.value)">
                                            <option value="" selected hidden>--請選擇儲存點--</option>
                                            <?php foreach($allLocals as $local){ ?>
                                                <?php if($_SESSION[$sys_id]["role"] == 0 || $local["fab_id"] == $_SESSION[$sys_id]["fab_id"] || (in_array($local["fab_id"], $_SESSION[$sys_id]["sFab_id"]))){ ?>  
                                                    <option value="<?php echo $local["id"];?>" >
                                                        <?php echo $local["id"]."：".$local["site_title"]."&nbsp".$local["fab_title"]."_".$local["local_title"]; echo ($local["flag"] == "Off") ? " - (已關閉)":"";?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <label for="local_id" class="form-label">local_id/儲存位置：<sup class="text-danger">*</sup></label>
                                    </div>
                                <!-- </form> -->
                            </div>

                            <div class="col-12 col-md-6 py-1">

                                <div class="form-floating">
                                    <select name="cata_id" id="cata_id" class="form-control" required onchange="update_standard_lv(this.value)">
                                    <!-- <select name="cata_id" id="cata_id" class="form-control" required > -->
                                        <option value="" selected hidden>--請選擇衛材--</option>
                                        <?php foreach($catalogs as $catalog){ ?>
                                            <option value="<?php echo $catalog["id"];?>" title="<?php echo $catalog["cata_remark"];?>"><?php echo $catalog["SN"]."：".$catalog["pname"];?></option>
                                        <?php } ?>
                                    </select>
                                    <label for="cata_id" class="form-label">cata_id/衛材名稱：<sup class="text-danger">*</sup></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 第二列 -->
                    <div class="col-12 rounded bg-light">
                        <div class="row">
                            <!-- 左側-數量 -->
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating">
                                    <input type="number" name="standard_lv" id="standard_lv" class="form-control t-center" placeholder="標準數量(管理員填)" min="1" max="400"
                                        <?php echo $_SESSION[$sys_id]["role"] >= 1 ? "readonly":""; ?> >
                                    <label for="standard_lv" class="form-label">standard_lv/安全存量：<sup class="text-danger"><?php echo ($_SESSION[$sys_id]["role"] >= 1) ? " - disabled":" *";?></sup></label>
                                </div>
                            </div>
                            <!-- 右側-批號 -->
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating">
                                    <input type="number" name="amount" id="amount" class="form-control t-center" required placeholder="正常數量" min="0" max="999">
                                    <label for="amount" class="form-label">amount/現場存量：<sup class="text-danger">*</sup></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text" name="po_no" id="po_no" class="form-control" placeholder="PO採購編號">
                                    <label for="po_no" class="form-label">po_no/PO採購編號：</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text" name="pno" id="pno" class="form-control" placeholder="料號">
                                    <label for="pno" class="form-label">pno/料號：</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <textarea name="stock_remark" id="stock_remark" class="form-control" style="height: 100px"></textarea>
                                    <label for="stock_remark" class="form-label">stock_remark/備註說明：</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating pb-0">
                                    <input type="date" name="lot_num" id="lot_num" class="form-control" required>
                                    <label for="lot_num" class="form-label">lot_num/批號/期限：<sup class="text-danger">*</sup></label>
                                </div>
                                <div class="col-12 pt-0 text-end">
                                    <button type="button" id="toggle_btn" class="btn btn-sm btn-xs btn-warning text-dark">永久</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-end">
                    <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                    <input type="hidden" name="fab_id" value="<?php echo $select_local["fab_id"];?>">
                    <input type="submit" name="submit" value="儲存" class="btn btn-primary">
                    <input type="reset" value="清除" class="btn btn-info">
                    <input type="button" value="取消" class="btn btn-danger" onclick="history.back()">
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Jquery -->
<script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
<script>
    
    var allLocals = <?=json_encode($allLocals);?>;                      // 引入所有local的allLocals值
    var low_level = [];                                                 // 宣告low_level變數

    // 選擇local時，取得該local的low_level
    function select_local(local_id){
        Object(allLocals).forEach(function(aLocal){
            if(aLocal['id'] == local_id){
                low_level = JSON.parse(aLocal['low_level']);            // 引入所選local的low_level值
            }
        })
        // 預防已經先選了器材，進行防錯
        var select_cata_id = document.getElementById('cata_id').value;  // 取得器材的選項值
        if(select_cata_id != null){                                     // 假如器材已經選擇了
            update_standard_lv(select_cata_id);                         // 就執行取得low_level對應值
        }
    }
    // 選擇器材，並更新low_level值
    function update_standard_lv(catalog_id){
        var standard_lv = document.getElementById('standard_lv');       // 定義standard_lv主體
        // var low_level = JSON.parse('<=json_encode($low_level);?>');    // 引入所local的low_level值
        if(low_level[catalog_id] == null){
            standard_lv.value = 0;                                      // 預防low_level對應值是null
        }else{
            standard_lv.value = low_level[catalog_id];                  // 套用對應cata_id的low_level值
        }
    }

    // 20230131 新增保存日期為'永久'    20230714 升級合併'永久'、'清除'
    $(function(){
        var toggle_btn = document.getElementById('toggle_btn');
        var lot_num = document.getElementById('lot_num');
        // 變更按鈕樣態
        function change_btn(){
            if (lot_num.value == '') {
                // 输入字段为空
                toggle_btn.innerText = '永久';
                toggle_btn.classList.remove('btn-secondary');
                toggle_btn.classList.add('btn-warning', 'text-dark');
            } else {
                // 输入字段有值
                toggle_btn.innerText = '清除';
                toggle_btn.classList.remove('btn-warning', 'text-dark');
                toggle_btn.classList.add('btn-secondary');
            }
        }
        // 監聽lot_num是否有輸入值，跟著改變樣態
        $('#lot_num').on('input', function() {
            change_btn();
        });
        // 永久按鈕點擊執行項，跟著改變樣態
        $('#toggle_btn').click(function(){
            if(lot_num.value =='') {
                lot_num.value = '9999-12-31';
            }else{
                lot_num.value = '';
            }
            change_btn();
        });
        
        // $('#local_id').select(function(){
            
        // });
    });

</script>

<?php include("../template/footer.php"); ?>