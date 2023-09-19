<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

        // 刪除表單
        if(isset($_POST["delete_receive"])){
            $check_delete_result = delete_receive($_REQUEST);
            if($check_delete_result){
                echo "<script>alert('receive領用申請單 -- 已刪除');</script>";
                header("refresh:0;url=index.php");
                exit;		// 請注意
            }else{
                echo "<script>alert('receive領用申請單 -- 刪除失敗!!');</script>";
            }
        }
        // 更新log
        if(isset($_POST["delete_log"])){
            updateLogs($_REQUEST);
        }

    // 決定表單開啟方式
    if(isset($_REQUEST["action"])){
        $action = $_REQUEST["action"];              // 有action就帶action
    }else{
        $action = 'create';                         // 沒有action就新開單
    }

    if(isset($_REQUEST["uuid"])){
        $receive_row = show_receive($_REQUEST);
        if(empty($receive_row)){
            echo "<script>alert('uuid-error：{$_REQUEST["uuid"]}')</script>";
            header("refresh:0;url=index.php");
            exit;
        }
        // logs紀錄鋪設前處理 
        $logs_dec = json_decode($receive_row["logs"]);
        $logs_arr = (array) $logs_dec;

    }else{
        $receive_row = array( "uuid" => "" );       // 預設receive_row[uuid]=空array
        $logs_arr = [];                             // 預設logs_arr=空array
        $action = 'create';                         // 因為沒有uuid，列為新開單，防止action outOfspc
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
    <!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
    <script src="../../libs/sweetalert/sweetalert.min.js"></script>
    <!-- mloading JS -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <!-- mloading CSS -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <style>
        .unblock{
            display: none;
            /* transition: 3s; */
        }
        /*眼睛*/
        #checkEye , #in_sign_badge {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
        }
        .tag{
            display: inline-block;
            /* 粉紅 */
            /* background-color: #fa0e7e; */
            /* 粉藍 */
            background-color: #0e7efa;
            font: 14px;
            color: white;
            border-radius: 5px;
            padding: 0px 3px 0px 7px;
            margin-right: 5px;
            margin-bottom:5px;
            /* 粉紅 */
            /* box-shadow: 0 5px 15px -2px rgba(250 , 14 , 126 , .7); */
            /* 粉藍 */
            box-shadow: 0 5px 15px -2px rgba(3 , 65 , 134 , .7);
        }
        .tag .remove {
            margin: 0 7px 3px;
            display: inline-block;
            cursor: pointer;
        }
        #catalog_list img {
            max-width: 100px;
            /* max-height: 100px; */
            max-height: 100px;
        }
        .badge {
            /* 標籤增加陰影辨識度 */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        .cata_info_btn , .add_btn{
            /* 將圖示的背景色設置為透明並添加陰影 */
            background-color: transparent; 
            text-shadow: 0px 0px 1px #fff;
            color: blue;
            /* 將圖示的背景色設置為按鈕的背景色 */
            /* background-color: inherit; */
        }
        .cata_info_btn:hover , .add_btn:hover{
            /* color: red; */
            transition: .5s;
            font-weight: bold;
            text-shadow: 3px 3px 5px rgba(0,0,0,.5);
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
                icon: "../../libs/jquery/Wedges-3s-120px.gif",
            }); 
        }
        mloading();    // 畫面載入時開啟loading
    </script>
</head>

