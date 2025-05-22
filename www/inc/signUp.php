<aside>
    <img src="/images/mobile.png" alt="Mobiltelefon" width="240" />
</aside>
<section>
    <h2>Skapa konto på EGY Talk</h2>
    <form method="post" action="api/createUser.php">
        <label for="fn">Förnamn</label>
        <input id="fn" type="text" name="firstname" />

        <label for="ln">Efternamn</label>
        <input id="ln" type="text" name="surname" />

        <label for="usr">Användarnamn</label>
        <input id="usr" type="text" name="username" />

        <label for="pwd">Lösenord</label>
        <input id="pwd" type="password" name="pwd" />

        <input type="submit" value="Skapa Konto"/>
    </form>
    <p class="center">eller</p>
        <a href="index.php?login=true"><button type="submit">Logga In</button></a>
</section>

