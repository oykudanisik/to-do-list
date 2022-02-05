<?php
    
    session_start();

    require_once "db.php";
    $user = $_SESSION["user"];


    if (!empty($_POST)) {
        extract($_POST);
        var_dump($listname);
        $listname = filter_var($listname, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        var_dump($_SESSION['user']['id']);
    
        if (strlen(trim($listname)) === 0) {
            $_SESSION["message"] = "Empty Input!";
    
        } else {
            try {
                $sql = "insert into list (name,userId) values (?,?)";
                $rs = $db->prepare($sql);
                $rs->execute([$listname,intval($_SESSION["user"]['id'])]);
            } catch (PDOException $ex) {
                echo  $ex;
            }
        }
    }
    header("Location: main.php");