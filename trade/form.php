<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

        // 刪除表單
        if(!empty($_POST["delete_trade"])){
            $check_delete_result = delete_trade($_REQUEST);
            if($check_delete_result){
                echo "<script>alert('trade批量調撥單 -- 已刪除');</script>";
                header("refresh:0;url=index.php");
                exit;		// 請注意
            }else{
                echo "<script>alert('trade批量調撥單 -- 刪除失敗!!');</script>";
            }
        }
        // 更新log
        if(!empty($_POST["delete_log"])){
            updateLogs($_REQUEST);
        }

    // 決定表單開啟方式
    if(!empty($_REQUEST["action"])){
        $action = $_REQUEST["action"];              // 有action就帶action
    }else{
        $action = 'create';                         // 沒有action就新開單
    }

    if(!empty($_REQUEST["id"])){                    // 編輯 或 閱讀時
        $trade_row = show_trade($_REQUEST);
        if(empty($trade_row)){                      // 防呆
            echo "<script>alert('id-error：{$_REQUEST["id"]}')</script>";
            header("refresh:0;url=index.php");
            exit;
        }
        // logs紀錄鋪設前處理 
        $logs_dec = json_decode($trade_row["logs"]);
        $logs_arr = (array) $logs_dec;

    }else{
        $trade_row = array( "id" => "" );           // 預設trade_row[id]=空array
        $logs_arr = [];                             // 預設logs_arr=空array
        $action = 'create';                         // 因為沒有id，列為新開單，防止action outOfspc
    }

    if(!empty($_POST["local_id"])){                 // create時，送出已選擇出庫廠區站點
        // $_REQUEST["local_id"] = $_POST["local_id"];
        $select_local = select_local($_REQUEST);    // 讀出已被選擇出庫廠區站點的器材存量限制buy_ty
        $buy_ty = $select_local["buy_ty"];
        $catalogs = show_local_stock($_REQUEST);    // 後來改用這個讀取catalog清單外加該local的儲存量，作為需求首頁目錄
    
    }else if(!empty($trade_row["out_local"])){       // edit trade表單，get已選擇出庫廠區站點
        $query_local = array(
            'local_id' => $trade_row["out_local"]
        );
        $select_local = select_local($query_local);
        $buy_ty = $select_local["buy_ty"];
        // $catalogs = read_local_stock($query_local);    // 後來改用這個讀取catalog清單外加該local的儲存量，作為需求首頁目錄
        $catalogs = show_local_stock($query_local);    // 後來改用這個讀取catalog清單外加該local的儲存量，作為需求首頁目錄

    }else{
        $select_local = array(
            'id' => ''
        );
        $catalogs = [];
    }

    $allcatalogs = show_catalogs();                 // 後來改用這個讀取catalog清單，作為需求首頁目錄 for Edit時，假如原儲存項目已消失
    $allLocals = show_allLocal();                   // 所有儲存站點

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
        #checkEye {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
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
        // All resources finished loading! // 關閉mLoading提示
        window.addEventListener("load", function(event) {
            $("body").mLoading("hide");
        });
        // 畫面載入時開啟loading
        mloading();
    </script>
</head>

