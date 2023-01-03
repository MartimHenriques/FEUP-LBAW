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
                        <div class="collapse" id="collapseOne" data-parent="#faqAccordion">
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
                            <h6 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">Can I publish messages in my event?<span class="lni-chevron-up"></span></h6>
                        </div>
                        <div class="collapse" id="collapseTwo" data-parent="#faqAccordion">
                            <div class="card-body">
                            <h6>To publish messages in your event:</h6>
                            <ol>
                                <li>Access your event in the section <b>My events</b></li>
                                <li>Access the <b>Forum</b> section</li>
                                <li>Click in the <b>Post</b> button after writing text you wish to publish</li>
                            </ol>
                            <p>Your messages will be published in your event's forum and all the attendees, including yourself, will be able to see them.</p>
                        </div>
                        </div>
                    </div>

                    <div class="card border-0 wow fadeInUp" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        <div class="card-header" id="headingThree">
                            <h6 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">How can I invite people to other events?<span class="lni-chevron-up"></span></h6>
                        </div>
                        <div class="collapse" id="collapseThree" data-parent="#faqAccordion">
                            <div class="card-body">
                                <p>To share an event with your friends:</p>
                                <ol>
                                <li>Locate the event that you want to share. If the event is public, you will be able to find it on the website. If the event is private, you will need to be invited by the event organizer.</li>
                                <li>Once you have located the event, look for the share button.</li>
                                <li>By clicking in this button you will be copying the page link to your clipboard</li>
                                <li>Share the link with anyone you like</li>
                            </ol>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 wow fadeInUp" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        <div class="card-header" id="headingFour">
                            <h6 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">How can I manage who can access and attend my event?<span class="lni-chevron-up"></span></h6>
                        </div>
                        <div class="collapse" id="collapseFour" data-parent="#faqAccordion">
                            <div class="card-body">
                                <p>By being an event organizer you have the power when it comes to your events:</p>
                                <ol>
                                <li>You can remove any attendee you like using the <b>Remove</b> button next to their name in the attendee section</li>
                                <li>You can change the visibility of your event so only people invited by you can access your event.</li>
                                </ol>
                                <p>Note: By making an event private, this event will no longer appear on home page for everyone.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 wow fadeInUp" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        <div class="card-header" id="headingFive">
                            <h6 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">How can I see my invitations?<span class="lni-chevron-up"></span></h6>
                        </div>
                        <div class="collapse" id="collapseFive" data-parent="#faqAccordion">
                            <div class="card-body">
                             <p>To see your invitations:</p>
                                <ol>
                                <li>Access the <b>Notification</b> section on the left sidebar.</li>
                                </ol>
                                <p>Here you will be able to see all the invitations you have received, including invites.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 wow fadeInUp" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        <div class="card-header" id="headingSix">
                            <h6 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseSix" aria-expanded="true" aria-controls="collapseSix">How can I see previous and future events?<span class="lni-chevron-up"></span></h6>
                        </div>
                        <div class="collapse" id="collapseSix" data-parent="#faqAccordion">
                            <div class="card-body">
                                <p>To see your previous and future events:</p>
                                <ol>
                                <li>Access the <b>My calendar</b> section on the left sidebar.</li>
                                <li>In the first section, you will be able to see all the events you have attended.</li>
                                <li>In the second section, you will be able to see all the events you will attend.</li>
                                </ol>
                                <p>Note: If you are an event organizer, you will be able to see all the events you have created in the <b>My events</b> section.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 wow fadeInUp" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        <div class="card-header" id="headingSeven">
                            <h6 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="true" aria-controls="collapseSeven">How can I find events of my interest?<span class="lni-chevron-up"></span></h6>
                        </div>
                        <div class="collapse" id="collapseSeven" data-parent="#faqAccordion">
                            <div class="card-body">
                                <p>To find events of your interest:</p>
                                <ol>
                                <li>Access the <b>Home Page</b> section on the left sidebar.</li>
                                <li>Here you will be able to see all the events that are public and that are happening in your area.</li>
                                <li>Use the search bar to find events of your interest.</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 wow fadeInUp" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        <div class="card-header" id="headingEight">
                            <h6 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseEight" aria-expanded="true" aria-controls="collapseEight">Contact form is not working?<span class="lni-chevron-up"></span></h6>
                        </div>
                        <div class="collapse" id="collapseEight" data-parent="#faqAccordion">
                            <div class="card-body">
                                <p>Contact forms is not working?</p>
                                <p>Make sure you have filled all the fields correctly.</p>
                                <p>If you are still having issues, please contact us at R. Dr. Roberto Frias, 4200-465 Porto.</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Support Button-->
                <div class="support-button text-center d-flex align-items-center justify-content-center mt-4 wow fadeInUp" data-wow-delay="0.5s" style="visibility: visible; animation-delay: 0.5s; animation-name: fadeInUp;">
                    <i class="lni-emoji-sad"></i>
                    <p class="mb-0 px-2">Can't find your answers?</p>
                    <a href="/contact_us"> Contact us</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
