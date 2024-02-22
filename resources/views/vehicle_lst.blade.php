@extends('layouts.master')
@section('title')
Vehicles
@endsection

@section('content')
<script>
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
    
        const accessToken = "{{ Auth::user()->createToken(env('TOKEN_NAME'))->accessToken }}";

        var download_vehicle_data_api="{{ url('download_vehicles_list')}}";
        $('#downloadbtn').on('click',function(even) {


            window.location=download_vehicle_data_api;


        });
        // var upload_inventory_api_url="{{url('api/import_inventory')}}";
        
        // $("#processbtn").css("display", "none");

        // $('#closeuploadbtn').hide();

        // var excel_file_element=$('#excel_file');
        // check_csv_extension(excel_file_element);
        // if (excel_file_element==true)
        // {
        //     $('#uploadbtn').prop('disabled','false');
        // } else {
        //     $('#uploadbtn').prop('disabled','true');
        // }

        // function check_csv_extension(file_element)
        // {
        //     var ext=file_element.val().split(".").pop().toLowerCase();
        //     var csvResult="";
        //     if($.inArray(ext, ["csv"]) == -1) {

        //         $('#upload_error_message').show();
        //         $('#upload_error_message').empty();
        //         $('#upload_error_message').removeClass();
        //         $('#upload_error_message').addClass('alert-danger');
        //         $('#upload_error_message').html("Please Upload CSV File");
        //         $('#upload_error_message').prop('disabled', true);
        //         return false;

        //     } else {
        //         $('#upload_error_message').empty();
        //         $('upload_error_message').css('display','none');
        //         $('#upload_error_message').removeClass();
        //         $('#uploadbtn').prop('disabled', false);
        //     }
        //     return false;
        // }



        // $('#excel_file').change(function(e){
        //             var ext = $("input#excel_file").val().split(".").pop().toLowerCase();
        //             var csvResult="";

        //             if($.inArray(ext, ["csv"]) == -1) {

        //                 $('#upload_error_message').show();
        //                 $('upload_error_message').empty();
        //                 $('#upload_error_message').removeClass();
        //                 $('#upload_error_message').addClass('alert-danger');
        //                 $('#upload_error_message').html("Alert! Please Upload CSV File");
        //                 $('#uploadbtn').prop('disabled', true);
        //                 return false;

        //             } else {
        //                 $('upload_error_message').empty();
        //                 $('#upload_error_message').html("");
        //                 $('upload_error_message').css('display','none');
        //                 $('#upload_error_message').removeClass();
        //                 $('#uploadbtn').prop('disabled', false);
        //             }
        //             return false;
        // });

        // $('#uploadbtn').click(function(e){

        //     event.preventDefault();
        //     $('#uploadbtn').prop('disabled', true);

        //     $('#upload_inventory_frm').submit();
        // });

        // $('#upload_inventory_frm').submit(function(e)
        // {
        //     e.preventDefault();
        //     var formData = new FormData(this);
        //     var txt="";
        //     var myFile = document.getElementById("excel_file");
        //     if (myFile.files.length > 0) {

        //         formData.append("excel_file", myFile.files[0]);
        //     }
        //     console.log(myFile.files[0]);
        //     console.log(formData);
        //     $.ajax({
        //         url:upload_inventory_api_url,
        //         type:'POST',
        //         headers: {
        //                 'Authorization': 'Bearer '+accessToken,
        //                 'Accept':'application/json'
        //         },
        //         data:formData,
        //         dataType:'JSON',
        //         contentType: false,
        //         cache: false,
        //         processData: false,
        //         error: function (xhr, status, data) {
        //             if (xhr.status==400 || xhr.status==500)
        //             {
        //                 var status_data=status;
        //                 $('#upload_error_message').empty();

        //                 $('#upload_error_message').empty();
        //                 $('#upload_error_message').show();
        //                 $('#upload_error_message').html(xhr.response);
        //             } else if(xhr.status==401) {
        //                 window.location="{{url('/login')}}";
        //             }


        //         },
        //         success:function(data)
        //         {
        //             var data_response=data.response;
        //             var data_msg=data_response.msg;
        //             var data_success=data_response.success;
        //             var data_rows_imported=data.rows_imported;
        //             var data_rows=data_response.total_rows;



        //             if (data_success==true)
        //             {
        //                 console.log(data_success)
        //                 $('#upload_message').css('display','block');
        //                 $('#upload_message').empty();
        //                 $('#upload_message').addClass('alert-success');
        //                 $('#upload_message').html(data_msg);
        //                 window.location.reload();
        //             } else {

        //                 $('#upload_message').empty();
        //                 $('#upload_message').css('display','none');
        //                 $('#upload_error_message').css('display','block');
        //                 $('#upload_error_message').addClass('alert-danger');
        //                 $('#upload_error_message').html(data_msg);
        //             }
        //         }
        //     })
            
        // });


    });
</script>
<div>
    <div class="parallel-heading-div">
        <p class="dashboard-main-heading">
           Vehicles
        </p>
        <div class="search-input-div">
            <input type="search" placeholder="search" id="searchBox" onkeyup="searchTable()"/>
        </div>
        <div class="tabs-btn-div">
            <button id="downloadbtn">Download List</button>
        </div>
        {{-- <div class="tabs-btn-div">
            <button type="button" data-bs-toggle="modal" data-bs-target="#upload_inventory">
               Upload Vehicle(s) Data
              </button>
        </div> --}}
    </div>
    <div class="table-responsive table-div" style="max-width: 1920px;">
        <table class="table" id="table_data">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">ID</th>
                    <th scope="col">Chasis #</th>
                    <th scope="col">Purchase Date</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <?php $count = 1; ?>
                @foreach($vehicle_lst_recs as $itm)

                <tbody>
                    <tr>
                        <td>{{ $count }}</td>
                        <td>{{ $itm['vehicle_id'] }}</td>
                        <td>{{ $itm['vehicle_chasis_number'] }}</td>
                        <td>{{ $itm['date_purchase'] }}</td>
                        <td>{{ $itm['vehicle_status'] }}</td>
                    </tr>
                    <?php $count++; ?>
                    @endforeach

                </tbody>
        </table>
    </div>
</div>

@endsection