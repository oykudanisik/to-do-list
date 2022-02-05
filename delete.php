<?php
    
    require "./db.php" ;
    header("Content-Type: application/json") ; // return data in json format

    //var_dump("heleloy");

    if ( $_SERVER["REQUEST_METHOD"] == "DELETE") {
        $requestData = file_get_contents("php://input") ; // data part of the http request packet.
        parse_str($requestData, $_DELETE) ;  // convert url encoded string to associative array
        $response = deleteTask($_DELETE["taskId"],$_DELETE['listId']);
    
    }
 
    echo json_encode($response);


    function deleteTask($taskId,$listId) {
        global $db ;
        try {
            $stmt = $db->prepare("delete from item where id = ?") ;
            $stmt->execute([$taskId]);

            $stmt2 = $db->prepare("select count(*) from item where listId = (?) and checked = 0");
            $stmt2 -> execute([$listId]);
            $checkList = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            return ["count"=> $checkList] ;
        } catch(PDOException $ex) {
            return ["valid" => false] ;
        }
    }
    