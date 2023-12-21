<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 去年年份
    $thisYear = date('Y') ;                        // 這裡要減1才會找出去年的用量

    if(isset($_POST["local_id"])){
        $select_local = select_local($_REQUEST);
        if(empty($select_local)){                           // 查無資料時返回指定頁面
            echo "<script>history.back()</script>";         // 用script導回上一頁。防止崩煃
        }
        $buy_ty = $select_local["buy_ty"];                              // 限購規模
        $low_level = json_decode($select_local["low_level"]);           // 安全存量
        if(is_object($low_level)) { $low_level = (array)$low_level; } 

        $catalogs = show_catalogs();

        // 組合查詢條件陣列
        $list_issue_setting = array(
            'local_id'  => $_POST["local_id"],
            'thisYear'  => $thisYear
        );
        $myReceives = show_my_receive($list_issue_setting);         // 列出這個fab_id、今年度的領用單
        $stock_cata_SN = show_stock_cata_SN($list_issue_setting);

    }else{
        $select_local = array(
            'id' => ''
        );
        $buy_ty = "";
        $catalogs = [];
        $myReceives = [];
        $stock_cata_SN = [];
    }

    $allLocals = show_allLocal();

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
        <!-- <link rel="stylesheet" type="text/css" href="../../libs/dataTables/jquery.dataTables.css"> -->
        <!-- <script type="text/javascript" charset="utf8" src="../../libs/dataTables/jquery.dataTables.js"></script> -->
    <!-- 引入 SweetAlert 的 JS 套件 參考資料 https://w3c.hexschool.com/blog/13ef5369 -->
    <script src="../../libs/sweetalert/sweetalert.min.js"></script>
    <!-- mloading JS -->
    <script src="../../libs/jquery/jquery.mloading.js"></script>
    <!-- mloading CSS -->
    <link rel="stylesheet" href="../../libs/jquery/jquery.mloading.css">
    <style>
        .img:checked + label{
            border: 3px solid #f00;
        } 

    </style>
