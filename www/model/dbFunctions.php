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
    define ('DB_NAME', 'db');
    
    // Skapar en anslutning till MySql och databasen egytalk
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $db = new PDO($dsn, DB_USER, DB_PASSWORD);
    
    return $db;
}

/**
 * Kontrollerar av användare och lösen.
 *
 * @param  $db  databasobjekt (pdo)
 * @param  $username  Användarnamn
 * @param  $pwd  Lösenord
 * @return $response användardata eller tom [] om inloggning misslyckas
 */
function getUser($db, $username, $pwd) {
    $response = [];

    try {
        /* Bygger upp sql frågan */
        $stmt = $db->prepare("SELECT * FROM user WHERE username = :user");
        $stmt->bindValue(":user", $username);
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
        if (password_verify($pwd, $user['pwd'])) {
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

    try {
        $sqlkod = "SELECT post.*, user.firstname, user.surname, user.username 
                   FROM post NATURAL JOIN user 
                   WHERE post.uid = :uid 
                   ORDER BY post.date DESC";
        $stmt = $db->prepare($sqlkod);
        $stmt->bindValue(":uid", $uid);
        $stmt->execute();
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(Exception $e) {}
    
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
        FROM post NATURAL JOIN user ORDER BY post.date DESC LIMIT 0,30";
    
        /* Kör frågan mot databasen egytalk och tabellen post */
        $stmt = $db->prepare($sqlkod);
        $stmt->execute();

        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }catch(Exception $e){}

    return $response;
}

/**
 * Hämtar alla kommentarer för ett specifikt inlägg.
 *
 * @param PDO $db PDO-objekt för databasanslutning.
 * @param int $pid ID för inlägget vars kommentarer ska hämtas.
 * @return array En array med kommentarer, sorterade efter datum.
 */
function getCommentsByPostId($db, $pid) {
    $response = [];
    try {
        $sql = "SELECT c.*, u.username 
                FROM comment c
                JOIN user u ON c.uid = u.uid
                WHERE c.pid = :pid 
                ORDER BY c.date ASC"; // Ändra till DESC för nyaste först om så önskas
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':pid', $pid, PDO::PARAM_INT); // Antagande att pid är INT
        $stmt->execute();
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Här kan du logga felet, t.ex. error_log($e->getMessage());
    }
    return $response;
}

/**
 * Lägger till en ny kommentar i databasen.
 *
 * @param PDO $db PDO-objekt för databasanslutning.
 * @param int $pid ID för inlägget som kommenteras.
 * @param string $uid ID för användaren som skriver kommentaren.
 * @param string $commentText Kommentartexten.
 * @return bool True om kommentaren lades till, annars false.
 */
function addComment($db, $pid, $uid, $commentText) {
    try {
        $sql = "INSERT INTO comment (pid, uid, comment_txt, date) VALUES (:pid, :uid, :comment_txt, :date)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':pid' => $pid, ':uid' => $uid, ':comment_txt' => $commentText, ':date' => date("Y-m-d H:i:s")]);
    } catch (PDOException $e) {
        // Här kan du logga felet, t.ex. error_log($e->getMessage());
        return false;
    }
}

/**
 * Deletes a post and all its associated comments, ensuring the user owns the post.
 *
 * @param PDO $db PDO-objekt för databasanslutning.
 * @param int $postId ID för inlägget som ska raderas.
 * @param string $userId ID för användaren som försöker radera.
 * @return bool True om raderingen lyckades, annars false.
 */
function deletePostAndComments($db, $postId, $userId) {
    try {
        // Verify ownership
        $stmt = $db->prepare("SELECT uid FROM post WHERE pid = :pid");
        $stmt->bindValue(':pid', $postId, PDO::PARAM_INT);
        $stmt->execute();
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$post || $post['uid'] !== $userId) {
            error_log("Delete post failed: Post not found or user mismatch. PostID: $postId, UserID: $userId");
            return false; // Post not found or user does not own the post
        }

        // Start transaction
        $db->beginTransaction();

        // Delete comments associated with the post
        $stmtComments = $db->prepare("DELETE FROM comment WHERE pid = :pid");
        $stmtComments->bindValue(':pid', $postId, PDO::PARAM_INT);
        $stmtComments->execute();

        // Delete the post
        $stmtPost = $db->prepare("DELETE FROM post WHERE pid = :pid AND uid = :uid");
        $stmtPost->bindValue(':pid', $postId, PDO::PARAM_INT);
        $stmtPost->bindValue(':uid', $userId, PDO::PARAM_STR);
        $stmtPost->execute();

        if ($stmtPost->rowCount() > 0) {
            $db->commit();
            return true;
        } else {
            $db->rollBack();
            error_log("Delete post failed: Post deletion affected 0 rows. PostID: $postId, UserID: $userId");
            return false;
        }
    } catch (PDOException $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log("Error deleting post (PDOException): " . $e->getMessage() . " PostID: $postId");
        return false;
    }
}

