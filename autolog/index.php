<?php
    require_once("../pdo.php");
    require_once("function.php");
    accessDeniedAdmin();
    
	$sys_id = "todo";
    if(isset($_POST["storeLog"])){
        $msg = storeLog($_REQUEST);
    }
    if(isset($_POST["updateLog"])){
        updateLog($_REQUEST);
    }
    if(isset($_POST["deleteLog"])){
        deleteLog($_REQUEST);
    }
    $log_list = show_log_list();
?>

<?php include("../template/header.php"); ?>
<?php include("../template/nav.php"); ?>
<head>
    <!-- goTop滾動畫面aos.css 1/4-->
    <link href="../css/aos.css" rel="stylesheet">
    <!-- Jquery -->
    <script src="../css/jquery.min.js" referrerpolicy="no-referrer"></script>
    <!-- dataTable參照 https://ithelp.ithome.com.tw/articles/10230169 -->
    <!-- data table CSS+JS -->
    <link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="../css/jquery.dataTables.js"></script>

    <!-- dataTable RWD JS -->
    <script src="../css/jquery.dataTables.min.js" referrerpolicy="no-referrer"></script>
    <script src="../css/dataTables.responsive.min.js" referrerpolicy="no-referrer"></script>

    <!-- <link rel="stylesheet" href="../css/style.css?=time()"> -->

    <style>
        tr > td {
            text-align: left;
        }
        .word-break {
            word-break: break-all; 
            white-space: normal;
        }
        .unblock{
            display: none;
        }
    </style>
