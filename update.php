<?php
// les en-tete requises
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset= utf-8');
header("Access-Control-Allow-Methods: PUT");
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
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
    // verifier si tout les champs sont renseigner 
    if (
        !empty($array->marque) && !empty($array->modele) && !empty($array->categorie)
        && !empty($array->description) && !empty($array->immatriculation) && !empty($array->id)
    ) {
        $marque = verify($array->marque);
        $modele = verify($array->modele);
        $categorie = verify($array->categorie);
        $description = verify($array->description);
        $immatriculation = verify(strtoupper($array->immatriculation));
        $id = verify($array->id);
        $voiture_exist = "";
        // selectionner toute les voiture
        $requette = $connexion->query("SELECT id FROM voiture");
        $donnee = $requette->fetchall();
        foreach ($donnee as $valeur) {
            if ($valeur["id"] == $id)
                $voiture_exist = "oui";
        }
        if ($voiture_exist == "oui") {
            // modifier la voiture
            $requette = $connexion->prepare('UPDATE voiture SET marque=?,modele=?,id_categorie=?,description=?,immatriculation=? WHERE id=?');
            $requette->execute(array($marque, $modele, $categorie, $description, $immatriculation, $id));
            echo json_encode(["message" => "modification avec succes"]);
        } else {
            echo json_encode(["message" => "cette voiture n'existe pas veillez revoir l'identifiant"]);
        }
    } else {
        echo json_encode(["message" => "les champs suivants sont obligatoires dont: marque, modele,categorie,description,immatriculation, id."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "vous utilisez une mauvaise methode"]);
}
