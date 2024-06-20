<?php
session_start();
include "connexiondb.php";

if (!isset($_SESSION['id_utilisateur']) || !isset($_SESSION['role']) || !isset($_GET['id_projet'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté ou si l'ID du projet n'est pas défini
    header("Location: connexion.php");
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$id_projet = $_GET['id_projet'];

// Récupérer les informations de l'utilisateur connecté
$query_utilisateur = "SELECT nom, prenom, email FROM utilisateur WHERE id_utilisateur = ?";
$stmt = $conn->prepare($query_utilisateur);
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$stmt->bind_result($nom_utilisateur, $prenom_utilisateur, $email_utilisateur);
$stmt->fetch();
$stmt->close();

// Récupérer les informations du projet à modifier
$query_projet = "SELECT titre, description, date_debut, date_fin, statut_projet, id_methode FROM projet WHERE id_projet = ?";
$stmt = $conn->prepare($query_projet);
$stmt->bind_param("i", $id_projet);
$stmt->execute();
$stmt->bind_result($titre_projet, $description_projet, $date_debut_projet, $date_fin_projet, $statut_projet, $id_methode);
$stmt->fetch();
$stmt->close();

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nouvelle_description = $_POST['description'];
    $nouvelle_date_debut = $_POST['date_debut'];
    $nouvelle_date_fin = $_POST['date_fin'];
    $nouveau_statut = $_POST['statut_projet'];

    // Validation des dates
    if ($nouvelle_date_debut < date('Y-m-d')) {
        $error_message = "La date de début ne peut pas être antérieure à la date actuelle.";
    } elseif ($nouvelle_date_fin <= $nouvelle_date_debut) {
        $error_message = "La date de fin doit être supérieure à la date de début.";
    }

    if (empty($error_message)) {
        $query_update = "UPDATE projet SET description = ?, date_debut = ?, date_fin = ?, statut_projet = ? WHERE id_projet = ?";
        $stmt_update = $conn->prepare($query_update);
        if (!$stmt_update) {
            die("Erreur de préparation de la requête : " . $conn->error);
        }
        $stmt_update->bind_param("ssssi", $nouvelle_description, $nouvelle_date_debut, $nouvelle_date_fin, $nouveau_statut, $id_projet);
        if ($stmt_update->execute()) {
            echo "<script>alert('Projet modifié avec succès.'); window.location.href='tableau_de_bord_chef.php?id_projet=$id_projet';</script>";
        } else {
            echo "<script>alert('Erreur lors de la modification du projet.'); window.location.href='tableau_de_bord_chef.php?id_projet=$id_projet';</script>";
        }
        $stmt_update->close();
        exit();
    } else {
        echo "<script>alert('$error_message');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Projet</title>
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
            background: linear-gradient(90deg, #02cb19, #0056b3);
            color: white;
        }
    </style>
</head>
<body id="background" background="assets/img/B.jpg">
    <div class="container">
        <h2 class="text-center"><b><?php echo htmlspecialchars($titre_projet); ?></b></h2><br>
        <div class="card form">
            <div class="card-header bg-gradient-rose-red text-white">
                MODIFIER PROJET
            </div>
            <div class="">
                <div class="card-body">
                    <form action="modifier_projet.php?id_projet=<?php echo $id_projet; ?>" method="POST" id="project-form">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($description_projet); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="date_debut">Date de début</label>
                            <input type="date" class="form-control" id="date_debut" name="date_debut" value="<?php echo $date_debut_projet; ?>" required>
                            <span id="date-debut-error" class="text-danger" style="display:none;"></span>
                        </div>
                        <div class="form-group">
                            <label for="date_fin">Date de fin</label>
                            <input type="date" class="form-control" id="date_fin" name="date_fin" value="<?php echo $date_fin_projet; ?>" required>
                            <span id="date-fin-error" class="text-danger" style="display:none;"></span>
                        </div>
                        <div class="form-group">
                            <label for="statut_tache">Statut</label>
                            <select class="form-control" id="statut_projet" name="statut_projet" required>
                                <option value="en_attente" <?php echo $statut_projet == 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                                <option value="en_cours" <?php echo $statut_projet == 'en_cours' ? 'selected' : ''; ?>>En cours</option>
                                <option value="termine" <?php echo $statut_projet == 'termine' ? 'selected' : ''; ?>>Terminé</option>
                            </select>
                        </div>
                        <div class="text-center">
                            <div class="row">
                                <div class="col-2"></div>
                                <div class="col-4">
                                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                </div>
                                <div class="col-4">
                                    <a href="tableau_de_bord_chef.php?id_projet=<?php echo $id_projet; ?>" class="btn btn-secondary">Annuler</a>
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
            $('#date_debut').attr('min', today);
            $('#date_fin').attr('min', today);

            $('#project-form').on('submit', function(event) {
                var dateDebut = $('#date_debut').val();
                var dateFin = $('#date_fin').val();
                var isValid = true;

                if (dateDebut < today) {
                    $('#date-debut-error').text('La date de début ne peut pas être antérieure à la date actuelle.').show();
                    isValid = false;
                } else {
                    $('#date-debut-error').hide();
                }

                if (dateFin <= dateDebut) {
                    $('#date-fin-error').text('La date de fin doit être supérieure à la date de début.').show();
                    isValid = false;
                } else {
                    $('#date-fin-error').hide();
                }

                if (!isValid) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>

    
        