</head>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 border px-3 rounded my-2" style="background-color: #D4D4D4;">
            <!-- 表頭：按鈕 -->
            <div class="row">
                <div class="col-12 col-md-6 pb-0">
                    <h4>安全存量設定</h4>
                </div>
                <div class="col-12 col-md-6 pb-0 text-end">
                    <?php if(($_SESSION[$sys_id]["role"] <= 2) && !empty($buy_ty)){ ?>    
                        <a href="#" target="_blank" title="Submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveSubmit"> <i class="fa fa-paper-plane" aria-hidden="true"></i> 送出</a>
                    <?php } ?>
                    <a href="#access_info" target="_blank" title="連線說明" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#access_info">
                        <i class="fa fa-info-circle" aria-hidden="true"></i> 安全庫存量說明</a>
                    <a class="btn btn-secondary" href="index.php"><i class="fa fa-caret-up" aria-hidden="true"></i> 回總表</a>
                </div>
            </div>

            <!-- 表頭：選單 -->
            <div class="row py-0">
                <!-- 表頭：左側 -->
                <div class="col-12 col-md-8 pb-2">
                    <!-- 表頭：左上=選擇廠區 -->
                    <form action="" method="post" onsubmit="this.$myLocal.disabled=false">
                        <div class="form-floating">
                            <select name="local_id" id="local_id" class="form-control" required style='width:80%;' onchange="this.form.submit()">
                                <option value="" hidden>--請選擇 low_level 儲存點--</option>
                                <?php foreach($allLocals as $allLocal){ ?>
                                    <?php if($_SESSION[$sys_id]["role"] <= 1 || $allLocal["fab_id"] == $_SESSION[$sys_id]["fab_id"] || (in_array($allLocal["fab_id"], $_SESSION[$sys_id]["sfab_id"]))){ ?>  
                                        <option value="<?php echo $allLocal["id"];?>" title="<?php echo $allLocal["fab_title"];?>" <?php echo $allLocal["id"] == $select_local["id"] ? "selected":""; ?>>
                                            <?php echo $allLocal["id"].": ".$allLocal["fab_title"]."&nbsp(".$allLocal["fab_remark"].")_".$allLocal["local_title"]."&nbsp(".$allLocal["local_remark"].")"; if($allLocal["flag"] == "Off"){ ?>(已關閉)<?php }?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <label for="local_id" class="form-label">設定廠區：</label>
                        </div>
                    </form>
                </div>
                
                <!-- 表頭：右側 -->
                <div class="col-12 col-md-4 pb-2 text-end">
                    <?php if(!empty($buy_ty)){ ?> 
                        <a href="#" target="_blank" title="Submit" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#update_stock_stand_lv"><i class="fa-solid fa-thumbs-up"></i> 立即同步現有庫存安量</a>
                    <?php } ?>
                </div>
            </div>

            <div class="row">
                <!-- 本次create表單form開始 -->
                <form action="store_lowLevel.php" method="post" onsubmit="this.site_id.disabled=false,this.standard_lv.disabled=false">
                    <div class="col-12 rounded bg-white">
                        <table id="catalog_list" class="catalog_list table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>PIC</th>
                                    <th>名稱 / 訊息</th>
                                    <th><?php echo $thisYear;?>年領用</th>
                                    <th>建議值<?php echo (!empty($buy_ty)) ? "_".$buy_ty:"";?></th>
                                    <th style="width: 15%;">新安全存量</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($catalogs as $catalog){ ?>
                                    <tr>
                                        <td><img src="../catalog/images/<?php echo $catalog["PIC"];?>" class="img-thumbnail"></td>
                                        <td style="text-align: left;"><h5><b><?php echo $catalog["SN"].'：'.$catalog["pname"];?></b></h5>
                                            <span class="badge rounded-pill <?php switch($catalog["cate_id"]){
                                                                    case "1": echo "bg-primary"; break;
                                                                    case "2": echo "bg-success"; break;
                                                                    case "3": echo "bg-warning text-dark"; break;
                                                                    case "4": echo "bg-danger"; break;
                                                                    case "5": echo "bg-info text-dark"; break;
                                                                    case "6": echo "bg-dark"; break;
                                                                    case "7": echo "bg-secondary"; break;
                                                                    default: echo "bg-light text-success"; break;
                                                                }?>">
                                                    <?php echo $catalog["cate_no"].".".$catalog["cate_title"];?></span>
                                            <?php echo $catalog["model"] ? "&nbsp型號：".$catalog["model"]:"";?>
                                        </td>
                                        <td id="receive_<?php echo $select_local["id"].'_'.$catalog['SN'];?>">--</td>
                                        <td id="buy_qt_<?php echo $select_local["id"].'_'.$catalog['SN'];?>">
                                            <?php switch($buy_ty){
                                                    case 'a': 
                                                        $buy_qty = (!empty($low_level[$catalog['SN']])) ? ($low_level[$catalog['SN']]) : "0";  
                                                        break;
                                                    case 'b': 
                                                        $buy_qty = (!empty($low_level[$catalog['SN']])) ? ($low_level[$catalog['SN']]) : "0"; 
                                                        break;
                                                    default : 
                                                        $buy_qty = (!empty($low_level[$catalog['SN']])) ? ($low_level[$catalog['SN']]) : "0"; 
                                                        break; } ?>
                                            <label for="catalog_SN_<?php echo $catalog["SN"];?>"><?php echo $buy_qty."&nbsp/&nbsp".$catalog["unit"];?> </label>
                                        </td>
                                        <td>
                                            <div class="col-12 text-center py-0 ">
                                                <b><?php echo "目前安量： "; echo !empty($low_level[$catalog['SN']]) ? $low_level[$catalog['SN']]: $buy_qty;?></b>
                                            </div>
                                            <input type="number" name="low_level[<?php echo $catalog["SN"];?>]" id="<?php echo $select_local["id"].'_'.$catalog['SN'];?>" class="form-control amount t-center" 
                                                placeholder="請填最低值" min="0" 
                                                <?php if($sys_id_role <= 1){ ?>
                                                    
                                                <?php } else { ?>
                                                    max="9999" maxlength="<?php echo strlen($buy_qty);?>" 
                                                    oninput="if(value.length > <?php echo strlen($buy_qty);?>)value = value.slice(0, <?php echo strlen($buy_qty);?>)"
                                                <?php } ?>
                                                    value="<?php echo !empty($low_level[$catalog['SN']]) ? $low_level[$catalog['SN']]:$buy_qty ;?>"
                                                >
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- 尾段：衛材訊息 -->
                    <div class="col-12 mb-0">
                        <div style="font-size: 6px;" class="text-end">
                            catalog-end
                        </div>
                    </div>
                    <!-- 彈出畫面說明模組 saveSubmit-->
                    <div class="modal fade" id="saveSubmit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">local儲存區安全庫存設定值：</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body px-5">
                                    <div class="col-12 border rounded ">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="update_stock_stand_lv_option" name="update_stock_stand_lv_option" checked>
                                            <label class="form-check-label" for="update_stock_stand_lv_option">同步更新現有庫存之安全存量</label>
                                        </div>
                                    </div>
                                    <div class="col-12 text-end">
                                        <h4>確認完畢，是否送出?</h4>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="updated_user"    value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                    <input type="hidden" name="local_id"        value="<?php echo $select_local["id"];?>">
                                    <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                                        <input type="submit" value="Submit" name="low_level_submit" class="btn btn-primary">
                                    <?php } ?>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <hr>
            <!-- 尾段：debug訊息 -->
            <?php 
                if(isset($_REQUEST["debug"])){
                    include("debug_board.php"); 
                } 
            ?>

            <!-- 彈出畫面說明模組 update_stock_stand_lv-->
            <div class="modal fade" id="update_stock_stand_lv" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title">立即同步現有庫存安量</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form action="#" method="post" onsubmit="this.site_id.disabled=false,this.standard_lv.disabled=false">
                            <div class="modal-body px-4">
                                <div class="col-12 py-0 text-center text-danger">
                                    <h4><b>!! 注意 !!</b><h4>
                                </div>
                                <div class="col-12" style="line-height: 1.5; font-size: 20px;">
                                        <?php if(!empty($stock_cata_SN) && !empty($select_local)){
                                            echo "此動作將以目前安量，更新到<b>(";
                                            echo $select_local["fab_title"]."&nbsp(".$select_local["fab_remark"].")_".$select_local["local_title"]."&nbsp(".$select_local["local_remark"].")</b>";
                                            echo ")，現有庫存品<b>(共".count($stock_cata_SN)." 筆)</b>之安全存量將會被異動";
                                        } ?>
                                </div>
                                <div class="col-12 py-0 text-end">
                                    <h4>Q：確定送出?</h4>
                                </div>
                            </div>
                            
                            <div class="modal-footer">
                                <input type="hidden" name="updated_user"    value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                <input type="hidden" name="local_id"        value="<?php echo $select_local["id"];?>">
                                <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                                    <input type="submit" value="Submit" name="low_level_submit" class="btn btn-primary">
                                <?php } ?>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- 彈出畫面模組-安全庫存量說明 -->
