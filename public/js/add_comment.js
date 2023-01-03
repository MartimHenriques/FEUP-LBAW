
document.querySelectorAll("#submitComment").forEach((e) => {
    e.addEventListener("click", () => {
      console.log(e)
        let id = parseInt(window.location.pathname.split('/')[2]);
        let content = e.previousElementSibling.value;
        sendAjaxRequest('post', '/api/event/comment/create', {id:id, content:content} , addMessageHandler);
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

function addMessageHandler() {
  
  let msg = document.querySelectorAll(".message")[0]
  let newMessage = document.createElement('div')
  newMessage.className = "message"
  newMessage.innerHTML = JSON.parse(this.responseText)
  if(msg){
    msg.parentNode.insertBefore(newMessage,  msg)
  }
  else{
    let post = document.getElementById('postMessage')
    post.parentElement.appendChild(newMessage)
  }
  
  

}