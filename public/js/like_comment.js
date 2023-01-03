document.querySelectorAll("#like").forEach((e) => {
  e.addEventListener("click", () => {
    let id = e.getAttribute('data-id');
    if(e.classList == 'bi bi-hand-thumbs-up'){
      sendAjaxRequest('post', '/api/comment/vote/create', {id:id} , likeMessageHandler(e));
    }else if(e.classList == 'bi bi-hand-thumbs-up-fill'){
      sendAjaxRequest('post', '/api/comment/vote/delete', {id:id} , removeLikeMessageHandler(e));
    }
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

  
function likeMessageHandler(e) {
  let prev = e.previousElementSibling;
  prev.innerHTML = parseInt(prev.innerHTML) + 1;
  e.classList = "bi bi-hand-thumbs-up-fill"
}
  
function removeLikeMessageHandler(e) {
  let prev = e.previousElementSibling;
  prev.innerHTML = parseInt(prev.innerHTML) - 1;
  e.classList = "bi bi-hand-thumbs-up";
}