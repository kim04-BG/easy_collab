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

// Récupérer les messages pour ce projet
$query_messages = "
    SELECT m.message, m.date_envoi, u.nom, u.prenom, m.id_utilisateur
    FROM messages m
    JOIN utilisateur u ON m.id_utilisateur = u.id_utilisateur
    WHERE m.id_projet = ?
    ORDER BY m.date_envoi ASC
";
$stmt = $conn->prepare($query_messages);
$stmt->bind_param("i", $id_projet);
$stmt->execute();
$result_messages = $stmt->get_result();
$messages = $result_messages->fetch_all(MYSQLI_ASSOC);
$stmt->close();

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie</title>
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .chat-box {
            max-height: 500px;
            overflow-y: scroll;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .message-received {
            text-align: left;
            background-color: #d1e7ff;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
            max-width: 80%;
        }
        .message-sent {
            text-align: right;
            background-color: #ffdde1;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
            max-width: 80%;
            margin-left: auto;
        }
        .chat-input {
            display: flex;
        }
        .chat-input input {
            flex: 1;
            padding: 10px;
        }
        .chat-input button {
            padding: 10px 20px;
        }
    </style>
</head>
<body>
<div class="container">
        <h2 class="mt-4">Chat du projet</h2>
        <div class="chat-box" id="chat-box">
            <?php foreach ($messages as $message) : ?>
                <div class="<?php echo $message['id_utilisateur'] == $id_utilisateur ? 'message-sent' : 'message-received'; ?>">
                    <strong><?php echo htmlspecialchars($message['nom'] . ' ' . $message['prenom']); ?></strong><br>
                    <small class="text-muted"><?php echo htmlspecialchars($message['date_envoi']); ?></small>
                    <p><?php echo htmlspecialchars($message['message']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <form id="chat-form" class="chat-input">
            <input type="text" id="message" placeholder="Tapez votre message..." required>
            <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
    </div>

    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="assets/js/index.js"></script>
    
</body>
</html>
