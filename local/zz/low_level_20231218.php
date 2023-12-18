<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    if(isset($_GET["local_id"])){
        $select_local = select_local($_REQUEST);
        if(empty($select_local)){                           // 查無資料時返回指定頁面
            echo "<script>history.back()</script>";         // 用script導回上一頁。防止崩煃
        }
        $buy_ty = $select_local["buy_ty"];                              // 限購規模
        $low_level = json_decode($select_local["low_level"]);           // 安全水位
        if(is_object($low_level)) { $low_level = (array)$low_level; } 
        $catalogs = show_catalogs();

    }else{
        $select_local = array(
            'id' => ''
        );
        $catalogs = [];
    }

    $allLocals = show_allLocal();

?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
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
            max-width: 100%;
            /* max-height: 100px; */
            max-height: 150px;
        }
        /* 標籤增加陰影辨識度 */
        .badge {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 border p-4 rounded my-2" style="background-color: #D4D4D4;">
            <!-- 表頭：衛材訊息 -->
            <div class="row">
                <div class="col-12 col-md-6 py-0">
                    <h4>local安全水位設定值</h4>
                </div>
                <div class="col-12 col-md-6 py-0 text-end">
                    <a href="#access_info" target="_blank" title="連線說明" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#access_info">
                        <i class="fa fa-info-circle" aria-hidden="true"></i> 安全庫存量說明</a>
                    <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>    
                        <a href="#" target="_blank" title="Submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveSubmit"> <i class="fa fa-paper-plane" aria-hidden="true"></i> 送出</a>
                    <?php } ?>
                    <a class="btn btn-success" href="index.php"><i class="fa fa-caret-up" aria-hidden="true"></i> 回總表</a>
                </div>
            </div>

            <!-- 表頭 -->
            <div class="row px-3 py-0">
                <!-- 表頭：左側 -->
                <div class="col-12 col-md-8 pb-2">
                    <!-- 表頭：左上=選擇廠區 -->
                    <form action="" method="get" onsubmit="this.$myLocal.disabled=false">
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
                <div class="col-12 col-md-4 pb-2">
                    <?php echo isset($buy_ty) ? "buy_ty/廠區規模(限購類別)：".$buy_ty:""; ?>
                    </br>*.安全水位建議參考說明
                </div>
            </div>

            <!-- 本次create表單form開始 -->
            <form action="store_lowLevel.php" method="post" onsubmit="this.site_id.disabled=false,this.standard_lv.disabled=false">
                <input type="hidden" value="<?php echo $_SESSION[$sys_id]["id"];?>" name="user_id">
                <input type="hidden" value="<?php echo $select_local["id"];?>" name="local_id">

                <!-- <hr> -->
                <div class="col-12 py-0" id="catalog_list">
                    <div class="row">
                        <?php foreach($catalogs as $catalog){?>
                            <div class="col-6 col-md-3 p-1">
                                <div class="col-12 rounded border bg-white" style="text-align: left;vertical-align:top;word-break: break-all">
                                    <!-- cover-image -->
                                    <div class="" style="height: 160px; text-align:center;">
                                        <input type="checkbox" name="catalog_SN[<?php echo $catalog["SN"];?>]" id="catalog_SN_<?php echo $catalog["SN"];?>" class="img unblock" value="<?php echo $catalog["SN"];?>"
                                            <?php if(isset($low_level[$catalog['SN']])){ echo "checked"; }?>>
                                        <label for="catalog_SN_<?php echo $catalog["SN"];?>"><img src="../catalog/images/<?php echo $catalog["PIC"];?>" class="img-thumbnail"></label>
                                    </div>
                                    <!-- catalog info -->
                                    <div style="height: 100px;">
                                        <div class="row">
                                            <div class="col-12 col-md-5 py-0">
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
                                                    <?php echo $catalog["cate_no"].".".$catalog["cate_title"];?></span></h5>
                                            </div>
                                            <div class="col-12 col-md-7 py-0">
                                                <?php echo "SN：".$catalog["SN"];?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 py-0" title="<?php echo 'id:'.$catalog['id'];?>">
                                                <?php echo $catalog["pname"];?>
                                            </div>
                                            <div class="col-12 py-0">
                                                <?php echo $catalog["size"] ? "size：".$catalog["size"]:"";?>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- issue amount -->
                                    <div class="px-1">
                                        <?php switch($buy_ty){
                                                case 'a': $buy_qty = ceil($catalog['buy_a']/2); break;
                                                case 'b': $buy_qty = ceil($catalog['buy_b']/2); break;
                                                default : $buy_qty = ceil($catalog['buy_a']/2); break; } ?>
                                                <!-- // default : $buy_qty = ceil(999/2); break; } > -->
                                        <div class="form-floating">
                                            <input type="number" name="amount[<?php echo $catalog['SN'];?>]" id="<?php echo $catalog['SN'];?>" class="form-control amount t-center" 
                                                placeholder="請填最低值" min="1" max="999" maxlength="<?php echo strlen($buy_qty);?>" 
                                                    oninput="if(value.length > <?php echo strlen($buy_qty);?>)value = value.slice(0, <?php echo strlen($buy_qty);?>)"
                                                    value="<?php echo !empty($low_level[$catalog['SN']]) ? $low_level[$catalog['SN']]:$buy_qty ;?>">
                                            <label for="catalog_SN_<?php echo $catalog["SN"];?>"><?php echo "建議值：".$buy_qty."&nbsp/&nbsp".$catalog["unit"];?> </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
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
                                <h5 class="modal-title">local儲存區低水位設定值：</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body px-5">
                                <h3>確認完畢，是否送出?</h3>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                                <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                                    <input type="submit" value="Submit" name="submit" class="btn btn-primary">
                                <?php } ?>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
<script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>
<!-- 有數量自動勾選，沒數量自動取消 -->
<script>
    let amounts = [...document.querySelectorAll('.amount')];
    for(let amount of amounts){
        amount.onchange = e => {
            let amount_id = e.target.id;
            if(amount.value == ''){
                document.getElementById('catalog_SN_'+ amount_id).checked=false;
            } else {
                document.getElementById('catalog_SN_'+ amount_id).checked=true;
            }
        }
    }
    // setInterval(5000);
</script>
 
<?php include("../template/footer.php"); ?>