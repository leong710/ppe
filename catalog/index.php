<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 調整flag ==> 20230712改用AJAX

    if(isset($_REQUEST["cate_no"])){
        $sort_cate_no = $_REQUEST["cate_no"];
    }else{
        $sort_cate_no = "All";
    }

    $sort_category = array(
        'cate_no' => $sort_cate_no
    );
    $catalogs = show_catalogs($sort_category);    // 器材
    $categories = show_categories();              // 分類
    $sum_categorys = show_sum_category();         // 統計分類與數量

    $per_total = count($catalogs);  //計算總筆數

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
    <!-- mloading JS 1/3 -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <!-- mloading CSS 2/3 -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <!-- mLoading_init.js 3/3 -->
    <script src="../../libs/jquery/mloading_init.js"></script>
    <style>

        /* PIC圖片初始設定 */
        .cover_btn {
            max-width: 250px;
            max-height: 150px;
            border-radius: 5px;
            /* 超出的部分隱藏 */
            overflow: hidden;
            /* PIC顯示圖片中心位置 */
            /* display: inline-block; */
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .cover_btn img {
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 100%;
            /* PIC圖片縮放動畫秒數*/
            transition: transform .6s ease-in-out;
        }
        /* PIC圖片指向時的放大倍率 */
        .cover_btn:hover img {
            transform: scale(1.5);
        }

    </style>
</head>
<!-- <div class="container"> -->
<div class="col-12">
    <div class="row justify-content-center">
        <div class="col-xl-12 col-12 p-4 rounded" style="background-color: rgba(255, 255, 255, .8);">
            <div class="row">
                <div class="col-md-4 py-0">
                    <h5>Catalog/目錄管理</h5>
                </div>
                <div class="col-md-4 py-0 text-end">
                    <?php if($_SESSION[$sys_id]["role"] <= 2 && $per_total != 0){ ?>
                        <!-- 下載EXCEL的觸發 -->
                        <form id="cata_myForm" method="post" action="../_Format/download_excel.php">
                            <input type="hidden" name="htmlTable" id="cata_htmlTable" value="">
                            <button type="submit" name="submit" class="btn btn-success" value="cata" onclick="submitDownloadExcel(this.value)" >
                                <i class="fa fa-download" aria-hidden="true"></i> 下載</button>
                        </form>
                    <?php } ?>
                </div>
                <!-- 表頭按鈕 -->
                <div class="col-md-4 py-0 text-end">
                    <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                        <a href="create.php" title="新增catalog" class="btn btn-primary"> <i class="fa fa-plus"></i> 新增品項</a>
                        <a href="category.php" title="編輯category" class="btn btn-warning"> <i class="fa fa-wrench"></i> 編輯分類</a>
                    <?php } ?>
                </div>
            </div>
                
            <!-- NAV分頁標籤與統計 -->
            <div class="col-12 pb-0 px-0">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $sort_cate_no == 'All' ? 'active':'';?>" href="?cate_no=All">
                            All&nbsp<span class="badge bg-secondary"><?php echo $per_total;?></span></a>
                    </li>
                    <?php foreach($sum_categorys as $sum_cate){ ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $sort_cate_no == $sum_cate["cate_no"] ? 'active':'';?>" href="?cate_no=<?php echo $sum_cate["cate_no"];?>">
                                <?php echo $sum_cate["cate_no"].".".$sum_cate["cate_title"];?>
                                <span class="badge bg-secondary"><?php echo $sum_cate["catalog_count"];?></span></a>
                        </li>
                    <?php  } ?>
                </ul>
            </div>
            <div class="col-12 bg-white">
                <!-- catalog名單 -->
                <table id="catalog_list" class="catalog_list table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>PIC</th>
                            <th>名稱</th>
                            <th>分類</th>
                            <th>尺寸</th>
                            <th style="width: 20%;">其他說明&nbsp<i class="fa fa-info-circle" aria-hidden="true"></i></th>
                            <th style="width: 30%;">SPEC/規格</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($catalogs as $catalog){ ?>
                            <tr>
                                <td><img src="../catalog/images/<?php echo $catalog["PIC"];?>" class="img-thumbnail"></td>
                                <td style="text-align: left;">
                                    <div class="row">
                                        <div class="col-12 py-0">
                                            <a href="repo.php?sn=<?php echo $catalog["SN"];?>" title="品名"><h5><?php echo $catalog["pname"];?></h5></a>
                                            <?php echo $catalog["SN"] ? 'SN：'.$catalog["SN"]:'';
                                                  echo $catalog["cata_remark"] ? '</br>( 敘述：'.$catalog["cata_remark"].' )':'</br>';?>
                                        </div>
                                        <div class="col-12 py-0 text-center">
                                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                                <button type="button" name="catalog" id="<?php echo $catalog['id'];?>" class="btn btn-sm btn-xs flagBtn <?php echo $catalog['flag'] == 'On' ? 'btn-success':'btn-warning';?>" value="<?php echo $catalog['flag'];?>"><?php echo $catalog['flag'];?></button>
                                                <a href="edit.php?sn=<?php echo $catalog["SN"];?>&img=<?php echo $catalog["PIC"];?>&cate_no=<?php echo $sort_cate_no;?>" class="btn btn-sm btn-xs btn-info" title="最後編輯：<?php echo $catalog["updated_at"]." / by: ".$catalog["updated_user"];?>">編輯</a>
                                            <?php } ?>
                                        </div>
                                    </div>
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
                                        echo $catalog["size"] ? "尺寸：".$catalog["size"]."</br>":"";
                                        echo $catalog["part_no"] ? "</br>料號：".$catalog["part_no"]:""; ?>
                                </td>
                                <td style="text-align: left;"><?php 
                                        echo "品牌/製造商：".$catalog["OBM"]."</br>型號：".$catalog["model"]; 
                                        echo "</br>單位：".$catalog["unit"];
                                        echo $catalog["scomp_no"] ? "</br>供應商：".$catalog["scomp_no"]:""; 
                                    ?>
                                </td>
                                <td style="text-align: left; vertical-align:top; word-break: break-all;">
                                    <textarea style="height: 90px; resize:none;" placeholder="規格" disabled ><?php echo $catalog["SPEC"];?></textarea>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
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
<!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
<script src="../../libs/sweetalert/sweetalert.min.js"></script>

