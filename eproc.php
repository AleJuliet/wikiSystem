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
<body>

    <?php 
    $page = "proc";
    include('header.php');
    
    $db = opendb();
    $pid = "";
    if(isset($_GET["pi"]))
      $pid = $_GET["pi"];
      
    $query = $db->prepare('SELECT * FROM PROCEDURES as p where p.procid=:id');
    $query->bindValue(':id', $pid, SQLITE3_TEXT);
    
    $resultx = $query->execute();
    ?>
    
    <!-- Page Content -->
    <div class="container">
        <!-- Search Content -->
	<?php include('searchbox.php'); ?>
	
	
	<div style="margin-top:50px"></div>
	
	<div id="bodyelements" style="">
	  <ul id="prmenu" class="nav nav-tabs ">
	      <li role="presentation"><a class="pointer" style="font-size:12px;padding: 5px 8px;">All Procedures</a></li>
	      <li role="presentation"><a class="pointer" style="font-size:12px;padding: 5px 8px;">Categories</a></li>
	  </ul>
	  
	  <div id="procedurepanel" style="vertical-align: top;margin-top:25px;margin-left:5px">
	      <div class="panel panel-default" style="min-height:600px;">
		  <div class="panel-heading" style="padding: 0px 5px;text-align:right">
		    <button style="visibility:hidden" ="button" id="newprocb" onclick="addproc()" class="btn btn-default navbar-btn"><img src="img/add.png" width="15px" style="margin-bottom:2px">
			<label style="font-weight:400;margin-bottom: 1px;cursor:pointer" class="newproc">Add Procedure</label></button>
		    <div class="btn-group" style="visibility:hidden" id="orderbyp">
		      <button type="button" class="btn btn-danger">Order by</button>
		      <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		      </button>
		      <ul class="dropdown-menu">
		      </ul>
		    </div>
		  </div>
		 
		  
		  <div id="newproc" style="margin: 30px;">
		    <form role="form" method="post" name="uprocedure" id="uprocedure">
		    <?php
		    while ($resx = $resultx->fetchArray(SQLITE3_ASSOC)) {
		    $userinfo = selectUser($_SESSION['login_user']);
		    $des = decryptdescription($resx["procdescription"],$_SESSION['acc_ll'],$userinfo);
		    ?>
		    <div class="form-group" >
		    
			  <label>Name</label>
			  <input class="form-control" name="name" id="nameinput" value="<?php echo $resx["procname"]?>">
			  <p class="help-block"></p>
		    </div>                                        
		      <div class="form-group">
			  <label>Description</label>	
			  <div id="otabs">
			  <ul  class="nav nav-tabs">
			    <li id="tab1" class="<?php if($resx["parsed"]=="0") echo 'active'?>"><a class="pointer" onclick="changeeditor(1)">Editor</a></li>
			    <li id="tab2" class="<?php if($resx["parsed"]=="1") echo 'active'?>"><a class="pointer" onclick="changeeditor(2)">MediaWiki</a></li>
			  </ul>
			  </div>
			  <textarea class="form-control" rows="8" name="description2" id="description2"
			  style="<?php if($resx["parsed"]=="0") echo 'display:none'?>;height:300px"><?php if($resx["parsed"]=="1") echo $des?></textarea>
			  <div id="ed2"  style="<?php if($resx["parsed"]=="1") echo 'display:none'?>;">
			  <?php
			    if ( !function_exists('version_compare') || version_compare( phpversion(), '5', '<' ) )
			    {
				include('include/ckeditor/ckeditor_php4.php' ) ;}
			    else
			    {
				include('include/ckeditor/ckeditor_php5.php' ) ;}
			    $CKEditor = new CKEditor();
			    $CKEditor->basePath = 'include/ckeditor/';
			    $CKEditor->textareaAttributes = array("cols" => 80, "rows" => 10);
			    $config['resize_enabled'] = false;
			    $config['toolbarCanCollapse'] = false;
			    $config['language']='en';		
			    $config['height']='300';
			    $config['toolbar'] = array(
				  array( 'Source', '-','Undo','Redo','-','Bold', 'Italic', 'Underline', 'Strike' ),
			    array('JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Outdent','Indent','-','NumberedList','BulletedList','Blockquote'),
			    array('Subscript','Superscript','Table','HorizontalRule','SpecialChar'),
			    array('Link', 'Unlink'),
			    array('Format')
			    );
			    
// 			    if($resx["parsed"]=="0")
// 			      $config['startupMode']='source';

			    $initialValue="";
			    if ($des!="" && $resx["parsed"]=="0") $initialValue=$des;
			    $code = $CKEditor->editor("description", $initialValue, $config);
			  ?>
			  </div>
		      </div>
		      <!--Categories-->
		      <div class="form-group" id="modulesnewtc">
		      <label>Category:</label>
		      <select class="selectform" id="categoryName" name="categoryName">
		      <?php		    
		      $ret = $db->query('SELECT * FROM CATEGORIES');
		      
		      $i = 0;
		      while ($cat = $ret->fetchArray(SQLITE3_ASSOC)) {
		      ?>
			<option value="<?php echo $cat["catid"]?>" <?php if ($resx['categoryid']==$cat["catid"]) echo "selected"?>><?php echo $cat["catname"]?></option>
		      <?php
		      }
		      ?>
		      </select>
		      <input type="hidden" id="usercreator" name="usercreator" value="<?php echo $_SESSION['login_user']?>">
		      <input type="hidden" id="procid" name="procid" value="<?php echo $resx["procid"]?>">
		      </div>
		      <img class="loadingicon2" src="img/loading.gif" style="visibility: hidden;    margin: 10px;" width="20">
		    <?php 
		    }
		    ?>
		    </form>
		    
		    <button type="button " id="addbutton" class="btn btn-success" onclick="updateproc()">Update</button>
                    <button type="reset" id="cancelbutton" class="btn btn-default" onclick="cancelproc()">Cancel</button>
		  </div>
		
	      </div>
	  </div>

	  
	  
        </div><!-- body elements -->
    </div>

    
    <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/aux.js"></script>
    
    <script type="text/javascript">
    $(document).ready(function(){$("#prmenu > li > a").click(function(a){var b="Categories"==$(this).text()?"procs.php?s=c":"procs.php";setTimeout(function(){document.location.href=b},100)})});
