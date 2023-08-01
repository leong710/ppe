<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    if(isset($_REQUEST)){
        print_r($_REQUEST);
    }

    $allLocals = show_allLocal();                   // 所有儲存站點
    
    $catalogs = show_catalogs();                    // 器材=All
    $categories = show_categories();                // 分類
    $sum_categorys = show_sum_category();           // 統計分類與數量

?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <!-- Jquery -->
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
    <!-- dataTable參照 https://ithelp.ithome.com.tw/articles/10230169 -->
        <!-- data table CSS+JS -->
        <link rel="stylesheet" type="text/css" href="../../libs/dataTables/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="../../libs/dataTables/jquery.dataTables.js"></script>
        <!-- dataTable RWD JS -->
        <!-- <script src="../../libs/dataTables/jquery.dataTables.min.js" referrerpolicy="no-referrer"></script> -->
        <!-- <script src="../../libs/dataTables/dataTables.responsive.min.js" referrerpolicy="no-referrer"></script> -->
    <!-- mloading CSS -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
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
            max-width: 100px;
            /* max-height: 100px; */
            max-height: 100px;
        }
        /* 標籤增加陰影辨識度 */
        .badge {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        .add_btn {
            /* 將圖示的背景色設置為透明並添加陰影 */
            background-color: transparent; 
            text-shadow: 0px 0px 1px #fff;
            color: blue;
            /* 將圖示的背景色設置為按鈕的背景色 */
            /* background-color: inherit; */
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
        tr > th {
            color: blue;
            text-align: center;
            vertical-align: top; 
            word-break: break-all; 
            background-color: white;
            font-size: 16px;
        }
        tr > td {
            vertical-align: middle; 
        }
    </style>
</head>

<div class="col-12">
    <div class="row justify-content-center">
        <div class="col-11 border rounded px-3 py-4" style="background-color: #D4D4D4;">
            <!-- 表頭1 -->
            <div class="row px-2">
                <div class="col-12 col-md-6 py-0">
                    <h3><b>領用申請</b></h3>
                </div>
                <div class="col-12 col-md-6 py-0 text-end">
                    <a class="btn btn-success" href="index.php"><i class="fa fa-caret-up" aria-hidden="true"></i> 回總表</a>
                </div>
            </div>
            <div class="row px-2">
                <div class="col-12 col-md-6">
                    需求單號：(尚未給號)</br>
                    開單日期：<?php echo date('Y-m-d H:i'); ?> (實際以送出時間為主)</br>
                    填單人員：<?php echo $_SESSION["AUTH"]["emp_id"]." / ".$_SESSION["AUTH"]["cname"];?>
                </div>
                <div class="col-12 col-md-6">

                </div>
            </div>

            <!-- container -->
            <div class="col-12 p-0">
                <!-- 分頁標籤 -->
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">1.選取器材用品</button>
                        <button class="nav-link" id="nav-shopping_cart-tab" data-bs-toggle="tab" data-bs-target="#nav-shopping_cart" type="button" role="tab" aria-controls="nav-shopping_cart" aria-selected="false">2.購物車</button>
                        <button class="nav-link" id="nav-review-tab" data-bs-toggle="tab" data-bs-target="#nav-review" type="button" role="tab" aria-controls="nav-review" aria-selected="false">3.申請單成立</button>
                        <button class="unblock" id="edit_myTodo_btn" data-bs-toggle="modal" data-bs-target="#edit_myTodo" >編輯</button>
                    </div>
                </nav>
                <form action="#" method="post">
                    <!-- 內頁 -->
                    <div class="tab-content rounded bg-light" id="nav-tabContent">
                        <!-- 1.商品目錄 -->
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                            <div class="col-12 px-4">
                                <table id="catalog_list" class="catalog_list table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="unblock">cate_no</th>
                                            <th>PIC</th>
                                            <th>SN</th>
                                            <th style="width: 30%;">名稱&nbsp<i class="fa fa-info-circle" aria-hidden="true"></i></th>
                                            <th>分類</th>
                                            <th>尺寸</th>
                                            <th>需求</th>
                                            <th><i class="fa-solid fa-cart-plus"></i>&nbsp購物車</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($catalogs as $catalog){ ?>
                                            <tr>
                                                <td class="unblock"><?php echo $catalog["cate_no"];?></td>
                                                <td><img src="../catalog/images/<?php echo $catalog["PIC"];?>" class="img-thumbnail"></td>
                                                <td>
                                                    <input type="checkbox" name="catalog_SN[<?php echo $catalog["SN"];?>]" id="catalog_SN_<?php echo $catalog["SN"];?>" class="img unblock" value="<?php echo $catalog["SN"];?>">
                                                    <label for="catalog_SN_<?php echo $catalog["SN"];?>"><?php echo $catalog["SN"];?></label>
                                                </td>
                                                <td style="text-align: left;">
                                                    <button type="button" id="cata_info_<?php echo $catalog['SN'];?>" value="<?php echo $catalog['SN'];?>" data-bs-toggle="modal" data-bs-target="#cata_info" 
                                                        class="cata_info_btn" onclick="info_module('catalog',this.value);"><h5><b><?php echo $catalog["pname"];?></b></h5></button>
                                                    <?php echo $catalog["cata_remark"] ? '</br>( 敘述：'.$catalog["cata_remark"].' )':'</br>';?>
                                                </td>
                                                <td><span class="badge rounded-pill <?php switch($catalog["cate_id"]){
                                                                            case "1": echo "bg-primary"; break;
                                                                            case "2": echo "bg-success"; break;
                                                                            case "3": echo "bg-warning text-dark"; break;
                                                                            case "4": echo "bg-danger"; break;
                                                                            case "5": echo "bg-info text-dark"; break;
                                                                            case "6": echo "bg-dark"; break;
                                                                            case "7": echo "bg-secondary"; break;
                                                                            default: echo "bg-light text-success"; break;
                                                                        }?>">
                                                            <?php echo $catalog["cate_no"].".".$catalog["cate_title"];?></span></td>
                                                <td style="text-align: left; word-break: break-all;">
                                                    <?php 
                                                        echo $catalog["size"] ? "尺寸：".$catalog["size"]:"--";
                                                        echo $catalog["unit"] ? "</br>單位：".$catalog["unit"]:"";
                                                        echo $catalog["OBM"] ? "</br>品牌/製造商：".$catalog["OBM"]:"";
                                                        echo $catalog["model"] ? "</br>型號：".$catalog["model"]:""; 
                                                    ?>
                                                </td>
                                                <td>
                                                    <input type="number" name="amount[<?php echo $catalog['SN'];?>]" id="<?php echo $catalog['SN'];?>" class="form-control amount t-center"
                                                        placeholder="數量" min="0" max="999" maxlength="3" oninput="if(value.length>3)value=value.slice(0,3)">
                                                </td>
                                                <td>
                                                    <button type="button" name="<?php echo $catalog['SN'];?>" id="add_<?php echo $catalog['SN'];?>" class="add_btn" value="" title="加入購物車" onclick="add_item(this.name,this.value);"><h5><i class="fa-regular fa-square-plus"></i></h5></button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 2.購物車 -->
                        <div class="tab-pane fade" id="nav-shopping_cart" role="tabpanel" aria-labelledby="nav-shopping_cart-tab">
                            <div class="col-12 px-4">
                                <label class="form-label">器材用品/數量單位：<sup class="text-danger"> *</sup></label>
                                <div class=" rounded border bg-light" id="shopping_cart">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>select</th>
                                                <th>SN</th>
                                                <th>品名</th>
                                                <th>數量</th>
                                                <th>單位</th>
                                            </tr>
                                        </thead>
                                        <tbody id="shopping_cart_tbody">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- 3.申請單成立 -->
                        <div class="tab-pane bg-white fade" id="nav-review" role="tabpanel" aria-labelledby="nav-review-tab">
                            <div class="col-12 py-4 px-5">
                                <!-- 表列0 說明 -->
                                <div class="row">
                                    <div class="col-12">
                                        請申請人填入相關資料：
                                    </div>
                                    <hr>
                                </div>

                                <!-- 表列1 申請單位 -->
                                <div class="row">
                                    <div class="col-6 col-md-4 py-1 px-2">
                                        <div class="form-floating">
                                            <input type="text" name="pname" id="pname" class="form-control" required placeholder="申請單位" >
                                            <label for="pname" class="form-label">申請單位：<sup class="text-danger"> *</sup></label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4 py-1 px-2">
                                        <div class="form-floating">
                                            <input type="text" name="pname" id="pname" class="form-control" required placeholder="部門名稱" >
                                            <label for="pname" class="form-label">部門名稱：<sup class="text-danger"> *</sup></label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4 py-1 px-2">
                                        <div class="form-floating">
                                            <input type="text" name="pname" id="pname" class="form-control" required placeholder="部門代號" >
                                            <label for="pname" class="form-label">部門代號：<sup class="text-danger"> *</sup></label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- 表列2 申請人 -->
                                <div class="row">
                                    <div class="col-6 col-md-4 py-1 px-2">
                                        <div class="form-floating">
                                            <input type="text" name="pname" id="pname" class="form-control" required placeholder="工號" value="<?php echo $_SESSION["AUTH"]["emp_id"];?>">
                                            <label for="pname" class="form-label">工號：<sup class="text-danger"> *</sup></label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4 py-1 px-2">
                                        <div class="form-floating">
                                            <input type="text" name="pname" id="pname" class="form-control" required placeholder="申請人姓名" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                            <label for="pname" class="form-label">申請人姓名：<sup class="text-danger"> *</sup></label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4 py-1 px-2">
                                        <div class="form-floating">
                                            <input type="text" name="pname" id="pname" class="form-control" required placeholder="分機" >
                                            <label for="pname" class="form-label">分機：<sup class="text-danger"> *</sup></label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- 表列3 領用站點 -->
                                <div class="row">
                                    <div class="col-12 col-md-8 py-3 px-2">
                                        <div class="form-floating">
                                            <select name="local_id" id="local_id" class="form-select" required>
                                                <option value="" hidden>-- [請選擇 領用站點] --</option>
                                                <?php foreach($allLocals as $allLocal){ ?>
                                                    <?php if($_SESSION[$sys_id]["role"] <= 1 || $allLocal["fab_id"] == $_SESSION[$sys_id]["fab_id"] || (in_array($allLocal["fab_id"], $_SESSION[$sys_id]["sfab_id"]))){ ?>  
                                                        <option value="<?php echo $allLocal["id"];?>" title="<?php echo $allLocal["fab_title"];?>" >
                                                            <?php echo $allLocal["id"]."：".$allLocal["site_title"]."&nbsp".$allLocal["fab_title"]."_".$allLocal["local_title"]; if($allLocal["flag"] == "Off"){ ?>(已關閉)<?php }?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                            <label for="local_id" class="form-label">領用站點：<sup class="text-danger"> *</sup></label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4 py-3 px-2">
                                        <div style="display: flex;">
                                            <label for="ppty" class="form-label">需求類別：</label></br>&nbsp
                                            <input type="radio" name="ppty" value="1" id="ppty_1" class="form-check-input" required checked>
                                            <label for="ppty_1" class="form-check-label">一般&nbsp&nbsp&nbsp</label>
                                            <input type="radio" name="ppty" value="3" id="ppty_3" class="form-check-input" required>
                                            <label for="ppty_3" class="form-check-label">緊急(事故須通報防災)</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- 表列5 說明 -->
                                <div class="row">
                                    <div class="col-12 px-2">
                                        <div class="form-floating">
                                            <textarea name="cata_remark" id="cata_remark" class="form-control" style="height: 90px;" placeholder="(由申請單位填寫用品/器材請領原由)"></textarea>
                                            <label for="cata_remark" class="form-label">用途說明：<sup class="text-danger"> * (由申請單位填寫用品/器材請領原由)</sup></label>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="col-12 py-1">
                                        備註：
                                        </br>&nbsp1.填入申請單位、部門名稱、申請日期、器材、數量及用途說明。
                                        </br>&nbsp2.簽核：申請人=>申請部門三級主管=>環安單位承辦人=>環安單位(課)主管=>發放人及領用人=>各廠環安單位存查3年。 
                                        </br>&nbsp3.需求類別若是[緊急]，必須說明事故原因，並通報防災中心。 
                                        </br>&nbsp4.以上若有填報不實，將於以退件。 
                                    </div>
                                    <input type="hidden" value="1" name="idty">
                                    <input type="hidden" value="<?php echo $_SESSION["AUTH"]["emp_id"];?>" name="in_user_id">
                                    <input type="hidden" value="<?php echo $_SESSION["AUTH"]["cname"];?>" name="cname">
                                    <input type="hidden" value="<?php echo $select_local["id"];?>" name="in_local">
                                </div>

                                <div class="row">
                                    <div class="col-6 col-md-6 py-1 px-2">
                                        
                                    </div>
                                    <div class="col-6 col-md-6 py-1 px-2 text-end">
                                        <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                                            <a href="#" target="_blank" title="Submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveSubmit"> <i class="fa fa-paper-plane" aria-hidden="true"></i> 送出</a>
                                        <?php } ?>
                                        <a class="btn btn-success" href="index.php"><i class="fa fa-caret-up" aria-hidden="true"></i> 回總表</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <hr>
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
                            <h5 class="modal-title">領用申請單：</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-5">
                            <label for="out_remark" class="form-check-label" >command：</label>
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
        </div>
    </div>
</div>

<!-- 彈出說明模組 cata_info -->
    <div class="modal fade" id="cata_info" tabindex="-1" aria-labelledby="cata_info" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">細項說明：</h5>
                    <button type="button" class="btn-close" aria-label="Close" data-bs-target="#catalog_modal" data-bs-toggle="modal"></button>
                </div>

                <div class="modal-body px-5">
                    <div class="row">
                        <div class="col-6 col-md-4" id="pic_append">

                        </div>
                        <div class="col-6 col-md-8" id="info_append">

                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">返回</button>
                </div>
            </div>
        </div>
    </div>

<!-- goTop滾動畫面DIV 2/4-->
    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
<!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>
<!-- mloading JS -->
<script src="../../libs/jquery/jquery.mloading.js"></script>
<!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
<script src="../../libs/sweetalert/sweetalert.min.js"></script>

<script>
    // mloading function
    function mloading(){
        $("body").mLoading({    // 開啟loading
            icon: "../../libs/jquery/Wedges-3s-120px.gif",
        }); 
    }

    mloading();

    // <!-- 有數量自動勾選，沒數量自動取消 -->
    // let amounts = [...document.querySelectorAll('.amount')];
    // for(let amount of amounts){
    //     amount.onchange = e => {
    //         let amount_id = e.target.id;
    //         if(amount.value == ''){
    //             document.getElementById('catalog_SN_'+ amount_id).checked=false;
    //             document.getElementById('add_'+ amount_id).value = '';
    //         } else {
    //             document.getElementById('catalog_SN_'+ amount_id).checked=true;
    //             document.getElementById('add_'+ amount_id).value = amount.value;
    //         }
    //     }
    // }
    // <!-- 有數量自動勾選，沒數量自動取消 -->
    let amounts = [...document.querySelectorAll('.amount')];
    for(let amount of amounts){
        amount.onchange = e => {
            let amount_id = e.target.id;
            if(amount.value == ''){
                // document.getElementById('catalog_SN_'+ amount_id).checked=false;
                document.getElementById('add_'+ amount_id).value = '';
            } else {
                // document.getElementById('catalog_SN_'+ amount_id).checked=true;
                document.getElementById('add_'+ amount_id).value = amount.value;
            }
        }
    }
    // setInterval(5000);

// // // info modal function
    var catalog = <?=json_encode($catalogs);?>;                        // 引入catalogs資料
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
        $('#info_append, #pic_append').empty();
        Object(window[to_module]).forEach(function(row){          
            if(row['SN'] === row_SN){
                // step2.鋪畫面到module
                Object.keys(window[to_module+'_item']).forEach(function(item_key){
                    if(item_key === 'cate_no'){
                        item_value = row['cate_no']+'.'+row['cate_title'];
                    }else{
                        item_value = row[item_key]; 
                    }
                    $('#info_append').append('<b>'+window[to_module+'_item'][item_key]+'：</b> '+item_value+'</br>');
                })
                $('#pic_append').append('<img src="../catalog/images/'+row['PIC']+'" style="width: 100%;" class="img-thumbnail">');     // 圖片
                return; // 假設每個<cata_SN>只會對應到一筆資料，找到後就可以結束迴圈了
            }
        })
    }
// // // catalog_modal 篩選 function
    // 目錄中分類篩選：All或各分類cate_no
    var catalog_list = document.querySelectorAll('.catalog_list > tbody > tr');
                function groupBy_cate_no(cate_no){
                    mloading();
                    if(cate_no==='All'){
                        catalog_list.forEach(function(row){
                            row.classList.remove('unblock');
                        })
                    }else{
                        catalog_list.forEach(function(row){
                            if(row.children[0].innerText === cate_no){
                                row.classList.remove('unblock');
                            }else{
                                row.classList.add('unblock');
                            }
                        })  
                    }
                    $("body").mLoading("hide");
                }

                // function check_select_item(){
                //     var select_items = [...document.querySelectorAll('.select_item')];
                //     for(var select_item of select_items){
                //         select_item.onchange = e => {
                //             var select_item_id = e.target.id;
                //             if(select_item.checked=false){
                //                 check_item(select_item_id);    // call function 查找已存在的項目，並予以清除。
                //             }
                //         }
                //     }
                // }

    // 加入清單
    function add_item(cata_SN, add_amount){
        mloading();

        if(add_amount <= 0 ){
            alert(cata_SN+' 沒有填數量!');

        }else{
            check_item(cata_SN);    // call function 查找已存在的項目，並予以清除。
            Object(catalog).forEach(function(cata){          
                if(cata['SN'] === cata_SN){
                    var input_cb = '<input type="checkbox" name="catalog_SN['+cata['SN']+']" id="catalog_SN_'+cata['SN']+'" class="select_item" value="'+cata['SN']+'" checked  oninput="if(checked=false)check_item(this.value)">';
                    var add_cata_item = '<tr id="item_'+cata['SN']+'"><td>'+input_cb+'</td><td>'+cata['SN']+'</td><td>'+cata['pname']+'</td><td>'+add_amount+'</td><td>'+cata['unit']+'</td></tr>';
                    $('#shopping_cart_tbody').append(add_cata_item);
                    return;         // 假設每個<cata_SN>只會對應到一筆資料，找到後就可以結束迴圈了
                }
            })
            
        }
        // check_select_item();
        $("body").mLoading("hide");
    }
    // 查找已存在的項目，並予以清除。
    function check_item(cata_SN) {
        var shopping_cart_list = document.querySelectorAll('#shopping_cart_tbody > tr');
        if (shopping_cart_list.length > 0) {
            // 使用for迴圈遍歷NodeList，而不是Object.keys()
            for (var i = 0; i < shopping_cart_list.length; i++) {
                var trElement = shopping_cart_list[i];
                if (trElement.id === 'item_' + cata_SN) {
                    // 從父節點中移除指定的<tr>元素
                    trElement.parentNode.removeChild(trElement);
                    return; // 假設每個<cata_SN>只會對應到一筆資料，找到後就可以結束迴圈了
                }
            }
        }
    }

    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })

    $(document).ready(function () {
        
        // dataTable 2 https://ithelp.ithome.com.tw/articles/10272439
        $('#catalog_list').DataTable({
            "autoWidth": false,
            // 排序
            // "order": [[ 4, "asc" ]],
            // 顯示長度
            "pageLength": 25,
            // 中文化
            "language":{
                url: "../../libs/dataTables/dataTable_zh.json"
            }
        });

        $("body").mLoading("hide");

    })
</script>

<?php include("../template/footer.php"); ?>