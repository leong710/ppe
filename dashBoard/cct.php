<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    // // *** 篩選組合項目~~
        if(isset($_REQUEST["receive_yy"])){
            $receive_yy = $_REQUEST["receive_yy"];
        }else{
            $receive_yy = date('Y');                       // 今年月
        }
        if(isset($_REQUEST["receive_mm"])){
            $receive_mm = $_REQUEST["receive_mm"];
        }else{
            $receive_mm = date('m');                       // 今年月
        }
        $list_setting = array(                              // 組合查詢陣列 -- 建立查詢陣列for顯示今年領用單
            'receive_yy'  => $receive_yy,
            'receive_mm'  => $receive_mm
        );

    $receive_lists = show_receives($list_setting);      // 調閱點檢表
    $allReceive_yys = show_allReceive_yy();             // 取出checked年份清單 => 供checked頁面篩選

    $locals = show_local();                             // 標題用：區域名稱
    $catalogs = show_catalogs();                        // 標題用：器材名稱
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
        #fix_local tr > th {
            color: blue;
            text-align: center;
            vertical-align: top; 
            word-break: break-all; 
            /* background-color: white; */
            font-size: 16px;
            -webkit-writing-mode: vertical-lr;
            writing-mode: vertical-lr; 
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
        .vlr tr > th {
            vertical-align: middle; 
            writing-mode: vertical-lr;
            /* text-orientation: upright; */
            text-align: right;
        }

    </style>
    <script>    
        // loading function
        function mloading(){
            $("body").mLoading({
                // icon: "../../libs/jquery/Wedges-3s-120px.gif",
                icon: "../../libs/jquery/loading.gif",
            }); 
        }
        // All resources finished loading! // 關閉mLoading提示
        window.addEventListener("load", function(event) {
            $("body").mLoading("hide");
        });
        mloading();    // 畫面載入時開啟loading
    </script>
</head>
<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_12 col-12 px-3 py-3 rounded" style="background-color: rgba(255, 255, 255, .8);" >
                <!-- 表頭 -->
                <div class="row">
                    <div class="col-md-4 py-1">
                        <div>
                            <h3><b>領用匯總表</b></h3>
                        </div>
                    </div>
                    <div class="col-md-4 py-1">
                        <form action="" method="post">
                            <div class="input-group">
                                <span class="input-group-text">篩選年度</span>
                                <select name="receive_yy" id="groupBy_receive_yy" class="form-select">
                                    <?php foreach($allReceive_yys as $aR_yy){ ?>
                                        <option value="<?php echo $aR_yy["yy"];?>" <?php echo ($aR_yy["yy"] == $receive_yy) ? "selected":"";?>>
                                            <?php echo $aR_yy["yy"]."y";?></option>
                                    <?php } ?>
                                </select>
                                <select name="receive_mm" id="groupBy_receive_mm" class="form-select">
                                    <option value="All" <?php echo ($receive_mm == "All") ? "selected":"";?> >-- 全年度 / All --</option>
                                    <?php foreach (range(1, 12) as $item) {
                                            $item_str = str_pad($item, 2, '0', STR_PAD_LEFT);
                                            echo "<option value='{$item_str}'";
                                            echo ($item_str == $receive_mm ) ? "selected":"";
                                            echo " >{$item_str}m</option>";
                                        } ?>
                                </select>
                                <button type="submit" class="btn btn-outline-secondary">查詢</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4 py-1 text-end">

                    </div>
                </div>
                <!-- Bootstrap Alarm -->
                <div id="liveAlertPlaceholder" class="col-11 mb-0 p-0"></div>
                <!-- 表單table -->
                <div class="col-12 bg-white">
                    <table class="w-100 table table-striped table-hover">
                        <thead class="vlr">
                            <tr>
                                <th style="writing-mode: horizontal-tb; text-align: start; vertical-align: bottom; ">cata_SN / pname</th>
                                <?php foreach($locals as $local){
                                    echo "<th id='local_{$local["id"]}'>".$local["fab_title"]."</br>（".$local["local_title"]."）"."</th>";
                                } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($catalogs as $catalog){
                                echo "<tr>";
                                    echo "<td id='cata_{$catalog["SN"]}' class='text-start'>".$catalog["SN"]."</br>".$catalog["pname"]."</td>";
                                    foreach($locals as $local){
                                        echo "<td id='{$local["id"]}_{$catalog["SN"]}'></td>";
                                    };
                                echo "</tr>";
                            } ?>
                        </tbody>
                    </table>
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
    var receive_lists = <?=json_encode($receive_lists);?>;                                                   // 引入receive_lists資料
    var receiveAmount   = [];                                           // 宣告變數陣列，承裝Receives年領用量
    
    // function show_receives(){
        // 彙整出SN年領用量
        Object(receive_lists).forEach(function(row){
            let csa = JSON.parse(row['cata_SN_amount']);
            Object.keys(csa).forEach(key =>{
                let pay = Number(csa[key]['pay']);
                let l_key = row['local_id'] +'_'+ key;
                if(receiveAmount[l_key]){
                    receiveAmount[l_key] += pay;
                }else{
                    receiveAmount[l_key] = pay;
                }
                // console.log(l_key, pay)
            })
        });

        console.log('receiveAmount:', receiveAmount)
        // 選染到Table上指定欄位
        Object.keys(receiveAmount).forEach(key => {
            let value = receiveAmount[key];
            $('#'+key).empty();
            $('#'+key).append(value);
        })

        // let sinn = '<b>** 自動帶入 年領用累計 ... 完成</b>~';
        // inside_toast(sinn);
    // }


</script>

<?php include("../template/footer.php"); ?>

