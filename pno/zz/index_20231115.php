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
            $_year = date('Y');                 // 今年
            // $_year = "All";                     // 全部
        }
        $query_array = array(
            '_year' => $_year
        );
    $pnos = show_pno($query_array);

    // <!-- 20211215分頁工具 -->
        $per_total = count($pnos);      //計算總筆數
        $per = 25;                      //每頁筆數
        $pages = ceil($per_total/$per); //計算總頁數;ceil(x)取>=x的整數,也就是小數無條件進1法
        if(!isset($_GET['page'])){      //!isset 判斷有沒有$_GET['page']這個變數
            $page = 1;	  
        }else{
            $page = $_GET['page'];
        }
        $start = ($page-1)*$per;        //每一頁開始的資料序號(資料庫序號是從0開始)
        // 合併嵌入分頁工具
            $query_array["start"] = $start;
            $query_array["per"] = $per;
            // array_push($sort_PNO_year, $receive_page_div);
        $pnos = show_pno($query_array);
        $page_start = $start +1;            //選取頁的起始筆數
        $page_end = $start + $per;          //選取頁的最後筆數
            if($page_end>$per_total){       //最後頁的最後筆數=總筆數
                $page_end = $per_total;
            }
    // <!-- 20211215分頁工具 -->

        // 新增料號時，需提供對應的器材選項
        $query_array["cate_no"] = "all";                // 預設全部器材類別
        $catalogs = show_catalogs($query_array);        // 讀取器材清單 by all
        $pno_years = show_PNO_GB_year();                // 取出PNO年份清單 => 供Part_NO料號頁面篩選
    
    $lastYear = date('Y')-1;                            // 取今年值 for 新增料號預設年度
    $thisYear = date('Y');                              // 取今年值 for 新增料號預設年度
    $url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"];


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
        #fix_price tr > th {
            color: blue;
            text-align: center;
            vertical-align: top; 
            word-break: break-all; 
            background-color: white;
            font-size: 16px;
        }
        #fix_price tr > td {
            vertical-align: middle; 
        }
        #fix_price input{
            text-align: center;
        }
        .fix_quote:hover {
            /* font-size: 1.05rem; */
            font-weight: bold;
            text-shadow: 3px 3px 5px rgba(0,0,0,.5);
        }
    </style>
