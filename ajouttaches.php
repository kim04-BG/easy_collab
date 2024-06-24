<?php
session_start();
include "connexiondb.php";

if (!isset($_SESSION['id_utilisateur']) || !isset($_SESSION['role']) || !isset($_SESSION['id_projet'])) {
    header("Location: connexion.php");
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$id_projet = $_SESSION['id_projet'];

$query_utilisateur = "SELECT nom, prenom, email FROM utilisateur WHERE id_utilisateur = ?";
$stmt = $conn->prepare($query_utilisateur);
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$stmt->bind_result($nom_utilisateur, $prenom_utilisateur, $email_utilisateur);
$stmt->fetch();
$stmt->close();

$query_projet = "SELECT titre FROM projet WHERE id_projet = ?";
$stmt = $conn->prepare($query_projet);
$stmt->bind_param("i", $id_projet);
$stmt->execute();
$stmt->bind_result($nom_projet);
$stmt->fetch();
$stmt->close();

$error_message = '';

if (isset($_POST['ajouter'])) {
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
        [
            'nom' => htmlspecialchars($_POST['nom3']),
            'description' => htmlspecialchars($_POST['description3']),
            'date_debut' => htmlspecialchars($_POST['date_debut3']),
            'date_fin' => htmlspecialchars($_POST['date_fin3']),
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
        foreach ($tasks as $task) {
            $statut = "en_attente";
            $stmt->bind_param("sssssiiss", $task['nom'], $task['description'], $task['date_debut'], $task['date_fin'], $statut, $id_projet, $id_utilisateur, $id_utilisateur, $id_utilisateur);
        $res = $stmt->execute();
        if (!$res) {
            $error_message = "Erreur : Une ou plusieurs tâches n'ont pas pu être ajoutées.";
            break;
        }
    }
    $stmt->close();

    if (empty($error_message)) {
        echo '<script>alert("Toutes les tâches ont été ajoutées avec succès."); window.location.href = "ajoutequipe.php";</script>';
    }
}

$conn->close();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EASYCOLLAB</title>
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body id="background" background="">
        <header>
            <div class="row">
                <div class="col-4"></div>
                <div class="col-4">
                    <nav class="navbar navbar-expand-lg navbar-light">
                    <a class="navbar-brand" href="acceuil.html">
                        <img src="assets/img/LOGO.png" alt="Logo de la plateforme" height="30">
                        <b>EASYCOLLAB</b>
                    </a>   
                    </nav>
                </div>
                <div class="col-4"></div>
            </div>
            
        </header>

    <section id="c-pro">
        <div class="">
            <h2><B><?php echo htmlspecialchars($nom_projet); ?></B></h2>
            <h2 class="">Transformez votre projet avec chaque tâche ajoutée <B><?php echo htmlspecialchars($prenom_utilisateur); ?></B></h2>
            <p class="lead">Ajoutez des étapes concrètes pour concrétiser votre vision, une tâche à la fois</p>
        </div>
    </section>

    <div class="container custom-container">
        <div class="row">
            <div class="col-md-6">
                <h2><B>Ajouter des Tâches</B></h2>
                <form id="tasks-form" method="POST">
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <!-- Tâche 1 -->
                    <div class="form-group">
                        <h5 class="text-center mb-4" style="color: rgb(2, 2, 160);"><B>Tâche 1</B></h5>
                        <input type="text" class="form-control" placeholder="Nom de la tâche" name="nom1" required><br>
                        <textarea class="form-control" rows="3" placeholder="Description de la tâche" name="description1" required></textarea>
                        <div class="row">
                            <div class="col-6">
                                <label for="task1-start-date">Date de Début :</label>
                                <input type="date" class="form-control" id="task1-start-date" name="date_debut1" required>
                                <div class="invalid-feedback" id="date-debut1-error"></div>
                            </div>
                            <div class="col-6">
                                <label for="task1-end-date">Date de Fin :</label>
                                <input type="date" class="form-control" id="task1-end-date" name="date_fin1" required>
                                <div class="invalid-feedback" id="date-fin1-error"></div>
                            </div>
                        </div><br>
                    </div>

                    <!-- Tâche 2 -->
                    <div class="form-group">
                        <h5 class="text-center mb-4" style="color: rgb(2, 2, 160);"><B>Tâche 2</B></h5>
                        <input type="text" class="form-control" placeholder="Nom de la tâche" name="nom2" required><br>
                        <textarea class="form-control" rows="3" placeholder="Description de la tâche" name="description2" required></textarea>
                        <div class="row">
                            <div class="col-6">
                                <label for="task2-start-date">Date de Début :</label>
                                <input type="date" class="form-control" id="task2-start-date" name="date_debut2" required>
                                <div class="invalid-feedback" id="date-debut2-error"></div>
                            </div>
                            <div class="col-6">
                                <label for="task2-end-date">Date de Fin :</label>
                                <input type="date" class="form-control" id="task2-end-date" name="date_fin2" required>
                                <div class="invalid-feedback" id="date-fin2-error"></div>
                            </div>
                        </div><br>
                    </div>

                    <!-- Tâche 3 -->
                    <div class="form-group">
                        <h5 class="text-center mb-4" style="color: rgb(2, 2, 160);"><B>Tâche 3</B></h5>
                        <input type="text" class="form-control" placeholder="Nom de la tâche" name="nom3" required><br>
                        <textarea class="form-control" rows="3" placeholder="Description de la tâche" name="description3" required></textarea>
                        <div class="row">
                            <div class="col-6">
                                <label for="task3-start-date">Date de Début :</label>
                                <input type="date" class="form-control" id="task3-start-date" name="date_debut3" required>
                                <div class="invalid-feedback" id="date-debut3-error"></div>
                            </div>
                            <div class="col-6">
                                <label for="task3-end-date">Date de Fin :</label>
                                <input type="date" class="form-control" id="task3-end-date" name="date_fin3" required>
                                <div class="invalid-feedback" id="date-fin3-error"></div>
                            </div>
                        </div><br>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" name="ajouter">Ajouter des Tâches</button>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <h2><B>Aperçu des Tâches</B></h2>
                <div class="card"> 
                    <!-- Tableau illustrant l'aperçu des tâches -->
                    <img src="assets/img/tache.jpg" alt="">
                </div>
                <div class="card"> 
                    <!-- Tableau illustrant l'aperçu des tâches -->
                    <img src="assets/img/tache.jpg" alt="">
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-3.6.0.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/bootstrap.bundle.js"></script>
    <script src="assets/js/index.js"></script>
    <script>
        $(document).ready(function() {
            var today = new Date().toISOString().split('T')[0];
            $('#task1-start-date').attr('min', today);
            $('#task1-end-date').attr('min', today);
            $('#task2-start-date').attr('min', today);
            $('#task2-end-date').attr('min', today);
            $('#task3-start-date').attr('min', today);
            $('#task3-end-date').attr('min', today);

            $('#task1-start-date').on('change', function() {
                var dateDebut = $(this).val();
                $('#task1-end-date').attr('min', dateDebut);
            });

            $('#task2-start-date').on('change', function() {
                var dateDebut = $(this).val();
                $('#task2-end-date').attr('min', dateDebut);
            });

            $('#task3-start-date').on('change', function() {
                var dateDebut = $(this).val();
                $('#task3-end-date').attr('min', dateDebut);
            });

            $('#tasks-form').on('submit', function(event) {
                var dateDebut1 = $('#task1-start-date').val();
                var dateFin1 = $('#task1-end-date').val();
                var dateDebut2 = $('#task2-start-date').val();
                var dateFin2 = $('#task2-end-date').val();
                var dateDebut3 = $('#task3-start-date').val();
                var dateFin3 = $('#task3-end-date').val();
                
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

                if (dateFin3 <= dateDebut3) {
                    $('#date-fin3-error').text('La date de fin doit être supérieure à la date de début.').show();
                    isValid = false;
                } else {
                    $('#date-fin3-error').hide();
                }

                if (!isValid) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>

