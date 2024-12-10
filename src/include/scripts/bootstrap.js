 

$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
});


$(document).ready(function(){
  $('[data-toggle="popover"]').popover();
});

// Funktionen für die Auswahl von Saisons
function seasons_all_on(list){
  list.forEach(seasons_button_on);
}


function seasons_all_off(list){
  list.forEach(seasons_button_off);
}
    
function seasons_button_on(id){
  element = document.getElementById("gesamt_button_"+id);
  text_element = document.getElementById("seasons_speicherung");
  
  element.classList.remove("btn-outline-secondary");
  element.classList.add("btn-success");      
  text_element.classList.add(id);
  text_element.value = text_element.classList;
}

function seasons_button_off(id){
  element = document.getElementById("gesamt_button_"+id);
  text_element = document.getElementById("seasons_speicherung");
  
  element.classList.remove("btn-success");
  element.classList.add("btn-outline-secondary");
  text_element.classList.remove(id);
  text_element.value = text_element.classList;
}

function seasons_toggle_button(id){
  element = document.getElementById("gesamt_button_"+id);
  
  if (element.classList.contains("btn-success")){
    // Disable!
    seasons_button_off(id);
  } else {
    //Enable!
    seasons_button_on(id);
  }
}