<div class="modal fade" id="access_info" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h4 class="modal-title">安全庫存量說明 (<sup class="text-danger"> * </sup>參考)</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-6 py-0">
                            <div>
                                安全庫存評估表
                            </div>
                            <table>
                                <thead> 
                                    <tr>
                                        <th>評估內容</th>
                                        <th>評估等級</th>
                                        <th>加權占比<sup class="text-danger"> *</sup></th>
                                        <th>對應分值<sup class="text-danger"> *</sup></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td rowspan="3">需求重要性</td>
                                        <td>A</td>
                                        <td>90</td>
                                        <td rowspan="3">40%</td>
                                    </tr>
                                    <tr><td>B</td><td>40</td></tr>
                                    <tr><td>C</td><td>0</td></tr>
    
                                    <tr>
                                        <td rowspan="3">需求預測性</td>
                                        <td>高</td>
                                        <td>90</td>
                                        <td rowspan="3">20%</td>
                                    </tr>
                                    <tr><td>中</td><td>60</td></tr>
                                    <tr><td>低</td><td>0</td></tr>
    
                                    <tr>
                                        <td rowspan="3">供應穩定性</td>
                                        <td>低</td>
                                        <td>90</td>
                                        <td rowspan="3">10%</td>
                                    </tr>
                                    <tr><td>中</td><td>60</td></tr>
                                    <tr><td>高</td><td>0</td></tr>
    
                                    <tr>
                                        <td rowspan="3">採購Lead Time</td>
                                        <td>>30天</td>
                                        <td>90</td>
                                        <td rowspan="3">10%</td>
                                    </tr>
                                    <tr><td>10~30天</td><td>60</td></tr>
                                    <tr><td><10天</td><td>10</td></tr>
    
                                    <tr>
                                        <td rowspan="3">料件通用性</td>
                                        <td>高</td>
                                        <td>90</td>
                                        <td rowspan="3">20%</td>
                                    </tr>
                                    <tr><td>中</td><td>60</td></tr>
                                    <tr><td>低</td><td>0</td></tr>
    
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="col-12 col-md-6 py-0">
                            <div>
                                安全庫存建議
                            </div>
                            <table>
                                <thead> 
                                    <tr>
                                        <th>分數</th>
                                        <th>安全庫存量建議</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>>80</td>
                                        <td>一個採購Lead Time的100%預計需求量</td>
                                    </tr>
                                    <tr>
                                        <td>40~80</td>
                                        <td>一個採購Lead Time的50%預計需求量</td>
                                    </tr>
                                    <tr>
                                        <td>20~40</td>
                                        <td>一個採購Lead Time的20%預計需求量</td>
                                    </tr>
                                    <tr>
                                        <td><20</td>
                                        <td>不備安全庫存</td>
                                    </tr>
    
    
       
    
                                </tbody>
                            </table>
                            * 範例：(90x40%)+(60x20%)+(60x10%)+(90x10%)+(90x20%)=81</br></br>
                            * ppePM計算方式：一年領用量1.1倍(來年自動滾算)
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                    </div>
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
<!-- 有數量自動勾選，沒數量自動取消 -->
<script>
    // 找出Local_id算SN年領用量
    var catalogs        = <?=json_encode($catalogs);?>;                  // 引入myReceives資料，算年領用量
    var myReceives      = <?=json_encode($myReceives);?>;                // 引入myReceives資料，算年領用量
    var receiveAmount   = [];                                            // 宣告變數陣列，承裝Receives年領用量
    var buy_ty          = '<?=$buy_ty;?>';                               // 取得fab的安全倍數

    // 彙整出SN年領用量
    Object(myReceives).forEach(function(row){
        let csa = JSON.parse(row['cata_SN_amount']);
        Object.keys(csa).forEach(key =>{                                // 這裡的key = SN
            let pay = Number(csa[key]['pay']);
            let l_key = row['local_id'] +'_'+ key;
            if(receiveAmount[l_key]){
                receiveAmount[l_key]['pay'] += pay;
            }else{
                receiveAmount[l_key] = {
                    'pay' : pay
                }
            }
            Object(catalogs).forEach(function(cata){
                if(cata['SN'] == key){
                    receiveAmount[l_key]['buy_dm'] = cata['buy_'+buy_ty];
                    receiveAmount[l_key]['unit'] = cata['unit'];
                    return;
                }
            })
        })
    });

    // 選染到Table上指定欄位
    Object.keys(receiveAmount).forEach(key => {
        let pay = receiveAmount[key]['pay'];                // 領用總數
        let buy_dm = receiveAmount[key]['buy_dm'];          // 安量倍數
        let unit = receiveAmount[key]['unit'];              // SN-unit單位
        let value = Math.ceil(pay * buy_dm);                // 算出建議值
        $('#receive_'+key).empty();                                             // 清除領用格
        $('#receive_'+key).append(pay);                                         // 貼上領用總量
        $('#buy_qt_'+key).empty();                                              // 清除建議值
        $('#buy_qt_'+key).append('x '+ buy_dm +' =</br>'+ value +' / '+ unit );      // 貼上建議值計算公式
        document.getElementById('buy_qt_'+key).classList.add('alert_it');       // 將建議值套用css:alert_it
        document.getElementById(key).value = value;                             // input.value套用
    })

    $(document).ready(function () {
        // 停用 dataTable => 原因是它換頁後，前面的數值無法送出...
            // $('#catalog_list').DataTable({
            //     "autoWidth": false,
            //     // 排序
            //     // "order": [[ 4, "asc" ]],
            //     // 顯示長度
            //     "pageLength": 25,
            //     // 中文化
            //     "language":{
            //         url: "../../libs/dataTables/dataTable_zh.json"
            //     }
            // });

    })
</script>
 
<?php include("../template/footer.php"); ?>