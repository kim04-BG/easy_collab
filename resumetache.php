<?php
// Assurez-vous qu'aucune sortie n'est envoyée avant les en-têtes HTTP
ob_start();
include "connexiondb.php"; // Inclut le fichier de connexion à la base de données

// Récupérer l'ID du projet depuis l'URL
if (isset($_GET['id_projet'])) {
    $id_projet = $_GET['id_projet'];
} else {
    // Rediriger ou afficher un message d'erreur si l'ID du projet est manquant
    echo "ID du projet manquant.";
    exit();
}

// Récupérer les informations du projet
$query_projet = "SELECT titre FROM projet WHERE id_projet = ?";
$stmt = $conn->prepare($query_projet);
if (!$stmt) {
    die("Erreur de préparation de la requête : " . $conn->error);
}
$stmt->bind_param("i", $id_projet);
$stmt->execute();
$stmt->bind_result($titre_projet);
$stmt->fetch();
$stmt->close();

// Récupérer les statistiques des tâches pour le projet en cours
$result_terminées = mysqli_query($conn, "SELECT COUNT(*) AS count FROM tache WHERE statut_tache = 'termine' AND id_projet = $id_projet");
$row_terminées = mysqli_fetch_assoc($result_terminées);
$tâches_terminées = $row_terminées['count'];

$result_non_terminées = mysqli_query($conn, "SELECT COUNT(*) AS count FROM tache WHERE statut_tache != 'termine' AND id_projet = $id_projet");
$row_non_terminées = mysqli_fetch_assoc($result_non_terminées);
$tâches_non_terminées = $row_non_terminées['count'];

$result_retard = mysqli_query($conn, "SELECT COUNT(*) AS count FROM tache WHERE statut_tache != 'termine' AND date_fin < CURDATE() AND id_projet = $id_projet");
$row_retard = mysqli_fetch_assoc($result_retard);
$tâches_retard = $row_retard['count'];

$result_total = mysqli_query($conn, "SELECT COUNT(*) AS count FROM tache WHERE id_projet = $id_projet");
$row_total = mysqli_fetch_assoc($result_total);
$tâches_total = $row_total['count'];

$query_tasks_by_status = "SELECT 
    CASE 
        WHEN statut_tache = 'en_attente' THEN 'En attente'
        WHEN statut_tache = 'en_cours' THEN 'En cours'
        WHEN statut_tache = 'termine' THEN 'Terminé'
    END AS statut,
    COUNT(*) AS nombre_taches 
FROM tache 
WHERE id_projet = $id_projet
GROUP BY statut_tache";
$result_tasks_by_status = mysqli_query($conn, $query_tasks_by_status);

$labels = [];
$data = [];
while ($row = mysqli_fetch_assoc($result_tasks_by_status)) {
    $labels[] = $row['statut'];
    $data[] = $row['nombre_taches'];
}

$query_total_tasks = "SELECT COUNT(*) AS total FROM tache WHERE id_projet = ?";
$stmt_total_tasks = $conn->prepare($query_total_tasks);
if (!$stmt_total_tasks) {
    die("Erreur de préparation de la requête : " . $conn->error);
}
$stmt_total_tasks->bind_param("i", $id_projet);
$stmt_total_tasks->execute();
$result_total_tasks = $stmt_total_tasks->get_result();
$row_total_tasks = $result_total_tasks->fetch_assoc();
$total_tasks = $row_total_tasks['total'];
$stmt_total_tasks->close();

$query_completed_tasks = "SELECT COUNT(*) AS completed FROM tache WHERE statut_tache = 'termine' AND id_projet = ?";
$stmt_completed_tasks = $conn->prepare($query_completed_tasks);
if (!$stmt_completed_tasks) {
    die("Erreur de préparation de la requête : " . $conn->error);
}
$stmt_completed_tasks->bind_param("i", $id_projet);
$stmt_completed_tasks->execute();
$result_completed_tasks = $stmt_completed_tasks->get_result();
$row_completed_tasks = $result_completed_tasks->fetch_assoc();
$completed_tasks = $row_completed_tasks['completed'];
$stmt_completed_tasks->close();

if ($total_tasks > 0) {
    $pourcentage_taches_terminees = ($completed_tasks / $total_tasks) * 100;
} else {
    $pourcentage_taches_terminees = 0;
}

$pourcentage_taches_non_terminees = 100 - $pourcentage_taches_terminees;

$query_taches = $conn->prepare("SELECT id_tache, titre, description, date_debut, date_fin, statut_tache FROM tache WHERE id_projet = ?");
if (!$query_taches) {
    die("Erreur de préparation de la requête : " . $conn->error);
}
$query_taches->bind_param("i", $id_projet);
$query_taches->execute();
$result_taches = $query_taches->get_result();
$taches = $result_taches->fetch_all(MYSQLI_ASSOC);
$query_taches->close();



