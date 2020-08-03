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
    
    $section = "";
    if(isset($_GET["s"]))
      $section = $_GET["s"];
    
    //Order by option
    $orderbytc = "";
    $orderid = "";
    $ordername = "Date";
    if(isset($_GET["ord"]))
      $orderid = $_GET["ord"];
    if ($orderid==1)
    {
      $orderbytc = " order by procname";
      $ordername = "Name";
    }
    else if ($orderid==2)
    {
      $orderbytc = " order by catname";
      $ordername = "Category";
    }
    else if ($orderid==3)
      $orderbytc = " order by creationdatep";
    else if ($orderid==4)
    {
      $orderbytc = " order by usercreator";
      $ordername = "User";
    }
    
    $acc_ll = 0;
    $u_l = $_SESSION['login_user'];
    if(isset($_SESSION['acc_ll']))
      $acc_ll = 1;
    ?>
    
    <!-- Page Content -->
    <div class="container">
        <!-- Search Content -->
        <?php include('searchbox.php'); ?>
	
	
	<div style="margin-top:50px"></div>
	
	<div id="bodyelements" style="">
	  <ul id="prmenu" class="nav nav-tabs ">
	      <li role="presentation"><a class="pointer" onclick="changepar('')" style="font-size:12px;padding: 5px 8px;">All Procedures</a></li>
	      <li role="presentation"><a class="pointer" onclick="changepar('c')" style="font-size:12px;padding: 5px 8px;">Categories</a></li>
	  </ul>
	  
	  <div id="procedurepanel" style="<?php if($section=="c") echo "display:none;"?>vertical-align: top;margin-top:25px;margin-left:5px">
	      <div class="panel panel-default" style="min-height:600px;">
		  <div class="panel-heading" style="padding: 0px 5px;text-align:right">
		    <button type="button" id="newprocb" onclick="addproc('')" class="btn btn-default navbar-btn"><img src="img/add.png" width="15px" style="margin-bottom:2px">
			<label style="font-weight:400;margin-bottom: 1px;cursor:pointer" class="newproc">Add Procedure</label></button>
		    <div class="btn-group" id="orderbyp">
		      <button id="orderbybutton" type="button" class="btn btn-danger">Order by<?php echo " (".$ordername.")"?></button>
		      <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		      </button>
		      <ul class="dropdown-menu">
			<li><a style="cursor:pointer" onclick="orderby(1)">Name</a></li>
			<li><a style="cursor:pointer" onclick="orderby(2)">Category</a></li>
			<li><a style="cursor:pointer" onclick="orderby(3)">Date</a></li>
			<li><a style="cursor:pointer" onclick="orderby(4)">User</a></li>
		      </ul>
		    </div>
		  </div>
		  <table class="table " id="listprocs" >
		    <tbody>
		     <form role="form" method="post" name="oneprocedure" id="oneprocedure">
		      <tr>
			<td style="padding-top:5px; "></td>
			<td class="processname" style="padding-top:5px;width:300px;"></td>
			<td style="padding-top:5px;font-size:14px;"></td>
		      </tr>
		      <?php 
		      $db = opendb();
		      $query = $db->query('SELECT *,date(creationdatep) as cdate FROM PROCEDURES as p, CATEGORIES as c where p.categoryid=catid '.$orderbytc);
		      
		      while ($procs = $query->fetchArray(SQLITE3_ASSOC)) {
		      ?>
		       <tr class="hoverrow" data-id="<?php echo $procs["procid"]?>" >
			<td style="width: 20px;"><img src="img/bullet2.gif" height="6px"></td>
			<td class="processname" style="width: 500px;" onclick="gotoproc(<?php echo $procs["procid"]?>,'')"><?php echo $procs["procname"]?></td>
			<?php if($procs["usermod"]!="")
			{
			?>
			<td style="font-size:12px;padding-top: 3px;width: 300px;" onclick="gotoproc(<?php echo $procs["procid"]?>,'')"><span style="font-style: italic;">Modified by</span>
			    <?php echo $procs["usermod"]?><span style="font-style: italic;"> on</span> <?php echo $procs["moddatep"]?></td>
			<?php } 
			else { ?>
			<td style="font-size:12px;padding-top: 3px;width: 300px;" onclick="gotoproc(<?php echo $procs["procid"]?>,'')"><span style="font-style: italic;">Created by</span>
			    <?php echo $procs["usercreator"]?><span style="font-style: italic;"> on</span> <?php echo $procs["creationdatep"]?></td>
			<?php 
			}
			?>
			<td style="font-size:12px;color:#400000;padding-top: 3px;" onclick="gotoproc(<?php echo $procs["procid"]?>,'')"><?php echo $procs["catname"]?></td>
			<td style="font-size:12px;padding-top: 3px;" onclick="gotoproc(<?php echo $procs["procid"]?>,'')"><?php echo $procs["usercreator"]?></td>			
			<td>
			<img onclick="deleteproc(<?php echo $procs["procid"]?>,'<?php echo $procs["procname"]?>')" id="deleteproc<?php echo $procs["procid"]?>" style="visibility: hidden" src="img/closeimg.png"></td>
		        <td><img onclick="editproc(<?php echo $procs["procid"]?>)" id="editproc<?php echo $procs["procid"]?>" style="visibility: hidden;width:16px" src="img/edit.png">
		        </td>
		       </tr>
		      <?php 	      

		      }
		      ?>
		      <input type="hidden" id="procid" name="procid" value="">
		     </form>
		    </tbody>
		  </table>
		  
		  <div id="newproc" style="display:none;margin: 30px;">
		    <form role="form" method="post" name="newprocedure" id="newprocedure">
		    <div class="form-group" >
		    
			  <label>Name</label>
			  <input class="form-control" name="name" id="nameinput">
			  <p class="help-block"></p>
		    </div>                                        
		      <div class="form-group">
			  <label>Description</label>	
			  <ul class="nav nav-tabs">
			    <li class="active"><a class="pointer" onclick="changeeditor(1)">Editor</a></li>
			    <li><a class="pointer" onclick="changeeditor(2)">MediaWiki</a></li>
			  </ul>
			  <textarea class="form-control" rows="8" name="description2" id="description2" style="display:none;height:500px"></textarea>
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
			    $config['height']='500';
			    $config['toolbar'] = array(
				  array( 'Source', '-','Undo','Redo','-','Bold', 'Italic', 'Underline', 'Strike' ),
			    array('JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Outdent','Indent','-','NumberedList','BulletedList','Blockquote'),
			    array('Subscript','Superscript','Table','HorizontalRule','SpecialChar'),
			    array('Link', 'Unlink'),
			    array('Format')
			    );

			    $initialValue="";
			    $code = $CKEditor->editor("description", $initialValue, $config);
			  ?>
			  
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
			<option value="<?php echo $cat["catid"]?>"><?php echo $cat["catname"]?></option>
		      <?php
		      }
		      ?>
		      </select>
		      <input type="hidden" id="usercreator" name="usercreator" value="<?php echo $u_l?>">
		      <input type="hidden" id="acc_ll" name="acc_ll" value="<?php echo $acc_ll?>">
		      </div>
		      <img class="loadingicon2" src="img/loading.gif" style="visibility: hidden;    margin: 10px;" width="20">
		    </form>
		    
		    <button type="button " id="addbutton" class="btn btn-success" onclick="newproc()">Add Procedure</button>
                    <button type="reset" id="cancelbutton" class="btn btn-default" onclick="cancelproc()">Cancel</button>
		  </div>
		
	      </div>
	  </div>
	  

	  <div id="categorypanel" style="<?php if($section!="c") echo "display:none;"?>vertical-align: top;margin-top:25px;margin-left:5px">
	    <div class="panel panel-default " style="min-height:600px;">
		  <div class="panel-heading" style="padding: 0px 5px;text-align:right">
		    <button data-toggle="modal" data-target=".bs-newcategory-modal" type="button" class="btn btn-default navbar-btn">
		        <img src="img/add.png" width="15px" style="margin-bottom:2px;cursor:pointer">
			<label style="font-weight:400;margin-bottom: 1px;cursor:pointer " class="newproc">Add Category</label></button>
		  </div>
		  <div class="panel-group listcat" role="tablist" aria-multiselectable="true" id="accordion" style="margin: 6px 10px 5px 10px">
		  <?php 
		  $ret = $db->query('SELECT * FROM CATEGORIES where parentCat_id=0');
		  $i = 0;
		  $depth = 0;
		  $directorytree = array();		  
		  while ($cat = $ret->fetchArray(SQLITE3_ASSOC)) {		  
		    $query1 = $db->prepare("SELECT * FROM `Category_Clousure` WHERE parentCategory_id=:id order by depth DESC,date");
		    $query1->bindValue(':id', $cat["catid"], SQLITE3_TEXT);
		    if (!($result1 = $query1->execute())) {
			continue;
		    } 
		    $nm = $result1->fetchArray();
		    $levels = "";
		    $orderby1 = "";
		    $orderby = "";
		    $totalChilds = $nm["depth"];
		    for ($i = 0; $i <= $totalChilds; $i++) 
		    {
		      if($levels=="")
		      {
			$levels = "t".$i.".catid as lev".$i.","."t".$i.".catname as name".$i;
			$leftjoins = "FROM CATEGORIES as t".$i;
			$orderby1 .= " order by lev".$i;
			$orderby = " ,t".$i.".date";
		      }
		      else
		      {
			$levels .= ", t".$i.".catid as lev".$i.","."t".$i.".catname as name".$i;
			$leftjoins .= " LEFT JOIN CATEGORIES AS t".$i." ON t".$i.".parentCat_id = t".($i-1).".catid ";
			$orderby1 .= " , lev".$i;
			$orderby = " ,t".$i.".date";
		      }
		    }
		    //no childs totalchilds=0
		    if ($totalChilds==0)
		    {
		        $query = $db->prepare('SELECT * FROM PROCEDURES where categoryid=:id');
			$query->bindValue(':id', $cat["catid"], SQLITE3_TEXT);/*
			if (!($result = $query->execute())) {
			    continue;
			}  */
			$cat_idP = $cat["catid"];
			$cat_nameP = $cat["catname"];
			$groupid = $cat["catid"];
			$directorytree[$groupid] = $cat_nameP;
			$depth = 0;
			include("procs_childs.php");
			continue;
		    }
		    
		    $queryDirChild = $db->prepare("SELECT ".$levels." ".$leftjoins." where t0.catid=:id");
		    $queryDirChild->bindValue(':id', $cat["catid"], SQLITE3_TEXT);
		    $queryDirChild2 = $db->prepare("SELECT ".$levels." ".$leftjoins." where t0.catid=:id");
		    $queryDirChild2->bindValue(':id', $cat["catid"], SQLITE3_TEXT);
		    if (!($resultTree = $queryDirChild->execute())) {
			continue;
		    }
		    else 
		       $resultTree2 = $queryDirChild2->execute();
		    $firstparent = 0;
		    while ($record2 = $resultTree->fetchArray(SQLITE3_ASSOC)) {	
		      $groupid = "";
		      $childlevel1 = $resultTree2->fetchArray();
		      $start = 0;
		      if ($firstparent>0)
		      {
			  $start = 1;
			  $groupid = $childlevel1["lev0"];
		      }
		      for ($i = $start; $i <= $totalChilds; $i++) 
		      {
			if (!isset($childlevel1["lev".$i]) || $childlevel1["lev".$i]==null)
			  continue;
			$cat_idP = $childlevel1["lev".$i];
			$cat_nameP = $childlevel1["name".$i];
			$depth = $i;
			$groupid .= $cat_idP;
			if (isset($directorytree[$groupid]) && $directorytree[$groupid]!=null)
			  continue;
			$directorytree[$groupid] = $cat_nameP;
			$query = $db->prepare('SELECT * FROM PROCEDURES where categoryid=:id');
			$query->bindValue(':id',$cat_idP, SQLITE3_TEXT);
			include("procs_childs.php");
		      }
		      $firstparent++;
		    }
		  ?>
		  <?php 
		  }
		  $db->close();
		  ?>
		  
		</div><!-- listcat -->
	      </div><!-- panel-default -->
	  </div><!-- categorypanel -->
	  
        </div><!-- body elements -->
    
    </div>
    
    <!-- Creates the bootstrap modal where the image will appear -->
     <div class="modal fade bs-newcategory-modal" id="newcategorym" ="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      <h4 class="modal-title" id="myModalLabel">New Category</h4>
	    </div>
	    <div class="modal-body">
	      <form role="form" method="post" name="newcategory" id="newcategory">
	      <input type="hidden" name="catid" value="">
	      <input type="hidden" name="section" value="<?php echo $section?>">
		<div class="form-group">
		    <label>Name</label>
		    <input class="form-control" name="name">
		</div>
	      <input type="hidden" id="vl" name="vl" value="">
	      <div style="text-align:right"><img class="loadingicon" src="img/loading.gif" style="visibility: hidden" width="20"></div>
	     </div>
	     </form>
	    <div class="modal-footer">
	      <button type="button" class="btn btn-primary" onclick='return addcategory();'>Ok</button>
	      <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	    </div>
	  </div>
	</div>
      </div>
      
      <!-- Creates the bootstrap modal where the image will appear -->
     <div class="modal fade bs-editcategory-modal" id="editcategorym" ="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      <h4 class="modal-title" id="myModalLabel">Edit Category</h4>
	    </div>
	    <div class="modal-body">
	      <form role="form" method="post" name="editcategory" id="editcategory">
	      <input type="hidden" name="catid" value="">
	      <input type="hidden" name="section" value="<?php echo $section?>">
		<div class="form-group">
		    <label>Name</label>
		    <input class="form-control" name="name">
		</div>
	      <input type="hidden" id="vl" name="vl" value="">
	      <div style="text-align:right"><img class="loadingicon" src="img/loading.gif" style="visibility: hidden" width="20"></div>
	     </div>
	     </form>
	    <div class="modal-footer">
	      <button type="button" class="btn btn-primary" onclick='return edditcategory();'>Ok</button>
	      <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	    </div>
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
                                    <form role="form" method="post" name="acc_us" id="acc_us">
                                <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Please write your password</label>
                                            <input style="display:none" type="password" name="fakepasswordremembered"/>
                                            <input class="form-control" type="password" name="psw" autocomplete="off">
                                        </div>
                                        
                                        <button type="button" class="btn btn-default" onclick="javascript: newproc2();">Create Procedure</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick='return cancelproc2();'>Close</button>
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
    <script src="js/aux.js"></script>
    
    <script type="text/javascript">
    $(document).ready(function(){$("#proccat a").click(function(a){$("#proccat .dropdown-toggle").html($(this).text()+' <span class="caret"></span>');"Procedures"==$(this).text()?($("#procedurepanel").css("display","table-cell"),$("#categorypanel").css("display","none"),$("#proclist").css("display","table-cell"),$("#catlist").css("display","none")):($("#procedurepanel").css("display","none"),$("#categorypanel").css("display","table-cell"),$("#proclist").css("display","none"),$("#catlist").css("display",
"table-cell"))});var a=window.location.hash;"c"==document.forms.newcategory.section.value||"#c"==a?($('.nav a:contains("Categories")').parent().addClass("active"),$("#procedurepanel").css("display","none"),$("#categorypanel").css("display","block")):$('.nav a:contains("All Procedures")').parent().addClass("active");$(".nav-tabs > li > a").click(function(a){$(this).parent().addClass("active").siblings().removeClass("active");"Categories"==$(this).text()?(cancelproc(),$("#procedurepanel").css("display",
"none"),$("#categorypanel").css("display","block")):($("#categorypanel").css("display","none"),$("#procedurepanel").css("display","block"))});$('#categorypanel .panel-collapse:not(".in")').collapse("show");$(".hoverrow").hover(function(){var a=$(this).attr("data-id");$("#deleteproc"+a).css("visibility","visible");$("#editproc"+a).css("visibility","visible")},function(){var a=$(this).attr("data-id");"img/loading.gif"!=$("#deleteproc"+a).attr("src")&&$("#deleteproc"+a).css("visibility","hidden");"img/loading.gif"!=
$("#editproc"+a).attr("src")&&$("#editproc"+a).css("visibility","hidden")});movemargins()});
function addcategory(){var a=document.forms.newcategory.name,b=document.forms.newcategory.catid;null==a?alert("Name must be filled out"):($(".loadingicon").css("visibility","visible"),$.ajax({url:"include/ops.php",data:{consultid:"addcategory",cat:a.value,catid:b.value},type:"post",success:function(a){console.log(a);var b=document.location.protocol+"//"+document.location.hostname+document.location.pathname+"?s=c";setTimeout(function(){document.location.href=b},1E3)}}))}
function addproc(a){""!=a&&($('.nav a:contains("Categories")').parent().removeClass("active"),$('.nav a:contains("All Procedures")').parent().addClass("active"),$("#procedurepanel").css("display","block"),$("#categorypanel").css("display","none"),$("#categoryName").val(a));$("#newproc").css("display","block");$("#listprocs").css("display","none");$("#newprocb").css("visibility","hidden");$("#orderbyp").css("visibility","hidden")}
function cancelproc(){$("#newproc").css("display","none");$("#listprocs").css("display","table");$("#newprocb").css("visibility","visible");$("#orderbyp").css("visibility","visible");var a=window.location.hash;if("c"==document.forms.newcategory.section.value||"#c"==a)$('.nav a:contains("Categories")').parent().addClass("active"),$('.nav a:contains("All Procedure")').parent().removeClass("active"),$("#procedurepanel").css("display","none"),$("#categorypanel").css("display","block")}
function cancelproc2(){$("#modalnewus").modal("hide")}
function deletecat(a,b){1==confirm("Are you sure you want to delete category: "+b+"?\nWARNING: This will delete all procedures under this category")?($("#deletecat"+a).attr("src","img/loading.gif"),$("#deletecat"+a).attr("width","20"),$.ajax({url:"include/ops.php",data:{consultid:"deletecat",catid:a},type:"post",success:function(a){console.log(a);var b=document.location.protocol+"//"+document.location.hostname+document.location.pathname+"?s=c";setTimeout(function(){document.location.href=
b},100)}})):console.log("no")}function newproc(){0==document.forms.newprocedure.acc_ll.value?$("#modalnewus").modal("show"):newproc2()}
function newproc2(){var a=document.forms.newprocedure.name;if(0==document.forms.newprocedure.acc_ll.value){var b=document.forms.newsuer.acc_us.value;""==b.value&&alert("Process not created")}else b="";var c=CKEDITOR.instances.description.getData(),d=0;"MediaWiki"==$("#newproc .nav-tabs li.active").text()&&(d=1,des0=document.forms.newprocedure.description2,c=des0.value);var e=document.forms.newprocedure.categoryName,f=document.forms.newprocedure.usercreator;""==e.value?alert("Error: There is no category"):
""==a.value||""==c.value?alert("All fields must be filled out"):($(".loadingicon2").css("visibility","visible"),$("#addbutton").prop("disabled",!0),$("#cancelbutton").prop("disabled",!0),$.ajax({url:"include/ops.php",data:{consultid:"addprocedure",name:a.value,des:c,cat:e.value,user:f.value,pars:d,sm:b},type:"post",success:function(a){console.log(a);0==a?alert("Wrong passowrd"):(window.location.href.split("?"),location.reload())}}))}
function deleteproc(a,b){1==confirm("Are you sure you want to delete procedure: "+b+"?")?($("#deleteproc"+a).attr("src","img/loading.gif"),$("#deleteproc"+a).attr("width","20"),$.ajax({url:"include/ops.php",data:{consultid:"deleteproc",id:a},type:"post",success:function(a){setTimeout(function(){location.reload()},1E3)}})):console.log("no")}function gotoproc(a,b){var c=""!=b?"proci.php?pi="+a+"&s=c":"proci.php?pi="+a;setTimeout(function(){document.location.href=c},100)}
function editproc(a){$("#editproc"+a).attr("src","img/loading.gif");$("#editproc"+a).attr("width","20");var b="eproc.php?pi="+a;setTimeout(function(){document.location.href=b},100)}function orderby(a){document.location.href=document.location.protocol+"//"+document.location.hostname+document.location.pathname+"?ord="+a}
function changeeditor(a){1==a?($("#cke_description").css("display","block"),$("#description2").css("display","none")):($("#description2").css("display","block"),$("#cke_description").css("display","none"))}function changepar(a){if(""==a&&"c"==getQueryVariable("s")){var b=removeparameters(window.location.href);document.location.href=b}window.location.hash=a}function removeparameters(a){var b=0,c=a,b=a.indexOf("?");-1==b&&(b=a.indexOf("#"));-1!=b&&(c=a.substring(0,b));return c}
function modalcategory(a){$("#newcategorym").modal("show");document.forms.newcategory.catid.value=a}function getQueryVariable(a){for(var b=window.location.search.substring(1).split("&"),c=0;c<b.length;c++){var d=b[c].split("=");if(decodeURIComponent(d[0])==a)return decodeURIComponent(d[1])}}function movemargins(){$("*[class^='group']").css("margin-left","0");$("*[class^='group']").each(function(){var a=30*this.getAttribute("data-depth");$(this).css("margin-left",a+"px")})}
function edditcategory0(a,b){$("#editcategorym").modal("show");document.forms.editcategory.catid.value=a;document.forms.editcategory.name.value=b}
function edditcategory(){var a=document.forms.editcategory.name,b=document.forms.editcategory.catid;null==a?alert("Name must be filled out"):($(".loadingicon").css("visibility","visible"),$.ajax({url:"include/ops.php",data:{consultid:"editcategory",cat:a.value,catid:b.value},type:"post",success:function(a){console.log(a);var b=document.location.protocol+"//"+document.location.hostname+document.location.pathname+"?s=c";setTimeout(function(){document.location.href=b},1E3)}}))}
function hidemodules(a){var b=$("#pointercat"+a).attr("src");console.log("paso "+a);"img/arrow2.png"==b?($("*[class^='group"+a+"']").css("display","none"),$(".group"+a).css("display","block"),$("#pointercat"+a).attr("src","img/arrow.png")):($("*[class^='group"+a+"']").css("display","block"),$("#pointercat"+a).attr("src","img/arrow2.png"))};
    </script>

</body>

</html>
