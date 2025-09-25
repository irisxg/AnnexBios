 <header class="hoofd-header">
 <link rel="stylesheet" href="../assets/css/annexbios.css">
 <link rel="stylesheet" href="../assets/css/footer.css">
 <link rel="stylesheet" href="../assets/css/footer.css">
        <div class="header-container">
            <div class="logo">
                <img src="../assets/img/annexlogo.png" alt="AnnexBios" class="logo-afbeelding">
            </div>

              <!-- Hamburger knop buiten nav -->
        <button class="hamburger-knop" aria-label="Menu">☰</button>

            <nav class="hoofd-navigatie">
                <ul class="navigatie-lijst">
                    <li><a href="#" class="navigatie-link">VESTIGINGEN</a></li>
                    <li><a href="#" class="navigatie-link">AANBEVOLEN FILMS</a></li>
                    <li><a href="../main_page/contact.php" class="navigatie-link">CONTACT</a></li>
                    <li><a href="#" class="navigatie-link">NIEUWS</a></li>
                </ul>
            </nav>
        </div>
    </header>
     <section class="ticket-sectie">
        <div class="ticket-container">
            <h2 class="ticket-titel">KOOP JE TICKETS</h2>
            <div class="ticket-formulier">
                <select class="locatie-selectie">
                    <option>Kies je vestiging</option>
                    <option>Bilthoven</option>
                    <option>Montfoort</option>
                    <option>Woerden</option>
                    <option>Leidschereijn</option>
                </select>
                <button class="tickets-knop">BEKIJK TICKETS</button>
            </div>
        </div>
    </section>

    <script>
    const hamburgerKnop = document.querySelector('.hamburger-knop');
    const hoofdNavigatie = document.querySelector('.hoofd-navigatie');

    hamburgerKnop.addEventListener('click', () => {
        hoofdNavigatie.classList.toggle('open');
    });
</script>