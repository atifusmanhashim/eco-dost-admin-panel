@extends('layouts.master')
@section('title')
Add New Dealer
@endsection

@section('content')

<script>
    $(document).ready(function(){

        const accessToken = "{{ Auth::user()->createToken(env('TOKEN_NAME'))->accessToken }}";

        var add_new_dealer_api="{{ url('api/create_vendor')}}";
        $("form").attr('autocomplete', 'off');
        $('.numberonly').empty();
        $('.numberonly').keypress(function (e) {    
    
            var charCode = (e.which) ? e.which : event.keyCode    

            if (String.fromCharCode(charCode).match(/[^0-9]/g))    

                return false;                        

        });   

        $.validator.setDefaults({
            submitHandler: function() {
                console.log("submitted!");
            }
        });
        $('#add-dealer-by-admin').click(function(e){
            e.preventDefault();
            $("#add_vendor_form").validate ();  
            if ($('#add_vendor_form').valid())
            {
                $('#add_vendor_form').submit();
            } else {
                $('#btn-add-user-by-admin').prop('disabled',false);
                $( "div.alert_failure" ).html('Please fill the required fields');
                $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
            }
        });
       
        $('#add_vendor_form').submit(function(e){
            e.preventDefault();
            $('#add-dealer-by-admin').prop('disabled',true);
            var formData = new FormData(this);
            $.ajax({
                url:add_new_dealer_api,
                type:'POST',
                headers: {
                        'Authorization': 'Bearer '+accessToken,
                        'Accept':'application/json'
                },
                data:formData,
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                error: function (xhr, status, data) {
                    if (xhr.status==400 || xhr.status==500)
                    {
                        var status_data=status;
                        $('#upload_error_message').empty();

                        $('#upload_error_message').empty();
                        $('#upload_error_message').show();
                        $('#upload_error_message').html(xhr.response);
                    } else if(xhr.status==401) {
                        window.location="{{url('/login')}}";
                    }


                },
                success:function(data)
                {
                    var data_response=data.response;
                    var response_msg=data_response.msg;
                    var response_success=data_response.success;
                    if (response_success==true)
                    {
                        $( "div.alert_success" ).html(response_msg);
                        $( "div.alert_success" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                        location.href = "{{ url('/vendors')}}";
                    } else {
                        $( "div.alert_failure" ).html(response_msg);
                        $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                    }
                },
                error: function(xhr, status, error) {
                    $('#add-dealer-by-admin').prop('disabled',false);
                    $( "div.alert_failure" ).html(xhr.responseJSON.response.msg);
                    // $( "div.alert_failure" ).html('Something Went Wrong');
                    $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                    // if (xhr.responseJSON && xhr.responseJSON.response && xhr.responseJSON.response.msg) {
                    //     alert("Error: " + xhr.responseJSON.response.msg);
                    // } else {
                    //     alert("AJAX Error: " + error);
                    // }
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

    });
</script>

<div>

    <div class="parallel-heading-div">
        <p class="dashboard-main-heading">
           Dealer > <span class="breadcrumb-small-text">Add New Dealer</span>
        </p>
    </div>


    <div class="add-form-div">
        <form id="add_vendor_form" method="POST" autocomplete="off"> 
            @csrf

            
            <div class="row">
                <div class="form-group col">
                    <label for="dealer-name-text">Dealer Name</label>
                    <input type="text" class="form-control" id="dealer-name-text" name="name" placeholder="Full Name" required/>
                </div>
                <div class="form-group col">
                    <label>Company Name</label>
                    <input type="text" class="form-control" id="dealer-company-name-text" name="company_name" placeholder="Company Name" required/>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col">
                    <label>Address</label>
                    <input type="text" class="form-control" id="dealer-address-text" name="address" placeholder="Address"/>
                </div>
                <div class="form-group col">
                    <label>City</label>
                    <select name="city_id" id="city_id" class="form-select">
                        @foreach($city_lst as $city)
                        <option value="{{ $city->city_id }}">{{ $city->city_name }}</option>
                        @endforeach
                    </select>
                    
                </div>
                <div class="form-group col">
                    <label>Type</label>
                    <select name="vendor_type_id" class="form-control form-select" id="vendor_type_id" name="vendor_type_id" class="vendor_type_id">
                        @foreach($vendor_type as $itm)
                            <option value="{{ $itm->vendor_type_id }}">{{ $itm->vendor_type_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col">
                    <label>Phone No.</label>
                    <input type="tel" class="form-control numberonly" id="dealer-phone-text" name="phone_no" placeholder="Phone Number e.g. 03001231231" autocomplete="off" required />
                </div>
                <div class="form-group col">
                    <label>Password</label>
                    <input type="password" class="form-control" id="dealer-password-text" name="password" placeholder="Enter Password" autocomplete="off" required/>
                </div>
            </div>
            <hr>
            <button id="add-dealer-by-admin" name="add-dealer-by-admin" class="btn btn-success w-100">
                Save
            </button>
            <div class="alert-box alert_success"></div>
            <div class="alert-box alert_failure"></div>
            <div class="alert-box alert_warning"></div>
        </form> 
    </div>
</div>
@endsection
