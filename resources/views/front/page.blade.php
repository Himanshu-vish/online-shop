@extends('front.layouts.app')
@section('content')
<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
        <div class="light-font">
            <ol class="breadcrumb primary-color mb-0">
                <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                <li class="breadcrumb-item">{{$pages->name}}</li>
            </ol>
        </div>
    </div>
</section>
<section class=" section-10">
    <div class="container">
        @if ($pages->slug == 'contact-us')
        <section class=" section-10">
            <div class="container">
                <div class="section-title mt-5 ">
                    <h2>{{$pages->name}}</h2>
                </div>        
            </div>
            <div class="col-md-12">
                @include('front.accounts.common.message')
            </div>
        </section>
    
        <section>
            <div class="container">          
                <div class="row">
                    <div class="col-md-6 mt-3 pe-lg-5">
                        {!! $pages->content !!}



                    </div>
    
                    <div class="col-md-6">
                        <form class="shake" role="form" method="post" id="contactForm" name="contactform">
                            <div class="mb-3">
                                <label class="mb-2" for="name">Name</label>
                                <input class="form-control" id="name" type="text" name="name"  data-error="Please enter your name">
                                <p></p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="mb-2" for="email">Email</label>
                                <input class="form-control" id="email" type="email" name="email" data-error="Please enter your Email">
                                <p></p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="mb-2">Subject</label>
                                <input class="form-control" id="subject" type="text" name="subject" data-error="Please enter your message subject">
                                <p></p>
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="mb-2">Message</label>
                                <textarea class="form-control" rows="3" id="message" name="message" data-error="Write your message"></textarea>
                                <p></p>
                            </div>
                          
                            <div class="form-submit">
                                <button class="btn btn-dark" type="submit" id="form-submit"><i class="material-icons mdi mdi-message-outline"></i> Send Message</button>
                                <div id="msgSubmit" class="h3 text-center hidden"></div>
                                <div class="clearfix"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        @else
        <h1 class="my-3">{{$pages->name}}</h1>

        {!!$pages->content!!}    
        @endif
        
       

    </div>
</section>
@endsection
@section('customJs')
<script>
    $("#contactForm").submit(function(event){
    event.preventDefault();
     $.ajax({
         url:'{{route("front.sendContactEmail")}}',
         type:'post',
         data:$(this).serializeArray(),
         dataType:'json',
         success:function(response){
            if(response.status == true){

             window.location.href = '{{route("front.page",$pages->slug)}}';   

             $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
             $("#subject").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
             $("#email").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
             
             

            }
         },
         error:function(jqXHR, exception){
             if (jqXHR.status === 422) {
             // Laravel validation errors
             var errors = jqXHR.responseJSON.errors;

                 if (errors.name) {
                     $("#name").addClass('is-invalid')
                     .siblings('p').addClass('invalid-feedback').html(errors.name[0]);  
                 }
                 if (errors.subject) {
                     $("#subject").addClass('is-invalid')
                     .siblings('p').addClass('invalid-feedback').html(errors.subject[0]);  
                 }
                 if (errors.email) {
                     $("#email").addClass('is-invalid')
                     .siblings('p').addClass('invalid-feedback').html(errors.email[0]);  
                 }
            }
         }
      });
 }); 

</script>
@endsection