<HTML>	
<HEAD><META http-EQUIV=refresh CONTENT=10;url=mysql5.php></HEAD>
<BODY>
<!--<CENTER>-->
Deux moment S.V.P.............10 sec<P>
<?php
//mail('stephane.mercier@clevislauzon.qc.ca','La bouette','C est de la bouette');    
     $headers ='From: "Mers"<stephane.mercier@clevislauzon.qc.ca>'."\n";
     $headers .='Reply-To: stephane.mercier@clevislauzon.qc.ca'."\n";
     $headers .='Content-Type: text/plain; charset="iso-8859-1"'."\n";
     $headers .='Content-Transfer-Encoding: 8bit';

     if(mail('clonecharle1@gmail.com', 'La bouette', "Ben, c'est de la mouditte bouette", $headers))
     {
          echo 'Le message a bien été envoyé';
     }
     else
     {
          echo 'Le message n\'a pu être envoyé';
     } 
?>
<!--<CENTER>-->
</BODY>
</HTML>