<?php

//Sessions
session_start();
session_write_close();

if (isset($_SESSION["user_id"]) && isset($_SESSION["name"])) {
	$crypt = new Encryption("wNVBNjGYcbfBMK8YzSaYDbA9ERpgFyPR");
	$secret = $crypt->encrypt(bin2hex(openssl_random_pseudo_bytes(mt_rand(10,40))) . ":" . $_SESSION["user_id"] . ":" . bin2hex(openssl_random_pseudo_bytes(mt_rand(15,55))));

	$domains = array("cloudpack3.ml", "cloudpack4.ml", "cloudpack5.ml", "cloudpack6.ml", "cloudpack7.ml", "cloudpack8.ml", "cloudpack9.ml", "cloudpack10.ml");

	while (true) {
		$domain = $domains[mt_rand(0,7)];
		$fileNumber = mt_rand(0,10000);
		if (!file_exists("/home/admin/www_fileTransferDomains/downloadFile" . $fileNumber . ".php") && (gethostbyname($domain) == "78.156.116.170")) {
			copy("/home/admin/template/downloadFileTemplate.php", "/home/admin/www_fileTransferDomains/downloadFile" . $fileNumber . ".php");
			header("HTTP/1.1 307 Temporary Redirect");
			header("Location: https://" . $domain . "/downloadFile" . $fileNumber . ".php?secret=" . urlencode($secret)); 
			break;
		}
	}	
}


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