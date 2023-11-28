<?php
    require_once("../pdo.php");
    // 以下為EXCEL檔案上傳
    // 引入PhpSpreadsheet库
    require '../vendor/autoload.php';
    include("../template/header.php");

    // echo print_r($_REQUEST);

    use PhpOffice\PhpSpreadsheet\IOFactory;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['excelUpload'])) {
            $submit = $_POST['excelUpload'];
            if ($submit === '') {
            // "上傳"按钮被点击时执行的操作
                if (isset($_FILES['excelFile'])) {
                    $file = $_FILES['excelFile']['tmp_name'];
                    $spreadsheet = IOFactory::load($file);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $data = $worksheet->toArray();
                    // echo print_r($data);
                    // 在此处可以对$data进行进一步处理
                    // 将结果输出为HTML表格
                    $theadTitles = array('SN', '名稱', '需求數量');
                    // 計算陣列中的"key"
                    $keyCount = count($theadTitles);
                    echo '<div class="col-12 justify-content-center rounded bg-light">';
                    echo '<table><thead><tr>';
                    // 繞出每一個"theadTitles"的值
                    foreach ($theadTitles as $theadTitle){
                        echo '<th>' . $theadTitle . '</th>';
                    }
                    echo '</tr></thead>';
                    // 防止無資料送入的錯誤。
                    if(!isset($data[1])){
                        echo "<script>alert('請確認『上傳清冊』格式是否正確！');</script>";
                        return ;
        
                    }else{

                        echo '<tbody>';
                        // 設定一個"result"陣列
                        $result = array();
                        $stopUpload = 0;

                        // 繞出每一個Data的值
                        foreach ($data as $rowIndex => $row) {
                            // 跳過表頭
                                if ($rowIndex === 0) {
                                    continue; 
                                }
                            echo '<tr>';
                            // 避免輸入的SN代碼中有 空白、小寫
                            $SN_replace = strtoupper(trim(str_replace(' ', '', $row[0])));
                            // 避免輸入的Amount代碼中有 空白
                            $amount_replace = trim(str_replace(' ', '', $row[1]));
                                // 查詢SN是否存在
                                $SN_row = checkSN($SN_replace);

                            if ($SN_row["state"] !== "NA" && is_numeric($amount_replace)) {
                                echo '<td>' . htmlspecialchars($SN_replace) . '</td>';
                                echo '<td>' . htmlspecialchars($SN_row["pname"]) . '</td>';
                                echo '<td>' . htmlspecialchars($amount_replace) . '</td>';

                                // $process = array(
                                //     'SN'        => $SN_replace,
                                //     'amount'    => $amount_replace
                                // );
                                $process = [
                                    $SN_replace => $amount_replace
                                ];
                                $result[] = $process;
                            }else {
                                handleInvalidRow($SN_row, $amount_replace);
                            }

                            echo '</tr>'; 
                        };

                        echo '</tbody></table>';
                        // 增加卡"SN有誤"不能上傳。
                        // print_r($result);
                        // 如果"有誤"的累計資料等於"0"。
                        if( $stopUpload === 0 ){
                            // 將資料打包成JSON
                            $jsonString = json_encode($result);

                        // cata購物車鋪設前處理 
                            $cart_dec = (array) json_decode($jsonString);

                            // 以下是回傳給form購物車使用。
                            echo '<textarea name="" id="excel_json" class="form-control" style="display: none;">'.$jsonString.'</textarea>';
                            echo '</div>';
                        }else{
                            echo '<div name="" id="stopUpload" style="color: red; font-weight: bold;">'."有".$stopUpload."個，資料有誤。請確認後再上傳。".'</div>';
                            echo '</div>';
                        }
                    }
                }
            }
            else if ($submit === '其他按钮名称') {
                // 其他按钮被点击时执行的操作
                // ...
            }
        }
    }

    // 確認SN是否已經建立
    function checkSN($check_SN){
        $pdo = pdo();
        $sql_check = "SELECT * FROM _cata WHERE SN = ?";
        $stmt_check = $pdo -> prepare($sql_check);
        try {
            $stmt_check -> execute([$check_SN]);
            if($stmt_check -> rowCount() >0){     
                // 確認SN編號是否已經註冊
                $cata = $stmt_check->fetch();
            // 如果有值，則返回fab的id資料。
                $cataRecall = [
                    "state" => "OK",
                    "SN"    => $cata["SN"],
                    "pname" => $cata["pname"]
                ];
            }else{
                // 返回"NA"值
                $cataRecall = [
                    "state" => "NA",
                    "SN"    => $check_SN
                ];
            }
            return $cataRecall;

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function handleInvalidRow($SN_row_check, $amount_check) {
        // 构建错误消息
        $errorMsg = generateErrorMessage($SN_row_check, $amount_check);
        // 输出错误消息
        if($SN_row_check["state"] == "NA"){
            echo '<td>'.$SN_row_check["SN"].'</td><td colspan="2">此欄位' . $errorMsg . '未建立</td>';
        }else{
            echo '<td>'.$SN_row_check["SN"].'</td><td colspan="2">此欄位' . $errorMsg . '有誤</td>';
        }
        
        // 更新错误计数
        global $stopUpload;
        $stopUpload += 1;
    }

    function generateErrorMessage($SN_row_check, $amount_check) {
        $errorMsg = '';
        $conCount = 0;

        if($SN_row_check["state"] =="NA"){
            $errorMsg .= '<span style="color: red;">SN</span>';
            $conCount = 0;
        }

        if (!is_numeric($amount_check)) {
            if ($errorMsg !== '') {
                $errorMsg .= '錯誤與';
            }
            $errorMsg .= '<span style="color: red;">Amount</span>';
            $conCount++;
        }

        if( $conCount > 1){
            $errorMsg .= '皆';
        }

        return $errorMsg;
    }

?>

