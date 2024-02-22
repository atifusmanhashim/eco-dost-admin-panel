@extends('layouts.master')
@section('title')
Vendors
@endsection

@section('content')

<script>
const accessToken = "{{ Auth::user()->createToken(env('TOKEN_NAME'))->accessToken }}";

var delete_dealer_api="{{ url('api/delete_vendor')}}";

function deleteUser(event) {
    if (confirm("Are you sure you want to delete this user?") == true) {
        const vendorId = event.currentTarget.getAttribute("data-id");
        // Now you can use the dataId variable for further processing or deletion
        // console.log("Data ID:", vendorId);
        // Perform your deletion logic here using the dataId variable
        const formObject = {
            vendor_id: vendorId // Add any other data you want to send to the server
        };
        $.ajax({
                url:delete_dealer_api,
                type: "POST",
                headers: {
                    "Authorization": "Bearer "+accessToken,
                    "Accept": "application/json"
                },
                data:formObject,
                success:function(response)
                {


                    alert(response.response.msg);
                    window.location.href = "/vendors";



                },
                error: function(xhr, status, error) {
                if (xhr.responseJSON && xhr.responseJSON.response && xhr.responseJSON.response.msg) {
                    alert("Error: " + xhr.responseJSON.response.msg);
                } else {
                    alert("AJAX Error: " + error);
                }
                }

            })

    } else {
        console.log("You canceled!");
        // Handle the cancel action if needed
    }
}


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
    $('.edit_vendor_btn').click(function(){
        var sel_vendor_id=$(this).attr('data-id');
        // console.log(sel_vendor_id);
        window.location.href="{{url('/editvendor')}}/"+sel_vendor_id;
    });

    $('.delete_vendor_btn').click(function(){
            var vendor_id=$(this).attr('data-id');
            console.log(vendor_id);
            $('h4.modal-title').text('Confirm vendor # '+vendor_id+' Deletion ');
            $("#data_vendor_id").val(vendor_id);
        });

        $('#btn_delete_vendor_yes').click(function(e){
            e.preventDefault();
            $('#delete_vendor_frm').submit();
            
        });
        $('#delete_vendor_frm').submit(function(e){
                e.preventDefault();
                $('#btn_delete_vendor_yes').prop('disabled',true);
                var formData = new FormData(this);
                console.log(formData);
                $.ajax({
                    url: delete_dealer_api,
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
                        location.href="{{url('/vendors')}}";
                    }
                })
        });
});
</script>


<div>
    <div class="parallel-heading-div">
        <p class="dashboard-main-heading">
            Dealers
        </p>
        <div class="search-input-div">
            <input type="search" placeholder="search" id="searchBox" onkeyup="searchTable()"/>
        </div>
        <div class="tabs-btn-div">
            <button onclick="window.location.href='{{url('/addvendor')}}'">+ Add New Dealer</button>
        </div>
    </div>
    <div class="table-responsive table-div" style="max-width: 1920px;">
        <table class="table" id="table_data">
            <thead>
                <tr>
                    <th scope="col">ID </th>
                    <th scope="col">Name</th>
                    <th scope="col">Company</th>
                    <th scope="col">City</th>
                    <th scope="col">Address</th>
                    <th scope="col">Phone No.</th>
                    <th scope="col">Type</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1; ?>
                @foreach($vendor_lst_recs as $itm)
                <tr>
                    <td>{{ $itm['vendor_id']}}</td>
                    <td>{{ $itm['name']}}</td>
                    <td>{{ $itm['company_name']}}</td>
                    <td>{{ $itm['city_name']}}</td>
                    <td>{{ $itm['address']}}</td>
                    <td>{{ $itm['phone_no']}}</td>
                    <td>{{ $itm['vendor_type_name']}}</td>
                    <td class="text-danger"> {{ $itm['vendor_status']}}</td>
                    <td>
                        <div class="two-btn-div">
                            <button class="edit_vendor_btn" data-id= "{{ $itm['vendor_id'] }}" class="edit-btn"> <img class="edit-icon"
                                src="{{url('images/edit-icon.svg')}}"  /></button>
                             <button data-id="{{ $itm['vendor_id'] }}" class="edit-btn delete_vendor_btn" data-bs-toggle="modal"" data-bs-target="#vendor_delete_modal"> <img class="delete-icon"
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
<div class="modal" id="vendor_delete_modal">
    <form name="delete_vendor_frm" id="delete_vendor_frm" class="delete_vendor_frm">
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
            <p>Do you want to delete selected vendor?</p>
            <input type="hidden" name="vendor_id" id="data_vendor_id" class="txt_data_vendor_id">
        </div>
  
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
          <button type="button" class="btn btn-primary" id="btn_delete_vendor_yes">Yes</button>
        </div>
  
      </div>
    </div>
  </div>
</div>

@endsection








