<?php
if (session_status() == PHP_SESSION_NONE)  session_start();

include_once('../model/dbFunctions.php');

/**
 * Kontrollerar av användare och lösen.
 * Skapar global sessions-array med användarinformation.
 *
 * @param  $userName  Användarnamn
 * @param  $passWord  Lösenord
 * @return $response användardata eller tom [] om inloggning misslyckas
 */
function auth($userName, $password){
   $success = false;
   $userName = trim(filter_var($userName, FILTER_SANITIZE_SPECIAL_CHARS));

   $db = connectToDb();
   $response = getUser($db, $userName, $password);

   if (!empty($response)) {
      session_regenerate_id();

      $_SESSION['uid'] = $response['uid'];
      $_SESSION['username'] = $response['username'];
      $_SESSION['firstname'] = $response['firstname'];
      $_SESSION['surname'] = $response['surname'];

      $success = true;
   }

   return $success;
}

/**
 * Hämtar användares status-uppdateringar i tabellen post
 *
 * @param $uid Användarens uid
 * @return array med alla status-uppdateringar
 */
function getPostsFromUser($uid){
   if(!isset($_SESSION['uid']))
      return [];

   $db = connectToDb();
   return getPosts($db, $uid);
}
/**
 * Loggar ut genom att ta bort sessionen
 */
function logOut(){
   $_SESSION = array();
   session_destroy();
}
