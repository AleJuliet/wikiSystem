<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Wiki</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style type="text/css">
    body {
      padding-top: 70px; /* Required padding for .navbar-fixed-top. Remove if using .navbar-static-top. Change if height of navigation changes. */
    }
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
    .table>tbody>tr>td
    {
     border-top-width: 0;
    }
    .table>tbody>tr>td {
     padding: 0px 5px 3px 10px;
    }
    .processname:hover 
    {
     text-decoration: underline;
     cursor: pointer;
    }
    .panelss .listcat .panel-heading {
      background-color: transparent;
      padding: 3px 2px 8px;
      border-bottom: 1px solid #bababa;
    }
    .panelss .listcat .panel-default {
      border-color: transparent;
    }
    .panelss .listcat .panel-body {
      padding: 0; 
    }
    .hoverrow:focus, .hoverrow:hover {
      background-color: #d9edf7 !important;
      cursor: pointer;
    }
    .selectform
    {
    border-radius: 5px;
    padding: 2px;
    }
    .pointer 
    {
    cursor: pointer;
    }
    .left {    
    position: absolute;
    left: 0;
    }
    .right {    
    position: absolute;
    right: 0;
    }
    .popover{position:fixed;
    left:12% !important;}
    .selectform {
      border-radius: 5px;
      padding: 2px;
    }
    </style>

</head>

<body>

    <?php 
    // include("include/tables.php");
    // createtables();
    $page = "serv";
    include('header.php');
    
    //Order by option
    $orderbytc = "";
    $orderid = "";
    if(isset($_GET["ord"]))
      $orderid = $_GET["ord"];
    if ($orderid==1)
    {
      $orderbytc = " order by servername";
    }
    
    $userinfo = selectUser($_SESSION['login_user']);
    
    //Pools 
    $db = opendb();   
    $ret = $db->query('SELECT * FROM POOL');
    
    $pool = array();
    while ($res = $ret->fetchArray(SQLITE3_ASSOC)) {
      $poolname = decryptdescription( $res["poolname"],$_SESSION['acc_ll'],$userinfo);
      $pool[$res["poolid"]] = $poolname;
    }
    
    $ret = $db->query('SELECT * FROM TYPE');
    
    $type = array();
    while ($res = $ret->fetchArray(SQLITE3_ASSOC)) {
      $type[$res["typeid"]] = $res["typename"];
    }
    
    $ret = $db->query('SELECT * FROM LOCATION');
    
    $location = array();
    while ($res = $ret->fetchArray(SQLITE3_ASSOC)) {
      $location[$res["locid"]] = $res["location"];
    }

//     include("include/tables.php");
//     createtables();
    $section = "";
    if(isset($_GET["s"]))
      $section = $_GET["s"];
      
    $acc_ll = 0;
    $u_l = $_SESSION['login_user'];
    if(isset($_SESSION['acc_ll']))
      $acc_ll = 1;
    
//     deletetable();

    $samenw = array();
    
    ?>
    
    <script type="text/javascript">
      var hash = window.location.hash;
    </script>
    
    <!-- Page Content -->
    <div class="container">
        <!-- Search Content -->
	<?php include('searchbox.php'); ?>
	
	
	<div style="margin-top:50px"></div>
	
	<div id="bodyelements" style="">
	  <ul class="nav nav-tabs ">
	      <li role="presentation"><a class="pointer" onclick="changepar('')" style="font-size:12px;padding: 5px 8px;">All Servers</a></li>
	      <li role="presentation"><a class="pointer" onclick="changepar('p')" style="font-size:12px;padding: 5px 8px;">Pool</a></li>
	      <li role="presentation"><a class="pointer" onclick="changepar('l')" style="font-size:12px;padding: 5px 8px;">Location</a></li>
	      <li role="presentation"><a class="pointer" onclick="changepar('t')" style="font-size:12px;padding: 5px 8px;">Type</a></li>
	      <li role="presentation"><a class="pointer" onclick="changepar('v')" style="font-size:12px;padding: 5px 8px;">Networks</a></li>
	  </ul>
	  
	  <div id="procedurepanel" style="display:none;vertical-align: top;margin-top:25px;margin-left:5px">
	      <div class="panel panel-default" style="min-height:600px;">
		  <div class="panel-heading" style="padding: 0px 5px;text-align:right">
		    <button type="button" onclick="addserverf()" id="newserb" class="btn btn-default navbar-btn"><img src="img/add.png" width="15px" style="margin-bottom:2px">
			<label style="font-weight:400;margin-bottom: 1px;cursor:pointer " class="newproc">Add Server</label></button>
		  </div>
		  <table class="table table-striped" id="listservers">
		    <thead>
		      <tr>
			<th><img src="img/server.png" height="15px"></th>
			<th style="cursor:pointer" onclick="orderby(1)">Name</th>
			<th style="cursor:pointer" onclick="orderby(0)">Date</th>
			<th>Ip</th>
			<th>Pool</th>
			<th>Type</th>
			<th>Location</th>
			<th><img src="img/users.png" height="15px"> Users </th>
			<th></th>
		      </tr>
		    </thead>
		    <tbody>
		      <tr style="background-color:white">
			<td ></td>
			<td  style=""></td>
			<td style="padding-top:0px;font-size:12px;"></td>
			<td style="padding-top:0px;font-size:12px;"></td>
			<td style="padding-top:0px;font-size:12px;color:#400000"></td>
			<td style="padding-top:0px;font-size:12px;color:#400000"></td>
			<td style="padding-top:0px;font-size:12px;color:#400000"></td>
		      </tr>
		      <?php		 
		      $ret = $db->query('SELECT * FROM SERVERS '.$orderbytc);
		      
		      $i = 0;
		      while ($res = $ret->fetchArray(SQLITE3_ASSOC)) {
			  $query = $db->prepare('SELECT COUNT(*) as total FROM USERSERVER where serverid=:id');
			  $query->bindValue(':id', $res["serverid"], SQLITE3_TEXT);
			  if (!($result = $query->execute())) {
			      continue;
			  } 
			  $nm = $result->fetchArray();
			  
			  $servern = decryptdescription($res["servername"],$_SESSION['acc_ll'],$userinfo);
			  $serverd = decryptdescription( $res["serverdes"],$_SESSION['acc_ll'],$userinfo);
		      ?>
			<tr class="hoverrow" data-id="<?php echo $res["serverid"]?>"
			id="nsr<?php echo $res["serverid"]?>" data-toggle="popover" data-trigger="hover" data-content="<?php echo $serverd?>"
			 >
			  <td style="   width: 20px;" onclick="gotoserverv(<?php echo $res["serverid"]?>,'')"><img src="img/bullet2.gif" height="6px"></td>
			  
			  <td onclick="gotoserverv(<?php echo $res["serverid"]?>,'')"><?php echo $servern?></td>
			  
			  <td style="padding-top:0px;font-size:12px;" onclick="gotoserverv(<?php echo $res["serverid"]?>,'')"><?php echo $res["creationdates"]?></td>
			  
			  <td style="font-size:12px" onclick="gotoserverv(<?php echo $res["serverid"]?>,'')">
			  <?php 
			  $new = $db->prepare('SELECT * FROM IPSERVER WHERE serverid=:id');
			  $new->bindValue(':id', $res["serverid"], SQLITE3_TEXT);
			  if (!($resultx = $new->execute())) {
			      continue;
			  } 
			  while ($resx = $resultx->fetchArray(SQLITE3_ASSOC)) {
			  $ipd = decryptdescription( $resx["ipserver"],$_SESSION['acc_ll'],$userinfo);
			  $mascd = decryptdescription( $resx["mascara"],$_SESSION['acc_ll'],$userinfo);
			  ?>
			    <?php echo $ipd."/".$mascd?><br>
			  <?php 
			  }
			  
			  ?>
			  </td>
			  
			  <td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $res["serverid"]?>,'')"><?php if (isset($pool[$res["spoolid"]])) echo  $pool[$res["spoolid"]]?></td>
			  <td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $res["serverid"]?>,'')"><?php if (isset($type[$res["stypeid"]])) echo  $type[$res["stypeid"]]?></td>
			  <td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $res["serverid"]?>,'')"><?php if (isset($location[$res["slocationid"]])) echo $location[$res["slocationid"]]?></td>
			  <td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $res["serverid"]?>,'')"><?php if (isset($nm["total"])) echo  $nm["total"]?></td>
			  <td>
			  <img style="cursor:pointer;margin-left:10px;visibility: hidden" width="17" onclick="editserver(<?php echo $res["serverid"]?>)" id="editserver<?php echo $res["serverid"]?>" src="img/edit2.png">
			  <img style="cursor:pointer;visibility: hidden;margin-left:10px" width="17" onclick="deleteserver(<?php echo $res["serverid"]?>,'<?php echo $res["servername"]?>')" id="deleteserver<?php echo $res["serverid"]?>" src="img/delete2.png">	
			  </td>
			</tr>
		      <?php
