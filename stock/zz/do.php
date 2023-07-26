<?php
    // require 'vendor/autoload.php';

    // use PhpOffice\PhpSpreadsheet\Spreadsheet;
    // use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
                
    // $spreadsheet = new Spreadsheet();
    // $sheet = $spreadsheet->getActiveSheet();
    // $sheet->setCellValue('A1', 'Hello World !');
    
    // $writer = new Xlsx($spreadsheet);
    // $writer->save('hello world.xlsx');
    
    // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    // header('Content-Disposition: attachment;filename="hello world.xlsx"');
    // header('Cache-Control: max-age=0');
    
    // $writer = new Xlsx($spreadsheet);
    // $writer->save('php://output');

    // include_once ("../pdo.php"); //連線資料庫 
    // require_once("function.php");




    // require '../../../vendor/autoload.php';

    // // require 'vendor/autoload.php';

    // use PhpOffice\PhpSpreadsheet\Spreadsheet;
    // use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    
    // $spreadsheet = new Spreadsheet();
    // $sheet = $spreadsheet->getActiveSheet();
    // $sheet->setCellValue('A1', 'Hello World !');
    
    // $writer = new Xlsx($spreadsheet);
    // $writer->save('hello world.xlsx');
    
    // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    // header('Content-Disposition: attachment;filename="hello world.xlsx"');
    // header('Cache-Control: max-age=0');
    
    // $writer = new Xlsx($spreadsheet);
    // $writer->save('php://output');

    $str_arr = array(
        'stock_No',
        'fab廠處',
        'site_儲存點位置',
        '衛材分類',
        '衛材名稱',
        '安全存量',
        '現場存量',
        '備註說明',
        '批號/效期',
        'PO_num',
        '其他說明',
        '最後更新',
        '最後編輯'
    );

    // print_r($str_arr);

    foreach(range('0', '10') as $num) {
        foreach(range('A', 'M') as $letter) {
            // echo $letter.$num."  ";

            // array_unshift( $str_arr, $letter.$num);
            array_push( $str_arr, $letter.$num);
        }
        // echo "</br>";
    }


    print_r($str_arr);
    // 
    // 
    // 


// namespace app\api\controller;

// use app\common\controller\Api;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;



// class Test extends Api
// {
//     protected $noNeedLogin = ['*'];
//     protected $noNeedRight = ['*'];
//     public function _initialize()
//     {
//         parent::_initialize();

//     }



//     public function export($data=null)
//     {

//             set_time_limit(0);
//             $search = $this->request->post('search');
//             $ids = $this->request->post('ids');
//             $filter = $this->request->post('filter');
//             $op = $this->request->post('op');
//             $columns = $this->request->post('columns');

//             //$excel = new PHPExcel();
//             $spreadsheet = new Spreadsheet();

//             $spreadsheet->getProperties()
//                 ->setCreator("FastAdmin")
//                 ->setLastModifiedBy("FastAdmin")
//                 ->setTitle("标题")
//                 ->setSubject("Subject");
//             $spreadsheet->getDefaultStyle()->getFont()->setName('Microsoft Yahei');
//             $spreadsheet->getDefaultStyle()->getFont()->setSize(12);
//             $worksheet = $spreadsheet->setActiveSheetIndex(0);
//             $list=$data;
//             $total=count($list);
//             $first = array_keys($list[0]);
//             foreach ($first as $index => $item) {
//                 $worksheet->setCellValueByColumnAndRow($index, 1, __($item));
//             }

// //            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(__DIR__ . '/muban/test.xls');  //读取模板
//             $worksheet = $spreadsheet->getActiveSheet();     //指向激活的工作表
//             $worksheet->setTitle('模板测试标题');
//             $tree=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'];
//             $length=count($data[0]);
//             $keys=array_keys($data[0]);
//             $tree=array_slice($tree,0,$length);
//             //行头
//             for($i=0;$i<$length;$i++){
//                 $worksheet->setCellValue($tree[$i].'1', $keys[$i]);
//             }
//             //内容
//             for($i=0;$i<$total;$i++){
//                 $a=$i+2;
//                 //$i代表当前第几条
//                 for ($s=0;$s<$length;$s++){
//                     //$s代表每个字段插入一次
//                     //需要插入的字段
//                     $k=$keys[$s];
//                     //替换表情
//                     $str = preg_replace_callback(
//                         '/./u',
//                         function (array $match) {
//                             return strlen($match[0]) >= 4 ? '' : $match[0];
//                         },
//                         $data[$i][$k]);
//                     $worksheet->setCellValue($tree[$s].$a, $str);   //送入A1的内容
//                 }

