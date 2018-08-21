<?php

class _SPECK {
   
   
   
   //Fungsi ini digunakan untuk menangani pergeseran ke kanan, jika bilangan lebih besar ( > ) dr 32 bits
	function _rshift($integer, $n)
	{

		// Konversi bilangan ke bentuk 32 bits

		if (0xffffffff < $integer || -0xffffffff > $integer) {

			$integer = fmod($integer, 0xffffffff + 1);

		}



		// Konversi ke bentuk bilangan bertipe unsigned integer

		if (0x7fffffff < $integer) {

			$integer -= 0xffffffff + 1.0;

		} elseif (-0x80000000 > $integer) {

			$integer += 0xffffffff + 1.0;

		}



		// Proses Pergeseran ke kanan (untuk bilangan unsigned integer)

		if (0 > $integer) {								//Jika bilangan lebih besar dari range integer (> 32 bit)

			$integer &= 0x7fffffff;                     // Hapus bilangan sign bit sebelum pergeseran

			$integer >>= $n;                            // Pergeseran ke kanan

			$integer |= 1 << (31 - $n);                 // memasukkan nilai pergeseran sign bit

		} else {

			$integer >>= $n;                            // Gunakan pergeseran normal, jika bilangan < 32 bit

		}



		return $integer;

	}

	//Fungsi ini digunakan untuk Proses Key Expansion
	function expandKey($key, $expanded) {
		$key = array(hexdec(substr($key,0,8)), hexdec(substr($key,8,8)), hexdec(substr($key,16,8)), hexdec(substr($key,24,8)) );
		
		$k = $key[3];
		for ($i = 0, $j; $i < 27; ++$i) {
			$expanded[$i] = $k;
			$j = 2 - $i % 3;
			$key[$j] = ( ($key[$j] << 24 | $this->_rshift($key[$j] , 8) ) + $k ) ^ $i; // nilai 24 diperoleh dari : WORD_SIZE - 8 bit = 32 bit - 8 bit = 24 bit
			$k = ( $k << 3 | $this->_rshift($k , 29) )^ $key[$j]; // nilai 29 diperoleh dari : WORD_SIZE - 8 bit = 32 bit - 3 bit = 29 bit
			
		}
		
			
		return $expanded;
	}

	//Fungsi ini untuk melakukan proses Enkripsi
	function encrypt($text, $key) {
		
		$text = array(hexdec(substr($text,0,8)), hexdec(substr($text,8,8)));
		
		$x = $text[0]; $y = $text[1];
		for ($i = 0; $i < 27; $i++) {
			
			$x = ( ($x << 24 | $this->_rshift($x , 8) )+ $y )^ $key[$i];
			$y = ($y << 3 | $this->_rshift($y , 29) ) ^ $x;
			
			
		}
		$text[0] = $x; $text[1] = $y;
		
		
		
		//return array($x,$y);
		$cipher = dechex($x).dechex($y);
		return $cipher;
		
	}

	//Fungsi ini digunakan untuk proses dekripsi
	function decrypt($text, $key) {
		
		$text = array(hexdec(substr($text,0,8)), hexdec(substr($text,8,8)));	
		$x = $text[0]; $y = $text[1];
		for ($i = 27 - 1; $i >= 0; $i--) {
			
			$y = $x ^ $y;
			$y = $y << 29 | $this->_rshift($y , 3);
			$x = ($x ^ $key[$i]) - $y;
			$x = $x << 8 | $this->_rshift($x , 24);
		}
		$text[0] = $x; $text[1] = $y;
		
		//return array($x,$y);
		return dechex($x).dechex($y);
	}
   //====================================
    
  
   //Fungsi untuk mengkonversi String Ke bentuk long integer
   function _str2long($data) {
       $n = strlen($data);
       $tmp = unpack('N*', $data);
       $data_long = array();
       $j = 0;
       foreach ($tmp as $value) $data_long[$j++] = $value;
       return $data_long;
   }
   
   //Fungsi untuk mengkonversi long integer ke bentuk string
   function _long2str($l){
       return pack('N', $l);
   }
   
    
}
