<?php 

define("PBKDF2_HASH_ALGORITHM", "sha256");
define("PBKDF2_ITERATIONS", 2000);

function savekeyfile($key)
{
  $file = 'db/i_vcmlo.tw';
  file_put_contents($file, $key);
}

/*
* First User creation
*/
function firstuser($username,$fullname,$password)
{
    //Random dbkey
    // Generate a 256-bit encryption key (32 bytes), binary string
    $dbKey = mcrypt_create_iv(32, MCRYPT_DEV_URANDOM); 
    //Creates an initialization vector (IV) from a random source, MCRYPT_DEV_URANDOM default
    $initVector = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
    
    // CREATING SALT STRINGS
    //binary
    $passwordSalt = base64_encode(openssl_random_pseudo_bytes(32));
    $dbKeySalt = base64_encode(openssl_random_pseudo_bytes(32));
    
    // CREATING HASHED SALTED PASSWORDS. Returns String
    /*
    * hashedHexString — uses PBKDF2 to generate a hashed value, using sha256 function
    *    PBKDF2 (with the salt) repeats the process many times to produce a derived key
    */
    //Returns raw binary 32 bytes to use as key in the function mcrypt_encrypt
    $dbKeyEncryptionKey = hashedHexString(PBKDF2_HASH_ALGORITHM, $password, $dbKeySalt, PBKDF2_ITERATIONS, 32, true);
    //Returns hexadecimal
    $hashedSaltUsrPwd = hashedHexString(PBKDF2_HASH_ALGORITHM, $password, $passwordSalt, PBKDF2_ITERATIONS, 0, false);
    
    if (!$dbKeyEncryptionKey || !$hashedSaltUsrPwd)
      return false;
    
    // CRYPTED PASSWORDS
    /*
    * mcrypt_encrypt — Encrypts plaintext with given parameters
    * $cipher AES-256 is RIJNDAEL-128
    * $key the key should be random raw binary
    * $data The data that will be encrypted with the given cipher and mode (it's in hexadecimal)
    * $mode MCRYPT_MODE_CBC
    * $iv
    * pkcs5_pad() implements PKCS#5 padding
    */
    //Encrypts dbkey with the hashed key
    $blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $input = pkcs5_pad($dbKey, $blockSize); 
    $cryptDBKey = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $dbKeyEncryptionKey,
                                 $input, MCRYPT_MODE_CBC, $initVector);
    
    //Encrypts the hashed password with dbkey
    $blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $input = pkcs5_pad($hashedSaltUsrPwd, $blockSize); 
    $cryptHashedSaltUsrPwd = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $dbKey,
                                 $input, MCRYPT_MODE_CBC, $initVector);
    

    savekeyfile($initVector);
    return insertUser($username,$fullname,$passwordSalt, $dbKeySalt, bin2hex($cryptHashedSaltUsrPwd),bin2hex($cryptDBKey), 1);                           
} 

