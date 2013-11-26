<?php

class Hash extends Plugin {

	function isTriggered() {
		if(!isset($this->info['text'])) {
			$this->sendOutput($this->CONFIG['usage']);
			return;
		}

		define("PBKDF2_HASH_ALGORITHM", "sha512");
		define("PBKDF2_ITERATIONS", 10000);
		define("PBKDF2_SALT_BYTES", 8);
		define("PBKDF2_HASH_BYTES", 128);
		
		define("HASH_SECTIONS", 4);
		define("HASH_ALGORITHM_INDEX", 0);
		define("HASH_ITERATION_INDEX", 1);
		define("HASH_SALT_INDEX", 2);
		define("HASH_PBKDF2_INDEX", 3);
		$this->sendOutput($this->create_hash($this->info['text']));
		return;
	}
	
	function create_hash($password) {
	    // format: algorithm:iterations:salt:hash
	    $salt = base64_encode(mcrypt_create_iv(PBKDF2_SALT_BYTES, MCRYPT_DEV_URANDOM));
	    return "ALGO: " . PBKDF2_HASH_ALGORITHM . " - ITERATIONS: " . PBKDF2_ITERATIONS . " - SALT: " .  $salt . " - HASH: " . 
	        base64_encode($this->pbkdf2(PBKDF2_HASH_ALGORITHM,$password,$salt,PBKDF2_ITERATIONS,PBKDF2_HASH_BYTES,true));
	}
	
	function validate_password($password, $good_hash) {
	    $params = explode(":", $good_hash);
	    if(count($params) < HASH_SECTIONS)
	       return false; 
	    $pbkdf2 = base64_decode($params[HASH_PBKDF2_INDEX]);
	    return slow_equals($pbkdf2,pbkdf2($params[HASH_ALGORITHM_INDEX],$password,$params[HASH_SALT_INDEX],(int)$params[HASH_ITERATION_INDEX],strlen($pbkdf2),true));
	}
	// Compares two strings $a and $b in length-constant time.
	function slow_equals($a, $b) {
	    $diff = strlen($a) ^ strlen($b);
	    for($i = 0; $i < strlen($a) && $i < strlen($b); $i++) {
	        $diff |= ord($a[$i]) ^ ord($b[$i]);
	    }
	    return $diff === 0; 
	}
	function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false){
	    $algorithm = strtolower($algorithm);
	    if(!in_array($algorithm, hash_algos(), true))
	        die('PBKDF2 ERROR: Invalid hash algorithm.');
	    if($count <= 0 || $key_length <= 0)
	        die('PBKDF2 ERROR: Invalid parameters.');
	    $hash_length = strlen(hash($algorithm, "", true));
	    $block_count = ceil($key_length / $hash_length);
	
	    $output = "";
	    for($i = 1; $i <= $block_count; $i++) {
	        // $i encoded as 4 bytes, big endian.
	        $last = $salt . pack("N", $i);
	        // first iteration
	        $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
	        // perform the other $count - 1 iterations
	        for ($j = 1; $j < $count; $j++) {
	            $xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
	        }
	        $output .= $xorsum;
	    }
	
	    if($raw_output)
	        return substr($output, 0, $key_length);
	    else
	        return bin2hex(substr($output, 0, $key_length));
	}
}