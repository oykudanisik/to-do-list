<?php

session_start();
require_once "./db.php";

$sql = "select * from user";
$rs = $db->prepare($sql);
$rs->execute();

$user = $rs->fetchAll(PDO::FETCH_ASSOC);
//var_dump($user);

if (!empty($_POST)) {
  require_once "./upload.php";
  $upload = new Upload("profile", "images");
  extract($_POST);
  //$sql = "insert into user (name, email, password, profile)
  //     values (?,?,?,?) ";

  $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $password = filter_var($password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $email = filter_var($email, FILTER_SANITIZE_FULL_SPECIAL_CHARS);


  $sql = "insert into user (name, email, password, profile)
           values (?,?,?,?) ";

  $rs = $db->prepare($sql);
  //$rs -> execute(["oyku","oyku@gmail.com","1234","abc.img"]);

  if (preg_match_all('/\b(\w+)@(?:\w+\.){1,3}(?:com|tr)\b/i', $email) === 0) {
    $_SESSION["message"] = "Invalid Email!";
  } else if ($upload->file() == null) {
    $_SESSION["message"] = "Invalid Data!";
  } else if (strlen(trim($password)) === 0 || strlen(trim($name)) === 0) {
    $_SESSION["message"] = "Invalid Name or Password!";
} else {
    //DONT FORGET TO HASH THE PASSWORD
    $rs->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $upload->file()]);
    header("Location: login.php");
    exit;
  }
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
  <style>
    .container {
      margin-top: 50px;
    }

    .input-field {
      width: 50%;
      margin: 40px auto;
    }
  </style>
</head>

<body>
  <nav>
    <div class="nav-wrapper center blue lighten-2">
      <a href="#" class="brand-logo center">TaskMan</a>
    </div>
  </nav>
  <div class="container">
    <form action="" method="post" enctype="multipart/form-data">
      <div class="input-field">
        <input name="name" id="name" type="text" class="validate" value ="<?php if(isset($name)) echo$name?>">
        <label for="name">Name Lastname</label>
      </div>

      <div class="input-field">
        <input name="email" id="email" type="text" class="validate" value ="<?php if(isset($email)) echo$email?>">
        <label for="email">Email</label>
      </div>

      <div class="input-field">
        <input name="password" id="password" type="password" class="validate" value ="<?php if(isset($password)) echo$password?>">
        <label for="password">Password</label>
      </div>

      <div class="file-field input-field">
        <div class="btn">
          <span>File</span>
          <input type="file" name="profile">
        </div>
        <div class="file-path-wrapper">
          <input class="file-path validate" type="text" value ="<?php if(isset($profile)) echo$profile?>">
        </div>
      </div>
      <div class="center">
        <button class="btn waves-effect waves-light" type="submit" name="action">Register
          <i class="material-icons right">send</i>
        </button>
      </div>
    </form>
  </div>

  <?php
  if (!empty($_SESSION["message"])) {
    $err = $_SESSION["message"];
    echo "<script> M.toast({html: '$err', classes: 'gray white-text'}) ; </script>";
    unset($_SESSION["message"]); // unset : delete from assoc. array.
  }
  ?>
</body>

</html>