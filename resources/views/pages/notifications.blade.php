@extends('layouts.app')

@section('title', 'Invite')

@section('content')

<div class="formbg-outer">
  <div class="formbg">
    <div class="formbg-inner" id="not-list" style="padding: 48px">
      <span style="padding-bottom: 15px">Notifications</span>
        <?php
        $count = 0;
        foreach ($notifications as $notification) {
            $count++;
            if($notification->type == "Invite") {
                echo '<div class="card border-0">';
                echo  '<div class="card-header" id="headingTwo">';
                echo   '<a href="/invites/'.$notification->id.'" class="my-1 w-100 btn btn-outline-secondary">'.$notification->content.'</a>';
                echo     '</div>';
                echo   '</div>';
            }
            else {
                echo '<div class="card border-0">';
                echo  '<div class="card-header" id="headingTwo">';
                echo   '<h6>'.$notification->content.'</h6>';
                echo     '</div>';
                echo   '</div>';
            }
        } 
        ?>
        <form id="clear-full" action="/notifications/{{\Illuminate\Support\Facades\Auth::id()}}/clear" method="POST" class="text-center">
            @csrf
            <button type="submit" class="my-1 w-50 btn btn-outline-danger">Clear All</button>
        </form>
        </div>
        </div>
        <div id="notification-number-compact" class="notification-number" style="left: 78%"></div>
        </div>
</div>
@endsection

<script>
    window.addEventListener('load', () => {
        // Enable pusher logging - don't include this in production
        // Pusher.logToConsole = false;
        var pusher = new Pusher('a021cd3183fc29125e01', {
            cluster: 'eu',
        });
        var channel3 = pusher.subscribe('notifications-invites');
        channel3.bind('event-invite-{{Auth::id()}}', function(data) {
            //red_dot(1);
            let body = document.getElementById("not-list");
            let div_pop = document.createElement("div");
            div_pop.class = "card border-0";
            let button = document.createElement("a");
            button.setAttribute("href", "/invites/" + data.notification_id);
            button.classList.add("btn");
            button.classList.add("btn-outline-secondary");
            button.classList.add("my-1");
            button.classList.add("w-100");
            button.innerText = data.message;
            button.style.color = "#198754";
            div_pop.appendChild(button);
            body.prepend(div_pop);
        });
    });
    function red_dot(count) {
        if (count > 0) {
            let assign1 = document.getElementById('notification-number-full');
            assign1.style.visibility = 'visible';
            let assign2 = document.getElementById('notification-number-compact');
            assign2.style.visibility = 'visible';
            let clear_full = document.getElementById("clear-full");
            clear_full.style.display = 'block';
            let clear_compact = document.getElementById("clear-compact");
            clear_compact.style.display = 'block';
        }
        else {
            let assign1 = document.getElementById('notification-number-full');
            assign1.style.visibility = 'hidden';
            let assign2 = document.getElementById('notification-number-compact');
            assign2.style.visibility = 'hidden';
            let empty_full = document.getElementById("empty-full");
            empty_full.style.display = 'block';
            let clear_full = document.getElementById("clear-full");
            clear_full.style.display = 'none';
            let empty_compact = document.getElementById("empty-compact");
            empty_compact.style.display = 'block';
            let clear_compact = document.getElementById("clear-compact");
            clear_compact.style.display = 'none';
        }
    }
</script>

<?php
//  if (Auth::check()) {
//     echo '<script type="text/javascript"> red_dot('.$count.') </script>';
//  }
?>


