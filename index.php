<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameBook Organiser</title>
</head>

<body>
    <h1></h1>Bienvenue sur GameBook Organiser</h1>
    <?php
    echo "<h1>Bienvenue sur GameBook Organiser</h1>";

    //Connexion à la base de données 
    $bdd = new PDO('mysql:host=localhost;dbname=nom_de_la_bdd', 'root','', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    //Exécution de la requête SQL avec un try catch pour la gestion des exceptions (messages d’erreurs) 
    try {
        //requête pour stocker le contenu de toute la table le contenu est stocké dans la variable $data, $req stocke la requête SQL.
        $req = $bdd->query('SELECT * FROM utilisateur');
        //boucle pour parcourir et afficher le contenu de chaque ligne de la table.
        while ($data = $req->fetch()) {   
            //affichage les informations d’une colonne de la bdd par son non d’attribut.
            echo '<p>'.$data['nom_attribut'].'</p>';
        }
    }
    catch(Exception $e) {  
        //affichage d'une exception en cas d’erreur
        die('Erreur : '.$e->getMessage());
    }
    ?>
    
</body>

</html>