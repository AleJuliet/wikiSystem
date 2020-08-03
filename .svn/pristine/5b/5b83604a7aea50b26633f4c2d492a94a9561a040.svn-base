<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Wiki</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style type="text/css">
    
    </style>

</head>
<?php 
  include("include/consults.php");
  $page = "";
  include('header.php');
  
  $acc_ll = 0;
  $u_l = $_SESSION['login_user'];
  if(isset($_SESSION['acc_ll']))
    $acc_ll = $_SESSION['acc_ll'];
  $userinfo = selectUser($_SESSION['login_user']);
    
  //User
  if(isset($_POST['keyword']) && !empty($_POST['keyword'])) 
  {    
    $keyword = $_POST["keyword"];
  }
  else 
    $keyword = "";   
    
  $db = opendb();
  $query = $db->prepare('SELECT * FROM PROCEDURES');
  
  $resultp = $query->execute();
  $procsarray = array();
  while ($procs = $resultp->fetchArray(SQLITE3_ASSOC)) {
    $search = $procs["procname"];
    $search2 = decryptdescription($procs["procdescription"],$acc_ll,$userinfo);
    if (stripos($search, $keyword) !== false || stripos($search2, $keyword) !== false) 
      $procsarray[$procs["procid"]] = $procs["procname"];
  }  
  
  $query = $db->prepare('SELECT * FROM CATEGORIES as c ');
  $query->bindValue(':key', "%".$keyword."%", SQLITE3_TEXT);
  
  $resultc = $query->execute();
  $catssarray = array();
  while ($cats = $resultc->fetchArray(SQLITE3_ASSOC)) {
    if (stripos($cats["catname"], $keyword) !== false)
      $catssarray["catid"] = $cats["catname"];
  }  
  
  $query = $db->prepare('SELECT * FROM SERVERS as s');
  $query->bindValue(':key', "%".$keyword."%", SQLITE3_TEXT);
  
  $results = $query->execute();
  $serversarray = array();
  while ($serv = $results->fetchArray(SQLITE3_ASSOC)) {
    $search = decryptdescription($serv["servername"],$acc_ll,$userinfo);
    $search2 = decryptdescription($serv["serverdes"],$acc_ll,$userinfo);
    if (stripos($search, $keyword) !== false || stripos($search2, $keyword) !== false)
      $serversarray[$serv["serverid"]] = $search;
  } 

  
  
    
?>
<body>
   
    <!-- Page Content -->
    <div class="container">
	
	
	<div style="margin-top:50px"></div>
	
	<div id="bodyelements" style="">
	  
	  <div id="procedurepanel" style="vertical-align: top;margin-top:25px;margin-left:5px">
	      <div class="panel panel-default" style="min-height:600px;">
		  <div class="panel-heading" style="height:50px">
		    <label>Search Result: </label><?php echo "    ".$keyword?>
		  </div>
		  <table class="table ">
		    <tbody>
		      <tr>
			<td style="padding-top:5px; "></td>
		      </tr>		      
		      <?php 
		      if($keyword!="")
		      {
			echo '<tr style="padding-top:5px; "><td style="padding-top:5px;font-weight:600 ">Procedures:</td></tr>';
			foreach ($procsarray as $key => $value) {
			?>
			  <tr style="padding-top:5px; "><td style="padding-top:5px; ">
			  <img src="img/bullet2.gif" height="6px" style="margin-right:5px">
			  <a href="proci.php?pi=<?php echo $key?>"><?php echo $value?></a></td></tr>
			<?php 
			}
			echo '<tr style="padding-top:5px; "><td style="padding-top:5px;font-weight:600 ">Categories:</td></tr>';
			foreach ($catssarray as $key => $value) {
			?>
			  <tr style="padding-top:5px; "><td style="padding-top:5px; ">
			  <img src="img/bullet2.gif" height="6px" style="margin-right:5px">
			  <a href="procs.php?s=c"><?php echo $value?></a></td></tr>
		      <?php 
			}
			echo '<tr style="padding-top:5px; "><td style="padding-top:5px;font-weight:600 ">Servers:</td></tr>';
			foreach ($serversarray as $key => $value) {
			?>
			  <tr style="padding-top:5px; "><td style="padding-top:5px; ">
			  <img src="img/bullet2.gif" height="6px" style="margin-right:5px">
			  <a href="vserver.php?sid=<?php echo $key?>"><?php echo $value?></a></td></tr>
		      <?php 
			}
		      }
		      ?>
		    </tbody>
		  </table>
	      </div>
	  </div>

	  
	  
        </div><!-- body elements -->
    
    </div>
    
    <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    
    <script type="text/javascript">
    </script>

</body>

</html>
