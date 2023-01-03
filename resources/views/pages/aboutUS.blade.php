@extends ('layouts.app')

@section('content')

<div class="bg-light" style="padding:3.5%;">
  <div class="container py-5">
    <div class="row h-100 align-items-center py-5">
      <div class="col-lg-6">
        <h1 class="font-weight-light">Meet WeMeet</h1>
        <p class="font-italic text-muted mb-4">WeMeet is a website that specializes in event organizing. It allows users to create and manage events such as conferences, workshops, and meetings. With WeMeet, users can create an event, manage invitations and communicate with attendees at the click of a button.</p>
      </div>
      <div class="col-lg-6 d-none d-lg-block"><img src="/../img_static/meet.png" alt="People having fun" class="img-fluid"></div>
    </div>
  </div>
</div>

<div class="bg-white py-5">
  <div class="container py-5">
    <div class="row align-items-center mb-5">
      <div class="col-lg-6 order-2 order-lg-1"><i class="fa fa-bar-chart fa-2x mb-3 text-primary"></i>
        <h2 class="font-weight-light">Why WeMeet</h2>
        <p class="font-italic text-muted mb-4">WeMeet is a comprehensive platform for organizing and managing events of all sizes and types. It aims to make the process of planning and executing events easier and more efficient for event organizers, while providing attendees with a seamless and engaging experience.</p><a href="/events" class="btn btn-light px-5 rounded-pill shadow-sm">Get Started</a>
      </div>
      <div class="col-lg-5 px-5 mx-auto order-1 order-lg-2"><img src="https://bootstrapious.com/i/snippets/sn-about/img-1.jpg" alt="person in computer" class="img-fluid mb-4 mb-lg-0"></div>
    </div>
    <div class="row align-items-center">
      <div class="col-lg-5 px-5 mx-auto"><img src="/../img_static/product.jpg" alt="ourProduct" class="img-fluid mb-4 mb-lg-0"></div>
      <div class="col-lg-6"><i class="fa fa-leaf fa-2x mb-3 text-primary"></i>
        <h2 class="font-weight-light">Our Product</h2>
        <p class="font-italic text-muted mb-4">WeMeet offers a range of features to help users plan and execute successful events. These include tools for creating customizable events, managing attendees, scheduling sessions and networking events, and communicating with attendees. It also includes features such as a message board - a forum for each event where attendees can communicate with each other and with the event organizer.</p><a href="/events" class="btn btn-light px-5 rounded-pill shadow-sm">Get Started</a>
      </div>
    </div>
  </div>
</div>

<div class="bg-light py-5">
  <div class="container py-5">
    <div class="row mb-4">
      <div class="col-lg-5">
        <h2 class="display-4 font-weight-light" style="text-align:center">Our Team</h2>
      </div>
    </div>

    <div class="row text-center">

      <!-- Team item-->
      <div class="col-xl-3 col-sm-6 mb-5">
        <div class="bg-white rounded shadow-sm py-5 px-4"><img src="/../img_developers/bb.jpg" alt="" width="100" class="img-fluid rounded-circle mb-3 img-thumbnail shadow-sm">
          <h5 class="mb-0">Bruna Marques</h5><span class="small text-uppercase text-muted">Developer</span>
          <ul class="social mb-0 list-inline mt-3">
            <li class="list-inline-item"><a href="#" class="social-link"><i class="bi bi-linkedin"></i></a></li>
          </ul>
        </div>
      </div>
      <!-- End-->

      <!-- Team item-->
      <div class="col-xl-3 col-sm-6 mb-5">
        <div class="bg-white rounded shadow-sm py-5 px-4"><img src="/../img_developers/fg.jpg" alt="" width="100" class="img-fluid rounded-circle mb-3 img-thumbnail shadow-sm">
          <h5 class="mb-0">Francisca Guimarães</h5><span class="small text-uppercase text-muted">Developer</span>
          <ul class="social mb-0 list-inline mt-3">
            <li class="list-inline-item"><a href="#" class="social-link"><i class="bi bi-linkedin"></i></a></li>
          </ul>
        </div>
      </div>
      <!-- End-->

      <!-- Team item-->
      <div class="col-xl-3 col-sm-6 mb-5">
        <div class="bg-white rounded shadow-sm py-5 px-4"><img src="/../img_developers/mt.png" alt="" width="100" class="img-fluid rounded-circle mb-3 img-thumbnail shadow-sm">
          <h5 class="mb-0">Mariana Teixeira</h5><span class="small text-uppercase text-muted">Developer</span>
          <ul class="social mb-0 list-inline mt-3">
            <li class="list-inline-item"><a href="#" class="social-link"><i class="bi bi-linkedin"></i></a></li>
          </ul>
        </div>
      </div>
      <!-- End-->

      <!-- Team item-->
      <div class="col-xl-3 col-sm-6 mb-5">
        <div class="bg-white rounded shadow-sm py-5 px-4"><img src="/../img_developers/mh.jpg" alt="" width="100" class="img-fluid rounded-circle mb-3 img-thumbnail shadow-sm">
          <h5 class="mb-0">Martim Henriques</h5><span class="small text-uppercase text-muted">Developer</span>
          <ul class="social mb-0 list-inline mt-3">
            <li class="list-inline-item"><a href="#" class="social-link"><i class="bi bi-linkedin"></i></a></li>
          </ul>
        </div>
      </div>
      <!-- End-->

    </div>
  </div>
</div>
@endsection

