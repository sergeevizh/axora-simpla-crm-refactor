<?php

namespace App\Helpers;

class AuthHelper
{
    /**
     * Generate a secure hash for a given password. The cost is passed
     * to the blowfish algorithm. Check the PHP manual page for crypt to
     * find more information about this setting.
     * @param $password
     * @param int $cost
     * @return string|null
     */
    public static function generateHash($password, $cost = 11)
    {
        /* To generate the salt, first generate enough random bytes. Because
         * base64 returns one character for each 6 bits, the we should generate
         * at least 22*6/8=16.5 bytes, so we generate 17. Then we get the first
         * 22 base64 characters
         */
        $salt = substr(base64_encode(openssl_random_pseudo_bytes(17)), 0, 22);
        /* As blowfish takes a salt with the alphabet ./A-Za-z0-9 we have to
         * replace any '+' in the base64 string with '.'. We don't have to do
         * anything about the '=', as this only occurs when the b64 string is
         * padded, which is always after the first 22 characters.
         */
        $salt = str_replace("+", ".", $salt);
        /* Next, create a string that will be passed to crypt, containing all
         * of the settings, separated by dollar signs
         */
        $param = '$' . implode('$', array(
                "2y", //select the most secure version of blowfish (>=PHP 5.3.7)
                str_pad($cost, 2, "0", STR_PAD_LEFT), //add the cost in two digits
                $salt //add the salt
            ));

        //now do the actual hashing
        return crypt($password, $param);
    }

    public static function validatePassword($password, $hash)
    {
        /* Regenerating the with an available hash as the options parameter should
         * produce the same hash if the same password is passed.
         */
        return crypt($password, $hash) === $hash;
    }
}