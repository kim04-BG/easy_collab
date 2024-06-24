<?php
include "connexiondb.php";

if (!isset($_SESSION['id_utilisateur']) || !isset($_GET['id_projet'])) {
    header("Location: connexion.php");
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$id_projet = $_GET['id_projet'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie</title>
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
        .message-container {
            height: 500px;
            overflow-y: scroll;
            padding: 80px;
            
           
        }
        .message {
            margin-bottom: 15px;
        }
        .message.sent {
            text-align: right;
            
        }
        .message.received {
            text-align: left;
        }
        .message .card {
            display: inline-block;
            max-width: 70%;
            padding: 5px;
            border-radius: 20px;
            margin-bottom: -10px; 
            
        }
        .message.sent .card {
            background: linear-gradient(90deg, #f54ea2, #ff7676);
            color: white;
            margin-left: auto;
            
        }
        .message.received .card {
            background: linear-gradient(90deg, #02cb19, #0056b3);
            color: white;
        }
        .input-group-append .btn-attachment {
            border: none;
            background: none;
            font-size: 1.5em;
            color: #007bff;
            cursor: pointer;
        }
        .input-group-append .btn-send {
            background: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            
        }
        .icon-circle {
            width: 40px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            
            justify-content: center;
            
            color: white;
            cursor: pointer;
        }
        .icon-circle i {
            font-size: 1.2em;
        }
    </style>
    <script>
        function validateForm(event) {
            var messageInput = document.getElementById("contenu");
            var fileInput = document.getElementById("fichier");

            if (messageInput.value.trim() === "" && fileInput.files.length === 0) {
                ("Veuillez entrer un message ou sélectionner un fichier.");
                event.preventDefault();
                return false;
            }
        }

        function updateFileName() {
            var fileInput = document.getElementById("fichier");
            var fileNameDisplay = document.getElementById("file-name-display");
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = fileInput.files[0].name;
            } else {
                fileNameDisplay.textContent = "";
            }
        }
    </script>
</head>
<body>
<div class="container">
    <h2 class="text-center">Messagerie du Projet</h2>
    <div id="message-container" class="message-container"></div>
    <form id="message-form" action="envoyer_message.php" method="POST" enctype="multipart/form-data" onsubmit="validateForm(event)">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8">
                <div class="input-group" style="width: 500px; margin-right: 50px">
                        <label class="input-group-text icon-circle" for="fichier"><i class="fas fa-paperclip"></i></label>
                        <input type="file" class="d-none" id="fichier" name="fichier" accept=".zip,.rar,.7z"  onchange="updateFileName()">
                
                    <input type="hidden" name="id_projet" value="<?php echo $id_projet; ?>">
                    <textarea class="form-control" id="contenu" name="contenu" placeholder="Écrire un message..." style="height: 50px;"></textarea>
                    
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary icon-circle"><i class="fas fa-arrow-circle-up"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-2"></div>
        </div>
            
            <div id="file-name-display" style="margin-top: 3px;"></div>
        </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script>
    $(document).ready(function() {
        const id_projet = <?php echo $id_projet; ?>;
        const id_utilisateur = <?php echo $id_utilisateur; ?>;

        function loadMessages() {
            $.get('recevoir_messages.php', {id_projet: id_projet}, function(data) {
                const messages = JSON.parse(data);
                const messageContainer = $('#message-container');
                messageContainer.empty();
                messages.forEach(message => {
                    const messageClass = message.id_utilisateur == id_utilisateur ? 'sent' : 'received';
                    messageContainer.append(`
                        <div class="message ${messageClass}">
                            <div><small>${message.nom} ${message.prenom}</small></div>
                            <div class="card">
                                
                                <div>${message.contenu ? message.contenu : ''}</div>
                                ${message.fichier ? `<div><a href="uploads/${message.fichier}" target="_blank">${message.fichier}</a></div>` : ''}
                                <div><small><small>${message.date_envoi}</small></small></div>
                            </div>
                        </div>
                    `);
                });
                messageContainer.scrollTop(messageContainer[0].scrollHeight);
            });
        }

        $('#file-input').on('change', function() {
            const fileName = this.files[0].name;
            $('#file-name').text(`Fichier sélectionné : ${fileName}`);
        });

        $('#message-form').on('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                url: 'envoyer_message.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    loadMessages();
                    $('#message-form')[0].reset();
                    $('#file-name').text('');
                }
            });
        });

        loadMessages();
        setInterval(loadMessages, 5000); // Rafraîchit les messages toutes les 5 secondes
    });
</script>
</body>
</html>
