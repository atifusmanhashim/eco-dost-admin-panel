@extends('layouts.master')
@section('title')
All Users
@endsection

@section('content')

<script>
    const accessToken = "{{ Auth::user()->createToken(env('TOKEN_NAME'))->accessToken }}";

    var delete_user_api="{{ url('api/delete-user')}}";

    // function deleteAdminUser(event) {
    //     if (confirm("Are you sure you want to delete this user?") == true) {
    //         const userId = event.currentTarget.getAttribute("data-id");
    //         // Now you can use the dataId variable for further processing or deletion
    //         // console.log("Data ID:", userId);
    //         // Perform your deletion logic here using the dataId variable
    //         const formObject = {
    //             user_id: userId // Add any other data you want to send to the server
    //         };
    //         $.ajax({
    //                 url:delete_user_api,
    //                 type: "POST",
    //                 headers: {
    //                     "Authorization": "Bearer "+accessToken,
    //                     "Accept": "application/json"
    //                 },
    //                 data:formObject,
    //                 success:function(response)
    //                 {
                        
                        
    //                   //  alert(response.response.msg);
    //                     window.location.href = "/users";
                

                    
    //                 },
    //                 error: function(xhr, status, error) {
    //                 if (xhr.responseJSON && xhr.responseJSON.response && xhr.responseJSON.response.msg) {
    //                     alert("Error: " + xhr.responseJSON.response.msg);
    //                 } else {
    //                     alert("AJAX Error: " + error);
    //                 }
    //                 }

    //             })

    //     } else {
    //         console.log("You canceled!");
    //         // Handle the cancel action if needed
    //     }
    // }
    function searchTable() {
        // Declare variables
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("searchBox");
        filter = input.value.toUpperCase();
        table = document.getElementById("table_data");
        tr = table.getElementsByTagName("tr");

        // Loop through all table rows (except the header row), and hide those who don't match the search query
        for (i = 1; i < tr.length; i++) { // Start from index 1 to skip the header row (th)
            var foundMatch = false;
            // Loop through all table columns (td elements) for each row (tr element)
            for (j = 0; j < tr[i].getElementsByTagName("td").length; j++) {
                td = tr[i].getElementsByTagName("td")[j];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        foundMatch = true;
                        break; // If a match is found in any column, no need to check the rest
                    }
                }
            }
            // Show/hide the row based on whether a match was found
            if (foundMatch) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }

    $(document).ready(function(){
        $('.edit_user_btn').click(function(){
            var sel_user_id=$(this).attr('data-id');
            // console.log(sel_user_id);
            window.location.href="{{url('/edituser')}}/"+sel_user_id;
        });


        $('.delete_user_btn').click(function(){
            var user_id=$(this).attr('data-id');
            console.log(user_id);
            $('h4.modal-title').text('Confirm User # '+user_id+' Deletion ');
            $("#data_user_id").val(user_id);
        });

        $('#btn_delete_yes').click(function(e){
            e.preventDefault();
            $('#delete_user_frm').submit();   
        });
        $('#delete_user_frm').submit(function(e){
                e.preventDefault();
                $('#btn_delete_yes').prop('disabled',true);
                var formData = new FormData(this);
                console.log(formData);
                $.ajax({
                    url: delete_user_api,
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
                    success:function(data)
                    {
                        var data_response=data.response;
                        console.log(data_response);
                        location.href="{{url('/users')}}";
                    }
                })
        });
    });
</script>


<div>
    <div class="parallel-heading-div">
        <p class="dashboard-main-heading">
        Admin Users
        </p>
        <div class="search-input-div" >
            <input type="search" placeholder="search" id="searchBox" onkeyup="searchTable()"/>
        </div>
        <div class="tabs-btn-div">

            <button onclick="window.location.href='{{url('/adduser')}}'">+ Add New User</button>
        </div>
    </div>
    <div class="table-responsive table-div" style="max-width: 1920px;">
        <table class="table" id="table_data">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Phone No.</th>
                    <th scope="col">Date of Joining</th>
                    <th scope="col">Role</th>
                    <th scope="col">Location</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1; ?>
                @foreach($user_lst_recs as $itm)

                <tr>
                    <td>{{ $count }}</td>
                    <td>{{ $itm['user_id'] }}</td>
                    <td>{{ $itm['user_name'] }}</td>
                    <td>{{ $itm['contact_no'] }}</td>
                    <td>{{ $itm['created_at'] }}</td>
                    <td>{{ $itm['role_name'] }}</td>
                    <td>{{ $itm['location_name'] }}</td>
                    <td>{{ $itm['user_status'] }}</td>
                    <td>
                        <div class="two-btn-div">
                            <button class="edit_user_btn" data-id= "{{ $itm['user_id'] }}" class="edit-btn"> <img class="edit-icon"
                                    src="{{url('images/edit-icon.svg')}}" /></button>
                            <button data-id="{{ $itm['user_id'] }}" class="edit-btn delete_user_btn" data-bs-toggle="modal" data-bs-target="#user_delete_modal"> <img class="delete-icon"
                                src="{{url('images/delete-icon.svg')}}" /></button>
                        </div>
                    </td>
                </tr>
                <?php $count++; ?>
                @endforeach

            </tbody>
        </table>
    </div>
</div>

<!-- The Modal -->
<div class="modal" id="user_delete_modal">
    <form name="delete_user_frm" id="delete_user_frm" class="delete_user_frm">
    @csrf
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
  
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title"></h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
  
        <!-- Modal body -->
        <div class="modal-body">
            <p>Do you want to delete selected user?</p>
            <input type="hidden" name="user_id" id="data_user_id" class="txt_data_user_id">
        </div>
  
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
          <button type="button" class="btn btn-primary" id="btn_delete_yes">Yes</button>
        </div>
  
      </div>
    </div>
  </div>
</div>
@endsection








