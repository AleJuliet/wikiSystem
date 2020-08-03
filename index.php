
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Sign in &middot; Twitter Bootstrap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      .form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
        background-color: #ffffff;
	border: 1px solid #cccccc;
	box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
	transition: border linear 0.2s, box-shadow linear 0.2s;
	line-height: 20px;
	color: #555555;
	vertical-align: middle;
	border-radius: 4px;
      }
      .input-block-level {
	width: 100%;
      }
      .checkbox {
	min-height: 20px;
	padding-left: 20px;
      }
    </style>
    
  </head>
  
  <?php 
  include('include/login.php'); // Includes Login Script

  if(isset($_SESSION['login_user'])){
    header("location: procs.php");
  }
  ?>

  <body>

    <div class="container">

      <form action="" method="post" id="log" class="form-signin">
        <div id="containerin">
	  <h2 class="form-signin-heading" style="font-weight: 600;">Sign in</h2>
	  <input type="text" name="username" class="input-block-level" placeholder="Email address">
	  <input type="password" name="ps" class="input-block-level" placeholder="Password">
	  <!--<label class="checkbox">
	    <input type="checkbox" value="remember-me"> Remember me
	  </label>-->
	  <input type="hidden" id="submit" name="submit" value="Login">
	  <label style="color:red;font-weight:400"><?php echo $error ?></label>
	  <button onclick="function () { document.getElementById("log").submit();}" class="btn btn-danger btn-large" type="submit" style="margin-top:10px">Sign in</button>
        </div>
      </form>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="http://code.jquery.com/jquery.js"></script>
    
    <script type="text/javascript">

    </script>

  </body>
</html>
