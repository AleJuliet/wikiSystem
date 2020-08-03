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
    $page = "serv";
    include('header.php');

    $sid = "";
    if(isset($_GET["sid"]))
      $sid = $_GET["sid"];
      
    //Pools 
    $db = opendb();   
    $ret = $db->query('SELECT * FROM POOL');
    
    $pool = array();
    while ($res = $ret->fetchArray(SQLITE3_ASSOC)) {
      $pool[$res["poolid"]] = $res["poolname"];
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
      
    $db = opendb();
    $query = $db->prepare('SELECT * FROM SERVERS as s where s.serverid=:id');
    $query->bindValue(':id', $sid, SQLITE3_TEXT);
    
    $resultx = $query->execute();
    $userinfo = selectUser($_SESSION['login_user']);
    ?>
    <script type="text/javascript">
      var usersk = {};
      var usserph = {};
    </script>
    
    <!-- Page Content -->
    <div class="container">
        <!-- Search Content -->
	<?php include('searchbox.php'); ?>
	
	
	<div style="margin-top:50px"></div>
	
	<div id="bodyelements" style="">
	  <ul class="nav nav-tabs ">
	      <li role="presentation"><a class="pointer"  style="font-size:12px;padding: 5px 8px;">All Servers</a></li>
	      <li role="presentation"><a class="pointer" style="font-size:12px;padding: 5px 8px;">Pool</a></li>
	      <li role="presentation"><a class="pointer" style="font-size:12px;padding: 5px 8px;">Location</a></li>
	      <li role="presentation"><a class="pointer"  style="font-size:12px;padding: 5px 8px;">Type</a></li>
	      <li role="presentation"><a class="pointer"  style="font-size:12px;padding: 5px 8px;">Networks</a></li>
	  </ul>
	  
	  <div id="procedurepanel" style="vertical-align: top;margin-top:25px;margin-left:5px;margin-bottom: 0px;">
	      <div class="panel panel-default" style="min-height:600px;box-shadow: 4px 4px 5px #b7b7b7;">
	          <div style="margin:20px">
	          <?php
	          while ($resx = $resultx->fetchArray(SQLITE3_ASSOC)) {
	          $poold = decryptdescription($pool[$resx["spoolid"]],$_SESSION['acc_ll'],$userinfo);
	          $servern = decryptdescription($resx["servername"],$_SESSION['acc_ll'],$userinfo);
		  $serverd = decryptdescription($resx["serverdes"],$_SESSION['acc_ll'],$userinfo);
	          ?>
		    <div>
		    <h2><?=$servern?></h2>
		    </div>
		    <div>
		    <label>Description:</label><span style="margin-left:10px"><?=nl2br($serverd)?></span>
		    </div>
		    <div>
		    <label>Role:</label><span style="margin-left:10px"><?=$resx["role"]?></span>
		    </div>
		    <div>
		    <label>Pool:</label><span style="margin-left:10px"><?=$poold?></span>
		    </div>
		    <div>
		    <label>Type:</label><span style="margin-left:10px"><?=$type[$resx["stypeid"]]?></span>
		    </div>
		    <div>
		    <label>Location:</label><span style="margin-left:10px"><?=$location[$resx["slocationid"]]?></span>
		    </div>
		    <div>
		    <label>Date:</label><span style="margin-left:10px"><?=$resx["creationdates"]?></span>
		    </div>
		    <div>
		    <label>Ip/Mascara:</label>
		    <?php 
		    $new = $db->prepare('SELECT * FROM IPSERVER WHERE serverid=:id');
		    $new->bindValue(':id',$sid, SQLITE3_TEXT);
		    if (!($result = $new->execute())) {
			continue;
		    } 
		    $i=0;
		    while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
		    $ipd = decryptdescription($res["ipserver"],$_SESSION['acc_ll'],$userinfo);
		    $mascd = decryptdescription($res["mascara"],$_SESSION['acc_ll'],$userinfo);
		      if ($i>0)
			echo ", ";
		    ?>
		      <?php echo $ipd."/".$mascd?>
		    <?php 
		      $i++;
		    }
		    ?>
		    </div>
		    <div>
		    <label>Users:</label>
		    <table class="table table-bordered" style="width: 500px;">
		      <thead>
			<tr>
			  <th>Name</th>
			  <th>Password</th>
			</tr>
		      </thead>
		      <tbody>
		    <?php 
		    $new = $db->prepare('SELECT * FROM USERSERVER WHERE serverid=:id');
		    $new->bindValue(':id',$sid, SQLITE3_TEXT);
		    if (!($result = $new->execute())) {
			continue;
		    } 
		    $ssharray = array();
		    $pasarray = array();
		    while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
		      $usern = decryptdescription($res["serveruser"],$_SESSION['acc_ll'],$userinfo);
		      $userp = decryptdescription($res["passw"],$_SESSION['acc_ll'],$userinfo);
		      
		      //SSh pars
		      $ssha = $db->prepare('SELECT * FROM SSHPAS WHERE serverid=:id and userid=:userid');
		      $ssha->bindValue(':id',$sid, SQLITE3_TEXT);
		      $ssha->bindValue(':userid',$res["serveruser"], SQLITE3_TEXT);
		      if (!($resultssh = $ssha->execute())) {
			  continue;
		      } 
		      $sshs = array();
		      $pars = array();
		      while ($ressh = $resultssh->fetchArray(SQLITE3_ASSOC)) {	
			$sshs[] = decryptdescription($ressh["sshkey"],$_SESSION['acc_ll'],$userinfo);
			$pars[] = decryptdescription($ressh["passphrase"],$_SESSION['acc_ll'],$userinfo);
		      }
		      $ssharray[$usern] = $sshs;
		      $pasarray[$usern] = $pars;
		    ?>		    
			<tr>
			  <td><a style="cursor:pointer" onclick="openmodalssh('<?=$usern?>')"><?php echo $usern?></a></td>
			  <td><?php echo $userp?></td>
			</tr>			      		      
		    <?php 
		    }
		    ?>
		    <script type="text/javascript">
		      usersk = <?php echo json_encode($ssharray); ?>;
		      usserph = <?php echo json_encode($pasarray); ?>;
		    </script>
		      </tbody>
		    </table>
		    </div>
		    <div>
		    <label>Procedures:</label>
		    <?php 
		    $new = $db->prepare('SELECT * FROM PROCSERVER as prs,PROCEDURES pr where serverid=:id and pr.procid=prs.procid');
		    $new->bindValue(':id',$sid, SQLITE3_TEXT);
		    if (!($result = $new->execute())) {
			continue;
		    } 
		    $i=0;
		    while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
		      if ($i==0)
			echo "<br>";
		    ?>
		      <a href="proci.php?pi=<?=$res["procid"]?>"><?php echo $res["procname"]?></a><br>
		    <?php 
		      $i++;
		    }
		    ?>
		    </div>
	          <?php
		  }
		  $db->close();
		  ?>
	         <div>
	      </div>
	  </div>

	  
	  
        </div><!-- body elements -->
    
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
    $( document ).ready(function() {
      $('.nav-tabs > li > a').click(function(event){
	$(this).parent().addClass('active').siblings().removeClass('active');  
	if($(this).text()=="Pool")
	{
	  var urlf = "server.php#p";
	  setTimeout(function(){document.location.href=urlf},100); 
	}
	else if($(this).text()=="Location")
	{
	  var urlf = "server.php#l";
	  setTimeout(function(){document.location.href=urlf},100); 
	}
	else if($(this).text()=="Type")
	{
	  var urlf = "server.php#t";
	  setTimeout(function(){document.location.href=urlf},100); 
	}
	else if($(this).text()=="Networks")
	{
	  var urlf = "server.php#v";
	  setTimeout(function(){document.location.href=urlf},100); 	
	}
	else
	{
	  var urlf = "server.php";
	  setTimeout(function(){document.location.href=urlf},100); 
	}
      });
      $("#sshSel").on("change", function()
      {
	var key = $(this).val();
	var user = $("#currentsshuser").text();
	var sshs = usersk[user];
	var passa = usserph[user];
	$("#sshkey").val(sshs[key]);
	$("#passp").val(passa[key]);	
      });
    });  
    function openmodalssh(user)
    {
      var sshs = usersk[user];
      var passa = usserph[user];
      var i=0;
      $("#sshSel").empty();
      for (var key in sshs) 
      {
        if (i==0)
          firstval = key;
	$("#sshSel")
         .append($("<option></option>")
         .attr("value",key)
         .text("Ssh key "+key));
        i++;
      }
      if (i>0)
      {           
	$("#sshkey").val(sshs[0]);
	$("#passp").val(passa[0]);
      }
      else 
      {
        $("#sshkey").val(sshs[0]);
	$("#passp").val(passa[0]);
      }
      $("#sshkey").val(sshs[0]);
	$("#passp").val(passa[0]);
      $("#currentsshuser").text(user);
      $("#newsshpasslabel").text("SSH Keys and Passphrase for "+user);
      $(".bs-newsshpass-modal").modal("show"); 
    }
    </script>

</body>

</html>
