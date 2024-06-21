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
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.0.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .message-container {
            height: 500px;
            overflow-y: scroll;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #fff;
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
            padding: 10px;
            border-radius: 15px;
        }
        .message.sent .card {
            background: linear-gradient(90deg, #f54ea2, #ff7676);
            color: white;
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
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container card card-body">
    <h2 class="text-center">Messagerie du Projet</h2>
    <div id="message-container" class="message-container"></div>
    <form id="message-form" enctype="multipart/form-data">
        <div class="input-group mt-3">
            <input type="hidden" name="id_projet" value="<?php echo $id_projet; ?>">
            <div class="input-group-append">
            <label for="file-input" class="btn-attachment">
                    <i class="fas fa-paperclip"></i>
                </label>
                <input type="file" id="file-input" name="fichier" accept=".zip,.rar,.7zip" style="display: none;">
            </div>
            
            <input type="text" name="contenu" class="form-control" placeholder="Entrez votre message">
            <div class="input-group-append">
                
                <button type="submit" class="btn-send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
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
                }
            });
        });

        loadMessages();
        setInterval(loadMessages, 5000); // Rafraîchit les messages toutes les 5 secondes
    });
</script>
</body>
</html>
