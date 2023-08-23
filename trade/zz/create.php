<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../catalog/function.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 初始化半年後日期，讓系統判斷與highLight
        $toDay = date('Y-m-d');
        $half_month = date('Y-m-d', strtotime($toDay."+6 month -1 day"));
    // 將自己的site_id設為初始值
        $myEmp_id = array('emp_id' => $_SESSION["AUTH"]["emp_id"]);
        $user = showMe($myEmp_id);
    // 把所有Location讀進來作為收貨地點_select option *** 
    $allLocals = show_allLocal();  
    // 將自己的site_id設為初始值 *** 只選總倉
    $stocks = [];
    if(isset($_POST["local_id"])){
        $stocks = show_local_stock($_REQUEST);
        $select_local = select_local($_REQUEST);
    }
?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <style>
        .img:checked + label{
            border: 3px solid #f00;
        }
        .unblock{
            display: none;
            /* transition: 3s; */
        }        
        #list_cover img {
            /* max-width: 90%; */
            max-height: 100px;
            /* height: 100px; */
        }
    </style>
</head>

<div class="col-12">
    <div class="row justify-content-center">
        <div class="col-11 border rounded px-3 py-4" style="background-color: #D4D4D4;">
                <!-- 表頭：訊息 -->
                <div class="row px-2">
                    <div class="col-12 col-md-6 py-0">
                        <h3><b>批量調撥</b><?php echo empty($action) ? "":" - ".$action;?></h3>
                    </div>
                    <div class="col-12 col-md-6 py-0 text-end">
                        <?php if($_SESSION[$sys_id]["role"] <= 2){ ?>
                            <a href="#" target="_blank" title="Submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveSubmit"> <i class="fa fa-paper-plane" aria-hidden="true"></i> 送出</a>
                        <?php } ?>
                        <a href="index.php" class="btn btn-danger" onclick="return confirm('確認返回？');" ><i class="fa fa-external-link" aria-hidden="true"></i> 返回</a>
                    </div>
                </div>
                <div class="row px-2">
                    <!-- 表頭：左側 -->
                    <div class="col-12 col-md-6">
                        交易單號：(尚未給號)</br>
                        交易日期：<?php echo date('Y-m-d H:i'); ?> (實際以送出當下時間為主)</br>
                        發貨人員：<?php echo $_SESSION["AUTH"]["cname"];?></br>
                        發貨廠區：<?php if(isset($select_local)){
                                    echo $select_local["id"].': '.$select_local["site_title"].'&nbsp'.$select_local["fab_title"].'_'.$select_local["local_title"];
                                }?></br>
                    </div>
                    <!-- 表頭：右側上=選擇出庫廠區 -->
                    <div class="col-12 col-md-6">
                        <form action="" method="post" onsubmit="this.$myLocal.disabled=false">
                            <div class="form-floating">
                                <select name="local_id" id="local_id" class="form-select" required onchange="this.form.submit()">
                                    <option value="" selected hidden>--請選擇 出庫 儲存點--</option>
                                    <?php foreach($allLocals as $allLocal){ ?>
                                        <?php if($_SESSION[$sys_id]["role"] <= 0 || $allLocal["fab_id"] == $_SESSION[$sys_id]["fab_id"] || (in_array($allLocal["fab_id"], $_SESSION[$sys_id]["sfab_id"]))){ ?>  
                                            <option value="<?php echo $allLocal["id"];?>" title="<?php echo $allLocal["site_title"];?>" <?php if(isset($_POST["local_id"]) && ($_POST["local_id"]) == $allLocal["id"]){?> selected <?php }?>>
                                                <?php echo $allLocal["id"].":".$allLocal["site_title"]."&nbsp".$allLocal["fab_title"]."_".$allLocal["local_title"]; if($allLocal["flag"] == "Off"){ ?>(已關閉)<?php }?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <label for="local_id" class="form-label">出庫廠區：<sup class="text-danger"> *</sup></label>
                            </div>
                        </form>
                        
            <!-- 本次create表單form開始 -->
            <form action="store.php" method="post" onsubmit="this.fab_id.disabled=false,this.standard_lv.disabled=false">
                <input type="hidden" value="1" name="idty">
                <input type="hidden" value="<?php echo $_SESSION["AUTH"]["emp_id"];?>" name="out_user_id">
                <input type="hidden" value="<?php echo $_SESSION["AUTH"]["cname"];?>" name="cname">
                <input type="hidden" value="<?php echo $select_local["id"];?>" name="out_local">
                            
                        <!-- 表頭：右側下=選擇入庫廠區 -->
                        <div class="form-floating">
                            <select name="in_local" id="in_local" class="form-select" required >
                                <option value="" selected hidden>--請選擇 入庫 儲存點--</option>
                                <?php foreach($allLocals as $allLocal){ ?>
                                    <option value="<?php echo $allLocal["id"];?>" title="<?php echo $allLocal["site_title"];?>" >
                                        <?php echo $allLocal["id"];?>:<?php echo $allLocal["site_title"];?>&nbsp<?php echo $allLocal["fab_title"];?>_<?php echo $allLocal["local_title"];?><?php if($allLocal["flag"] == "Off"){ ?>(已關閉)<?php }?></option>
                                <?php } ?>
                            </select>
                            <label for="in_local" class="form-label">入庫廠區：<sup class="text-danger"> *</sup></label>
                        </div>
                    </div>
                </div>
                <!-- <hr> -->
                <!-- 中段：訊息 -->
                <div class="col-12" >
                    <table>
                        <thead>
                            <tr>
                                <th>PIC</th>
                                <th>名稱</th>
                                <th>庫存量</th>
                                <th>單位</th>
                                <th data-toggle="tooltip" data-placement="bottom" title="效期小於6個月將highlight">批號/期限 <i class="fa fa-info-circle" aria-hidden="true"></i></th>
                                <th>PO編號</th>
                                <th>調撥數量</th>
                            </tr>
                        </thead>
                        <tbody id="list_cover">
                            <?php foreach($stocks as $stock){?>
                                <tr>
                                    <td><!-- cover-image -->
                                        <input type="checkbox" name="stock[<?php echo $stock["id"];?>]" id="stock_id_<?php echo $stock["id"];?>" class="img unblock" value="<?php echo $stock["id"];?>">
                                        <label for="stock_id_<?php echo $stock["id"];?>"><img src="../catalog/images/<?php echo $stock["PIC"];?>" class="img-thumbnail"></label>
                                    </td>
                                    <td class="t-left" style="word-break: break-all;">
                                        <div class="row">
                                            <div class="col-12 col-md-5 py-1">
                                                <span class="badge rounded-pill <?php switch($stock["cate_id"]){
                                                    case "1": echo "bg-primary"; break;
                                                    case "2": echo "bg-success"; break;
                                                    case "3": echo "bg-warning text-dark"; break;
                                                    case "4": echo "bg-danger"; break;
                                                    case "5": echo "bg-info text-dark"; break;
                                                    case "6": echo "bg-dark"; break;
                                                    case "7": echo "bg-secondary"; break;
                                                    default: echo "bg-light text-success"; break; }?>">
                                                <?php echo $stock["cate_no"].".".$stock["cate_title"];?></span>
                                            </div>
                                            <div class="col-12 col-md-7 py-1" title="<?php echo 'id:'.$stock['id'];?>">
                                                <?php echo "SN：".$stock["SN"];?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 py-1">
                                                <label for="<?php echo $stock["id"];?>" class="form-check-label"><b><?php echo $stock["pname"];?></b></label>
                                            </div>
                                            <div class="col-12 py-1">
                                                <?php echo $stock["size"] ? "size：".$stock["size"]:"";?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo $stock["amount"]; ?></td>
                                    <td><?php echo $stock["unit"]; ?></td>
                                    <td <?php if($stock["lot_num"] < $half_month){ ?> style="background-color:#FFBFFF; color:red;" data-toggle="tooltip" data-placement="bottom" title="有效期限小於：<?php echo $half_month;?>" <?php } ?>>
                                        <?php echo $stock["lot_num"]; ?></td>
                                    <td><?php echo $stock["po_no"]; ?></td>
                                    <td>
                                        <input type="hidden" name="lot_num[<?php echo $stock['id'];?>]" value="<?php echo $stock['lot_num'];?>">
                                        <input type="hidden" name="po_no[<?php echo $stock['id'];?>]" value="<?php echo $stock['po_no'];?>">
                                        <input type="hidden" name="item[<?php echo $stock['id'];?>]" value="<?php echo $stock['cata_SN'];?>">
                                        <input type="number" name="amount[<?php echo $stock['id'];?>]" id="<?php echo $stock['id'];?>" class="form-control amount t-center"
                                            placeholder="調撥數量" min="0" max="<?php echo $stock["amount"];?>" 
                                            maxlength="<?php echo strlen($stock['amount']);?>" oninput="if(value.length><?php echo strlen($stock['amount']);?>)value=value.slice(0,<?php echo strlen($stock['amount']);?>)">
                                    </td>
                                </tr>
                            <?php }?>    
                        </tbody>
                    </table>
                </div>
                <!-- 尾段：衛材訊息 -->
                <div class="col-12 mb-0">
                    <div style="font-size: 6px;" class="text-end">
                        訊息text-end
                    </div>
                </div>
                <!-- 彈出畫面說明模組 saveSubmit-->
                <div class="modal fade" id="saveSubmit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">批量調撥：</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body px-5">
                                <label for="out_remark" class="form-check-label" >出貨備註說明：</label>
                                <textarea name="remark" id="remark" class="form-control" rows="5"></textarea>
                            </div>
                            <div class="modal-footer">
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
                document.getElementById('stock_id_'+ amount_id).checked=false;
            } else {
                document.getElementById('stock_id_'+ amount_id).checked=true;
            }
        }
    }
    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
</script>
 
<?php include("../template/footer.php"); ?>