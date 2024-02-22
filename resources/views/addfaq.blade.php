@extends('layouts.master')
@section('title')
Add New FAQ
@endsection

@section('content')

<script>
    $(document).ready(function(){

        const accessToken = "{{ Auth::user()->createToken(env('TOKEN_NAME'))->accessToken }}";

        var add_new_faq_api="{{ url('api/faq_insert')}}";

        // $.validator.setDefaults({
        //     submitHandler: function() {
        //         console.log("submitted!");
        //     }
        // });

        
        $('#btn-add-faq-by-admin').click(function(e){
            e.preventDefault();
            $('#add_faq_by_admin').validate();
            if ($('#add_faq_by_admin').valid())
            {
                $('#add_faq_by_admin').submit();
            } else {
                $('#btn-add-user-by-admin').prop('disabled',false);
                $( "div.alert_failure" ).html('Please fill the required fields');
                $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
            }
           
          
        });

       

        $('#add_faq_by_admin').submit(function(e){
            e.preventDefault();
            $('#btn-add-faq-by-admin').prop('disabled',true);
            var formData = new FormData(this);
            $.ajax({
                url:add_new_faq_api,
                type: "POST",
                headers: {
                    "Authorization": "Bearer "+accessToken,
                    "Accept": "application/json"
                },
                data:formData,
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success:function(response)
                {
                    var data_response=response.response;
                    var response_msg=data_response.msg;
                    var response_success=data_response.success;
                    if (response_success==true)
                    {
                        $( "div.alert_success" ).html(response_msg);
                        $( "div.alert_success" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                        location.href = "{{ url('/faqs')}}";
                    } else {
                        $( "div.alert_failure" ).html(response_msg);
                        $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                    }
                    // alert(response.response.msg);
                    // window.location.href = "{{url('/faqs')}}";
                    
                    // alert(response.response.msg);
                    // window.location.href = "/faqs";
            

                
                },
                error: function(xhr, status, error) {
                    $('#btn-add-faq-by-admin').prop('disabled',false);
                    $( "div.alert_failure" ).html(xhr.responseJSON.response.msg);
                    // $( "div.alert_failure" ).html('Something Went Wrong');
                    $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                    // if (xhr.responseJSON && xhr.responseJSON.response && xhr.responseJSON.response.msg) {
                    //     $( "div.alert_failure" ).html(xhr.responseJSON.response.msg);
                    //     $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                    //     // alert("Error: " + xhr.responseJSON.response.msg);
                    // } else {
                    //     $( "div.alert_failure" ).html(error);
                    //     $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                    //     // alert("AJAX Error: " + error);
                    // }
                }

            })
        });
        // const form = document.getElementById("add_faq_by_admin");
        // form.addEventListener("submit", addFAQbyAdmin);
        // function addFAQbyAdmin(event) {
        //     // log.textContent = `Form Submitted! Timestamp: ${event.timeStamp}`;
        //     event.preventDefault();
        //     const formData = new FormData(form);
        //     const formObject = {};

        //     formData.forEach((value, key)=>{
        //         // console.log(value);
        //         formObject[key]=value;
        //     });
            
           
        // }

    });
</script>

<div>
    <div class="parallel-heading-div">
        <p class="dashboard-main-heading">
            FAQs > <span class="breadcrumb-small-text">Add New FAQ</span>
        </p>
    </div>
    <div class="add-form-div">
        <form id="add_faq_by_admin" method="post" autocomplete="off">
            @csrf
            <div class="row">
                <div class="form-group col">
                    <label for="question">Question</label>
                    <input type="text" class="form-control" id="question" name="question" placeholder="Question" required/>
                    
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col">
                    <label for="answer">Answer</label>
                    <input type="text" class="form-control" id="answer" name="answer" placeholder="Answer" required/>
                    
                </div>
            </div>
            <hr>
            <button type="button" id="btn-add-faq-by-admin" class="btn btn-success">
                Add FAQ
            </button>
            <div class="alert-box alert_success"></div>
            <div class="alert-box alert_failure"></div>
            <div class="alert-box alert_warning"></div>
        </form>
    </div>
</div>
@endsection
