<?php
session_start();
include "connexiondb.php";

$upload_dir = "uploads";

// Vérifie si le répertoire existe, sinon le crée
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if (!isset($_SESSION['id_utilisateur'])) {
    echo json_encode(["status" => "error", "message" => "Non connecté"]);
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$id_projet = $_POST['id_projet'];
$contenu = isset($_POST['contenu']) ? trim($_POST['contenu']) : '';
$fichier = '';

// Vérifie qu'un message ou un fichier a été fourni
if (empty($contenu) && empty($_FILES['fichier']['name'])) {
    echo json_encode(["status" => "error", "message" => "Veuillez entrer un message ou sélectionner un fichier."]);
    exit();
}

if (!empty($_FILES['fichier']['name'])) {
    $target_dir = "uploads/";
    $fichier = $target_dir . basename($_FILES["fichier"]["name"]);
    if (move_uploaded_file($_FILES["fichier"]["tmp_name"], $fichier)) {
        $fichier = basename($_FILES["fichier"]["name"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Erreur lors du téléchargement du fichier"]);
        exit();
    }
}

$query = "INSERT INTO message (id_projet, id_utilisateur, contenu, fichier) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiss", $id_projet, $id_utilisateur, $contenu, $fichier);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Message envoyé"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erreur lors de l'envoi du message"]);
}

$stmt->close();
?>
