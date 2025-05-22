<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kontrollera om användaren är inloggad
if (!isset($_SESSION['uid'])) {
    header("Location: ../index.php?error=notloggedin_comment");
    exit();
}

// Kontrollera att metoden är POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php?error=invalid_request_method_comment");
    exit();
}

// Kontrollera att post_id och comment_content finns 
if (!isset($_POST["post_id"]) || !isset($_POST["comment_content"])) {
    header("Location: ../index.php?error=missing_comment_data");
    exit();
}

$postId = $_POST["post_id"];
$commentText = trim($_POST["comment_content"]);
$userId = $_SESSION['uid'];

if (empty($commentText)) {
    header("Location: ../index.php?error=empty_comment#post-" . urlencode($postId));
    exit();
}

// Inkludera databasfunktioner
include_once $_SERVER['DOCUMENT_ROOT'] . '/../model/dbFunctions.php';

// Sanera kommentaren
$commentTextSanitized = htmlspecialchars($commentText, ENT_QUOTES, 'UTF-8');

$db = connectToDb();

// Lägg till kommentaren i databasen
if (addComment($db, $postId, $userId, $commentTextSanitized)) {
    header("Location: ../index.php?success=comment_posted#post-" . urlencode($postId));
    exit();
} else {
    // Något gick fel vid sparning, logga detta server-side
    error_log("Failed to add comment for post_id: " . $postId . " by user_id: " . $userId);
    header("Location: ../index.php?error=comment_failed#post-" . urlencode($postId));
    exit();
}
?>