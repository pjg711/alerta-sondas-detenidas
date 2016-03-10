<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_email
 *
 * @author pablo
 */

require 'lib/PHPMailer-master/PHPMailerAutoload.php';

class EMAIL 
{
    function envio_emails($informe,$usuario,$fecha,$emails)
    {
        $informe_html="
        <html>
            <head>
                <title>Informe de sondas para usuario ".$usuario.", fecha ".$fecha."</title>
                <meta charset=\"UTF-8\">
                <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
            </head>
            <body>
                <div align=\"center\">
                    <a href=\"http://www.seedmech.com\"><img src=\"img/seedmech_agrotecnologia.png\" height=\"90\" width=\"340\" alt=\"Seedmech\"></a>
                </div>";
        $informe_html.=  presento_informe($informe);
        $informe_html.="</body></html>";
        //
        $mail = new PHPMailer;
        $mail->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = MAIL_HOST;
        // use
        // $mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6

        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = MAIL_PORT;
        $mail->SMTPSecure = MAIL_SMTPSecure;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->setFrom('sondas.seedmech@gmail.com', 'Sondas Seedmech');
        foreach($emails as $email)
        {
            if($email<>"")
            {
                $mail->addAddress($email, '');
            }
        }
        $mail->Subject = "Informe de sondas para usuario ".$usuario.", ".$fecha;
        $mail->msgHTML($informe_html, dirname(__FILE__));
        $mail->AltBody = 'This is a plain-text message body';

        //Attach an image file
        $mail->addAttachment('img/seedmech_agrotecnologia.png');

        //send the message, check for errors
        if(!$mail->send())
        {
            echo "Mailer Error: " . $mail->ErrorInfo."\n";
        }else
        {
            //echo "Message sent!\n";
        }
    }
}
