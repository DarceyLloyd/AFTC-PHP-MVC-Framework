<?php

namespace AFTC\Libs;

use AFTC\Config\Config;

class EncryptionLib
{
    // CBC doesn't require an iv to be stored
    // We need this as we are encrypting array & session keys
    private string $cipher = "aes-128-cbc";
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


    public function __construct()
    {
        // $this->key = base64_encode($this->key);
    }
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


    public function encrypt($value):string
    {
        return base64_encode(openssl_encrypt(
            $value,
            $this->cipher,
            Config::$encryption_key,
            OPENSSL_RAW_DATA,
            Config::$encryption_iv
        ));

        // Following examples need an IV we cant use one for what we need, not as secure but will do
        // $ivlen = openssl_cipher_iv_length($this->cipher);
        // $iv = openssl_random_pseudo_bytes($ivlen);
        // $encrypted = openssl_encrypt($value, $this->cipher, Config::$encryption_key, $options=0, $iv, $tag);
        // return $encrypted;

        // $key = base64_decode( $this->key );
        // $iv = openssl_random_pseudo_bytes( openssl_cipher_iv_length( $this->cipher ) );
        // $tag = ""; // openssl_encrypt will fill this
        // $result = openssl_encrypt( $value , $this->cipher , $key , OPENSSL_RAW_DATA , $iv , $tag , "" , 12 );
        // return base64_encode( $iv.$tag.$result );
    }
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


    public function decrypt($value):string
    {
        return openssl_decrypt(
            base64_decode($value),
            $this->cipher,
            Config::$encryption_key,
            OPENSSL_RAW_DATA,
            Config::$encryption_iv
        );

        // Following examples need an IV we cant use one for what we need, not as secure but will do
        // $value = base64_decode( $value );
        // $key = base64_decode( $this->key );
        // $ivLength = openssl_cipher_iv_length( $this->cipher );
        // $iv = substr( $value , 0 , $ivLength );
        // $tag = substr( $value , $ivLength , 16 );
        // $text = substr( $value , $ivLength+16 );
        // return openssl_decrypt( $text , $this->cipher , $key , OPENSSL_RAW_DATA , $iv , $tag );
    }
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
}
