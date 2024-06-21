<?php
session_start();
include "connexiondb.php";

if (!isset($_SESSION['id_utilisateur'])) {
    echo json_encode(["status" => "error", "message" => "Non connecté"]);
    exit();
}

$id_projet = $_GET['id_projet'];

$query = "SELECT m.contenu, m.fichier, m.date_envoi, u.nom, u.prenom, m.id_utilisateur
          FROM message m
          JOIN utilisateur u ON m.id_utilisateur = u.id_utilisateur
          WHERE m.id_projet = ?
          ORDER BY m.date_envoi ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_projet);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
$stmt->close();
?>
