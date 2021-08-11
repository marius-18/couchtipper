



function changeGroupTable(id){
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

    //console.log(id);
    
    document.getElementById(id).style.display = 'block';

}
