<?php
/**
 * Exemple d'utilisation de la classe PHPMail, librement repris des exemples du dépôt GIT
 * https://github.com/PHPMailer/PHPMailer
 */

require_once __DIR__ . "/../autoload.php";

//Create a new PHPMailer instance
$mail = new PHPMailer();
//Tell PHPMailer to use SMTP
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 2;
//Set the hostname and port of the mail server
$mail->Host = 'sympa'; $mail->Port = 25;
// Do not use TLS even if available
$mail->SMTPAutoTLS = false;
//Set who the message is to be sent from
$mail->setFrom('Alonzo.Mathon@etu.univ-grenoble-alpes.fr', 'Mathieu Dessolin');
//Set who the message is to be sent to
$mail->addAddress('Alonzo.Mathon@etu.univ-grenoble-alpes.fr', 'Les super Canards');
// Set the character set to use
$mail->CharSet = "UTF-8";
//Set the subject line
$mail->Subject = "Essai d'envoi d'un courriel";
$mail->Body = "Le corps du message avec quelques caractères accentués pour\r\nvérifier que l'encodage fonctionne.";

//send the message, check for errors
if (!$mail->send()) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message sent!';
}