/**
 * Deletes a specific comment, ensuring the user owns the comment.
 * (Assumes 'cid' is the primary key for the 'comment' table).
 *
 * @param PDO $db PDO-objekt för databasanslutning.
 * @param int $commentId ID för kommentaren som ska raderas.
 * @param string $userId ID för användaren som försöker radera.
 * @return bool True om raderingen lyckades (minst en rad påverkades), annars false.
 */
function deleteComment($db, $commentId, $userId) {
    try {
        $stmt = $db->prepare("DELETE FROM comment WHERE cid = :cid AND uid = :uid");
        $stmt->bindValue(':cid', $commentId, PDO::PARAM_INT);
        $stmt->bindValue(':uid', $userId, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error deleting comment (PDOException): " . $e->getMessage() . " CommentID: $commentId");
        return false;
    }
}

/**
 * Deletes a user account and all associated data (posts, comments on their posts, comments made by them).
 *
 * @param PDO $db PDO-objekt för databasanslutning.
 * @param string $userId ID för användaren som ska raderas.
 * @return bool True om raderingen lyckades, annars false.
 */
function deleteUserAccount($db, $userId) {
    try {
        $db->beginTransaction();

        // 1. Delete comments ON posts owned by the user
        // (This deletes comments made by anyone on this user's posts)
        $stmtCommentsOnUserPosts = $db->prepare("DELETE FROM comment WHERE pid IN (SELECT pid FROM post WHERE uid = :uid)");
        $stmtCommentsOnUserPosts->bindValue(':uid', $userId, PDO::PARAM_STR);
        $stmtCommentsOnUserPosts->execute();

        // 2. Delete posts owned by the user
        $stmtUserPosts = $db->prepare("DELETE FROM post WHERE uid = :uid");
        $stmtUserPosts->bindValue(':uid', $userId, PDO::PARAM_STR);
        $stmtUserPosts->execute();

        // 3. Delete comments MADE by the user (on other users' posts)
        $stmtCommentsByUser = $db->prepare("DELETE FROM comment WHERE uid = :uid");
        $stmtCommentsByUser->bindValue(':uid', $userId, PDO::PARAM_STR);
        $stmtCommentsByUser->execute();

        // 4. Delete the user record
        $stmtUser = $db->prepare("DELETE FROM user WHERE uid = :uid");
        $stmtUser->bindValue(':uid', $userId, PDO::PARAM_STR);
        $stmtUser->execute();

        if ($stmtUser->rowCount() > 0) { // Check if the user was actually deleted
            $db->commit();
            return true;
        } else {
            $db->rollBack(); // User not found or already deleted, rollback other changes if any were made.
            error_log("Delete user account failed: User not found or no rows affected for user deletion. UserID: $userId");
            return false;
        }
    } catch (PDOException $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log("Error deleting user account (PDOException): " . $e->getMessage() . " UserID: $userId");
        return false;
    }
}
