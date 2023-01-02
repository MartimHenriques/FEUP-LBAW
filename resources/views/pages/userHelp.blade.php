@extends ('layouts.app')

@section('content')

<div class="faq_area section_padding_130" id="faq">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-8 col-lg-6">
                <!-- Section Heading-->
                <div class="section_heading text-center wow fadeInUp" data-wow-delay="0.2s" style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                    <h3><span>Frequently </span> Asked Questions</h3>
                    <p>In this section you will quickly find answers to common questions and resolve any issues you may have.</p>
                    <div class="line"></div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <!-- FAQ Area-->
            <div class="col-12 col-sm-10 col-lg-8">

                <div class="accordion faq-accordian" id="faqAccordion">


                    <div class="card border-0 wow fadeInUp" data-wow-delay="0.2s" style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                        <div class="card-header" id="headingOne">
                            <h6 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">How can I create an in-person Event?<span class="lni-chevron-up"></span></h6>
                        </div>
                        <div class="collapse" id="collapseOne" aria-labelledby="headingOne" data-parent="#faqAccordion">
                            <div class="card-body">
                                <h6>To create an in-person event:</h6>
                                <ol>
                                    <li>In the sidebar on the left choose the <b>create event +</b> button</li>
                                    <li>Add the details of your event</li>
                                    <li>Save your changes by clicking on the <b>create event</b> button at the end of the page</li>
                                </ol>
                                <p>Your event will be published and you will be able to share it with other people so they can become attendees of your event.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 wow fadeInUp" data-wow-delay="0.3s" style="visibility: visible; animation-delay: 0.3s; animation-name: fadeInUp;">
                        <div class="card-header" id="headingTwo">
                            <h6 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">Can I publish pictures of my event?<span class="lni-chevron-up"></span></h6>
                        </div>
                        <div class="collapse" id="collapseTwo" aria-labelledby="headingTwo" data-parent="#faqAccordion">
                            <div class="card-body">
                            <h6>To publish pictures of your event:</h6>
                            <ol>
                                <li>Access your event in the section <b>My events</b></li>
                                <li>Access the <b>Forum</b> section</li>
                                <li>Click in the <b>Upload File</b> button and choose the pictures you wish to upload</li>
                            </ol>
                            <p>Your pictures will be published in your event's forum and all the attendees, including yourself, will be able to see them.</p>
                        </div>
                        </div>
                    </div>

                    <div class="card border-0 wow fadeInUp" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        <div class="card-header" id="headingThree">
                            <h6 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">How can I invite people to my event?<span class="lni-chevron-up"></span></h6>
                        </div>
                        <div class="collapse" id="collapseThree" aria-labelledby="headingThree" data-parent="#faqAccordion">
                            <div class="card-body">
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto quidem facere deserunt sint animi sapiente vitae suscipit.</p>
                                <p>Appland is completely creative, lightweight, clean &amp; super responsive app landing page.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 wow fadeInUp" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        <div class="card-header" id="headingFour">
                            <h6 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">How can I manage who can access and attends my event?<span class="lni-chevron-up"></span></h6>
                        </div>
                        <div class="collapse" id="collapseFour" aria-labelledby="headingFour" data-parent="#faqAccordion">
                            <div class="card-body">
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto quidem facere deserunt sint animi sapiente vitae suscipit.</p>
                                <p>Appland is completely creative, lightweight, clean &amp; super responsive app landing page.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 wow fadeInUp" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        <div class="card-header" id="headingFive">
                            <h6 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">How can I see my invitations and future events I am attending?<span class="lni-chevron-up"></span></h6>
                        </div>
                        <div class="collapse" id="collapseFive" aria-labelledby="headingFive" data-parent="#faqAccordion">
                            <div class="card-body">
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto quidem facere deserunt sint animi sapiente vitae suscipit.</p>
                                <p>Appland is completely creative, lightweight, clean &amp; super responsive app landing page.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 wow fadeInUp" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        <div class="card-header" id="headingSix">
                            <h6 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseSix" aria-expanded="true" aria-controls="collapseSix">How can I see previous events I have attended?<span class="lni-chevron-up"></span></h6>
                        </div>
                        <div class="collapse" id="collapseSix" aria-labelledby="headingSix" data-parent="#faqAccordion">
                            <div class="card-body">
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto quidem facere deserunt sint animi sapiente vitae suscipit.</p>
                                <p>Appland is completely creative, lightweight, clean &amp; super responsive app landing page.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 wow fadeInUp" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        <div class="card-header" id="headingSeven">
                            <h6 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="true" aria-controls="collapseSeven">How can I find events of my interest?<span class="lni-chevron-up"></span></h6>
                        </div>
                        <div class="collapse" id="collapseSeven" aria-labelledby="headingSeven" data-parent="#faqAccordion">
                            <div class="card-body">
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto quidem facere deserunt sint animi sapiente vitae suscipit.</p>
                                <p>Appland is completely creative, lightweight, clean &amp; super responsive app landing page.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 wow fadeInUp" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        <div class="card-header" id="headingEight">
                            <h6 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseEight" aria-expanded="true" aria-controls="collapseEight">Contact form isn't working?<span class="lni-chevron-up"></span></h6>
                        </div>
                        <div class="collapse" id="collapseEight" aria-labelledby="headingEight" data-parent="#faqAccordion">
                            <div class="card-body">
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto quidem facere deserunt sint animi sapiente vitae suscipit.</p>
                                <p>Appland is completely creative, lightweight, clean &amp; super responsive app landing page.</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Support Button-->
                <div class="support-button text-center d-flex align-items-center justify-content-center mt-4 wow fadeInUp" data-wow-delay="0.5s" style="visibility: visible; animation-delay: 0.5s; animation-name: fadeInUp;">
                    <i class="lni-emoji-sad"></i>
                    <p class="mb-0 px-2">Can't find your answers?</p>
                    <a href="/contactUs"> Contact us</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
