<?php
/**
* Anluter till databasen och returnerar ett PDO-objekt
* @return PDO  Objektet som returneras
*/
function connectToDb(){
    // Definierar konstanter med användarinformation.
    define ('DB_USER', 'egytalk');
    define ('DB_PASSWORD', '12345');
    define ('DB_HOST', 'mariadb'); // mariadb om docker annars localhost
    define ('DB_NAME', 'egytalk');
    
    // Skapar en anslutning till MySql och databasen egytalk
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
    $db = new PDO($dsn, DB_USER, DB_PASSWORD);
    
    return $db;
}

/**
 * Kontrollerar av användare och lösen.
 *
 * @param  $db  databasobjekt (pdo)
 * @param  $userName  Användarnamn
 * @param  $passWord  Lösenord
 * @return $response användardata eller tom [] om inloggning misslyckas
 */
function getUser($db, $userName, $password) {
    $response = [];

    try {
        /* Bygger upp sql frågan */
        $stmt = $db->prepare("SELECT * FROM user WHERE username = :user");
        $stmt->bindValue(":user", $userName);
        $stmt->execute();
    } catch(Exception $e){
        /** Tom array om anropet misslyckas */
        return $response;
    } 

    /** Kontroll att resultat finns */
    if ($stmt->rowCount() == 1) {
        // Hämtar användaren, kan endast vara 1 person
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // Kontrollerar lösenordet, och allt ok.
        if (password_verify($password, $user['password'])) {
            $response = $user;
        }
    }
    
    return $response;
}

/**
 * Hämtar användares status-uppdateringar i tabellen post
 *
 * @param $db PDO-objekt
 * @param $uid Användarens uid
 * @return array med alla status-uppdateringar
 */
function getPosts($db, $uid){
    $response = [];

    // Egen kod!
    
    return $response;
}
 
/**
* Hämtar alla status-uppdateringar i tabellen post
*
* @param $db PDO-objekt
* @return array med alla status-uppdateringar
*/
function getAllPosts($db){
    $response = [];

    try{
        $sqlkod = "SELECT post.*, user.firstname, user.surname, user.username 
        FROM post NATURAL JOIN user ORDER BY post.date LIMIT 0,30";
    
        /* Kör frågan mot databasen egytalk och tabellen post */
        $stmt = $db->prepare($sqlkod);
        $stmt->execute();

        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }catch(Exception $e){}

    return $response;
}
