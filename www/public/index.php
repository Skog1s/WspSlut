<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>EGY Talk</title>
	<link rel="stylesheet" href="/css/style.css">
    <link rel="icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>    
	<?php include $_SERVER['DOCUMENT_ROOT'] . '/../inc/header.php'; ?>

	<main>
    <?php   

if(isset($_GET['login'])){
    $login = true;
}else{
    $login = false;
}

if(isset($_SESSION['uid'])){
        // Show Flow ?>
        <section class="create-post-section">
            <h2><?php echo htmlspecialchars($_SESSION['username']); ?> flow</h2>
            <form action="/api/createPost.php" method="POST" class="create-post-form">
                <div>
                    <label for="post_content">Skriv en kommentar!</label>
                    <textarea id="post_content" name="post_content" rows="4" required placeholder="Skriv här..."></textarea>
                </div>
                <button type="submit" class="button">Post</button>
            </form>
        </section>
        <hr> <?php 

} elseif($login == true){
    include $_SERVER['DOCUMENT_ROOT'] . '/../inc/signIn.php';
}else if($login == false){ 
    include $_SERVER['DOCUMENT_ROOT'] . '/../inc/signUp.php';
}
    ?>
	</main>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/../inc/footer.php'; ?>
</body>
</html>

<style>
.create-post-section {
    background-color: #ffffff;
    padding: 20px;
    margin: 30px auto;
    max-width: 700px; 
    width: 100%;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); 
    color: #000000; 
    border: 1px solid #000000;

.create-post-section h2 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #000000; 
    font-size: 1.5em;
    border-bottom: 2px solid #cc0000; 
    padding-bottom: 10px; 
}

.create-post-form div {
    margin-bottom: 15px; 
}

.create-post-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #000000; 
}

.create-post-form textarea {
    width: 100%;
    padding: 10px;
    background-color: #ffffff; 
    color: #000000;
    border: 1px solid #333333;
    border-radius: 4px;
    box-sizing: border-box;
    font-family: inherit; 
    font-size: 1em;
    resize: none; 
    transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.create-post-form textarea:focus {
    border-color: #cc0000; 
    box-shadow: 0 0 0 0.2rem rgba(204, 0, 0, 0.25); 
}

.create-post-form textarea::placeholder {
    color: #666; 
}

.create-post-form .button {
    padding: 10px 20px;
    background-color: #cc0000; 
    color: #ffffff;
    border: 1px solid #a30000;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1em;
    transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
}

.create-post-form .button:hover {
    background-color: #a30000;
    border-color: #7a0000; 
}

hr {
    border: 0;
    height: 1px;
    background-color: #333;
    margin-top: 20px;
    margin-bottom: 20px;
}
</style>