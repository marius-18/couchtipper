
function identify_bot(test)
{
    var result = "default";
    result = $.ajax({
        type: 'POST',
        async: false,   // WICHTIG! 
        url: 'javascript_to.php',
        data: ({
            bot_identifier: test
        })
    }).responseText;

    document.getElementById("bot_infobox").innerHTML = "<div class=\"badge badge-pill badge-success\">Update:</div> Eine Testnachricht ist an das Gerät gesendet worden!"; 
    document.getElementById("bot_infobox").style.display = "";
}
    
function register_bot()
{
    var result = "default"; 
    result = $.ajax({
        type: 'POST',
        async: false,   // WICHTIG! 
        url: 'javascript_to.php',
        data: ({
            bot_register: 1
        })
    }).responseText;
    location.reload();
}

function toggle_bot(bot, modus)
{
    if (modus == "mute"){
        var x = "true";
        document.getElementById("silent" + bot).style.color = "red";
        document.getElementById("silent" + bot).innerHTML = "<i class=\"fas fa-bell-slash\"></i>"; 
        document.getElementById("silent" + bot).setAttribute("onclick", "toggle_bot(" + bot + ", 'unmute')"); 
        document.getElementById("bot_infobox").innerHTML = "<div class=\"badge badge-pill badge-success\">Update:</div> Das Ger&auml;t wurde stumm geschaltet!"; 
    } else if (modus == "unmute"){
        var x = "false";
        document.getElementById("silent" + bot).style.color = "black";        
        document.getElementById("silent" + bot).innerHTML = "<i class=\"fas fa-bell\"></i>";        
        document.getElementById("silent" + bot).setAttribute("onclick", "toggle_bot(" + bot + ", 'mute')"); 
        document.getElementById("bot_infobox").innerHTML = "<div class=\"badge badge-pill badge-success\">Update:</div> Die Stummschaltung wurde aufgehoben!"; 

    } else {
        var x = document.getElementById(bot + modus).checked;
        document.getElementById("bot_infobox").innerHTML = "<div class=\"badge badge-pill badge-success\">Update:</div> Die neuen Einstellungen wurden gespeichert!"; 
    } 
    document.getElementById("bot_infobox").style.display = "";
    
    var result = "default";
    result = $.ajax({
        type: 'POST',
        async: false,   // WICHTIG! 
        url: 'javascript_to.php',
        data: ({
            toggle_bot_mode: 1,
            chat_id: bot,
            mode: modus,
            bool: x
        })
    }).responseText;
}


