document.querySelectorAll("#submitReply").forEach((e) => {
    e.addEventListener("click", () => {
        let url = window.location.href;
        let id = url.substring(url.lastIndexOf('/') + 1);
        let id_parent = e.parentElement.parentElement.getAttribute('msg-id');
        let content = e.previousElementSibling.value;
        sendAjaxRequest('post', '/api/event/reply/create', {id:id, id_parent:id_parent, content:content} ,replyMessageHandler(e));
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

function replyMessageHandler(e) {
    let parent = e.parentElement.parentElement;
    let div = document.createElement("div");
    parent.appendChild(div);
    
}