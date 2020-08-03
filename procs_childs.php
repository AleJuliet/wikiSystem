<div class="group<?php echo $groupid?> panel panel-default" id="g<?php echo $groupid?>" style="box-shadow: 0 0px 00px rgba(0,0,0,.05);margin-bottom: 10px;" data-depth="<?php echo $depth?>">
  <div class="panel-heading" style="position: relative;height: 30px;border-bottom: 0;    font-size: 14px;height: 25px;padding-bottom:0">
    <div class="">
      <h4 class="panel-title">
        <img id="pointercat<?php echo $groupid?>" onclick="hidemodules('<?php echo $groupid?>')" src="img/arrow2.png" width="14px" style="cursor:pointer">
	<a class="accordion-toggle" data-toggle="collapse"  href="<?php echo "#collapse".$cat_idP?>">
	  <span style="font-weight: 600;font-size: 14px;"><?php echo $cat_nameP?></span>
	</a>
	<img style="cursor:pointer;" width="17" onclick="edditcategory0(<?php echo $cat_idP?>,'<?php echo $cat_nameP?>')" id="edditcategory<?php echo $cat_idP?>" src="img/edit2.png"></td>
	<img style="cursor:pointer;" width="12" onclick="deletecat(<?php echo $cat_idP?>,'<?php echo $cat_nameP?>')" id="deletecat<?php echo $cat_idP?>" src="img/delete2.png"></td>
	</h4>
    </div>
  </div>
  <div id="<?php echo "collapse".$cat_idP?>" class="panel-collapse collapse in">
    <div class="panel-body" style="    border-top: 0px solid #ddd;">
      <table class="table " style="    font-size: 12px;    margin-bottom: 5px;">
	<tbody>
	<?php 
	if (!($result = $query->execute())) {
	    continue;
	}  
	while ($proc = $result->fetchArray()) {
	?>
	  <tr style="" class="hoverrow" data-id="<?php echo $proc["procid"]?>" onclick="gotoproc(<?php echo $proc["procid"]?>,'cat')">
	    <td style="width: 20px;padding-top: 3px;"><img src="img/bullet2.gif" height="6px"></td>
	    <td class="processname" style="width: 600px;padding-top: 3px;"><?php echo $proc["procname"]?></td>
	    <?php if($proc["usermod"]!="")
	    {
	    ?>
	    <td style="font-size:12px;padding-top: 3px;width: 300px;"><span style="font-style: italic;">Modified by</span>
		<?php echo $proc["usermod"]?><span style="font-style: italic;"> on</span> <?php echo $proc["moddatep"]?></td>
	    <?php } 
	    else { ?>
	    <td style="font-size:12px;padding-top: 3px;width: 300px;"><span style="font-style: italic;">Created by</span>
		<?php echo $proc["usercreator"]?><span style="font-style: italic;"> on</span> <?php echo $proc["creationdatep"]?></td>
	    <?php 
	    }
	    ?>
	    <td style="font-size:12px;padding-top: 3px;"><?php echo $proc["usercreator"]?></td>
	  </tr>
	<?php 
	}
	?>
	</tbody>
      </table>
      
      
    </div>
  </div>
  
  <a style="padding-right: 10px;cursor:pointer;color: black;font-size:10px;text-decoration: underline;color:#707070" 
      onclick="addproc(<?php echo $cat_idP?>)">Add Procedure</a>
  <a style="padding-right: 20px;cursor:pointer;color: black;font-size:10px;text-decoration: underline;color:#707070" 
      onclick="modalcategory(<?php echo $cat_idP?>)">Add Category</a>
</div>