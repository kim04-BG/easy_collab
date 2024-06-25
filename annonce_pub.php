<?php

include "connexiondb.php";

$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupérer toutes les publications
$query_publications = "
    SELECT pub.id_publication, p.id_projet, p.titre AS projet_titre, pub.titre, pub.contenu, pub.date_publication, p.id_chef
    FROM publications pub 
    JOIN projet p ON pub.id_projet = p.id_projet
";
$stmt = $conn->prepare($query_publications);
if (!$stmt) {
    die('Erreur lors de la préparation de la requête: ' . $conn->error);
}
$stmt->execute();
$result_publications = $stmt->get_result();
$publications = $result_publications->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Récupérer les demandes d'adhésion de l'utilisateur connecté
$query_demandes = "SELECT id_publication, statut FROM demandes_adhesion WHERE id_utilisateur = ?";
$stmt = $conn->prepare($query_demandes);
if (!$stmt) {
    die('Erreur lors de la préparation de la requête: ' . $conn->error);
}
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$result_demandes = $stmt->get_result();
$demandes = $result_demandes->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$demandes_publications = [];
foreach ($demandes as $demande) {
    $demandes_publications[$demande['id_publication']] = $demande['statut'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publication des Projets</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Publications</h2>
        <?php if (isset($_SESSION['message'])) : ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Affichage des publications -->
        <div class="row">
            <?php foreach ($publications as $publication) : ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title" style="font-size: 180%;"><?php echo htmlspecialchars($publication['titre']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($publication['contenu']); ?></p>
                            <p class="card-text">Projet: <?php echo htmlspecialchars($publication['projet_titre']); ?></p>
                            <p class="card-text">Date de publication: <?php echo htmlspecialchars($publication['date_publication']); ?></p>
                            <?php if ($publication['id_chef'] != $id_utilisateur): ?>
                                <?php if (isset($demandes_publications[$publication['id_publication']])): ?>
                                    <p class="card-text">Demande: <?php echo htmlspecialchars($demandes_publications[$publication['id_publication']]); ?></p>
                                <?php else: ?>
                                    <a href="demander_adhesion.php?id_publication=<?php echo $publication['id_publication']; ?>" class="btn btn-primary">Demander à rejoindre</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="card-text"><em>Vous avez créer cette publication</em></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="assets/js/jquery-3.6.0.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/bootstrap.bundle.js"></script>
</body>
</html>