<script>

    // catalog 切換上架/下架開關 20230712
    let flagBtns = [...document.querySelectorAll('.flagBtn')];
    for(let flagBtn of flagBtns){
        flagBtn.onclick = e => {
            let swal_content = e.target.name+'_id:'+e.target.id+'=';
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

    // 20231128_下載Excel
    var cata      = <?=json_encode($catalogs);?>;                                                   // 引入catalogs資料
    function submitDownloadExcel(to_module) {
        // 定義要抓的key=>value
        if(to_module == "cata"){
            var item_keys = {
                "cate_no"       : "分類編號",
                "SN"            : "器材編號", 
                "pname"         : "器材名稱", 
                "PIC"           : "器材照片", 
                "cata_remark"   : "敘述",
                "OBM"           : "品牌/製造商", 
                "model"         : "型號", 
                "size"          : "尺寸範圍", 
                "unit"          : "單位", 
                "SPEC"          : "規格", 
                "part_no"       : "料號", 
                "scomp_no"      : "供應商", 
                "buy_a"         : "安量倍數A", 
                "buy_b"         : "安量倍數B", 
                "flag"          : "開關",
                "updated_at"    : "最後更新",
                "created_at"    : "建檔日期", 
                "updated_user"  : "最後編輯"
            };
        }else{
            var item_keys = {};
        }

        var sort_listData = [];         // 建立整理陣列
        for(var i=0; i < window[to_module].length; i++){
            sort_listData[i] = {};      // 建立物件
            Object.keys(item_keys).forEach(function(i_key){
                sort_listData[i][item_keys[i_key]] = window[to_module][i][i_key];
            })
        }
        var htmlTableValue = JSON.stringify(sort_listData);
        document.getElementById(to_module+'_htmlTable').value = htmlTableValue;
    }

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
    })

</script>

<?php include("../template/footer.php"); ?>