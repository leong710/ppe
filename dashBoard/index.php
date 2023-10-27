<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    require_once("count_visitor.php");              // 網站計數器
    if(!isset($_SESSION)){                          // 確認session是否啟動
		session_start();
	}
    if(!empty($_SESSION["AUTH"]["pass"]) && !isset($_SESSION[$sys_id])){
		accessDenied_sys($sys_id);                  // 套用sys_id權限
    }

    // $stock_percentages = show_site_percentage();    // 各廠器材存量百分比(缺點：截長補短的問題) table-1 驗證用
    // $catalog_stocks = show_stock_byCatalog();       // 器材存量的清單 table-2
    // $stock_losts = show_stock_lost();               // 各廠有缺少的清單 table-3

    // 今年年份
    $today_year = date('Y');
        // 半年分界線
        if(date('m') <= 6 ){
            $half = "H1";
        }else{
            $half = "H2";
        }
        // 建立查詢陣列for顯示今年點檢表
        $check_yh = array(
            'checked_year' => $today_year,
            'half' => $half
        );
    // dashBoard-表頭數據用，秀出-site短缺/器材短缺 table-0 左
    // $stock_db1 = show_stock_db1();
    // dashBoard-表頭數據用，秀出-點檢達成率/未完成site數 table-0 右
    // $stock_db2 = show_stock_db2($check_yh);

    if(isset($_SESSION[$sys_id])){
        $login_AUTH = TRUE;
    }else{
        $login_AUTH = FALSE;
    }
    // 讓一般nobody用戶帶到 我的申請文件
    if($login_AUTH && $_SESSION[$sys_id]["role"] == 3){
        header("refresh:0;url=../receive/");
        exit;
    }
?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <style>
        .TOP {
            background-image: URL('../images/stock2.jpg');
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
            /* overflow: hidden; 會影響表頭黏貼功能*/
            background-attachment: fixed;
            background-position: center top;
            /* background-position: left top; */
            background-repeat: no-repeat;
            /* background-size: cover; */
            background-size: contain;
            padding-top: 300px;
        }
        .ul , li {
            color: #FFFFFF;
        }
        .unblock{
            display: none;
        }
        .dbShow {
            text-align: center;
        }
        .cs_a{
            /* background-image: linear-gradient(to bottom, #548C00, #CCFF80); */
            background-color: #CCFF80;
        }
        .box_a{
            background-color: gray;
            border-radius: 10px;
        }
        .title {
            color: #FFFFFF;
            font-size: 20px;
        }
        .title_end {
            color: #FFFFFF;
            /* text-align: right; */
            font-size: 15px;
        }
        .inside {
            color: #FFD306;
            line-height: 85px;
            font-size: 70px;
            font-weight: bolder;
        }
        #toggle .ball {
            width: 90px;
            height: 90px;
            background-color: greenyellow;
            border-radius: 50%;
            line-height: 90px;
            /* font-weight:bold; */
            vertical-align: middle;
            margin: auto;               /*置中對齊*/
            display: flex;
            /* text-shadow: 3px 3px 5px rgba(0,0,0,.5); */
            box-shadow:3px 3px 9px gray;
            /* text-align: center; */
            /* align-items: center; */
            /* position: absolute; */
            /* top: 50%; */
            /* left: 50%; */
            /* transform: scale(0.9); */
            /* transform: translateY(-50%); */
            /* box-shadow: inset -7px -7px 7px 1px lightcyan; */
            transition: 1s;
        }
        #toggle .ball2 {
            width: 75px;
            height: 75px;
            background-color: white;
            border-radius: 50%;
            line-height: 75px;
            font-weight: bold;
            vertical-align: middle;
            margin: auto;               /*置中對齊*/
            /* display: flex; */
            text-shadow: 3px 3px 5px rgba(0,0,0,.5);
            box-shadow: inset 3px 3px 9px gray;
            /* position: absolute; */
        }
        #toggle .ball:hover{
            /* width: 90px; */
            /* height: 90px; */
            /* 字體變大1.3倍 */
            font-size: 1.3em;
            /* 向上抬升 */
            transform: translateY(-30%);
        }
    </style>
</head>

<body>
    <div class="TOP">
        <div class="col-12">
            <div class="row justify-content-center">
                <div class="col_xl_10 col-10 rounded p-3 py-2" style="background-color: rgba(255, 255, 255, .8);">
                    <div class="col-12 py-0">
                        <!-- 抬頭h1的位置 -->
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <h3>tnESH PPE防護具管理</h3>
                            </div>
                            <div class="col-12 col-md-6 text-end">
                                <div id="show_time1" class="text-end" style="display:inline-block;">
                                    <script>
                                        setInterval("show_time1.innerHTML=new Date().toLocaleString()+' 星期'+'日一二三四五六'.charAt(new Date().getDay());",1000);  
                                    </script>
                                </div>
                                <!-- 網站人數 -->
                                <div class="my-2 t-center" style="display:inline-block;">
                                    <span>您是第 <?php echo $counter; //輸出計數器 ?> 位訪客！</span>
                                </div>
                            </div>
                        </div>

                     </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="row justify-content-center">

            <div class="col_xl_10 col-10">
                <!-- Bootstrap Alarm -->
                <div id="liveAlertPlaceholder"></div>
                

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

<script>


    function open_div(obj){
        $("#"+obj+" > .fa-chevron-circle-down").toggleClass("fa-chevron-circle-up");
        if($("#"+obj+"_div").css("display")=="none"){
            $("#"+obj+"_div").css("display","block")
            return;
        }
        if($("#"+obj+"_div").css("display")=="block"){
            $("#"+obj+"_div").css("display","none")
            return;
        }
    }

    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
</script>
<!-- 警告視窗 -->
<script>
    var checkList = document.getElementById("checkList");   // 檢點表單
    var alertPlaceholder = document.getElementById("liveAlertPlaceholder")      // Bootstrap Alarm

    // 神奇PHP變數帶入js方法
    // <php echo "var check_yh_list_num ='$numChecked';";?>       // 年度檢查筆數
    // <php echo "var login_AUTH ='$login_AUTH';";?>              // 是否已經登入

    // Bootstrap Alarm function
    function alert(message, type) {
        var wrapper = document.createElement('div')
        wrapper.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible" role="alert">' + message + '<a href="#"  id="checkItem" class="alert-link">[打開點檢表]</a>' + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'

        alertPlaceholder.append(wrapper)
    }
    // 假如index找不到當下存在已完成的表單，而且user已經登入就alarm它!
    // if (login_AUTH && check_yh_list_num == '0') {
    //     alert('*** <?php echo $today_year;?> 年 <?php echo $half;?> 年度 衛材儲存量確認開始了! 請務必在指定時間前完成確認~ ', 'danger')
    // }

</script>

<?php include("../template/footer.php"); ?>

