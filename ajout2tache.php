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
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tasks = [
        [
            'nom' => htmlspecialchars($_POST['nom1']),
            'description' => htmlspecialchars($_POST['description1']),
            'date_debut' => htmlspecialchars($_POST['date_debut1']),
            'date_fin' => htmlspecialchars($_POST['date_fin1']),
        ],
        [
            'nom' => htmlspecialchars($_POST['nom2']),
            'description' => htmlspecialchars($_POST['description2']),
            'date_debut' => htmlspecialchars($_POST['date_debut2']),
            'date_fin' => htmlspecialchars($_POST['date_fin2']),
        ],
    ];

    foreach ($tasks as $index => $task) {
        if ($task['date_debut'] < date('Y-m-d')) {
            $error_message = "La date de début de la tâche " . ($index + 1) . " ne peut pas être antérieure à la date actuelle.";
            break;
        } elseif ($task['date_fin'] <= $task['date_debut']) {
            $error_message = "La date de fin de la tâche " . ($index + 1) . " doit être supérieure à la date de début.";
            break;
        }
    }

    if (empty($error_message)) {
        $stmt = $conn->prepare("INSERT INTO `tache`(`titre`, `description`, `date_debut`, `date_fin`, `statut_tache`, `id_projet`, `id_utilisateur`, `id_chef`, `id_ass`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $statut = "en_attente";
        foreach ($tasks as $task) {
            $stmt->bind_param("sssssiiss", $task['nom'], $task['description'], $task['date_debut'], $task['date_fin'], $statut, $id_projet, $id_utilisateur, $id_utilisateur, $id_utilisateur);
            $res = $stmt->execute();
            if (!$res) {
                $error_message = "Erreur : Une ou plusieurs tâches n'ont pas pu être ajoutées.";
                break;
            }
        }
        $stmt->close();

        if (empty($error_message)) {
            echo '<script>alert("Toutes les tâches ont été ajoutées avec succès."); window.location.href = "mestaches.php?id_projet='.$id_projet.'";</script>';
        }
    }

    if (!empty($error_message)) {
        echo '<script>alert("'.$error_message.'");</script>';
    }

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
    <h2 class="text-center"><b><?php echo htmlspecialchars($titre_projet); ?></b></h2><br>
    <div class="card form">
        <div class="card-header bg-gradient-rose-red text-white">
            Ajouter des tâches
        </div>
        <div class="">
            <div class="card-body">
                <form action="ajout2tache.php?id_projet=<?php echo $id_projet; ?>" method="post" id="task-form">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <h5 class="text-center mb-4" style="color: rgb(2, 2, 160);"><b>Tâche 1</b></h5>
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
                                        <span id="date-fin1-error" class="text-danger" style="display:none;"></span>
                                    </div>
                                </div><br>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <h5 class="text-center mb-4" style="color: rgb(2, 2, 160);"><b>Tâche 2</b></h5>
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
                                        <span id="date-fin2-error" class="text-danger" style="display:none;"></span>
                                    </div>
                                </div><br>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="row">
                            <div class="col-2"></div>
                            <div class="col-4">
                                <button type="submit" class="btn btn-success">Ajouter</button>
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

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function() {
        var today = new Date().toISOString().split('T')[0];
        $('input[type="date"]').attr('min', today);

        $('#task-form').on('submit', function(event) {
            var dateDebut1 = $('input[name="date_debut1"]').val();
            var dateFin1 = $('input[name="date_fin1"]').val();
            var dateDebut2 = $('input[name="date_debut2"]').val();
            var dateFin2 = $('input[name="date_fin2"]').val();

            var isValid = true;

            if (dateFin1 <= dateDebut1) {
                $('#date-fin1-error').text('La date de fin doit être supérieure à la date de début.').show();
                isValid = false;
            } else {
                $('#date-fin1-error').hide();
            }

            if (dateFin2 <= dateDebut2) {
                $('#date-fin2-error').text('La date de fin doit être supérieure à la date de début.').show();
                isValid = false;
            } else {
                $('#date-fin2-error').hide();
            }

            if (!isValid) {
                event.preventDefault();
            }
        });
    });
</script>
</body>
</html>

