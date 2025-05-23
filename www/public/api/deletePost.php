<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kontrollera om användaren är inloggad
if (!isset($_SESSION['uid'])) {
    header("Location: ../index.php?error=notloggedin_deletepost");
    exit();
}

// Kontrollera att metoden är POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php?error=invalid_request_method_deletepost");
    exit();
}

// Kontrollera att post_id finns
if (!isset($_POST["post_id"])) {
    header("Location: ../index.php?error=missing_post_id_deletepost");
    exit();
}

$postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
$userId = $_SESSION['uid'];
$origin = isset($_POST['origin']) && ($_POST['origin'] === 'flow' || $_POST['origin'] === 'index') ? $_POST['origin'] : 'index';
$redirectPage = ($origin === 'flow') ? "../flow.php" : "../index.php";

if ($postId === false || $postId === null) {
    header("Location: " . $redirectPage . "?error=invalid_post_id_deletepost");
    exit();
}

// Inkludera databasfunktioner
include_once $_SERVER['DOCUMENT_ROOT'] . '/../model/dbFunctions.php';
$db = connectToDb();

if (deletePostAndComments($db, $postId, $userId)) {
    header("Location: " . $redirectPage . "?success=post_deleted");
    exit();
} else {
    // Något gick fel vid radering, eller ingen behörighet
    error_log("Failed to delete post_id: " . $postId . " by user_id: " . $userId . " from origin: " . $origin);
    // Lägg till #post-id om det misslyckas så användaren kommer tillbaka till rätt ställe
    $anchor = ($postId) ? "#post-" . urlencode($postId) : "";
    header("Location: " . $redirectPage . "?error=post_delete_failed" . $anchor);
    exit();
}
?>