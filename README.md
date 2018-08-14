
# This is Spack Block Cipher Type 128-64 

# Specification:
Alpha = 8, Beta = 3, Word Size (n) = 32, Key Word (m) = 4, Key Size (mn) = 128, Block Size (2n) = 64. 

# Note:
29 value in XOR Shift, getted from: Word Size (n) - Beta = 32 bit - 3 bit = 29 bit. 
24 value in XOR Shift, getted from: Word Size (n) - Alpha = 32 bit - 8 bit = 24 bit. 

# Usage:
<pre>
<?php

include "_speck.class.php";

$key_schedule=array();// this is for Key Expansion
$key="12345678";//Key is 8 characters(64 bit)
$speck = new _SPECK();//instantiation 
$key_schedule = $speck->expandKey($key, $key_schedule);//Create Key Expantion
$plaintext="abcdefghijklmnop";//plain is 16 characters(128 bit)
$ciphertext = $speck->encrypt($plaintext, $key_schedule);// call encrypt function	
echo $ciphertext; // show Result

?>

</pre>
