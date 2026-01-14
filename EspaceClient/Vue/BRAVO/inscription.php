<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nom = $_POST["nom"];
    $email = $_POST["email"];
    $mot_de_passe = $_POST["mot_de_passe"];
    $date_naissance = $_POST["date_naissance"];
    $genre = $_POST["genre"];
    $pays = $_POST["pays"];

    // Enregistrement dans un fichier texte
    $fichier = fopen("inscriptions.txt", "a");
    fwrite($fichier, "Nom: $nom | Email: $email | Mot de passe: $mot_de_passe | Date: $date_naissance | Genre: $genre | Pays: $pays\n");
    fclose($fichier);

    echo "<h2>Inscription réussie </h2>";
    echo "<p>Merci $nom, votre inscription a bien été enregistrée.</p>";
}
?>
