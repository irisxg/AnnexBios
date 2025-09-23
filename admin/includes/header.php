<<<<<<< Updated upstream
<head>
<link rel="stylesheet" href="../assets/css/annexbios.css"/>
<link rel="stylesheet" href="../assets/css/admin_vertoning_form.css"/>
<link rel="stylesheet" href="../assets/css/admin_vertoningen_list.css"/>
<link rel="stylesheet" href="../assets/css/admin.css"/>
<link rel="stylesheet" href="../assets/css/footer.css"/>
<link rel="stylesheet" href="../assets/css/admin_vestiging_form.css"/>
<link rel="stylesheet" href="../assets/css/admin_vestigingen_list.css"/>
=======
<link rel="stylesheet" href="../assets/css/annexbios.css">
<link rel="stylesheet" href="../assets/css/admin_vertoning_form.css">
<link rel="stylesheet" href="../assets/css/admin_vertoningen_list.css">
<link rel="stylesheet" href="../assets/css/admin.css">
<link rel="stylesheet" href="../assets/css/admin_vestiging_form.css">
<link rel="stylesheet" href="../assets/css/admin_vestigingen_list.css">
<link rel="stylesheet" href="../assets/css/annexbios.css">
<link rel="stylesheet" href="../assets/css/nieuws.css">
<link rel="stylesheet" href="../assets/css/footer.css">
>>>>>>> Stashed changes

</head>
<header class="hoofd-header">
    <div class="header-container">
        <div class="logo">
            <a href="admin.php">
                <img src="../assets/img/annexbioslogo.png" alt="AnnexBios" class="logo-afbeelding">
            </a>
        </div>

        <button class="hamburger-knop" aria-label="Menu">☰</button>

        <nav class="hoofd-navigatie">
            <ul class="navigatie-lijst">
<<<<<<< Updated upstream
                <li><a href="../overzichtpagina/admin.php" class="navigatie-link">Home</a></li>
=======
                <li><a href="../main_page/admin.php" class="navigatie-link">Home</a></li>
>>>>>>> Stashed changes
                <li><a href="../vestigingen_crud/list_vestigingen.php" class="navigatie-link">Vestigingen</a></li>
                <li><a href="../crud_films/list_vertoningen.php" class="navigatie-link">Films</a></li>
                <li><a href="../news/nieuws.php" class="navigatie-link">Nieuws</a></li>
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