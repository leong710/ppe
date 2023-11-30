<?php

    // require 不容許回傳值
    // echo print_r($_POST["htmlTable"]);
    // 以下為"PhpSpreadsheet"啟動碼
    require '../vendor/autoload.php';  // 导入 PhpSpreadsheet 库

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    $data = json_decode($_POST["htmlTable"], true);
    if(!empty($_REQUEST["submit"])){
        $to_module = $_REQUEST["submit"];
    }else{
        $to_module = "downLoad_excel";
    }
    $now = date("Y-m-d");
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $keys = array_keys($data[0]);
    $column = 1;
        foreach ($keys as $key) {
            if ($key === "action") {
                continue; // 跳过特定的 $key
            }
            $sheet->setCellValueByColumnAndRow($column, 1, $key);
            $column++;
        }

    $row = 2;
        foreach ($data as $item) {
            $col = 1;
            foreach ($item as $key => $value) {
                if ($key === "action") {
                    continue; // 跳过特定的 $key
                }
                $sheet->setCellValueByColumnAndRow($col, $row, $value);
                $col++;
            }
            $row++;
        }


    // 設定檔案名稱
        switch($to_module){
            case "stock":
                $filename_head = $data[0]["儲存點"]."_PPE存量總表";
                break;
            case "supp":
                $filename_head = "PPE供應商_總表下載";
                break;
            case "contact":
                $filename_head = "PPE聯絡人_總表下載";
                break;
            default:
                $filename_head = $to_module;
                break;
        }

    $filename = $filename_head."_".$now.'.xlsx';  // 設定檔名 

    // 以下為EXCEL規範制式的表頭格式
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename=".$filename);
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');

?>