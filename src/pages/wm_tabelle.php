<?php
require_once('src/include/code/tabelle.inc.php');
require_once('src/include/code/get_games.inc.php');
?>



<div class="container-fluid text-center" >
<ul class="pagination justify-content-center">
  <li id="LgroupA" class="page-item active"><a class="page-link" onclick="changeGroupTable('groupA')">A</a></li>
  <li id="LgroupB" class="page-item"><a class="page-link" onclick="changeGroupTable('groupB')">B</a></li>
  <li id="LgroupC" class="page-item"><a class="page-link" onclick="changeGroupTable('groupC')">C</a></li>
  <li id="LgroupD" class="page-item"><a class="page-link" onclick="changeGroupTable('groupD')">D</a></li>
  <li id="LgroupE" class="page-item"><a class="page-link" onclick="changeGroupTable('groupE')">E</a></li>
  <li id="LgroupF" class="page-item"><a class="page-link" onclick="changeGroupTable('groupF')">F</a></li>
</ul>

    <div style='display: block;' id="groupA">
      <?php print_wm_tabelle(wm_tabelle("A")); print_gruppe("A");  ?>
    </div>
    <div style='display: none' id="groupB">
      <?php print_wm_tabelle(wm_tabelle("B")); print_gruppe("B");?>
    </div>
    <div style='display: none' id="groupC">
      <?php print_wm_tabelle(wm_tabelle("C")); print_gruppe("C"); ?>
    </div>
    <div style='display: none' id="groupD">
      <?php  print_wm_tabelle(wm_tabelle("D")); print_gruppe("D"); ?>
    </div>
    <div style='display: none' id="groupE">
      <?php print_wm_tabelle(wm_tabelle("E")); print_gruppe("E"); ?>
    </div>
    <div style='display: none' id="groupF">
      <?php print_wm_tabelle(wm_tabelle("F")); print_gruppe("F"); ?>
    </div>
</div>

<!--<script src='src/swipe.js'></script>
<script>
var slider3 = new Swipe(document.getElementById('slider3'));
</script>
-->

