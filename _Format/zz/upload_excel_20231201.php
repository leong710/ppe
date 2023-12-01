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
                        echo '<div class="col-12 justify-content-center border bg-light">';
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
                                    // $SN_row = checkSN($SN_replace);
                                    $SN_row = check_something($submit, $SN_replace);

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
                                    handleInvalidRow($submit, $SN_row, $amount_replace);
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
                                echo '<div name="" id="stopUpload" style="color: red; font-weight: bold;">'."有".$stopUpload."個資料有誤。請確認後再上傳。".'</div>';
                                echo '</div>';
                            }
                        }
                    }
                }
            else if ($submit === 'supp') {
                // 上傳--供應商supp
                    if (isset($_FILES['excelFile'])) {
                        $file = $_FILES['excelFile']['tmp_name'];
                        $spreadsheet = IOFactory::load($file);
                        $worksheet = $spreadsheet->getActiveSheet();
                        $data = $worksheet->toArray();
                        // echo print_r($data);
                        // 在此处可以对$data进行进一步处理
                        // 将结果输出为HTML表格
                        $theadTitles = array('供應商中文名稱','供應商英文名稱','發票抬頭','統編','發票地址','聯絡人','連絡電話','電子信箱','傳真','註解說明');
                        // 計算陣列中的"key"
                        $keyCount = count($theadTitles);
                        echo '<div class="col-12 justify-content-center border bg-light">';
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
                                    // if ($rowIndex <= 1) {
                                        continue; 
                                    }
                                echo '<tr>';
                                // 避免輸入的comp_no代碼中有 空白
                                // $comp_no_replace = strtoupper(trim(str_replace(' ', '', $row[0])));
                                $comp_no_replace = trim(str_replace(' ', '', $row[3]));
                                    // // 避免輸入的Phone代碼中有 空白
                                    // $phone_replace = trim(str_replace(' ', '', $row[1]));
                                // 查詢comp_no是否存在
                                $supp_row = check_something($submit, $comp_no_replace);
                                
                                if ($supp_row["state"] !== "NA") {
                                    echo '<td>' . htmlspecialchars($row[0]) . '</td>';
                                    echo '<td>' . htmlspecialchars($row[1]) . '</td>';
                                    echo '<td>' . htmlspecialchars($row[2]) . '</td>';
                                    echo '<td>' . htmlspecialchars($comp_no_replace) . '</td>';
                                    echo '<td>' . htmlspecialchars($row[4]) . '</td>';
                                    echo '<td>' . htmlspecialchars($row[5]) . '</td>';
                                    echo '<td>' . htmlspecialchars($row[6]) . '</td>';
                                    echo '<td>' . htmlspecialchars($row[7]) . '</td>';
                                    echo '<td>' . htmlspecialchars($row[8]) . '</td>';
                                    echo '<td>' . htmlspecialchars($row[9]) . '</td>';
    
                                    $process = array(
                                        'scname'        => $row[0],
                                        'sname'         => $row[1],
                                        'inv_title'     => $row[2],
                                        'comp_no'       => $comp_no_replace,
                                        '_address'      => $row[4],
                                        'contact'       => $row[5],
                                        'phone'         => $row[6],
                                        'email'         => $row[7],
                                        'fax'           => $row[8],
                                        'supp_remark'   => $row[9]
                                    );
                                    $result[] = $process;

                                }else {
                                    handleInvalidRow($submit, $supp_row, $comp_no_replace);
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
            else if ($submit === 'contact') {
                // 上傳--聯絡人contact
                    if (isset($_FILES['excelFile'])) {
                        $file = $_FILES['excelFile']['tmp_name'];
                        $spreadsheet = IOFactory::load($file);
                        $worksheet = $spreadsheet->getActiveSheet();
                        $data = $worksheet->toArray();
                        // echo print_r($data);
                        // 在此处可以对$data进行进一步处理
                        // 将结果输出为HTML表格
                        $theadTitles = array('聯絡人姓名', '連絡電話', '電子信箱', '傳真', '供應商統編', '註解說明');
                        // 計算陣列中的"key"
                        $keyCount = count($theadTitles);
                        echo '<div class="col-12 justify-content-center border bg-light">';
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
                                    // if ($rowIndex <= 1) {
                                        continue; 
                                    }
                                echo '<tr>';
                                // 避免輸入的cname代碼中有 空白
                                // $cname_replace = strtoupper(trim(str_replace(' ', '', $row[0])));
                                $cname_replace = trim(str_replace(' ', '', $row[0]));
                                // 避免輸入的Phone代碼中有 空白
                                // $phone_replace = trim(str_replace(' ', '', $row[1]));
                                // 查詢cname是否存在
                                $contace_row = check_something($submit, $cname_replace);
                                
                                if ($contace_row["state"] !== "NA") {
                                    echo '<td>' . htmlspecialchars($cname_replace) . '</td>';
                                    echo '<td>' . htmlspecialchars($row[1]) . '</td>';
                                    echo '<td>' . htmlspecialchars($row[2]) . '</td>';
                                    echo '<td>' . htmlspecialchars($row[3]) . '</td>';
                                    echo '<td>' . htmlspecialchars($row[4]) . '</td>';
                                    echo '<td>' . htmlspecialchars($row[5]) . '</td>';
    
                                    $process = array(
                                        'cname'             => $cname_replace,
                                        'phone'             => $row[1],
                                        'email'             => $row[2],
                                        'fax'               => $row[3],
                                        'comp_no'           => $row[4],
                                        'contact_remark'    => $row[5]
                                    );
                                    $result[] = $process;

                                }else {
                                    handleInvalidRow($submit, $contace_row, $cname_replace);
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
            else if ($submit === 'pno') {
                // 上傳--Part_NO料號pno
                    if (isset($_FILES['excelFile'])) {
                        $file = $_FILES['excelFile']['tmp_name'];
                        $spreadsheet = IOFactory::load($file);
                        $worksheet = $spreadsheet->getActiveSheet();
                        $data = $worksheet->toArray();
                        // echo print_r($data);
                        // 在此处可以对$data进行进一步处理
                        // 将结果输出为HTML表格
                        $theadTitles = array('料號', '料號註解', '年度', '統器材編號編', '尺寸');
                        // 計算陣列中的"key"
                        $keyCount = count($theadTitles);
                        echo '<div class="col-12 justify-content-center border bg-light">';
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
                                    // if ($rowIndex <= 1) {
                                        continue; 
                                    }
                                echo '<tr>';
                                // 避免輸入的part_no代碼中有 空白
                                $part_no_replace = strtoupper(trim(str_replace(' ', '', $row[0])));
                                // 避免輸入的Phone代碼中有 空白
                                // $phone_replace = trim(str_replace(' ', '', $row[1]));
                                // 查詢cname是否存在
                                $part_no_row = check_something($submit, $part_no_replace);
                                
                                if ($part_no_row["state"] !== "NA") {
                                    echo '<td>' . htmlspecialchars($part_no_replace) . '</td>';
                                    echo '<td>' . htmlspecialchars($row[1]) . '</td>';
                                    echo '<td>' . htmlspecialchars($row[2]) . '</td>';
                                    echo '<td>' . htmlspecialchars($row[3]) . '</td>';
                                    echo '<td>' . htmlspecialchars($row[4]) . '</td>';
    
                                    $process = array(
                                        'part_no'        => $part_no_replace,
                                        'pno_remark'     => $row[1],
                                        '_year'          => $row[2],
                                        'cata_SN'        => $row[3],
                                        'size'           => $row[4]
       
                                    );
                                    $result[] = $process;

                                }else {
                                    handleInvalidRow($submit, $part_no_row, $part_no_replace);
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

    // for整合確認：確認stock/SN、supp/comp_no、contact/cname是否已經建立
    function check_something($submit, $query_item){
        $pdo = pdo();
        switch($submit){
            case "stock":           
                $sql_check = "SELECT * FROM _cata WHERE SN = ?";
                break;

            case "supp":
                $sql_check = "SELECT * FROM _supp WHERE comp_no = ?";
                break;

            case "contact":
                $sql_check = "SELECT * FROM _contact WHERE cname = ?";
                break;

            default:            // 預定失效 
                return; 
                break;
        }
        $stmt_check = $pdo -> prepare($sql_check);
        try {
            $stmt_check -> execute([$query_item]);
            if($stmt_check -> rowCount() > 0){     
                // 確認cname姓名是否已經註冊
                $result = $stmt_check->fetch();
                // 如果有值，則返回找到的資料。
                switch($submit){
                    case "stock":
                        $resultRecall = [
                            "state"         => "OK",
                            "query_item"    => $query_item,
                            "query_row"     => "SN",
                            "SN"            => $result["SN"],
                            "pname"         => $result["pname"]
                        ];    
                        break;
                    case "supp":
                        $resultRecall = [
                            "state"         => "NA",
                            "query_item"    => $query_item,
                            "query_row"     => "comp_no",
                            "comp_no"       => $result["comp_no"],
                            "scname"        => $result["scname"]
                        ];   
                        break;
                    case "contact":
                        $resultRecall = [
                            "state"         => "NA",
                            "query_item"    => $query_item,
                            "query_row"     => "cname",
                            "cname"         => $result["cname"],
                            "comp_no"       => $result["comp_no"]
                        ];  
                        break;
                    default:            // 預定失效 
                        break;
                }

            }else{
                // 返回"NA"值
                switch($submit){
                    case "stock":
                        $resultRecall = [
                            "state"         => "NA",
                            "query_item"    => $query_item,
                            "query_row"     => "SN",
                            "SN"            => $query_item
                        ];
                        break;
                    case "supp":
                        $resultRecall = [
                            "state"         => "OK",
                            "query_item"    => $query_item,
                            "query_row"     => "comp_no",
                            "comp_no"       => $query_item,
                            "scname"        => "NA"
                        ];
                        break;
                    case "contact":
                        $resultRecall = [
                            "state"         => "OK",
                            "query_item"    => $query_item,
                            "query_row"     => "cname",
                            "cname"         => $query_item,
                            "comp_no"       => "NA"
                        ];
                        break;
                    default:            // 預定失效 
                        break;
                }
            }
            return $resultRecall;

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    function handleInvalidRow($submit, $row_check, $item_check) {
        // 构建错误消息
        $errorMsg = generateErrorMessage($submit, $row_check, $item_check);
        // 输出错误消息
        if($row_check["state"] == "NA"){
            switch($submit){
                case "stock":           
                    echo '<td>'.$row_check["query_item"].'</td><td colspan="2">此欄位' . $errorMsg . '未建立</td>';
                    break;
                case "supp":
                    echo '<td>'.$row_check["scname"].'<td colspan="2"></td>';
                    echo '<td>'.$row_check["query_item"].'</td><td colspan="6" style="text-align: left;">此筆資料'.$errorMsg.'已被建立</td>';
                    break;
                case "contact":
                    echo '<td>'.$row_check["query_item"].'</td><td colspan="5" style="text-align: left;">此筆資料'.$errorMsg.'已被建立</td>';
                    break;
                default:            // 預定失效 
                    break;
            }
        }else{
            switch($submit){
                case "stock":           
                    echo '<td>'.$row_check["query_item"].'</td><td colspan="2">此欄位' . $errorMsg . '有誤</td>';
                    break;
                case "supp":
                    echo '<td>'.$row_check["scname"].'<td colspan="2"></td>';
                    echo '<td>'.$row_check["query_item"].'</td><td colspan="6" style="text-align: left;">此筆資料'.$errorMsg.'有誤</td>';
                    break;
                case "contact":
                    echo '<td>'.$row_check["query_item"].'</td><td colspan="5" style="text-align: left;">此筆資料'.$errorMsg.'有誤</td>';
                    break;
                default:            // 預定失效 
                    break;
            }
        }
        // 更新错误计数
        global $stopUpload;
        $stopUpload += 1;
    }

    function generateErrorMessage($submit, $row_check, $item_check) {
        $errorMsg = '';
        $conCount = 0;

        if($row_check["state"] == "NA"){
            $errorMsg .= '<span style="color: red;">'.$row_check["query_row"].'</span>';
            $conCount = 0;
        }

        switch($submit){
            case "stock":           
                if (!is_numeric($item_check)) {
                    if ($errorMsg !== '') {
                        $errorMsg .= '錯誤與';
                    }
                    $errorMsg .= '<span style="color: red;">Amount</span>';
                    $conCount++;
                }
                break;
            case "supp":
                break;
            case "contact":
                break;
            default:            // 預定失效 
                break;
        }

        if( $conCount > 1){
            $errorMsg .= '皆';
        }

        return $errorMsg;
    }

?>