</head>
<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_11 col-11 p-4 rounded" style="background-color: rgba(255, 255, 255, .8);" >
                    
                <div class="row">
                    <div class="col-12 col-md-4 py-0">
                        <h5>Part_NO料號管理</h5>
                    </div>
                    <div class="col-12 col-md-4 py-0">
                        <form action="<?php echo $url;?>" method="post">
                            <input type="hidden" name="activeTab" value="0">
                            <div class="input-group">
                                <span class="input-group-text">篩選年度</span>
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
                            <button type="button" id="add_pno_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_pno" onclick="add_module('pno')" > <i class="fa fa-plus"></i> 新增Part_NO料號</button>
                        <?php } ?>
                        <a href="#" target="_blank" title="編輯Price歷年報價" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#fix_price"> <i class="fa fa-plus"></i> 編輯歷年報價</a>
                    </div>
                </div>

                <!-- NAV分頁標籤與統計 -->
                <div class="col-12 pb-0 px-0">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <button type="button" class="nav-link active">料號&nbsp<span class="badge bg-secondary"><?php echo $per_total;?></span></button>
                        </li>
                    </ul>
                </div>
                <!-- 內頁 -->
                <div class="col-12 bg-white">
                    <div class="col-12 p-0">
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
                                        }else if(isset($_year)){
                                            echo "<a href=?_year=".$_year."&page=1>首頁 </a> ";
                                            echo "<a href=?_year=".$_year."&page=".($page-1).">上一頁 </a> ";
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
                                                    echo '<u><b>'.$i.'</b></u> ';
                                                }else{
                                                    echo '<a href=?page='.$i.'>'.$i.'</a> ';
                                                }
                                            }
                                        }
                                        //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                        if($page==$pages){
                                            echo " 下一頁";
                                            echo " 末頁";
                                        }else if(isset($_year)){
                                            echo "<a href=?_year=".$_year."&page=".($page+1)."> 下一頁</a>";
                                            echo "<a href=?_year=".$_year."&page=".$pages."> 末頁</a>";
                                        }else{
                                            echo "<a href=?page=".($page+1)."> 下一頁</a>";
                                            echo "<a href=?page=".$pages."> 末頁</a>";		
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                        <!-- 20211215分頁工具 -->
                        <table>
                            <thead>
                                <tr class="">
                                    <th rowspan="2" class="block">ai</th>
                                    <th rowspan="2">cate_no</br>器材分類</th>
                                    <th rowspan="2">cata_SN</br>器材編號(名稱)</th>
                                    <th rowspan="2">size</br>尺寸</th>
                                    <th rowspan="2">part_no</br>料號</th>
                                    <th rowspan="2">_year</br>建立年度</th>
                                    <th colspan="2">price</br>年度/單價</th>
                                    <th rowspan="2">part_remark</br>註解說明</th>
                                    <th rowspan="2">flag</th>
                                    <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>    
                                        <th rowspan="2">action</th>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <th><?php echo $lastYear."y";?></th>
                                    <th><?php echo $thisYear."y";?></th>
                                </tr>
                            </thead>
                            <!-- 這裡開始抓SQL裡的紀錄來這裡放上 -->
                            <tbody>
                                <?php foreach($pnos as $pno){ ?>
                                    <tr>
                                        <td style="font-size: 6px;" class="block"><?php echo $pno["id"]; ?></td>
                                        <td><span class="badge rounded-pill <?php switch($pno["cate_id"]){
                                                            case "1": echo "bg-primary"; break;
                                                            case "2": echo "bg-success"; break;
                                                            case "3": echo "bg-warning text-dark"; break;
                                                            case "4": echo "bg-danger"; break;
                                                            case "5": echo "bg-info text-dark"; break;
                                                            case "6": echo "bg-dark"; break;
                                                            case "7": echo "bg-secondary"; break;
                                                            default : echo "bg-light text-success"; break;
                                                        }?>">
                                            <?php echo $pno["cate_no"] ? $pno["cate_no"].".".$pno["cate_remark"]:""; ?></span>
                                        </td>
                                        <td style="width: 25%" class="word_bk">
                                            <?php echo $pno["cata_SN"] ? "<b>".$pno["cata_SN"]."_".$pno["pname"]."</b>":"-- 無 --";
                                                  echo $pno["model"] ? "</br>[".$pno["model"]."]" :""; 
                                                  echo ($pno["cata_flag"] == "Off") ? "<sup class='text-danger'>-已關閉</sup>":"";?></td>
                                        <td><?php echo $pno["size"]; ?></td>
                                        <td style="text-align:left;"><?php echo $pno["part_no"];?></td>
                                        <td><?php echo $pno["_year"]; ?></td>

                                        <td style="text-align: right;">
                                            <?php $price_arr = (array) json_decode($pno["price"]);
                                                  echo "$"; echo isset($price_arr[$thisYear-1]) ? number_format($price_arr[$thisYear-1]) : "0";?>
                                        </td>
                                        <!-- <php echo "$"; echo isset($price_arr[$thisYear]) ? number_format($price_arr[$thisYear]) : "0";?> -->
                                        <td style="text-align: right;" class="fix_quote" id="<?php echo $pno["id"];?>" name="<?php echo $thisYear;?>" contenteditable="true">
                                            <?php echo isset($price_arr[$thisYear]) ? $price_arr[$thisYear] : "0";?>
                                        </td>

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
                        <hr>
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
                                        }else if(isset($_year)){
                                            echo "<a href=?_year=".$_year."&page=1>首頁 </a> ";
                                            echo "<a href=?_year=".$_year."&page=".($page-1).">上一頁 </a> ";
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
                                                    echo '<u><b>'.$i.'</b></u> ';
                                                }else{
                                                    echo '<a href=?page='.$i.'>'.$i.'</a> ';
                                                }
                                            }
                                        }
                                        //在最後頁時,該頁就不超連結,可連結就送出$_GET['page']	
                                        if($page==$pages){
                                            echo " 下一頁";
                                            echo " 末頁";
                                        }else if(isset($_year)){
                                            echo "<a href=?_year=".$_year."&page=".($page+1)."> 下一頁</a>";
                                            echo "<a href=?_year=".$_year."&page=".$pages."> 末頁</a>";
                                        }else{
                                            echo "<a href=?page=".($page+1)."> 下一頁</a>";
                                            echo "<a href=?page=".$pages."> 末頁</a>";		
                                        }
                                    }
                                ?>
                            </div>
                        </div>             
                        <!-- 20211215分頁工具 -->               
                    </div>
                </div>

            </div>
        </div>
    </div>

