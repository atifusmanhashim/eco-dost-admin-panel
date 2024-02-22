@extends('layouts.master')
@section('title')
Add New User
@endsection

@section('content')

<script>
    $(document).ready(function(){

        const accessToken = "{{ Auth::user()->createToken(env('TOKEN_NAME'))->accessToken }}";

        var add_new_user_api="{{ url('api/register')}}";


        

        $('#btn-add-user-by-admin').click(function(e){
            e.preventDefault();
            $('#add_user_by_admin').validate();  
            if ($('#add_user_by_admin').valid())
            {
                $('#add_user_by_admin').submit();
            } else {
                $('#btn-add-user-by-admin').prop('disabled',false);
                $( "div.alert_failure" ).html('Please fill the required fields');
                $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
            }
            
        }); 

        $('#add_user_by_admin').submit(function(e){
            e.preventDefault();
            $('#btn-add-user-by-admin').prop('disabled',true);
            var formData = new FormData(this);
            $.ajax({
                url:add_new_user_api,
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
                        location.href = "{{ url('/users')}}";
                    } else {
                        $( "div.alert_failure" ).html(response_msg);
                        $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                    }
                    // alert(response.response.msg);
                    // window.location.href = "{{url('/users')}}";
            

                
                },
                error: function(xhr, status, error) {
                    $('#btn-add-user-by-admin').prop('disabled',false);
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
        // const form = document.getElementById("add_user_by_admin");
        // form.addEventListener("submit", addUserbyAdmin);
        // function addUserbyAdmin(event) {
        //     // log.textContent = `Form Submitted! Timestamp: ${event.timeStamp}`;
        //     event.preventDefault();
        //     const formData = new FormData(form);
        //     const formObject = {};

        //     formData.forEach((value, key)=>{
        //         // console.log(value);
        //         formObject[key]=value;
        //     });
        //     const userLocationDropdown = document.querySelector(".user_location_id_update");
        //     var LselectedOption = userLocationDropdown.options[userLocationDropdown.selectedIndex];
        //     const locationId = LselectedOption.getAttribute("data-id");

        //     formObject["location_id"] = locationId;


        //     const userRoleDropdown = document.querySelector(".user_role_id_update");
        //     var selectedOption = userRoleDropdown.options[userRoleDropdown.selectedIndex];
        //     const roleId = selectedOption.getAttribute("data-id");

        //     formObject["role_id"] = roleId;
        //     // console.log(formObject);

        //     // var form_data={
        //     //     'contact_no':formObject["contact_no"],
        //     //     'email':formObject["email"],
        //     //     'name':formObject["name"],
        //     //     'password':formObject["password"],
        //     //     'location_id':formObject["location_id"],
        //     //     'role_id':formObject["role_id"],
        //     // };

        //     // console.log(form_data);

        // }

    });
</script>

<div>
    <div class="parallel-heading-div">
        <p class="dashboard-main-heading">
            All Users > <span class="breadcrumb-small-text">Add New User</span>
        </p>
    </div>
    <div class="add-form-div">
        <form id="add_user_by_admin" method="post" autocomplete="off">
            @csrf
            <div class="row">
                <div class="form-group col">
                    <label for="user-name-text">Name</label>
                    <input type="text" class="form-control" id="user-name-text" name="name" placeholder="Full Name" required/>
                </div>
                <div class="form-group col">
                    <label for="user-email">Email</label>
                    <input type="text" class="form-control" id="user-email" name="email" placeholder="user@domain.com" required/>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col">
                    <label for="user-password-text">Password</label>
                    <input type="password" name="password" id="user-password-text" placeholder="Password" required />
                </div>
                <div class="form-group col">
                    <label for="user_role_id_update">Role</label>
                    <select name="role_id" class="form-select" id="user_role_id_update">
                        @foreach($available_roles as $itm)
                            <option name="role_id" value="{{ $itm['role_id'] }}" data-id= "{{ $itm['role_id'] }}">{{ $itm['role_name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col">
                    <label for="user-phone-text">Phone</label>
                    <input type="text" class="form-control numberonly" name="contact_no" placeholder="Phone Number e.g. 03001231231"/>
                </div>
                <div class="form-group col">
                    <label for="user_location_id_update">Location</label>
                    <select name="location_id" class="form-select" id="user_location_id_update">
                        @foreach($available_locations as $itm)
                            <option name="location_id" value="{{ $itm['location_id'] }}" data-id= "{{ $itm['location_id'] }}">{{ $itm['location_name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <hr>
            <button id="btn-add-user-by-admin" name="btn-add-user-by-admin" class="btn btn-success w-100">
                Create User
            </button>
            <div class="alert-box alert_success"></div>
            <div class="alert-box alert_failure"></div>
            <div class="alert-box alert_warning"></div>
        
        </form>
    </div>
</div>
@endsection
