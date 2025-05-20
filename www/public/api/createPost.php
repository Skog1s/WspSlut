<?php
session_start();


if (!isset($_SESSION['uid']) || !isset($_SESSION['username'])) {
    header("Location: ../index.php?error=notloggedin");
    exit();
}

if(isset($_POST["post_content"])){
    $posttext = trim($_POST["post_content"]);

    if(!empty($posttext)){
        include_once('../../inc/db.inc.php'); 
        $posttext_sanitized = filter_var($posttext, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $post = "INSERT INTO flow(uid, post_txt, date, username) VALUES( :uid, :post_txt,:date, :username)";
        $stmt = $db->prepare($post);
        $stmt->bindValue(":username", $_SESSION["username"]);
        $stmt->bindValue(":post_txt", $posttext_sanitized); 
        $stmt->bindValue(":uid", $_SESSION['uid']);
        $stmt->bindValue(":date", date("Y-m-d H:i:s"));

        try{
            $stmt->execute();
            header ("location: ../index.php?success=posted");
        }catch(PDOException $e){

            header ("location: ../index.php?error=db_error");
        }
        exit;
    }
    else{
       
        header ("location: ../index.php?error=empty_post");
        exit;
    }
}
else {
   
    header ("location: ../index.php?error=no_post_data");
    exit;
}
?>