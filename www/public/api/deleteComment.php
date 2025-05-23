<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kontrollera om användaren är inloggad
if (!isset($_SESSION['uid'])) {
    header("Location: ../index.php?error=notloggedin_deletecomment");
    exit();
}

// Kontrollera att metoden är POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php?error=invalid_request_method_deletecomment");
    exit();
}

// Kontrollera att comment_id finns
if (!isset($_POST["comment_id"])) {
    header("Location: ../index.php?error=missing_comment_id_deletecomment");
    exit();
}

$commentId = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
$userId = $_SESSION['uid'];
$postIdForRedirect = isset($_POST["post_id_for_redirect"]) ? filter_input(INPUT_POST, 'post_id_for_redirect', FILTER_VALIDATE_INT) : null;
$origin = isset($_POST['origin']) && ($_POST['origin'] === 'flow' || $_POST['origin'] === 'index') ? $_POST['origin'] : 'index';
$redirectPage = ($origin === 'flow') ? "../flow.php" : "../index.php";

$redirectSuffix = $postIdForRedirect ? "#post-" . urlencode($postIdForRedirect) : "";

if ($commentId === false || $commentId === null) {
    header("Location: " . $redirectPage . "?error=invalid_comment_id_deletecomment" . $redirectSuffix);
    exit();
}

// Inkludera databasfunktioner
include_once $_SERVER['DOCUMENT_ROOT'] . '/../model/dbFunctions.php';
$db = connectToDb();

if (deleteComment($db, $commentId, $userId)) {
    header("Location: " . $redirectPage . "?success=comment_deleted" . $redirectSuffix);
    exit();
} else {
    // Något gick fel vid radering, eller ingen behörighet
    error_log("Failed to delete comment_id: " . $commentId . " by user_id: " . $userId . " for post_id: " . $postIdForRedirect . " from origin: " . $origin);
    header("Location: " . $redirectPage . "?error=comment_delete_failed" . $redirectSuffix);
    exit();
}
?>