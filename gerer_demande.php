<?php
session_start();
include "connexiondb.php";

$id_demande = $_GET['id_demande'];
$action = $_GET['action'];

// Récupérer les informations de la demande
$query_demande = "SELECT id_utilisateur, id_projet FROM demandes_adhesion WHERE id_demande = ?";
$stmt = $conn->prepare($query_demande);
if (!$stmt) {
    die('Erreur lors de la préparation de la requête: ' . $conn->error);
}
$stmt->bind_param("i", $id_demande);
$stmt->execute();
$stmt->bind_result($id_utilisateur, $id_projet);
$stmt->fetch();
$stmt->close();

if ($action == 'accepter') {
    // Récupérer l'id_chef du projet
    $query_projet = "SELECT id_chef FROM projet WHERE id_projet = ?";
    $stmt = $conn->prepare($query_projet);
    if (!$stmt) {
        die('Erreur lors de la préparation de la requête: ' . $conn->error);
    }
    $stmt->bind_param("i", $id_projet);
    $stmt->execute();
    $stmt->bind_result($id_chef);
    $stmt->fetch();
    $stmt->close();

    // Vérifier si l'utilisateur est déjà membre de l'équipe du projet
    $query_verif = "SELECT COUNT(*) FROM equipe WHERE id_projet = ? AND id_utilisateur = ?";
    $stmt = $conn->prepare($query_verif);
    if (!$stmt) {
        die('Erreur lors de la préparation de la requête: ' . $conn->error);
    }
    $stmt->bind_param("ii", $id_projet, $id_utilisateur);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count == 0) {
        // Ajouter l'utilisateur à l'équipe du projet
        $stmt = $conn->prepare("INSERT INTO equipe (id_projet, id_utilisateur, id_chef) VALUES (?, ?, ?)");
        if (!$stmt) {
            die('Erreur lors de la préparation de la requête: ' . $conn->error);
        }
        $stmt->bind_param("iii", $id_projet, $id_utilisateur, $id_chef);
        if (!$stmt->execute()) {
            $_SESSION['message'] = "Erreur lors de l'acceptation de la demande : " . $stmt->error;
            $_SESSION['message_type'] = "danger";
        } else {
            // Mettre à jour le statut de la demande
            $stmt = $conn->prepare("UPDATE demandes_adhesion SET statut = 'accepte' WHERE id_demande = ?");
            if (!$stmt) {
                die('Erreur lors de la préparation de la requête: ' . $conn->error);
            }
            $stmt->bind_param("i", $id_demande);
            $stmt->execute();
            $stmt->close();

            $_SESSION['message'] = "Demande acceptée avec succès.";
            $_SESSION['message_type'] = "success";
        }
    } else {
        $_SESSION['message'] = "L'utilisateur fait déjà partie de l'équipe.";
        $_SESSION['message_type'] = "warning";
    }
} elseif ($action == 'refuser') {
    // Mettre à jour le statut de la demande
    $stmt = $conn->prepare("UPDATE demandes_adhesion SET statut = 'refuse' WHERE id_demande = ?");
    if (!$stmt) {
        die('Erreur lors de la préparation de la requête: ' . $conn->error);
    }
    $stmt->bind_param("i", $id_demande);
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = "Demande refusée.";
    $_SESSION['message_type'] = "warning";
}

header("Location: publication.php");
exit();
?>
