<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    if(isset($_POST["local_id"])){
        $select_local = select_local($_REQUEST);
        $buy_ty = $select_local["buy_ty"];
        $catalogs = read_local_stock($_REQUEST);    // 後來改用這個讀取catalog清單外加該local的儲存量，作為需求首頁目錄
    }else{
        $select_local = array(
            'id' => ''
        );
        $catalogs = [];
    }
    $allLocals = show_allLocal();
?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <style>
        .cover label {
            display: inline-block;
            width: 150px;
            height: 100px;
            margin: 5px;
            cursor: pointer;
            border: 5px solid #fff;
        }
        .cover label img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
        .img:checked + label{
            border: 3px solid #f00;
        }
        .unblock{
            display: none;
            /* transition: 3s; */
        }        
        #catalog_list img {
            max-width: 100%;
            /* max-height: 100px; */
            max-height: 150px;
        }
        /* 標籤增加陰影辨識度 */
        .badge {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        /*眼睛*/
        #checkEye {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
        }
        .cata_info_btn {
            /* 將圖示的背景色設置為透明並添加陰影 */
            background-color: transparent; 
            text-shadow: 0px 0px 1px #fff;
            color: blue;
            /* 將圖示的背景色設置為按鈕的背景色 */
            /* background-color: inherit; */
        }
    </style>
</head>

