<?php
require_once('src/include/code/tabelle.inc.php');
require_once('src/include/code/get_games.inc.php');
require_once('src/include/code/print_games.php');
require_once('src/include/lib/precomputation.inc.php');
?>



<div class="container-fluid text-center" >

    <?php
      select_gruppe();
    ?>
    
    <div style='display: block;' id="groupA" class="big_tournament_group">
      <?php print_wm_tabelle(wm_tabelle("A")); print_gruppe("A");  ?>
    </div>
    <div style='display: none' id="groupB" class="big_tournament_group">
      <?php print_wm_tabelle(wm_tabelle("B")); print_gruppe("B");?>
    </div>
    <div style='display: none' id="groupC" class="big_tournament_group">
      <?php print_wm_tabelle(wm_tabelle("C")); print_gruppe("C"); ?>
    </div>
    <div style='display: none' id="groupD" class="big_tournament_group">
      <?php  print_wm_tabelle(wm_tabelle("D")); print_gruppe("D"); ?>
    </div>
    <div style='display: none' id="groupE" class="big_tournament_group">
      <?php print_wm_tabelle(wm_tabelle("E")); print_gruppe("E"); ?>
    </div>
    <div style='display: none' id="groupF" class="big_tournament_group">
      <?php print_wm_tabelle(wm_tabelle("F")); print_gruppe("F"); ?>
    </div>
    
    <?php
    if (get_wettbewerb_code(get_curr_wett())  == "WM"){
      echo "<div style='display: none' id=\"groupG\" class=\"big_tournament_group\">";
        print_wm_tabelle(wm_tabelle("G")); 
        print_gruppe("G");
      echo "</div>";
      echo "<div style='display: none' id=\"groupH\" class=\"big_tournament_group\">";
        print_wm_tabelle(wm_tabelle("H")); 
        print_gruppe("H");
      echo "</div>";
    }
    ?>
</div>

<!--<script src='src/swipe.js'></script>
<script>
var slider3 = new Swipe(document.getElementById('slider3'));
</script>
-->

