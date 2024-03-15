<?php
    require_once("../pdo.php");
    require_once("../sso.php");
    require_once("function.php");
    accessDenied($sys_id);
    
    $sort_cate_no = (isset($_REQUEST["cate_no"])) ? $_REQUEST["cate_no"] : "All";

    if(isset($_POST["delete"])){
        delete_catalog($_REQUEST);
        header("location:../catalog/");
        exit;
    }

    if(isset($_POST["submit"])){
        update_catalog($_REQUEST);
        header("location:../catalog/?cate_no={$sort_cate_no}");
        exit;
    }

    // // *** PNO篩選組合項目~~
    $_year = (isset($_REQUEST["_year"])) ? $_REQUEST["_year"] : "All"; // 全部
    // $_year = date('Y');                              // 今年
    $sort_PNO_year = array(  '_year' => $_year );

    $pnos       = show_pno($sort_PNO_year);             // 取得料號清單
    $categories = show_categories();                    // 取得分類
    $catalog    = edit_catalog($_REQUEST);              // 取得要編輯的器材

    if(empty($catalog)){
        echo "<script>history.back()</script>";         // 用script導回上一頁。防止崩煃
    }
?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>

<head>
    <link href="../../libs/aos/aos.css" rel="stylesheet">
    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
    <link href="../../libs/la_loading/la_loading.css" rel="stylesheet">
    <style>
        .box {
            margin: 0px;
            padding:0px;               /*div框的內距，為了不讓兩框文字相連*/
        }
        table thead {
            font-size: 14px;
            text-align: center;
            background-color: rgb(122, 162, 238);
        }
        table .btn {
            /* padding:2px 2px; */
            margin: 5px;
        }
        img {
            max-width: 100%;
            /* max-height: 50%; */
        }
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
        .cover .img {
            display: none;
    
        }
        .img:checked + label{
            border: 5px solid #f00;
        }
        .gallery {
            display: none;
        }
        .gallery-overlay {
            position: fixed;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,.8);
            top: 0;
            left: 0;
        }
        .gallery-container {
            position: fixed;
            background-color: #fff;
            width: 90%;
            left: 50%;
            top: 10%;
            height: 80%;
            transform: translateX(-50%);
            padding: 30px;
            z-index: 9999;
        }
    
        .tab-panel {
            display: none;
        }
        .tab-panel.active {
            display: block;
            animation: fadeIn 2s;
        }
        #cover img {
            max-height: 340px;
            /* text-align: center; */
            display:block; 
            margin:auto;
        }

        /* 限定整體高度 */
        .input-group {
            height: auto;
            /* height: 100%; */
        }
        .input-group .form-control,
        .input-group .btn {
            /* padding: 0.5rem; */
            /* line-height: 1.5; */
            height: 100%;
        }
    </style>