/*
* User creation
*/
function createuser($fullname,$username,$password,$user,$firstpassword)
{
    //Check password checkPasswordCorrectness
    $sameps = checkPasswordCorrectness($firstpassword,$user["dbKeySalt"],$user["passwordSalt"],$user["cryptDBKey"],$user["cryptHashedSaltUsrPwd"]);
    if (!$sameps)
      return "2";
    
    //Retrieve dbkey and iv
    $file = 'db/i_vcmlo.tw';
    $string = file_get_contents($file);
    $iv = mb_strcut($string, 0, 16);
    
    // CREATING SALT STRINGS
    //binary
    $passwordSalt = base64_encode(openssl_random_pseudo_bytes(32));
    $dbKeySalt = base64_encode(openssl_random_pseudo_bytes(32));
    
    // CREATING HASHED SALTED PASSWORDS. Returns String
    //Returns raw binary 32 bytes to use as key in the function mcrypt_encrypt
    $dbKeyEncryptionKey = hashedHexString(PBKDF2_HASH_ALGORITHM, $firstpassword, $user["dbKeySalt"], PBKDF2_ITERATIONS, 32, true);
    $dbKeyEncryptionKeyC = hashedHexString(PBKDF2_HASH_ALGORITHM, $password, $dbKeySalt, PBKDF2_ITERATIONS, 32, true);
    //Returns hexadecimal
    $hashedSaltUsrPwd = hashedHexString(PBKDF2_HASH_ALGORITHM, $password, $passwordSalt, PBKDF2_ITERATIONS, 0, false);
      
    // DECRYPTING SAVED KEYS
    $dbKey = pkcs5_unpad(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $dbKeyEncryptionKey,
                                 hex2bin($user["cryptDBKey"]), MCRYPT_MODE_CBC, $iv));       
                                 
    if (!$dbKeyEncryptionKeyC || !$hashedSaltUsrPwd)
      return "0";
    
    // CRYPTED PASSWORDS
    //Encrypts dbkey with the hashed key
    $blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $input = pkcs5_pad($dbKey, $blockSize); 
    $cryptDBKey = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $dbKeyEncryptionKeyC,
                                 $input, MCRYPT_MODE_CBC, $iv);
    
    //Encrypts the hashed password with dbkey
    $blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $input = pkcs5_pad($hashedSaltUsrPwd, $blockSize); 
    $cryptHashedSaltUsrPwd = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $dbKey,
                                 $input, MCRYPT_MODE_CBC, $iv);
    
    return insertUser($username,$fullname,$passwordSalt, $dbKeySalt, bin2hex($cryptHashedSaltUsrPwd), bin2hex($cryptDBKey), 0);
                                 
} 

/**
* PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
* $algorithm - The hash algorithm to use. Recommended: SHA256
* $password - The password.
* $salt - A salt that is unique to the password.
* $count - Iteration count. Higher is better, but slower. Recommended: At least 1000.
* $key_length - The length of the derived key in bytes.
* $raw_output - If true, the key is returned in raw binary format. Hex encoded otherwise.
* Returns: A $key_length-byte key derived from the password and salt.
*/
function hashedHexString($algorithm,$password,$salt,$count,$length,$raw_output)
{ 
        if(!in_array($algorithm, hash_algos(), true))
	  return trigger_error('PBKDF2 ERROR: Invalid hash algorithm.', E_USER_ERROR);
	if($count<=0)
	  return trigger_error('PBKDF2 ERROR: Invalid parameters.', E_USER_ERROR);
	  
	if (function_exists("hash_pbkdf2")) {
	  if ($length==0)
	  {
	    return hash_pbkdf2($algorithm, $password, $salt, $count);
	  }
	  else 
	    return hash_pbkdf2($algorithm, $password, $salt, $count,32,$raw_output);
	    
	}
	else 
	  return false;
}

function pkcs5_pad ($text, $blocksize) 
{ 
    $pad = $blocksize - (strlen($text) % $blocksize); 
    return $text . str_repeat(chr($pad), $pad); 
} 

function pkcs5_unpad($text) 
{ 
    $pad = ord($text{strlen($text)-1}); 
    if ($pad > strlen($text)) return false; 
    if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false; 
    return substr($text, 0, -1 * $pad); 
}

/*
*
*/
function checkPasswordCorrectness($password,$dbKeySalt,$passwordSalt,$cryptDBKey,$cryptHashedSaltUsrPwd)
{
    $file = 'db/i_vcmlo.tw';
    $string = file_get_contents($file);
    $initVector = mb_strcut($string, 0, 16);
    
    // CREATING HASHED SALTED PASSWORDS
    //Returns raw binary 32 bytes to use as key in the function mcrypt_encrypt
    $dbKeyEncryptionKey = hashedHexString(PBKDF2_HASH_ALGORITHM, $password, $dbKeySalt, PBKDF2_ITERATIONS, 32, true);
    //Returns hexadecimal
    $newHashedSalted = hashedHexString(PBKDF2_HASH_ALGORITHM, $password, $passwordSalt, PBKDF2_ITERATIONS, 0, false);
    
    // DECRYPTING SAVED KEYS
    $dbKey = pkcs5_unpad(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $dbKeyEncryptionKey,
                                 hex2bin($cryptDBKey), MCRYPT_MODE_CBC, $initVector));
    $oldHashedSalted = pkcs5_unpad(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $dbKey,
                                 hex2bin($cryptHashedSaltUsrPwd), MCRYPT_MODE_CBC, $initVector));

    if ($newHashedSalted==$oldHashedSalted)
    {
	$_SESSION['acc_ll']=$dbKeyEncryptionKey;
	return true;
    }
    return false;
}

