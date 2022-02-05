<?php

session_start();
require_once "./db.php";

if (!empty($_POST)) {
  extract($_POST);
  //var_dump($password);
  if (preg_match_all('/\b(\w+)@(?:\w+\.){1,3}(?:com|tr)\b/i', $email) === 0) {
    $_SESSION["message"] = "Invalid Email!";
  } 
  $email = filter_var($email, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $password = filter_var($password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
 
  $rs = $db->prepare("select * from user where email = ?");
  $rs->execute([$email]);

  if ($rs->rowCount() === 1) {
    $user = $rs->fetch(PDO::FETCH_ASSOC);
    //var_dump($user["password"]);

    if (password_verify($password, $user["password"])) {

      $_SESSION["user"] = $user;
      header("Location: main.php");
      exit;
    } else {
      $_SESSION["message"] = "Invalid Password!";
    }
  } else {
    $_SESSION["message"] = "Login Failed!";
    //var_dump($_SESSION["message"]);
    //echo "no user with that email address" ;
    //header("Location: login.php");
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
    .input-field {
      width: 50%;
      margin: 30px auto;
    }

    .container {
      margin-top: 150px;
    }
  </style>
</head>

<body>
  <nav>
    <div class="nav-wrapper blue lighten-2">
      <a href="#" class="brand-logo center">TaskMan</a>
      <ul id="nav-mobile" class="right">
        <li><a href=""></a></li>
        <li><a href="register.php"><i class="material-icons left">person_add</i> Register</a></li>
      </ul>
    </div>
  </nav>
  <div class="container">
    <form action="login.php" method="post">
      <div class="input-field">
        <i class="material-icons prefix">account_circle</i>
        <input name="email" id="email" type="text" class="validate" value ="<?php if(isset($email)) echo$email?>">
        <label for="email">Email</label>
      </div>

      <div class="input-field">
        <i class="material-icons prefix">lock</i>
        <input name="password" id="password" type="password" class="validate">
        <label for="password">Password</label>
      </div>


      <div class="center">
        <button class="btn waves-effect waves-light teal lighten-2 btn-large" type="submit" name="action" style="border-radius: 4px; font-size: 24px;">Login
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
  <script>
    $(function() {

    })
  </script>
</body>

</html>