<?php

    session_start();
    require_once "db.php";
    extract($_POST);
    $task = filter_var($task, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    var_dump($_SESSION);


    if($_SESSION['list'] == "Important"){
        $_SESSION["message"] = "cannot add here";
    }
    else{
        if (strlen(trim($task)) === 0 && $_SESSION['list'] != "Important") {
            $_SESSION["message"] = "Empty Input!";
    
        } else {
            try {
                $sql = "insert into item (name,listId) values (?,?)";
                $rs = $db->prepare($sql);
                $rs->execute([$task,$_SESSION['listId']]);
            } catch (PDOException $ex) {
                $_SESSION["message"] = "Insert Fail";
            }
        }
    }

    header("Location: main.php");