<!-- 彈出畫面模組 新增、編輯PNO料號 -->
    <div class="modal fade" id="edit_pno" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="modal_action"></span>Part_NO料號</h4>

                    <form action="" method="post">
                        <input type="hidden" name="id" id="pno_delete_id">
                        <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                            &nbsp&nbsp&nbsp&nbsp&nbsp
                            <span id="modal_delect_btn"></span>
                        <?php } ?>
                    </form>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body px-4">
                        <div class="row">

                            <div class="col-12 col-md-4 py-1">
                                <div class="form-floating">
                                    <input type="text" name="_year" id="edit__year" class="form-control" required placeholder="_year建立年度"  value="<?php echo $thisYear;?>">
                                    <label for="edit__year" class="form-label">_year建立年度<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 py-1">
                                <div class="form-floating">
                                    <input type="text" name="part_no" id="edit_part_no" class="form-control" required placeholder="part_no料號">
                                    <label for="edit_part_no" class="form-label">part_no/料號：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 py-1">
                                <div class="form-floating">
                                    <input type="text" name="size" id="edit_size" class="form-control" placeholder="size尺寸">
                                    <label for="edit_size" class="form-label">size/尺寸：</label>
                                </div>
                            </div>

                            <div class="col-12 py-1">
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

                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <textarea name="pno_remark" id="edit_pno_remark" class="form-control" style="height: 100px" placeholder="註解說明"></textarea>
                                    <label for="edit_pno_remark" class="form-label">pno_remark/備註說明：</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 py-1">
                                <div class="form-floating">
                                    <input type="text" name="_quoteYear" id="edit_quoteYear" class="form-control" required placeholder="_quoteYear年度" value="<?php echo $thisYear;?>">
                                    <label for="edit_quoteYear" class="form-label">_quoteYear/報價年度：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 py-1">
                                <div class="form-floating">
                                    <input type="number" name="_price" id="edit_price" class="form-control" required placeholder="_price單價" min="0">
                                    <label for="edit_price" class="form-label">_price/單價：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 py-1">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="flag" class="form-label">flag/顯示開關：</label>
                                        </td>
                                        <td>
                                            <input type="radio" name="flag" value="On" id="edit_pno_On" class="form-check-input" checked>&nbsp
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
                            <input type="hidden" name="page" value="<?php echo isset($_REQUEST['page']) ? $_REQUEST['page'] : '1' ;?>">
                            <input type="hidden" name="id" id="pno_edit_id" >
                            <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>   
                                <span id="modal_button"></span>
                            <?php } ?>
                            <input type="reset" class="btn btn-info" id="reset_btn" value="清除">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
    
            </div>
        </div>
    </div>

<!-- 彈出畫面模組 編輯Price歷年報價 -->
    <div class="modal fade" id="fix_price" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" aria-modal="true" role="dialog" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">編輯料號：<span id="fix_part_no">fix123</span> 歷年報價</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post" id="fix_price_form">
                    <div class="modal-body px-4">
                        <div class="col-12 py-0">
                            <table class="for-table logs table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>報價年度</th>
                                        <th>單價</th>
                                    </tr>
                                </thead>
                                <tbody id="fix_part_table">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" name="activeTab" value="0">
                            <input type="hidden" name="page" value="<?php echo isset($_REQUEST['page']) ? $_REQUEST['page'] : '1' ;?>">
                            <input type="hidden" name="id" id="pno_price_id" >
                            <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>   
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="fix_price_form()">儲存</button>
                            <?php } ?>
                            <input type="reset" class="btn btn-info" id="reset_btn" value="清除">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
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
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>
<!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
<script src="../../libs/sweetalert/sweetalert.min.js"></script>

