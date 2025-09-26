<?php 
require '../db/db.php';
include '../includes/header.php';
?>

<main>
    <div class="zwart-vak">
        <span class="lees-meer">
            <div class="lees-meer-titel">AnnexBios – Hoofdkantoor Utrecht</div>
            Wij zijn AnnexBios, een jong en innovatief bedrijf met ons hoofdkantoor in Utrecht. Vanuit hier sturen wij onze projecten en ondersteunen we de andere vestigingen. 
            <br><br>
            De afgelopen periode hebben wij veel bereikt:
            <ul>
                <li>Een API ontwikkeld voor een klant, zodat zij hun processen makkelijker en sneller kunnen laten verlopen.</li>
                <li>Belangrijke informatie opgeleverd aan onze andere kantoren, waarmee zij hun website konden laten werken en verder konden uitbouwen.</li>
            </ul>
            Ons hoofdkantoor in Utrecht vormt het kloppend hart van AnnexBios. Hier bedenken, bouwen en leveren we oplossingen die onze klanten én onze eigen organisatie vooruithelpen.
            <br><br>
            <strong>Toekomstvisie</strong><br>
            In de komende tijd willen wij onze technologie verder uitbreiden, zodat we nog meer klanten kunnen ondersteunen met slimme digitale oplossingen. Daarnaast werken we aan nieuwe manieren om onze kantoren wereldwijd beter met elkaar te verbinden en informatie sneller te delen. 
            Ons doel is om AnnexBios te laten groeien tot een betrouwbaar en vooruitstrevend bedrijf dat innovatie en samenwerking centraal stelt.
        </span>
    </div>
<div id='vestegingen'>
    <div class="vestigingen-grid">
        <?php 
        // Alleen de naam ophalen
        $result = $conn->query("SELECT name FROM vestigingen");
        while ($vestiging = $result->fetch_assoc()): 
        ?>
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
</div>
<div id='aanbevolen'>
<div class="film-slider-container">
    <div class="film-slider">
        <?php 
        $result = $conn->query("SELECT * FROM movies");
        while ($film = $result->fetch_assoc()): 
        ?>
            <div class="film-vak">
                <img src="https://image.tmdb.org/t/p/w500/<?php echo htmlspecialchars($film['backdrop_path']); ?>" alt="Poster" class="film-poster">
                <div class="film-info">
                    <div class="film-titel"><?php echo htmlspecialchars($film['title']); ?></div>
                    <div class="film-release"><strong>Release:</strong> <?php echo htmlspecialchars($film['release_date']); ?></div>
                    <div class="film-runtime"><strong>Duur:</strong> <?php echo htmlspecialchars($film['runtime']); ?> min</div>
                    <div class="film-overview"><?php echo htmlspecialchars($film['overview']); ?></div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</div>
<div class="nieuws-container">
    <?php 
    $sql = "SELECT * FROM nieuws ORDER BY publiceerdatum DESC";
    $result = $conn->query($sql);

    $array = [];
    while ($nieuws = $result->fetch_assoc()) {
        $array[] = $nieuws;
    }
    ?>

    
    <div id='nieuws' class="lange-blok">
        <?php if (count($array) > 0): $nieuws = $array[0]; ?>
            <div class="nieuwsitem">
                <img src="../admin/assets/img/<?php echo htmlspecialchars($nieuws['afbeelding']); ?>" alt="Nieuws" class="nieuws-afbeelding">
                <h2><?php echo htmlspecialchars($nieuws['titel']); ?></h2>
                <p><?php echo htmlspecialchars($nieuws['beschrijving']); ?></p>
                <p><?php echo htmlspecialchars($nieuws['samenvatting']); ?></p>
                <p><?php echo htmlspecialchars($nieuws['publiceerdatum']); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Nieuws grid -->
    <?php for ($i = 2; $i < count($array) - 1; $i++): $nieuws = $array[$i]; ?>
        <div class="nieuus-grid">
            <div class="nieuwsitem">
                <img src="../admin/assets/img/<?php echo htmlspecialchars($nieuws['afbeelding']); ?>" alt="Nieuws" class="nieuws-afbeelding">
                <h2><?php echo htmlspecialchars($nieuws['titel']); ?></h2>
                <p><?php echo htmlspecialchars($nieuws['beschrijving']); ?></p>
                <p><?php echo htmlspecialchars($nieuws['samenvatting']); ?></p>
                <p><?php echo htmlspecialchars($nieuws['publiceerdatum']); ?></p>
            </div>
        </div>
    <?php endfor; ?>

    <!-- Laatste lange blok -->
    <?php if (count($array) > 1): $nieuws = end($array); ?>
        <div class="lange-blok">
            <div class="nieuwsitem">
                <img src="../admin/assets/img/<?php echo htmlspecialchars($nieuws['afbeelding']); ?>" alt="Nieuws" class="nieuws-afbeelding">
                <h2><?php echo htmlspecialchars($nieuws['titel']); ?></h2>
                <p><?php echo htmlspecialchars($nieuws['beschrijving']); ?></p>
                <p><?php echo htmlspecialchars($nieuws['samenvatting']); ?></p>
                <p><?php echo htmlspecialchars($nieuws['publiceerdatum']); ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php 
include '../includes/footer.php';
$conn->close(); 
?>

<script>
const container = document.querySelector(".film-slider-container");
const slider = document.querySelector(".film-slider");

slider.innerHTML += slider.innerHTML;

const slideWidth = slider.scrollWidth / 2;
let scrollAmount = 20;
let hovering = false;

container.addEventListener("mouseenter", () => {
    hovering = true;
});

container.addEventListener("mouseleave", () => {
    hovering = false;
});

function autoScroll() {
    if (!hovering) {
        container.scrollLeft += scrollAmount;
    }
    requestAnimationFrame(autoScroll);
}
autoScroll();
</script>
