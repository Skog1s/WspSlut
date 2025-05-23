<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>Globalt Flöde - EGY Talk</title>
	<link rel="stylesheet" href="/css/style.css">
    <link rel="icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>    
	<?php include $_SERVER['DOCUMENT_ROOT'] . '/../inc/header.php'; ?>

	<main>
    <section class="all-posts-section">
        <h2>Globalt flöde</h2>
        <?php   
        // Inkludera dbFunctions för att få tillgång till databasfunktioner
        include_once $_SERVER['DOCUMENT_ROOT'] . '/../model/dbFunctions.php'; 

        $db = connectToDb();
        $allPosts = getAllPosts($db); 

        if (!empty($allPosts)) {
            foreach ($allPosts as $post) {
                $postId = $post['pid'] ?? null; // Säkerställer att pid finns

                echo "<article class='post' id='post-" . htmlspecialchars($postId) . "'>";
                echo "<h3>" . htmlspecialchars($post['username']) . "</h3>"; 
                echo "<p class='post-date'>" . htmlspecialchars($post['date']) . "</p>";
                echo "<p>" . htmlspecialchars($post['post_txt']) . "</p>";

               
                if (isset($_SESSION['uid']) && isset($post['uid']) && $_SESSION['uid'] == $post['uid']) {
                    echo "<form action='./api/deletePost.php' method='POST' class='delete-form'>";
                    echo "<input type='hidden' name='post_id' value='" . htmlspecialchars($postId) . "'>";
                    echo "<input type='hidden' name='origin' value='flow'>"; 
                    echo "<button type='submit' class='button delete-button' onclick=\"return confirm('Är du säker på att du vill radera detta inlägg och alla dess kommentarer?');\">Radera Inlägg</button>";
                    echo "</form>";
                }

                if ($postId) { // Visa bara kommentarer om postID finns
                    $comments = getCommentsByPostId($db, $postId);

                    echo "<div class='comments-section'>";
                    if (!empty($comments)) {
                        echo "<h4>Kommentarer:</h4>";
                        foreach ($comments as $comment) {
                            echo "<div class='comment'>";
                            echo "<p class='comment-author'><strong>" . htmlspecialchars($comment['username']) . "</strong> (" . htmlspecialchars($comment['date']) . "):</p>";
                            echo "<p class='comment-text'>" . htmlspecialchars($comment['comment_txt']) . "</p>";

                            if (isset($_SESSION['uid']) && isset($comment['uid']) && $_SESSION['uid'] == $comment['uid'] && isset($comment['cid'])) {
                                echo "<form action='./api/deleteComment.php' method='POST' class='delete-form comment-delete-form'>";
                                echo "<input type='hidden' name='comment_id' value='" . htmlspecialchars($comment['cid']) . "'>";
                                echo "<input type='hidden' name='post_id_for_redirect' value='" . htmlspecialchars($postId) . "'>";
                                echo "<input type='hidden' name='origin' value='flow'>"; 
                                echo "<button type='submit' class='button delete-button comment-delete-button' onclick=\"return confirm('Är du säker på att du vill radera denna kommentar?');\">Radera Kommentar</button>";
                                echo "</form>";
                            }
                            echo "</div>";
                        }
                    } else {
                        echo "<p class='no-comments'>Inga kommentarer än.</p>";
                    }

                    // Modul för att skriva kommentar
                    echo "<form action='./api/createComment.php' method='POST' class='comment-form'>";
                    echo "<input type='hidden' name='post_id' value='" . htmlspecialchars($postId) . "'>";
                    echo "<div><label for='comment_content_" . htmlspecialchars($postId) . "'>Lämna en kommentar:</label>";
                    echo "<textarea id='comment_content_" . htmlspecialchars($postId) . "' name='comment_content' rows='2' required placeholder='Skriv din kommentar här...'></textarea></div>";
                    echo "<button type='submit' class='button comment-button'>Kommentera</button>";
                    echo "</form></div>";
                }
                echo "</article>";
            }
        } else {
            echo "<p class='center'>Det finns inga inlägg i flödet än.</p>";
        }
        ?>
    </section>
	</main>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/../inc/footer.php'; ?>
</body>
</html>

<style>
    create-post-section {
    background-color: #ffffff;
    padding: 20px;
    margin: 30px auto;
    max-width: 700px; 
    width: 100%;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); 
    color: #000000; 
    border: 1px solid #000000;
}

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

.all-posts-section { 
    background-color: #ffffff;
    padding: 20px;
    margin: 20px auto; 
    max-width: 700px; 
    width: 100%;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); 
    color: #000000; 
    border: 1px solid #000000;
}

.all-posts-section h2 { 
    margin-top: 0;
    margin-bottom: 15px;
    color: #000000; 
    font-size: 1.5em; 
    border-bottom: 2px solid #cc0000; 
    padding-bottom: 10px; 
}

.post {
    border-bottom: 1px solid #eeeeee; 
    padding-bottom: 15px;
    margin-bottom: 15px;
}

.post:last-child { 
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.post h3 { 
    margin-top: 0;
    margin-bottom: 5px;
    font-size: 1.1em;
}
.post-date { 
    font-size: 0.85em;
    color: #555555; 
    margin-bottom: 10px;
}

.comments-section {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #e0e0e0;
}

.comments-section h4 {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 1.1em; 
    color: #333333;
}

.comment {
    margin-bottom: 10px;
    padding: 10px;
    background-color: #f9f9f9; 
    border-radius: 4px;
    border: 1px solid #eee;
}

.comment:last-child {
    margin-bottom: 0;
}

.comment-author {
    font-size: 0.9em;
    color: #555555;
    margin-bottom: 4px;
}

.comment-author strong {
    color: #000000;
}

.comment-text {
    font-size: 0.95em;
    line-height: 1.5;
    color: #222222;
    word-wrap: break-word; 
}

.no-comments {
    font-style: italic;
    color: #777777;
    font-size: 0.9em;
    margin-top: 10px;
}

.comment-form {
    margin-top: 15px;
}

.comment-form textarea {
    width: 100%;
    padding: 8px;
    background-color: #ffffff; 
    color: #000000;
    border: 1px solid #cccccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-family: inherit;
    font-size: 0.95em;
    resize: none; 
    min-height: 60px;
}

.comment-form textarea:focus {
    border-color: #cc0000; 
    box-shadow: 0 0 0 0.2rem rgba(204, 0, 0, 0.25); 
}

.comment-form .comment-button { 
    margin-top: 10px;
    
}

.delete-form {
    display: block; /* Gör att knappen hamnar på en ny rad */
    margin-top: 10px;
    margin-bottom: 5px;
}

.comment-delete-form {
     margin-left: 0; /* Justera om det behövs för inbäddade kommentarer */
}

.button.delete-button {
    background-color: #dc3545; /* Röd färg för radering */
    color: white;
    border: 1px solid #c82333;
    padding: 6px 12px; 
    font-size: 0.9em;   
    cursor: pointer;
    border-radius: 4px;
    width: fit-content;
}

.button.delete-button:hover {
    background-color: #c82333;
    border-color: #bd2130;
}
</style>