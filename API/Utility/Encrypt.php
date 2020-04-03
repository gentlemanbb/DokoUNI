<?php
    function Encrypt($password){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        return $hash;
    }
?>