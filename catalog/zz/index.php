<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // $sort_cate_no = "all";
    // if(!empty($_REQUEST)){      // 有帶查詢
    // }
    
    if(isset($_POST["submit"])){        // 儲存新增
        store_catalog($_REQUEST);
        $_GET["img"] = "";
    }
        
    if(isset($_REQUEST["changeCatalog_flag"])){        // 調整flag
        changeCatalog_flag($_REQUEST);
    }

    if(isset($_REQUEST["cate_no"])){
        $sort_cate_no = $_REQUEST["cate_no"];
    }else{
        $sort_cate_no = "all";
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
        $per = 10;  //每頁筆數
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
    <style>
        .TOP {
            background-image: URL('../images/about_innolux.png');
            width: auto;
            height: auto;
            position: relative;
            overflow: hidden;
            /* overflow: hidden; 會影響表頭黏貼功能*/
            background-attachment: fixed;
            /* background-position: center top; 對齊*/
            background-position: left top;
            background-repeat: no-repeat;
            background-size: cover;
            /* background-size: contain; */
            padding-top: 100px;
        }
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
        /* 膠囊球增加陰影辨識度 */
        .rounded-pill {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

    </style>
</head>
<!-- <div class="container"> -->
<div class="TOP">
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-xl-12 col-12 rounded p-3 " style="background-color: rgba(255, 255, 255, .6);">
                <h5><b>Catalog/分類與小計：</b></h5>
                <div class="text-center">
                    <a href="?cate_no=all">
                    <span class="badge rounded-pill bg-dark">All</span></a>&nbsp&nbsp
                    <?php foreach($sum_categorys as $sum_cate){ ?>
                        <div class="py-1" style="display: inline-block;">
                            <a href="?cate_no=<?php echo $sum_cate["cate_no"];?>">
                            <span class="badge rounded-pill 
                                <?php switch($sum_cate["cate_id"]){
                                        case "1": echo "bg-primary"; break;
                                        case "2": echo "bg-success"; break;
                                        case "3": echo "bg-warning text-dark"; break;
                                        case "4": echo "bg-danger"; break;
                                        case "5": echo "bg-info text-dark"; break;
                                        case "6": echo "bg-dark"; break;
                                        case "7": echo "bg-secondary"; break;
                                        default: echo "bg-light text-success"; break;
                                    }?>"><?php echo $sum_cate["cate_id"].".".$sum_cate["cate_title"];?>
                                <span class="badge bg-secondary"><?php echo $sum_cate["catalog_count"];?></span>
                            </span></a>&nbsp&nbsp</div>
                    <?php  } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-12">
    <div class="row justify-content-center">
        <div class="col-xl-11 col-11 p-4 rounded" style="background-color: rgba(255, 255, 255, .7);">
            <div class="row">
                <div class="col-md-6 py-0">
                    <div>
                        <h5>Catalog/目錄管理 - 共 <?php echo $per_total;?> 筆</h5>
                    </div>
                </div>
                <div class="col-md-6 py-0 text-end">
                    <div>
                        <?php if($_SESSION["AUTH"]["role"] <= 1){ ?>
                            <a href="create.php" title="新增catalog" class="btn btn-primary"> <i class="fa fa-plus"></i> 新增品項</a>
                            <a href="category.php" title="編輯category" class="btn btn-warning"> <i class="fa fa-wrench"></i> 編輯分類</a>
                            <!-- <a href="..\trade\create.php" title="管理員限定" class="btn btn-warning"><i class="fa fa-upload" aria-hidden="true"></i> 批量撥補</a> -->
                            <!-- <a href="#" title="新增品項2" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#add_cata"> <i class="fa fa-search" aria-hidden="true"></i> 新增品項2</a> -->
                            <!-- <a href="#" target="_blank" title="查詢分類" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#query_category"> <i class="fa fa-search" aria-hidden="true"></i> 查詢分類</a> -->
                        <?php } ?>
                    </div>
                </div>
            </div>
            <hr>
            <div class="px-2">
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
                                }else if(isset($sort_category["category_id"])){
                                    echo "<a href=?category_id=".$sort_category["category_id"]."&page=1>首頁 </a> ";
                                    echo "<a href=?category_id=".$sort_category["category_id"]."&page=".($page-1).">上一頁 </a> ";		
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
                                        }else{
                                            echo '<a href=?page='.$i.'>'.$i.'</a> ';
                                        }
                                    }
                                }
                                //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                if($page==$pages){
                                    echo " 下一頁";
                                    echo " 末頁";
                                }else if(isset($sort_category["category_id"])){
                                    echo "<a href=?category_id=".$sort_category["category_id"]."&page=".($page+1)."> 下一頁</a>";
                                    echo "<a href=?category_id=".$sort_category["category_id"]."&page=".$pages."> 末頁</a>";		
                                }else{
                                    echo "<a href=?page=".($page+1)."> 下一頁</a>";
                                    echo "<a href=?page=".$pages."> 末頁</a>";		
                                }
                            }
                        ?>
                    </div>
                </div>
                
                <!-- catalog名單 -->
                <table>
                    <thead>
                        <tr>
                            <th>PIC</th>
                            <th style="width: 25%;">品項名稱</th>
                            <th>品項訊息</th>
                            <th style="width: 30%;">其他說明<i class="fa fa-info-circle" aria-hidden="true"></i></th>
                            <!-- <th>限購數量</br>3千下/3千上</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($catalogs as $catalog){ ?>
                            <tr>
                                <td>
                                    <div class="col-12 text-center py-1">
                                        <div class="cover_btn px-1">
                                            <a href="repo.php?id=<?php echo $catalog["id"];?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $catalog['pname'];?>">
                                                <img src="images/<?php echo $catalog["PIC"];?>" class="img-thumbnail"></a>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: left; word-break: break-all;">
                                    <div class="row">
                                        <div class="col-12 col-md-6 py-0 px-2">
                                            <h5><span class="badge rounded-pill <?php switch($catalog["cate_id"]){
                                                            case "1": echo "bg-primary"; break;
                                                            case "2": echo "bg-success"; break;
                                                            case "3": echo "bg-warning text-dark"; break;
                                                            case "4": echo "bg-danger"; break;
                                                            case "5": echo "bg-info text-dark"; break;
                                                            case "6": echo "bg-dark"; break;
                                                            case "7": echo "bg-secondary"; break;
                                                            default: echo "bg-light text-success"; break;
                                                        }?>">
                                            <?php echo $catalog["cate_id"].".".$catalog["cate_title"];?></span></h5>
                                        </div>
                                        <div class="col-12 col-md-6 py-0 px-2">
                                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                                <form action="" method="post" class="d-inline-block">
                                                    <input type="hidden" name="id" value="<?php echo $catalog['id'];?>">
                                                    <input type="hidden" name="cate_id" value="<?php echo $catalog['cate_id'];?>">
                                                    <input type="submit" name="changeCatalog_flag" value="<?php echo $catalog['flag'] == 'On' ? '上架':'下架';?>" 
                                                        class="btn btn-sm btn-xs <?php echo $catalog['flag'] == 'On' ? 'btn-success':'btn-warning';?>">
                                                </form>
                                                <a href="edit.php?id=<?php echo $catalog["id"];?>&img=<?php echo $catalog["PIC"];?>" class="btn btn-sm btn-xs btn-info" title="最後編輯：<?php echo $catalog["updated_at"]." / by: ".$catalog["updated_user"];?>">編輯</a>
                                            <?php } ?>
                                        </div>

                                        <div class="col-12 py-0 px-2">
                                            <?php echo "SN：".$catalog["SN"];?>
                                        </div>
    
                                        <div class="col-12 py-0 px-2">
                                            <a href="repo.php?id=<?php echo $catalog["id"];?>"><h5><?php echo $catalog["pname"];?></h5></a>
                                        </div>

                                        <div class="col-12 py-0 px-2">
                                            <?php echo $catalog["cata_remark"] ? '( '.$catalog["cata_remark"].' )':'';?>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: left;"><?php 
                                        echo "單位：".$catalog["unit"]."</br>尺寸：".$catalog["size"];
                                        echo "</br>限購量a/b：".$catalog["buy_a"]." / ".$catalog["buy_b"]."</br>料號：".$catalog["part_no"]; 
                                        echo "</br>品牌/製造商：".$catalog["OBM"]."</br>型號：".$catalog["model"]; 
                                    ?>
                                </td>
                                <td style="text-align: left; vertical-align:top; word-break: break-all;">
                                    <div class="row">
                                        <div class="col-12">
                                            <?php echo "供應商：".$catalog["scomp_no"]."</br>";?>
                                            規格：
                                            <textarea style="height: 90px; resize:none;" placeholder="規格" disabled ><?php echo $catalog["SPEC"];?></textarea>
                                        </div>
                                    </div>
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
                                }else if(isset($sort_category["category_id"])){
                                    echo "<a href=?category_id=".$sort_category["category_id"]."&page=1>首頁 </a> ";
                                    echo "<a href=?category_id=".$sort_category["category_id"]."&page=".($page-1).">上一頁 </a> ";			
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
                                        }else{
                                            echo '<a href=?page='.$i.'>'.$i.'</a> ';
                                        }
                                    }
                                }
                                //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                if($page==$pages){
                                    echo " 下一頁";
                                    echo " 末頁";
                                }else if(isset($sort_category["category_id"])){
                                    echo "<a href=?category_id=".$sort_category["category_id"]."&page=".($page+1)."> 下一頁</a>";
                                    echo "<a href=?category_id=".$sort_category["category_id"]."&page=".$pages."> 末頁</a>";	
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

<!-- 彈出畫面模組1 查詢其他category分類-->
    <div class="modal fade" id="query_category" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">query_category</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="get" onsubmit="this.category_id.disabled=false">
                    <div class="modal-body p-4">
                        <div class="row mb-0">
                            <div class="col-12">
                                <div class="form-floating">
                                    <select name="cate_no" id="cate_no" class="form-control" required >
                                        <option value="" selected hidden>--請選擇category類別--</option>
                                        <option value="all" <?php echo $sort_category["cate_no"] == "all" ? "selected":""; ?>> all: 全部類別(All) </option>
                                        <?php foreach($categories as $category){ ?>
                                            <option value="<?php echo $category["cate_no"];?>" <?php echo $category["cate_no"] == $sort_category["cate_no"] ? "selected":""; ?>>
                                                <?php echo $category["id"].": ".$category["cate_title"]." (".$category["cate_remark"].")";?></option>
                                        <?php } ?>
                                    </select>
                                    <label for="cate_no" class="form-label">cate_no：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="submit" class="btn btn-primary">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>

<!-- 彈出畫面模組2 新增品項-->
    <div class="modal fade" id="add_cata" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog" >
        <div class="modal-dialog modal-xl ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">新增catalog品項資訊</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="modal-body p-4">

                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="submit" class="btn btn-primary">
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
<!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>

<?php include("../template/footer.php"); ?>