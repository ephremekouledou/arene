<?php
// les en-tete requises
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset= utf-8');
header("Access-Control-Allow-Methods: POST");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "arene";
    // connexin a la base de donnée
    $connexion = new PDO("mysql:host=$servername; dbname=$dbname", $username, $password);
    //reception des donner en format json
    $array = json_decode(file_get_contents("php://input"));
    // la fonction permettant deviter la faille XSS
    function verify($var)
    {
        $var = htmlspecialchars($var);
        $var = trim($var);
        $var = stripslashes($var);
        return $var;
    }
    // verifier si l'utilisateur a renseigner tout les champ
    if (
        !empty($array->marque) && !empty($array->modele) && !empty($array->categorie)
        && !empty($array->description) && !empty($array->immatriculation)
    ) {
        $marque = verify($array->marque);
        $modele = verify($array->modele);
        $categorie = verify($array->categorie);
        $immatriculation = verify(strtoupper($array->immatriculation));
        $description = verify($array->description);
        $voiture_exist = "";
        // selectionner les voitures de la base de donnée
        $requette = $connexion->query("SELECT immatriculation FROM voiture");
        $donnee = $requette->fetchall();
        foreach ($donnee as $valeur) {
            //  verifier si la voiture existe deja 
            if ($valeur['immatriculation'] == $immatriculation) {
                $voiture_exist = "oui";
            }
        }
        if ($voiture_exist == "oui") {
            echo json_encode(["message" => "cette voiture existe deja veillez revoir l'immatriculation"]);
        } else {
            // enregistrer la voiture
            $requette = $connexion->prepare('INSERT INTO voiture(marque,modele,id_categorie,immatriculation, description) VALUES(?,?,?,?,?)');
            $requette->execute(array($marque, $modele, $categorie, $immatriculation, $description));
            echo json_encode(["message" => "enregistrement effectuer avec succes"]);
        }
    } else {
        // aider lutilisateur a connaitre es champs a remplir
        echo json_encode(["message" => "5 champs sont obligatoires dont: marque, modele, categorie, immatriculation, description. Verifiez l'ortographe des cle des champs"]);
    }
} else {
    http_response_code(405);
    // signaler a l'utilisateur de changer de methode 
    echo json_encode(["message" => "vous utilisez une mauvaise methode"]);
}
