
# This is Speck Block Cipher Type 128-64 

# Specification:
Alpha = 8, Beta = 3, Word Size (n) = 32, Key Word (m) = 4, Key Size (mn) = 128, Block Size (2n) = 64. 

# Note:
1. 29 value in XOR Shift, getted from: Word Size (n) - Beta = 32 bit - 3 bit = 29 bit. <br>
2. 24 value in XOR Shift, getted from: Word Size (n) - Alpha = 32 bit - 8 bit = 24 bit. <br>
3. <b>This speck class can process encryption more than 8 character (64 bit) of plain text. But key must 128 bit only</b>

# Usage:

<b>Encryption :</b>
<pre>

include "_speck.class.php";

$key_schedule=array();// declaration of variable Key Expansion
$key="abcdefghijklmnop";//example of Key (16 characters or 128 bit)
$speck = new _SPECK();//instantiation 
$key_schedule = $speck->expandKey($key, $key_schedule);//Create Key Expansion
$plaintext="abcdefgh";//plain is 8 characters(64 bit)
$ciphertext = $speck->encrypt($plaintext, $key_schedule);// call encrypt function	
echo $ciphertext; // show Result
</pre>


<b>Decryption :</b>

<pre>
include "_speck.class.php";
$key_schedule=array();// declaration of variable Key Expansion
$key="abcdefghijklmnop";//example of Key (16 characters or 128 bit)
$speck = new _SPECK();//instantiation 
$key_schedule = $speck->expandKey($key, $key_schedule);	//Create Key Expansion			
$ciphertext=< Put your cipertext here >;//cipher is 8 characters(64 bit)
$plaintext = $speck->decrypt($ciphertext, $key_schedule); // call decrypt function	
echo $plaintext; // show Result
</pre>

# Usage of Test Vector:

<b>Data of test vector taken from "The Simon and Speck Families Of Lightweight of Block Ciper" Journal</b>

<b>Speck64/96 data:</b> <br>
Key: 13121110 0b0a0908 03020100<br>
Plaintext: 74614620 736e6165<br>
Ciphertext: 9f7952ec 4175946c<br>

<b>Encryption ( ciphertext result must "9f7952ec4175946c" )</b>
<pre>
include "_speck.tv.class.php";

$key_schedule=array();
$key="131211100b0a090803020100";//Key from journal
$speck = new _SPECK();
$key_schedule = $speck->expandKey($key, $key_schedule);
$plaintext="74614620736e6165";//Plain text from Journal
$ciphertext = $speck->encrypt($plaintext, $key_schedule);
echo $ciphertext; // show Result

</pre>

<b>Decryption ( Plaintext result must "74614620736e6165" )</b>
<pre>
include "_speck.tv.class.php";
$key_schedule=array();
$key="131211100b0a090803020100";//Key from journal
$speck = new _SPECK();
$key_schedule = $speck->expandKey($key, $key_schedule);				
$ciphertext="9f7952ec4175946c";//Cipher Text from Journal
$plaintext = $speck->decrypt($ciphertext, $key_schedule);
echo $plaintext; // show Result

</pre>




