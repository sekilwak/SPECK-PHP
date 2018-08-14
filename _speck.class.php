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
		$key = $this->_str2long(str_pad($key, 16, $key));
		
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
      $n = strlen($text);
      if($n%8 != 0) $lng = ($n+(8-($n%8)));
      else $lng = 0;
      $text = str_pad($text, $lng, ' ');
      $text = $this->_str2long($text);
	  //echo " <br><b>Plain  :</b> ".implode(" ",$text)."<br>";
      $cipher=array();
      $a = 1;
      for($i = 0; $i<count($text); $i+=2) {
         //echo " <br><b>PUTARAN SESI KE $a :</b> ";
         $cipher[] = $this->block_encrypt($text[$i],$text[$i+1], $key);
		 //echo " <br><b>HASIL :</b> ".implode(" ",$cipher[$a-1])."<br>";
         $a++; 
		 
      }
	  
	  
	  
      $output = ""; //Inisialisasi variable output utk menampung hasil akhir
	  
      for($i = 0; $i<count($cipher); $i++) {
         $output .= $this->_long2str($cipher[$i][0]); // Mengkonversi Array posisi [0,0] dan [0,1] dari Long Integer ke String
		 //echo  "CIPHERTEXT X,Y (DALAM STRING): =>CIPHERTEXT [".$i.",1] = ".$cipher[$i][0]." = ".$this->_long2str($cipher[$i][0])."<br>";
		 
         $output .= $this->_long2str($cipher[$i][1]); //Mengkonversi Array posisi [1,0] dan [1,1] dari Long Integer ke String
		//echo  "CIPHERTEXT X,Y (DALAM STRING): =>CIPHERTEXT [".$i.",1] = ".$cipher[$i][1]." = ".$this->_long2str($cipher[$i][1])."<br>";
		 //echo  "CIPHERTEXT SETELAH PENGABUNGAN KE ".($i+1)." : ". $output."<br><br>";
      }
	  //echo "HASIL AKHIR CIPHERTEXT : ". $output."<br>";
      return base64_encode($output); //Mengkonversi hasil akhir berupa Kode ASCII ke bentuk BASE64
   }
	function block_encrypt($_x, $_y, $key) {
		  
		  //
		$x = $_x; $y = $_y;
		for ($i = 0; $i < 27; $i++) {
			
			$x = ( ($x << 24 | $this->_rshift($x , 8) )+ $y )^ $key[$i];
			$y = ($y << 3 | $this->_rshift($y , 29) ) ^ $x;
			
			
		}
		$text[0] = $x; $text[1] = $y;
		
		
		
		//return array($x,$y);
		//$cipher = $this->_long2str($x).$this->_long2str($y);
		//return base64_encode($cipher);
		return array($x,$y);
	}
	
	
	//Fungsi Dekripsi
   function decrypt($text, $key) {
      $plain = array();
      $cipher = $this->_str2long(base64_decode($text));
      
      for($i=0; $i<count($cipher); $i+=2) {
		  
		 //Memasukkan ciphertext ke dalam fungsi block-decrypt untuk didekripsi
         $plain[] = $this->block_decrypt($cipher[$i],$cipher[$i+1], $key); 
         
		 
		 //echo "HASIL SETELAH ROUND TERAKHIR [X,Y]:".implode(" ",$plain)."<br>";
		 
		 
            
			
		
      }
		//echo " <br><b>HASIL SETELAH XOR:</b> "."<br>";
	    //echo "PLAINTEXT X,Y Ke 0 (DALAM LONG INTEGER):".implode(" ",$plain[0])."<br>";
		//echo "PLAINTEXT X,Y Ke 1 (DALAM LONG INTEGER):".implode(" ",$plain[1])."<br>";
		//echo "PLAINTEXT X,Y Ke 1 (DALAM LONG INTEGER):".implode(" ",$plain[2])."<br>";
		
	  $output=""; //Inisialisasi variable output utk menampung hasil akahir
	  
	  //echo " <br><b>HASIL SETELAH DIKONVERSI (LONG INTEGER KE STRING):</b> "."<br>";
      for($i = 0; $i<count($plain); $i++) {
		 // Mengkonversi Array posisi [0,0] dan [0,1] dari Long Integer ke String
         $output .= $this->_long2str($plain[$i][0]);
		 //echo  "PLAINTEXT X,Y (DALAM STRING): =>PLAINTEXT [".$i.",1] = ".$plain[$i][0]." = ".$this->_long2str($plain[$i][0])."<br>";
		 
		 // Mengkonversi Array posisi [1,0] dan [1,1] dari Long Integer ke String
         $output .= $this->_long2str($plain[$i][1]);
		 //echo  "PLAINTEXT X,Y (DALAM STRING): =>PLAINTEXT [".$i.",1] = ".$plain[$i][1]." = ".$this->_long2str($plain[$i][1])."<br>";
		 //echo  "PLAINTEXT SETELAH PENGABUNGAN KE ".($i+1)." : ". $output."<br><br>";
      }
      return $output; //Menampilkan hasil akhir Plaintext
   }
	//Fungsi ini digunakan untuk proses dekripsi
	//function decrypt($text, $key) {
	function block_decrypt($_x, $_y,$key) {	
		//$text = $this->_str2long(base64_decode($text));		
		//$x = $text[0]; $y = $text[1];
		$x = $_x; $y = $_y;
		for ($i = 27 - 1; $i >= 0; $i--) {
			
			$y = $x ^ $y;
			$y = $y << 29 | $this->_rshift($y , 3);
			$x = ($x ^ $key[$i]) - $y;
			$x = $x << 8 | $this->_rshift($x , 24);
		}
		$text[0] = $x; $text[1] = $y;
		
		return array($x,$y);
		//return $this->_long2str($x).$this->_long2str($y);
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