</head>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 bg-white border rounded p-4 my-2">
            <div class="row">
                <div class="col-12 col-md-6 py-0">
                    <h4>編輯catalog資訊</h4>
                </div>
                <div class="col-12 col-md-6 py-0 text-end">
                    <?php if(($_SESSION[$sys_id]["role"] == 0) && ($catalog["flag"] == "Off")){?>
                        <form action="" method="post">
                            <input type="hidden" name="id" value="<?php echo $catalog["id"];?>">
                            <input type="submit" name="delete" value="刪除" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('我們不建議您刪除! 如果可以請用[flag顯示開關將]其關閉即可!\n\n確認刪除？')">
                        </form>
                    <?php }?>
                    <!-- loading 畫面css 2/4-->
                    <a href="#" id="show_loading" data-bs-toggle="modal" data-bs-target="#modal_loading" class="unblock">show_loading</a>
                    <button type="button" id="history_back" class="main-btn btn btn-secondary" onclick="history.back()">
                        <i class="fa fa-external-link" aria-hidden="true"></i> 回上頁
                    </button>
                </div>
            </div>
            <hr>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="row px-3">
                    <div class="col-12 col-md-6 rounded bg-light">
                        <label for="" class="form-label">PIC/圖片：</label><br>
                        <div id="PIC" class="text-end">
                            <?php if(isset($_GET["img"])){ ?>
                                <input type="hidden" name="PIC" value="<?php echo $_GET["img"]; ?>">
                                <div style="text-align:center;">
                                    <a href="#" target="_blank" title="img preView" class="" data-bs-toggle="modal" data-bs-target="#about-1">
                                        <img src="./images/<?php echo $_GET["img"]; ?>" class="img-thumbnail">
                                    </a>
                                </div>
                                <a href="#cover" id="selectImg" class="btn btn-outline-success"><i class="fa-solid fa-image"></i> 切換圖片</a>
                            <?php }else{ ?>
                                <a href="#cover" id="selectImg" class="btn btn-outline-success"><i class="fa-solid fa-image"></i> 選擇圖片</a>
                                <input type="hidden" name="PIC" value="">
                            <?php } ?>
                        </div>
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <div class="row">
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text" name="SN" id="SN" class="form-control" required placeholder="編號" value="<?php echo $catalog["SN"];?>">
                                    <label for="SN" class="form-label">SN/編號：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <select name="cate_no" id="cate_no" class="form-control" required>
                                        <option value="" selected hidden>--請選擇類別--</option>
                                        <?php foreach($categories as $cate){ ?>
                                            <option value="<?php echo $cate["cate_no"];?>" <?php echo ($cate["cate_no"] == $catalog['cate_no']) ? "selected":"";?>>
                                                <?php echo $cate["id"].". ".$cate["cate_no"]."_".$cate["cate_title"]." (".$cate["cate_remark"].")";?></option>
                                        <?php } ?>
                                    </select> 
                                    <label for="cate_no" class="form-label">category/分類：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>

                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <input type="text" name="pname" id="pname" class="form-control" required placeholder="品名" value="<?php echo $catalog["pname"];?>">
                                    <label for="pname" class="form-label">pname/品名：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <textarea name="cata_remark" id="cata_remark" class="form-control" style="height: 90px;" placeholder="敘述說明"><?php echo $catalog["cata_remark"];?></textarea>
                                    <label for="cata_remark" class="form-label">cata_remark/敘述說明：</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text" name="OBM" id="OBM" class="form-control" placeholder="品牌/製造商" value="<?php echo $catalog["OBM"];?>">
                                    <label for="OBM" class="form-label">OBM/品牌/製造商：</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text"  name="model" id="model" class="form-control" placeholder="型號" value="<?php echo $catalog["model"];?>">
                                    <label for="model" class="form-label">model/型號：</label>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text"  name="size" id="size" class="form-control" placeholder="尺寸" value="<?php echo $catalog["size"];?>">
                                    <label for="size" class="form-label">size/尺寸：</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text" name="unit" id="unit" class="form-control" required placeholder="單位" value="<?php echo $catalog["unit"];?>">
                                    <label for="unit" class="form-label">unit/單位：<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>

                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <textarea name="SPEC" id="SPEC" class="form-control" style="height: 90px;" placeholder="規格"><?php echo $catalog["SPEC"];?></textarea>
                                    <label for="SPEC" class="form-label">SPEC/規格：</label>
                                </div>
                            </div>

                            <div class="col-12 py-1">
                                <div class="col-12 p-3 border rounded" id="selectScomp_no" style="background-color: rgba(255, 255, 129, .5);">
                                    <label for="" class="from-label">scomp_no：(-- 請選擇供應商 --)</label><br>
                                    <div class="row">
                                        <!-- 第一排 顯示已加入名單+input -->
                                        <div class="col-12 px-4 py-0">
                                            <div id="selectScomp_noItem"></div>
                                            <input type="hidden" name="scomp_no[]" id="scomp_no" class="form-control" placeholder="已加入的供應商">
                                        </div>
                                        <!-- 第二排 搜尋功能 -->
                                        <div class="col-12 px-4">
                                            <div class="input-group search" id="selectScomp_noForm">
                                                <input type="text" id="key_word" class="form-control" placeholder="請輸入統編或廠商名稱" aria-label="請輸入統編或廠商名稱">
                                                <button class="btn btn-outline-secondary" type="button" onclick="search_fun();">查詢</button>
                                                <button class="btn btn-outline-secondary" type="button" onclick="resetMain();">清除</button>
                                            </div>
                                        </div>
                                        <!-- 第三排 放查詢結果-->
                                        <div class="result" id="result">
                                            <table id="result_table" class="table table-striped table-hover"></table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 py-1">
                                <table>
                                    <tr>
                                        <td style="text-align: right;">
                                            <label for="flag" class="form-label">flag/顯示開關：</label>
                                        </td>
                                        <td>
                                            <input type="radio" name="flag" id="On" class="form-check-input" value="On" <?php echo $catalog["flag"] == "On" ? "checked":"";?>>&nbsp
                                            <label for="On" class="form-check-label">On</label>
                                        </td>
                                        <td>
                                            <input type="radio" name="flag" id="Off" class="form-check-input" value="Off" <?php echo $catalog["flag"] == "Off" ? "checked":"";?>>&nbsp
                                            <label for="Off" class="form-check-label">Off</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <!-- 暫時 -->
                        <div class="row">
                            <div class="col-12 py-1">
                                <div class="form-floating">
                                    <select name="part_no" id="part_no" class="form-select">
                                        <option value="" selected hidden>-- 請選擇 料號 (建議由料號管理進行綁定!) --</option>
                                        <?php foreach($pnos as $pno){ ?>
                                            <option value="<?php echo $pno["part_no"];?>" <?php echo ($pno["cata_SN"] == $catalog['SN']) ? "selected":"";?>>
                                                <?php echo $pno["_year"]."_".$pno["part_no"];
                                                    echo $pno["size"] ? " - ".$pno["size"]:"";
                                                    echo $pno["pno_remark"] ? " (".$pno["pno_remark"].")":"";
                                                    echo $pno["cata_SN"] ? " [".$pno["cata_SN"]."]":"";
                                                    ?></option>
                                        <?php } ?>
                                    </select>    
                                    <label for="part_no" class="form-label">part_no/料號：<sup class="text-danger"> * 建議由料號管理進行綁定!</sup></label>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text" name="buy_a" id="buy_a" class="form-control" placeholder="x領用倍數" value="<?php echo $catalog["buy_a"];?>">
                                    <label for="buy_a" class="form-label">安量倍數.a：</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-1">
                                <div class="form-floating">
                                    <input type="text" name="buy_b" id="buy_b" class="form-control" placeholder="x領用倍數" value="<?php echo $catalog["buy_b"];?>">
                                    <label for="buy_b" class="form-label">安量倍數.b：</label>
                                </div>
                            </div>
                        </div>

                        <div style="font-size: 12px;" class="text-end">
                            updated_at：<?php echo $catalog["updated_at"];?> / by：<?php echo $catalog["updated_user"];?>
                        </div>
                        
                    </div>
                </div>
                <hr>
                <div class="text-end">
                    <input type="hidden" value="<?php echo $catalog["id"];?>" name="id">
                    <input type="hidden" name="updated_user" value="<?php echo $_SESSION["AUTH"]["cname"];?>">
                    <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                        <input type="submit" value="儲存" name="submit" class="btn btn-primary">
                    <?php } ?>
                    <input type="button" value="取消" class="btn btn-secondary" onclick="history.back()">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 摺疊頁簽1-Cover -->
    <div class="tab-panel my-5 cover" id="cover" <?php if(isset($_GET["cover"])){ ?> style="display:block" <?php } ?>>
        <div class="col-12">
            <div class="row justify-content-center">
                <div class="col-11 bg-light rounded p-4">
                    <div class="row">
                        <div class="col-12 col-md-6 py-0">
                            <h4>選擇PIC照片</h4>
                        </div>
                        <div class="col-12 col-md-6 py-0 text-end">
                            <form action="upload_edit.php" method="post" enctype="multipart/form-data">
                                <div class="input-group">
                                    <input type="hidden" value="<?php echo $catalog["id"]; ?>" name="id">
                                    <input type="file" class="form-control" name="img" id="img">
                                    <button type="submit" class="btn btn-outline-secondary" style="height: 100%;">上傳圖片</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <hr>
                    <?php 
                        $galleries = glob("images/*.{jpeg,jpg,png,gif,webp,JPG}",GLOB_BRACE);
                        foreach($galleries as $g){ ?>
                            <input type="radio" name="img" id="<?php echo $g; ?>" value="<?php echo $g; ?>" class="img">
                            <label for="<?php echo $g; ?>">
                                <img src="<?php echo $g; ?>" width="200">
                            </label>
                            <form action="deleteCover_edit.php" class="d-inline-block">
                                <input type="hidden" value="<?php echo $catalog["id"]; ?>" name="id">
                                <input type="hidden" value="<?php echo $g; ?>" name="img">
                                <input type="submit" value="刪除" class="btn btn-sm btn-xs btn-danger" onclick="return confirm('確認刪除？')">
                            </form>
                    <?php } ?>
                    <hr>
                    <div class="text-end">
                        <a href="#" class="selected btn btn-primary">送出</a>
                        <a href="#" class="cancel btn btn-secondary">取消</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- 彈出畫面說明模組 -->
    <div class="modal fade" id="about-1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">preView 預覽：<?php echo $_GET["img"]; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5" style="text-align:center;" >
                    <img src="./images/<?php echo $_GET["img"]; ?>" style="height: 100%;" class="img-thumbnail">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<!-- loading 畫面css 3/4-->
    <div class="modal fade" id="modal_loading" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="la-ball-spin-clockwise-fade-rotating la-3x" id="la_loading">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
    </div>
    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>

