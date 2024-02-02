<?php

    // require 不容許回傳值
    // echo print_r($_POST["htmlTable"]);
    // 以下為"PhpSpreadsheet"啟動碼
    require '../../libs/vendor/autoload.php';  // 导入 PhpSpreadsheet 库

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
    // $sheet = $spreadsheet->getActiveSheet();
    $sheet = $spreadsheet->getActiveSheet()->freezePane('A2');      // 冻结窗格，锁定行和列
    
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
                // $sheet->setCellValueByColumnAndRow($col, $row, $value); // cell直接帶入值
                    // 自動換行
                    $cell = $sheet->getCellByColumnAndRow($col, $row);
                    $cell->setValue($value);                                   
                    $style = $cell->getStyle();
                    $style->getAlignment()->setWrapText(true);

                $col++;
            }
            $row++;
        }

    // 設定檔案名稱
        switch($to_module){
            case "stock":
                $filename_head = "PPE存量總表_".$data[0]["儲存點"];
                    $columns = ['B', 'C', 'E', 'F', 'G', 'K', 'L', 'M'];
                    foreach ($columns as $column) {
                        $spreadsheet->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
                    }
                break;
            case "supp":
                $filename_head = "PPE供應商_總表下載";
                    $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'L'];
                    foreach ($columns as $column) {
                        $spreadsheet->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
                    }
                break;
            case "contact":
                $filename_head = "PPE聯絡人_總表下載";
                    $columns = ['B', 'C', 'D', 'F', 'H'];
                    foreach ($columns as $column) {
                        $spreadsheet->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
                    }
                break;                    
            case "pno":
                $filename_head = "PPE_Part_NO料號_總表下載";
                    $columns = ['B', 'C', 'D', 'G', 'H', 'J'];
                    foreach ($columns as $column) {
                        $spreadsheet->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
                    }
                break;
            case "issueAmount":
                $filename_head = "PPE_請購需求單待轉PR_總表下載";
                    $columns = ['C', 'H'];
                    foreach ($columns as $column) {
                        $spreadsheet->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
                    }
                break;
            case "issueAmount_PR":
                $filename_head = "PPE_請購需求單已開PR：{$_REQUEST["pr_no"]}_總表下載";
                break;
            case "cata":
                $filename_head = "PPE_器材目錄管理_總表下載";
                    $columns = ['B', 'C', 'D', 'E', 'F', 'G', 'J', 'P', 'Q'];
                    foreach ($columns as $column) {
                        $spreadsheet->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
                    }
                break;
            case "sum_report":
                $filename_head = "PPE_進出量與成本匯總：{$_REQUEST["report_yy"]}{$_REQUEST["report_mm"]}_{$_REQUEST["tab_name"]}_{$_REQUEST["form_type"]}_下載";
                $spreadsheet->getActiveSheet()->setTitle($_REQUEST["tab_name"]);                    // 定義sheetName
                $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);         // A欄-自動欄寬
                break;
            case "sum_ptreport":
                $filename_head = "除汙器材管控清單：{$_REQUEST["form_type"]}_{$_REQUEST["tab_name"]}_下載";
                $spreadsheet->getActiveSheet()->setTitle($_REQUEST["tab_name"]);                    // 定義sheetName
                $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);         // A欄-自動欄寬
                break;
            default:
                $filename_head = $to_module;
                break;
        }

    // 調整欄列寬高換行
    $spreadsheet->getActiveSheet()->getStyle('1:1')->getAlignment()->setWrapText(true); // 1列-自動換行
    $spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(-1);               // 1列-自動欄高

    $filename = $filename_head."_".$now.'.xlsx';  // 設定檔名 

    // 以下為EXCEL規範制式的表頭格式
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename=".$filename);
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');

?>