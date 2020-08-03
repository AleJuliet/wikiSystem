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
    </style>

</head>

<body>

    <?php 
    $page = "serv";
    include('header.php');
    
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

    $section = "";
    if(isset($_GET["s"]))
      $section = $_GET["s"];

    $samenw = array();
    $db = opendb();
    $sid = "";
    if(isset($_GET["si"]))
      $sid = $_GET["si"];
      
    $query = $db->prepare('SELECT * FROM SERVERS as s where s.serverid=:id');
    $query->bindValue(':id', $sid, SQLITE3_TEXT);
    
    $resultx = $query->execute();
    $userinfo = selectUser($_SESSION['login_user']);
    $u_l = $_SESSION['login_user'];
    ?>
    
    <script type="text/javascript">
      var hash = window.location.hash;
      var usersk = {};
      var usserph = {};
    </script>
    
    <!-- Page Content -->
    <div class="container">
        <!-- Search Content -->
	<div style="float: right;width:300px">
	  <div class="input-group">
	    <input type="text" class="form-control" placeholder="Search for...">
	    <span class="input-group-btn">
	      <button class="btn btn-default" type="button">Go!</button>
	    </span>
	  </div><!-- /input-group -->
	</div><!-- /.row -->
	
	
	<div style="margin-top:50px"></div>
	
	<div id="bodyelements" style="">
	  <ul class="nav nav-tabs ">
	      <li role="presentation"><a class="pointer"  style="font-size:12px;padding: 5px 8px;">All Servers</a></li>
	      <li role="presentation"><a class="pointer" style="font-size:12px;padding: 5px 8px;">Pool</a></li>
	      <li role="presentation"><a class="pointer" style="font-size:12px;padding: 5px 8px;">Location</a></li>
	      <li role="presentation"><a class="pointer"  style="font-size:12px;padding: 5px 8px;">Type</a></li>
	      <li role="presentation"><a class="pointer"  style="font-size:12px;padding: 5px 8px;">Networks</a></li>
	  </ul>
	  
	  <div id="procedurepanel" style="vertical-align: top;margin-top:25px;margin-left:5px">
	      <div class="panel panel-default" style="min-height:600px;">
		  <div class="panel-heading" style="padding: 0px 5px;text-align:right">
		    <button type="button" onclick="addserverf()" id="newserb" style="    visibility: hidden;" class="btn btn-default navbar-btn"><img src="img/add.png" width="15px" 
		    style="margin-bottom:2px">
			<label style="font-weight:400;margin-bottom: 1px;cursor:pointer " class="newproc">Edit Server</label></button>
		  </div>
		  		  
		  <div id="newserver" style="display:block;margin: 30px;">
		    <form role="form" method="post" name="newserver" id="newserver">
		    <?php 
		    while ($resx = $resultx->fetchArray(SQLITE3_ASSOC)) {
		    $servern = decryptdescription($resx["servername"],$_SESSION['acc_ll'],$userinfo);
		    $serverd = decryptdescription($resx["serverdes"],$_SESSION['acc_ll'],$userinfo);
		    ?>
		      <div class="form-group" >		  
			  <label>Name</label>
			  <input class="form-control" name="name" id="nameinput" style="width:40%" value="<?php echo $servern?>">
			  <p class="help-block"></p>
		      </div>    
		      <div class="form-group" style="margin-top:20px;margin-bottom:20px" >	
			  <?php 
			  $new = $db->prepare('SELECT * FROM IPSERVER WHERE serverid=:id');
			  $new->bindValue(':id', $sid, SQLITE3_TEXT);
			  if (!($resultx2 = $new->execute())) {
			      continue;
			  } 
			  $h = 0;
			  while ($resx2 = $resultx2->fetchArray(SQLITE3_ASSOC)) {
			  $ipd = decryptdescription($resx2["ipserver"],$_SESSION['acc_ll'],$userinfo);
			  $mascd = decryptdescription($resx2["mascara"],$_SESSION['acc_ll'],$userinfo);
			  ?>
			   <?php 
			   if ($h==0)
			   {
			   ?>
			    <label style="margin-right:5px">Ip</label>
			    <input class="form-control" name="ip" id="ipinput" style="width:30%;display:inline;margin-right:15px" value="<?php echo $ipd?>">
			    <label style="margin-right:5px">Mascara</label>
			    <input class="form-control" name="mascara" id="mascarainput" style="width:30%;display:inline" value="<?php echo $mascd?>">
			    <img style='cursor:pointer;margin-left:10px;margin-bottom:5px'
			      onclick='addnewip()' width="25px" id='newipadd' src='img/add.png'>
			    <p class="help-block"></p>
			    <div id="othersip">
			   <?php 
			   }
			   else 
			   {
			   ?>
			    <div style="margin-top:5px"><label style="margin-right:5px">Ip</label>
			    <input class="form-control" name="ip" id="ipinput" style="width:30%;display:inline;margin-right:15px" value="<?php echo $ipd?>">
			    <label style="margin-right:5px">Mascara</label>
			    <input class="form-control" name="mascara" id="mascarainput" style="width:30%;display:inline" value="<?php echo $mascd?>"></div>
			  <?php
			   }
			   $h++;
			  }
			  if ($h>0)
			    echo "</div>";
			  else 
			  {
			  ?>
			  <label style="margin-right:5px">Ip</label>
			  <input class="form-control" name="ip" id="ipinput" style="width:30%;display:inline;margin-right:15px">
			  <label style="margin-right:5px">Mascara</label>
			  <input class="form-control" name="mascara" id="mascarainput" style="width:30%;display:inline">
			  <img style='cursor:pointer;margin-left:10px;margin-bottom:5px'
			    onclick='addnewip()' width="25px" id='newipadd' src='img/add.png'>
			  <p class="help-block"></p>
			  <div id="othersip">
			  
			  </div>
			  <?php 
			  }
			  ?>
			  
		      </div>
		      <div class="form-group">
			  <label>Description</label>
			  <textarea class="form-control" rows="8" name="description" id="description"><?php echo $serverd?></textarea>
		      </div>		      
		      <div class="form-group" >		  
			  <label>Role</label>
			  <input class="form-control" name="role" id="roleinput" style="width:40%" value="<?php echo $resx["role"]?>">
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
			  <?php		    
			  $ret = $db->prepare('SELECT * FROM USERSERVER where serverid=:id');
			  $ret->bindValue(':id', $sid, SQLITE3_TEXT);
			  if (!($results = $ret->execute())) {
			      continue;
			  } 
			  $i = 0;
			  $ssharray = array();
			  $pasarray = array();
			  while ($cat = $results->fetchArray(SQLITE3_ASSOC)) {
			    $usern = decryptdescription($cat["serveruser"],$_SESSION['acc_ll'],$userinfo);
			    
			    //SSh pars
			    $ssha = $db->prepare('SELECT * FROM SSHPAS WHERE serverid=:id and userid=:userid');
			    $ssha->bindValue(':id',$sid, SQLITE3_TEXT);
			    $ssha->bindValue(':userid',$cat["serveruser"], SQLITE3_TEXT);
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
			  <div class='userdiv' id='sp<?php echo $cat["serveruser"]?>' style='margin-bottom:5px'><span><?php echo $usern?></span>
			  <img style='cursor:pointer;'
			    onclick='removeuser("<?php echo $cat["serveruser"]?>",<?php echo $cat["serverid"]?>)' id='usrdel<?php echo $cat["serveruser"]?>' src='img/closeimg.png'>
			   <button style='padding: 3px;' type='button' onclick='openmodalssh("<?php echo $usern?>")' class='btn btn-success'>SSH KEYS</button>
			   </div>
			  <?php
			  }
			  ?>			  
			  </div>
			  <script type="text/javascript">
			    usersk = <?php echo json_encode($ssharray); ?>;
			    usserph = <?php echo json_encode($pasarray); ?>;
			    if (usersk.length==0)
			    {
			      usersk = {};
			      usserph = {};
			    }
			  </script>
			  <input class="form-control" name="usernameinput" id="usernameinput" style="width:20%;display: inline;">
			  <input type="hidden" id="usercreator" name="usercreator" value="<?php echo $u_l?>">
			  <input style="display:none" type="password" name="fakepasswordremembered"/>
                          Password:<input class="form-control" type="password" id="psw" name="psw" autocomplete="off" style="margin-left:10px;width:20%;display: inline;">
			  <button type="reset" id="cancelbutton" class="btn btn-default" onclick="adduser()">Add</button>
			  <p class="help-block"></p>			  
		      </div>  
		      
		      <div class="form-group" >		  
			  <label>Procedures:</label></p>
			  <span id="proclist2">
			  <?php		    
			  $ret = $db->prepare('SELECT * FROM PROCSERVER as prs,PROCEDURES pr where serverid=:id and pr.procid=prs.procid');
			  $ret->bindValue(':id', $sid, SQLITE3_TEXT);
			  if (!($results = $ret->execute())) {
			      continue;
			  } 
			  $i = 0;
			  while ($cat = $results->fetchArray(SQLITE3_ASSOC)) {
			  ?>
			    <div id='spp<?php echo $cat["procid"]?>'><span class='proclistswe' data-value='<?php echo $cat["procid"]?>'>
			    <?php echo $cat["procname"]?></span>
			    <img style='cursor:pointer;' 
			    onclick='removeproc(<?php echo $cat["procid"]?>)' id='cprocdel<?php echo $cat["procid"]?>' src='img/closeimg.png'><br>
			    </div>
			  <?php
			  }
			  ?>
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
		    <?php } ?>
		    <button type="button " id="addbutton" class="btn btn-success" onclick="editserver()">Edit Server</button>
                    <button type="reset" id="cancelbutton" class="btn btn-default" onclick="cancelserver()">Cancel</button>
		  </div>
		  
	      </div>
	  </div>
	  
        </div><!-- body elements -->
    
    </div>
    
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
    
    <script type="text/javascript">
    function removeItem(e,s){e.hasOwnProperty(s)&&(!isNaN(parseInt(s))&&e instanceof Array?e.splice(s,1):delete e[s])}function editserver(){var e=document.forms.newserver.name,s=(document.forms.newserver.ip,document.forms.newserver.mascara,document.forms.newserver.description),r=document.forms.newserver.role,t=document.forms.newserver.poolName,a=document.forms.newserver.typeName,n=document.forms.newserver.locName,o=getQueryVariable("si"),i=[];$("#userslist .userdiv > span").each(function(){i.push($(this).text()),usrarrf.push(usrarr[$(this).text()])});var l=[];$(".proclistswe").each(function(){l.push($(this).attr("data-value"))});var u=[],p=0,v=!1;$("input[name=ip]").each(function(){0==p&&""==$(this).val()&&(v=!0),""!=$(this).val()&&(u.push($(this).val()),p++)});var d=[];return j=0,$("input[name=mascara]").each(function(){0==j&&""==$(this).val()&&(v=!0),""!=$(this).val()&&(d.push($(this).val()),j++)}),p!=j&&(v=!0),v||""==e.value||""==s.value||""==r.value||""==t.value||""==a.value||""==n.value?void alert("All fields must be filled out"):($(".loadingicon4").css("visibility","visible"),$("#addbutton").prop("disabled",!0),$("#cancelbutton").prop("disabled",!0),c=document.forms.newserver.usercreator.value,void $.ajax({url:"include/ops.php",data:{consultid:"editserver",name:e.value,des:s.value,ip:u,masc:d,rol:r.value,pool:t.value,type:a.value,location:n.value,usersarray:i,serverid:o,uxsw:usrarrf,user:c,procss:l,addssh:JSON.stringify(usersk),addph:usserph},type:"post",success:function(e){console.log(e);window.location.href.split("#")[0];setTimeout(function(){document.location.href="server.php"},1e3)}}))}function getQueryVariable(e){for(var s=window.location.search.substring(1),r=s.split("&"),t=0;t<r.length;t++){var a=r[t].split("=");if(decodeURIComponent(a[0])==e)return decodeURIComponent(a[1])}}function removeproc(e){$("#spp"+e).remove(),$("#procdel"+e).remove()}function adduser(){var e=document.forms.newserver.usernameinput,s=document.forms.newserver.psw,r=$("#userslist").html();return null!=s&&""==s.value?void alert("Password is missing"):void(0==$("#sp"+e.value).length&&(usrarr[e.value]=s.value,$("#userslist").html(r+"<div class='userdiv' style='margin-bottom:5px' id='sp"+e.value+"'><span>"+e.value+"</span><img style='cursor:pointer;'onclick='removeuser(\""+e.value+"\")' id='usrdel"+e.value+"' src='img/closeimg.png'><button style='padding: 3px;' type='button' onclick='openmodalssh(\""+e.value+"\")' class='btn btn-success'>SSH KEYS</button></div>"),usersk[e.value]=[],usserph[e.value]=[]))}function openmodalssh(e){var s=usersk[e];usserph[e];$("#sshSel").empty();var r=0,t=0;$("#sshSel").append($("<option></option>").attr("value","").text(""));for(var a in s)0==r&&(t=a),$("#sshSel").append($("<option></option>").attr("value",a).text("SSh "+a)),r++;$("#currentsshuser").text(e),$("#sshkey").val(""),$("#newsshpasslabel").text("SSH Keys and Passphrase for "+e),$("#passp").val(""),$(".bs-newsshpass-modal").modal("show")}function deletekey(){var e=$("#currentsshuser").text(),s=usersk[e],r=usserph[e],t=$("#sshSel").find(":selected").val();t>-1&&(s.splice(t,1),r.splice(t,1)),$(".bs-newsshpass-modal").modal("hide")}function addsshuser(){var e=$("#currentsshuser").text(),s=document.forms.sshkeyuser.sshkey,r=document.forms.sshkeyuser.passp,t=usersk[e],a=$("#sshSel").find(":selected").val();if(""==a)var a=t.length;t[a]=s.value;var n=usserph[e];n[a]=r.value,usersk[e]=t,usserph[e]=n,$(".bs-newsshpass-modal").modal("hide")}function removeuser(e){$("#sp"+e).remove(),$("#usrdel"+e).remove(),$("#sshbutton"+e).remove(),removeItem(usrarr,e),removeItem(usersk,e),removeItem(usserph,e)}function cancelserver(){setTimeout(function(){document.location.href="server.php"},1e3)}function addnewip(){$("#othersip").append('<div style="margin-top:10px"><label style="margin-right:8px">Ip</label><input class="form-control" name="ip" id="ipinput" style="width:30%;display:inline;margin-right:20px" value=""><label style="margin-right:10px">Mascara</label><input class="form-control" name="mascara" id="mascarainput" style="width:30%;display:inline" value=""></div>')}var usrarr=[],usrarrf=[];$(document).ready(function(){$(".nav-tabs > li > a").click(function(e){if("All Servers"==$(this).text()){var s="server.php";setTimeout(function(){document.location.href=s},100)}else if("Pool"==$(this).text()){var s="server.php#p";setTimeout(function(){document.location.href=s},100)}else if("Location"==$(this).text()){var s="server.php#l";setTimeout(function(){document.location.href=s},100)}else if("Type"==$(this).text()){var s="server.php#t";setTimeout(function(){document.location.href=s},100)}else{var s="server.php#v";setTimeout(function(){document.location.href=s},100)}}),$("#usersname").on("change",function(){"d"==$("#usersname option:first-child").val()&&$("#usersname option:first-child").remove();var e=this.value,s=$("#userslist").html();0==$("#sp"+e).length&&(usrarr[e.value]="",$("#userslist").html(s+"<span id='sp"+e+"'>"+e+"<img style='cursor:pointer;'onclick='removeuser(\""+e+"\")' id='usrdel"+e+"' src='img/closeimg.png'><br></span>"))}),$("#procnames").on("change",function(){"d"==$("#procnames option:first-child").val()&&$("#procnames option:first-child").remove();var e=this.value,s=$("#procnames option:selected").html(),r=$("#proclist2").html();0==$("#spp"+e).length&&$("#proclist2").html(r+"<div data-value='"+e+"' id='spp"+e+"'><span class='proclistswe' data-value='"+e+"'>"+s+"</span><img style='cursor:pointer;'onclick='removeproc(\""+e+"\")' id='cprocdel"+e+"' src='img/closeimg.png'><br></div>")}),$("#sshSel").on("change",function(){var e=$(this).val(),s=$("#currentsshuser").text(),r=usersk[s],t=usserph[s];$("#sshkey").val(r[e]),$("#passp").val(t[e]),""!=e?$("#delsshbutt").css("visibility","visible"):$("#delsshbutt").css("visibility","hidden")})});
    </script>

</body>

</html>
