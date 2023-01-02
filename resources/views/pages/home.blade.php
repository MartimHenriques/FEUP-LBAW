@extends ('layouts.app')

@section('content2')
<div id="landing">

  <div>
    <h3>Search for new experiences and meet new people</h3>
    <a class="button" href="{{ url('events') }}" style="background-color:#6b8febce; max-width: 15em; display: block;">Get Started<i style="margin-left: 1em;" class="bi bi-arrow-right"></i></a>
    
  </div>
  
  <img src="../img_events/people.png" alt="happy people" style="width: 40%; min-width: 18em; display:block;">
</div>


@endsection