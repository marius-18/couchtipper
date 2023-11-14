<div class="container-fluid">
<?php

if (!allow_verwaltung()){
    echo "<div class=\"alert alert-danger\"> Dieser Bereich ist <strong>nur f&uuml;r Administratoren</strong>!<br>
        Frage beim Administrator nach, um Rechte zum &Auml;ndern von Rechten zu bekommen.</div>";
    exit;
}


#########################################################################################
########## U P D A T E
#########################################################################################


### Update des Bezahl Zustandes
pay_update(get_curr_wett());


### Update der Rechte
rechte_update(get_curr_wett());


#########################################################################################
########## B E Z A H L U N G
#########################################################################################

### Überschrift
echo "<div style=\"text-align:left\"><font size =\"+2\"><u>Einsatz</u></font></div><br>";


### Liste der Spieler die mitspielen mit dem Status ob sie bezahlt haben oder nicht.
$out_einsatz = paid(get_curr_wett());

## Starte Form 
echo "<form action=\"?index=9#main\" method=\"post\">";

## Starte Tabelle
echo "<center><table class=\"table table-striped table-hover text-center\">";

## Kopfzeile
if (!is_big_tournament(get_curr_wett())){
    echo "<tr class=\"thead-dark\">
            <th>Username</th>
            <th>Hinrunde</th>
            <th>R&uumlckrunde</th>
            </tr>
      ";
} else {
    echo "<tr class=\"thead-dark\">
            <th>Username</th>
            <th>bezahlt</th>
            </tr>
      ";
}
## Inhalt ausgeben 
foreach ($out_einsatz as $output){
    echo $output;
}

## Tabelle beenden 
echo "</table>";

## Ändern des Zustandes
echo "<input type=\"hidden\" value = \"1\" name =\"paysecure\"><input type = \"submit\" value = \"&Auml;ndern\" ></form></center>";




echo "<hr>";


#########################################################################################
########## R E C H T E    V E R W A L T U N G 
#########################################################################################


#echo rechte1(get_curr_wett());


### Überschrift
echo "<div style=\"text-align:left\"><font size =\"+2\"><u>Rechte-Verwaltung</u></font></div><br>";

### Liste der Spieler die mitspielen im Wettbewerb
$out = rechte(get_curr_wett());

## Starte Form 
echo "<form action=\"?index=9#main\" method=\"post\">";

## Starte Tabelle
echo "<center><div class=\"table-responsive\"><table class=\"table table-striped table-hover text-center\">";

## Kopfzeile 
echo "<tr class=\"thead-dark\">
      <th>Username</th>
      <th>Tipps &auml;ndern</th>
      <th>Erg. &auml;ndern</th>
      <th>Datum &auml;ndern</th>
      <th>User</th></tr>";


## Inhalt ausgeben 
foreach ($out as $output){
    echo $output;

}

## Tabelle beenden
echo "</table></div>";

## Ändern des Zustandes
echo "<input type=\"hidden\" value = \"1\" name =\"secure\"><input type = \"submit\" value = \"&Auml;ndern\" ></form></center>";





?>


</div>
<br>
