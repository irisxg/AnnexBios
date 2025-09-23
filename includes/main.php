<main>
    <div class="zwart-vak">
        <span class="lees-meer">lees meer</span>
    </div>
     <div class="vestigingen-grid">
      <?php
      // Database connectie
      $conn = new mysqli('localhost', 'root', '', "school_cinema's");
      if ($conn->connect_error) {
          die("Verbinding mislukt: " . $conn->connect_error);
      }

      // Alleen de naam ophalen
      $result = $conn->query("SELECT name FROM vestigingen");
      while($vestiging = $result->fetch_assoc()): ?>
        <div class="vestiging-kaart">
            <img src="assets/img/Schermafbeelding 2025-09-10 154400.png" alt="Bioscoop" class="vestiging-afbeelding">
            <div class="vestiging-info">
                <strong><?php echo htmlspecialchars($vestiging['name']); ?></strong>
                <h1>Rijksstraatweg 42, 3223 KA</h1>
                <button class="vestiging-btn">BEZOEK WEBSITE</button>
            </div>
        </div>
      <?php endwhile; ?>
    </div>

</main>
<div class="film-slider-container">
    <div class="film-slider">
        <?php

        $conn = new mysqli('localhost', 'root', '', "school_cinema's");
        if ($conn->connect_error) {
            die("Verbinding mislukt: " . $conn->connect_error);
        }
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
        $conn->close();
        ?>
    </div>
</div>


<div class="nieuws-container">
    <div class="lange-blok">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Sequi possimus cupiditate laudantium voluptas doloribus. Earum autem quidem alias quos fugiat.</div>
    <div class="nieuws-grid">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsam, quam.</div>
    <div class="nieuws-grid">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsam, quam.</div>
    <div class="lange-blok">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Sequi possimus cupiditate laudantium voluptas doloribus. Earum autem quidem alias quos fugiat.</div>
</div>