<?php 
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    
    require_once &quot;vendor/autoload.php&quot;;

    // Connexion à la base de données (ajustez les paramètres selon votre configuration)
   
    $pdo = new PDO("mysql:host=localhost; dbname=leroy", "vivien", "vivien");

    if ($pdo->connect_error) {
        die("Erreur de connexion à la base de données : " . $pdo->connect_error);
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = htmlspecialchars($_POST["name"]);
        $email = htmlspecialchars($_POST["adress"]);
        $sujet = htmlspecialchars($_POST["subject"]);
        $message = htmlspecialchars($_POST["text"]);
    
        if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
            echo "Veuillez remplir tous les champs.";
        } else {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Adresse e-mail invalide.";
            } else {
                // Envoi du formulaire par e-mail avec PHPMailer
                $mail = new PHPMailer(true);
    
                try {
                    // code PHPMailer 
                    $mail->isSMTP();
                    $mail->Host = 'smtp.votreserveur.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'votre_nom_utilisateur';
                    $mail->Password = 'votre_mot_de_passe';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    // Destinataire et contenu du message
                    $mail->setFrom($email, $nom);
                    $mail->addAddress('votre@email.com'); // Remplacez par votre adresse e-mail
                    $mail->Subject = "Nouveau message de $nom : $sujet";
                    $mail->Body = "Nom: $nom\nEmail: $email\nSujet: $sujet\n\nMessage:\n$message";

                    //encodage 
                    $mail->CharSet = 'UTF-8';
                    $mail->Encoding = 'base64';
                    // Envoi du mail
                    $mail->send();
                    echo "Formulaire envoyé avec succès!";
    
                    // Écriture dans la base de données
                    $sql = "insert into user(name, adress, subject, text) values(?, ?, ?, ?)";
                    if ($pdo->query($sql) === TRUE) {
                        echo "Données enregistrées dans la base de données.";
                    } else {
                        echo "Erreur d'insertion dans la base de données : " . $pdo->error;
                    }
                } catch (Exception $e) {
                    echo "Erreur lors de l'envoi du formulaire: {$mail->ErrorInfo}";
                }
            }
        }
        
        // Fermer la connexion à la base de données
        $pdo->close();
    }
    ?>
    


