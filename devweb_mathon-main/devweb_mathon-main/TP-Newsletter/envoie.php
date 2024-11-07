<?php
require_once "autoload.php";

// Contrôleur

session_start();
if (isset($_POST['Logout'])){
    $_SESSION['role'] = "Logout";
}
if ($_SESSION['role'] != 'ROLE_ADMIN' and $_SESSION['role'] != 'ROLE_REDACTEUR') {
    header("Location: connexion.php");
}

// Contrôleur


//Envoie du mail 
if(isset($_POST['objet']) & isset($_POST['contenue'])){

    $dao = new InscritsDAO(MaBD::getInstance());

    //Create a new PHPMailer instance
    $mail = new PHPMailer();
    //Tell PHPMailer to use SMTP
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    //Set the hostname and port of the mail server
    $mail->Host = 'sympa'; $mail->Port = 25;
    // Do not use TLS even if available
    $mail->SMTPAutoTLS = false;
    //Set who the message is to be sent from
    $mail->setFrom($_SESSION['mail'],$_SESSION['prenom']." ".$_SESSION['nom']);
    //Set who the message is to be sent to
    $count=0;
    $all= $dao->getAllInscrit();
    foreach($all as $inscrit){
        $mail->addAddress($inscrit->getMail(), $inscrit->getSurname() .' '. $inscrit->getName());
        $count +=1;
    }
        // Set the character set to use
    $mail->CharSet = "UTF-8";
    //Set the subject line
    $mail->Subject = $_POST['objet'];
    $mail->Body = $_POST['contenue'];

//send the message, check for errors
if (!$mail->send()) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo  "Vous avez envoyé ".$count." mail";
}
}
//Envoie du mail 

$message = "Bonjour";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" type="text/css" href="newsletter.css" />
    <title>Liste des inscrits</title>
</head>

<body>
    <div id="bandeau">
        <div id="titre">
            <img src="LogoIUT-V-300x300.png" alt="Logo UGA IUT" />
            &nbsp;&nbsp;&nbsp;&nbsp;Newsletter
        </div>
        <form method="post">
        <?php
        echo $_SESSION['nom'] . " " . $_SESSION['prenom'];
        ?>
        <input type="submit" name="Logout[]" value="Déconnection">
        </form>
    </div>

    </div>
    <h1>Envoyez un mail</h1>
    <p></p>
    <p class="binome">Réalisée par : Alonzo MATHON - Groupe 2G</p>


    <form id='formulaire' method="post">
    <input type="text" name="objet" id="objet">
    <textarea name="contenue" id="contenue" cols="30" rows="10"></textarea>
    <input type="submit" value="Envoyer le mail">
    </form>

</body>

</html>