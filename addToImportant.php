<?php
    
    require "./db.php" ;
    header("Content-Type: application/json") ; // return data in json format


    if ( $_SERVER["REQUEST_METHOD"] == "POST") {
        $response = addToImp($_POST["taskId"]);
    }

    echo json_encode($response);

    function addToImp($taskId) {
        global $db ;
        try {
            $stmt = $db->prepare("update item set important = NOT important where id = (?)") ;
            $stmt->execute([$taskId]) ;
            return ["valid" => true] ;
        } catch(PDOException $ex) {
            return ["valid" => false] ;
        }
    }

