<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 新增
    // if(isset($_POST["submit"])){                        // 儲存新增
    //     store_catalog($_REQUEST);
    //     $_GET["img"] = "";
    // }
        
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

    // <!-- 20211215分頁工具 -->
        //分頁設定
        $per_total = count($catalogs);  //計算總筆數
        $per = 25;  //每頁筆數
        $pages = ceil($per_total/$per);  //計算總頁數;ceil(x)取>=x的整數,也就是小數無條件進1法
        if(!isset($_GET['page'])){  //!isset 判斷有沒有$_GET['page']這個變數
            $page = 1;	  
        }else{
            $page = $_GET['page'];
        }
        $start = ($page-1)*$per;  //每一頁開始的資料序號(資料庫序號是從0開始)

        $catalogs = page_div_catalogs($start, $per, $sort_category);
        // $rs = page_div($start,$per);
        $page_start = $start +1;  //選取頁的起始筆數
        $page_end = $start + $per;  //選取頁的最後筆數
        if($page_end>$per_total){  //最後頁的最後筆數=總筆數
            $page_end = $per_total;
        }
    // <!-- 20211215分頁工具 -->

?>
<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <!-- Jquery -->
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
    <!-- mloading JS -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <!-- mloading CSS -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <style>
        .unblock{
            display: none;
            /* transition: 3s; */
        }
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
        /* 標籤增加陰影辨識度 */
        .badge {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        /* 膠囊球增加陰影辨識度 */
        .rounded-pill {
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.5);
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
    <script>    
        // loading function
        function mloading(){
            $("body").mLoading({
                // icon: "../../libs/jquery/Wedges-3s-120px.gif",
                icon: "../../libs/jquery/loading.gif",
            }); 
        }
        mloading();    // 畫面載入時開啟loading
    </script>
</head>
<!-- <div class="container"> -->
<div class="col-12">
    <div class="row justify-content-center">
        <div class="col-xl-12 col-12 p-4 rounded" style="background-color: rgba(255, 255, 255, .9);">
            <div class="row">
                <div class="col-md-6 py-0">
                    <h5>Catalog/目錄管理</h5>
                </div>
                <!-- 表頭按鈕 -->
                <div class="col-md-6 py-0 text-end">
                    <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                        <a href="create.php" title="新增catalog" class="btn btn-primary"> <i class="fa fa-plus"></i> 新增品項</a>
                        <a href="category.php" title="編輯category" class="btn btn-warning"> <i class="fa fa-wrench"></i> 編輯分類</a>
                        <!-- <a href="..\trade\create.php" title="管理員限定" class="btn btn-warning"><i class="fa fa-upload" aria-hidden="true"></i> 批量撥補</a> -->
                        <!-- <a href="#" title="新增品項2" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#add_cata"> <i class="fa fa-search" aria-hidden="true"></i> 新增品項2</a> -->
                        <!-- <a href="#" target="_blank" title="查詢分類" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#query_category"> <i class="fa fa-search" aria-hidden="true"></i> 查詢分類</a> -->
                    <?php } ?>
                </div>
                
                <!-- NAV分頁標籤與統計 -->
                <div class="col-12 pb-0">
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
            </div>
            <div class="col-12 bg-white">
                <!-- 20211215分頁工具 -->  
                <div class="row">
                    <div class="col-12 col-md-6">	
                        <?php
                            //每頁顯示筆數明細
                            echo '顯示 '.$page_start.' 到 '.$page_end.' 筆 共 '.$per_total.' 筆，目前在第 '.$page.' 頁 共 '.$pages.' 頁'; 
                        ?>
                    </div>
                    <div class="col-12 col-md-6 text-end">
                        <?php
                            if($pages>1){  //總頁數>1才顯示分頁選單
    
                                //分頁頁碼；在第一頁時,該頁就不超連結,可連結就送出$_GET['page']
                                if($page=='1'){
                                    echo "首頁 ";
                                    echo "上一頁 ";
                                }else if(isset($sort_cate_no)){
                                    echo "<a href=?cate_no=".$sort_cate_no."&page=1>首頁 </a> ";
                                    echo "<a href=?cate_no=".$sort_cate_no."&page=".($page-1).">上一頁 </a> ";		
                                }else{
                                    echo "<a href=?page=1>首頁 </a> ";
                                    echo "<a href=?page=".($page-1).">上一頁 </a> ";		
                                }
    
                                //此分頁頁籤以左、右頁數來控制總顯示頁籤數，例如顯示5個分頁數且將當下分頁位於中間，則設2+1+2 即可。若要當下頁位於第1個，則設0+1+4。也就是總合就是要顯示分頁數。如要顯示10頁，則為 4+1+5 或 0+1+9，以此類推。	
                                for($i=1 ; $i<=$pages ;$i++){ 
                                    $lnum = 2;  //顯示左分頁數，直接修改就可增減顯示左頁數
                                    $rnum = 2;  //顯示右分頁數，直接修改就可增減顯示右頁數
    
                                    //判斷左(右)頁籤數是否足夠設定的分頁數，不夠就增加右(左)頁數，以保持總顯示分頁數目。
                                    if($page <= $lnum){
                                        $rnum = $rnum + ($lnum-$page+1);
                                    }
    
                                    if($page+$rnum > $pages){
                                        $lnum = $lnum + ($rnum - ($pages-$page));
                                    }
                                    //分頁部份處於該頁就不超連結,不是就連結送出$_GET['page']
                                    if($page-$lnum <= $i && $i <= $page+$rnum){
                                        if($i==$page){
                                            echo $i.' ';
                                        }else if(isset($sort_cate_no)){
                                            echo "<a href=?cate_no=".$sort_cate_no."&page=".$i.">".$i."</a> ";
                                        }else{
                                            echo '<a href=?page='.$i.'>'.$i.'</a> ';
                                        }
                                    }
                                }
                                //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                if($page==$pages){
                                    echo " 下一頁";
                                    echo " 末頁";
                                }else if(isset($sort_cate_no)){
                                    echo "<a href=?cate_no=".$sort_cate_no."&page=".($page+1)."> 下一頁</a>";
                                    echo "<a href=?cate_no=".$sort_cate_no."&page=".$pages."> 末頁</a>";		
                                }else{
                                    echo "<a href=?page=".($page+1)."> 下一頁</a>";
                                    echo "<a href=?page=".$pages."> 末頁</a>";		
                                }
                            }
                        ?>
                    </div>
                </div>
                
                <!-- catalog名單 -->
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>名稱</th>
                            <th>分類</th>
                            <th>尺寸</th>
                            <th style="width: 20%;">其他說明&nbsp<i class="fa fa-info-circle" aria-hidden="true"></i></th>
                            <th style="width: 30%;">SPEC/規格</th>
                            <!-- <th>限購數量</br>3千下/3千上</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($catalogs as $catalog){ ?>
                            <tr>
                                <td><?php echo $catalog["SN"];?></td>
                                <td style="text-align: left;">
                                    <div class="row">
                                        <div class="col-12 py-0">
                                            <a href="repo.php?sn=<?php echo $catalog["SN"];?>" title="品名"><h5><?php echo $catalog["pname"];?></h5></a>
                                            <?php echo $catalog["cata_remark"] ? '( 敘述：'.$catalog["cata_remark"].' )':'</br>';?>
                                        </div>
                                        <div class="col-12 py-0 text-center">
                                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                                <button type="button" name="catalog" id="<?php echo $catalog['id'];?>" class="btn btn-sm btn-xs flagBtn <?php echo $catalog['flag'] == 'On' ? 'btn-success':'btn-warning';?>" value="<?php echo $catalog['flag'];?>"><?php echo $catalog['flag'];?></button>
                                                <a href="edit.php?sn=<?php echo $catalog["SN"];?>&img=<?php echo $catalog["PIC"];?>" class="btn btn-sm btn-xs btn-info" title="最後編輯：<?php echo $catalog["updated_at"]." / by: ".$catalog["updated_user"];?>">編輯</a>
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
                <!-- 20211215分頁工具 -->  
                <div class="row">
                    <div class="col-12 col-md-6">	
                        <?php
                            //每頁顯示筆數明細
                            echo '顯示 '.$page_start.' 到 '.$page_end.' 筆 共 '.$per_total.' 筆，目前在第 '.$page.' 頁 共 '.$pages.' 頁'; 
                        ?>
                    </div>
                    <div class="col-12 col-md-6 text-end">
                        <?php
                            if($pages>1){  //總頁數>1才顯示分頁選單
    
                                //分頁頁碼；在第一頁時,該頁就不超連結,可連結就送出$_GET['page']
                                if($page=='1'){
                                    echo "首頁 ";
                                    echo "上一頁 ";
                                }else if(isset($sort_cate_no)){
                                    echo "<a href=?cate_no=".$sort_cate_no."&page=1>首頁 </a> ";
                                    echo "<a href=?cate_no=".$sort_cate_no."&page=".($page-1).">上一頁 </a> ";			
                                }else{
                                    echo "<a href=?page=1>首頁 </a> ";
                                    echo "<a href=?page=".($page-1).">上一頁 </a> ";		
                                }
    
                                //此分頁頁籤以左、右頁數來控制總顯示頁籤數，例如顯示5個分頁數且將當下分頁位於中間，則設2+1+2 即可。若要當下頁位於第1個，則設0+1+4。也就是總合就是要顯示分頁數。如要顯示10頁，則為 4+1+5 或 0+1+9，以此類推。	
                                for($i=1 ; $i<=$pages ;$i++){ 
                                    $lnum = 2;  //顯示左分頁數，直接修改就可增減顯示左頁數
                                    $rnum = 2;  //顯示右分頁數，直接修改就可增減顯示右頁數
    
                                    //判斷左(右)頁籤數是否足夠設定的分頁數，不夠就增加右(左)頁數，以保持總顯示分頁數目。
                                    if($page <= $lnum){
                                        $rnum = $rnum + ($lnum-$page+1);
                                    }
    
                                    if($page+$rnum > $pages){
                                        $lnum = $lnum + ($rnum - ($pages-$page));
                                    }
                                    //分頁部份處於該頁就不超連結,不是就連結送出$_GET['page']
                                    if($page-$lnum <= $i && $i <= $page+$rnum){
                                        if($i==$page){
                                            echo $i.' ';
                                        }else if(isset($sort_cate_no)){
                                            echo "<a href=?cate_no=".$sort_cate_no."&page=".$i.">".$i."</a> ";
                                        }else{
                                            echo '<a href=?page='.$i.'>'.$i.'</a> ';
                                        }
                                    }
                                }
                                //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                if($page==$pages){
                                    echo " 下一頁";
                                    echo " 末頁";
                                }else if(isset($sort_cate_no)){
                                    echo "<a href=?cate_no=".$sort_cate_no."&page=".($page+1)."> 下一頁</a>";
                                    echo "<a href=?cate_no=".$sort_cate_no."&page=".$pages."> 末頁</a>";	
                                }else{
                                    echo "<a href=?page=".($page+1)."> 下一頁</a>";
                                    echo "<a href=?page=".$pages."> 末頁</a>";		
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
            <hr>
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
    // All resources finished loading! // 關閉mLoading提示
    window.addEventListener("load", function(event) {
        $("body").mLoading("hide");
    });

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
            swal('change_flag' ,swal_content ,swal_action, {buttons: false, timer:1000});

        }
    }

</script>

<?php include("../template/footer.php"); ?>