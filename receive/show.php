<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../user_info.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 複製本頁網址藥用
    $receive_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];                 // 回本頁
     
    // 4.組合我的廠區到$sys_sfab_id => 包含原sfab_id、fab_id和sign_code所涵蓋的廠區
    $sys_sfab_id = get_sfab_id($sys_id, "arr");

    // 決定表單開啟方式 // 有action就帶action，沒有action就新開單
    $action = (isset($_REQUEST["action"])) ? $_REQUEST["action"] : 'review';                         

    if(!empty($_REQUEST["uuid"])){
        $receive_row = show_receive($_REQUEST);
        if(empty($receive_row)){
            echo "<script>alert('uuid-error：{$_REQUEST["uuid"]}')</script>";
            header("refresh:0;url={$up_href}");
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

    if(!empty($receive_row["local_id"])){           // edit trade表單，get已選擇出庫廠區站點
        $query_local = array(
            'local_id' => $receive_row["local_id"]
        );
        $select_local = select_local($query_local);   // 讀出已被選擇出庫廠區站點

    }else{
        $select_local = array('id' => '');
    }
    $pm_emp_id = $receive_row["pm_emp_id"];         // *** 廠區業務窗口
    $pm_emp_id_arr = explode(",",$pm_emp_id);       // 資料表是字串，要炸成陣列

    $catalogs = show_catalogs();                    // 器材=All

    require_once("function_step.php");              // 呼叫身份處理功能，並取得$step套用身份

?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <link href="../../libs/aos/aos.css" rel="stylesheet">                                   <!-- goTop滾動畫面aos.css 1/4-->
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>    <!-- Jquery -->
    <script src="../../libs/sweetalert/sweetalert.min.js"></script>                         <!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>                            <!-- mloading JS 1/3 -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">                    <!-- mloading CSS 2/3 -->
    <script src="../../libs/jquery/mloading_init.js"></script>                              <!-- mLoading_init.js 3/3 -->
    <style>
        .inb {
                display: inline-block;
            }
    </style>
</head>

<body>
    <?php $sys_sfab_id = get_sfab_id($sys_id, "arr");     // 240125-這裡補上防空值 ?>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-12 border rounded px-2 py-3" style="background-color: #D4D4D4;">
                <!-- 表頭1 -->
                <div class="row px-2">
                    <div class="col-12 col-md-4 py-0">
                        <h3><i class="fa-solid fa-3"></i>&nbsp<b>領用申請</b><?php echo empty($action) ? "":" - ".$action;?></h3>
                    </div>
                    <div class="col-12 col-md-4 py-0 t-center">
                        <?php 
                            echo "<h3><span class='badge rounded-pill ";
                                switch($receive_row['idty']){
                                    case "0" : echo "bg-info'>待領";                break;
                                    case "1" : echo "bg-primary'>待簽";             break;
                                    case "2" : echo "bg-warning text-dark'>退回";   break;
                                    case "3" : echo "bg-dark'>取消";                break;
                                    case "10": echo "bg-secondary'>結案";           break;
                                    case "11": echo "bg-primary'>待簽";             break;
                                    case "12": echo "bg-success'>待收";             break;
                                    case "13": echo "bg-danger'>待簽";              break;
                                    default  : echo "'>na";                         break; 
                                }
                            echo !empty($receive_row['in_signName']) ? "：".$receive_row['in_signName']." " :"";

                            if($receive_row['idty'] == 10 && !empty($receive_row['flow'])){
                                $flow_arr = explode(",", $receive_row['flow']);
                                echo " / ".end($flow_arr)." ";
                            }else{
                                echo " / ".$receive_row['flow']." ";
                            }
                            echo "</span></h3>";
                        ?>
                    </div>
                    <div class="col-12 col-md-4 py-0 text-end">
                        <!-- 指派簽核主管 -->
                        <div class="inb">
                            <button type="button" id="assignSign_btn" class="btn btn-warning <?php echo ($sys_role <= 0) ? '':'disabled unblock';?>" data-bs-toggle="modal" data-bs-target="#assignSignModal"><i class="fa-solid fa-user-tie"></i> 指派簽核</button>
                            <snap id="newSign"></snap>
                        </div>
                        <!-- <button type="button" class="btn btn-secondary" onclick="location.href='index.php'"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回首頁</button> -->
                        <!-- <button type="button" class="btn btn-secondary" onclick="location.href='<php echo $up_href;?>'"><i class="fa fa-external-link" aria-hidden="true"></i>&nbsp回上頁</button> -->
                        <button type="button" class="btn btn-secondary rtn_btn" onclick="closeWindow()"><i class="fa fa-caret-up" aria-hidden="true"></i>&nbsp回首頁</button>
                    </div>
                </div>

                <div class="row px-2">
                    <div class="col-12 col-md-4">
                        需求單號：<?php echo ($receive_row['id'])             ? "receive_aid_".$receive_row['id'] : "(尚未給號)";?></br>
                        開單日期：<?php echo ($receive_row['created_at'])     ? $receive_row['created_at'] : date('Y-m-d H:i')."&nbsp(實際以送出時間為主)";?></br>
                        填單人員：<?php echo ($receive_row["created_emp_id"]) ? $receive_row["created_emp_id"]." / ".$receive_row["created_cname"] : $auth_emp_id." / ".$auth_cname;?>
                    </div>
                    <div class="col-12 col-md-8 text-end" id="form_btn_div">
                        <?php
                            $let_btn_s = '<button type="button" class="btn ';
                            $let_btn_m = '" data-bs-toggle="modal" data-bs-target="#submitModal" value="';
                            $let_btn_e = '" onclick="submit_item(this.value, this.innerHTML);">';
                        
                            if( (($receive_row['idty'] == 1) && ($receive_row['in_sign'] == $auth_emp_id)) || $sys_role <= 1 ){ 
                                if(in_array($receive_row['idty'], [ 1 ])){ // 1.簽核中
                                    echo $let_btn_s."btn-success".$let_btn_m."0".$let_btn_e."同意 (Approve)</button> ";
                                    if( ($receive_row["flow"] != "forward")  ){  
                                        echo $let_btn_s."btn-info".$let_btn_m."5".$let_btn_e."轉呈 (Forwarded)</button> ";
                                    }
                                }
                            } 
                            if((in_array($receive_row['idty'], [ 1, 12 ])) && ((in_array($receive_row["fab_id"], $sys_sfab_id) && $sys_role <= 2 ) || ($receive_row['in_sign'] == $auth_emp_id))){ // 1.簽核中 12.待領
                                echo $let_btn_s."btn-danger".$let_btn_m."2".$let_btn_e."退回 (Reject)</button> ";
                            }
                            // 這裡取得發放權限 idty=12.待領、待收 => 13.交貨 (Delivery)
                                $receive_collect_role = (($receive_row['idty'] == 12) && ($receive_row['flow'] == 'collect') && (in_array($receive_row["fab_id"], $sys_sfab_id))); 
                                if($receive_collect_role){ 
                                    echo $let_btn_s."btn-primary".$let_btn_m."13".$let_btn_e."交貨 (Delivery)</button> ";
                                }  
                            // 承辦+主管簽核選項 idty=13.交貨delivery => 11.承辦簽核 (Undertake)
                                $receive_delivery_role = ($receive_row['flow'] == 'PPEpm' && (in_array($auth_emp_id, $pm_emp_id_arr) || $sys_role <= 1));
                                if($receive_row['idty'] == 13 && $receive_delivery_role){  
                                    echo $let_btn_s."btn-primary".$let_btn_m."11".$let_btn_e."承辦同意 (Approve)</button> ";
                                } 
                            // 承辦+主管簽核選項 idty=11.承辦簽核 => 10.結案 (Close)
                                if( $receive_row['idty'] == 11 && ( $receive_row['in_sign'] == $auth_emp_id || $sys_role <= 0 )){ 
                                    echo $let_btn_s."btn-primary".$let_btn_m."10".$let_btn_e."主管同意 (Approve)</button> ";
                                } 
                            // 20240429 承辦退貨選項 idty=10.同意退貨 => 2.結案 (Close)
                            if( $receive_row['idty'] == 10 && ((in_array($receive_row["fab_id"], $sys_sfab_id)) && $sys_role <= 2 )){ 
                                echo $let_btn_s.'btn-danger" id="return_btn" onclick="return_the_goods()">退貨 (Return)</button> ';
                            }
                         ?>
                    </div>
                </div>
    
                <!-- container -->
                <div class="col-12 p-0">
                    <!-- 內頁 -->
                    <form action="store.php" method="post" >
                    <!-- <form action="zz/debug.php" method="post" > -->
                                            
                        <!-- 3.申請單成立 -->
                        <div class="tab-pane bg-white rounded fade show active" id="nav-review" role="tabpanel" aria-labelledby="nav-review-tab">
                            <div class="col-12 py-2 px-4">
                                <div class="row">
                                    <!-- 表頭 -->
                                    <div class="col-6 col-md-6">
                                        <b>申請人相關資訊：</b>
                                        <button type="button" id="info_btn" class="op_tab_btn" value="info" onclick="op_tab(this.value)" title="訊息收折"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                                    </div>
                                    <div class="col-6 col-md-6 text-end">
                                        <!-- 限定表單所有人：created_emp_id開單人、emp_id申請人、ppe pm、admin -->
                                        <?php 
                                            if( ($receive_row['created_emp_id'] == $auth_emp_id) || ($receive_row['emp_id'] == $auth_emp_id) || ($sys_role <= 1) ){
                                                // 表單狀態：2退回 4編輯 6暫存
                                                if(in_array($receive_row['idty'], [ 2, 4, 6 ])){ 
                                                    echo "<a href='form.php?uuid={$receive_row['uuid']}&action=edit'  class='btn btn-primary'>編輯 (Edit)</a> ";
                                                }
                                                // 表單狀態：1送出 2退回 4編輯 5轉呈 6暫存
                                                if(in_array($receive_row['idty'], [ 1, 2, 4, 5, 6 ])){ 
                                                    echo $let_btn_s."bg-warning text-dark".$let_btn_m."3".$let_btn_e."作廢 (Abort)</button> ";
                                                } 
                                            } 
                                            if($receive_row['idty'] == 12 && $receive_row['flow'] == 'collect'  // 12.待領、待收
                                                        && (in_array($receive_row["fab_id"], $sys_sfab_id) || in_array($auth_emp_id, [$receive_row['emp_id'], $receive_row['created_emp_id']])) ){
                                                echo $let_btn_s.'btn-success" '." onclick='push_mapp({$auth_emp_id})' data-toggle='tooltip' data-placement='bottom' title='mapp給自己'><i class='fa-brands fa-facebook-messenger'></i> 推送 (Push)</button> ";
                                            } 
                                        ?>
                                    </div>
                                    <hr>
                                    <!-- 相關資訊說明 -->
                                    <div class="col-12 py-1" id="info_table"> 

                                        <!-- 表列1 申請人 -->
                                        <div class="row">
                                            <div class="col-6 col-md-4 py-1 px-2">
                                                <div class="form-floating">
                                                    <input type="text" name="emp_id" id="emp_id" class="form-control" required placeholder="工號" value="<?php echo $receive_row["emp_id"];?>" readonly >
                                                    <label for="emp_id" class="form-label">emp_id/工號：<sup class="text-danger"> *</sup></label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4 py-1 px-2">
                                                <div class="form-floating">
                                                    <input type="text" name="cname" id="cname" class="form-control" required placeholder="申請人姓名" value="<?php echo $receive_row["cname"];?>" readonly >
                                                    <label for="cname" class="form-label">cname/申請人姓名：<sup class="text-danger"> *</sup></label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4 py-1 px-2">
                                                <div class="form-floating">
                                                    <input type="text" name="extp" id="extp" class="form-control" required placeholder="分機" readonly >
                                                    <label for="extp" class="form-label">extp/分機：<sup class="text-danger"> *</sup></label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- 表列2 申請單位 -->
                                        <div class="row">
                                            <div class="col-6 col-md-4 py-1 px-2">
                                                <div class="form-floating">
                                                    <input type="text" name="plant" id="plant" class="form-control" required placeholder="申請單位" readonly >
                                                    <label for="plant" class="form-label">plant/申請單位：<sup class="text-danger"> *</sup></label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4 py-1 px-2">
                                                <div class="form-floating">
                                                    <input type="text" name="dept" id="dept" class="form-control" required placeholder="部門名稱" readonly >
                                                    <label for="dept" class="form-label">dept/部門名稱：<sup class="text-danger"> *</sup></label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4 py-1 px-2">
                                                <div class="form-floating">
                                                    <input type="text" name="sign_code" id="sign_code" class="form-control" required placeholder="部門代號" readonly >
                                                    <label for="sign_code" class="form-label">sign_code/部門代號：<sup class="text-danger"> *</sup></label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- 表列3 領用站點 -->
                                        <div class="row">
                                            <div class="col-6 col-md-4 py-1 px-2">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="local_id" readonly
                                                        value="<?php echo $select_local['id'].'：'.$select_local['fab_title'].'_'.$select_local['local_title']; 
                                                            echo ($select_local['flag'] == 'Off') ? '(已關閉)':''; ?>">
                                                    <label for="local_id" class="form-label">local_id/領用站點：<sup class="text-danger"> *</sup></label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4 py-1 px-2">
                                                <div style="display: flex;">
                                                    <span for="ppty" class="form-label">ppty/需求類別：</span></br>&nbsp
                                                    <input type="radio" name="ppty" value="1" id="ppty_1" class="form-check-input" required disabled>
                                                    <label for="ppty_1" class="form-check-label">&nbsp一般&nbsp&nbsp</label>
                                                    <input type="radio" name="ppty" value="3" id="ppty_3" class="form-check-input" required disabled>
                                                    <label for="ppty_3" class="form-check-label" data-toggle="tooltip" data-placement="bottom" title="注意：事故須先通報防災!!">&nbsp緊急</label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4 py-1 px-2">
                                                <div class="form-floating">
                                                    <input type="text" name="omager" id="omager" class="form-control" required disabled placeholder="主管工號">
                                                    <label for="omager" class="form-label">omager/主管工號：<sup class="text-danger"> *</sup></label>
                                                    <div id="omager_badge"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 表列5 說明 -->
                                        <div class="row">
                                            <div class="col-12 col-md-12 py-2 px-2">
                                                <div class="form-floating">
                                                    <textarea name="receive_remark" id="receive_remark" class="form-control" style="height: 150px;" placeholder="(由申請單位填寫用品/器材請領原由)" disabled></textarea>
                                                    <label for="receive_remark" class="form-label">receive_remark/用途說明：<sup class="text-danger"> * (由申請單位填寫用品/器材請領原由)</sup></label>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="col-12 py-1">
                                                <b>備註：</b>
                                                </br>&nbsp1.填入申請單位、部門名稱、申請日期、器材、數量及用途說明。
                                                </br>&nbsp2.簽核：申請人(送出)=>申請部門三級主管=>發放人及領用人(發放)=>環安單位承辦人=>環安單位(課)主管=>各廠環安單位存查3年(結案)。 
                                                </br>&nbsp3.需求類別若是[緊急]，必須說明事故原因，並通報防災中心。 
                                                </br>&nbsp4.以上若有填報不實，將於以退件。 
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>

                                <!-- 表列4 購物車 -->
                                <div class="row">
                                    <div class="col-12 border rounded bg-info">
                                        <span class="form-label">器材用品/數量單位：&nbsp<span id="shopping_count" class="badge rounded-pill bg-danger"></span></span>
                                        <div class=" rounded border bg-light" id="shopping_cart">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>select</th>
                                                        <th style="text-align: left;">SN / 品名</th>
                                                        <th>型號</th>
                                                        <th>尺寸</th>
                                                        <th>申請量 / 單位</th>
                                                        <th>實發量</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="shopping_cart_tbody">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div style="font-size: 12px;" class="py-2 text-end"></div>
                                </div>

                            </div>
                        </div>

                        <!-- 模組 submitModal-->
                        <div class="modal fade" id="submitModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable ">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Do you submit this：<span id="idty_title"></span>&nbsp?</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    
                                    <div class="modal-body px-3">
                                        <!-- 第二排的功能 : 搜尋功能 -->
                                        <div class="row unblock" id="forwarded">
                                            <div class="col-12" id="searchUser_table">
                                                <div class="input-group search" id="select_inSign_Form">
                                                    <span class="input-group-text form-label">轉呈</span>
                                                    <input type="text" name="in_sign" id="in_sign" class="form-control" placeholder="請輸入工號"
                                                            aria-label="請輸入查詢對象工號" onchange="search_fun(this.id, this.value);">
                                                    <div id="in_sign_badge"></div>
                                                    <input type="hidden" name="in_signName" id="in_signName" class="form-control">
                                                </div>
                                            </div>
                                            <hr>
                                        </div>
                                        <label for="sign_comm" id="sign_comm_label" class="form-check-label" >command：</label>
                                        <textarea name="sign_comm" id="sign_comm" class="form-control" rows="5"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="updated_user"    id="updated_user"   value="<?php echo $auth_cname;?>">
                                        <input type="hidden" name="updated_emp_id"  id="updated_emp_id" value="<?php echo $auth_emp_id;?>">
                                        <input type="hidden" name="uuid"            id="uuid"           value="<?php echo $receive_row['uuid'];?>">
                                        <input type="hidden" name="fab_sign_code"   id="fab_sign_code"  value="<?php echo $receive_row['fab_sign_code'];?>">
                                        <input type="hidden" name="action"          id="action"         value="<?php echo $action;?>">
                                        <input type="hidden" name="step"            id="step"           value="<?php echo $step;?>">
                                        <input type="hidden" name="idty"            id="idty"           value="">
                                        <input type="hidden" name="old_idty"        id="old_idty"       value="<?php echo $receive_row['idty'];?>">
                                        <?php if($sys_role <= 3){ ?>
                                            <button type="submit" name="receive_submit" value="Submit" class="btn btn-primary" ><i class="fa fa-paper-plane" aria-hidden="true"></i> Agree</button>
                                        <?php } ?>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <!-- 尾段logs訊息 -->
                    <div class="col-12 pt-0 rounded bg-light" id="logs_div">
                        <div class="row">
                            <div class="col-6 col-md-6">
                                表單記錄：
                            </div>
                            <div class="col-6 col-md-6">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 py-1 px-3">
                                <table class="for-table logs table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Step</th>
                                            <th>Signer</th>
                                            <th>Time Signed</th>
                                            <th>Status</th>
                                            <th>Comment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div style="font-size: 12px;" class="text-end">
                                logs-end
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- 模組 assignSignModal-->
    <form action="store.php" method="post" >
        <div class="modal fade" id="assignSignModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">指派簽核：</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body px-4 pt-1">
                        <!-- 第二排的功能 : 搜尋功能 -->
                        <div class="row">
                            <div class="col-12">
                                <div class="input-group search">
                                    <span class="input-group-text form-label">指派</span>
                                    <input type="text" name="in_sign" id="assignSign" class="form-control" placeholder="請輸入工號"
                                            aria-label="請輸入查詢對象工號" onchange="search_fun(this.id, this.value);">&nbsp;&nbsp;
                                    <div id="assignSign_badge"></div>
                                    <input type="hidden" name="in_signName" id="assignSignName" >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input  type="hidden" name="uuid"   id="uuid"   value="<?php echo $receive_row['uuid'];?>">
                        <input  type="hidden" name="action" id="action" value="assignSign">
                        <button type="submit" id="assignSignSubmit" class="btn btn-primary <?php echo ($sys_role <= 0) ? '':'unblock';?>" ><i class="fa fa-paper-plane" aria-hidden="true"></i> Submit</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>

</body>

<script src="../../libs/aos/aos.js"></script>
<script src="../../libs/aos/aos_init.js"></script>
<script src="../../libs/openUrl/openUrl.js"></script>           <!-- 彈出子畫面 -->

<script>
    var catalogs             = <?=json_encode($catalogs)?>;                 // 第一頁：info modal function 引入catalogs資料
    var action               = '<?=$action?>';                              // Edit選染    // 引入action資料
    var receive_row          = <?=json_encode($receive_row)?>;              // Edit選染    // 引入receive_row資料作為Edit
    var receive_collect_role = '<?=$receive_collect_role?>';                // collect選染 // 引入receive_row_發放人權限作為渲染標記
    var json                 = <?=json_encode($logs_arr)?>;                 // 鋪設logs紀錄 240124-JSON.parse長度有bug   240124-改去除JSON.parse
    var receive_url          = '<?=$receive_url?>';                         // push訊息    // 本文件網址

    const assignSign_btn       = document.getElementById('assignSign_btn');      // 定義出assignSign_btn
    const assignSignSubmit_btn = document.getElementById('assignSignSubmit'); // 定義出assignSignSubmit_btn
    var   assignSign_Modal     = new bootstrap.Modal(document.getElementById('assignSignModal'), { keyboard: false });
 
</script>

<script src="receive_show.js?v=<?=time()?>"></script>

<?php include("../template/footer.php"); ?>