function encryptdescription($des,$acc_ll,$user)
{    
    $file = 'db/i_vcmlo.tw';
    $string = file_get_contents($file);
    $iv = mb_strcut($string, 0, 16);
    
    $dbKey = pkcs5_unpad(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $acc_ll,
				  hex2bin($user["cryptDBKey"]), MCRYPT_MODE_CBC, $iv));
    
    //Encrypts the hashed password with dbkey
    $blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $input = pkcs5_pad($des, $blockSize); 
    $desencript = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $dbKey,
                                 $input, MCRYPT_MODE_CBC, $iv);
                                 
    return $desencript;
}

function decryptdescription($des,$acc_ll,$user)
{    
    $file = 'db/i_vcmlo.tw';
    $string = file_get_contents($file);
    $iv = mb_strcut($string, 0, 16);

    $dbKey = pkcs5_unpad(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $acc_ll,
                                 hex2bin($user["cryptDBKey"]), MCRYPT_MODE_CBC, $iv));
    
    //Encrypts the hashed password with dbkey
    $des2 = pkcs5_unpad(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $dbKey,
                                 hex2bin($des), MCRYPT_MODE_CBC, $iv));
    return $des2;
}

function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
}

function getToken($length){
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    for($i=0;$i<$length;$i++){
        $token .= $codeAlphabet[crypto_rand_secure(0,strlen($codeAlphabet))];
    }
    return $token;
}

function opendb()
{
  try {
      $db = new DB();
      return $db;
  } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
  }
}

function deleteuser($username)
{
   $db = opendb();
   
   $smt = $db->prepare("DELETE FROM USERS where username=:username");
   $smt->bindValue(':username', $username, SQLITE3_TEXT);

   $smt->execute();
   if(!$smt){
      echo $db->lastErrorMsg();
   } else {
//        echo "Records created successfully\n";
      return true;
   }
   $db->close();
}

function insertUser($username,$fullname,$passwordSalt, $dbKeySalt, $cryptHashedSaltUsrPwd,$cryptDBKey, $type)
{
   $db = opendb();
   
   $smt = $db->prepare("INSERT INTO USERS (username,fullname,passwordSalt,dbKeySalt,cryptHashedSaltUsrPwd,cryptDBKey,type)
           VALUES (:username, :fullname, :passwordSalt, :dbKeySalt, :cryptHashedSaltUsrPwd,:cryptDBKey,  :type)");
   $smt->bindValue(':username', $username, SQLITE3_TEXT);
   $smt->bindValue(':fullname', $fullname, SQLITE3_TEXT);
   $smt->bindValue(':passwordSalt', $passwordSalt, SQLITE3_TEXT);
   $smt->bindValue(':dbKeySalt', $dbKeySalt, SQLITE3_TEXT);
   $smt->bindValue(':cryptHashedSaltUsrPwd', $cryptHashedSaltUsrPwd, SQLITE3_BLOB);
   $smt->bindValue(':type', $type, SQLITE3_TEXT);
   $smt->bindValue(':cryptDBKey', $cryptDBKey, SQLITE3_TEXT);

   $smt->execute();
   if(!$smt){
      echo $db->lastErrorMsg();
   } else {
//        echo "Records created successfully\n";
      return true;
   }
   $db->close();

}

function selectUser($username)
{
   $db = opendb();
   
   $ret = $db->prepare('SELECT * FROM USERS WHERE username=:id');
   $ret->bindValue(':id', $username, SQLITE3_TEXT);

   $result = $ret->execute();
   $nm = $result->fetchArray();
   
   $db->close();
   return $nm;
}

function selectfirst()
{
   $db = opendb();
   
   $ret = $db->prepare('SELECT * FROM USERS WHERE type=1');
   $ret->bindValue(':id', $username, SQLITE3_TEXT);

   $result = $ret->execute();
   $nm = $result->fetchArray();
   
   $db->close();
   return $nm;
}

?>
