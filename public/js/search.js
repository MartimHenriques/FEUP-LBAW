const eventsDiv = document.getElementById('eventsSearch')

const eventsTitleSearch = document.getElementById('eventsTitleSearch')

let searchBar = document.getElementById("searchbar")
searchBar.addEventListener('keyup', searchRequest);

/* function */
function searchRequest(event){
    eventsDiv.innerHTML = ""
    let search = searchBar.value.trim()
    sendAjaxRequest('post', '/api/search', {search:search} , searchHandler);
}

function searchHandler(){
    let result = JSON.parse(this.responseText);
    console.log(result)
    let events = result
    if(searchBar.value == ""){
        eventsTitleSearch.style.display = "none";
    }else{
        eventsTitleSearch.style.display = "block";
    }
    if((events.length)>0){
        addEvents(events)
    }

}
function addevents(events){

    for (var i = 0; i < events.length; i++) {
        eventsDiv.append(createEvent(events[i]));
    }

}

function createEvent(event){
    let eventSearch = document.createElement('div');
    eventSearch.classList.add('eventSearch');
    eventSearch.innerHTML = 
        '<a href="events/${event.id}">${event.title}</a>';
    

    return eventSearch;
}




