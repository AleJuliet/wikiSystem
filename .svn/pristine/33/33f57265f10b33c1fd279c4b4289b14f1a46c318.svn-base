<?php
  require_once 'include/database.php';
  require_once 'include/consults.php';
  
  session_start();// Starting Session
  // Storing Session
  $user_check=$_SESSION['login_user'];
  
  //Check user in database
  $user = selectUser($user_check);
  if (!$user)
  {
    header('Location: index.php');
  } 
  
  //Every hour
  if (((isset($_SESSION['canary']['oper']) && $_SESSION['canary']['oper']=='n') && ($_SESSION['canary']['time'] < time() - 3600))
    || (!isset($_SESSION['canary']['oper']) && ($_SESSION['canary']['time'] < time() - 3600))) {
      session_destroy();
  }
  
  //Useragent
  if (isset($_SESSION['canary']['HTTP_USER_AGENT']))
  {
      if ($_SESSION['canary']['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
      {
          session_destroy();
	  header('Location: index.php');
      }
  }
  else
  {
      $_SESSION['canary']['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
  }
  
  //IP
  if ($_SESSION['canary']['IP'] !== $_SERVER['REMOTE_ADDR']) {
    // Delete everything:
    session_destroy();
    header('Location: index.php');
  }
  
  //Regenerate the SID on each 20 requests
  if (++$_SESSION['last_regeneration'] >= 20) {
      $_SESSION['last_regeneration'] = 0;
      session_regenerate_id(true);
  }

  $login_session =$user_check;
?>