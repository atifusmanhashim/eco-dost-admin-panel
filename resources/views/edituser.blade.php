@extends('layouts.master')
@section('title')
Edit User
@endsection

@section('content')
<script>
$(document).ready(function() {

    var update_user_api = "{{ url('api/update_user_details')}}";

    const accessToken = "{{ Auth::user()->createToken(env('TOKEN_NAME'))->accessToken }}";

    const sel_user_id = "{{ $sel_user_id}}";


    $('#btn-update-user-by-admin').submit(function(e){
        e.preventDefault();
        $('#update_user_by_admin').validate();
        if ($('#update_user_by_admin').valid())
        {
            $('#update_user_by_admin').submit();
        } else {
            $('#btn-add-user-by-admin').prop('disabled',false);
            $( "div.alert_failure" ).html('Please fill the required fields');
            $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
        }
       
    });
    
    $('#update_user_by_admin').submit(function(e){
        e.preventDefault();
        $('#btn-update-user-by-admin').prop('disabled',true);
        var formData = new FormData(this);
        $.ajax({
            url: update_user_api,
            type: "POST",
            headers: {
                "Authorization": "Bearer " + accessToken,
                "Accept": "application/json"
            },
            data: formData,
            dataType:'JSON',
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {
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
                location.href = "{{ url('/users')}}";
                // window.location.href="{{url('/users')}}";
                // alert(response.response.msg);
                // window.location.reload();



            },
            error: function(xhr, status, error) {
                $('#btn-update-user-by-admin').prop('disabled',false);
                $( "div.alert_failure" ).html(xhr.responseJSON.response.msg);
                // $( "div.alert_failure" ).html('Something Went Wrong');
                $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                // if (status!=401)
                // {
                //     if (xhr.responseJSON && xhr.responseJSON.response && xhr.responseJSON.response
                //     .msg) {
                //         alert("Error: " + xhr.responseJSON.response.msg);
                //     } else {
                //         alert("AJAX Error: " + error);
                //     }
                // } else {
                //     location.href="{{url('/login')}}"
                // }
                
            }

        })
    })
    // console.log(sel_user_id);

    // const form = document.getElementById("update_user_by_admin");
    // form.addEventListener("submit", updateUserbyAdmin);

    // function updateUserbyAdmin(event) {
    //     // log.textContent = `Form Submitted! Timestamp: ${event.timeStamp}`;
    //     event.preventDefault();
    //     const formData = new FormData(form);
    //     const formObject = {};

    //     formData.forEach((value, key) => {
    //         // console.log(value);
    //         formObject[key] = value;
    //     });
    //     const userLocationDropdown = document.querySelector(".user_location_id_update");
    //     var LselectedOption = userLocationDropdown.options[userLocationDropdown.selectedIndex];
    //     const locationId = LselectedOption.getAttribute("data-id");

    //     formObject["location_id"] = locationId;

    //     formObject["user_id"] = sel_user_id;

    //     const userRoleDropdown = document.querySelector(".user_role_id_update");
    //     var selectedOption = userRoleDropdown.options[userRoleDropdown.selectedIndex];
    //     const roleId = selectedOption.getAttribute("data-id");

    //     formObject["role_id"] = roleId;
    //     // console.log(formObject);

    //     $.ajax({
    //         url: update_user_api,
    //         type: "POST",
    //         headers: {
    //             "Authorization": "Bearer " + accessToken,
    //             "Accept": "application/json"
    //         },
    //         data: formObject,
    //         success: function(response) {
    //             var data_response=response.response;
    //             var response_msg=data_response.msg;
    //             var response_success=data_response.success;
    //             if (response_success==true)
    //             {
    //                 $( "div.alert_success" ).html(response_msg);
    //                 $( "div.alert_success" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                   
    //             } else {
    //                 $( "div.alert_failure" ).html(response_msg);
    //                 $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
    //             }
    //             location.href = "{{ url('/users')}}";
    //             // window.location.href="{{url('/users')}}";
    //             // alert(response.response.msg);
    //             // window.location.reload();



    //         },
    //         error: function(xhr, status, error) {
    //             if (status!=401)
    //             {
    //                 if (xhr.responseJSON && xhr.responseJSON.response && xhr.responseJSON.response
    //                 .msg) {
    //                     alert("Error: " + xhr.responseJSON.response.msg);
    //                 } else {
    //                     alert("AJAX Error: " + error);
    //                 }
    //             } else {
    //                 location.href="{{url('/login')}}"
    //             }
                
    //         }

    //     })
    // }
});
</script>
<div>
    <div class="parallel-heading-div">
        <p class="dashboard-main-heading">
            All Users > <span class="breadcrumb-small-text">Edit User </span>
        </p>
    </div>
    <div class="add-form-div">
        <form id="update_user_by_admin" method="post" autocomplete="off">
            @csrf
            <input name="user_id" type="hidden" id="user_id" value="{{ $sel_user_id }}">
            <div class="row">
                <div class="form-group col">
                    <label for="user-name-text">Name</label>
                    <input type="text" value="{{ $user_data->name }}" class="form-control" id="user-name-text" name="name" placeholder="Full Name" required/>
                </div>
                <div class="form-group col">
                    <label for="user-email">Email</label>
                    <input type="email" value="{{ $user_data->email }}" class="form-control" id="user-email" name="email" placeholder="user@domain.com" readonly=true required/>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col">
                    <label for="user-password-text">Password</label>
                    <input type="password" name="password" id="user-password-text" placeholder="Password"/>
                </div>
                <div class="form-group col">
                    <label for="user_role_id_update">Role</label>
                    <select name="role_id" class="form-select" id="user_role_id_update">
                        @foreach($available_roles as $itm)
                        @if($itm['role_id']===$user_data->role_id)
                            <option selected value="{{ $itm['role_id'] }}">{{ $itm['role_name'] }}</option>
                        @else
                            <option value="{{ $itm['role_id'] }}">{{ $itm['role_name'] }}</option>
                        @endif
                        
                        @endforeach
                    </select>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col">
                    <label for="user-phone-text">Phone</label>
                    <input type="text" class="form-control numberonly" value="{{$user_data->contact_no}}" name="contact_no" placeholder="Phone Number e.g. 03001231231"/>
                </div>
                <div class="form-group col">
                    <label for="user_location_id_update">Location</label>
                    <select name="location_id" class="form-select" id="user_location_id_update">
                        @foreach($available_locations as $itm)
                            @if($itm['location_id']==$user_data->location_id)
                                <option selected value="{{ $itm['location_id'] }}">{{ $itm['location_name'] }}</option>
                            @else
                                <option value="{{ $itm['location_id'] }}">{{ $itm['location_name'] }}</option>
                            @endif
                        
                        @endforeach
                    </select>
                </div>
            </div>
            <hr>
            <button id="btn-update-user-by-admin" name="btn-update-user-by-admin" class="btn btn-success w-100">
                Update User Details
            </button>
            <div class="alert-box alert_success"></div>
            <div class="alert-box alert_failure"></div>
            <div class="alert-box alert_warning"></div>
           
        </form>
    </div>
</div>
@endsection