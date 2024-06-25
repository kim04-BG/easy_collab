<?php

include "connexiondb.php";

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

// Récupérer les membres de l'équipe
$query_membres = "SELECT utilisateur.id_utilisateur, utilisateur.nom, utilisateur.prenom FROM equipe JOIN utilisateur ON equipe.id_utilisateur = utilisateur.id_utilisateur WHERE equipe.id_projet = ?";
$stmt = $conn->prepare($query_membres);
$stmt->bind_param("i", $id_projet);
$stmt->execute();
$result_membres = $stmt->get_result();

// Récupérer le chef de projet
$query_chef = "SELECT utilisateur.nom, utilisateur.prenom FROM projet JOIN utilisateur ON projet.id_chef = utilisateur.id_utilisateur WHERE projet.id_projet = ?";
$stmt = $conn->prepare($query_chef);
$stmt->bind_param("i", $id_projet);
$stmt->execute();
$stmt->bind_result($nom_chef, $prenom_chef);
$stmt->fetch();
$stmt->close();

// Récupérer les tâches assignées à l'utilisateur dans ce projet
$query_taches = "SELECT id_tache, titre, description, date_debut, date_fin, statut_tache FROM tache WHERE id_projet = ? AND id_utilisateur = ?";
$stmt_taches = $conn->prepare($query_taches);
$stmt_taches->bind_param("ii", $id_projet, $id_utilisateur);
$stmt_taches->execute();
$result_taches = $stmt_taches->get_result();
$taches = $result_taches->fetch_all(MYSQLI_ASSOC);
$stmt_taches->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'Équipe</title>
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
            margin-top: 50px;
        }
        .card {
            margin-bottom: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table thead th {
            background-color: darkgray;
            color: white;
        }
        .A thead th {
            background-color: #ff007f;
            color: white;
        }
        .table tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }
        .table tbody tr:hover {
            background-color: #e9ecef;
        }
        .page-title {
            margin-bottom: 30px;
        }
        .bg-gradient-rose-red {
            background: linear-gradient(90deg, #ff007f, #ff0040);
            color: white;
        }
    </style>
</head>
<body>
    <h2 class="text-center"><B><?php echo htmlspecialchars($titre_projet); ?></B></h2><br>
    <div class="container"> 
        <h2 class="page-title">Détails de l'Équipe</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-gradient-rose-red">
                        Chef de Projet
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead class="bg-gradient-rose-red">
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($nom_chef); ?></td>
                                    <td><?php echo htmlspecialchars($prenom_chef); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Membres de l'Équipe
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result_membres->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($row['prenom']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="page-title">Vos différentes tâche dans ce projet</h2>
        <div class="row">
            <!-- Bouton et tableau assignationrécapitulatif des tâches -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Vos différentes tâches à faire s'affichent ici</h5>
                <table class="table table-bordered A">
                    <thead>
                        <tr class="card-header bg-gradient-rose-red text-white">
                            <th>ID Tâche</th>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($taches as $tache) : ?>
                            <tr>
                                <td><?php echo $tache['id_tache']; ?></td>
                                <td><?php echo htmlspecialchars($tache['titre']); ?></td>
                                <td><?php echo htmlspecialchars($tache['description']); ?></td>
                                <td><?php echo htmlspecialchars($tache['date_debut']); ?></td>
                                <td><?php echo htmlspecialchars($tache['date_fin']); ?></td>
                                <td><?php echo htmlspecialchars($tache['statut_tache']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="assets/js/index.js"></script>
</body>
</html>
