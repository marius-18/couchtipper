

function rangliste_ausblenden(wert) {
    // Blendet die verschiedenen Ranglisten ein und aus
    for (i=1; i<=3; i++){
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
    document.getElementById("groupA").style.display = 'none';
    document.getElementById("groupB").style.display = 'none';
    document.getElementById("groupC").style.display = 'none';
    document.getElementById("groupD").style.display = 'none';
    document.getElementById("groupE").style.display = 'none';
    document.getElementById("groupF").style.display = 'none';
    
    document.getElementById("LgroupA").classList.remove('active');
    document.getElementById("LgroupB").classList.remove('active');
    document.getElementById("LgroupC").classList.remove('active');
    document.getElementById("LgroupD").classList.remove('active');
    document.getElementById("LgroupE").classList.remove('active');
    document.getElementById("LgroupF").classList.remove('active');


    var label = "L" + id;
    document.getElementById(label).classList.add('active');
    
    document.getElementById(id).style.display = 'block';

}
