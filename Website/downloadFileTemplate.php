<?php

//5 min.
set_time_limit(300);

//Get HTTP POST data:
$url = base64_decode($_POST["url"]);
$fileID = $_POST["fileID"];
$secret = $_GET["secret"];

$number = str_replace("_", "", $fileID);

if (isset($secret) && preg_match("/https:\/\/[a-zA-Z]+.itslearning.com/s", $url) && is_numeric($number)) {
	
	$user_id;

	try {
		$crypt = new Encryption("wNVBNjGYcbfBMK8YzSaYDbA9ERpgFyPR");
		$decrypted_string = $crypt->decrypt($secret);
		$split = explode(":", urldecode($decrypted_string));
		$user_id = $split[1];
	} catch (Exception $e) {
		http_response_code(500);
		unlink(__FILE__);
		die();
	}
	
	if (is_numeric($user_id)) {
	
		//Download file
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 480);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);

		$response = curl_exec($ch);
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$file = substr($response, $header_size);

		curl_close($ch);
		
		if ($response_code == 200) {
			if (preg_match('/filename=\"(.*)\"/', $header, $match) && preg_match("/Content-Type:\s([\S]+)/", $header, $match2)) {

				//Get name and mime
				$arr = explode(".", $match[1]);
				$mime = "." . $arr[count($arr) - 1];
				$name = str_replace($mime, "", $match[1]);

				//Get content-type
				$mime_type = $match2[1];

				//Create hash for file
				$hash = hash("sha256", $file);

				//Include database connection
				include_once("/home/admin/www_cloudpack/database.php");
				$json = mysqli_real_escape_string($con, '"' . $fileID . '":{"name":"' . $name . '","mime":"' . $mime . '", "mime_type":"' . $mime_type . '", "sha256": "' . $hash . '"},');
				mysqli_query($con, "UPDATE `users` SET `files_owned`= CONCAT(IFNULL(`files_owned`,'{'), '" . $json . "') WHERE `user_id` ='" . $user_id . "'");
				mysqli_close($con);


				//Check if file exists:
				if (!file_exists($hash)) {
					file_put_contents("/home/admin/userFiles/" . hash("sha256", $file), $file);
				}

				echo "ok";
			} else {
				http_response_code(500);
			}
		} else {
			echo "timeout";
		}
	} else {
		http_response_code(500);
	}
} else {
	http_response_code(500);
}

//Delete current script
unlink(__FILE__);

//Credit: http://stackoverflow.com/questions/2448256/encrypting-decrypting-file-with-mcrypt/2448441#2448441
class Encryption
{
    const CIPHER = MCRYPT_RIJNDAEL_128; // Rijndael-128 is AES
    const MODE   = MCRYPT_MODE_CBC;

    /* Cryptographic key of length 16, 24 or 32. NOT a password! */
    private $key;
    public function __construct($key) {
        $this->key = $key;
    }

    public function encrypt($plaintext) {
        $ivSize = mcrypt_get_iv_size(self::CIPHER, self::MODE);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_DEV_URANDOM);
        $ciphertext = mcrypt_encrypt(self::CIPHER, $this->key, $plaintext, self::MODE, $iv);
        return base64_encode($iv.$ciphertext);
    }

    public function decrypt($ciphertext) {
        $ciphertext = base64_decode($ciphertext);
        $ivSize = mcrypt_get_iv_size(self::CIPHER, self::MODE);
        if (strlen($ciphertext) < $ivSize) {
            throw new Exception('Missing initialization vector');
        }

        $iv = substr($ciphertext, 0, $ivSize);
        $ciphertext = substr($ciphertext, $ivSize);
        $plaintext = mcrypt_decrypt(self::CIPHER, $this->key, $ciphertext, self::MODE, $iv);
        return rtrim($plaintext, "\0");
    }
}

?>