function updateproc(){var a=document.forms.uprocedure.name,b=CKEDITOR.instances.description.getData(),c=0;"MediaWiki"==$("#newproc .nav-tabs li.active").text()&&(c=1,des0=document.forms.uprocedure.description2,b=des0.value);var d=document.forms.uprocedure.categoryName,e=document.forms.uprocedure.usercreator,f=document.forms.uprocedure.procid;null==a||null==b||null==d?alert("Name must be filled out"):($(".loadingicon2").css("visibility","visible"),$("#addbutton").prop("disabled",!0),$("#cancelbutton").prop("disabled",
!0),$.ajax({url:"include/ops.php",data:{consultid:"uprocedure",name:a.value,des:b,cat:d.value,user:e.value,pars:c,id:f.value},type:"post",success:function(a){console.log(a);var b="procs.php";s=getQueryVariable("s");pi=getQueryVariable("pi");"2"==s&&(b="proci.php?pi="+pi);setTimeout(function(){/*document.location.href=b*/},100)}}))}
function cancelproc(){var a="procs.php";s=getQueryVariable("s");pi=getQueryVariable("pi");"2"==s&&(a="proci.php?pi="+pi);setTimeout(function(){document.location.href=a},100)}
function changeeditor(a){1==a?($("#tab2").removeClass(),$("#tab1").removeClass(),$("#tab1").addClass("active"),$("#ed2").css("display","block"),$("#description2").css("display","none")):($("#tab1").removeClass(),$("#tab2").removeClass(),$("#tab2").addClass("active"),$("#description2").css("display","block"),$("#ed2").css("display","none"))}
function getQueryVariable(a){for(var b=window.location.search.substring(1).split("&"),c=0;c<b.length;c++){var d=b[c].split("=");if(decodeURIComponent(d[0])==a)return decodeURIComponent(d[1])}};
    </script>

</body>

</html>
