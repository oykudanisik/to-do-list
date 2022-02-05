
<?php
    require "./db.php" ;
    header("Content-Type: application/json") ; // return data in json format

    if ( $_SERVER["REQUEST_METHOD"] == "POST") {
        $response = checkAsDone($_POST["taskId"],$_POST['listId']);
    }

    echo json_encode($response);

    function checkAsDone($taskId,$listId) {
        global $db ;
        try {
            $stmt = $db->prepare("update item set checked = NOT checked where id = (?)") ;
            $stmt->execute([$taskId]) ;

            $stmt2 = $db->prepare("select count(*) from item where listId = (?) and checked = 0");
            $stmt2 -> execute([$listId]);
            $checkList = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            return ["count"=> $checkList] ;
        } catch(PDOException $ex) {
            return ["valid" => false] ;
        }
    }
