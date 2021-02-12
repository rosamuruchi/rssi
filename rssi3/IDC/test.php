
<?php
  $name_session = new SNMP(SNMP::VERSION_1, "10.9.11.5", "public");
  $dname = $name_session->walk("sysName");

  $device_name = substr($dname['SNMPv2-MIB::sysName.0'], strpos($dname['SNMPv2-MIB::sysName.0'], 'STRING:')+8, strpos($dname['SNMPv2-MIB::sysName.0'], substr($dname['SNMPv2-MIB::sysName.0'], -1)));
  print_r($dname);

  echo "\n\n $device_name \n\n";
  $name_session->close();
?>
