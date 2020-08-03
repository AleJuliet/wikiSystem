<?php
require_once 'include/database.php';
require_once 'include/consults.php';
// firstuser("alejuliet","Alechandrina Pereira","tR4pHukU");
session_start(); // Starting Session
$error=''; // Variable To Store Error Message
if (isset($_POST['submit']) && $_POST['submit']!=null) {
  if (empty($_POST['username']) || empty($_POST['ps'])) {
    $error = "Username or Password is invalid";
    return;
  }
  else
  {
    // Define $username and $password
    $username=$_POST['username'];
    $password=$_POST['ps'];
    
    //Check user in database
    $user = selectUser($username);
    
    if (!$user)
    {
      $error = "Username or Password is invalid";
      return;
    }    
    
    $sameps = checkPasswordCorrectness($password,$user["dbKeySalt"],$user["passwordSalt"],$user["cryptDBKey"],$user["cryptHashedSaltUsrPwd"]);
    
    if ($sameps) {
      session_regenerate_id();
//       $fingerprint = base64_encode(openssl_random_pseudo_bytes(16));
      $_SESSION['login_user']=$username; // Initializing Session
//       $_SESSION['fingerprint']=$fingerprint;
      $_SESSION['canary']['IP'] = $_SERVER['REMOTE_ADDR'];
      $_SESSION['canary']['time'] = time();
      $_SESSION['last_regeneration'] = 0;
      header("location: procs.php"); // Redirecting To Other Page      
    } else {
      $error = "Username or Password is invalid";
    }
  }
}
?>