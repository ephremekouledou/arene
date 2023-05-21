<?php
// les en-tete requises
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset= utf-8');
header("Access-Control-Allow-Methods: DELETE");
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "arene";
    // connexion a la base de donnée
    $connexion = new PDO("mysql:host=$servername; dbname=$dbname", $username, $password);
    // recevoir des donnée en json
    $array = json_decode(file_get_contents("php://input"));
    function verify($var)
    {
        $var = htmlspecialchars($var);
        $var = trim($var);
        $var = stripslashes($var);
        return $var;
    }
    // verifier si l'identifiant de la voiture a supprimer existe
    if (!empty($array->id)) {
        $id = verify($array->id);
        $voiture_exist = "";
        // selectionner les voitures existante
        $requette = $connexion->query("SELECT id FROM voiture");
        $donnee = $requette->fetchall();
        foreach ($donnee as $valeur) {
            if ($valeur["id"] == $id)
                $voiture_exist = "oui";
        }
        if ($voiture_exist == "oui") {
            // supprimer la voiture
            $requette = $connexion->prepare("DELETE FROM voiture WHERE id=?");
            $requette->execute(array($id));
            echo json_encode(["message" => "suppresion effectuer"]);
        } else {
            echo json_encode(["message" => "cette voiture n'existe pas veillez revoir l'identifiant"]);
        }
    } else {
        echo json_encode(["message" => "veillez renseignez l'identifiant du champs a supprimer avec la cle id"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "vous utilisez une mauvaise methode"]);
}
