<?php

/**
 * Instans av klassen skapar en koppling till databasen egytalk
 * och tillhandahåller ett antal metoder för att hämta och manipulera data i databasen.
 *
 */
class DbEgyTalk
{
    /**
     * Används i metoder med    $this->db
     */
    private $db;

    /**
     * DbEgyTalk constructor.
     *
     * Skapar en koppling till databaseb egytalk
     */
    public function __construct(){
        // Definierar konstanter med användarinformation.
        define('DB_USER', 'egytalk');
        define('DB_PASSWORD', '12345');
        define('DB_HOST', 'mariadb');
        define('DB_NAME', 'egytalk');

        // Skapar en anslutning till MySql och databasen egytalk
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
        $this->db = new PDO($dsn, DB_USER, DB_PASSWORD);
    }

  /**
     * Hämtar alla inlägg från flödet.
     *
     * @return array En array med alla inlägg, sorterade efter datum (nyast först).
     */
    function getAllPosts(){
        // Ensure you select all necessary columns, e.g., uid, username, post_txt, date
        $stmt = $this->db->prepare("SELECT uid, username, post_txt, date FROM flow ORDER BY date DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Hämtar poster för en användare,
     * sorterade efter publiceringsdatum
     *
     * @param   $uid    användar-ID för användaren
     * @param   $limit  antal poster, default = 10
     * @return  array med statusuppdateringar sorterade efter datum
     */
    function getPosts($uid, $limit = 10) {
        $posts = [];
        
        // Egen kod

        return $posts;
    }

    /**
     * Hämtar alla kommentarer till en post
     *
     * @param  $pid postens ID-nummer
     * @param   $limit  antal kommentarer, default = 5
     * @return array med kommentarer sorterade efter datum
     */
    function getComments($pid, $limit = 5) {
        $comments = [];

        // Egen kod

        return $comments;
    }

    /**
     * Kontrollerar av användare och lösen.
     * Skapar global sessions-array med användarinformation.
     *
     * @param  $username  Användarnamn
     * @param  $password  Lösenord
     * @return $response användardata eller tom [] om inloggning misslyckas
     */
    function getUser($username, $pwd) {
        $userName = trim(filter_var($username, FILTER_UNSAFE_RAW));
        $response = [];

        /* Bygger upp sql frågan */
        $stmt = $this->db->prepare("SELECT * FROM user WHERE username = :user");
        $stmt->bindValue(":user", $username);
        $stmt->execute();


        /** Kontroll att resultat finns */
        if ($stmt->rowCount() == 1) {
            // Hämtar användaren, kan endast vara 1 person
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // Kontrollerar lösenordet, och allt ok.
            if (password_verify($pwd, $user['password'])) {
                $response['uid'] = $user['uid'];
                $response['username'] = $user['username'];
                $response['firstname'] = $user['firstname'];
                $response['surname'] = $user['surname'];
            }
        }

        return $response;
    }

    /**
     * Hämtar anvädardata från användare med secifikt användarID
     * 
     * @param  $uid  användarID
     * @return $response användardata eller tom [] om ingen anvädare hittats eller fel inträffat
     */
    function getUserFromUid($uid) {
        $response = [];

        // Egen kod!

        return $response;
    }

    /**
     * Skapar ny samtalstråd.
     *
     * @param  $uid       Användar-ID
     * @param  $postTxt   Postat inlägg
     * @return true om det lyckades, annars false
     */
    function addPost($uid, $postTxt){
        $stmt = $this->db->prepare("INSERT INTO flow(uid, post_txt, date) VALUES(:uid, :post, :date)");

        $stmt->bindValue(":uid", $uid);
        $stmt->bindValue(":post", $postTxt);
        $stmt->bindValue(":date", date("Y-m-d h:i:s"));

        return $stmt->execute();
    }

    /**
     * Lägger till en ny kommentar till en post.
     *
     * @param  $userID    Användar-ID för den som skriver kommentaren
     * @param  $statusID  Status-ID för statusuppdatering som kommenteras
     * @param  $comment   Kommentar
     * @return true om det lyckades, annars false
     */
    function addComment($uid, $pid, $comment) {
        $pid = filter_var($pid, FILTER_SANITIZE_NUMBER_INT);
        $comment = filter_var($comment, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Egen kod!

        return $stmt->execute();
    }

    /**
     * Lägger till en ny användare
     *
     * @param  $fname   Förnamn
     * @param  $sname   Efternamn
     * @param  $user    Användarnamn
     * @param  $pwd     Lösenord
     * @return true om det lyckades, annars false
     */
    function addUser($fname, $sname, $user, $pwd){
        $fname = filter_var($fname, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $sname = filter_var($sname, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $user = filter_var($user, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $pwd = password_hash($pwd, PASSWORD_DEFAULT);

        // Egen kod!

        return $stmt->execute();
    }

    /**
     * Söker efter användare.
     *
     * @param  $user    Sökord
     * @return array med användare
     */
    function findUsers($user){
        $searchWord = filter_var($user, FILTER_UNSAFE_RAW);

        /* Bygger upp sql frågan */
        $stmt = $this->db->prepare("SELECT * FROM user WHERE firstname LIKE :search");
        $stmt->bindValue(":search", "%$user%");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
