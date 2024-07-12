<?php
    if(isset($_REQUEST['fun'])) {
        require_once("function.php");
        $result = [];
        switch ($_REQUEST['fun']){

            case 'update_amount':
                // 宣告查詢陣列內容
                extract($_REQUEST);
                if(isset($id) && isset($amount)) {
                    require_once("../pdo.php");
                    $pdo = pdo();
                    $sql = "UPDATE _stock SET amount=?, updated_at=now() WHERE id=? ";
                    $stmt = $pdo->prepare($sql);
                    try {
                        $stmt->execute([$amount, $id]);
                        $result['result'] =  "mySQL寫入 - 成功";
                    }catch(PDOException $e){
                        echo $e->getMessage();
                        $result['error'] = 'Load '.$fun.' failed...(e)';
                        $result['result'] =  "mySQL寫入 - 失敗";
                    }
                } else {
                    $result['error'] = 'Load '.$fun.' failed...(no id/amount)';
                }
                break;
            default:
                $result['error'] = $_REQUEST['fun'].' - 錯誤fun參數!';
                
        };

        if(isset($result["error"])){
            http_response_code(500);
        }else{
            http_response_code(200);
        }
        echo json_encode($result);

    } else {
        http_response_code(500);
        echo json_encode(['error' => 'fun is lost.']);
    }
?>