<script src="../../libs/aos/aos.js"></script>
<script src="../../libs/aos/aos_init.js"></script>

<script>

    //選擇圖片函數
    $(function(){
        $('#selectImg').click(function(){
            $('.cover').show();
        })

        $('.cancel').click(function(){
            $('.cover').hide();
        })

        $('.selected').click(function(){
            $.ajax({
                url:'edit.php',
                type:'get',
                data: {
                    id: '<?=$catalog["id"]?>',
                    img: $('.img:checked').val().substr(6 +1)      // 6 = images +1 = bypass %2f
                },
                success(){
                    location.href = this.url;
                    $('.cover').hide();
                },
                error(){

                }
            })
        })
    })

    function resetMain(){
        $("#result").removeClass("border rounded bg-white");
        $('#result_table').empty();
        document.querySelector('#key_word').value = '';
    }
    // 第一-階段：search Key_word
    function search_fun(){
        let search = $('.search > input').val().trim();
        if(!search || (search.length < 2)){
            alert("查詢字數最少 2 個字以上!!");
            return false;
        } 
        $.ajax({
            url:'api.php',
            method:'get',
            dataType:'json',
            data:{
                function: 'searchSupp',             // 操作功能
                key_word: search                    // 查詢對象key_word
            },
            success: function(res){
                var res_r = res["result"];
                postList(res_r);                    // 將結果轉給postList進行渲染
            },
            error (){
                console.log("search error");
            }
        })
    }
    // 第一階段：渲染功能
    function postList(res_r){
        // 清除表頭
        $('#result_table').empty();
        // $("#result").addClass("border rounded bg-white");
        $("#result").addClass("bg-white");
        // 定義表格頭段
        var div_result_table = document.querySelector('.result table');
        var Rinner = "<thead><tr>"+
                        "<th>供應商_中文</th>"+"<th>供應商_英文</th>"+"<th>聯絡人</th>"+"<th>統一編號/抬頭</th>"+"<th>select</th>"+
                    "</tr></thead>" + "<tbody id='tbody'>"+"</tbody>";
        // 鋪設表格頭段thead
        div_result_table.innerHTML += Rinner;
        // 定義表格中段tbody
        var div_result_tbody = document.querySelector('.result table tbody');
        $('#tbody').empty();
        var len = res_r.length;
        for (let i=0; i < len; i++) {
            div_result_tbody.innerHTML += 
                '<tr>' +
                    '<td>' + res_r[i].scname +'</td>' +
                    '<td>' + res_r[i].sname + '</td>' +
                    '<td>' + res_r[i].contact + '</td>' +
                    '<td>' + res_r[i].comp_no +'/'+ res_r[i].inv_title + '</td>' +
                    '<td>' + '<button type="button" class="btn btn-default btn-xs" id="'+res_r[i].comp_no+'" value="'+res_r[i].comp_no+','+ res_r[i].scname+'" onclick="tagsInput_me(this.value);">'+
                    '<i class="fa-regular fa-circle"></i></button>' + '</td>' +
                '</tr>';
        }
        // edit_pm.handleUpdate();
    }
    // 第二階段：點選、渲染模組
    var tags = [];
    function tagsInput_me(val) {
        let scname = val.substr(val.search(',',)+1);        // 指定scname
        let comp_no = val.substr(0, val.search(','));       // 指定comp_no
        if (val !== '') {
            tags.push(val);
            $('#selectScomp_noItem').append('<div class="tag">' + scname + '<span class="remove">x</span></div>');
            let tag_supp = document.getElementById(comp_no);
            if(tag_supp){
                tag_supp.value = '';
                // $("#"+comp_no+" .fa-circle").toggleClass("fa-circle-check");
            }
            let scomp_no = document.getElementById('scomp_no');
            if(scomp_no){
                scomp_no.value = tags;
            }
        }
        // edit_pm.handleUpdate();
    }
    // 第二階段：移除單項模組
    $('#selectScomp_noItem').on('click', '.remove', function() {
        var tagIndex = $(this).closest('.tag').index();
        let tagg = tags[tagIndex];                              // 取得目標數值 comp_no,cname
        let comp_no = tagg.substr(0, tagg.search(','));         // 指定 comp_no
        let tag_supp = document.getElementById(comp_no);
        if(tag_supp){
            tag_supp.value = tagg;
            // $("#"+comp_no+" .fa-circle-check").toggleClass("fa-circle");
        }
        tags.splice(tagIndex, 1);                               // 自陣列中移除
        $(this).closest('.tag').remove();                       // 自畫面中移除
        let scomp_no = document.getElementById('scomp_no');
        if(scomp_no){
            scomp_no.value = tags;
        }
    });

    $(document).ready( function () {
        resetMain();        // 先清除表單
        $('#scomp_no').value = '';
        $('#selectScomp_noItem').empty();
        tags = [];                                              // 清除tag名單陣列
        var pmLists = {};
        // 第0階段：套用既有數據
        var intt_val_str = <?=json_encode($catalog["scomp_no"]);?>;         // 引入副PM資料            
        var intt_val = [];
        if(intt_val_str.length !== 0){                          // 過濾原本spm字串不能為空
            intt_val = intt_val_str.split(',');                 // 直接使用 split 方法得到陣列
            for(let i=0; i < intt_val.length; i=i+2){   
                tagsInput_me(intt_val[i]+','+intt_val[i+1]);    // 利用合併帶入
            }
        }
    })
</script>

<?php include("../template/footer.php"); ?>