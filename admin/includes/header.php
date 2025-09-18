<link rel="stylesheet" href="./assets/annexbios.css">

<header class="hoofd-header">
    <div class="header-container">
        <div class="logo">
            <a href="admin.php">
                <img src="./assets/img/annexbioslogo.png" alt="AnnexBios" class="logo-afbeelding">
            </a>
        </div>

        <!-- Hamburger knop buiten nav -->
        <button class="hamburger-knop" aria-label="Menu">☰</button>

        <nav class="hoofd-navigatie">
            <ul class="navigatie-lijst">
                <li><a href="admin.php" class="navigatie-link">Home</a></li>
                <li><a href="vestegingen.php" class="navigatie-link">Vestigingen</a></li>
                <li><a href="films.php" class="navigatie-link">Films</a></li>
                <li><a href="nieuws.php" class="navigatie-link">Nieuws</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="ticket-sectie">
    <div class="ticket-container">
        <h2 class="ticket-titel">Onderhoud uw pagina</h2>
    </div>
</section>

<script>
    const hamburgerKnop = document.querySelector('.hamburger-knop');
    const hoofdNavigatie = document.querySelector('.hoofd-navigatie');

    hamburgerKnop.addEventListener('click', () => {
        hoofdNavigatie.classList.toggle('open');
    });
</script>