<?php 
  chdir('../');
  require_once 'include/session.php';
  require_once 'include/consults.php';
  require_once 'include/aux.php';
  
  /*
  * Insert user
  */
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="deleteuser") {
    $username = $_POST['username'];
    deleteuser($username);
  }
  
  /*
  * Create category
  */
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="addcategory") {
    $_SESSION['canary']['oper'] = "y";   
    
    $catparent = $_POST['catid'];
    $cat = $_POST['cat'];
    
    if ($_SESSION['canary']['time'] < time() - 3600) {
      return;
    }
    
    if($catparent=="")
    {
      $catparent=0;
    }
    $db = opendb(); 
    //Table parents
    $smt = $db->prepare("INSERT INTO CATEGORIES (catname,parentCat_id)
	    VALUES (:catname,:parentCat_id)");
    $smt->bindValue(':catname', $cat, SQLITE3_TEXT);
    $smt->bindValue(':parentCat_id', $catparent, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
	echo "Records created successfully\n";
    }
    $idcat = $db->lastInsertRowid();
    
    //Table clousure 
    $smt = $db->prepare("INSERT INTO Category_Clousure (parentCategory_id,child_id,depth)
	    VALUES (:parentCategory_id,:child_id,:depth)");
    $smt->bindValue(':parentCategory_id', $idcat, SQLITE3_TEXT);
    $smt->bindValue(':child_id', $idcat, SQLITE3_TEXT);
    $smt->bindValue(':depth', 0, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
	echo "Records created successfully\n";
    }
    
    //Table clousure
    $smt = $db->prepare("INSERT INTO Category_Clousure (parentCategory_id,child_id,depth)
	    SELECT p.parentCategory_id, c.child_id, p.depth+c.depth+1
		    FROM Category_Clousure as p, Category_Clousure as c
		    WHERE p.child_id=:parentCategory_id and c.parentCategory_id=:child_id");
    $smt->bindValue(':parentCategory_id', $catparent, SQLITE3_TEXT);
    $smt->bindValue(':child_id', $idcat, SQLITE3_TEXT);
    
    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
	echo "Records created successfully\n";
    }
    $db->close();
    $_SESSION['canary']['oper'] = "n";   
    return true;
  }
  
  /*
  * Delete category
  */
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="deletecat") {
    $id = $_POST['catid'];
    
    if ($_SESSION['canary']['time'] < time() - 3600) {
      return;
    }
    
    $db = opendb();
    
    $query = $db->prepare('SELECT * FROM Category_Clousure where parentCategory_id=:id');
    $query->bindValue(':id', $id, SQLITE3_TEXT);
    if (!($result = $query->execute())) {
	continue;
    } 	      
    while ($cat = $result->fetchArray(SQLITE3_ASSOC)) {
      $smt = $db->prepare("DELETE FROM CATEGORIES where catid=:id");
      $smt->bindValue(':id', $cat["child_id"], SQLITE3_TEXT);

      $smt->execute();
      if(!$smt){
	  echo $db->lastErrorMsg();
      } else {
    //        echo "Records created successfully\n";
      }
            
      if($cat["child_id"]!=$id)
      {
        $smt = $db->prepare("delete from Category_Clousure where parentCategory_id=:id and child_id=:id;");
	$smt->bindValue(':id', $cat["child_id"], SQLITE3_TEXT);
	$smt->execute();
	if(!$smt){
	    echo $db->lastErrorMsg();
	} else {
      //        echo "Records created successfully\n";
	}
      }
    }
	
    $smt = $db->prepare("delete from Category_Clousure where id in ( Select link.id from Category_Clousure p, 
        Category_Clousure link, Category_Clousure c, Category_Clousure to_delete
	where p.parentCategory_id = link.parentCategory_id      and c.child_id = link.child_id
	and p.child_id  = to_delete.parentCategory_id and c.parentCategory_id= to_delete.child_id
	and (to_delete.parentCategory_id=:id or to_delete.child_id=:id)
	and to_delete.depth<2);");
	

    $smt->bindValue(':id', $id, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
  //        echo "Records created successfully\n";
    }
    
    //Delete all procedures in this category 
    $procs = array();
    $queryprocs = $db->prepare('SELECT * FROM PROCEDURES where categoryid=:id');
    $queryprocs->bindValue(':id',$id, SQLITE3_TEXT);
    if (!($resultproc = $queryprocs->execute())) {
	continue;
    }
    while ($proc = $resultproc->fetchArray()) {
      $procs[] = $proc["procid"];
    }
    
    $smt = $db->prepare("DELETE FROM PROCEDURES where categoryid=:id");
    $smt->bindValue(':id', $id, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
  //        echo "Records created successfully\n";
// 	return true;
    }
    
    //Delete from server table
    for ($i=0;$i<count($procs);$i++)
    {
      $smt = $db->prepare("DELETE FROM PROCSERVER where procid=:id");
      $smt->bindValue(':id', $procs[$i], SQLITE3_TEXT);

      $smt->execute();
      if(!$smt){
	  echo $db->lastErrorMsg();
      } else {
    //        echo "Records created successfully\n";
  // 	return true;
      }
    }    
        
    $db->close();
    return true;
  }
  
  /*
  * Edit category
  */
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="editcategory") {
    $_SESSION['canary']['oper'] = "y";   
    $name = $_POST['cat'];
    $catid = $_POST['catid'];
    
    $db = opendb(); 
    //Table parents
    $smt = $db->prepare("UPDATE CATEGORIES SET catname=:catname WHERE catid=:catid");
    $smt->bindValue(':catname', $name, SQLITE3_TEXT);
    $smt->bindValue(':catid', $catid, SQLITE3_TEXT);

    $smt->execute();
    
    $db->close();
    $_SESSION['canary']['oper'] = "n";   
    return true;
  }
  
  /*
  * Create procedure
  */
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="addprocedure") {
    $_SESSION['canary']['oper'] = "y";   
    
    $proc = $_POST['name'];
    $des0 = $_POST['des'];
    $cat = $_POST['cat'];
    $user = $_POST['user'];
    $parsed = $_POST['pars'];
    
    //Encript data 
    $userinfo = selectUser($user);
    $acc_ll = $_POST['sm'];
    if($acc_ll!="")
    {
      $sameps = checkPasswordCorrectness($password,$user["dbKeySalt"],$user["passwordSalt"],$user["cryptDBKey"],$user["cryptHashedSaltUsrPwd"]);
      if(!$sameps)
      {
        return 0;
      }
    }
    $acc_ll = $_SESSION['acc_ll'];    
    
    $des = encryptdescription($des0,$acc_ll,$userinfo);
    
    
    $db = opendb();   
    $smt = $db->prepare("INSERT INTO PROCEDURES (procname,procdescription,categoryid,usercreator, parsed)
	    VALUES (:procname,:procdes,:proccat,:user, :parsed)");
    $smt->bindValue(':procname', $proc, SQLITE3_TEXT);
    $smt->bindValue(':procdes', bin2hex($des), SQLITE3_TEXT);
    $smt->bindValue(':proccat', $cat, SQLITE3_TEXT);
    $smt->bindValue(':user', $user, SQLITE3_TEXT);
    $smt->bindValue(':parsed', $parsed, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
         echo "Records created successfully\n";
    }
    $db->close();
    $_SESSION['canary']['oper'] = "n";   
    return true;
  }  
  /*
  * Delete procedure
  */
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="deleteproc") {
    $_SESSION['canary']['oper'] = "y";   
    
    $id = $_POST['id'];
    $db = opendb();
   
    $smt = $db->prepare("DELETE FROM PROCEDURES where procid=:id");
    $smt->bindValue(':id', $id, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
  //        echo "Records created successfully\n";
// 	return true;
    }
    
    //Delete from server
    $smt = $db->prepare("DELETE FROM PROCSERVER where procid=:id");
    $smt->bindValue(':id', $id, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
  //        echo "Records created successfully\n";
// 	return true;
    }
      
    $db->close();
    $_SESSION['canary']['oper'] = "n";   
    return true;
  }
  /*
  * Update procedure
  */
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="uprocedure") {
    $_SESSION['canary']['oper'] = "y";   
    
    $proc = $_POST['name'];
    $des = $_POST['des'];
    $cat = $_POST['cat'];
    $user = $_POST['user'];
    $parsed = $_POST['pars'];
    $id = $_POST['id'];
    
    //Encript data 
    $userinfo = selectUser($user);
    $acc_ll = $_SESSION['acc_ll'];    
    
    $des = encryptdescription($des,$acc_ll,$userinfo);
    
    $db = opendb();
   
    $smt = $db->prepare("UPDATE PROCEDURES SET procname=:procname,
       procdescription=:procdes,categoryid=:proccat,usermod=:user,parsed=:parsed,moddatep=datetime() where procid=:id");
    $smt->bindValue(':procname', $proc, SQLITE3_TEXT);
    $smt->bindValue(':procdes', bin2hex($des), SQLITE3_TEXT);
    $smt->bindValue(':proccat', $cat, SQLITE3_TEXT);
    $smt->bindValue(':user', $user, SQLITE3_TEXT);
    $smt->bindValue(':parsed', $parsed, SQLITE3_TEXT);
    $smt->bindValue(':id', $id, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
  //        echo "Records created successfully\n";
// 	return true;
    }
    $db->close();
    $_SESSION['canary']['oper'] = "n";   
    return true;
  }
  
  /*
  * Create pool
  */
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="addpool") {
    $pool = $_POST['pool'];
    $des = $_POST['des'];
    $user = $_POST['user']; 
    
    if ($_SESSION['canary']['time'] < time() - 3600) {
      return;
    }
    
    //Encript data 
    $userinfo = selectUser($user);
    $acc_ll = $_SESSION['acc_ll'];
    
    $db = opendb();   
    $pname = encryptdescription($pool,$acc_ll,$userinfo);
    $pdes = encryptdescription($des,$acc_ll,$userinfo);
    
    $smt = $db->prepare("INSERT INTO POOL (poolname,pooldes)
	    VALUES (:poolname,:pooldes)");
    $smt->bindValue(':poolname', bin2hex($pname), SQLITE3_TEXT);
    $smt->bindValue(':pooldes', bin2hex($pdes), SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
         echo "Records created successfully\n";
    }
    $db->close();
    return true;
  }
  /*
  * Delete pool
  */
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="deletepool") {
    $id = $_POST['id'];
    
    if ($_SESSION['canary']['time'] < time() - 3600) {
      return;
    }
    
    $db = opendb();
   
    $smt = $db->prepare("DELETE FROM POOL where poolid=:id");
    $smt->bindValue(':id', $id, SQLITE3_TEXT);
    
    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
         echo "Records created successfully\n";
	return true;
    }
    $db->close();
    return true;
  }
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="editpool") {
    $namepool = $_POST['pool'];
    $id = $_POST['idpool'];
    $despool = $_POST['des'];
    $user = $_POST['user']; 
    
    if ($_SESSION['canary']['time'] < time() - 3600) {
      return;
    }
    
    //Encript data 
    $userinfo = selectUser($user);
    $acc_ll = $_SESSION['acc_ll'];
    
    $pname = encryptdescription($namepool,$acc_ll,$userinfo);
    $pdes = encryptdescription($despool,$acc_ll,$userinfo);
    
    $db = opendb();   
    $smt = $db->prepare("UPDATE POOL SET poolname=:poolname, pooldes=:pooldes
        where poolid=:poolid");
    $smt->bindValue(':poolname', bin2hex($pname), SQLITE3_TEXT);
    $smt->bindValue(':pooldes', bin2hex($pdes), SQLITE3_TEXT);
    $smt->bindValue(':poolid', $id, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
         echo "Records updated successfully\n";
    }
    $db->close();
    return true;
  }
  
  /*
  * Create location
  */
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="addloc") {
    $loc = $_POST['location'];
    
    if ($_SESSION['canary']['time'] < time() - 3600) {
      return;
    }
    
    $db = opendb();   
    $smt = $db->prepare("INSERT INTO LOCATION (location)
	    VALUES (:location)");
    $smt->bindValue(':location', $loc, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
         echo "Records created successfully\n";
    }
    $db->close();
    return true;
  }
  /*
  * Delete location
  */
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="deletelocation") {
    $id = $_POST['id'];
    
    if ($_SESSION['canary']['time'] < time() - 3600) {
      return;
    }
    
    $db = opendb();
   
    $smt = $db->prepare("DELETE FROM LOCATION where locid=:id");
    $smt->bindValue(':id', $id, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
  //        echo "Records created successfully\n";
// 	return true;
    }
    $db->close();
    return true;
  }
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="editloc") {
    $loc = $_POST['location'];
    $id = $_POST['locationid'];
    
    if ($_SESSION['canary']['time'] < time() - 3600) {
      return;
    }
    
    $db = opendb();   
    $smt = $db->prepare("UPDATE LOCATION SET location=:location
        where locid=:id");
    $smt->bindValue(':location', $loc, SQLITE3_TEXT);
    $smt->bindValue(':id', $id, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
         echo "Records updated successfully\n";
    }
    $db->close();
    return true;
  }
  
  /*
  * Create Type
  */
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="addtype") {
    $type = $_POST['type'];
    
    if ($_SESSION['canary']['time'] < time() - 3600) {
      return;
    }
    
    $db = opendb();   
    $smt = $db->prepare("INSERT INTO TYPE (typename)
	    VALUES (:typename)");
    $smt->bindValue(':typename', $type, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
         echo "Records created successfully\n";
    }
    $db->close();
    return true;
  }
  /*
  * Delete type
  */
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="deletetype") {
    $id = $_POST['id'];
    
    if ($_SESSION['canary']['time'] < time() - 3600) {
      return;
    }
    
    $db = opendb();
   
    $smt = $db->prepare("DELETE FROM TYPE where typeid=:id");
    $smt->bindValue(':id', $id, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
  //        echo "Records created successfully\n";
// 	return true;
    }
    $db->close();
    return true;
  }
  /* 
  * Edit type
  */
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="edittype") {
    $type = $_POST['type'];
    $id = $_POST['typeid'];
    
    if ($_SESSION['canary']['time'] < time() - 3600) {
      return;
    }
    
    $db = opendb();   
    $smt = $db->prepare("UPDATE TYPE SET typename=:typename
        where typeid=:id");
    $smt->bindValue(':typename', $type, SQLITE3_TEXT);
    $smt->bindValue(':id', $id, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
         echo "Records updated successfully\n";
    }
    $db->close();
    return true;
  }
  
  /*
  * Create Server
  */
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="addserver") {
    $_SESSION['canary']['oper'] = "y";    
    
    $name = $_POST['name'];
    $des = $_POST['des'];
    $ip = $_POST['ip'];
    $masc = $_POST['masc'];
    $rol = $_POST['rol'];
    $pool = $_POST['pool'];
    $type = $_POST['type'];
    $location = $_POST['location'];
    $users = $_POST['userss'];
    $procs = $_POST['procss'];
    $usersp = $_POST['uxsw'];  
    $user = $_POST['user']; 
    $usersk = json_decode($_POST['addssh']); 
    $usserph = $_POST['addph'];    
    
    //Encript data 
    $userinfo = selectUser($user);
    $acc_ll = $_SESSION['acc_ll'];    
    
    $db = opendb();   
    $sname = encryptdescription($name,$acc_ll,$userinfo);
    $sdes = encryptdescription($des,$acc_ll,$userinfo);
      
    $smt = $db->prepare("INSERT INTO SERVERS (servername,spoolid,stypeid,slocationid,role,serverdes)
	    VALUES (:servername,:spoolid,:stypeid,:slocationid,:role,:serverdes)");
    $smt->bindValue(':servername', bin2hex($sname), SQLITE3_TEXT);
    $smt->bindValue(':spoolid', $pool, SQLITE3_TEXT);
    $smt->bindValue(':stypeid', $type, SQLITE3_TEXT);
    $smt->bindValue(':slocationid', $location, SQLITE3_TEXT);
    $smt->bindValue(':role', $rol, SQLITE3_TEXT);
    $smt->bindValue(':serverdes', bin2hex($sdes), SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
	return;
    } else {
         echo "Records created successfully\n";
    }
    
    $idser = $db->lastInsertRowid();
    
    //Insert ips
    for ($i = 0; $i < count($ip); $i++) { 
      $ipe = encryptdescription($ip[$i],$acc_ll,$userinfo);
      $masce = encryptdescription($masc[$i],$acc_ll,$userinfo);      
      
      $netAdd = calNetworkAddress($ip[$i],$masc[$i]);
      $nete = encryptdescription($netAdd,$acc_ll,$userinfo);
      $smt = $db->prepare("INSERT INTO IPSERVER (serverid,ipserver,mascara,netAddress)
	      VALUES (:serverid,:ipserver,:mascara,:netAddress)");
      $smt->bindValue(':serverid', $idser, SQLITE3_TEXT);
      $smt->bindValue(':ipserver', bin2hex($ipe), SQLITE3_TEXT);
      $smt->bindValue(':mascara', bin2hex($masce), SQLITE3_TEXT);
      $smt->bindValue(':netAddress', bin2hex($nete), SQLITE3_TEXT);
      
      $smt->execute();
      if(!$smt){
	  echo $db->lastErrorMsg();
	  return;
      } else {
	  echo "Records created successfully\n";
      }
    }    
    
    //Insert Users
    for ($i = 0; $i < count($users); $i++) { 
      //Encript info 
      $usern = encryptdescription($users[$i],$acc_ll,$userinfo);
      $userp = encryptdescription($usersp[$i],$acc_ll,$userinfo);
    
      $smt = $db->prepare("INSERT INTO USERSERVER (serverid,serveruser,passw)
	      VALUES (:serverid,:serveruser, :passw)");
      $smt->bindValue(':serverid', $idser, SQLITE3_TEXT);
      $smt->bindValue(':serveruser', bin2hex($usern), SQLITE3_TEXT);
      $smt->bindValue(':passw', bin2hex($userp), SQLITE3_TEXT);

      $smt->execute();
      if(!$smt){
	  echo $db->lastErrorMsg();
      } else {
	  echo "Records created successfully\n";
      }
    }
    
    //Insert ssh and paraprhase
   foreach ($usersk as $key => $value) {
      //SSh for particular user 
      $ssharray = $value;
      $passarray = $usserph[$key];
      $user = $key;
      
      for ($j = 0; $j < count($ssharray); $j++) { 
      
      //Encript info 
        $usern = encryptdescription($user,$acc_ll,$userinfo);
	$sshen = encryptdescription($ssharray[$j],$acc_ll,$userinfo);
	$passen = encryptdescription($passarray[$j],$acc_ll,$userinfo);
      
	$smt = $db->prepare("INSERT INTO SSHPAS (userid,serverid,sshkey,passphrase)
		VALUES (:userid,:serverid,:sshkey, :passphrase)");
	$smt->bindValue(':userid', bin2hex($usern), SQLITE3_TEXT);
	$smt->bindValue(':serverid', $idser, SQLITE3_TEXT);
	$smt->bindValue(':sshkey', bin2hex($sshen), SQLITE3_TEXT);
	$smt->bindValue(':passphrase', bin2hex($passen), SQLITE3_TEXT);

	$smt->execute();
	if(!$smt){
	    echo $db->lastErrorMsg();
	} else {
	    echo "Records created successfully\n";
	}
      }
    }
    
    for ($i = 0; $i < count($procs); $i++) { 
      $smt = $db->prepare("INSERT INTO PROCSERVER (serverid,procid)
	      VALUES (:serverid,:procid)");
      $smt->bindValue(':serverid', $idser, SQLITE3_TEXT);
      $smt->bindValue(':procid', $procs[$i], SQLITE3_TEXT);

      $smt->execute();
      if(!$smt){
	  echo $db->lastErrorMsg();
      } else {
	  echo "Records created successfully\n";
      }
    }
    $db->close();
    $_SESSION['canary']['oper'] = "n";
    return true;
  }
  
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="editserver") {
    $_SESSION['canary']['oper'] = "y";   
    
    $name = $_POST['name'];
    $des = $_POST['des'];
    $ip = $_POST['ip'];
    $masc = $_POST['masc'];
    $rol = $_POST['rol'];
    $pool = $_POST['pool'];
    $type = $_POST['type'];
    $location = $_POST['location'];
    $users = $_POST['usersarray'];
    $serverid = $_POST['serverid'];
    $procs = $_POST['procss'];
    $usersp = $_POST['uxsw'];  
    $user = $_POST['user'];   
    $usersk = json_decode($_POST['addssh']); 
    $usserph = $_POST['addph']; 

    //Encript data 
    $userinfo = selectUser($user);
    $acc_ll = $_SESSION['acc_ll'];    
    
    $db = opendb();   
    $sname = encryptdescription($name,$acc_ll,$userinfo);
    $sdes = encryptdescription($des,$acc_ll,$userinfo);
    
    $smt = $db->prepare("UPDATE SERVERS SET servername=:servername,spoolid=:spoolid,stypeid=:stypeid,
                            slocationid=:slocationid,role=:role,serverdes=:serverdes WHERE serverid=:serverid");
    $smt->bindValue(':servername', bin2hex($sname), SQLITE3_TEXT);
    $smt->bindValue(':spoolid', $pool, SQLITE3_TEXT);
    $smt->bindValue(':stypeid', $type, SQLITE3_TEXT);
    $smt->bindValue(':slocationid', $location, SQLITE3_TEXT);
    $smt->bindValue(':role', $rol, SQLITE3_TEXT);
    $smt->bindValue(':serverdes', bin2hex($sdes), SQLITE3_TEXT);
    $smt->bindValue(':serverid', $serverid, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
	return;
    } else {
         echo "Records created successfully\n";
    }
    
    //Select ips 
    $ret = $db->prepare('SELECT * FROM IPSERVER where serverid=:id');
    $ret->bindValue(':id', $serverid, SQLITE3_TEXT);
    if (!($results2 = $ret->execute())) {
	continue;
    } 
    $i = 0;
    $ipserver = array();
    $mascaras = array();
    while ($cat2 = $results2->fetchArray(SQLITE3_ASSOC)) {
      $ipe = decryptdescription($cat2["ipserver"],$acc_ll,$userinfo);
      $masce = decryptdescription($cat2["mascara"],$acc_ll,$userinfo); 
      $ipserver[$ipe] = $masce;
      $mascaras[$ipe] = $masce;
    }
    
    //Insert ips
    for ($i = 0; $i < count($ip); $i++) { 
      $ipe = encryptdescription($ip[$i],$acc_ll,$userinfo);
      $masce = encryptdescription($masc[$i],$acc_ll,$userinfo);      
      
      $netAdd = calNetworkAddress($ip[$i],$masc[$i]);
      $nete = encryptdescription($netAdd,$acc_ll,$userinfo);
      $smt = $db->prepare("INSERT INTO IPSERVER (serverid,ipserver,mascara,netAddress)
	      VALUES (:serverid,:ipserver,:mascara,:netAddress)");
      $smt->bindValue(':serverid', $serverid, SQLITE3_TEXT);
      $smt->bindValue(':ipserver', bin2hex($ipe), SQLITE3_TEXT);
      $smt->bindValue(':mascara', bin2hex($masce), SQLITE3_TEXT);
      $smt->bindValue(':netAddress', bin2hex($nete), SQLITE3_TEXT);
      
      $smt->execute();
      if(!$smt){
	  echo $db->lastErrorMsg();
	  return;
      } else {
	  echo "Records created successfully\n";
      }
      
      //If it is on the list
     if (isset($ipserver[$ip[$i]]) && $ipserver[$ip[$i]]==$masc[$i])
     {
        unset($ipserver[$ip[$i]]);
        unset($mascaras[$ip[$i]]);
     }
    }  
    
    //delete ips
    if(count($ipserver)>0)
    {
      foreach ($ipserver as $key => $value) 
      {
        $key2 = encryptdescription($key,$acc_ll,$userinfo);
	$masce = encryptdescription($ipserver[$key],$acc_ll,$userinfo); 
	$smt = $db->prepare("DELETE FROM IPSERVER where serverid=:id and ipserver=:ipserver and mascara=:mascara");
	$smt->bindValue(':id', $serverid, SQLITE3_TEXT);
	$smt->bindValue(':ipserver', bin2hex($key2), SQLITE3_TEXT);
	$smt->bindValue(':mascara', bin2hex($masce), SQLITE3_TEXT);

	$smt->execute();
	if(!$smt){
	    echo $db->lastErrorMsg();
	} else {
	}
      }
    }
    
    //Select Procedures 
    $ret = $db->prepare('SELECT * FROM PROCSERVER where serverid=:id');
    $ret->bindValue(':id', $serverid, SQLITE3_TEXT);
    if (!($results = $ret->execute())) {
	continue;
    } 
    $i = 0;
    $serversproc = array();
    while ($cat = $results->fetchArray(SQLITE3_ASSOC)) {
      $serversproc[$cat["procid"]] = $cat["procid"];
    }        
    
    for ($i = 0; $i < count($procs); $i++) { 
      $smt = $db->prepare("INSERT INTO PROCSERVER (serverid,procid)
	      VALUES (:serverid,:procid)");
      $smt->bindValue(':serverid', $serverid, SQLITE3_TEXT);
      $smt->bindValue(':procid', $procs[$i], SQLITE3_TEXT);

      $smt->execute();
      if(!$smt){
	  echo $db->lastErrorMsg();
      } else {
	  echo "Records created successfully\n";
      }
      
      //If it is on the list
     if (isset($serversproc[$procs[$i]]))
        unset($serversproc[$procs[$i]]);
    }
    
    if(count($serversproc)>0)
    {
      foreach ($serversproc as $key => $value) 
      {
	$smt = $db->prepare("DELETE FROM PROCSERVER where serverid=:id and procid=:procid");
	$smt->bindValue(':id', $serverid, SQLITE3_TEXT);
	$smt->bindValue(':procid', $serversproc[$key], SQLITE3_TEXT);

	$smt->execute();
	if(!$smt){
	    echo $db->lastErrorMsg();
	} else {
	}
      }
    }
    
    /*
    ****
    */
    //Select users
    $ret = $db->prepare('SELECT * FROM USERSERVER where serverid=:id');
    $ret->bindValue(':id', $serverid, SQLITE3_TEXT);
    if (!($results = $ret->execute())) {
	continue;
    } 
    $i = 0;
    $userssever = array();
    while ($cat = $results->fetchArray(SQLITE3_ASSOC)) {
      $userser = decryptdescription($cat["serveruser"],$acc_ll,$userinfo);
      $userssever[$userser] = $userser;
    }
    
    for ($i = 0; $i < count($users); $i++) { 
      //Encript info 
      $usern = encryptdescription($users[$i],$acc_ll,$userinfo);
      $userp = encryptdescription($usersp[$i],$acc_ll,$userinfo);
      
      $smt = $db->prepare("INSERT INTO USERSERVER (serverid,serveruser,passw)
	      VALUES (:serverid,:serveruser,:passw)");
      $smt->bindValue(':serverid', $serverid, SQLITE3_TEXT);
      $smt->bindValue(':serveruser', bin2hex($usern), SQLITE3_TEXT);
      $smt->bindValue(':passw', bin2hex($userp), SQLITE3_TEXT);

      $smt->execute();
      if(!$smt){
	  echo $db->lastErrorMsg();
      } else {
	  echo "Records created successfully\n";
      } 
      
      //If it is on the list
     if (isset($userssever[$users[$i]]))
        unset($userssever[$users[$i]]);
    }
    
    if(count($userssever)>0)
    {
//       for ($i = 0; $i < count($userssever); $i++) 
      foreach ($userssever as $key => $value) 
      {
        $usersseverd = encryptdescription($userssever[$key],$acc_ll,$userinfo);
	$smt = $db->prepare("DELETE FROM USERSERVER where serverid=:id and serveruser=:serveruser");
	$smt->bindValue(':id', $serverid, SQLITE3_TEXT);
	$smt->bindValue(':serveruser', bin2hex($usersseverd), SQLITE3_TEXT);

	$smt->execute();
	if(!$smt){
	    echo $db->lastErrorMsg();
	} else {
	}
      }
    }
    
    //Passphrase ssh 
    $smt = $db->prepare("DELETE FROM SSHPAS where serverid=:id");
    $smt->bindValue(':id', $serverid, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
    }
	  
    //Insert ssh and paraprhase
    foreach ($usersk as $key => $value) {
	//SSh for particular user 
	$ssharray = $value;
	$passarray = $usserph[$key];
	$user = $key;
	
	for ($j = 0; $j < count($ssharray); $j++) { 
	
	//Encript info 
	  $usern = encryptdescription($user,$acc_ll,$userinfo);
	  $sshen = encryptdescription($ssharray[$j],$acc_ll,$userinfo);
	  $passen = encryptdescription($passarray[$j],$acc_ll,$userinfo);
	
	  $smt = $db->prepare("INSERT INTO SSHPAS (userid,serverid,sshkey,passphrase)
		  VALUES (:userid,:serverid,:sshkey, :passphrase)");
	  $smt->bindValue(':userid', bin2hex($usern), SQLITE3_TEXT);
	  $smt->bindValue(':serverid', $serverid, SQLITE3_TEXT);
	  $smt->bindValue(':sshkey', bin2hex($sshen), SQLITE3_TEXT);
	  $smt->bindValue(':passphrase', bin2hex($passen), SQLITE3_TEXT);

	  $smt->execute();
	  if(!$smt){
	      echo $db->lastErrorMsg();
	  } else {
	      echo "Records created successfully\n";
	  }
	}
    }
    
    $db->close();
    $_SESSION['canary']['oper'] = "n";   
    return true;
  }
  
  
  
  if(isset($_POST['consultid']) && !empty($_POST['consultid']) && $_POST["consultid"]=="deleteserver") {
    $_SESSION['canary']['oper'] = "y";   
    $id = $_POST['id'];

    
    $db = opendb();  
    
    $smt = $db->prepare("DELETE FROM IPSERVER where serverid=:id");
    $smt->bindValue(':id', $id, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
    }
    
    $smt = $db->prepare("DELETE FROM USERSERVER where serverid=:id");
    $smt->bindValue(':id', $id, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
    }
    
    $smt = $db->prepare("DELETE FROM PROCSERVER where serverid=:id");
    $smt->bindValue(':id', $id, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
    }
    
    $smt = $db->prepare("DELETE FROM SSHPAS where serverid=:id");
    $smt->bindValue(':id', $id, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
    }
    
    $smt = $db->prepare("DELETE FROM SERVERS where serverid=:id");
    $smt->bindValue(':id', $id, SQLITE3_TEXT);

    $smt->execute();
    if(!$smt){
	echo $db->lastErrorMsg();
    } else {
    }
    
    $db->close();
    $_SESSION['canary']['oper'] = "n";   
    return true;
  }
?>