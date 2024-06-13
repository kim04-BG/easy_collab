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

if (isset($_POST['ajouter_membre'])) {
    $id_membre = $_POST['id_membre'];
    $id_ass = $id_membre;

    $stmt = $conn->prepare("INSERT INTO equipe (id_projet, id_utilisateur, id_chef, id_ass) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $id_projet, $id_membre, $id_utilisateur, $id_membre);

    if ($stmt->execute()) {
        echo '<script>alert("Membre ajouté avec succès."); window.location.href = "equipe.php?id_projet='.$id_projet.'";</script>';
    } else {
        echo '<script>alert("Erreur lors de l\'ajout du membre.");</script>';
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Membre</title>
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
                AJOUTER MEMBRE
            </div>
            <div class="">
            <div class="card-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="id_membre">Sélectionnez un Membre</label>
                        <select name="id_membre" id="id_membre" class="form-control" required>
                            <?php
                            $query_membres = "SELECT id_utilisateur, nom, prenom FROM utilisateur WHERE id_utilisateur NOT IN (SELECT id_utilisateur FROM equipe WHERE id_projet = ?) AND id_utilisateur != ?";
                            $stmt = $conn->prepare($query_membres);
                            $stmt->bind_param("ii", $id_projet, $id_utilisateur);
                            $stmt->execute();
                            $result_membres = $stmt->get_result();

                            while ($row = $result_membres->fetch_assoc()) {
                                echo '<option value="'.$row['id_utilisateur'].'">'.$row['nom'].' '.$row['prenom'].'</option>';
                            }

                            $stmt->close();
                            ?>
                        </select>
                    </div>
                    <div class="text-center">
                        <div class="row">
                            <div class="col-2"></div>
                            <div class="col-4">
                                <button type="submit" name="ajouter_membre" class="btn btn-success">Ajouter</button>
                            </div>
                            <div class="col-4">
                                <a href="equipe.php?id_projet=<?php echo $id_projet; ?>" class="btn btn-secondary">Annuler</a>
                            </div>
                            <div class="col-2"></div>
                        </div>
                    </div>
                    
                </form>
            </div>
            </div>
            
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="assets/js/index.js"></script>
</body>
</html>
