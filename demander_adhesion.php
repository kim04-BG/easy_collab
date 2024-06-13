<?php
session_start();
include "connexiondb.php";

$id_utilisateur = $_SESSION['id_utilisateur'];
$id_publication = $_GET['id_publication'];

// Récupérer les informations de la publication pour obtenir l'id_projet
$query_publication = "SELECT id_projet FROM publications WHERE id_publication = ?";
$stmt = $conn->prepare($query_publication);
if (!$stmt) {
    die('Erreur lors de la préparation de la requête: ' . $conn->error);
}
$stmt->bind_param("i", $id_publication);
$stmt->execute();
$stmt->bind_result($id_projet);
$stmt->fetch();
$stmt->close();

// Vérifier si l'utilisateur a déjà envoyé une demande pour cette publication
$query_verif = "SELECT id_demande FROM demandes_adhesion WHERE id_utilisateur = ? AND id_publication = ?";
$stmt = $conn->prepare($query_verif);
if (!$stmt) {
    die('Erreur lors de la préparation de la requête: ' . $conn->error);
}
$stmt->bind_param("ii", $id_utilisateur, $id_publication);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $_SESSION['message'] = "Vous avez déjà envoyé une demande pour cette publication.";
    $_SESSION['message_type'] = "warning";
} else {
    // Insérer la demande dans la base de données
    $query_insert = "INSERT INTO demandes_adhesion (id_utilisateur, id_projet, id_publication, statut, date_demande) VALUES (?, ?, ?, 'en_attente', NOW())";
    $stmt = $conn->prepare($query_insert);
    if (!$stmt) {
        die('Erreur lors de la préparation de la requête: ' . $conn->error);
    }
    $stmt->bind_param("iii", $id_utilisateur, $id_projet, $id_publication);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Votre demande a été envoyée.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Erreur lors de l'envoi de la demande : " . $stmt->error;
        $_SESSION['message_type'] = "danger";
    }
}
$stmt->close();

header("Location: publication.php");
exit();
?>
