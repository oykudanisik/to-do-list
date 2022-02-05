<?php

   setcookie("PHPSESSID", "", 1, "/") ; // logically invalidate the session id.
   session_destroy(); // deletes the session file.

   header("Location: login.php") ;