<!-- <div class="container"> -->
<div class="col-12">
    <div class="row justify-content-center">
        <div class="col-11 border p-4 rounded my-2" style="background-color: #D4D4D4;">
            <!-- 表頭：衛材訊息 -->
            <div class="row">
                <div class="col-12 col-md-6 py-0">
                    <h4>填寫請購需求單</h4>
                </div>
                <div class="col-12 col-md-6 py-0 text-end">
                    <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                        <a href="#" target="_blank" title="Submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveSubmit"> <i class="fa fa-paper-plane" aria-hidden="true"></i> 送出</a>
                    <?php } ?>
                    <a class="btn btn-success" href="index.php"><i class="fa fa-caret-up" aria-hidden="true"></i> 回總表</a>
                </div>
            </div>

                <!-- 表頭 -->
                <div class="row px-3 py-0">
                    <!-- 表頭：左側 -->
                    <div class="col-12 col-md-6 pb-2">
                        <div>
                            需求單號：(尚未給號)</br>
                            開單日期：<?php echo date('Y-m-d H:i'); ?> (實際以送出時間為主)</br>
                            填單人員：<?php echo $_SESSION["AUTH"]["cname"];?>
                        </div>
                    </div>
                    
                    <div class="col-12 col-md-6 pb-2">
                        <!-- 表頭：右側上=選擇收貨廠區 -->
                        <!-- <form action="" method="post" onsubmit="this.$myLocal.disabled=false"> -->
                        <form action="" method="post">
                            <div style="display: flex;">
                                <label for="local_id" class="form-label">需求廠區：</label>
                                <select name="local_id" id="local_id" class="form-control" required style='width:80%;' onchange="this.form.submit()">
                                    <option value="" hidden>--請選擇 收貨 儲存點--</option>
                                    <?php foreach($allLocals as $allLocal){ ?>
                                        <?php if($_SESSION[$sys_id]["role"] <= 1 || $allLocal["fab_id"] == $_SESSION[$sys_id]["fab_id"] || (in_array($allLocal["fab_id"], $_SESSION[$sys_id]["sfab_id"]))){ ?>  
                                            <option value="<?php echo $allLocal["id"];?>" title="<?php echo $allLocal["fab_title"];?>" <?php echo $allLocal["id"] == $select_local["id"] ? "selected":""; ?>>
                                                <?php echo $allLocal["id"]."：".$allLocal["site_title"]."&nbsp".$allLocal["fab_title"]."_".$allLocal["local_title"]; if($allLocal["flag"] == "Off"){ ?>(已關閉)<?php }?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </form>

            <!-- 本次create表單form開始 -->
            <!-- <form action="store.php" method="post" onsubmit="this.site_id.disabled=false,this.standard_lv.disabled=false"> -->
            <form action="store.php" method="post">
                <input type="hidden" value="1" name="idty">
                <input type="hidden" value="<?php echo $_SESSION["AUTH"]["emp_id"];?>" name="in_user_id">
                <input type="hidden" value="<?php echo $_SESSION["AUTH"]["cname"];?>" name="cname">
                <input type="hidden" value="<?php echo $select_local["id"];?>" name="in_local">

                        <!-- 表頭：右側下=需求類別 -->
                        <div style="display: flex;">
                            <label for="ppty" class="form-label">需求類別：</label></br>

                            <input type="radio" name="ppty" value="0" id="ppty_0" class="form-check-input" required hidden>
                            <label for="ppty_0" class="form-check-label" hidden>&nbsp臨時&nbsp&nbsp</label>
                            
                            <input type="radio" name="ppty" value="1" id="ppty_1" class="form-check-input" required checked>
                            <label for="ppty_1" class="form-check-label">&nbsp定期&nbsp&nbsp</label>
                        </div>

                    </div>
                </div>
                <!-- <hr> -->
                <div class="col-12 py-0" id="catalog_list">
                    <div class="row">
                        <?php foreach($catalogs as $catalog){?>
                            <div class="col-4 col-md-3 p-1">
                                <div class="col-12 rounded border bg-white" style="text-align: left;vertical-align:top;word-break: break-all">
                                    <!-- cover-image -->
                                    <div class="" style="height: 160px; text-align:center;">
                                        <input type="checkbox" name="catalog_SN[<?php echo $catalog["SN"];?>]" id="catalog_SN_<?php echo $catalog["SN"];?>" class="img unblock" value="<?php echo $catalog["SN"];?>">
                                        <label for="catalog_SN_<?php echo $catalog["SN"];?>"><img src="../catalog/images/<?php echo $catalog["PIC"];?>" class="img-thumbnail"></label>
                                    </div>
                                    <!-- catalog info -->
                                    <div style="height: 100px;">
                                        <div class="row">
                                            <div class="col-12 col-md-5 py-0">
                                                <h5><span class="badge rounded-pill <?php switch($catalog["cate_id"]){
                                                    case "1": echo "bg-primary";            break;
                                                    case "2": echo "bg-success";            break;
                                                    case "3": echo "bg-warning text-dark";  break;
                                                    case "4": echo "bg-danger";             break;
                                                    case "5": echo "bg-info text-dark";     break;
                                                    case "6": echo "bg-dark";               break;
                                                    case "7": echo "bg-secondary";          break;
                                                    default: echo "bg-light text-success";  break;
                                                    }?>">
                                                <?php echo $catalog["cate_no"].".".$catalog["cate_title"];?></span></h5>
                                            </div>
                                            <div class="col-12 col-md-7 py-0" title="<?php echo 'id:'.$catalog['id'];?>">
                                                <?php echo "SN：".$catalog["SN"];?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 py-0" data-toggle="tooltip" data-placement="bottom" title="查看更多">
                                                <button type="button" id="cata_info_<?php echo $catalog['SN'];?>" value="<?php echo $catalog['SN'];?>" data-bs-toggle="modal" data-bs-target="#cata_info" 
                                                    class="cata_info_btn" onclick="info_module('catalog',this.value);"><b><?php echo $catalog["pname"];?></b></button>
                                            </div>
                                            <div class="col-12 py-0">
                                                <?php echo $catalog["size"] ? "size：".$catalog["size"]:"";?>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- issue amount -->
                                    <div class="col-12 text-center py-0 bg-light rounded " style="color:<?php echo ($catalog['amount'] <= $catalog['stock_stand']) ? "red":"blue";?>">
                                            <b><?php 
                                                echo "安全: "; echo (!empty($catalog["stock_stand"])) ? $catalog["stock_stand"]:"0";
                                                echo " / 存量: "; echo (!empty($catalog["amount"])) ? $catalog["amount"]:"0";
                                            ?></b>
                                    </div>
                                    <div class="px-1">
                                        <input type="number" name="amount[<?php echo $catalog['SN'];?>]" id="<?php echo $catalog['SN'];?>" class="form-control amount t-center" 
                                            placeholder="限購： <?php switch($buy_ty){
                                                    case 'a':echo $catalog['buy_a']; $buy_qty = $catalog['buy_a']; break;
                                                    case 'b':echo $catalog['buy_b']; $buy_qty = $catalog['buy_b']; break;
                                                    default :echo $catalog['buy_a']; $buy_qty = $catalog['buy_a']; break; }
                                                echo "&nbsp/&nbsp".$catalog["unit"];?>" min="1" max="<?php echo $buy_qty;?>" 
                                                maxlength="<?php echo strlen($buy_qty);?>" oninput="if(value.length><?php echo strlen($buy_qty);?>)value=value.slice(0,<?php echo strlen($buy_qty);?>)">
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <!-- 尾段：衛材訊息 -->
                <div class="col-12 mb-0">
                    <div style="font-size: 6px;" class="text-end">
                        text-end
                    </div>
                </div>
                <!-- 彈出畫面說明模組 saveSubmit-->
                <div class="modal fade" id="saveSubmit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">衛材需求單：</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body px-5">
                                <label for="out_remark" class="form-check-label" >需求備註說明：</label>
                                <textarea name="remark" id="remark" class="form-control" rows="5"></textarea>
                            </div>
                            <div class="modal-footer">
                                <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                                    <input type="submit" value="Submit" name="submit" class="btn btn-primary">
                                <?php } ?>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 彈出畫面說明模組 cata_info -->
