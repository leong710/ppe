<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("../catalog/function.php");
    require_once("function.php");
    accessDenied($sys_id);

    // 將自己的site_id設為初始值
    $myEmp_id = array('emp_id' => $_SESSION["AUTH"]["emp_id"]);
    $user = showMe($myEmp_id);
    // 把所有Location讀進來作為收貨地點_select option *** 只選健康中心
    $allLocals = show_allLocal();  
    $stocks = [];

    $sort_cate_no = "All";
    $sort_category = array(
        'cate_no' => $sort_cate_no
    );
    $catalogs = show_catalogs($sort_category);

    if(isset($_REQUEST["local_id"])){
        $stocks = show_local_stock($_REQUEST);
        $local = select_local($_REQUEST);
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

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 bg-light border p-4 rounded my-2">
            <!-- 表頭：Title+按鈕 -->
            <div class="row">
                <div class="col-12 col-md-6 py-0">
                    <h3> > > > PR請購進貨 > > > </h3>
                </div>
                <div class="col-12 col-md-6 py-0 text-end">
                    <a href="#" target="_blank" title="Submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveSubmit"> <i class="fa fa-paper-plane" aria-hidden="true"></i> 送出</a>
                    <a href="index.php" class="btn btn-danger" onclick="return confirm('確認返回？');" ><i class="fa fa-external-link" aria-hidden="true"></i> 返回</a>
                </div>
            </div>

            <!-- 本次create表單form開始 -->
            <!-- <form action="restock_store.php" method="post" onsubmit="this.site_id.disabled=false,this.standard_lv.disabled=false"> -->
            <form action="restock_store.php" method="post" onsubmit="">
                <input type="hidden" value="1" name="idty">
                <input type="hidden" value="<?php echo $_SESSION["AUTH"]["emp_id"];?>" name="out_user_id">
                <input type="hidden" value="<?php echo $_SESSION["AUTH"]["cname"];?>" name="cname"> 
                <input type="hidden" value="(PO編號)" name="stock_remark">
                <!-- 表頭：衛材訊息 -->
                <div class="row px-3 mx-3 mt-2 rounded bg-warning">
                    <!-- 表頭：左側 -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="in_local" id="in_local" class="form-control" required>
                                <option value="" selected hidden>--請選擇 收貨 儲存點--</option>
                                <?php foreach($allLocals as $allLocal){ ?>
                                    <?php if($_SESSION[$sys_id]["role"] <= 1 || $allLocal["fab_id"] == $_SESSION[$sys_id]["fab_id"] || (in_array($allLocal["fab_id"], $_SESSION[$sys_id]["sfab_id"]))){ ?>  
                                        <option value="<?php echo $allLocal["id"];?>" title="<?php echo $allLocal["fab_title"];?>" >
                                            <?php echo $allLocal["id"]."：".$allLocal["site_title"]."&nbsp".$allLocal["fab_title"]."_".$allLocal["local_title"]; if($allLocal["flag"] == "Off"){ ?>(已關閉)<?php }?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <label for="in_local" class="form-label">收貨廠區：<sup class="text-danger"> *</sup></label>
                        </div>
                    </div>
                    <!-- 表頭：右側=選擇收貨廠區 -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="po_no" id="po_no" class="form-control t-center" placeholder="請填PO編號" maxlength="12" required>
                            <label for="po_no" class="form-label">PO編號：<sup class="text-danger"> *</sup></label>
                        </div>
                    </div>
                </div>
                <!-- <hr> -->
                <!-- 中段：衛材訊息 -->
                <div class="col-12" >
                    <table>
                        <thead>
                            <tr>
                                <th>PIC</th>
                                <th>衛材名稱</br>( 內容物 )</th>
                                <th>單位</th>
                                <th>撥補數量</th>
                                <th>批號/期限</th>
                            </tr>
                        </thead>
                        <tbody id="list_PIC">
                            <?php foreach($catalogs as $catalog){?>
                                <tr>
                                    <td>
                                        <!-- PIC-image -->
                                        <input type="checkbox" name="item[<?php echo $catalog["SN"];?>]" id="catalog_SN_<?php echo $catalog["SN"];?>" class="img unblock" value="<?php echo $catalog["SN"];?>">
                                        <label for="catalog_SN_<?php echo $catalog["SN"];?>"><img src="../catalog/images/<?php echo $catalog["PIC"];?>" class="img-thumbnail"></label>
                                    </td>
                                    <td class="t-left" style="word-break: break-all;">
                                        <div class="row">
                                            <div class="col-12 col-md-5 py-0">
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
                                            </div>
                                            <div class="col-12 col-md-7 py-0" title="<?php echo 'id:'.$catalog['id'];?>">
                                                <?php echo "SN：".$catalog["SN"];?>
                                            </div>
                                        </div>

                                        <label for="catalog_SN_<?php echo $catalog["SN"];?>" class="form-check-label" >
                                        <?php echo "<b>".$catalog["pname"]."</b>";
                                              echo $catalog["size"] ? "</br>size：".$catalog["size"]:"";?></label>
                                    </td>
                                    <td><?php echo $catalog["unit"]; ?></td>
                                    <td>
                                        <input type="number" name="amount[<?php echo $catalog["SN"];?>]" id="<?php echo $catalog["SN"];?>" class="form-control amount t-center" placeholder="進貨數量" min="0">
                                    </td>
                                    <td>
                                        <input type="date" name="lot_num[<?php echo $catalog["SN"];?>]" class="form-control t-center">
                                    </td>
                                </tr>
                            <?php }?>    
                        </tbody>
                    </table>
                </div>
                <!-- 尾段：衛材訊息 -->
                <div class="col-12 mb-0">
                    <div style="font-size: 6px;" class="text-end">
                        衛材訊息text-end
                    </div>
                </div>
                <!-- 彈出畫面模組 saveSubmit-->
                <div class="modal fade" id="saveSubmit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">PR請購進貨：</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body px-5">
                                <label for="out_remark" class="form-check-label">備註說明：</label>
                                <textarea name="remark" id="remark" class="form-control" rows="5">(PR請購進貨)</textarea>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" value="Submit" name="submit" class="btn btn-primary">
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
<script>

</script>
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