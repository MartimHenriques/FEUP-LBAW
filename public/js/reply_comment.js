let currentBtn
document.querySelectorAll("#submitReply").forEach((e) => {
    e.addEventListener("click", () => {
      currentBtn = e
        let id = parseInt(window.location.pathname.split('/')[2]);

        let id_parent = e.parentElement.previousElementSibling.getAttribute('msg-id');
        let content = e.previousElementSibling.value;
        sendAjaxRequest('post', '/api/event/reply/create', {id:id, id_parent:id_parent, content:content} ,replyMessageHandler);
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

function replyMessageHandler() {
  let reply = currentBtn.parentElement
  let parent = currentBtn.parentElement.parentElement
  let newReply = document.createElement('div')

  newReply.innerHTML = JSON.parse(this.responseText)
  parent.insertBefore(newReply, reply)
    
}