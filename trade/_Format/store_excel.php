<?php
// 以下為EXCEL檔案上傳
 // 引入PhpSpreadsheet库
require '../vendor/autoload.php';
require_once("../pdo.php");
include("../template/header.php");

// echo print_r($_REQUEST);

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['excelUpload'])) {
        $submit = $_POST['excelUpload'];
        if ($submit === '') {
        // "上傳"按钮被点击时执行的操作
            if (isset($_FILES['excelFile'])) {
                $file = $_FILES['excelFile']['tmp_name'];
                $spreadsheet = IOFactory::load($file);
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
                // echo print_r($data);
                // 在此处可以对$data进行进一步处理
                // 将结果输出为HTML表格
                // $theadTitles = array('員工編號', '員工姓名', 'FAB', 'LOCAL', '部門代號', '部門名稱');
                $theadTitles = array('員工編號', '員工姓名', 'FAB', 'LOCAL', '部門代號', '廠處名稱');
                // 計算陣列中的"key"
                $keyCount = count($theadTitles);
                echo '<div class="col-12 justify-content-center rounded bg-light">';
                echo '<table>';
                echo '<thead>';
                echo '<tr>';
                // 繞出每一個"theadTitles"的值
                foreach ($theadTitles as $theadTitle){
                    echo '<th>' . $theadTitle . '</th>';
                }
                echo '</tr>';
                echo '</thead>';
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
                        // echo print_r($row);

                        // 引入這段是因為避免，輸入的工號中有"空白"
                        $startRow = trim(str_replace(' ', '', $row[1]));
                        // 引入這段是因為避免，輸入的部門代碼中有"空白"
                        $deptReplace = trim(str_replace(' ', '', $row[4]));

                        // 引入這段是因為避免，輸入的部門代碼中有"英文小寫"
                        $deptToupper = strtoupper($deptReplace);
                        // 讓"部門代碼"先行進行比對，並繞出fab、local。

                        if( strlen($deptToupper) === 8 && ctype_alnum($deptToupper)){
                            $deptRow = deptCheckExcel($deptToupper, strlen($deptToupper));
                        }else{
                            $deptRow = [
                                "state" => "Lack",
                                "strlen"=> strlen($deptToupper),
                            ];
                        }
                        
                        // 引入這段是因為避免，輸入的部門代碼中有"空白"
                        // $fabReplace = str_replace(' ', '', $data[$rowIndex][2]);
                        // 引入這段是因為避免，輸入的部門代碼中有"英文小寫"
                        // $fabToupper = strtoupper($fabReplace);
                        // 讓"廠區"先行進行比對，防止廠區錯誤。
                        // $fabRow = fabCheckExcel($fabToupper);
                        // echo print_r($deptRow)."<br>";
                        // 這邊在確認工號是否有問題。先確認是否為數字串；確認字串長度是否等於8位數
                        // if (is_numeric($startRow) && strlen($startRow) === 8) {
                        //     // 如果"部門代碼"檢查不是"NA"
                        //     if ($deptRow !== "NA") {
                        //         // 將資料帶入"processValidRow"
                        //         $processValidRow = processValidRow($theadTitles, $row, $keyCount, $data[$rowIndex], $deptRow);
                        //         // 將回傳結果加入$result陣列中
                        //         $result[] = $processValidRow;
                        //     } else {
                        //         handleInvalidRow($startRow, $fabRow, $deptRow);
                        //     }
                        // } else {
                        //     handleInvalidRow($startRow, $fabRow, $deptRow);
                        // }
                        // echo $deptRow;
                        if (is_numeric($startRow) && strlen($startRow) === 8 && $deptRow["state"] !== "NA" && $deptRow["state"] !== "Lack") {
                            echo '<td>' . htmlspecialchars($startRow) . '</td>';
                            echo '<td>' . htmlspecialchars($row[2]) . '</td>';
                            echo '<td>' . htmlspecialchars($deptRow["fab"]) . '</td>';
                            echo '<td>' . htmlspecialchars($deptRow["local"]) . '</td>';
                            echo '<td>' . htmlspecialchars($deptRow["code"]) . '</td>';
                            echo '<td>' . htmlspecialchars($deptRow["remark"]) . '</td>';
                            $process = [
                                '員工編號' =>$startRow,
                                '員工姓名' =>$data[$rowIndex][2],
                                'FAB' =>$deptRow["fab"],
                                'LOCAL' =>$deptRow["local"],
                                '部門代號' =>$deptRow["code"],
                                '廠處名稱' =>$deptRow["remark"]
                            ];
                            $result[] = $process;
                        }else {
                            handleInvalidRow($startRow, $deptRow);
                        }
                        

                        echo '</tr>'; 
                    };

                    echo '</tbody>';
                    echo '</table>';
                    // 增加卡"工號或廠區有誤"不能上傳。
                    // print_r($result);
                    // 如果"有誤"的累計資料等於"0"。
                    if( $stopUpload === 0 ){
                        // 將資料打包成JSON
                        $jsonString = json_encode($result);
                        // 以下是回傳給create儲存人員記錄使用。
                        echo '<textarea name="" id="json" style="display: none;">'.$jsonString.'</textarea>';
                        echo '</div>';
                        // $dataChange = json_encode($data);
                    }else{
                        echo '<div name="" id="stopUpload" style="color: red; font-weight: bold;">'."有".$stopUpload."個，資料有誤。請確認後再上傳。".'</div>';
                        echo '</div>';
                    }
                }
            }
        }
        elseif ($submit === '其他按钮名称') {
            // 其他按钮被点击时执行的操作
            // ...
        }
    }
}