<script>
    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })

    // All resources finished loading! // 關閉mLoading提示
    window.addEventListener("load", function(event) {
        $("body").mLoading("hide");
    });

    function resetMain(){
        $("#result").removeClass("border rounded bg-white");
        $('#result_table').empty();
        document.querySelector('#key_word').value = '';
    }

    var pno      = <?=json_encode($pnos);?>;                                                   // 引入pnos資料
    var thisYear_num = Number(<?=$thisYear;?>);                                                // 引入$thisYear資料
    var thisYear_str = String(<?=$thisYear;?>);                                                // 引入$thisYear資料
    var pno_item = ['id','_year','part_no','size','cata_SN','pno_remark','price','flag'];      // 交給其他功能帶入 delete_pno_id
    function add_module(to_module){     // 啟用新增模式
        $('#modal_action, #modal_button, #modal_delect_btn, #edit_pno_info').empty();   // 清除model功能
        $('#reset_btn').click();                                                        // reset清除表單
        var add_btn = '<input type="submit" name="pno_submit" class="btn btn-primary" value="新增">';
        $('#modal_action').append('新增');                      // model標題
        $('#modal_button').append(add_btn);                     // 儲存鈕
        var reset_btn = document.getElementById('reset_btn');   // 指定清除按鈕
        reset_btn.classList.remove('unblock');                  // 新增模式 = 解除
    }
    // fun-1.鋪編輯畫面
    function edit_module(to_module, row_id){
        $('#modal_action, #modal_button, #modal_delect_btn, #edit_pno_info').empty();   // 清除model功能
        $('#reset_btn').click();                                                        // reset清除表單
        var reset_btn = document.getElementById('reset_btn');   // 指定清除按鈕
        reset_btn.classList.add('unblock');                     // 編輯模式 = 隱藏
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
                    }else if(item_key == 'price'){
                        var price_json = row[item_key];
                        if(!price_json || price_json == 0){
                            var price_json_parse = {};
                        }else{
                            var price_json_parse = JSON.parse(price_json);
                        }
                        if(!price_json_parse[thisYear_num]){
                            price_json_parse[thisYear_num] = 0;
                        }
                        document.querySelector('#edit_'+to_module+' #edit_quoteYear').value = thisYear_num; 
                        document.querySelector('#edit_'+to_module+' #edit_price').value = price_json_parse[thisYear_num]; 
                    }else{
                        document.querySelector('#edit_'+to_module+' #edit_'+item_key).value = row[item_key]; 
                    }
                })

                // 鋪上最後更新
                let to_module_info = '最後更新：'+row['updated_at']+' / by '+row['updated_user'];
                document.querySelector('#edit_'+to_module+'_info').innerHTML = to_module_info;

                // step3-3.開啟 彈出畫面模組 for user編輯
                // edit_myTodo_btn.click();
                var add_btn = '<input type="submit" name="edit_pno_submit" class="btn btn-primary" value="儲存">';
                var del_btn = '<input type="submit" name="delete_pno" value="刪除pno料號" class="btn btn-sm btn-xs btn-danger" onclick="return confirm(`確認刪除？`)">';
                $('#modal_action').append('編輯');          // model標題
                $('#modal_delect_btn').append(del_btn);     // 刪除鈕
                $('#modal_button').append(add_btn);         // 儲存鈕
                return;
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
    
    // 20231114_綁定編輯完成事件    // contenteditable="true" table可編輯、儲存功能
    var rows = document.getElementsByTagName("td");
        Array.from(rows).forEach(function(row) {
            row.addEventListener("blur", handleBlur);               // 監聽進入編輯狀態 或失焦
            row.addEventListener("keydown", handleKeyDown);         // 監聽Enter => run失焦
            row.addEventListener("click", handleCellClick);         // 監聽點擊時取得原始值
        });
    // tableFun_1.綁定失焦事件
    function handleBlur(e) {                                    // 綁定失焦事件
        var rowId = parseFloat(this.id);
        var rowName = parseFloat(this.getAttribute("name"));
        var newValue = parseFloat(this.innerHTML.replace(/[^\d.-]/g, ""));
        var originalValue = parseFloat(this.getAttribute("data-original-value").replace(/[^\d.-]/g, ""));
        
        if (newValue !== originalValue) {
            var request = {
                "rowId"     : rowId,
                "rowName"   : rowName,
                "newValue"  : newValue
            }
            updateCellValue(this, newValue, request);           // 呼叫 tableFun_4.API更新
        }
    }
    // tableFun_2.綁定按鍵事件
    function handleKeyDown(e) {                                 // 綁定按鍵事件
        if (e.keyCode == 13) {                                  // 如果按下的是 Enter 键
            e.preventDefault();
            this.blur();
        } else if (e.keyCode == 27) {                           // 如果按下的是 Esc 键
            var originalValue = parseFloat(this.getAttribute("data-original-value").replace(/[^\d.-]/g, ""));
            this.innerHTML = originalValue;
            this.blur();
        }
    }
    // tableFun_3.綁定單元格編輯開始事件
    function handleCellClick(e) {                               // 綁定單元格編輯開始事件
        this.setAttribute("data-original-value", this.textContent.trim());      // 獲取當前單元格的原始值並設置到屬性中
    }
    // tableFun_4.API更新
    function updateCellValue(cell, newValue, _request) {
        cell.innerHTML = newValue;
        // console.log("送API", _request);
        $.ajax({
                url:'api.php',
                method:'post',
                async: false,                                           // ajax取得數據包後，可以return的重要參數
                dataType:'json',
                data:{
                    function: 'update_price',           // 操作功能
                    _id: _request['rowId'],
                    _quoteYear: _request['rowName'],
                    _price: _request['newValue'],
                    // json_request: JSON.stringify({_request})
                },
                success: function(res){
                    let res_r = res["result"];
                    // console.log('4.API更新 - ', res);
                    update_catchValue(_request);                        // 呼叫 tableFun_5.更新pno_Catch中的數值
                    // swal_action = 'success';
                    // swal_content += res_r_flag+' 套用成功';
                },
                error: function(e){
                    // swal_action = 'error';
                    // swal_content += res_r_flag+' 套用失敗';
                    console.log("error");
                }
            });
    }
    // tableFun_5.更新Catch中的數值
    function update_catchValue(_request){
        for(var i=0; i < pno.length ; i++){                             // pno array 採用迴圈繞出來
            if(pno[i]['id'] == _request['rowId']){                        // 找到id = rowId
                if(!pno[i]['price'] || pno[i]['price'] == 0){               // 沒有price或等於0
                    var price_json_parse = {};                                  // 建一個空物件
                }else{                                                      // 有price
                    var price_json_parse = JSON.parse(pno[i]['price']);         // 1.取pno中的price 2.解碼 3.放到price_json_parse
                }

                var _quoteYear = _request['rowName'];                   // 取參數 _quoteYear = rowName
                var _price = _request['newValue'];                      // 取參數 _price = newValue
                price_json_parse[_quoteYear] = _price;                  // 將參數覆蓋進去price_json_parse陣列/物件中
                pno[i]['price'] = JSON.stringify(price_json_parse);     // 1.編碼price_json_parse 2.覆蓋進pno中

                return;                                                 // 找到+完成後=返回
            }
        }
    }

    // 精簡前語法
    // var rows = document.getElementsByTagName("td");
        // Array.from(rows).forEach(function(row) {
        //     // 綁定失去焦點事件
        //     row.addEventListener("blur", function(e) {
        //         // 取得編輯後的值及目前行的編號
        //             var newValue = (this.innerHTML).replace(/[^\d.-]/g, "");
        //                 newValue = Number(newValue);
        //             var rowId = this.id;
        //             var rowName = this.getAttribute("name");

        //             // 取得原始資料
        //             var originalValue = this.getAttribute("data-original-value").replace(/[^\d.-]/g, "");
        //                 originalValue = Number(originalValue);

        //         // 如果編輯後的值與原始資料不同，才更新至資料庫
        //         if (newValue !== originalValue) {
        //             this.innerHTML = newValue;
        //             // 傳送 Ajax 請求至伺服器端，以下省略...
        //             console.log("失去焦點事件觸發", rowId, rowName, originalValue, newValue);
        //         }
        //     });

        //     // 綁定按下 enter 事件
        //     row.addEventListener("keydown", function(e) {
        //         // 如果按下的是 Enter 鍵
        //         if (e.keyCode == 13) {
        //             // 阻止預設行為 (移動到下一行)
        //             e.preventDefault();

        //             // 取得編輯後的值及目前行的編號
        //                 var newValue = (this.innerHTML).replace(/[^\d.-]/g, "");
        //                     newValue = Number(newValue);
        //                 var rowId = this.id;
        //                 var rowName = this.getAttribute("name");

        //                 // 取得原始資料
        //                 var originalValue = this.getAttribute("data-original-value").replace(/[^\d.-]/g, "");
        //                     originalValue = Number(originalValue);

        //             // 如果編輯後的值與原始資料不同，才更新至資料庫
        //             if (newValue !== originalValue) {
        //                 this.innerHTML = newValue;
        //                 // 傳送 Ajax 請求至伺服器端，以下省略...
        //                 console.log("按下 Enter 鍵事件觸發", rowId, rowName, originalValue, newValue);
        //             }

        //             // 取消表格單元格的 Focus 狀態
        //             this.blur();
        //         }
        //     });

        //     // 綁定單元格編輯開始事件
        //     row.addEventListener("click", function(e) {
        //         // 獲取當前單元格的原始值並設置到屬性中
        //         this.setAttribute("data-original-value", this.textContent.trim());
        //     });
        // });



