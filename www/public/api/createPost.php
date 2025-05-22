<?php
session_start();

// Kollar så användaren är inloggad
if (!isset($_SESSION['uid']) || !isset($_SESSION['username'])) {
    header("Location: ../index.php?error=notloggedin");
    exit();
}

// Kollar så den använda metoden är POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php?error=invalid_request_method");
    exit();
}

// Kollar om post_content har skickats
if (!isset($_POST["post_content"])) {
    header ("location: ../index.php?error=no_post_data");
    exit;
}

$posttext = trim($_POST["post_content"]);

if (empty($posttext)) {
    header ("location: ../index.php?error=empty_post");
    exit;
}

include_once('../../inc/db.inc.php');

// Sanerar texten från potentiellt farliga karaktärer
$posttext_sanitized = htmlspecialchars($posttext, ENT_QUOTES, 'UTF-8');

$sql = "INSERT INTO post(uid, post_txt, date, username) VALUES (:uid, :post_txt, :date, :username)";
$stmt = $db->prepare($sql);

$stmt->bindValue(":uid", $_SESSION['uid']);
$stmt->bindValue(":username", $_SESSION["username"]);
$stmt->bindValue(":post_txt", $posttext_sanitized);
$stmt->bindValue(":date", date("Y-m-d H:i:s"));

try {
    $stmt->execute();
    header ("location: ../index.php?success=posted");
} catch (PDOException $e) {
    // TEMPORARY: Display the actual error message for debugging
    echo "Database Error: " . $e->getMessage();
    
}
exit();
?>