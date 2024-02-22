@extends('layouts.master')
@section('title')
Add New FAQ
@endsection

@section('content')

<script>
    $(document).ready(function(){

        const accessToken = "{{ Auth::user()->createToken(env('TOKEN_NAME'))->accessToken }}";

        var update_faq_api="{{ url('api/update_faq')}}";

        const sel_faq_id="{{ $sel_faq_id}}";

        $.validator.setDefaults({
            submitHandler: function() {
                console.log("submitted!");
            }
        });

       

        $('#btn-update-faq-by-admin').click(function(e){
            e.preventDefault();
            $('#update_faq_by_admin').validate();
            if ($('#update_faq_by_admin').valid())
            {
                $('#update_faq_by_admin').submit();
            } else {
                $('#btn-add-user-by-admin').prop('disabled',false);
                $( "div.alert_failure" ).html('Please fill the required fields');
                $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
            }
           
        });
       
            
           
        $('#update_faq_by_admin').submit(function(e){
            e.preventDefault();
            $('#btn-update-faq-by-admin').prop('disabled',true);
            var formData = new FormData(this);
            $.ajax({
                url:update_faq_api,
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
                        
                    } else {
                        $( "div.alert_failure" ).html(response_msg);
                        $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                    }
                    // window.location.href="{{ url('/faqs')}}";
                    // alert(response.response.msg);
                    // // window.location.href = "/vendors";
                    location.href="{{ url('/faqs')}}";



                },
                error: function(xhr, status, error) {
                    $('#btn-update-faq-by-admin').prop('disabled',false);
                    $( "div.alert_failure" ).html('Something Went Wrong');
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
        // document.getElementById("update-faq-by-admin").addEventListener("click", updateFAQbyAdmin);
        // function updateFAQbyAdmin()
        // {
        //     // console.log("saad");
        //     // alert("saad");
        //     var sel_faq_question=document.getElementById("faq-question-text").value;
        //     var sel_faq_answer = document.getElementById("faq-answer-text").value;

            
        //     // console.log(sel_dealer_name);
        //     // console.log(sel_dealer_company_name);
        //     // console.log(sel_dealer_address);
        //     // console.log(sel_dealer_phone);
        //     // console.log(sel_dealer_password);


        //     var form_data={
        //         'id':sel_faq_id,
        //         'question':sel_faq_question,
        //         'answer':sel_faq_answer,
        //     };
           
        // }

    });
</script>

<div>
    <div class="parallel-heading-div">
        <p class="dashboard-main-heading">
            FAQs > <span class="breadcrumb-small-text"> Edit FAQ </span>
        </p>
    </div>
    <div class="add-form-div">
        <form id = "update_faq_by_admin" method="post" autocomplete="off">
            @csrf
            <input type="hidden" name="id" id="id" value="{{ $sel_faq_id }}">
            <div class="row">
                <div class="form-group col">
                    <label for="question">Question</label>
                    <input type="text" value="{{ $faq->question }}" class="form-control" id="question" name="question" placeholder="Question" required/>
                    
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col">
                    <label for="answer">Answer</label>
                    <input type="text" value="{{ $faq->answer }}" class="form-control" id="answer" name="answer" placeholder="Answer" required/>
                    
                </div>
            </div>
            <hr>
            <button type="submit" id="btn-update-faq-by-admin" class="btn btn-success">
                Update FAQ
            </button>
            <div class="alert-box alert_success"></div>
            <div class="alert-box alert_failure"></div>
            <div class="alert-box alert_warning"></div>

            {{-- <div class="form-innner-div">
                <div class="column-div">
                    <div class="input-outer-div">
                        <div class="label-div">
                            <label>Question</label>
                        </div>
                        <div>
                            <input type="textarea" value="{{ $faq->question }}" id="faq-question-text" name="question" placeholder="Question" />
                        </div>
                    </div>
                    <div class="input-outer-div">
                        <div class="label-div">
                            <label>Answer</label>
                        </div>
                        <div>
                            <input type="textarea" value="{{ $faq->answer }}" id="faq-answer-text" name="answer" placeholder="Answer" />
                        </div>
                    </div>
                    <div class="input-outer-div">
                        <!-- <div class="label-div">
                            <label>Submit</label>
                        </div> -->
                        
                    </div>
                    
                    
                </div>
                <!-- <div class="column-div">
                    
                    
                    
                </div> -->
            </div> --}}
        </form>
    </div>
</div>
@endsection
