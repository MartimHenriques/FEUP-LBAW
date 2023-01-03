
let msg
let div

document.querySelectorAll("#editBtn").forEach((e) => {
    e.addEventListener("click", () => {
        
        msg = e.parentElement.parentElement.parentElement.parentElement
        div = e.parentElement.parentElement.parentElement.previousElementSibling.getElementsByTagName('div')[2]
        div.innerHTML = "<input id='messageInput' type='text' name='content' placeholder='Write a comment'>";
        e.parentElement.innerHTML = "<a id='cancelEdit' onclick='cancelEditComment()'>Cancel</a> <a id='saveEdit' class='button' onclick='editComment()'>Save</a>"
      
    }
    )});

function editComment() {
    let id_event = parseInt(window.location.pathname.split('/')[2]);
    let id = msg.getAttribute('msg-id')
    let newContent = div.children[0].value
    sendAjaxRequest('post', '/edit/comment', {id:id, newContent:newContent, id_event:id_event} , editCommentHandler);
}

function cancelEditComment() {
    let id_event = parseInt(window.location.pathname.split('/')[2]);
    let id = msg.getAttribute('msg-id')
    sendAjaxRequest('post', '/edit/comment/cancel', {id:id, id_event:id_event} , editCommentHandler);
}


function editCommentHandler() {
    msg.parentElement.innerHTML = JSON.parse(this.responseText)

}

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