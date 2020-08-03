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
  
  //User
  if(isset($_POST['nameuserc']) && !empty($_POST['nameuserc'])) {
    $name = $_POST["nameuserc"];
    $username = $_POST["nameuser"];
    $ps = $_POST["psw"];
    $ps2 = $_POST["vl"];
    $user = selectUser($_SESSION['login_user']);
    $resIns = createuser($name,$username,$ps,$user,$ps2);
    if($resIns==1)
    {
      $host  = $_SERVER['HTTP_HOST'];
      $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
      $finalurl = "http://".$host.$uri."/users.php";
      print '<script language="Javascript">document.location.href="'.$finalurl.'" ;</script>';
      exit();
    }
    else
    {
      echo "<div class='container'><br>There was a problem with the user creation: <br>";
      if($resIns==2)
        echo "<br>Incorrect password. <br>";
      $host  = $_SERVER['HTTP_HOST'];
      $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
      $finalurl = "http://".$host.$uri."/users.php";
      echo "<a href='".$finalurl."'>Go Back</a></div>";
    }
    return;
  }
  
  //Order by option
  $orderbytc = "";
  $orderid = "";
  $ordername = "Date";
  if(isset($_GET["ord"]))
    $orderid = $_GET["ord"];
  if ($orderid==1)
  {
   $orderbytc = " order by fullname";
   $ordername = "Name";
  }
  if ($orderid==2)
   $orderbytc = " order by creationdate";