<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-11 border rounded px-3 py-4" style="background-color: #D4D4D4;">
                <!-- 表頭1 -->
                <div class="row px-2">
                    <div class="col-12 col-md-6 py-0">
                        <h3><i class="fa-solid fa-3"></i>&nbsp<b>領用申請</b><?php echo empty($action) ? "":" - ".$action;?></h3>
                    </div>
                    <div class="col-12 col-md-6 py-0 text-end">
                        <!-- <a href="index.php" class="btn btn-success"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回總表</a> -->
                        <a href="index.php" class="btn btn-danger" onclick="return confirm('確認返回？');" ><i class="fa fa-external-link" aria-hidden="true"></i> 返回</a>
                    </div>
                </div>

                <div class="row px-2">
                    <div class="col-12 col-md-6">
                        領用單號：<?php echo ($action == 'create') ? "(尚未給號)": "aid_".$receive_row['id']; ?></br>
                        開單日期：<?php echo ($action == 'create') ? date('Y-m-d H:i')."&nbsp(實際以送出時間為主)":$receive_row['created_at']; ?></br>
                        填單人員：<?php echo ($action == 'create') ? $_SESSION["AUTH"]["emp_id"]." / ".$_SESSION["AUTH"]["cname"] : $receive_row["created_emp_id"]." / ".$receive_row["created_cname"] ;?>
                    </div>
                    <div class="col-12 col-md-6 text-end">
                        <?php if(($_SESSION[$sys_id]["role"] <= 1 ) && (isset($receive_row['idty']) && $receive_row['idty'] != 0)){ ?>
                            <form action="" method="post">
                                <input type="hidden" name="uuid" value="<?php echo $receive_row["uuid"];?>">
                                <input type="submit" name="delete_receive" value="刪除" title="刪除申請單" class="btn btn-danger" onclick="return confirm('確認徹底刪除此單？')">
                            </form>
                        <?php }?>
                    </div>
                </div>
    
                <!-- container -->
                <div class="col-12 p-0">
                    <!-- 分頁標籤 -->
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" 
                                aria-controls="nav-home" aria-selected="true">1.選取器材用品</button>
                            <button class="nav-link" id="nav-shopping_cart-tab" data-bs-toggle="tab" data-bs-target="#nav-shopping_cart" type="button" role="tab" 
                                aria-controls="nav-shopping_cart" aria-selected="false">2.購物車&nbsp<span id="shopping_count" class="badge rounded-pill bg-danger"></span></button>
                            <button class="nav-link disabled" id="nav-review-tab" data-bs-toggle="tab" data-bs-target="#nav-review" type="button" role="tab" 
                                aria-controls="nav-review" aria-selected="false">3.申請單成立</button>
                        </div>
                    </nav>
                    <!-- 內頁 -->
                    <form action="store.php" method="post">
                        <div class="tab-content rounded bg-light" id="nav-tabContent">
                            <!-- 1.商品目錄 -->
                            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                <div class="col-12 px-4">
                                    <table id="catalog_list" class="catalog_list table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th class="unblock">cate_no</th>
                                                <th>PIC</th>
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
                                                    <td style="text-align: left;">
                                                        <button type="button" id="cata_info_<?php echo $catalog['SN'];?>" value="<?php echo $catalog['SN'];?>" data-bs-toggle="modal" data-bs-target="#cata_info" 
                                                            class="cata_info_btn" onclick="info_module('catalog',this.value);"><h5><b><?php echo $catalog["pname"];?></b></h5></button>
                                                        <?php 
                                                            echo $catalog["SN"] ? '</br>SN：'.$catalog["SN"]:'</br>';
                                                            echo $catalog["cata_remark"] ? '</br>敘述：'.$catalog["cata_remark"]:'</br>';?>
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
                                                        ?></td>
                                                    <td><input type="number" id="<?php echo $catalog['SN'];?>" class="form-control amount t-center"
                                                            placeholder="數量" min="0" max="999" maxlength="3" oninput="if(value.length>3)value=value.slice(0,3)"></td>
                                                    <td>
                                                        <button type="button" name="<?php echo $catalog['SN'];?>" id="add_<?php echo $catalog['SN'];?>" class="add_btn" value="" title="加入購物車" onclick="add_item(this.name,this.value,'off');"><h5><i class="fa-regular fa-square-plus"></i></h5></button>
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
                                                    <th>select_id</th>
                                                    <th>SN</th>
                                                    <th>品名</th>
                                                    <th>型號</th>
                                                    <th>尺寸</th>
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
                                                <input type="text" name="plant" id="plant" class="form-control" required placeholder="申請單位">
                                                <label for="plant" class="form-label">plant/申請單位：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div class="form-floating">
                                                <input type="text" name="dept" id="dept" class="form-control" required placeholder="部門名稱">
                                                <label for="dept" class="form-label">dept/部門名稱：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div class="form-floating">
                                                <input type="text" name="sign_code" id="sign_code" class="form-control" required placeholder="部門代號" onblur="this.value = this.value.toUpperCase();">
                                                <label for="sign_code" class="form-label">sign_code/部門代號：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 表列2 申請人 -->
                                    <div class="row">
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div class="form-floating">
                                                <input type="text" name="emp_id" id="emp_id" class="form-control" required placeholder="工號" value="<?php echo $_SESSION["AUTH"]["emp_id"];?>">
                                                <label for="emp_id" class="form-label">emp_id/工號：<sup class="text-danger"> *</sup></label>
                                                <button type="button" onclick="search_fun('emp_id');"><i id="checkEye" class="fa-solid fa-paint-roller" data-toggle="tooltip" data-placement="bottom" title="以工號自動帶出其他資訊"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div class="form-floating">
                                                <input type="text" name="cname" id="cname" class="form-control" required placeholder="申請人姓名" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                                <label for="cname" class="form-label">cname/申請人姓名：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div class="form-floating">
                                                <input type="text" name="extp" id="extp" class="form-control" required placeholder="分機">
                                                <label for="extp" class="form-label">extp/分機：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 表列3 領用站點 -->
                                    <div class="row">
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div class="form-floating">
                                                <select name="local_id" id="local_id" class="form-select" required>
                                                    <option value="" hidden>-- [請選擇 領用站點] --</option>
                                                    <?php foreach($allLocals as $allLocal){ ?>
                                                        <!-- <php if($_SESSION[$sys_id]["role"] <= 1 || $allLocal["fab_id"] == $_SESSION[$sys_id]["fab_id"] || (in_array($allLocal["fab_id"], $_SESSION[$sys_id]["sfab_id"]))){ ?>   -->
                                                        <?php if($allLocal["flag"] != "off"){ ?>  
                                                            <option value="<?php echo $allLocal["id"];?>" title="<?php echo $allLocal["fab_title"];?>" >
                                                                <?php echo $allLocal["id"]."：".$allLocal["site_title"]."&nbsp".$allLocal["fab_title"]."_".$allLocal["local_title"]; if($allLocal["flag"] == "Off"){ ?>(已關閉)<?php }?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                                <label for="local_id" class="form-label">local_id/領用站點：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div style="display: flex;">
                                                <label for="" class="form-label">ppty/需求類別：</label></br>&nbsp
                                                <input type="radio" name="ppty" value="1" id="ppty_1" class="form-check-input" required checked>
                                                <label for="ppty_1" class="form-check-label">&nbsp一般&nbsp&nbsp</label>
                                                <input type="radio" name="ppty" value="3" id="ppty_3" class="form-check-input" required>
                                                <label for="ppty_3" class="form-check-label" data-toggle="tooltip" data-placement="bottom" title="注意：事故須先通報防災!!">&nbsp緊急</label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4 py-1 px-2">
                                            <div class="form-floating">
                                                <input type="text" name="in_sign" id="in_sign" class="form-control" required placeholder="上層主管工號"
                                                        data-toggle="tooltip" data-placement="bottom" title="輸入上層主管工號"
                                                        onchange="search_fun(this.value);">
                                                <label for="in_sign" class="form-label">in_sign/上層主管工號：<sup class="text-danger"> *</sup></label>
                                                <!-- <h5><span id="in_sign_badge" class="badge pill bg-primary"></span></h5> -->
                                                <div id="in_sign_badge"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 表列5 說明 -->
                                    <div class="row">
                                        <div class="col-12 px-2">
                                            <div class="form-floating">
                                                <textarea name="receive_remark" id="receive_remark" class="form-control" style="height: 150px;" placeholder="(由申請單位填寫用品/器材請領原由)"></textarea>
                                                <label for="receive_remark" class="form-label">receive_remark/用途說明：<sup class="text-danger"> * (由申請單位填寫用品/器材請領原由)</sup></label>
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

                        <!-- 彈出畫面模組 saveSubmit-->
                        <div class="modal fade" id="saveSubmit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Do you submit this 領用申請：</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body px-5">
                                        <label for="sign_comm" class="form-check-label" >command：</label>
                                        <textarea name="sign_comm" id="sign_comm" class="form-control" rows="5"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="created_emp_id" id="created_emp_id" value="<?php echo $_SESSION["AUTH"]["emp_id"];?>">
                                        <input type="hidden" name="created_cname" id="created_cname" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                        <input type="hidden" name="updated_user" id="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                        <input type="hidden" name="uuid" id="uuid" value="">
                                        <input type="hidden" name="action" id="action" value="<?php echo $action;?>">
                                        <input type="hidden" name="idty" id="idty" value="1">
                                        <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                                            <!-- <input type="submit" value="Submit" name="receive_submit" class="btn btn-primary"> -->
                                            <button type="submit" value="Submit" name="receive_submit" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> 送出 (Submit)</button>
                                        <?php } ?>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <!-- 尾段logs訊息 -->
                    <div class="col-12 pt-0 rounded bg-light unblock" id="logs_div">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                表單Log記錄：
                            </div>
                            <div class="col-12 col-md-6">
                        
                            </div>
                        </div>
                        <table class="for-table logs table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Step</th>
                                    <th>Signer</th>
                                    <th>Time Signed</th>
                                    <th>Status</th>
                                    <th>Comment</th>
                                    <?php if($_SESSION[$sys_id]["role"] <= 1){ ?><th>action</th><?php } ?>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div style="font-size: 6px;" class="text-end">
                            logs訊息text-end
                        </div>
                    </div>

                </div>
    
                <!-- 尾段：衛材訊息 -->
                <div class="row unblock">
                    <div class="col-12 mb-0">
                        <div style="font-size: 6px;">
                            <?php
                                if($_REQUEST){
                                    echo "<pre>";
                                    // print_r($_REQUEST);
                                    echo "</pre>text-end";
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- toast -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="liveToast" class="toast bg-warning text-dark" role="alert" aria-live="assertive" aria-atomic="true" autohide="true" delay="2000">
                <div class="d-flex">
                    <div class="toast-body" id="toast-body">
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
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
                            <div class="col-6 col-md-4" id="pic_append"></div>
                            <div class="col-6 col-md-8" id="info_append"></div>
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
    
</body>

<!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>

<script>
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
    // 加入購物車清單
    function add_item(cata_SN, add_amount, flag){
        var swal_title = '加入購物車清單';
        // flag=off不顯示swal、其他是預設1秒
        if(flag == 'off'){
            var swal_time = 0;
        }else{
            var swal_time = 1 * 1000;
        }

        if(add_amount <= 0 ){
            var swal_content = cata_SN+' 沒有填數量!'+' 加入失敗';
            var swal_action = 'error';
            swal(swal_title ,swal_content ,swal_action);      // swal需要按鈕確認

        }else{
            var check_item_return = check_item(cata_SN, 0);    // call function 查找已存在的項目，並予以清除。
            Object(catalog).forEach(function(cata){          
                if(cata['SN'] === cata_SN){
                    var input_cb = '<input type="checkbox" name="cata_SN_amount['+cata['SN']+']" id="'+cata['SN']+'" class="select_item" value="'+add_amount+'" checked onchange="check_item(this.id)">';
                    var add_cata_item = '<tr id="item_'+cata['SN']+'"><td>'+input_cb+'</td><td>'+cata['SN']+'</td><td>'+cata['pname']+'</td><td>'+cata['model']+'</td><td>'+cata['size']+'</td><td>'+add_amount+'</td><td>'+cata['unit']+'</td></tr>';
                    $('#shopping_cart_tbody').append(add_cata_item);
                    return;         // 假設每個<cata_SN>只會對應到一筆資料，找到後就可以結束迴圈了
                }
            })
            // 根據check_item_return來決定使用哪個swal型態；true = 有找到數值=更新、false = 沒找到數值=加入
            if(check_item_return){
                var swal_content = ' 更新成功';
                var swal_action = 'info';
            }else{
                var swal_content = ' 加入成功';
                var swal_action = 'success';
            }
            // swal_time>0才顯示swal，主要過濾edit時的渲染導入
            if(swal_time > 0){
                swal(swal_title ,swal_content ,swal_action, {buttons: false, timer:swal_time});        // swal自動關閉
            }
            
        }
        check_shopping_count();        // 清算購物車件數
    }

    // 查找購物車清單已存在的項目，並予以清除
    function check_item(cata_SN, swal_time) {
        // swal_time = 是否啟動swal提示 ： 0 = 不啟動
        if(!swal_time){
            swal_time = 1;
        }
        var shopping_cart_list = document.querySelectorAll('#shopping_cart_tbody > tr');
        if (shopping_cart_list.length > 0) {
            // 使用for迴圈遍歷NodeList，而不是Object.keys()
            for (var i = 0; i < shopping_cart_list.length; i++) {
                var trElement = shopping_cart_list[i];
                if (trElement.id === 'item_' + cata_SN) {
                    // 從父節點中移除指定的<tr>元素
                    trElement.parentNode.removeChild(trElement);
                    if(swal_time != 0){
                        var swal_title = '移除購物車項目';
                        var swal_content = ' 移除成功';
                        var swal_action = 'warning';
                        swal_time = swal_time * 1000;
                        swal(swal_title ,swal_content ,swal_action, {buttons: false, timer:swal_time});        // swal自動關閉
                    }
                    check_shopping_count();
                    return true; // 假設每個<cata_SN>只會對應到一筆資料，找到後就可以結束迴圈了  // true = 有找到數值
                }
            }
        }
        // check_shopping_count();
        return false;       // false = 沒找到數值
    }

    // 清算購物車件數，顯示件數，切換申請單按鈕
    function check_shopping_count(){
        var shopping_cart_list = document.querySelectorAll('#shopping_cart_tbody > tr');
        var nav_review_btn = document.getElementById('nav-review-tab'); 
        $('#shopping_count').empty();
        if(shopping_cart_list.length > 0){
            $('#shopping_count').append(shopping_cart_list.length);
            nav_review_btn.classList.remove('disabled');        // 購物車大於0，取消disabled
        }else{
            nav_review_btn.classList.add('disabled');           // 購物車等於0，disabled
        }
    }

// // // searchUser function 
    // 第一-階段：search Key_word
    function search_fun(search){
        mloading("show");                       // 啟用mLoading

        if(search == 'emp_id'){
            var fun = search;
            search = $('#emp_id').val().trim();
            $('#plant, #dept, #sign_code, #cname, #extp').empty();

        }else{
            var fun = 'in_sign_badge';
            search = search.trim();
            $('#in_sign_badge').empty();

        }

        if(!search || (search.length < 8)){
            alert("查詢工號字數最少 8 個字以上!!");
            $("body").mLoading("hide");
            return false;
        } 

        $.ajax({
            url:'http://tneship.cminl.oa/hrdb/api/index.php',
            method:'get',
            dataType:'json',
            data:{
                functionname: 'search',                     // 操作功能
                uuid: '39aad298-a041-11ed-8ed4-2cfda183ef4f',
                search: search                              // 查詢對象key_word
            },
            success: function(res){
                var res_r = res["result"];
                // 將結果進行渲染
                if (res_r !== '') {
                    var obj_val = res_r[0];                                         // 取Object物件0

                    if(fun == 'emp_id'){     // 搜尋申請人emp_id
                        var input_cname = document.getElementById('cname');
                        var input_plant = document.getElementById('plant');
                        var input_dept = document.getElementById('dept');
                        var input_sign_code = document.getElementById('sign_code');
                        var input_extp = document.getElementById('extp');
                        if(obj_val){
                            if(obj_val.dept_d){                        // 位差判斷填入
                                input_plant.value = obj_val.dept_c;    // 將欄位帶入數值 = dept_c 部
                                input_dept.value = obj_val.dept_d;     // 將欄位帶入數值 = dept_d 課
                            }else{
                                input_plant.value = obj_val.dept_b;    // 將欄位帶入數值 = dept_b 處
                                input_dept.value = obj_val.dept_c;     // 將欄位帶入數值 = dept_c 部
                            }
                            input_sign_code.value = obj_val.dept_no;   // 將欄位帶入數值 = dept_no 部門代號
                            input_cname.value = obj_val.cname;         // 將欄位帶入數值 = cname
                            if(obj_val.extp){
                                input_extp.value = obj_val.extp;       // 將欄位帶入數值 = extp
                            }else{
                                input_extp.value = '';
                            }
                            var sinn = '以工號&nbsp<b>'+obj_val.emp_id+'/'+obj_val.cname+'</b>&nbsp帶入資訊...完成!!';
                            inside_toast(sinn);
    
                        }else{
                            // alert("查無工號["+search+"]!!");
                            input_cname.value = '';                         // 將欄位cname清除
                            input_plant.value = '';
                            input_dept.value = '';
                            input_sign_code.value = '';
                            input_extp.value = '';
                            var sinn = '查無工號&nbsp<b>'+ search +'</b>&nbsp!!';
                            inside_toast(sinn);
                        }
                        
                    }else{                    // 搜尋申請人上層主管emp_id
                        
                        if(obj_val){                            
                            // $('#in_sign_badge').append(obj_val.cname);
                            $('#in_sign_badge').append('<div class="tag">' + obj_val.cname + '<span class="remove">x</span></div>');
                            var sinn = '以工號&nbsp<b>'+obj_val.emp_id+'/'+obj_val.cname+'</b>&nbsp帶入上層主管資訊...完成!!';
                            inside_toast(sinn);
    
                        }else{
                            // alert("查無工號["+search+"]!!");
                            document.getElementById('in_sign').value = '';                         // 將欄位cname清除
                            var sinn = '查無工號&nbsp<b>'+ search +'</b>&nbsp!!';
                            inside_toast(sinn);
                        }
                    }
                }
            },
            error (){
                console.log("search error");
            }
        })
        $("body").mLoading("hide");
    }

    function inside_toast(sinn){
        // init toast
        var toastLiveExample = document.getElementById('liveToast');
        var toast = new bootstrap.Toast(toastLiveExample);
        var toast_body = document.getElementById('toast-body');
        toast_body.innerHTML = sinn;
        toast.show();

    }
    // 第二階段：移除單項模組
    $('#in_sign_badge').on('click', '.remove', function() {
        $(this).closest('.tag').remove();   // 自畫面中移除
        document.getElementById('in_sign').value = '';                         // 將欄位cname清除
        // $('#in_sign_badge').empty();
    });
// // // searchUser function 
    
// // // Edit選染
    var action = '<?=$action;?>';                       // 引入action資料
    function edit_item(){
        var receive_row = <?=json_encode($receive_row);?>;                        // 引入receive_row資料作為Edit
        var receive_item = {
            "plant"          : "plant/申請單位", 
            "dept"           : "dept/部門名稱", 
            "sign_code"      : "sign_code/部門代號", 
            "emp_id"         : "emp_id/工號",
            "cname"          : "cname/申請人姓名",
            "extp"           : "extp/分機",
            "local_id"       : "local_id/領用站點",
            "ppty"           : "** ppty/需求類別",
            "in_sign"        : "** in_sign/上層主管工號",
            "receive_remark" : "receive_remark/用途說明",
            "created_emp_id" : "created_emp_id/開單人工號",
            "created_cname"  : "created_cname/開單人姓名",
            // "idty"           : "idty",
            "uuid"           : "uuid",
            "cata_SN_amount" : "** cata_SN_amount"
            // "sign_comm"       : "command/簽核comm",
        };    // 定義要抓的key=>value
        // step1.將原排程陣列逐筆繞出來
        Object.keys(receive_item).forEach(function(receive_key){
            if(receive_key == 'ppty'){      // ppty/需求類別
                document.querySelector('#'+receive_key+'_'+receive_row[receive_key]).checked = true;
                
            }else if(receive_key == 'cata_SN_amount'){      //cata_SN_amount 購物車
                var receive_row_cart = JSON.parse(receive_row[receive_key]);
                Object.keys(receive_row_cart).forEach(function(cart_key){
                    add_item(cart_key, receive_row_cart[cart_key], 'off');
                })

            }else if(receive_key == 'in_sign'){             //in_sign 上層主管工號
                document.getElementById('in_sign').value = receive_row[receive_key];
                search_fun(receive_row[receive_key]);

            }else{
                document.querySelector('#'+receive_key).value = receive_row[receive_key]; 

            }
        })

        // 鋪設logs紀錄
        var json = JSON.parse('<?=json_encode($logs_arr)?>');
        var uuid = '<?=$receive_row["uuid"]?>';
        var forTable = document.querySelector('.logs tbody');
        for (var i = 0, len = json.length; i < len; i++) {
            forTable.innerHTML += 
                '<tr>' + '<td>' + json[i].step + '</td><td>' + json[i].cname + '</td><td>' + json[i].datetime + '</td><td>' + json[i].action + '</td><td>' + json[i].remark + '</td>' +
                    '<?php if($_SESSION[$sys_id]["role"] <= 1){ ?><td>' + '<form action="" method="post">'+
                        `<input type="hidden" name="log_id" value="` + [i] + `";>` +
                        `<input type="hidden" name="uuid" value="` + uuid + `";>` +
                        `<input type="submit" name="delete_log" value="刪除" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">` +
                    '</form>' + '</td><?php } ?>' +
                '</tr>';
        }
        document.getElementById('logs_div').classList.remove('unblock');           // 購物車等於0，disabled

    }


    // All resources finished loading! // 關閉mLoading提示
    window.addEventListener("load", function(event) {
        $("body").mLoading("hide");
    });


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

        // <!-- 有填數量自動帶入+號按鈕，沒數量自動清除+號按鈕的value -->
        let amounts = [...document.querySelectorAll('.amount')];
        for(let amount of amounts){
            amount.onchange = e => {
                let amount_id = e.target.id;
                if(amount.value == ''){
                    // document.getElementById('catalog_SN_'+ amount_id).checked=false;     // 取消選取 = 停用
                    document.getElementById('add_'+ amount_id).value = '';
                } else {
                    // document.getElementById('catalog_SN_'+ amount_id).checked=true;      // 增加選取 = 停用
                    document.getElementById('add_'+ amount_id).value = amount.value;
                }
            }
        }

    })

    // 20230817 禁用Enter鍵表單自動提交 
    document.onkeydown = function(event) { 
        var target, code, tag; 
        if (!event) { 
            event = window.event;       //針對ie瀏覽器 
            target = event.srcElement; 
            code = event.keyCode; 
            if (code == 13) { 
                tag = target.tagName; 
                if (tag == "TEXTAREA") { return true; } 
                else { return false; } 
            } 
        } else { 
            target = event.target;      //針對遵循w3c標準的瀏覽器，如Firefox 
            code = event.keyCode; 
            if (code == 13) { 
                tag = target.tagName; 
                if (tag == "INPUT") { return false; } 
                else { return true; } 
            } 
        } 
    };

    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
    
    // 確認action不是新表單，就進行Edit模式渲染
    if(action != 'create'){                                // 確認action不是新表單，就進行Edit模式渲染
        edit_item();
        $('.nav-tabs button:eq(1)').tab('show');        // 切換頁面到購物車
    }

</script>

<?php include("../template/footer.php"); ?>