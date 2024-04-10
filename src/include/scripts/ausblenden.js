

function rangliste_ausblenden(wert) {
    // Blendet die verschiedenen Ranglisten ein und aus
    for (i=1; i<=4; i++){
        if (i == wert){
            document.getElementById("rang" + wert).style.display = "";
            document.getElementById("rangbutton" + wert).className = "btn btn-info focus";
        } else{
            document.getElementById("rang" + i).style.display = "none";
            document.getElementById("rangbutton" + i).className = "btn btn-info";
        }
    }
}

function tagessieger_ausblenden(wert) {
    // Blendet die verschiedenen Ranglisten ein und aus
    for (i=1; i<=3; i++){
        if (i == wert){
            document.getElementById("tagessieger" + wert).style.display = "";
            document.getElementById("tagessieger_button" + wert).className = "btn btn-info focus";
        } else{
            document.getElementById("tagessieger" + i).style.display = "none";
            document.getElementById("tagessieger_button" + i).className = "btn btn-info";
        }
    }
}

function gewinn_ausblenden(wert) {
    // Blendet die verschiedenen Ranglisten ein und aus
    for (i=1; i<=3; i++){
        if (i == wert){
            document.getElementById("gewinn" + wert).style.display = "";
            document.getElementById("gewinn_button" + wert).className = "btn btn-info focus";
        } else{
            document.getElementById("gewinn" + i).style.display = "none";
            document.getElementById("gewinn_button" + i).className = "btn btn-info";
        }
    }
}

function changeGroupTable(id){
    // Für Gruppen-Tabelle in EM/WM Modus
    // Blendet Tabellen ein und aus
  
    var grps = document.getElementsByClassName("big_tournament_group");
    for (let i = 0; i < grps.length; i++) {
      grps[i].style.display = 'none';
    }

    
    var grps_menu = document.getElementsByClassName(" big_tournament_group_menu");
    for (let i = 0; i < grps.length; i++) {
      grps_menu[i].classList.remove('active');
    }

    var label = "L" + id;
    document.getElementById(label).classList.add('active');
    
    document.getElementById(id).style.display = 'block';

}

function changeGroupTableNext(max){
    var id = document.getElementById("curr_group").value;
    var min = 65;
    
    var new_id = id.charCodeAt(0) + 1;
    
    if (new_id > max){
        new_id = min;
    }
    
    new_id = String.fromCharCode(new_id);
    
    document.getElementById("curr_group").value = new_id;
    changeGroupTable("group" + new_id);
}

function changeGroupTablePrev(max){
    var id = document.getElementById("curr_group").value;
    var min = 65;
    
    var new_id = id.charCodeAt(0) - 1;
    
    if (new_id < min){
        new_id = max;
    }
    
    new_id = String.fromCharCode(new_id);
    
    document.getElementById("curr_group").value = new_id;
    changeGroupTable("group" + new_id);
}

    
function game_details_ausblenden(wert, ende, prefix) {
    var id = prefix + wert;
    if ( document.getElementById(id).style.display==""){
        document.getElementById(id).style.display = "none";
    } else {
        for (var i=100;i<= ende; i++){
            var out = prefix + i;
            document.getElementById(out).style.display = "none";
        }
        document.getElementById(id).style.display = "";
    }
}
