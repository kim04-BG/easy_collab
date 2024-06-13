<?php
session_start();
include "connexiondb.php";

if (!isset($_SESSION['id_utilisateur']) || !isset($_SESSION['id_projet'])) {
    header("Location: connexion.php");
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$id_projet = $_SESSION['id_projet'];

// Activer le rapport d'erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Récupérer les informations de l'utilisateur connecté
$query_utilisateur = "SELECT nom, prenom, email FROM utilisateur WHERE id_utilisateur = ?";
$stmt = $conn->prepare($query_utilisateur);
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$stmt->bind_result($nom_utilisateur, $prenom_utilisateur, $email_utilisateur);
$stmt->fetch();
$stmt->close();

// Récupérer les informations du projet en cours
$query_projet = "SELECT titre FROM projet WHERE id_projet = ?";
$stmt = $conn->prepare($query_projet);
$stmt->bind_param("i", $id_projet);
$stmt->execute();
$stmt->bind_result($nom_projet);
$stmt->fetch();
$stmt->close();

if (isset($_POST['ajouter_equipe'])) {
    $id_chef = $id_utilisateur;
    $id_ass = $id_membre;

    $membres = array_filter([
        $_POST['id_membre1'] ?? null,
        $_POST['id_membre2'] ?? null,
        $_POST['id_membre3'] ?? null
    ]);

    $stmt = $conn->prepare("INSERT INTO equipe (id_projet, id_utilisateur, id_chef, id_ass) VALUES (?, ?, ?, ?)");
    foreach ($membres as $id_membre) {
        $stmt->bind_param("iiii", $id_projet, $id_membre, $id_chef, $id_ass);
        if (!$stmt->execute()) {
            echo '<script>alert("Erreur lors de l\'ajout de l\'équipe : ' . $stmt->error . '");</script>';
            break;
        }
    }

    if ($stmt->affected_rows > 0) {
        echo '<script>alert("Équipe ajoutée avec succès."); window.location.href = "tableau_de_bord_chef.php";</script>';
    } else {
        echo '<script>alert("Aucun membre ajouté.");</script>';
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
    <title>EASYCOLLAB - Ajout des Membres de l'Équipe</title>
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body id="background" background="">

<section id="c-pro">
    <div class="">
    <h2><b><?php echo htmlspecialchars($nom_projet); ?></b></h2>
        <h2 class="">Préparez-vous à donner vie à votre vision <b><?php echo htmlspecialchars($prenom_utilisateur); ?>!</b></h2>
        <p class="lead">Lancez-vous dans la création de votre projet avec facilité et inspiration</p>
    </div>
</section>

<div class="container custom-container">
    <div class="row">
        <div class="col-md-6">
            <h2>Ajout des Membres de l'Équipe</h2>
            <form id="add-member-form" method="POST">
                <div class="form-group">
                    <label for="project-name">Nom du projet :</label>
                    <input type="text" class="form-control" id="project-name" value="<?php echo htmlspecialchars($nom_projet); ?>" readonly>
                </div>
                <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="form-group">
                    <label for="member-name<?php echo $i; ?>">Nom du Collègue <?php echo $i; ?> :</label>
                    <select class="form-control" id="member-name<?php echo $i; ?>" name="id_membre<?php echo $i; ?>">
                        <option value="">Sélectionnez un membre</option>
                        <?php
                        $resultats = mysqli_query($conn, "SELECT id_utilisateur, CONCAT(nom, ' ', prenom) AS nom_complet FROM utilisateur WHERE id_utilisateur != $id_utilisateur");
                        while ($row = mysqli_fetch_assoc($resultats)) {
                            echo "<option value='" . $row['id_utilisateur'] . "'>" . $row['nom_complet'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <?php endfor; ?>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary" name="ajouter_equipe">Ajouter Membres</button>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <h2>Aperçu de la Collaboration</h2>
            <div class="chat-preview">
                <img src="assets/img/chat.png" alt="">
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="assets/js/index.js"></script>
</body>
</html>
