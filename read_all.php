<?php
// les en-tete requises
header("Access-Control-Allow-Origin:*");
header("Content-type: application/json; charset= utf-8");
header("Access-Control-Allow-Methods: GET");
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "arene";
    $requette = "";
    $connexion = new PDO("mysql:host=$servername; dbname=$dbname", $username, $password);
    // selectionner toutes les voitres et les categorie aux quelle elle appartiennent
    $requette = $connexion->query("SELECT v.immatriculation, v.id as id_voiture, v.marque, v.modele, c.nom as nom_categorie, c.id as id_categorie FROM voiture v INNER JOIN categorie c ON v.id_categorie=c.id");
    $donnee = $requette->fetchall();
    echo json_encode($donnee);
} else {
    http_response_code(405);
    echo json_encode(["message" => "vous utilisez une mauvaise methode"]);
}
