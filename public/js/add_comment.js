document.querySelectorAll("#submitComment").forEach((e) => {
    e.addEventListener("click", () => {
        let url = window.location.href;
        let id = url.substring(url.lastIndexOf('/') + 1);
        let content = e.previousElementSibling.value;
        sendAjaxRequest('post', '/api/event/comment/create', {id:id, content:content} , addMessageHandler(e));
    } 
    )});

function encodeForAjax(data) {
    if (data == null) return null;
    return Object.keys(data).map(function(k){
      return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
    }).join('&');
}
  
function sendAjaxRequest(method, url, data, handler) {
  let request = new XMLHttpRequest();

  request.open(method, url, true);
  request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
  request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  request.addEventListener('load', handler);
  request.send(encodeForAjax(data));
}

function addMessageHandler(e) {
  let parentMessage = e.parentElement.parentElement;
  let newMessage = document.createElement('div')
  newMessage.innerHTML = JSON.parse(this.responseText) 
  parentMessage.appendChild(newMessage);
    
}