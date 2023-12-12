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
        $pnos_div = show_pno($query_array);
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
        .tab-content.active {
            /* display: block; */
            animation: fadeIn 1s;
        }
        .nav-tabs .nav-link.active {
            /* color: #FFFFFF; */
            background-color: #84C1FF;
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
        .add_mode_bgc{
            background-color: #ADD8E6;
        }
        .edit_mode_bgc{
            background-color: #FFFACD;
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
                            <div class="row">
                                <div class="col-12 col-md-4 py-0">
                                    <?php if($per_total != 0){ ?>
                                        <!-- 下載EXCEL的觸發 -->
                                        <form id="pno_myForm" method="post" action="../_Format/download_excel.php">
                                            <input type="hidden" name="htmlTable" id="pno_htmlTable" value="">
                                            <button type="submit" name="submit" class="btn btn-success" value="pno" onclick="submitDownloadExcel('pno')" >
                                                <i class="fa fa-download" aria-hidden="true"></i> 下載</button>
                                        </form>
                                    <?php } ?>
                                </div>
                                <div class="col-12 col-md-8 py-0">
                                    <button type="button" id="add_pno_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_pno" onclick="add_module('pno')" > <i class="fa fa-plus"></i> 單筆新增</button>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#load_excel" onclick="excel_module('pno')"><i class="fa fa-upload" aria-hidden="true"></i> 上傳</button>
                                </div>
                            </div>
                            
                        <?php } ?>
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
                                    <th rowspan="2" class="unblock">ai</th>
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
                                <?php foreach($pnos_div as $pno){ ?>
                                    <tr>
                                        <td style="font-size: 6px;" class="unblock"><?php echo $pno["id"]; ?></td>
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

                <!-- 尾段：debug訊息 -->
                <?php if(isset($_REQUEST["debug"])){
                    include("debug_board.php"); 
                } ?>

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
<!-- 互動視窗 upload_excel -->
    <div class="modal fade" id="load_excel" tabindex="-1" aria-labelledby="load_excel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">上傳<span id="excel_modal_action"></span>&nbspExcel檔：</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4">
                    <form name="excelInput" action="../_Format/upload_excel.php" method="POST" enctype="multipart/form-data" target="api" onsubmit="return checkExcelForm()">
                        <div class="row">
                            <div class="col-6 col-md-8 py-0">
                                <label for="excelFile" class="form-label">需求清單 <span id="excel_example"></span> 
                                    <sup class="text-danger"> * 限EXCEL檔案</sup></label>
                                <div class="input-group">
                                    <input type="file" name="excelFile" id="excelFile" style="font-size: 16px; max-width: 400px;" class="form-control form-control-sm" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                                    <button type="submit" name="excelUpload" id="upload_excel_btn" class="btn btn-outline-secondary" value="">上傳</button>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 py-0">
                                <p id="warningText" name="warning" >＊請上傳需求單Excel檔</p>
                                <p id="warningData" name="warning" >＊請確認Excel中的資料</p>
                            </div>
                        </div>
                                
                        <div class="row" id="excel_iframe">
                            <iframe id="api" name="api" width="100%" height="auto" style="display: none;" onclick="checkExcelForm()"></iframe>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <form action="import_excel.php" method="POST">
                        <input  type="hidden" name="excelTable"   id="excelTable"       value="">
                        <input  type="hidden" name="updated_user" id="updated_user"     value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                        <button type="submit" name="import_excel" id="import_excel_btn" value="" class="btn btn-success unblock" data-bs-dismiss="modal">載入</button>
                    </form>
                    <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">返回</button>
                </div>
            </div>
        </div>
    </div>
<!-- toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast align-items-center" role="alert" aria-live="assertive" aria-atomic="true" autohide="true" delay="1000">
            <div class="d-flex">
                <div class="toast-body" id="toast-body"></div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
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

    var pno      = <?=json_encode($pnos);?>;                                                   // 引入pnos資料
    var thisYear_num = Number(<?=$thisYear;?>);                                                // 引入$thisYear資料
    var thisYear_str = String(<?=$thisYear;?>);                                                // 引入$thisYear資料
    var pno_item = ['id','_year','part_no','size','cata_SN','pno_remark','price','flag'];      // 交給其他功能帶入 delete_pno_id

// 以下為控制 iframe
    var realName         = document.getElementById('realName');           // 上傳後，JSON存放處(給表單儲存使用)
    var iframe           = document.getElementById('api');                // 清冊的iframe介面
    var warningText      = document.getElementById('warningText');        // 提示-檔案上傳
    var warningData      = document.getElementById('warningData');        // 提示-檔案內容
    var excel_json       = document.getElementById('excel_json');         // excel內容轉json
    var excelFile        = document.getElementById('excelFile');          // 上傳檔案名稱
    var upload_excel_btn = document.getElementById('upload_excel_btn');   // 按鈕-上傳
    var import_excel_btn = document.getElementById('import_excel_btn');   // 按鈕-載入

</script>

<script src="pno.js?v=<?=time();?>"></script>

<?php include("../template/footer.php"); ?>