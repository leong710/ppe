<?php
    require_once("../pdo.php");
    require_once("function.php");
    accessDenied();

    if(isset($_GET["local_id"])){
        $select_local = select_local($_REQUEST);
        if(empty($select_local)){              // 查無資料時返回指定頁面
            header("location:../");     // 用這個，因為跳太快
        }
        $buy_ty = $select_local["buy_ty"];
        $low_level = json_decode($select_local["low_level"]);
        if(is_object($low_level)) { $low_level = (array)$low_level; } 
        $catalogs = show_catalogs();
    }else{
        $select_local = array(
            'id' => '0',
            'site_id' => '0'
        );
        $low_level = [];
        $catalogs = [];
    }
    // create時用自己區域 // 將自己的site_id設為初始值 

    $allLocals = show_allLocal();

    $i = 0;
    $j = 0;
?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<html lang="zh-TW">

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
                <div class="px-2">
                    <!-- 第一列 儲存區/衛材名稱 -->
                    <div class="col-12 rounded py-1" style="background-color: #D3FF93;">
                        <div class="row" >
                            <div class="col-12 col-md-6 py-1">
                                <form action="" method="get">
                                    <div class="form-floating">
                                        <select name="local_id" id="local_id" class="form-control" required onchange="this.form.submit()">
                                            <option value="" selected hidden>--請選擇儲存點--</option>
                                            <?php foreach($allLocals as $local){ ?>
                                                <?php if($_SESSION["AUTH"]["role"] == 0 || $local["site_id"] == $_SESSION["AUTH"]["site_id"] || (in_array($local["site_id"], $_SESSION["AUTH"]["ssite_id"]))){ ?>  
                                                    <option value="<?php echo $local["id"];?>" <?php echo $local["id"] == $select_local["id"] ? "selected":""; ?> >
                                                        <?php echo $local["id"].":".$local["fab_title"]."&nbsp".$local["site_title"]."_".$local["local_title"]; echo ($local["flag"] == "Off") ? " - (已關閉)":"";?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <label for="local_id" class="form-label">local/儲存位置：<sup class="text-danger">*</sup></label>
                                    </div>
                                </form>

                            </div>

                            <div class="col-12 col-md-6 py-1">
            <form action="store.php" method="post" onsubmit="this.site_id.disabled=false,this.standard_lv.disabled=false">

                                <div class="form-floating">
                                    <select name="catalog_id" id="catalog_id" class="form-control" required onchange="update_standard_lv(this.value)">
                                        <option value="" selected hidden>--請選擇衛材--</option>
                                        <?php foreach($catalogs as $catalog){ ?>
                                            <option value="<?php echo $catalog["id"];?>" title="<?php echo $catalog["remark"];?>"><?php echo $catalog["id"];?>: <?php echo $catalog["title"];?></option>
                                        <?php } ?>
                                    </select>
                                    <label for="catalog_id" class="form-label">catalog/衛材名稱：<sup class="text-danger">*</sup></label>
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
                                        <?php echo $_SESSION["AUTH"]["role"] >= 1 ? "readonly":""; ?> >
                                    <label for="standard_lv" class="form-label">standard_lv/安全存量：<sup class="text-danger"><?php echo ($_SESSION["AUTH"]["role"] >= 1) ? " - disabled":" *";?></sup></label>
                                </div>
                            </div>
                            <!-- 右側-批號 -->
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating pb-0">
                                    <input type="date" name="lot_num" id="lot_num" class="form-control" required>
                                    <label for="lot_num" class="form-label">lot_num/批號/期限：<sup class="text-danger">*</sup></label>
                                </div>
                                <div class="col-12 pt-0 text-end">
                                    <button type="button" class="btn btn-sm btn-xs btn-warning text-dark" onclick="forever()">永久</button>
                                    <button type="button" class="btn btn-sm btn-xs btn-secondary" onclick="clear_lot_num()">清除</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating">
                                    <input type="number" name="amount" id="amount" class="form-control t-center" required placeholder="正常數量" min="0" max="999">
                                    <label for="amount" class="form-label">amount/現場存量：<sup class="text-danger">*</sup></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating">
                                    <input type="text" name="po_num" id="po_num" class="form-control" placeholder="PO編號">
                                    <label for="po_num" class="form-label">po_num/PO編號：</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating">
                                    <textarea name="remark" id="remark" class="form-control" style="height: 100px"></textarea>
                                    <label for="remark" class="form-label">remark/備註說明：</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating">
                                    <textarea name="d_remark" id="d_remark" class="form-control" style="height: 100px"></textarea>
                                    <label for="d_remark" class="form-label">d_remark/其他說明：</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-end">
                    <input type="hidden" value="<?php echo $_SESSION["AUTH"]["id"];?>" name="user_id">
                    <input type="hidden" value="<?php echo $select_local["id"];?>" name="local_id">
                    <input type="hidden" value="<?php echo $select_local["site_id"];?>" name="site_id">
                    <input type="submit" value="儲存" name="submit" class="btn btn-primary">
                    <input type="button" value="取消" class="btn btn-danger" onclick="history.back()">
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" referrerpolicy="no-referrer"></script>
<script>
    // console.log(low_level);
    
    function update_standard_lv(catalog_id){
        var low_level = JSON.parse('<?=json_encode($low_level);?>');
        var standard_lv = document.getElementById('standard_lv')             // 定義standard_lv主體
        standard_lv.value = low_level[catalog_id];
    }
    // 20230131 新增保存日期為'永久'
    function forever(){
        var lot_num = document.getElementById('lot_num');
        lot_num.value = '9999-12-31';
    }
    // 20230131 新增日期清除鈕
    function clear_lot_num(){
        var lot_num = document.getElementById('lot_num');
        lot_num.value = '';
    }
    // $(function () {
    //     $('input[name="lot_num"]').blur(function(){
    //         console.log($(this).val());
    //     });
    // });
</script>
<?php include("../template/footer.php"); ?>