//     // 呼叫編輯Price歷年報價
    //     let fix_quotes = [...document.querySelectorAll('.fix_quote')];
    //     for(let fix_quote of fix_quotes){
    //         fix_quote.onclick = e => {
    //             let swal_content = e.target.name+'_id:'+e.target.id+'=';
    //             // console.log('e:',e.target.id);
    //             var target_id = e.target.id;
    //             $('#fix_part_no, #fix_part_table').empty();
    //                 // step1.將原陣列逐筆繞出來
    //                 Object(pno).forEach(function(row){          
    //                     if(row['id'] == target_id){
    //                         // step2.鋪畫面到module
    //                         $('#fix_part_no').append(row['part_no']);
    //                         var lastYear_num = thisYear_num-1;     
    //                         var row_price = row['price'];
    //                         if(row_price != 0 || row_price != ''){
    //                             row_price = JSON.parse(row_price);
    //                         }else{
    //                             row_price = {};
    //                         }

    //                         if(row_price[lastYear_num] != undefined) {
    //                             var lastYear_price = row_price[lastYear_num];
    //                         }else{
    //                             var lastYear_price = 0;
    //                         }
    //                         if(row_price[thisYear_num] != undefined) {
    //                             var thisYser_price = row_price[thisYear_num];
    //                         }else{
    //                             var thisYser_price = 0;
    //                         }
    //                         // Object.keys(row_price).forEach(function(price_key){  
    //                             //     let edit_quoteYear = '<tr><td><input type="number" name="_quoteYear" id="_quoteYear" required placeholder="_報價年度" value="'+price_key+'"></td>';
    //                             //     edit_quoteYear += '<td><input type="number" name="_price" id="_price" required placeholder="_單價" value="'+row_price[price_key]+'"></td></tr>';
    //                             //     $('#fix_part_table').append(edit_quoteYear);
    //                             // })  
    //                         var edit_quoteYear  = '<tr><td>'+lastYear_num+'</td>';
    //                             // edit_quoteYear += '<td><input type="number" name="price['+lastYear_num+']" required placeholder="_去年報價" value="'+lastYear_price+'"></td></tr>';
    //                             edit_quoteYear += '<td>'+lastYear_price+'</td></tr>';
    //                             edit_quoteYear += '<tr><td>'+thisYear_num+'</td>';
    //                             edit_quoteYear += '<td><input type="number" name="price['+thisYear_num+']" required placeholder="_今年報價" value="'+thisYser_price+'"></td></tr>';
    //                         $('#fix_part_table').append(edit_quoteYear);
    //                         document.querySelector('#pno_price_id').value = target_id; 

    //                         return; // 找到後就可以結束迴圈了
    //                     }
    //                 })


    //             var fix_price = new bootstrap.Modal(document.getElementById('fix_price'));
    //             fix_price.show();

    //             // $.ajax({
    //             //     url:'api.php',
    //             //     method:'post',
    //             //     async: false,                                           // ajax取得數據包後，可以return的重要參數
    //             //     dataType:'json',
    //             //     data:{
    //             //         function: 'cheng_flag',           // 操作功能
    //             //         table: e.target.name,
    //             //         id: e.target.id,
    //             //         flag: e.target.value
    //             //     },
    //             //     success: function(res){
    //             //         let res_r = res["result"];
    //             //         let res_r_flag = res_r["flag"];
    //             //         // console.log(res_r_flag);
    //             //         if(res_r_flag == 'Off'){
    //             //             e.target.classList.remove('btn-success');
    //             //             e.target.classList.add('btn-warning');
    //             //             e.target.value = 'Off';
    //             //             e.target.innerText = 'Off';
    //             //         }else{
    //             //             e.target.classList.remove('btn-warning');
    //             //             e.target.classList.add('btn-success');
    //             //             e.target.value = 'On';
    //             //             e.target.innerText = 'On';
    //             //         }
    //             //         swal_action = 'success';
    //             //         swal_content += res_r_flag+' 套用成功';
    //             //     },
    //             //     error: function(e){
    //             //         swal_action = 'error';
    //             //         swal_content += res_r_flag+' 套用失敗';
    //             //         console.log("error");
    //             //     }
    //             // });

    //             // swal('套用人事資料' ,swal_content ,swal_action, {buttons: false, timer:2000}).then(()=>{location.href = url;});     // deley3秒，then自動跳轉畫面
    //             // swal('change_flag' ,swal_content ,swal_action, {buttons: false, timer:1000});

    //         }
    //     }
