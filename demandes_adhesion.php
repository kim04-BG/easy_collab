<?php

include "connexiondb.php";



$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupérer les demandes d'adhésion pour les projets du chef de projet connecté
$query_demandes = "
    SELECT da.id_demande, u.nom, u.prenom, p.titre AS projet_titre, da.statut, da.date_demande
    FROM demandes_adhesion da
    JOIN utilisateur u ON da.id_utilisateur = u.id_utilisateur
    JOIN projet p ON da.id_projet = p.id_projet
    WHERE p.id_chef = ?
";
$stmt = $conn->prepare($query_demandes);
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$result_demandes = $stmt->get_result();
$demandes = $result_demandes->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demandes d'Adhésion</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Demandes d'Adhésion</h2>
        <?php foreach ($demandes as $demande) : ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($demande['nom'] . ' ' . $demande['prenom']); ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($demande['projet_titre']); ?></h6>
                    <p class="card-text">Statut: <?php echo htmlspecialchars($demande['statut']); ?></p>
                    <p class="card-text">Date de demande: <?php echo htmlspecialchars($demande['date_demande']); ?></p>
                    <?php if ($demande['statut'] == 'en_attente'): ?>
                        <a href="gerer_demande.php?id_demande=<?php echo $demande['id_demande']; ?>&action=accepter" class="btn btn-success">Accepter</a>
                        <a href="gerer_demande.php?id_demande=<?php echo $demande['id_demande']; ?>&action=refuser" class="btn btn-danger">Refuser</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <script src="assets/js/jquery-3.6.0.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/bootstrap.bundle.js"></script>
</body>
</html>
