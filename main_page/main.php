<?php
include "../includes/header.php";
$conn = new mysqli('localhost', 'root', 'root', "school_cinema");
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}
?>
<main>
    <div class="zwart-vak">
        <span class="lees-meer">
            <div class="lees-meer-titel">AnnexBios – Hoofdkantoor Utrecht</div>

            Wij zijn AnnexBios, een jong en innovatief bedrijf met ons hoofdkantoor in Utrecht. Vanuit hier sturen wij onze projecten en ondersteunen we de andere vestigingen.

            De afgelopen periode hebben wij veel bereikt:

            Een API ontwikkeld voor een klant, zodat zij hun processen makkelijker en sneller kunnen laten verlopen.

            Belangrijke informatie opgeleverd aan onze andere kantoren, waarmee zij hun website konden laten werken en verder konden uitbouwen.

            Ons hoofdkantoor in Utrecht vormt het kloppend hart van AnnexBios. Hier bedenken, bouwen en leveren we oplossingen die onze klanten én onze eigen organisatie vooruithelpen.

            Toekomstvisie
            In de komende tijd willen wij onze technologie verder uitbreiden, zodat we nog meer klanten kunnen ondersteunen met slimme digitale oplossingen. Daarnaast werken we aan nieuwe manieren om onze kantoren wereldwijd beter met elkaar te verbinden en informatie sneller te delen. Ons doel is om AnnexBios te laten groeien tot een betrouwbaar en vooruitstrevend bedrijf dat innovatie en samenwerking centraal stelt.
        </span>
    </div>
    <div class="vestigingen-grid">
        <?php
        // Alleen de naam ophalen
        $result = $conn->query("SELECT name FROM vestigingen");
        while ($vestiging = $result->fetch_assoc()): ?>
            <div class="vestiging-kaart">
                <img src="../assets/img/Schermafbeelding 2025-09-10 154400.png" alt="Bioscoop" class="vestiging-afbeelding">
                <div class="vestiging-info">
                    <strong><?php echo htmlspecialchars($vestiging['name']); ?></strong>
                    <button class="vestiging-btn">BEZOEK WEBSITE</button>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</main>
<div class="film-slider-container">
    <div class="film-slider">
        <?php
        $result = $conn->query("SELECT * FROM movies");
        while ($film = $result->fetch_assoc()) {
            echo '<div class="film-vak">';
            echo '<img src="https://image.tmdb.org/t/p/w500/' . htmlspecialchars($film['backdrop_path']) . '" alt="Poster" class="film-poster">';
            echo '<div class="film-info">';
            echo '<div class="film-titel">' . htmlspecialchars($film['title']) . '</div>';
            echo '<div class="film-release"><strong>Release:</strong> ' . htmlspecialchars($film['release_date']) . '</div>';
            echo '<div class="film-runtime"><strong>Duur:</strong> ' . htmlspecialchars($film['runtime']) . ' min</div>';
            echo '<div class="film-overview">' . htmlspecialchars($film['overview']) . '</div>';
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>
</div>
<div class="nieuws-container">
    <?php

    $sql = "SELECT * FROM nieuws ORDER BY publiceerdatum DESC";
    // Haal nieuwsitems op
    $result = $conn->query($sql);
    $num_rows = $result->num_rows;
    $teller = 1;

    $array = [];

    while ($nieuws = $result->fetch_assoc()):
        $array[] = $nieuws;
    endwhile;
    ?>

    <div class="lange-blok">

        <?php
        for ($i = 0; $i < 1; $i++) {
            $nieuws = $array[$i];
        ?>

            <div class="nieuwsitem">
                <img src="../admin/assets/img/<?php echo htmlspecialchars($nieuws['afbeelding']); ?>" alt="Nieuws" class="nieuws-afbeelding">
                <h2><?php echo htmlspecialchars($nieuws['titel']); ?></h2>
                <p><?php echo htmlspecialchars($nieuws['beschrijving']); ?></p>
                <p><?php echo htmlspecialchars($nieuws['samenvatting']); ?></p>
                <p><?php echo htmlspecialchars($nieuws['publiceerdatum']); ?></p>
            </div>

        <?php }; ?>
    </div>
    <?php

    for ($i = 2; $i < count($array) - 1; $i++) {
        $nieuws = $array[$i];
    ?>
        <div class="nieuus-grid">
            <div class="nieuwsitem">
                <img src="../admin/assets/img/<?php echo htmlspecialchars($nieuws['afbeelding']); ?>" alt="Nieuws" class="nieuws-afbeelding">
                <h2><?php echo htmlspecialchars($nieuws['titel']); ?></h2>
                <p><?php echo htmlspecialchars($nieuws['beschrijving']); ?></p>
                <p><?php echo htmlspecialchars($nieuws['samenvatting']); ?></p>
                <p><?php echo htmlspecialchars($nieuws['publiceerdatum']); ?></p>
            </div>
        </div>

    <?php };
    for ($i = count($array) - 1; $i < count($array); $i++) {
        $nieuws = $array[$i];
    ?>
        <div class="lange-blok">
            <div class="nieuwsitem">
                <img src="../admin/assets/img/<?php echo htmlspecialchars($nieuws['afbeelding']); ?>" alt="Nieuws" class="nieuws-afbeelding">
                <h2><?php echo htmlspecialchars($nieuws['titel']); ?></h2>
                <p><?php echo htmlspecialchars($nieuws['beschrijving']); ?></p>
                <p><?php echo htmlspecialchars($nieuws['samenvatting']); ?></p>
                <p><?php echo htmlspecialchars($nieuws['publiceerdatum']); ?></p>
            </div>
        </div>

    <?php };
    ?>

</div>
<?php include '../includes/footer.php';?> 
<?php $conn->close(); ?>