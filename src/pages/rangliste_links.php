<?php

##############################################################
######    Hinrunde / Rückrunde Tabelle!!
##############################################################

require_once('src/include/code/rangliste.inc.php');


list($id,$id_part) = get_curr_wett();


if ((get_wettbewerb_code(get_curr_wett()) == "BuLi") && ($id_part == 1)) {

    print_rangliste(18, akt_spieltag(), 1, true);
} else {
    print_rangliste(1, akt_spieltag(), 1, true);
}


?>





