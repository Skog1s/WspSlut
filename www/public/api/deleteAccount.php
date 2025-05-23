<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kontrollerar om användaren är inloggad
if (!isset($_SESSION['uid'])) {
    header("Location: ../index.php?error=notloggedin_deleteaccount");
    exit();
}

// Kontrollera att metoden är POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../settings.php?error=invalid_request_method_deleteaccount");
    exit();
}

$uid = $_SESSION['uid'];

// Inkluderar databasfunktioner
include_once $_SERVER['DOCUMENT_ROOT'] . '/../model/dbFunctions.php';
$db = connectToDb();

if (deleteUserAccount($db, $uid)) {
    // Loggar ut användaren och förstör sessionen
    $_SESSION = array(); 

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();

    header("Location: ../index.php?success=account_deleted");
    exit();
} else {
    error_log("Failed to delete account for user_id: " . $uid);
    header("Location: ../settings.php?error=account_delete_failed");
    exit();
}
?>