<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-11 border rounded px-3 py-4" style="background-color: #D4D4D4;">
                <!-- 表頭1 -->
                <div class="row px-2">
                    <div class="col-12 col-md-6 py-0">
                        <h3><i class="fa-solid fa-2"></i>&nbsp<b>調撥出庫</b><?php echo empty($action) ? "":" - ".$action;?></h3>
                    </div>
                    <div class="col-12 col-md-6 py-0 text-end">
                        <!-- <a href="index.php" class="btn btn-success"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回總表</a> -->
                        <a href="index.php" class="btn btn-danger" onclick="return confirm('確認返回？');" ><i class="fa fa-external-link" aria-hidden="true"></i> 返回</a>
                    </div>
                </div>

                <div class="row px-2">
                    <div class="col-12 col-md-6">
                        調撥單號：<?php echo ($action == 'create') ? "(尚未給號)": "aid_".$trade_row['id']; ?></br>
                        開單日期：<?php echo ($action == 'create') ? date('Y-m-d H:i')."&nbsp(實際以送出時間為主)":$trade_row['out_date']; ?></br>
                        填單人員：<?php echo ($action == 'create') ? $_SESSION["AUTH"]["emp_id"]." / ".$_SESSION["AUTH"]["cname"] : $trade_row["out_user_id"]." / ".$trade_row["cname_o"] ;?>
                    </div>
                    <div class="col-12 col-md-6 text-end">
                        <!-- 表頭：右側上=選擇出庫廠區 -->
                        <form action="" method="post">
                            <div class="form-floating">
                                <select name="local_id" id="select_local_id" class="form-control" required style='width:80%;' onchange="this.form.submit()">
                                    <option value="" hidden>--請選擇 出貨 儲存點--</option>
                                    <?php foreach($allLocals as $allLocal){ ?>
                                        <?php if($_SESSION[$sys_id]["role"] <= 1 || $allLocal["fab_id"] == $_SESSION[$sys_id]["fab_id"] || (in_array($allLocal["fab_id"], $_SESSION[$sys_id]["sfab_id"]))){ ?>  
                                            <option value="<?php echo $allLocal["id"];?>" title="<?php echo $allLocal["fab_title"];?>" <?php echo $allLocal["id"] == $select_local["id"] ? "selected":""; ?>>
                                                <?php echo $allLocal["id"]."：".$allLocal["site_title"]."&nbsp".$allLocal["fab_title"]."_".$allLocal["local_title"]; if($allLocal["flag"] == "Off"){ ?>(已關閉)<?php }?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <label for="select_local_id" class="form-label">out_local/出貨廠區：<sup class="text-danger"> *</sup></label>
                            </div>
                        </form>

                        <!-- 表單狀態限制在'3作廢'才可以刪除 -->
                        <?php if(($_SESSION[$sys_id]["role"] <= 1 ) && (isset($trade_row['idty']) && $trade_row['idty'] === 3)){ ?>
                            <form action="" method="post">
                                <input type="hidden" name="id" value="<?php echo $trade_row["id"];?>">
                                <input type="submit" name="delete_trade" value="刪除" title="刪除調撥單" class="btn btn-danger" onclick="return confirm('確認徹底刪除此單？')">
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
                                aria-controls="nav-review" aria-selected="false">3.調撥單成立</button>
                        </div>
                    </nav>
                    <!-- 內頁 -->
                    <form action="store.php" method="post">
                    <!-- <form action="#" method="post"> -->
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
                                                <th>分類/PO no/批號</th>
                                                <th>尺寸</th>
                                                <th>需求</th>
                                                <th><i class="fa-solid fa-cart-plus"></i>&nbsp購物車</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($catalogs as $catalog){ ?>
                                                <tr>
                                                    <td class="unblock"><?php echo $catalog["cate_no"];?></td>
                                                    <td title="<?php echo 'stk_id:'.$catalog["stk_id"];?>"><img src="../catalog/images/<?php echo $catalog["PIC"];?>" class="img-thumbnail"></td>
                                                    <td style="text-align: left;">
                                                        <button type="button" id="cata_info_<?php echo $catalog['SN'];?>" value="<?php echo $catalog["SN"].'_'.$catalog['stk_id'];?>" data-bs-toggle="modal" data-bs-target="#cata_info" 
                                                                class="cata_info_btn" onclick="info_module('catalog',this.value);"><h5><b><?php echo $catalog["pname"];?></b></h5></button>
                                                        <?php 
                                                            echo $catalog["SN"] ? '</br>SN：'.$catalog["SN"]:'</br>';
                                                            echo $catalog["cata_remark"] ? '</br>敘述：'.$catalog["cata_remark"]:'</br>';?>
                                                    </td>
                                                    <td style="text-align: left; word-break: break-all;">
                                                        <span class="badge rounded-pill 
                                                            <?php switch($catalog["cate_id"]){
                                                                    case "1": echo "bg-primary"; break;
                                                                    case "2": echo "bg-success"; break;
                                                                    case "3": echo "bg-warning text-dark"; break;
                                                                    case "4": echo "bg-danger"; break;
                                                                    case "5": echo "bg-info text-dark"; break;
                                                                    case "6": echo "bg-dark"; break;
                                                                    case "7": echo "bg-secondary"; break;
                                                                    default: echo "bg-light text-success"; break;
                                                                }?>"><?php echo $catalog["cate_no"].".".$catalog["cate_title"];?></span>
                                                        <?php
                                                            echo $catalog["po_no"] ? "</br>po_no：".$catalog["po_no"]:"--";
                                                            echo $catalog["lot_num"] ? "</br>批號/效期：".$catalog["lot_num"]:"--";?>
                                                    </td>
                                                    <td style="text-align: left; word-break: break-all;">
                                                        <?php 
                                                            echo $catalog["size"] ? "尺寸：".$catalog["size"]:"--";
                                                            echo $catalog["unit"] ? "</br>單位：".$catalog["unit"]:"";
                                                            echo $catalog["OBM"] ? "</br>品牌/製造商：".$catalog["OBM"]:"";
                                                            echo $catalog["model"] ? "</br>型號：".$catalog["model"]:""; 
                                                        ?></td>
                                                    <td>
                                                        <div class="col-12 text-center py-0 " style="color:<?php echo ($catalog['amount'] <= $catalog['standard_lv']) ? "red":"blue";?>">
                                                                <b><?php echo "安全: "; echo (!empty($catalog["standard_lv"])) ? $catalog["standard_lv"]:"0";
                                                                         echo " / 存量: "; echo (!empty($catalog["amount"])) ? $catalog["amount"]:"0"; ?></b>
                                                        </div>
                                                        <input type="number" id="<?php echo $catalog['SN'].'_'.$catalog['stk_id'];?>" class="form-control amount t-center"
                                                            placeholder="上限： <?php echo $catalog['amount']."&nbsp/&nbsp".$catalog["unit"]; $buy_qty = $catalog['amount'];?>" 
                                                            min="<?php echo $buy_qty == 0 ? '0':'1';?>" max="<?php echo $buy_qty;?>" maxlength="<?php echo strlen($buy_qty);?>" 
                                                            oninput="if(value.length><?php echo strlen($buy_qty);?>)value=value.slice(0,<?php echo strlen($buy_qty);?>)"
                                                            onblur="if(value >= <?php echo $buy_qty;?>)value=<?php echo $buy_qty;?>; add_cart_btn(this.id, this.value);" >
                                                    </td>
                                                    <td>
                                                        <button type="button" name="<?php echo $catalog['SN'].'_'.$catalog['stk_id'];?>" id="<?php echo 'add_'.$catalog['SN'].'_'.$catalog['stk_id'];?>" 
                                                                class="add_btn" value="" title="加入購物車" onclick="add_item(this.name, this.value, 'off');"><h5><i class="fa-regular fa-square-plus"></i></h5></button>
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
                                                    <th>型號</th>
                                                    <th>尺寸</th>
                                                    <th>數量</th>
                                                    <th>單位</th>
                                                    <th>PO no</th>
                                                    <th>批號/效期</th>
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
    
                                    <!-- 表列1 請購需求單站點 -->
                                    <div class="row">
                                        <!-- 表頭：右側上=選擇出庫廠區 -->
                                        <div class="col-12 col-md-6 py-3 px-2">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" readonly
                                                    value="<?php echo $select_local['id'].'：'.$select_local['site_title'].' '.$select_local['fab_title'].'_'.$select_local['local_title']; 
                                                            echo ($select_local['flag'] == 'Off') ? '(已關閉)':''; ?>">
                                                <label for="out_local" class="form-label">out_local/出庫廠區：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>
                                        <!-- 表頭：右側下=選擇入庫廠區 -->
                                        <div class="col-12 col-md-6 py-3 px-2">
                                            <div class="form-floating">
                                                <select name="in_local" id="in_local" class="form-select" required >
                                                    <option value="" hidden>--請選擇 入庫 儲存點--</option>
                                                    <?php foreach($allLocals as $allLocal){ ?>
                                                        <?php if($allLocal["id"] != $select_local["id"]){?>
                                                            <option value="<?php echo $allLocal["id"];?>" title="<?php echo $allLocal["site_title"];?>" >
                                                                <?php echo $allLocal["id"];?>:<?php echo $allLocal["site_title"];?>&nbsp<?php echo $allLocal["fab_title"];?>_<?php echo $allLocal["local_title"];?><?php if($allLocal["flag"] == "Off"){ ?>(已關閉)<?php }?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                                <label for="in_local" class="form-label">in_local/入庫廠區：<sup class="text-danger"> *</sup></label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 表列5 說明 -->
                                    <div class="row">
                                        <hr>
                                        <div class="col-12 py-1">
                                            備註：
                                            </br>&nbsp1.填入申請人工號、姓名、需求廠區、需求類別、器材數量。
                                            </br>&nbsp2.簽核：申請人=>承辦人=>PR待轉=>轉PR=>表單結案。 
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <hr>
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
                                        <h5 class="modal-title">Do you submit this 批量調撥：</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body px-5">
                                        <label for="sign_comm" class="form-check-label" >command：</label>
                                        <textarea name="sign_comm" id="sign_comm" class="form-control" rows="5"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="updated_user" id="updated_user"  value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                        <input type="hidden" name="cname"                           value="<?php echo $_SESSION["AUTH"]["cname"];?>">   <!-- cname/出庫填單人cname -->
                                        <input type="hidden" name="out_user_id"                     value="<?php echo $_SESSION["AUTH"]["emp_id"];?>">  <!-- out_user_id/出庫填單人emp_id -->
                                        <input type="hidden" name="out_local"                       value="<?php echo $select_local["id"];?>">          <!-- out_local/出庫廠區 -->    
                                        <input type="hidden" name="form_type"   id="form_type"      value="export">
                                        <input type="hidden" name="action"      id="action"         value="<?php echo $action;?>">
                                        <input type="hidden" name="idty"        id="idty"           value="1">
                                        <input type="hidden" name="id"          id="id"             value="">
                                        <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                                            <button type="submit" value="1" name="trade_submit" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> 送出 (Submit)</button>
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
                <div class="row block">
                    <div class="col-12 mb-0">
                        <div style="font-size: 6px;">
                            <?php
                                if($_REQUEST){
                                    echo "<pre>";
                                    print_r($_REQUEST);
                                    print_r($trade_row);
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
    var action = '<?=$action;?>';                        // 引入action資料
    var catalogs = <?=json_encode($catalogs);?>;         // 引入catalogs資料
    var allcatalogs = <?=json_encode($allcatalogs);?>;   // 引入allcatalogs資料
    var trade_row = <?=json_encode($trade_row);?>;                        // 引入trade_row資料作為Edit

</script>

<script src="trade_form.js?v=<?=time();?>"></script>

<?php include("../template/footer.php"); ?>