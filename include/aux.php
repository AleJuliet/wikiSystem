<?php

function calNetworkAddress($ip, $netmask)
{
  $ip_elements_decimal = split( "[.]", $ip );
  $netmask_result="";
  for($i=1; $i <= $netmask; $i++) {
    $netmask_result .= "1";
  }
  for($i=$netmask+1; $i <= 32; $i++) {
      $netmask_result .= "0";
  }
  $netmask_ip_binary_array = str_split( $netmask_result, 8 );
  $netmask_ip_decimal_array = array();
  foreach( $netmask_ip_binary_array as $k => $v ){
      $netmask_ip_decimal_array[$k] = bindec( $v ); // "100" => 4
      $network_address_array[$k] = ( $netmask_ip_decimal_array[$k] & $ip_elements_decimal[$k] );
  }
  $network_address = join( ".", $network_address_array );
  return $network_address;
}


?>