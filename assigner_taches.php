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

// Récupérer l'ID du projet depuis l'URL
if (isset($_GET['id_projet'])) {
    $id_projet = $_GET['id_projet'];
} else {
    // Rediriger ou afficher un message d'erreur si l'ID du projet est manquant
    echo "ID du projet manquant.";
    exit();
}

$query_taches = $conn->prepare("SELECT id_tache, titre FROM tache WHERE id_projet = ? AND (id_ass IS NULL OR id_ass = 0)");
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_tache = $_POST['id_tache'];
    $id_utilisateur = $_POST['id_utilisateur'];

    $query_assign = $conn->prepare("UPDATE tache SET id_ass = ? WHERE id_tache = ?");
    $query_assign->bind_param("ii", $id_utilisateur, $id_tache);
    $query_assign->execute();
    $query_assign->close();

    header("Location: mestaches.php?id_projet=$id_projet");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigner des Tâches</title>
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
                    ASSIGNER DES TACHES
                </div>
                <div class="">
                <div class="card-body">
                    <form method="POST" action="assigner_taches.php?id_projet=<?php echo $id_projet; ?>">
                        <div class="form-group">
                            <label for="id_tache">Tâche</label>
                            <select class="form-control" id="id_tache" name="id_tache">
                                <?php foreach ($taches as $tache) : ?>
                                    <option value="<?php echo $tache['id_tache']; ?>"><?php echo htmlspecialchars($tache['titre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_utilisateur">Membre</label>
                            <select class="form-control" id="id_utilisateur" name="id_utilisateur">
                                <?php foreach ($membres as $membre) : ?>
                                    <option value="<?php echo $membre['id_utilisateur']; ?>"><?php echo htmlspecialchars($membre['nom'] . ' ' . $membre['prenom']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="text-center">
                            <div class="row">
                                <div class="col-2"></div>
                                <div class="col-4">
                                    <button type="submit"  class="btn btn-success">Assigner</button>
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
