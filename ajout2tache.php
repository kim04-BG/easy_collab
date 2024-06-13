<?php
session_start();
include "connexiondb.php";

if (!isset($_SESSION['id_utilisateur']) || !isset($_GET['id_projet'])) {
    header("Location: connexion.php");
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$id_projet = $_GET['id_projet'];

// Récupérer les informations du projet
$query_projet = "SELECT titre FROM projet WHERE id_projet = ?";
$stmt = $conn->prepare($query_projet);
$stmt->bind_param("i", $id_projet);
$stmt->execute();
$stmt->bind_result($titre_projet);
$stmt->fetch();
$stmt->close();

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données pour la tâche 1
    $nom1 = htmlspecialchars($_POST['nom1']);
    $description1 = htmlspecialchars($_POST['description1']);
    $date_debut1 = htmlspecialchars($_POST['date_debut1']);
    $date_fin1 = htmlspecialchars($_POST['date_fin1']);
    $statut1 = "en_attente"; // Définir le statut par défaut
    $id_utilisateur = $_SESSION['id_utilisateur']; // Assurez-vous que l'utilisateur est connecté et son ID est disponible

    // Insertion de la tâche 1 dans la base de données
    $stmt1 = $conn->prepare("INSERT INTO `tache`(`titre`, `description`, `date_debut`, `date_fin`, `statut_tache`, `id_projet`, `id_utilisateur`, `id_chef`, `id_ass`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt1->bind_param("sssssiiss", $nom1, $description1, $date_debut1, $date_fin1, $statut1, $id_projet, $id_utilisateur, $id_utilisateur, $id_utilisateur);
    $res1 = $stmt1->execute();
    $stmt1->close();

    // Récupération des données pour la tâche 2
    $nom2 = htmlspecialchars($_POST['nom2']);
    $description2 = htmlspecialchars($_POST['description2']);
    $date_debut2 = htmlspecialchars($_POST['date_debut2']);
    $date_fin2 = htmlspecialchars($_POST['date_fin2']);
    $statut2 = "en_attente"; // Définir le statut par défaut

    // Insertion de la tâche 2 dans la base de données
    $stmt2 = $conn->prepare("INSERT INTO `tache`(`titre`, `description`, `date_debut`, `date_fin`, `statut_tache`, `id_projet`, `id_utilisateur`, `id_chef`, `id_ass`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt2->bind_param("sssssiiss", $nom2, $description2, $date_debut2, $date_fin2, $statut2, $id_projet, $id_utilisateur, $id_utilisateur, $id_utilisateur);
    $res2 = $stmt2->execute();
    $stmt2->close();

    // Redirection après insertion
    if ($res1 && $res2) {
        echo '<script>alert("Toutes les tâches ont été ajoutées avec succès."); window.location.href = "mestaches.php?id_projet='.$id_projet.'";</script>';
    } else {
        echo '<script>alert("Erreur : Une ou plusieurs tâches n\'ont pas pu être ajoutées.");</script>';
    }

    // Fermeture de la connexion
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter des tâches</title>
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 0px;
        }
        .card {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
        }
        .bg-gradient-rose-red {
            background: linear-gradient(90deg, #ff007f, #ff0040);
            color: white;
        }
    </style>
</head>
<body id="background" background="assets/img/B.jpg">
<div class="container">
    <h2 class="text-center"><B><?php echo htmlspecialchars($titre_projet); ?></B></h2><br>
        <div class="card form">
            <div class="card-header bg-gradient-rose-red text-white">
                Ajouter des tâches
            </div>
            <div class="">
            <div class="card-body">
            <form action="ajout2tache.php?id_projet=<?php echo $id_projet; ?>" method="post">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                                    <h5 class="text-center mb-4" style="color: rgb(2, 2, 160);"><B>Tâche 1</B></h5>
                                    <input type="text" class="form-control" placeholder="Nom de la tâche" name="nom1" required><br>
                                    <textarea class="form-control" rows="3" placeholder="Description de la tâche" name="description1" required></textarea>
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="task1-start-date">Date de Début :</label>
                                            <input type="date" class="form-control" name="date_debut1" required>
                                        </div>
                                        <div class="col-6">
                                            <label for="task1-end-date">Date de Fin :</label>
                                            <input type="date" class="form-control" name="date_fin1" required>
                                        </div>
                                    </div><br>
                                    
                        </div>
                    </div>
                <div class="col-6">
                    <!-- Tâche 2 -->
                    <div class="form-group">
                        <h5 class="text-center mb-4" style="color: rgb(2, 2, 160);"><B>Tâche 2</B></h5>
                        <input type="text" class="form-control" placeholder="Nom de la tâche" name="nom2" required><br>
                        <textarea class="form-control" rows="3" placeholder="Description de la tâche" name="description2" required></textarea>
                        <div class="row">
                            <div class="col-6">
                                <label for="task2-start-date">Date de Début :</label>
                                <input type="date" class="form-control" name="date_debut2" required>
                            </div>
                            <div class="col-6">
                                <label for="task2-end-date">Date de Fin :</label>
                                <input type="date" class="form-control" name="date_fin2" required>
                            </div>
                        </div><br>
                       
                    </div>
                </div>
            </div>
            <div class="text-center">
                        <div class="row">
                            <div class="col-2"></div>
                            <div class="col-4">
                                <button type="submit"  class="btn btn-success">Ajouter</button>
                            </div>
                            <div class="col-4">
                                <a href="mestaches.php?id_projet=<?php echo $id_projet; ?>" class="btn btn-secondary">Annuler</a>
                            </div>
                            <div class="col-2"></div>
                        </div>
            </div>  
                    

        
    </form> 
            </div>
            </div>
            
        </div>
    </div>

</body>
</html>
