<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    if(isset($_POST["submit"])){
        // 有收到年份 = 選定的年份
        $selected_year = $_REQUEST["checked_year"];
    }else{
        // 預設顯示年份 = 今年
        $selected_year = date('Y');
    }
    
    $byYear = array('checked_year' => $selected_year);
    $checked_lists = show_allchecked($byYear);

    // 把資料庫裡的年份清單讀出來
    $checked_years = show_allchecked_year();
?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <style>
        .TOP {
            background-image: URL('../images/checked3.jpg');
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
            /* overflow: hidden; 會影響表頭黏貼功能*/
            background-attachment: fixed;
            /* background-position: center top; 對齊*/
            background-position: left top;
            background-repeat: no-repeat;
            /* background-size: cover; */
            background-size: contain;
            padding-top: 50px;
        }
    </style>
</head>
<body>
    <div class="TOP">
        <div class="col-12">
            <div class="row justify-content-center">
                <div class="col_xl_11 col-11 p-3 rounded" style="background-color: rgba(255, 255, 255, .8);">
                    <!-- 歷史點檢紀錄表頭 -->
                    <div class="row px-2">
                        <div class="col-md-8">
                            <div>
                                <h3><b><?php echo $selected_year;?>y點檢紀錄總表</b></h3>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <form action="" method="post">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-search"></i>&nbsp篩選年份</span>
                                    <select name="checked_year" id="checked_year" class="form-select" onchange="this.form.submit()">
                                        <?php foreach($checked_years as $checked_year){ ?>
                                            <option value="<?php echo $checked_year["checked_year"];?>" <?php echo $checked_year["checked_year"] == $selected_year ? "selected":"";?>
                                                ><?php echo $checked_year["checked_year"];?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </form>   
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="container"> -->
    <div class="col-12">
        <div class="row justify-content-center">
            <!-- 歷史清單 -->
            <div class="col_xl_11 col-11 rounded" style="background-color: #D4D4D4;">
                <div class="col-12 justify-content-center rounded" >
                    <!-- 歷史點檢紀錄list -->
                    <div class="col-xl-12 col-12 rounded bg-light">
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th>ai</th>
                                    <th>fab</th>
                                    <th>點檢年度</th>
                                    <th>點檢日期/點檢人</th>
                                    <th>備註說明</th>
                                    <th>action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- 鋪設內容 -->
                                <?php foreach($checked_lists as $checked_list){ ?>
                                    <tr>
                                        <td style="font-size: 6px;"><?php echo $checked_list['id'];?></td>
                                        <td><?php echo $checked_list['fab_title'];?></td>
                                        <td><?php if(isset($checked_list['checked_year'])){
                                                        echo $checked_list['checked_year']."_".$checked_list['half'];}?></td>
                                        <td style="text-align: left;"><?php if(isset($checked_list['created_at'])){ 
                                                        echo $checked_list['created_at']." / ".$checked_list['updated_user'];}?></td>
                                        <td style="width:30%; text-align: left; word-break: break-all;"><?php echo $checked_list['checked_remark'];?></td>
                                        <td><?php if(isset($checked_list['id'])){ ?>
                                            <a href="read.php?id=<?php echo $checked_list['id'];?>&from2=siteList" class="btn btn-sm btn-xs btn-info">檢視</a>
                                            <?php } ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>                  
                </div>
            </div>
        </div>
    </div>

<!-- 彈出畫面模組2 匯出CSV-->
    <div class="modal fade" id="doCSV" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">匯出儲存點存量紀錄(csv)</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- 20220606測試CSV匯入/匯出 -->
                <form id="addform" action="docsv.php?action=exportSite" method="post"> 
                    <div class="modal-body p-4" >
                        <div class="col-12">
                            <label for="" class="form-label">請選擇您要查詢下載的site：<sup class="text-danger"> *</sup></label>
                            <select name="site_id" id="site_id" class="form-control" required >
                                <option value="" selected hidden>--請選擇site--</option>
                                <?php foreach($sites as $site){ ?>
                                    <option value="<?php echo $site["id"];?>"><?php echo $site["id"];?>: <?php echo $site["site_title"];?> (<?php echo $site["remark"];?>)</option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <input type="submit" class="btn btn-warning" value="匯出site存量CSV"> 
                        </div>
                        <hr>
                        <div class="col-12 text-end">
                            <a href="docsv.php?action=export" title="匯出tn存量CSV" class="btn btn-success"><i class="fa fa-download" aria-hidden="true"></i> 匯出tn存量CSV</a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form> 
            </div>
        </div>
    </div>
<!-- 彈出畫面模組x 匯出CSV-->
    <div class="modal fade" id="csvx" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">匯出CSV紀錄</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- 20220606測試CSV匯入/匯出 -->
                <form id="addform" action="../csv/do.php?action=import" method="post" enctype="multipart/form-data"> 
                    <div class="modal-body p-4">
                        請選擇要匯入的CSV檔案：
                        <input type="file" name="file">
                        <br><br>
                        ** 請注意CSV欄位需一致，第一列表頭將不被匯入!
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="submit" class="btn btn-primary" value="匯入CSV"> 
                            <input type="button" class="btn btn-info" value="匯出CSV" onclick="window.location.href='../csv/do.php?action=export'">
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
</body>
<!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
<script src="../../libs/aos/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../../libs/aos/aos_init.js"></script>

<?php include("../template/footer.php"); ?>