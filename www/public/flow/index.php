<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="utf-8" >
    <title>EGY Talk Flöde</title>
	<link rel="stylesheet" href="/css/style.css">
    <link rel="icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>    
	<?php include $_SERVER['DOCUMENT_ROOT'] . '/../inc/header.php'; ?>

	<main>
    <?php   
	    if(isset($_SESSION['uid'])){
            // Show Flow
            
        }else{ 
            // Egen kod!
        } 
    ?>
	</main>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/../inc/footer.php'; ?>
</body>
</html>
