<form>
    <input type="text" name="searchUsr" placeholder="Sök vänner" size="30" />
    <button class="search"><img src="/images/searchIcon.png" alt="Search" /></button>
</form>

<nav>
    <ul>
        <li><a href="/"><?php echo $_SESSION['name']; ?></a></li>
        <li><a href="/flow">Flöde</a></li>
        <li><a href="/friends">Vänner</a></li>
        <li><a href="/prefernces">Inställningar</a></li>
    </ul>
</nav>
<button class="sign"><img src="/images/logout.png" alt="Logga Ut" /></button>