$query_membres = $conn->prepare("SELECT utilisateur.id_utilisateur, utilisateur.nom, utilisateur.prenom 
                                 FROM utilisateur 
                                 JOIN equipe ON utilisateur.id_utilisateur = equipe.id_utilisateur 
                                 WHERE equipe.id_projet = ?");
if (!$query_membres) {
    die("Erreur de préparation de la requête : " . $conn->error);
}
$query_membres->bind_param("i", $id_projet);
$query_membres->execute();
$result_membres = $query_membres->get_result();
$membres = $result_membres->fetch_all(MYSQLI_ASSOC);
$query_membres->close();



ob_end_flush();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EASYCOLLAB - Tableau de bord - Résumé des tâches</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .bg-gradient-rose-red {
            background: linear-gradient(90deg, #ff007f, #ff0040);
            color: white;
        }

    </style>
</head>
<body class="with-welcome-text">
    <div class="d-sm-flex align-items-center justify-content-between border-bottom">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"></li>
            <li class="nav-item"></li>            
            <li class="nav-item"></li>             
        </ul>                  
        <div>
            <div class="btn-wrapper">
                
            </div>
        </div>                  
    </div>     

    <h2 class="text-center"><B><?php echo htmlspecialchars($titre_projet); ?></B></h2><br>
<div class="container">
    <h2>Tableau de bord</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tâches terminées</h5>
                    <p style="font-size: 01cm;" class="card-text"><?php echo $tâches_terminées; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tâches non terminées</h5>
                    <p style="font-size: 01cm;" class="card-text"><?php echo $tâches_non_terminées; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tâches en retard</h5>
                    <p style="font-size: 01cm;" class="card-text"><?php echo $tâches_retard; ?></p>
                </div>
            </div>
        </div>
    </div><br>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tâches par section non terminée</h5>
                    <div id="performanceLine-legend"></div>
                    <div class="chartjs-wrapper mt-4">
                        <canvas id="taskChart" width=""></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <!-- Card pour le pourcentage de tâches non terminées -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pourcentage de tâches terminées</h5>
                    <div>
                        <canvas class="my-auto" id="doughnut-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- tableau récapitulatif des tâches avec les actions-->       
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Details des tâches</h5>
                    <div class="d-flex justify-content-end mb-3">
                        <a href="ajout2tache.php?id_projet=<?php echo $id_projet; ?>" class="btn btn-primary" role="button"><i class="fas fa-plus"></i>Ajouter Tâches</a>
                    </div>
                        <table class=" table-bordered">
                            <thead>
                                <tr class="card-header bg-gradient-rose-red text-white">
                                    <th>ID Tâche</th>
                                    <th>Titre</th>
                                    <th>Description</th>
                                    <th>Date de début</th>
                                    <th>Date de fin</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
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
                                        <td>
                                            <a href="modifier_tache.php?id=<?php echo $tache['id_tache']; ?>&id_projet=<?php echo $id_projet; ?>" class="btn btn-success btn-sm">Modifier</a>
                                            <a href="supprimer_tache.php?id=<?php echo $tache['id_tache']; ?>&id_projet=<?php echo $id_projet; ?>" class="btn btn-danger btn-sm">Retirer</a>
                                        </td>
                                    
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                </div>
            </div><br>
    
    <!-- Bouton et tableau assignationrécapitulatif des tâches -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Assignation des tâches</h5>
            <div class="d-flex justify-content-end mb-3">
                <a href="assigner_taches.php?id_projet=<?php echo $id_projet; ?>" class="btn btn-primary">Assigner des tâches</a>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr class="card-header bg-gradient-rose-red text-white">
                        <th>ID Tâche</th>
                        <th>Titre</th>
                        <th>Assigné à</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($taches as $tache) : ?>
                        <tr>
                            <td><?php echo $tache['id_tache']; ?></td>
                            <td><?php echo htmlspecialchars($tache['titre']); ?></td>
                            <td>
                                <?php
                                $query_assignation = $conn->prepare("SELECT nom, prenom FROM utilisateur WHERE id_utilisateur = (SELECT id_ass FROM tache WHERE id_tache = ?)");
                                $query_assignation->bind_param("i", $tache['id_tache']);
                                $query_assignation->execute();
                                $result_assignation = $query_assignation->get_result();
                                $assignation = $result_assignation->fetch_assoc();
                                $query_assignation->close();
                                if ($assignation) {
                                    echo htmlspecialchars($assignation['nom'] . ' ' . $assignation['prenom']);
                                } else {
                                    echo '<div class="text-danger"><B>Non assigné</B></div>';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="modifier_assignation.php?id_tache=<?php echo $tache['id_tache']; ?>&id_projet=<?php echo $id_projet; ?>" class="btn btn-success btn-sm">Modifier</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
</div>


<!-- container-scroller -->
<!-- plugins:js -->
<script src="assets/vendors/chart.js/chart.umd.js"></script>
<script src="assets/vendors/progressbar.js/progressbar.min.js"></script>
<!-- End plugin js for this page -->
<!-- inject:js -->
<script src="assets/js/off-canvas.js"></script>
<script src="assets/js/template.js"></script>
<script src="assets/js/settings.js"></script>
<script src="assets/js/hoverable-collapse.js"></script>
<script src="assets/js/todolist.js"></script>
<!-- endinject -->
<!-- Custom js for this page-->
<script>
    // Récupérer les données de votre base de données
    var labels = <?php echo json_encode($labels); ?>;
    var data = <?php echo json_encode($data); ?>;

    // Créer le graphique à barres
    var ctx = document.getElementById('taskChart').getContext('2d');
    var taskChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Tâches par statut',
                data: data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(75, 192, 192, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<script>
    var ctx = document.getElementById('doughnut-chart').getContext('2d');
    var doughnutChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Tâches terminées', 'Tâches non terminées'],
            datasets: [{
                label: 'Tâches',
                data: [<?php echo $pourcentage_taches_terminees; ?>, <?php echo $pourcentage_taches_non_terminees; ?>],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(255, 99, 132, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Tâches par statut d\'achèvement'
                }
            }
        }
    });
</script>
</body>
</html>