?>
<body>
   
    <!-- Page Content -->
    <div class="container">
	
	
	<div style="margin-top:50px"></div>
	
	<div id="bodyelements" style="">
	  
	  <div id="procedurepanel" style="vertical-align: top;margin-top:25px;margin-left:5px">
	      <div class="panel panel-default" style="min-height:600px;">
		  <div class="panel-heading" style="padding: 0px 5px;text-align:right">
		    <button type="button" class="btn btn-default navbar-btn" onclick="adduser()"><img src="img/add.png" width="15px" style="margin-bottom:2px">
			<label style="font-weight:400;margin-bottom: 1px; " class="newproc" >Add User</label></button>
		    <div class="btn-group">
		      <button type="button" class="btn btn-danger">Order by<?php echo " (".$ordername.")"?></button>
		      <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		      </button>
		      <ul class="dropdown-menu">
			<li><a style="cursor:pointer" onclick="orderby(1)">Name</a></li>
			<li><a style="cursor:pointer" onclick="orderby(2)">Date</a></li>
		      </ul>
		    </div>
		  </div>
		  <table class="table ">
		    <tbody>
		      <tr>
			<td style="padding-top:5px; "></td>
			<td class="processname" style="padding-top:5px;width:300px;"></td>
			<td style="padding-top:5px;font-size:14px;"></td>
		      </tr>
		      <?php 
			$db = opendb();
			$ret = $db->query('SELECT fullname,username,creationdate,type FROM USERS '.$orderbytc);
			
			$i = 0;
			while ($nmu = $ret->fetchArray(SQLITE3_ASSOC)) {
			  $date = new DateTime($nmu["creationdate"]);
                      ?>
		      <tr class="hoverrow" data-id="<?php echo $i?>">
			<td style="    width: 20px;padding-bottom:2px;padding-top: 3px"><img src="img/bullet2.gif" height="6px"></td>
			<td class="processname" style="width:150px; padding-top: 3px;"><?php echo $nmu["fullname"] ?> <span style="font-size:14px;color:#2b73b7;padding:0;">
			  <?php if($nmu["type"]) echo '<span style="font-size:14px;color:#2b73b7;padding:0;">(Admin)</span>'?></td>
			<td class="processun" style="width:150px; padding-top: 3px;"><?php echo $nmu["username"] ?> <span style="font-size:14px;color:#2b73b7;padding:0;"></td>
			<td style="font-size:14px;    width: 200px; padding-top: 3px;"><?php echo $date->format('d-m-Y') ?></td>
			<td style="font-size:14px;;">
			<?php if($nmu["username"]!=$_SESSION['login_user'] && !$nmu["type"])
			{ ?>
			<img onclick="deleteuser('<?php echo $nmu["username"]?>',<?php echo $i?>)" id="deleteuser<?php echo $i?>" style="visibility: hidden" src="img/closeimg.png"></td>		
		        <?php } ?>
		      </tr>
		      <?php 
		        $i++;
		        } 
		        $db->close();
		      ?>
		    </tbody>
		  </table>
	      </div>
	  </div>

	  
	  
        </div><!-- body elements -->
    
    </div>
    
    
    <!--modal open new user-->
     <div class="modal fade bs-example-modal" id="modalnewus" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
      <div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick='return cancelInsert();'><span aria-hidden="true">&times;</span></button>
	      <h4 class="modal-title" id="modallabel2">New User</h4>
	    </div>
	<div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                    <form role="form" method="post" name="newuser" id="newsuer">
                                <div class="col-lg-12">
                                       <input type="hidden" name="addTestSuite" value="1">
                                        <div class="form-group">
                                            <label>Full name</label>
                                            <input style="display:none" type="text" name="fakeusernameremembered"/>
                                            <input class="form-control" name="nameuserc" autocomplete="off">
                                        </div>
                                </div>
                                <div class="col-lg-12">
                                       <input type="hidden" name="addTestSuite" value="1">
                                        <div class="form-group">
                                            <label>Username</label>
                                            <input style="display:none" type="text" name="fakeusernameremembered"/>
                                            <input class="form-control" name="nameuser" autocomplete="off">
                                        </div>
                                </div>
                                <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Insert Password</label>
                                            <input style="display:none" type="password" name="fakepasswordremembered"/>
                                            <input class="form-control" type="password" name="psw" autocomplete="off">
                                        </div>
                                        <div class="form-group">
                                            <label>Confirm Password</label>
                                            <input style="display:none" type="password2" name="fakepasswordremembered2"/>
                                            <input class="form-control" type="password" name="psw2" autocomplete="off">
                                        </div>
                                        <input type="hidden" id="vl" name="vl" value="">
                                        
                                        <button type="button" class="btn btn-default" onclick="javascript: insert_user();">Add User</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick='return cancelInsert();'>Close</button>
                                    </form>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                               
                                <!-- /.col-lg-6 (nested) -->
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
	</div>
      </div>
    </div>
    
    <!--modal open new user-->
     <div class="modal fade bs-example-modal" id="modalnewus2" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
      <div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick='return cancelInsert();'><span aria-hidden="true">&times;</span></button>
	      <h4 class="modal-title" id="modallabel2">New User</h4>
	    </div>
	<div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                    <form role="form" method="post" name="newuser2" id="newsuer2">
                                <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Please write your password</label>
                                            <input style="display:none" type="password" name="fakepasswordremembered"/>
                                            <input class="form-control" type="password" name="psw" autocomplete="off">
                                        </div>
                                        
                                        <button type="button" class="btn btn-default" onclick="javascript: insert_user2();">Add User</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick='return cancelInsert2();'>Close</button>
                                    </form>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                               
                                <!-- /.col-lg-6 (nested) -->
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
	</div>
      </div>
    </div>

    
    <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    
    <script type="text/javascript">
    $(document).ready(function(){$('.nav a:contains("All Procedures")').parent().addClass("active");$(".nav-tabs > li > a").click(function(a){$(this).parent().addClass("active").siblings().removeClass("active");"Categories"==$(this).text()?($("#procedurepanel").css("display","none"),$("#categorypanel").css("display","block")):($("#categorypanel").css("display","none"),$("#procedurepanel").css("display","block"))});$('#categorypanel .panel-collapse:not(".in")').collapse("show");$(".hoverrow").hover(function(){var a=
$(this).attr("data-id");$("#deleteuser"+a).css("visibility","visible")},function(){var a=$(this).attr("data-id");"img/loading.gif"!=$("#deleteuser"+a).attr("src")&&$("#deleteuser"+a).css("visibility","hidden")})});function adduser(){$("#modalnewus").modal("show")}function cancelInsert(){$("#modalnewus").modal("hide")}function cancelInsert2(){$("#modalnewus2").modal("hide")}
function insert_user(){var a=document.forms.newsuer.nameuserc,b=document.forms.newsuer.nameuser,c=document.forms.newsuer.psw;c.value!=document.forms.newsuer.psw2.value?alert("Passwords are different"):""==a.value||""==b.value||""==c.value?alert("All fields must be filled"):($("#modalnewus").modal("hide"),$("#modalnewus2").modal("show"))}
function insert_user2(){var a=document.forms.newsuer.vl;a.value=document.forms.newsuer2.psw.value;""==a.value?alert("All fields must be filled"):($("#modalnewus2").modal("hide"),document.getElementById("newsuer").submit())}
function deleteuser(a,b){1==confirm("Are you sure you want to delete user: "+a+"?")?($("#deleteuser"+b).attr("src","img/loading.gif"),$("#deleteuser"+b).attr("width","20"),$.ajax({url:"include/ops.php",data:{consultid:"deleteuser",username:a},type:"post",success:function(a){setTimeout(function(){location.reload()},1E3)}})):console.log("no")}function orderby(a){a=window.location.href.split("?")[0]+"?ord="+a;document.location.href=a};
    </script>

</body>

</html>
