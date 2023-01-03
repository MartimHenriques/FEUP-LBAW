function copyLinkFeed(id){
    var btn = document.getElementById(id);
    console.log("btn: ", btn);
    btn.innerHTML = 'link copied';
    btn.style.backgroundColor = "green"
    
    navigator.clipboard.writeText(window.location.href + "/" + id + "/info");

    setTimeout(function(){
        btn = document.getElementById(id);
        btn.innerHTML = 'Share';
        btn.style.backgroundColor = "#9bb6fcf6";
    }, 1000);
}

    