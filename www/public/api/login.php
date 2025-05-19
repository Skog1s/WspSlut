<?php
    session_start();
    echo "AAAA";
    if(isset($_POST['pwd'], $_POST['user'])){ 
		echo "BBBB";
   	 include_once '../../inc/db.inc.php';
	 echo "CCCC";
   	 $user = filter_input(INPUT_POST, 'user', FILTER_UNSAFE_RAW);
   	 $pwd = $_POST['pwd'];
   	 
   	 /* Bygger upp sql frågan */
   	 $stmt= $db->prepare("SELECT * FROM userInfo WHERE username = :user");
   	 $stmt->bindValue(":user", $user);
   	 
   	 $stmt->execute();
   	 
   	 /** Kontroll att resultat finns */
   	 if($stmt->rowCount() == 1){
		echo "DDDD";
   		 // Hämtar användaren, kan endast kunna vara 1 person
   		 $user = $stmt->fetch(PDO::FETCH_ASSOC);
   		 // Kontrollerar lösenordet, och allt ok.
   		 if(password_verify($pwd, $user['pwd'])){
   			 session_regenerate_id(true);
   			 
   			 $_SESSION['uid'] = $user['uid'];
   			 $_SESSION['username'] = $user['username'];
   			 $_SESSION['name'] = $user['surname']." ".$user['firstname'];

   			 header("Location: ../index.php");
   			 exit();
   		 }
   	   }
     }
?>