// 確認fab是否已經被建立
function fabCheckExcel($dataFab){
    $pdo = pdo();
    // echo print_r(($allDept))."<br>";
    $sql = "SELECT * FROM _fab WHERE fab_title = ?";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([$dataFab]);
        $post = $stmt->fetchAll();
        // 確認fetch到的資料有沒有
        if(empty($post)){
            // 返回"NA"值
            return "NA";
        }else{
            // 如果有值，則返回fab的id資料。
            $id = $post[0]["id"];
            return $id;
        }
    }catch(PDOException $e){
        echo $e->getMessage();
    }
}

function deptCheckExcel($dataDept, $strlen){
    $pdo = pdo();
    $sql = "SELECT _code.*, _fab.id AS f_id, _fab.fab_title,
                   _local.id AS l_id, _local.local_title
            FROM _code
            LEFT JOIN _fab ON _code.fab_id = _fab.id
            LEFT JOIN _local ON _code.local_id = _local.id
            WHERE code = ?";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([$dataDept]);
        $post = $stmt->fetchAll();
        // 確認fetch到的資料有沒有
        if(empty($post)){
            // 返回"NA"值
            $deptRecall = [
                "state" => "NA",
                "strlen"=> $strlen,
            ];

            return $deptRecall;
        }else{
            // 如果有值，則返回fab的id資料。
            $deptRecall = [
                "state" => "OK",
                "code" =>$post[0]["code"],
                "fab" =>$post[0]["fab_title"],
                "local" =>$post[0]["local_title"],
                "remark" =>$post[0]["remark"],
            ];
            return $deptRecall;
        }
    }catch(PDOException $e){
        echo $e->getMessage();
    }

}

// function processValidRow($theadTitles, $row, $keyCount, $rowIndex, $specificRow = null) {
//     foreach (array_slice($row, 0, $keyCount) as $cell) {
//         // 引入這段是因為避免，輸入的文字串中有"空白"
//         $cell = str_replace(' ', '', $cell);
//         // 這段是將英文字母轉換成大寫。
//         $cellData = strtoupper($cell);
//         // echo $cell."-".$rowIndex[2]."-".$rowIndex[3]."<br>";
//         // echo print_r($specificRow)."<br>";
//         // 如果$specificRow不是null，才會執行以下部分。
//         if($specificRow !== null){
//             // 如果cell等於FAB時(這邊使用的比較為值)
//             if ($cell == $rowIndex[2]) {
//                 // 則FAB的數值，套用DEPT繞出來的
//                 $cell = $specificRow["fab"];
//             }
//             // 如果cell等於LOCAL時(這邊使用的比較為值)
//             if ($cell == $rowIndex[3]) {
//                 // 則LOCAL的數值，套用DEPT繞出來的
//                 $cell = $specificRow["local"];
//             }
//         }

//         echo '<td>' . htmlspecialchars($cell) . '</td>';
//     }
    
//     $process = array_combine($theadTitles, array_map('trim', array_slice($row, 0, $keyCount)));
//     return $process;
// }

function handleInvalidRow($startRow, $deptRow) {
    // 构建错误消息
    $errorMsg = generateErrorMessage($startRow, $deptRow);
    // 输出错误消息
    if($deptRow["state"] == "NA"){
        echo '<td colspan="6">此欄位' . $errorMsg . '未建立</td>';
    }else{
        echo '<td colspan="6">此欄位' . $errorMsg . '有誤</td>';
    }
    
    // 更新错误计数
    global $stopUpload;
    $stopUpload += 1;
}

function generateErrorMessage($startRow, $deptRow) {
    $errorMsg = '';
    $conCount = 0;

    if (strlen($startRow) !== 8 || !is_numeric($startRow)) {
        $errorMsg .= '<span style="color: red;">工號</span>';
        $conCount++;
    }

    if ($deptRow["state"] == "Lack") {
        if ($errorMsg !== '') {
            $errorMsg .= '與';
        }
        $errorMsg .= '<span style="color: red;">部門代碼'.$deptRow["strlen"].'個字</span>';
        $conCount++;
    }elseif($deptRow["state"] =="NA"){
        if ($errorMsg !== '') {
            $errorMsg .= '錯誤與';
        }
        $errorMsg .= '<span style="color: red;">部門代碼</span>';
        $conCount = 0;
    }

    if( $conCount > 1){
        $errorMsg .='皆';
    }

    return $errorMsg;
}

?>

