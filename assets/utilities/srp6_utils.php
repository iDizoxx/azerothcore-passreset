<?php
function CalculateSRP6Verifier($username, $password, $salt) {
    $g = gmp_init(7);
    $N = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);

    $h1 = sha1(strtoupper($username . ':' . $password), TRUE);
    $h2 = sha1($salt . $h1, TRUE);

    $h2 = gmp_import($h2, 1, GMP_LSW_FIRST);
    $verifier = gmp_powm($g, $h2, $N);
    $verifier = gmp_export($verifier, 1, GMP_LSW_FIRST);

    return str_pad($verifier, 32, chr(0), STR_PAD_RIGHT);
}

function GetSRP6RegistrationData($username, $password) {
    $salt = random_bytes(32);
    $verifier = CalculateSRP6Verifier($username, $password, $salt);
    return array($salt, $verifier);
}
?>