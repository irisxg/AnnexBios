<?php
// Database connectie
$conn = new mysqli('localhost', 'root', '', "school_cinema's");
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}
?>
<main>
    <div class="zwart-vak">
        <span class="lees-meer">lees meer</span>
    </div>
    <div class="vestigingen-grid">
        <?php
        // Alleen de naam ophalen
        $result = $conn->query("SELECT name FROM vestigingen");
        while ($vestiging = $result->fetch_assoc()): ?>
            <div class="vestiging-kaart">
                <img src="assets/img/Schermafbeelding 2025-09-10 154400.png" alt="Bioscoop" class="vestiging-afbeelding">
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
    while ($nieuws = $result->fetch_assoc()): ?>
        <div class="nieuws-grid">
            <img src="assets/img/<?php echo htmlspecialchars($nieuws['afbeelding']); ?>" alt="Nieuws" class="nieuws-afbeelding">
            <h2><?php echo htmlspecialchars($nieuws['titel']); ?></h2>
            <p><?php echo htmlspecialchars($nieuws['beschrijving']); ?></p>
            <p><?php echo htmlspecialchars($nieuws['samenvatting']); ?></p>
            <p><?php echo htmlspecialchars($nieuws['publiceerdatum']); ?></p>
        </div>
    <?php endwhile; ?>
</div>
<?php $conn->close(); ?>