</head>
<body>
    <!-- <div class="container"> -->
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col_xl_12 col-12 rounded my-2 p-3" style="background-color: rgba(255, 255, 255, .6);">
                <div id="table">
                    <!-- todo_list -->
                    <div id="todo_list_table" class="col-12">
                        <div class="row">
                            <div class="col-12 col-md-8 py-0">
                                <h3>autoLog 記錄項目管理頁面</h3>
                            </div>
                            <div class="col-12 col-md-4 py-0 text-end">
                                <a href="#access_info" target="_blank" title="連線說明" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#access_info">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i> API連線說明</a>

                                <!-- <a href="#" target="_blank" title="個人" class="btn btn-success" ><i class="fa fa-info-circle" aria-hidden="true"></i> 個人</a> -->

                                <button title="新增log" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_log"><i class="fa fa-plus"></i> 新增</button>
                                <button class="btn btn-sm btn-xs btn-info unblock" data-bs-toggle="modal" data-bs-target="#edit_log" id="editLog_btn">編輯</button>

                            </div>
                        </div>
                        <hr>
                        <div class="col-12 rounded bg-light">
                            <div class="col-12 p-0">
                                <table id="log_table" class="display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th data-toggle="tooltip" data-placement="bottom" title="AUTO_INCREMENT">id</th>
                                            <th data-toggle="tooltip" data-placement="bottom" title="進資料庫時間">T_STAMP</th>
                                            <th data-toggle="tooltip" data-placement="bottom" title="系統名稱">sys</th>
                                            <th data-toggle="tooltip" data-placement="bottom" title="記錄事項">remark</th>
                                        </tr>
                                    </thead>
                                    <!-- 這裡開始抓SQL裡的紀錄來這裡放上 -->
                                    <tbody>
                                        <?php foreach($log_list as $log){ ?>
                                            <tr id="<?php echo $log['id']; ?>">
                                                <td><?php echo $log['id']; ?></td>
                                                <td><?php echo $log['t_stamp']; ?></td>
                                                <td><?php echo $log['sys']; ?></td>
                                                <td class="word-break"><?php echo $log['remark']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- </div> -->

<!-- 彈出畫面模組 新增log-->
    <div class="modal fade" id="add_log" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h4 class="modal-title">新增log</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body px-4" id="add" style="background-color: #D4D4D4;">
                        <div class="row">
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating">
                                    <input type="datetime-local" class="form-control" id="t_stamp" name="t_stamp" value="<?php echo date('Y-m-d\TH:i'); ?>">
                                    <label for="t_stamp" class="form-label">T_STAMP/進資料庫時間(可不填)</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="sys" name="sys">
                                    <label for="sys" class="form-label">sys/系統別:</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 py-0">
                                <div class="form-floating">
                                    <textarea type="textarea" class="form-control" id="remark" name="remark" style="height: 90px;" required ></textarea>
                                    <label for="remark" class="form-label">remark/事項:事項描述(最大兩百個中文字)<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="submit" name="storeLog" id="storeLog" value="新增" class="btn btn-primary">
                            <input type="reset" value="清除" class="btn btn-info" style="color: white; text-shadow: 3px 3px 5px rgba(0,0,0,.5);">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- 彈出畫面模組 編輯log-->
    <div class="modal fade" id="edit_log" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <div class="col-12 col-md-6 py-1">
                        <h4 class="modal-title">編輯log</h4>
                    </div>
                    <div class="col-12 col-md-5 py-1 text-end">
                        <form action="" method="post">
                            <input type="hidden" name="id" id="deleteLog_id" value="">
                            <?php if($_SESSION[$sys_id]["role"] <= 1){ ?>
                                <input type="submit" name="deleteLog" value="刪除" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" title="role <=1 限定" onclick="return confirm('確認刪除？')">
                            <?php }?>
                        </form>
                    </div>
                    <div class="col-12 col-md-1 py-1 text-end">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <form action="" method="post">
                    <div class="modal-body px-4" id="edit" style="background-color: #D4D4D4;">
                        <div class="row">
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating">
                                    <input type="datetime-local" class="form-control" id="edit_t_stamp" name="t_stamp" value="">
                                    <label for="edit_t_stamp" class="form-label">T_STAMP/進資料庫時間(可不填)</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 py-0">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="edit_sys" name="sys">
                                    <label for="edit_sys" class="form-label">sys/系統別:</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 py-0">
                                <div class="form-floating">
                                    <textarea type="textarea" class="form-control" id="edit_remark" name="remark" style="height: 90px;" required ></textarea>
                                    <label for="edit_remark" class="form-label">remark/事項:事項描述(最大兩百個中文字)<sup class="text-danger"> *</sup></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-end">
                            <input type="hidden" id="edit_id" name="id" >
                            <input type="submit" name="updateLog" id="updateLog" value="更新" class="btn btn-primary">
                            <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- 彈出畫面模組-API連線說明 -->
    <div class="modal fade" id="access_info" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">API連線說明</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="col-12 py-0 px-4">
                        <div>
                            <p>method：POST / JSON</p>
                        </div>
                        <table>
                            <thead> 
                                <tr>
                                    <th>SET</th>
                                    <th>KEY</th>
                                    <th>VALUE</th>
                                    <th>REMARK</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>function</td>
                                    <td>storeLog</br>editLog</br>deleteLog</br>updateLog</td>
                                    <td>1.storeLog 儲存log</br>2.editLog 讀取log</br>3.deleteLog 刪除log</br>4.updateLog 更新log</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>T_STAMP</td>
                                    <td>now()_value</td>
                                    <td>進資料庫時間(系統追查用,可不填此欄位)</td>

                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>sys</td>
                                    <td>系統別</td>
                                    <td>什麼系統驅動此事</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>remark</td>
                                    <td>事項</td>
                                    <td>系統做了什麼事要記錄</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-end">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">關閉</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- 彈出便利貼messages模組 -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
            <div class="toast-header bg-warning">
                <!-- <img src="..." class="rounded me-2" alt="..."> -->
                <i class="fa-solid fa-triangle-exclamation"></i>&nbsp
                <strong class="me-auto">Bootstrap</strong>
                <small>Long time ago</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toast-body">
                <!-- <h4>innerHTML</h4> -->
            </div>
        </div>
    </div>

<!-- goTop滾動畫面DIV 2/4-->
    <div id="gotop">
        <i class="fas fa-angle-up fa-2x"></i>
    </div>
</body>
<!-- goTop滾動畫面jquery.min.js+aos.js 3/4-->
<script src="../css/aos.js"></script>
<!-- goTop滾動畫面script.js 4/4-->
<script src="../css/script.js"></script>

<script>

    // 提醒便利貼起始設定
    var toastLiveExample = document.getElementById('liveToast')             // 定義便利貼主體
    if (toastLiveExample){                                                  // 定義便利貼主體
        toastLiveExample.addEventListener('hidden.bs.toast', function () {  // 增加監聽隱藏時的觸發動作
            var toast = new bootstrap.Toast(toastLiveExample);          // 定義便利貼
            toast.dispose();                                            // 隱藏吐司元素
        })
    }

    // 在任何地方啟用工具提示框
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })

    // step3.鋪編輯Log畫面
    function edit_module(row_id){
        let logList = <?=json_encode($log_list)?>;                              //把$log_list陣列encode，裝在json
        let editLog_btn = document.querySelector("#editLog_btn");               // 定義show_modal的觸發按鈕(已隱藏)
        Object(logList).forEach(function(row){                                  // step3-1.將原排程陣列逐筆繞出來
            if(row['id'] == row_id){
                let item = ['id','t_stamp','sys','remark'];
                Object(item).forEach(function(item_key){                        // step3-2.鋪畫面到module
                    document.querySelector('#edit_'+item_key).value = row[item_key]; 
                })
                document.querySelector('#deleteLog_id').value = row['id'];     // this id.no for delete form
                editLog_btn.click();                                           // step3-3.開啟 彈出畫面模組 for user編輯
            }
        })
    }

    // dataTable 2 https://ithelp.ithome.com.tw/articles/10272439
    $(document).ready( function () {
        $('#log_table').DataTable({
            // 排序
            "order": [[ 1, "desc" ]],
            // 顯示長度
            "pageLength": 25,
            // 中文化
            "language":{
                url: "../css/dataTable_zh.json"
            }
        });

        // step2.table可編輯畫面tr定義
        var table_tbody = document.querySelectorAll('#log_table tbody tr');
        for(let table_tbody_tr of table_tbody){         // step2-1.等待點擊觸發-tab_1
            // table_tbody_tr.ondblclick = e => {     // 雙擊
            table_tbody_tr.onclick = e => {             // 單擊
                var row_id = table_tbody_tr.id;
                edit_module(row_id);
            }
        }

    } );
</script>

<?php include("../template/footer.php"); ?>