//    // fun-1.鋪info畫面
    //     function fix_price_form(){
    //         var fix_price_form = document.getElementById('fix_price_form');
    //         // var fix_price_formDatd = new FormData(fix_price_form);
    //         // var username = form.elements["username"].value;
    //         // console.log('fix_price_form:', fix_price_form.elements["price["+thisYear_num+"]"].value);
    //         // console.log('id:', fix_price_form.elements["id"].value);
    //         var pno_id = fix_price_form.elements["id"].value;
    //         var thisYear_price = {};
    //         thisYear_price[thisYear_num] = Number(fix_price_form.elements["price["+thisYear_num+"]"].value);

    //         $.ajax({
    //             url:'api.php',
    //             method:'post',
    //             async: false,                                           // ajax取得數據包後，可以return的重要參數
    //             dataType:'json',
    //             data:{
    //                 function: 'update_price',           // 操作功能
    //                 id: pno_id,
    //                 price: JSON.stringify(thisYear_price)
    //             },
    //             success: function(res){
    //                 let res_r = res["result"];
    //                 let res_r_flag = res_r["flag"];
    //                 // console.log(res_r_flag);
    //                 if(res_r_flag == 'Off'){
    //                     e.target.classList.remove('btn-success');
    //                     e.target.classList.add('btn-warning');
    //                     e.target.value = 'Off';
    //                     e.target.innerText = 'Off';
    //                 }else{
    //                     e.target.classList.remove('btn-warning');
    //                     e.target.classList.add('btn-success');
    //                     e.target.value = 'On';
    //                     e.target.innerText = 'On';
    //                 }
    //                 swal_action = 'success';
    //                 swal_content += res_r_flag+' 套用成功';
    //             },
    //             error: function(e){
    //                 swal_action = 'error';
    //                 swal_content += res_r_flag+' 套用失敗';
    //                 console.log("error");
    //             }
    //         });
    //     }

</script>

<?php include("../template/footer.php"); ?>