<?php

function sessionMake()
{
    ini_set('session.use_strict_mode', 1);//Keeping things secure
    session_set_cookie_params([
      'lifetime' => 0, //Whenever the browser closes
      'path' => '/',
      'domain' => '',         
      'secure' => false, //Just in case      
      'httponly' => true,     
      'samesite' => 'Lax',   
    ]); 
}