<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);

    $locals = show_local();
    $catalogs = show_catalogs();
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
</head>
<body>
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_11 col-11 p-4 rounded" style="background-color: rgba(255, 255, 255, .8);" >
                <table>
                    <thead class="vlr">
                        <tr>
                            <th>cata_SN</th>
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


<?php include("../template/footer.php"); ?>

