@extends ('layouts.app')

@section('title', 'AdminReports')

@section('content')

<h3>Reports</h3>

<table class="table table-striped" id="reports-table">
<?php
    $count = 0;
    foreach ($notifications as $notification) {
        $count++;
        echo '<div class="card border-0">';
        echo  '<div class="card-header" id="headingTwo">';
        echo   '<a href="/report/'.$notification->id.'" class="my-1 w-100 btn btn-outline-secondary">'.$notification->content.'</a>';
        echo     '</div>';
        echo   '</div>';

    } 
    ?>
</table>
<div class="text-center">
    {!! $reports->links(); !!}
</div>
@endsection


<script>
    window.addEventListener('load', () => {
        var pusher = new Pusher('a021cd3183fc29125e01', {
            cluster: 'eu',
        });
        var channel1 = pusher.subscribe('notifications-reports');
        channel1.bind('event-report-{{Auth::id()}}', function(data) {
            //red_dot(1);
            let body = document.getElementById("reports-table");
            let div_pop = document.createElement("div");
            div_pop.class = "card border-0";
            let button = document.createElement("a");
            button.setAttribute("href", "/events/" + data.event_id + "/forum");
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