<?php
header("Content-Type: application/json");
include 'config.php';

// Haal de URL op
$url = isset($_GET['url']) ? explode('/', rtrim($_GET['url'], '/')) : [];

// URL structuur: /films/{id} of /vestigingen/{id}/films
if(empty($url[0])){
    echo json_encode(["error" => "No endpoint specified"]);
    exit;
}

switch($url[0]){
    case "films":
        // /films of /films/{id}
        if(isset($url[1]) && is_numeric($url[1])){
            getFilmById($url[1]);
        } else {
            getAllFilms();
        }
        break;
    case "vestigingen":
        // /vestigingen/{id}/films
        if(isset($url[1]) && is_numeric($url[1]) && isset($url[2]) && $url[2] == "films"){
            getFilmsByVestiging($url[1]);
        } else {
            echo json_encode(["error" => "Invalid endpoint"]);
        }
        break;
    default:
        echo json_encode(["error" => "Endpoint not found"]);
}

// Functies
function getAllFilms(){
    global $conn;
    $stmt = $conn->prepare("SELECT id, titel, beschrijving, duur FROM films");
    $stmt->execute();
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($films);
}

function getFilmById($id){
    global $conn;
    $stmt = $conn->prepare("SELECT id, titel, beschrijving, duur FROM films WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $film = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($film);
}

function getFilmsByVestiging($vestiging_id){
    global $conn;
    $query = "
    SELECT f.id, f.titel, f.beschrijving, f.duur, v.datum, v.tijd, z.zaalnummer
    FROM films f
    JOIN vertoningen v ON f.id = v.film_id
    JOIN zalen z ON v.zaal_id = z.id
    WHERE z.vestiging_id = :vestiging_id
    ORDER BY v.datum, v.tijd
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':vestiging_id', $vestiging_id, PDO::PARAM_INT);
    $stmt->execute();
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($films);
}