//             }

//         $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
//         //下载文档
//         header('Content-Type: application/vnd.ms-excel');
//         header('Content-Disposition: attachment;filename="'. date('Y-m-d') . '_test'.'.xlsx"');
//         header('Cache-Control: max-age=0');
// //                $writer = new Xlsx($spreadsheet);
//         $writer->save('php://output');


//             return;
//     }

// 可用如下：20230206

include_once ("../pdo.php"); //連線資料庫 
require_once("function.php");

require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$pdo = pdo();
extract($_REQUEST);
// 前段-初始查詢語法：全廠+全狀態
$sql = "SELECT stock.*, users.cname
            , _fab.fab_title, _fab.remark as fab_remark
            , _site.id as site_id, _site.site_title, _site.remark as site_remark
            , _local.local_title, _local.remark as loccal_remark
            , categories.cate_title
            , _catalog.title as catalog_title
        FROM `stock`
        LEFT JOIN _catalog ON stock.catalog_id = _catalog.id
        LEFT JOIN categories ON _catalog.category_id = categories.id
        LEFT JOIN users ON stock.user_id = users.id
        LEFT JOIN _local ON stock.local_id = _local.id
        LEFT JOIN _site ON _local.site_id = _site.id
        LEFT JOIN _fab ON _site.fab_id = _fab.id 
        ORDER BY fab_id, site_id, local_id, category_id ,catalog_id ASC ";
$stmt = $pdo->prepare($sql);
try {

    $stmt->execute();     //匯出處理 bySite
    $stocks = $stmt->fetchAll();
    
}catch(PDOException $e){
    echo $e->getMessage();
}

if(!empty($stocks)){

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    //設定工作表標題名稱 
    $sheet->setTitle('儲存表');
    // 設定表頭
    $sheet->setCellValue('A1','stock_No');
    $sheet->setCellValue('B1','fab廠處');
    $sheet->setCellValue('C1','site_儲存點位置');
    $sheet->setCellValue('D1','衛材分類');
    $sheet->setCellValue('E1','衛材名稱');
    $sheet->setCellValue('F1','安全存量');
    $sheet->setCellValue('G1','現場存量');
    $sheet->setCellValue('H1','備註說明');
    $sheet->setCellValue('I1','批號/效期');
    $sheet->setCellValue('J1','PO_num');
    $sheet->setCellValue('K1','其他說明');
    $sheet->setCellValue('L1','最後更新');
    $sheet->setCellValue('M1','最後編輯');

    // 資料行開始鋪設
    $col_num = 2;   // 跳過表頭從第2行開始
    foreach($stocks as $row){ 
        // 因為remark=textarea會包含html符號，必須用strip_tags移除html標籤
        $s_remark = strip_tags($row['remark']);
        $s_d_remark = strip_tags($row['d_remark']);

        $sheet->setCellValue('A'.$col_num ,$row['id']);
        $sheet->setCellValue('B'.$col_num ,$row['fab_title']);
        $sheet->setCellValue('C'.$col_num ,$row['site_title']);
        $sheet->setCellValue('D'.$col_num ,$row['cate_title']);
        $sheet->setCellValue('E'.$col_num ,$row['catalog_title']);
        $sheet->setCellValue('F'.$col_num ,$row['standard_lv']);
        $sheet->setCellValue('G'.$col_num ,$row['amount']);
        $sheet->setCellValue('H'.$col_num ,$s_remark);
        $sheet->setCellValue('I'.$col_num ,$row['lot_num']);
        $sheet->setCellValue('J'.$col_num ,$row['po_num']);
        $sheet->setCellValue('K'.$col_num ,$s_d_remark);
        $sheet->setCellValue('L'.$col_num ,$row['updated_at']);
        $sheet->setCellValue('M'.$col_num ,$row['cname']);

        $col_num++;     // 下一行
    } 

    // 輸出檔案命名選擇器
    $filename = 'tn衛材存量總表(all)_'.date('Ymd').'.xlsx'; //設定檔名 

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename='.$filename );
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');

}