<div class="modal fade" id="cata_info" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">細項說明：</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-5" id="info_append">

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- goTop滾動畫面DIV 2/4-->
    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
<!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>

<script>
    // <!-- 有數量自動勾選，沒數量自動取消 -->
    let amounts = [...document.querySelectorAll('.amount')];
    for(let amount of amounts){
        amount.onchange = e => {
            let amount_id = e.target.id;
            if(amount.value == ''){
                document.getElementById('catalog_SN_'+ amount_id).checked=false;
            } else {
                document.getElementById('catalog_SN_'+ amount_id).checked=true;
            }
        }
    }
    // setInterval(5000);

// // // info mode function
    var catalog = <?=json_encode($catalogs);?>;                        // 引入catalogs資料
    // var catalog_item = ['SN','cate_no','pname','cata_remark','OBM','model','size','unit','SPEC','scomp_no'];    // 交給其他功能帶入 delete_supp_id
    var catalog_item = {
            "SN"            : "SN/編號", 
            "cate_no"       : "category/分類", 
            "pname"         : "pname/品名", 
            "cata_remark"   : "cata_remark/敘述說明",
            "OBM"           : "OBM/品牌/製造商",
            "model"         : "model/型號",
            "size"          : "size/尺寸",
            "unit"          : "unit/單位",
            "SPEC"          : "SPEC/規格"
            // "scomp_no"      : "scomp_no/供應商"
        };    // 定義要抓的key=>value

    // fun-1.鋪info畫面
    function info_module(to_module, row_SN){
        // step1.將原排程陣列逐筆繞出來
        $('#info_append').empty();

        Object(window[to_module]).forEach(function(row){          
            if(row['SN'] == row_SN){
                // step2.鋪畫面到module
                Object.keys(window[to_module+'_item']).forEach(function(item_key){
                    if(item_key == 'cate_no'){
                        item_value = row['cate_no']+'.'+row['cate_title'];
                    }else{
                        item_value = row[item_key]; 
                    }

                    $('#info_append').append('<b>'+window[to_module+'_item'][item_key]+'：</b> '+item_value+'</br>');
                })

                // 鋪上最後更新
                // let to_module_info = '最後更新：'+row['updated_at']+' / by '+row['updated_user'];
                // document.querySelector('#edit_'+to_module+'_info').innerHTML = to_module_info;

            }
        })
    }

    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
</script>

<?php include("../template/footer.php"); ?>