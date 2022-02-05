<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user"])) {
    $_SESSION["message"] = "Unauthorized Access!";
    header("Location: login.php");
    exit;
}
$user = $_SESSION["user"];

//Default for listname
if (!empty($_GET['listname'])) {
    //var_dump($_GET['listId']);
    $_SESSION['list'] = $_GET['listname'] ?? "Important";
    $_SESSION['listId'] = $_GET['listId'] ?? 0;
    //var_dump($_SESSION);
}

//
//GET IMPORTANT LIST
//
$user = $_SESSION["user"];
try {
    $sql = "select * from item 
                      where important = 1 and checked = 0 and listId in (select id from list 
                                                           where userId = :id )";
    $rs = $db->prepare($sql);
    $rs->execute(["id" => $_SESSION['user']['id']]);
    $importantList = $rs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    $_SESSION["message"] = $ex;
}


//
//GET DATAS FROM ITEM TABLE
//
try {
    $sql = "select * from item where listId = :id";
    $rs = $db->prepare($sql);
    $rs->execute(["id" => $_SESSION['listId']]);
    //Convert PDOStatement/ResultSet to PHP array
    $items = $rs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    $_SESSION["message"] = "Cannot load List";
    var_dump($ex);
}

//
//GET DATAS FROM LIST TABLE
//
try {
    $rs = $db->query("select * from list");
    //Convert PDOStatement/ResultSet to PHP array
    $list = $rs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    $_SESSION["message"] = "Cannot load List";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Title of the document</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">

        <div class="col s2">
            <!-- Grey navigation panel -->
            <table>
                <tr>
                    <td><img src="./images/<?= $user["profile"] ?> " width="40px" height="40px" style="border-radius: 30px;"></td>
                    <td> <?= $user["name"]; ?> <br> <?= $user["email"];  ?> </td>
                    <td> <a href="logout.php"> <i class="material-icons teal-text">exit_to_app</i> </a></td>
                </tr>
                <tr style="border-bottom: 1px solid grey;">
                    <td><i class="material-icons">star_border</i></td>
                    <td><a href="?listname=Important" class="blue-text">Important</a></td>
                </tr>
                <?php

                foreach ($list as $l) :
                    if ($_SESSION["user"]["id"] === $l["userId"]) :
                        $qr = $db->query("select * from item where listId = " . "'" . $l['id'] . "' and checked = 0");
                ?>
                        <tr>
                            <td><i class='material-icons'>menu</i></td>
                            <?php $qs = http_build_query(["listname" => $l['name'], "listId" => $l['id']]); ?>
                            <td><a href="?<?= $qs ?>" class='blue-text'> <?= $l['name'] ?></a></td>
                            <td id="item-<?= $l["id"] ?>"><?php if ($qr->rowCount() != 0)
                                                                            echo "{$qr->rowCount()}"; ?> </td>
                        </tr>
                <?php
                    endif;
                endforeach;
                ?>
                    <!-- Adding a New List Modal -->
                <tr>
                    <td><i class="material-icons black-text">add</i></td>
                    <td>
                        <a class="modal-trigger" href="#modal1"><span class="blue-text">New List</span> </a>
                        <div id="modal1" class="modal">
                            <div class="modal-content">
                                <p>
                                <form action="addList.php" method="POST" enctype="multipart/form-data">
                                    <div class="input-field teal-text">
                                        <input name="listname" id="listname" type="text" class="validate">
                                        <label for="listname">List Name</label>
                                    </div>
                                </form>
                                </p>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div id="background" class="col s6 blue lighten-1 ">
            <!-- Teal page content  -->
            <h2 id="currentList"  style="color: white;" listId=<?= $_SESSION['listId']?> ><?= $_SESSION['list'] ?? "Important" ?></h2>
            <div style="flex-grow: 1; overflow: auto; ">
                <ul>
                    <?php
                    if ($_SESSION['listId'] != 0) :
                        //var_dump($_SESSION);$_SESSION['user']['id']==$item['id'] &&   
                        foreach ($items as $item) :
                    ?>
                            <li id=<?= $item['id'] ?> class="liTasks">
                                <p>
                                    <!--MARK AS CHECKED-->
                                    <span id="checkAsDone">
                                        <label>
                                            <input value="<?= $item["checked"] ?>" id="checkbox" name="checkbox-<?= $item["id"] ?>" type="checkbox" class="filled-in" 
                                            <?php if($item["checked"]) echo "checked = 'checked'"; ?>
                                            />
                                            <span class="itemNames" id="lined<?= $item['id'] ?>"><?= $item['name'] ?></span>
                                        </label>
                                        
                                    </span>

                                    <span id="rightAligners">
                                        <!--MARK AS IMPORTANT-->
                                        <?php if ($item["important"]) : ?>
                                            <a id="addToImportant" href="main.php"><i class="material-icons blue-text">star</i></a>
                                        <?php else : ?>
                                            <a id="addToImportant" href="main.php"><i class="material-icons blue-text">star_border</i></a>
                                        <?php endif; ?>
                                        <a id="deleteIcon" href="#"><i class="material-icons blue-text">delete_forever</i></a>
                                    </span>
                                </p>
                            </li>
                            <?php
                        endforeach;
                    else :
                        foreach ($importantList as $impList) :

                            $sql = "select name from list where id = :id";
                            $rs = $db->prepare($sql);
                            $rs->execute(["id" => $impList['listId']]);
                            $importantListNames = $rs->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($importantListNames as $impLNames) : ?>
                                <li style="list-style: none;" class="impliNames" id=<?= $impList['id'] ?>>
                                    <p>
                                        <i class="material-icons blue-text">star_border</i>
                                        <?= $impList['name'] ?>
                                    <div> (<?= $impLNames['name'] ?>) </div>
                                    </p>
                                </li>
                </ul>

    <?php
                            endforeach;
                        endforeach;
                    endif; ?>
            </div>
                    <!--Add a New Task -->
            <?php if ($_SESSION['listId'] != 0) : ?>
                <div>
                            <ul>
                                <li id="addATask" class="indigo lighten-1" style="border-radius: 5px; ">
                                    <span>
                                        <form action="add.php" method="POST">
                                            <div class="input-field teal-text">
                                                <i class="material-icons white-text left prefix">add</i>
                                                <input name="task" id="task" type="text" class="validate"
                                                        style="border: none;">
                                                <label for="task" class="grey-text"> Add a Task</label>
                                            </div>
                                        </form>
                                    </span>
                                </li>
                            </ul>
                        </div>

            
            <?php endif; ?>


        </div>
    </div>
    </div>
    <?php
    if (!empty($_SESSION["message"])) {
        $err = $_SESSION["message"];
        echo "<script> M.toast({html: '$err', classes: 'gray white-text'}) ; </script>";
        unset($_SESSION["message"]);
    }
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('.modal');
            var instances = M.Modal.init(elems);
        });

        //
        //DELETE TASK
        //
        $(document).on("click", "#deleteIcon", function(e) {
            var pid = this.parentNode.parentNode.parentNode.id;
            var currentList = document.getElementById("currentList");
            console.log(currentList);
            var listId = currentList.getAttribute("listId");
            var element = document.getElementById(pid);
            


            $.ajax({
                type: "delete",
                url: "delete.php",
                data: {
                    taskId: pid,
                    listId: listId
                },
                success: function(ex) {

                    element.remove();
                    var elemName = "item-" + listId;

                    var el = document.getElementById("item-" + listId);
                    //console.log(el);

                    console.log(ex);
                    $count = ex["count"][0]["count(*)"];
                    //alert($count);
                    if($count == 0){
                        el.innerText = ""
                    }
                    else{
                        el.innerText = $count;
                    }
                },
                error: function() {
                    alert("Sorry, couldnt delete the task");
                }
            })
        })

        //
        //CHECK ELEMENT AS DONE
        //

        $(document).on("change", "#checkAsDone", function(e) {
            //$('#checkAsDone').attr('disabled', 'disabled');
            var pid = this.parentNode.parentNode.id;
            var currentList = document.getElementById("currentList");
            console.log(currentList);
            var listId = currentList.getAttribute("listId");

            $.ajax({
                type: "post",
                url: "update.php",

                data: {
                    taskId: pid,
                    listId: listId
                },
                success: function(ex) {
                    var elemName = "item-" + listId;

                    var el = document.getElementById("item-" + listId);
                    console.log(el);
                
                    console.log(ex);
                    $count = ex["count"][0]["count(*)"];
                    //alert($count);
                    if($count == 0){
                        el.innerText = ""
                    }
                    else{
                        el.innerText = $count;

                    }
                    

                },
                error: function() {
                    alert("error");
                }
            })
        });

        //
        //ADDING ELEMENT TO IMPORTANT LIST
        //
        $(document).on("click", "#addToImportant", function(e) {
            var pid = this.parentNode.parentNode.parentNode.id;

            //alert(pid); 
            $.ajax({
                type: "post",
                url: "addToImportant.php",
                data: {
                    taskId: pid

                },
                success: function() {
                    //alert("successful");

                },
                error: function() {
                    alert("error");
                }
            })
        });
    </script>

</body>

</html>