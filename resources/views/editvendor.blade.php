@extends('layouts.master')
@section('title')
Edit Vendor
@endsection

@section('content')
<script>
$(document).ready(function() {

    var update_dealer_api = "{{ url('api/update_vendor_admin')}}";

    const accessToken = "{{ Auth::user()->createToken(env('TOKEN_NAME'))->accessToken }}";

    const sel_vendor_id = "{{ $sel_vendor_id}}";

    $("#update_vendor_by_admin").validate ();
    $('#update-dealer-by-admin').click(function(e){
        e.preventDefault();
        $('#update_vendor_by_admin').validate();
        if ($('#update_vendor_by_admin').valid())
        {
            $('#update_vendor_by_admin').submit();
        } else {
            $('#btn-add-user-by-admin').prop('disabled',false);
            $( "div.alert_failure" ).html('Please fill the required fields');
            $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
        }
       
    });

    $('#update_vendor_by_admin').submit(function(e){
        e.preventDefault();
        $('#update-dealer-by-admin').prop('disabled',true);
        var formData = new FormData(this);
        $.ajax({
            url: update_dealer_api,
            type: "POST",
            headers: {
                "Authorization": "Bearer " + accessToken,
                "Accept": "application/json"
            },
            data:formData,
            dataType:'JSON',
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {

                var data_response=response;
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
                window.location.href = "{{ url('/vendors')}}";
                // alert(response.response.msg);
                // // window.location.href = "/vendors";
                // window.location.reload();



            },
            error: function(xhr, status, error) {
                $('#update-dealer-by-admin').prop('disabled',false);
                $('#btn-add-user-by-admin').prop('disabled',false);
                $( "div.alert_failure" ).html('Something Went Wrong');
                $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                // if (xhr.responseJSON && xhr.responseJSON.response && xhr.responseJSON.response
                //     .msg) {
                //   //  alert("Error: " + xhr.responseJSON.response.msg);
                // } else {
                //   //  alert("AJAX Error: " + error);
                // }
            }

        })
    });


});
</script>
<div>
    <div class="parallel-heading-div">
        <p class="dashboard-main-heading">
            Dealers > <span class="breadcrumb-small-text">Update Dealer Information </span>
        </p>
    </div>
    <div class="add-form-div">
        <form id="update_vendor_by_admin" method="post" autocomplete="off">
            @csrf
            <input type="hidden" name="vendor_id" id="vendor_id" value="{{ $sel_vendor_id }}">
            <div class="row">
                <div class="form-group col">
                    <label for="dealer-name-text">Dealer Name</label>
                    <input type="text" class="form-control" value="{{ $vendor->name }}" name="name" id="dealer-name-text"
                        placeholder="Full Name" required/>
                </div>
                <div class="form-group col">
                    <label>Company Name</label>
                    <input type="text" class="form-control" name="company_name" value="{{ $vendor->company_name }}"
                        id="dealer-company-name-text" placeholder="Company Name" required/>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col">
                    <label>Address</label>
                    <input type="text" class="form-control" value="{{ $vendor->address }}" name="address" id="dealer-address-text"
                        placeholder="Address"/>
                </div>
                <div class="form-group col">
                    <label>City</label>
                    <select name="city" id="city" class="form-select">
                        @foreach($city_lst as $city)
                            @if ($vendor->city_id==$city->city_id)
                                <option selected value="{{ $city->city_id }}">{{ $city->city_name }}</option>
                            @else
                                <option value="{{ $city->city_id }}">{{ $city->city_name }}</option>
                            @endif
                        @endforeach
                    </select>
                    
                </div>
                <div class="form-group col">
                    <label>Type</label>
                    <select name="vendor_type_id" class="form-control form-select" name="vendor_type_id" id="vendor_type_id"
                        class="vendor_type_id">
                        @foreach($vendor_type as $itm)
                            @if ($vendor->vendor_type_id==$itm->vendor_type_id)
                                <option selected value="{{ $itm->vendor_type_id }}">{{ $itm->vendor_type_name }}</option>
                            @else
                                <option value="{{ $itm->vendor_type_id }}">{{ $itm->vendor_type_name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col">
                    <label>Phone No.</label>
                    <input type="tel" class="form-control" value="{{ $vendor->phone_no }}" name="phone_no" id="dealer-phone-text"
                        placeholder="Phone Number e.g. 03001231231"/>
                </div>
                <div class="form-group col">
                    <label>Password</label>
                    <input type="password" class="form-control" id="dealer-password-text" name="password" placeholder="Enter Password" />
                </div>
            </div>
            <hr>
            <button id="update-dealer-by-admin" name="update-dealer-by-admin" class="btn btn-success w-100">
                Save
            </button>
            <div class="alert-box alert_success"></div>
            <div class="alert-box alert_failure"></div>
            <div class="alert-box alert_warning"></div>
        </form>
    </div>

</div>
@endsection