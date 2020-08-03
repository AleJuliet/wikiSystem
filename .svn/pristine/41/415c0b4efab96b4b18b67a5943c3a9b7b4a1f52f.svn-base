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
    .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover
    {
      border: 0 solid #ddd;
    }
    .nav-tabs>.active>a
    {
      border: 0 solid #ddd;
    }
    .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
      border-bottom-color: #c22d1a;
      border-bottom-width:1px;
      color:black;
    }
    .nav-tabs>li>a {
      border-bottom: 0 solid #ddd; 
      color:#c22d1a;
    }
    .nav-tabs
    {
     border-bottom-width:0px; 
/*     border-bottom-color:#fcdad5; */
    }
    #tablelist {
	display:inline-block;
	width:250px;
	white-space: nowrap;
	overflow:hidden !important;
	text-overflow: ellipsis;
    }
    </style>

</head>
<body>

    <?php 
    // include("include/tables.php");
    // createtables();
    $page = "proc";
    include('header.php');

    $pid = "";
    if(isset($_GET["pi"]))
      $pid = $_GET["pi"];
      
    $db = opendb();
    $query = $db->prepare('SELECT * FROM PROCEDURES as p where p.procid=:id');
    $query->bindValue(':id', $pid, SQLITE3_TEXT);
    
    $resultx = $query->execute();
    $acc_ll = 0;
    ?>
    
    <!-- Page Content -->
    <div class="container">
        <!-- Search Content -->
	<?php include('searchbox.php'); ?>
	
	
	<div style="margin-top:50px"></div>
	
	<div id="bodyelements" style="">
	  <ul class="nav nav-tabs ">
	      <li role="presentation"><a class="pointer" style="font-size:12px;padding: 5px 8px;">All Procedures</a></li>
	      <li role="presentation"><a class="pointer" style="font-size:12px;padding: 5px 8px;">Categories</a></li>
	  </ul>
	  
	  <div id="procedurepanel" style="vertical-align: top;margin-top:25px;margin-left:5px;margin-bottom: 0px;">
	      <div class="panel panel-default" style="min-height:600px;box-shadow: 4px 4px 5px #b7b7b7;">
	          <?php
	          while ($resx = $resultx->fetchArray(SQLITE3_ASSOC)) {
	          $userinfo = selectUser($_SESSION['login_user']);
	          $des = decryptdescription($resx["procdescription"],$_SESSION['acc_ll'],$userinfo);
	          ?>
	          <div id="newactions2" style="  position: relative;margin: 10px 30px;">
		    <div style="position:absolute;left: 0;">
		    </div>
		    <div style="position:absolute;right: 0;">
		      <button class="editbutton large transparent-black button style-1" onclick="editproc(<?php echo $resx["procid"]?>)" >Edit</button>
		    </div>
		  </div>
<!--		  <div style="float:right;margin:40px 30px 0 0">
		  </div>-->
		  <div class="proceduretable panel-group" style="float:right;margin:40px 30px 0 0;padding-left: 20px;">
		    <div class="panel panel-default" style="width:250px">
		      <div class="panel-heading">
			<h4 class="panel-title">
			  <a data-toggle="collapse" href="#collapse1">Table of Contents</a>
			</h4>
		      </div>
		      <div id="collapse1" class="panel-collapse collapse">
			<ul id="tablecontetlist" class="list-group">
			</ul>
		      </div>
		    </div>
		  </div>
		  
		  <div id="titleproc" style="position: relative;margin: 30px 30px 0px 30px;    width: 900px;">
<!--		    <div style="position:absolute;left: 0;">-->
		      <?php echo "<h2>".$resx["procname"]."</h2>"; ?>
<!--<!--		    </div>-->
		    
		  </div>
		 
	          <div id="bodytext" style="margin: 0px 30px 20px 30px">
		  <?php 
		    //Markup language
		    require_once("include/Wiky.php-master/wiky.inc.php");
		    
		    $wiky=new wiky;
		    if ($resx["parsed"]=="1")
		    {
		      $input=htmlspecialchars(stripslashes($des));
		      echo $wiky->parse($input);
		    }
		    else 
		      echo $des;
		  ?>
		  </div>
		  </div>
	      </div>
	      <div style="margin-left:30px;margin-bottom:50px;font-style: italic;font-size: 13px;text-align:right">
	      <span>Created by <label><?php echo $resx["usercreator"]?></label> on <label><?php echo $resx["creationdatep"]?></label></span><br>
	      <?php 
		if($resx["usermod"]!="")
		{
	      ?>
		  <span>Edited by <label><?php echo $resx["usermod"]?></label> on <label><?php echo $resx["moddatep"]?></label></span>
	      <?php 
		}
	      }
		  $db->close();
	      ?>
	      </div>
	  </div>

	  
	  
        </div><!-- body elements -->
    
    </div>
    
    <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/aux.js"></script>
    
    <script type="text/javascript">
    $( document ).ready(function() {
      $('#procedurepanel .panel-collapse:not(".in")').collapse('show');
      $('.nav-tabs > li > a').click(function(event){
	if($(this).text()=="Categories")
	{
	  var urlf = "procs.php?s=c#c";
	  setTimeout(function(){document.location.href=urlf},100); 
	}
	else
	{
	  var urlf = "procs.php";
	  setTimeout(function(){document.location.href=urlf},100);
	}
      });
      var i = 1;
      $("#tablecontetlist").append('<li style="margin-left:0px" '+
	   'class="list-group-item"><img src="img/bullet2.png" height="6px"><a class="pointer" onclick="goto(\'0\')">'+$("#titleproc").text()+'</a></li>');
      $('#bodytext').find(':header').each(function(){ 
	var mar = 0;
	if($(this).prop("tagName")=="H2")
	  mar = 10;
	else if ($(this).prop("tagName")=="H3")
	  mar = 20;
	$(this).attr('id', 'header'+i);
	$("#tablecontetlist").append('<li id="tablelist" style="margin-left:'+mar+'px" '+
	   'class="list-group-item"><img src="img/bullet2.png" height="6px"><a class="pointer" onclick="goto(\''+i+'\')"> '+($(this).text()).trim()+'</a></li>');
	i++;
	/* do stuff */ 
      });
    });  
    function editproc(id)
    {
      var urlf = "eproc.php?pi="+id+"&s=2";
      setTimeout(function(){document.location.href=urlf},100); 
    }
    function goto(i)
    {
      document.location.href="#header" + i;
    }
    </script>

</body>

</html>
