<?php
include_once("src/include/code/gewinn.inc.php");
$einsatz = get_wettbewerb_einsatz(get_curr_wett());
$praemie = get_wettbewerb_praemie(get_curr_wett());
?>


<div class = "container">



<div class="alert alert-secondary">
    <i class="fas fa-question-circle"></i> <b>Was kostet mich das Tippspiel und bis wann muss ich bezahlen?</b>
</div>
<div class="alert alert-success">
    Das Tippspiel kostet einmalig <strong><?php echo $einsatz;?>€</strong>. Bitte zahle bis sp&auml;testens zum Ende der Gruppenphase.
</div>



<div class="alert alert-secondary">
    <i class="fas fa-question-circle"></i> <b>Wie kann ich bezahlen?</b>
</div>
<div class="alert alert-success">
    Am liebsten einfach &uuml;ber Paypal: <a href="https://PayPal.Me/couchtipper" class="alert-link" >PayPal.Me/couchtipper</a>. 
    <br>
    (Bitte benutzt den Link und schickt das Geld <strong>nicht</strong> an meinen privaten Account). 
    <br>
    Zur Not geht es auch direkt in Bar. <br>
    (Bei Paypal &Uuml;berweisungen sollte nat&uuml;rlich erkennbar sein, f&uuml;r welchen Nutzer bezahlt wurde...) <i class="far fa-smile-wink"></i>
</div>


<div class="alert alert-secondary">
    <i class="fas fa-question-circle"></i> <b>Was gibt es zu gewinnen?</b>
</div>
<div class="alert alert-success">
    Der komplette Einsatz aller Spieler wird als Gewinn an die besten Spieler ausgezahlt. 
    Wie sich der Gewinn für die entsprechenden Plätze ergibt, seht ihr auf der Seite
    <a href="?index=10#main" class="alert-link">"Gewinnverteilung"</a> im Menü.
</div>



<div class="alert alert-secondary">
    <i class="fas fa-question-circle"></i> <b>Was passiert mit dem Gewinn bei Punktgleichheit?</b>
</div>
<div class="alert alert-success">
    Wenn mehrere Spieler am Ende die gleiche Anzahl an Punkten haben, werden die
    Gewinne der einzelnen Plätze addiert und durch die Anzahl an den punktgleichen Spielern geteilt.
</div>

<div class="alert alert-secondary">
    <i class="fas fa-question-circle"></i> <b>Wie setzen sich die Punkte zusammen?</b>
</div>
<div class="alert alert-success">

    <table align = "center">
        <tr><td>3 Punkte:</td><td> richtiges Ergebnis</td></tr>
        <tr><td>2 Punkte:</td><td> richtige Differenz</td></tr>
        <tr><td>1 Punkt:</td><td> richtige Tendenz</td></tr>
        <tr><td>0 Punkte:</td><td> komplett falsch</td></tr>
    </table>
</div>



<div class="alert alert-secondary">
    <i class="fas fa-question-circle"></i> <b>Bis wann müssen die Tipps eingegeben sein?</b>
</div>
<div class="alert alert-success">
    Die Tipps müssen <strong>vor</strong> dem Anpfiff des jeweiligen Spiels eingetragen sein. 
    Eine spätere Eintragung ist nicht möglich.
</div>






<div class="alert alert-secondary">
    <i class="fas fa-question-circle"></i> <b>Kann man auch die Tipps der anderen Spieler sehen? </b>
</div>
<div class="alert alert-success">
    Ja, aber das ist erst möglich, wenn das Spiel schon begonnen hat, damit niemand "Abschreiben" kann.
    Um die Tipps zu sehen, muss man im Fenster "Spieltage" oder "Tipps" auf ein Spiel tippen. Die Tipps erscheinen dann.
</div>




<div class="alert alert-secondary">
    <i class="fas fa-question-circle"></i> <b>Was passiert, wenn ein Spiel im Elfmeterschießen oder der Verlängerung entschieden wird?</b>
</div>
<div class="alert alert-success">
    Es werden immer <strong>alle</strong> Tore gezählt die geschossen werden. Auch die in einer Verlängerung oder einem Elfmeterschießen.<br>
    <br>
    Bei KO-Spielen macht es also wenig Sinn auf Unentschieden zu tippen.....
</div>



<?php
    ## Ab hier nur noch für eingeloggte User!
    if (!is_logged()){
        echo "</div>";
        return 0;
    }
?>

<div class="alert alert-secondary">
    <i class="fas fa-question-circle"></i> <b>Gibt es eine couchtipper Chat-Gruppe oder sowas?</b>
</div>
<div class="alert alert-success">
    Ja, es gibt eine WhatsApp Gruppe zum Tippspiel. Wer will kann über folgenden Link beitreten: 
    <a href="https://chat.whatsapp.com/BAzcQVzSmso5q79TO0ywUN" class="alert-link"> WhatsApp <i class="fa-brands fa-whatsapp"></i></a>  
    <br>
    <strong>Achtung!</strong> Wenn du der Gruppe beitrittst, sehen natürlich alle Mitglieder der Gruppe deine Kontaktdaten. 
</div>


</div>
