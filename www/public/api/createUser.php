<?php    
if(isset($_POST['firstname'],$_POST['surname'],$_POST['username'],$_POST['pwd'])){        
    include_once('../../inc/db.inc.php');    
   
    $fname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_SPECIAL_CHARS);
    $sname = filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_SPECIAL_CHARS);
    $user = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $pwd = password_hash($_POST['pwd'], PASSWORD_DEFAULT);
    
    /* Bygger upp sql frågan */
    $stmt= $db->prepare("INSERT INTO user(uid, firstname, surname, username, pwd) VALUES(UUID(), :fn, :sn,:user,:pwd)");
    
    $stmt->bindValue(":fn", $fname);
    $stmt->bindValue(":sn", $sname);
    $stmt->bindValue(":user", $user);
    $stmt->bindValue(":pwd", $pwd);
    
    // Om INSERT gick bra!
    //try{
        $stmt->execute();
        header('Location: ../index.php'); // Borde visa att allt gick bra!
    // }catch(Exception $e){
    //     header('Content-Type: text/html; charset=utf-8');
    //     echo "<p>Kunde inte lägga till användaren. Kontrollera användarnamnet</p>";
    //     echo "<a href = 'addUserForm.html'>Försök igen</a>";
    // }
}
?> 