<?php

include "connexiondb.php";

$id_projet = $_GET['id_projet'];

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

foreach ($messages as $message) {
    echo '<div class="' . ($message['id_utilisateur'] == $_SESSION['id_utilisateur'] ? 'message-sent' : 'message-received') . '">';
    echo '<strong>' . htmlspecialchars($message['nom'] . ' ' . $message['prenom']) . '</strong><br>';
    echo '<small class="text-muted">' . htmlspecialchars($message['date_envoi']) . '</small>';
    echo '<p>' . htmlspecialchars($message['message']) . '</p>';
    echo '</div>';
}
?>