// 		      $samenw
// 		      array_push($samenw[], "apple");
		      }
		      ?>
		      
		    </tbody>
		  </table>
		  
		  <div id="newserver" style="display:none;margin: 30px;">
		    <form role="form" method="post" name="newserver" id="newserver">
		      <div class="form-group" >		  
			  <label>Name</label>
			  <input class="form-control" name="name" id="nameinput" style="width:40%">
			  <p class="help-block"></p>
		      </div>    
		      <div class="form-group" style="margin-top:20px;margin-bottom:20px" >		  
			  <label style="margin-right:5px">Ip</label>
			  <input class="form-control" name="ip" id="ipinput" style="width:30%;display:inline;margin-right:15px">
			  <label style="margin-right:5px">Mascara</label>
			  <input class="form-control" name="mascara" id="mascarainput" style="width:30%;display:inline">
			  <img style='cursor:pointer;margin-left:10px;margin-bottom:5px'
			    onclick='addnewip()' width="25px" id='newipadd' src='img/add.png'>
			  <p class="help-block"></p>
			  <div id="othersip">
			  
			  </div>
		      </div>
		      <div class="form-group">
			  <label>Description</label>
			  <textarea class="form-control" rows="8" name="description" id="description"></textarea>
		      </div>		      
		      <div class="form-group" >		  
			  <label>Role</label>
			  <input class="form-control" name="role" id="roleinput" style="width:40%">
			  <p class="help-block"></p>
		      </div>      
		      <div class="form-group" >		  
			  <label>Pool:</label>
			  <select class="selectform" id="poolName" name="poolName">
			  <?php		
			  $db = opendb();   
			  $ret = $db->query('SELECT * FROM POOL');
			  
			  $i = 0;
			  while ($cat = $ret->fetchArray(SQLITE3_ASSOC)) {
			  $pooln = decryptdescription($cat["poolname"],$_SESSION['acc_ll'],$userinfo);
			  ?>
			    <option value="<?php echo $cat["poolid"]?>"><?php echo $pooln?></option>
			  <?php
			  }
			  ?>
			</select>
		      </div>  
		      <div class="form-group" >		  
			  <label>Type:</label>
			  <select class="selectform" id="typeName" name="typeName">
			  <?php		    
			  $ret = $db->query('SELECT * FROM TYPE');
			  
			  $i = 0;
			  while ($cat = $ret->fetchArray(SQLITE3_ASSOC)) {
			  ?>
			    <option value="<?php echo $cat["typeid"]?>"><?php echo $cat["typename"]?></option>
			  <?php
			  }
			  ?>
			  </select>
		      </div>  
		      <div class="form-group" >		  
			  <label>Location:</label>
			  <select class="selectform" id="locName" name="locName">
			  <?php		    
			  $ret = $db->query('SELECT * FROM LOCATION');
			  
			  $i = 0;
			  while ($cat = $ret->fetchArray(SQLITE3_ASSOC)) {
			  ?>
			    <option value="<?php echo $cat["locid"]?>"><?php echo $cat["location"]?></option>
			  <?php
			  }
			  ?>
			  </select>
		      </div>  
		      <div class="form-group" >		  
			  <label>Users:</label></p>
			  <div id="userslist">
			  </div>
			  <!--<select class="selectform" id="usersname" name="usersname">
			  <option value="d"></option>
			    <?php		    
			    $ret = $db->query('SELECT * FROM USERS');
			    
			    $i = 0;
			    while ($cat = $ret->fetchArray(SQLITE3_ASSOC)) {
			    ?>
			      <option value="<?php echo $cat["username"]?>"><?php echo $cat["username"]?></option>
			    <?php
			    }
			    ?>
			  </select>
			  <label style="margin-right:5px">or</label>-->
			  <input type="hidden" id="acc_ll" name="acc_ll" value="<?php echo $acc_ll?>">
			  <input type="hidden" id="usercreator" name="usercreator" value="<?php echo $u_l?>">
			  Username:<input class="form-control" name="usernameinput" id="usernameinput" style="margin-left:10px;width:20%;display: inline;">
			  <input style="display:none" type="password" name="fakepasswordremembered"/>
                          Password:<input class="form-control" type="password" id="psw" name="psw" autocomplete="off" style="margin-left:10px;width:20%;display: inline;">
			  <button type="button" class="btn btn-default" onclick="adduser()">Add</button>
			  <p class="help-block"></p>
		      </div>  
		      
		      <div class="form-group" >		  
			  <label>Procedures:</label></p>
			  <span id="proclist2">
			  </span>
			  <select class="selectform" id="procnames" name="procnames">
			  <option value="d"></option>
			    <?php		    
			    $ret = $db->query('SELECT * FROM PROCEDURES order by procname');
			    
			    $i = 0;
			    while ($cat = $ret->fetchArray(SQLITE3_ASSOC)) {
			    ?>
			      <option value="<?php echo $cat["procid"]?>"><?php echo $cat["procname"]?></option>
			    <?php
			    }
			    ?>
			  </select>
			  <p class="help-block"></p>
		      </div> 
		      

		      <img class="loadingicon4" src="img/loading.gif" style="visibility: hidden;    margin: 10px;" width="20">
		    </form>
		    
		    <button type="button " id="addbutton" class="btn btn-success" onclick="addserver()">Add Server</button>
                    <button type="reset" id="cancelbutton" class="btn btn-default" onclick="cancelserver()">Cancel</button>
		  </div>
		  
	      </div>
	  </div>
	  
	  <div id="categorypanel" class="panelss" style="display:none;vertical-align: top;margin-top:25px;margin-left:5px">
	    <div class="panel panel-default " style="min-height:600px;">
		  <div class="panel-heading" style="padding: 0px 5px;text-align:right">
		    <button type="button" data-toggle="modal" data-target=".bs-newpool-modal" class="btn btn-default navbar-btn"><img src="img/add.png" width="15px" style="margin-bottom:2px">
			<label style="font-weight:400;margin-bottom: 1px; " class="newproc">Add Pool</label></button>
		  </div>
		  <div class="panel-group listcat" role="tablist" aria-multiselectable="true" id="accordion" style="">
		  <?php 
		  $db = opendb();
		  $ret = $db->query('SELECT * FROM POOL ');
		  
		  $i = 0;
		  while ($cat = $ret->fetchArray(SQLITE3_ASSOC)) {
		  $pooln = decryptdescription($cat["poolname"],$_SESSION['acc_ll'],$userinfo);
		  $poold = decryptdescription($cat["pooldes"],$_SESSION['acc_ll'],$userinfo);
		  ?>		  
		    <div class="panel panel-default">
		      <div class="panel-heading" style="position: relative;height: 30px;" id="poold<?php echo $cat["poolid"]?>" 
		        data-toggle="popover" data-trigger="hover" data-content="<?php echo $poold?>">			
<!--			<div class="left">-->
			  <h4 class="panel-title" style="    margin: 5px;">
			    <a class="accordion-toggle" data-toggle="collapse"  href="#collapsep<?php echo $cat["poolid"]?>">
			      <span style="font-weight: 600;"><?php echo $pooln?></span>
			    </a>
			    <img style="cursor:pointer;margin-left:10px" width="17" onclick="editpoolfun(<?php echo $cat["poolid"]?>,'<?php echo $pooln?>','<?php echo $poold?>')" id="editpoolfun<?php echo $cat["poolid"]?>" src="img/edit2.png">
			    <img style="cursor:pointer;" width="17" onclick="deletepool(<?php echo $cat["poolid"]?>,'<?php echo $pooln?>')" id="deletepool<?php echo $cat["poolid"]?>" src="img/delete2.png">
			      
			  </h4>
<!--			</div>
			<div class="right">
			  <img style="cursor:pointer;" onclick="deletepool(<?php echo $cat["poolid"]?>)" id="deletepool<?php echo $cat["poolid"]?>" src="img/closeimg.png"></td>	
			</div>-->
		      </div>
		      <div id="collapsep<?php echo $cat["poolid"]?>" class="panel-collapse collapse in">
			<div class="panel-body">
			 <?php 
			  $new = $db->prepare('SELECT * FROM SERVERS WHERE spoolid=:id');
			  $new->bindValue(':id', $cat["poolid"], SQLITE3_TEXT);
			  if (!($resultx = $new->execute())) {
			      continue;
			  } 
			  $h = 0;
			  while ($resx = $resultx->fetchArray(SQLITE3_ASSOC)) {
			  $servern = decryptdescription($resx["servername"],$_SESSION['acc_ll'],$userinfo);
			  if ($h==0)
			  {
			 ?>
			  <table class="table table-striped">
			  <thead>
			    <tr>
			      <th><img src="img/server.png" height="15px"></th>
			      <th>Name</th>
			      <th>Date</th>
			      <th>Ip</th>
			      <th>Pool</th>
			      <th>Type</th>
			      <th>Location</th>
			      <th><img src="img/users.png" height="15px"> Users </th>
			    </tr>
			  </thead>
			  <tbody>
			    <tr style="background-color:white">
			      <td ></td>
			      <td  style=""></td>
			      <td style="padding-top:0px;font-size:12px;"></td>
			      <td style="padding-top:0px;font-size:12px;"></td>
			      <td style="padding-top:0px;font-size:12px;color:#400000"></td>
			      <td style="padding-top:0px;font-size:12px;color:#400000"></td>
			    </tr>
			    <?php
			    }
			    $h++;
				$query = $db->prepare('SELECT COUNT(*) as total FROM USERSERVER where serverid=:id');
				$query->bindValue(':id', $resx["serverid"], SQLITE3_TEXT);
				if (!($result = $query->execute())) {
				    continue;
				} 
				$nm = $result->fetchArray();
			    ?>
			      <tr class="hoverrow">
				<td style="   width: 20px;"><img src="img/bullet2.gif" height="6px"></td>
				<td  style="" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php echo $servern?></td>
				<td style="padding-top:0px;font-size:12px;" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php echo $resx["creationdates"]?></td>
				<td style="font-size:12px" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')">
				<?php 
				$new2 = $db->prepare('SELECT * FROM IPSERVER WHERE serverid=:id');
				$new2->bindValue(':id', $resx["serverid"], SQLITE3_TEXT);
				if (!($resultx2 = $new2->execute())) {
				    continue;
				} 
				while ($resx2 = $resultx2->fetchArray(SQLITE3_ASSOC)) {
				$ipd = decryptdescription( $resx2["ipserver"],$_SESSION['acc_ll'],$userinfo);
				$mascd = decryptdescription( $resx2["mascara"],$_SESSION['acc_ll'],$userinfo);
				?>
				  <?php echo $ipd."/".$mascd?><br>
				<?php 
				}
				?>
				</td>
				<td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php if (isset($pool[$resx["spoolid"]])) echo $pool[$resx["spoolid"]]?></td>
				<td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php if (isset($type[$resx["stypeid"]])) echo $type[$resx["stypeid"]]?></td>
				<td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php if (isset($location[$resx["slocationid"]])) echo $location[$resx["slocationid"]]?></td>
				<td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php if (isset($nm["total"])) echo $nm["total"]?></td>
			      </tr>
			    <?php
			    }
			    ?>			   
			  </tbody>
			</table>
			</div>
		      </div>
		    </div>		  
		  <?php 
		 }
		  ?>
		  
		</div>
	      </div>
	  </div>
	  
	  <div id="locationpanel" class="panelss" style="display:none;vertical-align: top;margin-top:25px;margin-left:5px">
	    <div class="panel panel-default " style="min-height:600px;">
		  <div class="panel-heading" style="padding: 0px 5px;text-align:right">
		    <button type="button" data-toggle="modal" data-target=".bs-newlocation-modal" class="btn btn-default navbar-btn"><img src="img/add.png" width="15px" style="margin-bottom:2px">
			<label style="font-weight:400;margin-bottom: 1px; " class="newproc">Add Location</label></button>
		  </div>
		  <div class="panel-group listcat" role="tablist" aria-multiselectable="true" id="accordion" style="">
			<?php 
			$db = opendb();
			$ret = $db->query('SELECT * FROM LOCATION');
			
			$i = 0;
			while ($res = $ret->fetchArray(SQLITE3_ASSOC)) {
			?>
			<div class="panel panel-default">
			  <div class="panel-heading" style="position: relative;height: 30px;">			
<!--			    <div class="left">-->
			      <h4 class="panel-title" style="    margin: 5px;">
				<a class="accordion-toggle" data-toggle="collapse"  href="#collapsel<?php echo $res["locid"]?>" style="font-size:14px">
				  <span style="font-weight: 600;"><?php echo $res["location"]?></span>
				</a>
				<img style="cursor:pointer;margin-left:10px" width="17" onclick="editlocationfun(<?php echo $res["locid"]?>,'<?php echo $res["location"]?>')" id="edditcategory<?php echo $res["locid"]?>" src="img/edit2.png">
			        <img style="cursor:pointer;" width="17" onclick="deletelocation(<?php echo $res["locid"]?>,'<?php echo $res["location"]?>')" id="deletelocation<?php echo $res["locid"]?>" src="img/delete2.png">	
			      
			      </h4>
<!--			    </div>
			    <div class="right">
			      <img style="cursor:pointer;" onclick="deletelocation(<?php echo $res["locid"]?>)" id="deletelocation<?php echo $res["locid"]?>" src="img/closeimg.png"></td>	
			    </div>-->
			  </div>
			  <div id="collapsel<?php echo $res["locid"]?>" class="panel-collapse collapse in">
			    <div class="panel-body">
				<?php 
				  $new = $db->prepare('SELECT * FROM SERVERS WHERE slocationid=:id');
				  $new->bindValue(':id', $res["locid"], SQLITE3_TEXT);
				  if (!($resultx = $new->execute())) {
				      continue;
				  } 
				  $h = 0;
				  while ($resx = $resultx->fetchArray(SQLITE3_ASSOC)) {
				  $servern = decryptdescription($resx["servername"],$_SESSION['acc_ll'],$userinfo);
				  if ($h==0)
				  {
				?>
				<table class="table table-striped">
				  <thead>
				    <tr>
				      <th><img src="img/server.png" height="15px"></th>
				      <th>Name</th>
				      <th>Date</th>
				      <th>Ip</th>
				      <th>Pool</th>
				      <th>Type</th>
				      <th>Location</th>
				      <th><img src="img/users.png" height="15px"> Users </th>
				    </tr>
				  </thead>
				  <tbody>
				    <tr style="background-color:white">
				      <td ></td>
				      <td  style=""></td>
				      <td style="padding-top:0px;font-size:12px;"></td>
				      <td style="padding-top:0px;font-size:12px;"></td>
				      <td style="padding-top:0px;font-size:12px;color:#400000"></td>
				      <td style="padding-top:0px;font-size:12px;color:#400000"></td>
				    </tr>
				    <?php		
				    }
				    $h++;
					$query = $db->prepare('SELECT COUNT(*) as total FROM USERSERVER where serverid=:id');
					$query->bindValue(':id', $resx["serverid"], SQLITE3_TEXT);
					if (!($result = $query->execute())) {
					    continue;
					} 
					$nm = $result->fetchArray();
				    ?>
				      <tr class="hoverrow">
					<td style="   width: 20px;"><img src="img/bullet2.gif" height="6px"></td>
					<td  style="" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php echo $servern?></td>
					<td style="padding-top:0px;font-size:12px;" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php echo $resx["creationdates"]?></td>
					<td style="font-size:12px" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')">
					<?php 
					$new2 = $db->prepare('SELECT * FROM IPSERVER WHERE serverid=:id');
					$new2->bindValue(':id', $resx["serverid"], SQLITE3_TEXT);
					if (!($resultx2 = $new2->execute())) {
					    continue;
					} 
					while ($resx2 = $resultx2->fetchArray(SQLITE3_ASSOC)) {
					$ipd = decryptdescription( $resx2["ipserver"],$_SESSION['acc_ll'],$userinfo);
					$mascd = decryptdescription( $resx2["mascara"],$_SESSION['acc_ll'],$userinfo);
					?>
					  <?php echo $ipd."/".$mascd?><br>
					<?php 
					}
					?>
					</td>
					<td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php if (isset($pool[$resx["spoolid"]])) echo $pool[$resx["spoolid"]]?></td>
					<td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php if (isset($type[$resx["stypeid"]])) echo $type[$resx["stypeid"]]?></td>
					<td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php if (isset($location[$resx["slocationid"]])) echo $location[$resx["slocationid"]]?></td>
					<td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php if (isset($nm["total"])) echo $nm["total"]?></td>
				      </tr>
				    <?php
				    }
				    ?>			   
				  </tbody>
				</table>
			    </div>
			  </div>
			</div>
			<?php 
			}
			?>
		  </div>
	      </div>
	  </div>
	  
	  <div id="typepanel" class="panelss" style="display:none;vertical-align: top;margin-top:25px;margin-left:5px">
	    <div class="panel panel-default " style="min-height:600px;">
		  <div class="panel-heading" style="padding: 0px 5px;text-align:right">
		    <button type="button" data-toggle="modal" data-target=".bs-newtype-modal" class="btn btn-default navbar-btn"><img src="img/add.png" width="15px" style="margin-bottom:2px">
			<label style="font-weight:400;margin-bottom: 1px; " class="newproc">Add Type</label></button>
		  </div>
		  <div class="panel-group listcat" role="tablist" aria-multiselectable="true" id="accordion" >
			<?php 
			$db = opendb();
			$ret = $db->query('SELECT * FROM TYPE');
			
			$i = 0;
			while ($res = $ret->fetchArray(SQLITE3_ASSOC)) {
			?>
			<div class="panel panel-default">
			  <div class="panel-heading" style="position: relative;height: 30px;">			
<!--			    <div class="left">-->
			      <h4 class="panel-title" style="    margin: 5px;">
				<a class="accordion-toggle" data-toggle="collapse"  href="#collapset<?php echo $res["typeid"]?>" style="font-size:14px">
				  <span style="font-weight: 600;"><?php echo $res["typename"]?></span>
				</a>
				<img style="cursor:pointer;margin-left:10px" width="17" onclick="edittypefunction(<?php echo $res["typeid"]?>,'<?php echo $res["typename"]?>')" id="edditcategory<?php echo $res["typeid"]?>" src="img/edit2.png">
			        <img style="cursor:pointer;" onclick="deletetype(<?php echo $res["typeid"]?>,'<?php echo $res["typename"]?>')" width="12" id="deletetype<?php echo $res["typeid"]?>" src="img/delete2.png">	
			      </h4>
<!--			    </div>-->
<!--			    <div class="right">-->
			      
<!--			    </div>-->
			  </div>
			  <div id="collapset<?php echo $res["typeid"]?>" class="panel-collapse collapse in">
			    <div class="panel-body">
			      <?php 
				$new = $db->prepare('SELECT * FROM SERVERS WHERE stypeid=:id');
				$new->bindValue(':id', $res["typeid"], SQLITE3_TEXT);
				if (!($resultx = $new->execute())) {
				    continue;
				}  
				$h = 0;
				while ($resx = $resultx->fetchArray(SQLITE3_ASSOC)) {
				$servern = decryptdescription($resx["servername"],$_SESSION['acc_ll'],$userinfo);
				if ($h==0)
				{
			      ?>
			      <table class="table table-striped">
				  <thead>
				    <tr>
				      <th><img src="img/server.png" height="15px"></th>
				      <th>Name</th>
				      <th>Date</th>
				      <th>Ip</th>
				      <th>Pool</th>
				      <th>Type</th>
				      <th>Location</th>
				      <th><img src="img/users.png" height="15px"> Users </th>
				    </tr>
				  </thead>
				  <tbody>
				    <tr style="background-color:white">
				      <td ></td>
				      <td  style=""></td>
				      <td style="padding-top:0px;font-size:12px;"></td>
				      <td style="padding-top:0px;font-size:12px;"></td>
				      <td style="padding-top:0px;font-size:12px;color:#400000"></td>
				      <td style="padding-top:0px;font-size:12px;color:#400000"></td>
				    </tr>
				    <?php		 
				    }
				    $h++;
					$query = $db->prepare('SELECT COUNT(*) as total FROM USERSERVER where serverid=:id');
					$query->bindValue(':id', $resx["serverid"], SQLITE3_TEXT);
					if (!($result = $query->execute())) {
					    continue;
					} 
					$nm = $result->fetchArray();
				    ?>
				      <tr class="hoverrow">
					<td style="   width: 20px;"><img src="img/bullet2.gif" height="6px"></td>
					<td  style="" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php echo $servern?></td>
					<td style="padding-top:0px;font-size:12px;" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php echo $resx["creationdates"]?></td>
					<td style="font-size:12px" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')">
					<?php 
					$new2 = $db->prepare('SELECT * FROM IPSERVER WHERE serverid=:id');
					$new2->bindValue(':id', $resx["serverid"], SQLITE3_TEXT);
					if (!($resultx2 = $new2->execute())) {
					    continue;
					} 
					while ($resx2 = $resultx2->fetchArray(SQLITE3_ASSOC)) {
					$ipd = decryptdescription( $resx2["ipserver"],$_SESSION['acc_ll'],$userinfo);
					$mascd = decryptdescription( $resx2["mascara"],$_SESSION['acc_ll'],$userinfo);
					?>
					  <?php echo $ipd."/".$mascd?><br>
					<?php 
					}
					?>
					</td>
					<td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php if (isset($pool[$resx["spoolid"]])) echo $pool[$resx["spoolid"]]?></td>
					<td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php if (isset($type[$resx["stypeid"]])) echo $type[$resx["stypeid"]]?></td>
					<td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php if (isset($location[$resx["slocationid"]])) echo $location[$resx["slocationid"]]?></td>
					<td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php if (isset($nm["total"])) echo $nm["total"]?></td>
				      </tr>
				    <?php
				    }
				    ?>			   
				  </tbody>
				</table>
			    </div>
			  </div>
			</div>
			<?php 
			}
			?>
		  </div>
	      </div>
	  </div>
	  
	  <div id="vpnpanel" class="panelss" style="display:none;vertical-align: top;margin-top:25px;margin-left:5px">
	    <div class="panel panel-default " style="min-height:600px;">
		  <div class="panel-heading" style="padding: 0px 5px;text-align:right;height:50px">
		    <label style="font-weight:400;margin-bottom: 1px; " class="newproc"></label>
		  </div>
		  <div class="panel-group listcat" role="tablist" aria-multiselectable="true" id="accordion">
			<?php 
			$db = opendb();
			$ret = $db->query('SELECT * FROM IPSERVER group by netAddress');
			
			$i = 0;
			while ($res = $ret->fetchArray(SQLITE3_ASSOC)) {
			$netd = decryptdescription( $res["netAddress"],$_SESSION['acc_ll'],$userinfo);
			$masd = decryptdescription( $res["mascara"],$_SESSION['acc_ll'],$userinfo);
			$title = $netd."/".$masd;
			?>
			<div class="panel panel-default">
			  <div class="panel-heading" style="position: relative;height: 30px;">			
<!--			    <div class="left">-->
			      <h4 class="panel-title" style="    margin: 5px;">
				<a class="accordion-toggle" data-toggle="collapse"  href="#collapse<?=$i?>" style="font-size:14px">
				  <span style="font-weight: 600;"><?php echo $title?></span>
				</a>
			     </h4>
<!--			    </div>-->
<!--			    <div class="right">-->
			      
<!--			    </div>-->
			  </div>
			  <div id="collapse<?=$i?>" class="panel-collapse collapse">
			    <div class="panel-body">
			      <?php 
				$new = $db->prepare('SELECT * FROM SERVERS as s,IPSERVER as ips WHERE s.serverid=ips.serverid and ips.netAddress=:net');
				$new->bindValue(':net', $res["netAddress"], SQLITE3_TEXT);
				if (!($resultx = $new->execute())) {
				    continue;
				}  
				$h = 0;
				while ($resx = $resultx->fetchArray(SQLITE3_ASSOC)) {
				$servern = decryptdescription($resx["servername"],$_SESSION['acc_ll'],$userinfo);
				if ($h==0)
				{
			      ?>
			      <table class="table table-striped">
				  <thead>
				    <tr>
				      <th><img src="img/server.png" height="15px"></th>
				      <th>Name</th>
				      <th>Date</th>
				      <th>Ip</th>
				      <th>Pool</th>
				      <th>Type</th>
				      <th>Location</th>
				      <th><img src="img/users.png" height="15px"> Users </th>
				    </tr>
				  </thead>
				  <tbody>
				    <tr style="background-color:white">
				      <td ></td>
				      <td  style=""></td>
				      <td style="padding-top:0px;font-size:12px;"></td>
				      <td style="padding-top:0px;font-size:12px;"></td>
				      <td style="padding-top:0px;font-size:12px;color:#400000"></td>
				      <td style="padding-top:0px;font-size:12px;color:#400000"></td>
				    </tr>
				    <?php		 
				    }
				    $h++;
					$query = $db->prepare('SELECT COUNT(*) as total FROM USERSERVER where serverid=:id');
					$query->bindValue(':id', $resx["serverid"], SQLITE3_TEXT);
					if (!($result = $query->execute())) {
					    continue;
					} 
					$nm = $result->fetchArray();
				    ?>
				      <tr class="hoverrow">
					<td style="   width: 20px;"><img src="img/bullet2.gif" height="6px"></td>
					<td  style="" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php echo $servern?></td>
					<td style="padding-top:0px;font-size:12px;" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php echo $resx["creationdates"]?></td>
					<td style="font-size:12px" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')">
					<?php 
					$new2 = $db->prepare('SELECT * FROM IPSERVER WHERE serverid=:id');
					$new2->bindValue(':id', $resx["serverid"], SQLITE3_TEXT);
					if (!($resultx2 = $new2->execute())) {
					    continue;
					} 
					while ($resx2 = $resultx2->fetchArray(SQLITE3_ASSOC)) {
					$ipd = decryptdescription( $resx2["ipserver"],$_SESSION['acc_ll'],$userinfo);
					$mascd = decryptdescription( $resx2["mascara"],$_SESSION['acc_ll'],$userinfo);
					?>
					  <?php echo $ipd."/".$mascd?><br>
					<?php 
					}
					?>
					</td>
					<td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php if (isset($pool[$resx["spoolid"]])) echo $pool[$resx["spoolid"]]?></td>
					<td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php if (isset($type[$resx["stypeid"]])) echo $type[$resx["stypeid"]]?></td>
					<td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php if (isset($location[$resx["slocationid"]])) echo $location[$resx["slocationid"]]?></td>
					<td style="padding-top:0px;font-size:12px;color:#400000" onclick="gotoserverv(<?php echo $resx["serverid"]?>,'')"><?php if (isset($nm["total"])) echo $nm["total"]?></td>
				      </tr>
				    <?php
				    }
				    ?>			   
				  </tbody>
				</table>
			    </div>
			  </div>
			</div>
			<?php 
			$i++;
			}
			?>
		  </div>
	      </div>
	  </div>
	  
        </div><!-- body elements -->
    
    </div>

     <!-- Creates modal for new pool -->
      <div class="modal fade bs-newpool-modal" data-keyboard="false"  role="dialog" aria-labelledby="poolmodal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
	  <div class="modal-content">
	    <div class="modal-header">
	      <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></a>
	      <h4 class="modal-title" id="myModalLabel">New Pool</h4>
	    </div>
	    <div class="modal-body">
	      <form role="form" method="post" name="newpool" id="newpool">
	      <input type="hidden" name="section" value="<?php echo $section?>">
		<div class="form-group">
		    <label>Name</label>
		    <input class="form-control" name="namepool" id="namepool">
		</div>
		<div class="form-group">
		    <label>Description</label>
		    <textarea class="form-control" rows="8" name="despool" id="despool"></textarea>
		</div>
	      <input type="hidden" id="vl" name="vl" value="">
	      <div style="text-align:right"><img class="loadingicon" src="img/loading.gif" style="visibility: hidden" width="20"></div>
	     </div>
	     </form>
	    <div class="modal-footer">
	      <button type="button" class="btn btn-primary" onclick='return addpool();'>Ok</button>
	      <a type="button" class="btn btn-default" data-dismiss="modal">Cancel</a>
	    </div>
	  </div>
	</div>
      </div>
      
      <div class="modal fade bs-editpool-modal" data-keyboard="false"  role="dialog" aria-labelledby="poolmodal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
	  <div class="modal-content">
	    <div class="modal-header">
	      <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></a>
	      <h4 class="modal-title" id="myModalLabel">Edit Pool</h4>
	    </div>
	    <div class="modal-body">
	      <form role="form" method="post" name="editpool" id="editpool">
	      <input type="hidden" name="section" value="<?php echo $section?>">
	      <input type="hidden" name="poolid" value="">
		<div class="form-group">
		    <label>Name</label>
		    <input class="form-control" name="namepool" id="namepool">
		</div>
		<div class="form-group">
		    <label>Description</label>
		    <textarea class="form-control" rows="8" name="despool" id="despool"></textarea>
		</div>
	      <input type="hidden" id="vl" name="vl" value="">
	      <div style="text-align:right"><img class="loadingicon" src="img/loading.gif" style="visibility: hidden" width="20"></div>
	     </div>
	     </form>
	    <div class="modal-footer">
	      <button type="button" class="btn btn-primary" onclick='return editpool0();'>Ok</button>
	      <a type="button" class="btn btn-default" data-dismiss="modal">Cancel</a>
	    </div>
	  </div>
	</div>
      </div>
      
      <!-- Creates modal for new location -->
      <div class="modal fade bs-newlocation-modal" data-keyboard="false" role="dialog" aria-labelledby="poolmodal" aria-hidden="true">
	<div class="modal-dialog modal-sm">
	  <div class="modal-content">
	    <div class="modal-header">
	      <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></a>
	      <h4 class="modal-title" id="myModalLabel">New Location</h4>
	    </div>
	    <div class="modal-body">
	      <form role="form" method="post" name="newlocation" id="newlocation">
		<div class="form-group">
		    <label>Name</label>
		    <input class="form-control" name="namelocation" id="namelocation">
		</div>
	      <input type="hidden" id="vl" name="vl" value="">
	      <div style="text-align:right"><img class="loadingicon2" src="img/loading.gif" style="visibility: hidden" width="20"></div>
	     </div>
	     </form>
	    <div class="modal-footer">
	      <button type="button" class="btn btn-primary" onclick='return addlocation();'>Ok</button>
	      <a type="button" class="btn btn-default" data-dismiss="modal">Cancel</a>
	    </div>
	  </div>
	</div>
      </div>
      
      <div class="modal fade bs-editlocation-modal" data-keyboard="false" role="dialog" aria-labelledby="poolmodal" aria-hidden="true">
	<div class="modal-dialog modal-sm">
	  <div class="modal-content">
	    <div class="modal-header">
	      <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></a>
	      <h4 class="modal-title" id="myModalLabel">Edit Location</h4>
	    </div>
	    <div class="modal-body">
	      <form role="form" method="post" name="editlocation" id="newlocation">
	      <input type="hidden" name="locationid" value="">
		<div class="form-group">
		    <label>Name</label>
		    <input class="form-control" name="namelocation" id="namelocation">
		</div>
	      <input type="hidden" id="vl" name="vl" value="">
	      <div style="text-align:right"><img class="loadingicon2" src="img/loading.gif" style="visibility: hidden" width="20"></div>
	     </div>
	     </form>
	    <div class="modal-footer">
	      <button type="button" class="btn btn-primary" onclick='return editlocation0();'>Ok</button>
	      <a type="button" class="btn btn-default" data-dismiss="modal">Cancel</a>
	    </div>
	  </div>
	</div>
      </div>
      
      <!-- Creates modal for new type -->
      <div class="modal fade bs-newtype-modal" data-keyboard="false" role="dialog" aria-labelledby="poolmodal" aria-hidden="true">
	<div class="modal-dialog modal-sm">
	  <div class="modal-content">
	    <div class="modal-header">
	      <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></a>
	      <h4 class="modal-title" id="myModalLabel">New Type</h4>
	    </div>
	    <div class="modal-body">
	      <form role="form" method="post" name="newtype" id="newtype">
		<div class="form-group">
		    <label>Name</label>
		    <input class="form-control" name="nametype" id="nametype">
		</div>
	      <input type="hidden" id="vl" name="vl" value="">
	      <div style="text-align:right"><img class="loadingicon3" src="img/loading.gif" style="visibility: hidden" width="20"></div>
	     </div>
	     </form>
	    <div class="modal-footer">
	      <button type="button" class="btn btn-primary" onclick='return addtype();'>Ok</button>
	      <a type="button" class="btn btn-default" data-dismiss="modal">Cancel</a>
	    </div>
	  </div>
	</div>
      </div>
      
      <div class="modal fade bs-edittype-modal" data-keyboard="false" role="dialog" aria-labelledby="poolmodal" aria-hidden="true">
	<div class="modal-dialog modal-sm">
	  <div class="modal-content">
	    <div class="modal-header">
	      <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></a>
	      <h4 class="modal-title" id="myModalLabel">Edit Type</h4>
	    </div>
	    <div class="modal-body">
	      <form role="form" method="post" name="edittype" id="edittype">
	      <input type="hidden" name="typeid" value="">
		<div class="form-group">
		    <label>Name</label>
		    <input class="form-control" name="nametype" id="nametype">
		</div>
	      <input type="hidden" id="vl" name="vl" value="">
	      <div style="text-align:right"><img class="loadingicon3" src="img/loading.gif" style="visibility: hidden" width="20"></div>
	     </div>
	     </form>
	    <div class="modal-footer">
	      <button type="button" class="btn btn-primary" onclick='return edittype0();'>Ok</button>
	      <a type="button" class="btn btn-default" data-dismiss="modal">Cancel</a>
	    </div>
	  </div>
	</div>
      </div>
      
      <!-- Creates modal for new SSHPass -->
      <div class="modal fade bs-newsshpass-modal" data-keyboard="false" role="dialog" aria-labelledby="poolmodal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
	  <div class="modal-content">
	    <div class="modal-header">
	      <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></a>
	      <h4 class="modal-title" id="newsshpasslabel">SSH Keys and Passphrase</h4>
	    </div>
	    <div class="modal-body">
	      <form role="form" method="post" name="newtype" id="sshkeyuser">
	        <span id="currentsshuser" style="display:none"></span>
	        <select style="margin-bottom: 20px;" class="selectform" id="sshSel" name="sshSel">
		</select>
		<button style="padding: 3px;visibility:hidden" type="button" id="delsshbutt" onclick="deletekey()" class="btn btn-danger">Delete Key</button>
		<div class="form-group">
		    <label>SSH</label>
		    <textarea class="form-control" rows="8" name="sshkey" id="sshkey"></textarea>
		</div>
		<div class="form-group">
		    <label>Passphrase</label>
		    <textarea class="form-control" rows="8" name="passp" id="passp"></textarea>
		</div>
	      <input type="hidden" id="vl" name="vl" value="">
	      <div style="text-align:right"><img class="loadingicon3" src="img/loading.gif" style="visibility: hidden" width="20"></div>
	     </div>
	     </form>
	    <div class="modal-footer">
	      <button type="button" class="btn btn-primary" onclick='return addsshuser();'>Add</button>
	      <a type="button" class="btn btn-default" data-dismiss="modal">Cancel</a>
	    </div>
	  </div>
	</div>
      </div>
      
    <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/aux.js"></script>
    
    <script type="text/javascript">
    function removeItem(e,s){e.hasOwnProperty(s)&&(!isNaN(parseInt(s))&&e instanceof Array?e.splice(s,1):delete e[s])}function addserverf(){$("#newserver").css("display","block"),$("#listservers").css("display","none"),$("#newserb").css("visibility","hidden")}function cancelserver(){$("#newserver").css("display","none"),$("#listservers").css("display","table"),$("#newserb").css("visibility","visible")}function adduser(){var e=document.forms.newserver.usernameinput,s=document.forms.newserver.psw,o=$("#userslist").html();return null!=s&&""==s.value?void alert("Password is missing"):(0==$("#sp"+e.value).length&&(usrarr[e.value]=s.value,$("#userslist").html(o+"<div class='userdiv' style='margin-bottom:5px' id='sp"+e.value+"'><span>"+e.value+"</span><img style='cursor:pointer;'onclick='removeuser(\""+e.value+"\")' id='usrdel"+e.value+"' src='img/closeimg.png'><button id='sshbutton"+e.value+"' style='padding: 3px;' type='button' onclick='openmodalssh(\""+e.value+"\")' class='btn btn-success'>SSH KEYS</button></div>"),console.log(usersk),usersk[e.value]=[],usserph[e.value]=[],console.log(usersk)),$("#usernameinput").val(""),void $("#psw").val(""))}function openmodalssh(e){var s=usersk[e];usserph[e];$("#sshSel").empty();var o=0,t=0;$("#sshSel").append($("<option></option>").attr("value","").text(""));for(var n in s)0==o&&(t=n),$("#sshSel").append($("<option></option>").attr("value",n).text("SSh "+n)),o++;$("#currentsshuser").text(e),$("#sshkey").val(""),$("#newsshpasslabel").text("SSH Keys and Passphrase for "+e),$("#passp").val(""),$(".bs-newsshpass-modal").modal("show")}function deletekey(){var e=$("#currentsshuser").text(),s=usersk[e],o=usserph[e],t=$("#sshSel").find(":selected").val();t>-1&&(s.splice(t,1),o.splice(t,1)),$(".bs-newsshpass-modal").modal("hide")}function addsshuser(){var e=$("#currentsshuser").text(),s=document.forms.sshkeyuser.sshkey,o=document.forms.sshkeyuser.passp,t=usersk[e],n=$("#sshSel").find(":selected").val();if(""==n)var n=t.length;t[n]=s.value;var i=usserph[e];i[n]=o.value,usersk[e]=t,usserph[e]=i,$(".bs-newsshpass-modal").modal("hide")}function removeuser(e){$("#sp"+e).remove(),$("#usrdel"+e).remove(),$("#sshbutton"+e).remove(),removeItem(usrarr,e),removeItem(usersk,e),removeItem(usserph,e)}function removeproc(e){$("#spp"+e).remove(),$("#procdel"+e).remove()}function addpool(){var e=document.forms.newpool.namepool,s=document.forms.newserver.usercreator.value;if(""==e.value)return void alert("Name must be filled out");var o=document.forms.newpool.despool;$(".loadingicon").css("visibility","visible"),$.ajax({url:"include/ops.php",data:{consultid:"addpool",pool:e.value,des:o.value,user:s},type:"post",success:function(e){console.log(e);window.location.href.split("#")[0];setTimeout(function(){location.reload()},1e3)}})}function deletepool(e,s){var o=confirm("Are you sure you want to delete pool: "+s+"?");1==o?($("#deletepool"+e).attr("src","img/loading.gif"),$("#deletepool"+e).attr("width","20"),$.ajax({url:"include/ops.php",data:{consultid:"deletepool",id:e},type:"post",success:function(e){window.location.href.split("#")[0];setTimeout(function(){location.reload()},1e3)}})):console.log("no")}function editpoolfun(e,s,o){$(".bs-editpool-modal").modal("show"),document.forms.editpool.poolid.value=e,document.forms.editpool.namepool.value=s,document.forms.editpool.despool.value=o}function editpool0(){var e=document.forms.editpool.namepool,s=document.forms.editpool.poolid,o=document.forms.editpool.despool,t=document.forms.newserver.usercreator.value;return""==e.value?void alert("Name must be filled out"):($(".loadingicon").css("visibility","visible"),void $.ajax({url:"include/ops.php",data:{consultid:"editpool",pool:e.value,des:o.value,idpool:s.value,user:t},type:"post",success:function(e){console.log(e);window.location.href.split("#")[0];setTimeout(function(){location.reload()},1e3)}}))}function addlocation(){var e=document.forms.newlocation.namelocation;return""==e.value?void alert("Name must be filled out"):($(".loadingicon2").css("visibility","visible"),void $.ajax({url:"include/ops.php",data:{consultid:"addloc",location:e.value},type:"post",success:function(e){console.log(e);window.location.href.split("#")[0];setTimeout(function(){location.reload()},1e3)}}))}function deletelocation(e,s){var o=confirm("Are you sure you want to delete location: "+s+"?");1==o?($("#deletetype"+e).attr("src","img/loading.gif"),$("#deletetype"+e).attr("width","20"),$.ajax({url:"include/ops.php",data:{consultid:"deletelocation",id:e},type:"post",success:function(e){window.location.href.split("?")[0];setTimeout(function(){location.reload()},1e3)}})):console.log("no")}function editlocationfun(e,s){$(".bs-editlocation-modal").modal("show"),document.forms.editlocation.locationid.value=e,document.forms.editlocation.namelocation.value=s}function editlocation0(){var e=document.forms.editlocation.namelocation,s=document.forms.editlocation.locationid;return""==e.value?void alert("Name must be filled out"):($(".loadingicon2").css("visibility","visible"),void $.ajax({url:"include/ops.php",data:{consultid:"editloc",location:e.value,locationid:s.value},type:"post",success:function(e){console.log(e);window.location.href.split("#")[0];setTimeout(function(){location.reload()},1e3)}}))}function addtype(){var e=document.forms.newtype.nametype;return""==e.value?void alert("Name must be filled out"):($(".loadingicon3").css("visibility","visible"),void $.ajax({url:"include/ops.php",data:{consultid:"addtype",type:e.value},type:"post",success:function(e){console.log(e);window.location.href.split("#")[0];setTimeout(function(){location.reload()},1e3)}}))}function deletetype(e,s){var o=confirm("Are you sure you want to delete type: "+s+"?");1==o?($("#deletetype"+e).attr("src","img/loading.gif"),$("#deletetype"+e).attr("width","20"),$.ajax({url:"include/ops.php",data:{consultid:"deletetype",id:e},type:"post",success:function(e){window.location.href.split("#")[0];setTimeout(function(){location.reload()},1e3)}})):console.log("no")}function edittypefunction(e,s){$(".bs-edittype-modal").modal("show"),document.forms.edittype.typeid.value=e,document.forms.edittype.nametype.value=s}function edittype0(e,s){var o=document.forms.edittype.nametype,t=document.forms.edittype.typeid;return""==o.value?void alert("Name must be filled out"):($(".loadingicon3").css("visibility","visible"),void $.ajax({url:"include/ops.php",data:{consultid:"edittype",type:o.value,typeid:t.value},type:"post",success:function(e){console.log(e);window.location.href.split("#")[0];setTimeout(function(){location.reload()},1e3)}}))}function changepar(e){window.location.hash=e}function addserver(){var e=document.forms.newserver.name,s=(document.forms.newserver.ip,document.forms.newserver.mascara,document.forms.newserver.description),o=document.forms.newserver.role,t=document.forms.newserver.poolName,n=document.forms.newserver.typeName,i=document.forms.newserver.locName,a=[];$("#userslist .userdiv > span").each(function(){a.push($(this).text()),usrarrf.push(usrarr[$(this).text()])});var l=[];$(".proclistswe").each(function(){l.push($(this).attr("data-value"))});var r=[],d=0,u=!1;$("input[name=ip]").each(function(){0==d&&""==$(this).val()&&(u=!0),""!=$(this).val()&&(r.push($(this).val()),d++)});var p=[];if(j=0,$("input[name=mascara]").each(function(){0==j&&""==$(this).val()&&(u=!0),""!=$(this).val()&&(p.push($(this).val()),j++)}),d!=j&&(u=!0),u)return void alert("All fields must be filled out");$(".loadingicon4").css("visibility","visible"),$("#addbutton").prop("disabled",!0),$("#cancelbutton").prop("disabled",!0);var v="";v=document.forms.newserver.acc_ll.value,c=document.forms.newserver.usercreator.value,$.ajax({url:"include/ops.php",data:{consultid:"addserver",name:e.value,des:s.value,ip:r,masc:p,rol:o.value,pool:t.value,type:n.value,location:i.value,userss:a,uxsw:usrarrf,user:c,procss:l,addssh:JSON.stringify(usersk),addph:usserph},type:"post",success:function(e){console.log(e);var s=window.location.href.split("#")[0];setTimeout(function(){document.location.href=s},1e3)}})}function editserver(e){$("#editserver"+e).attr("src","img/loading.gif"),$("#editserver"+e).attr("width","20");var s="eserver.php?si="+e;setTimeout(function(){document.location.href=s},100)}function addnewip(){$("#othersip").append('<div style="margin-top:10px"><label style="margin-right:8px">Ip</label><input class="form-control" name="ip" id="ipinput" style="width:30%;display:inline;margin-right:20px" value=""><label style="margin-right:10px">Mascara</label><input class="form-control" name="mascara" id="mascarainput" style="width:30%;display:inline" value=""></div>')}function deleteserver(e,s){var o=confirm("Are you sure you want to delete server: "+s+"?");1==o?($("#deleteserver"+e).attr("src","img/loading.gif"),$("#deleteserver"+e).attr("width","20"),$.ajax({url:"include/ops.php",data:{consultid:"deleteserver",id:e},type:"post",success:function(e){setTimeout(function(){location.reload()},1e3)}})):console.log("no")}function gotoserverv(e,s){var o="vserver.php?sid="+e;setTimeout(function(){document.location.href=o},100)}function orderby(e){var s=document.location.protocol+"//"+document.location.hostname+document.location.pathname,o=s+"?ord="+e;document.location.href=o}var usrarr=[],usrarrf=[],usersk={},usserph={};$(document).ready(function(){$(".nav-tabs > li > a").click(function(e){$(this).parent().addClass("active").siblings().removeClass("active"),"Pool"==$(this).text()?($("#procedurepanel").css("display","none"),$("#categorypanel").css("display","block"),$("#locationpanel").css("display","none"),$("#typepanel").css("display","none"),$("#vpnpanel").css("display","none")):"Location"==$(this).text()?($("#procedurepanel").css("display","none"),$("#categorypanel").css("display","none"),$("#locationpanel").css("display","block"),$("#typepanel").css("display","none"),$("#vpnpanel").css("display","none")):"Type"==$(this).text()?($("#procedurepanel").css("display","none"),$("#categorypanel").css("display","none"),$("#locationpanel").css("display","none"),$("#typepanel").css("display","block"),$("#vpnpanel").css("display","none")):"Networks"==$(this).text()?($("#procedurepanel").css("display","none"),$("#categorypanel").css("display","none"),$("#locationpanel").css("display","none"),$("#typepanel").css("display","none"),$("#vpnpanel").css("display","block")):($("#categorypanel").css("display","none"),$("#procedurepanel").css("display","block"),$("#locationpanel").css("display","none"),$("#typepanel").css("display","none"),$("#vpnpanel").css("display","none"))}),$('#categorypanel .panel-collapse:not(".in")').collapse("show");var e=window.location.hash;"#p"==e?($('.nav a:contains("Pool")').parent().addClass("active"),$("#categorypanel").css("display","block")):"#l"==e?($('.nav a:contains("Location")').parent().addClass("active"),$("#locationpanel").css("display","block")):"#t"==e?($('.nav a:contains("Type")').parent().addClass("active"),$("#typepanel").css("display","block")):($('.nav a:contains("All Servers")').parent().addClass("active"),$("#procedurepanel").css("display","block"));var s={placement:"bottom"};$('tr[id^="nsr"]').popover(s),$('div[id^="poold"]').popover(s),$("form input").keydown(function(e){13==e.keyCode&&e.preventDefault()}),$("#usersname").on("change",function(){"d"==$("#usersname option:first-child").val()&&$("#usersname option:first-child").remove();var e=this.value,s=$("#userslist").html();0==$("#sp"+e).length&&(usrarr[e.value]="",$("#userslist").html(s+"<span id='sp"+e+"'>"+e+"<img style='cursor:pointer;'onclick='removeuser(\""+e+"\")' id='usrdel"+e+"' src='img/closeimg.png'><br></span><button type='button' class='btn btn-success'>SSH KEYS</button>"))}),$("#procnames").on("change",function(){"d"==$("#procnames option:first-child").val()&&$("#procnames option:first-child").remove();var e=this.value,s=$("#procnames option:selected").html(),o=$("#proclist2").html();0==$("#spp"+e).length&&$("#proclist2").html(o+"<div data-value='"+e+"' id='spp"+e+"'><span class='proclistswe' data-value='"+e+"'>"+s+"</span><img style='cursor:pointer;'onclick='removeproc(\""+e+"\")' id='cprocdel"+e+"' src='img/closeimg.png'><br></div>")}),$(".hoverrow").hover(function(){var e=$(this).attr("data-id");$("#editserver"+e).css("visibility","visible"),$("#deleteserver"+e).css("visibility","visible")},function(){var e=$(this).attr("data-id");"img/loading.gif"!=$("#editserver"+e).attr("src")&&$("#editserver"+e).css("visibility","hidden"),"img/loading.gif"!=$("#deleteserver"+e).attr("src")&&$("#deleteserver"+e).css("visibility","hidden")}),$("#sshSel").on("change",function(){var e=$(this).val(),s=$("#currentsshuser").text(),o=usersk[s],t=usserph[s];$("#sshkey").val(o[e]),$("#passp").val(t[e]),""!=e?$("#delsshbutt").css("visibility","visible"):$("#delsshbutt").css("visibility","hidden")})});
    </script>

</body>

</html>
