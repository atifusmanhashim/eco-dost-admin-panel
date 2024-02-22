@extends('layouts.master')
@section('title')
Welcome
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

        const accessToken = "{{ Auth::user()->createToken('SastaTareen Admin Personal Access Client')->accessToken }}";
        const user_role_id="{{ Auth::user()->role_id }}";
        const admin_user_id="{{ Auth::user()->id }}";

        var complaint_details_api="{{ url('api/complaint_details')}}";

        var complaint_update_status_api="{{ url('api/update_complaint_sc')}}";

        const admin_user = "{{ request()->session()->get('user_id') }}";
        // console.log(admin_user);





        $('.complaints-row').click(function(e){
            e.preventDefault();
            var sel_complaint_id=$(this).attr('data-id');
            // console.log(sel_complaint_id);
            $('#complaint_id_span').html(sel_complaint_id);
            var form_data={
                'complaint_id':sel_complaint_id
            };

            $("#update-status-admin").css('display','none');
            $("#details_update_status_div_comments").css('display','none');
            $("#details_update_status_select").css('display','none');
            $('#status-complaint').append('<option selected value="Resolved">Resolved</option>');
           
            $.ajax({
                url:complaint_details_api,
                type: "POST",
                headers: {
                    "Authorization": "Bearer "+accessToken,
                    "Accept": "application/json"
                },
                data:form_data,
                success:function(response)
                {
                    var data_response=response.response;
                    var complaint_details=data_response.data.complaint;
                    // console.log(complaint_details);
                    var customer_name=complaint_details.customer_name;
                    var complaint_desc=complaint_details.description;
                    var complaint_part=complaint_details.part_name;
                    var complaint_make=complaint_details.make;
                    var complaint_model=complaint_details.model;
                    var complaint_chasis=complaint_details.chasis_number;

                    var complaint_dealer_name = complaint_details.dealer_name

                    var complaint_priority=complaint_details.priority_name;
                    var complaint_date_created=complaint_details.created_at;
                    var complaint_date_updated=complaint_details.updated_at;
                    var complaint_comments=complaint_details.comment_by_admin;
                    var complaint_battery=complaint_details.battery_number;
                    var complaint_motor=complaint_details.motor_number;
                    var warranty=complaint_details.warranty;
                    var warranty_desc=complaint_details.warranty_desc;
                    var comment_by_admin=complaint_details.comment_by_admin;
                    // console.log(complaint_motor);

                    
                    var complaint_status=complaint_details.complaint_status;
                    const status_complaint_drop = document.getElementById('status-complaint');

                    $("#status-complaint").empty();
                    if (complaint_status=="Pending" || complaint_status=="In Process")
                    {
                        $('#details_status_comments_resolved').css('display','none');
                        if (warranty==1)
                        {
                            if (user_role_id==1 || user_role_id==2)
                            {
                                $(".details_update_status_div").css('display','block');

                                $("#update-status-admin").css('display','block');
                                $("#details_update_status_div_comments").css('display','block');
                                $("#details_update_status_select").css('display','block');
                                $('#status-complaint').empty();
                                if (complaint_status=="Pending")
                                {
                                    $('#status-complaint').append('<option selected value="Pending">Pending</option>');
                                    $('#status-complaint').append('<option value="In Process">In Process</option>');
                                    $('#status-complaint').append('<option value="Resolved">Resolved</option>');
                                    $('#status-complaint').append('<option value="Cancelled">Cancelled</option>');
                                } else if (complaint_status=="In Process") {
                                    $('#status-complaint').append('<option value="Pending">Pending</option>');
                                    $('#status-complaint').append('<option selected value="In Process">In Process</option>');
                                    $('#status-complaint').append('<option value="Resolved">Resolved</option>');
                                    $('#status-complaint').append('<option value="Cancelled">Cancelled</option>');
                                }
                                
                            } else {
                                $(".details_update_status_div").css('display','none');
                                $("#details_update_status_div_comments").css('display','none');
                                $("#details_update_status_select").css('display','none');
                                $("#update-status-admin").css('display','none');
                            }
                        } else {
                            $("#details_update_status_div").css('display','block');

                            $("#update-status-admin").css('display','block');
                            $("#details_update_status_div_comments").css('display','block');
                            $("#details_update_status_select").css('display','block');
                            $('#status-complaint').empty();
                            if (complaint_status=="Pending")
                            {
                                $('#status-complaint').append('<option selected value="Pending">Pending</option>');
                                $('#status-complaint').append('<option value="In Process">In Process</option>');
                                $('#status-complaint').append('<option value="Resolved">Resolved</option>');
                                $('#status-complaint').append('<option value="Cancelled">Cancelled</option>');
                            } else if (complaint_status=="In Process") {
                                $('#status-complaint').append('<option value="Pending">Pending</option>');
                                $('#status-complaint').append('<option selected value="In Process">In Process</option>');
                                $('#status-complaint').append('<option value="Resolved">Resolved</option>');
                                $('#status-complaint').append('<option value="Cancelled">Cancelled</option>');
                            }
                            
                            
                        }


                    } else {
                        $('.details_status_comments_resolved').css('display','block');
                        $(".details_update_status_div").css('display','none');
                        $("#details_update_status_div_comments").css('display','none');
                        $("#details_update_status_select").css('display','none');
                        $("#update-status-admin").css('display','none');
                    }




                    var complaint_images=complaint_details.images;
                    const img_container = document.getElementById('image-container');
                    $('#image-container').empty();
                    // $('#img-display').empty();
                    if (complaint_images.length >= 1)
                    {
                        complaint_images.forEach(image=>
                        {
                            const div_img = document.createElement('div');
                            div_img.classList.add("img-div");
                            const img = document.createElement('img');
                            img.src = image.url;
                            div_img.appendChild(img);
                            img_container.appendChild(div_img);
                        })
                    }
                    else
                    {
                        // document.getElementById("img-display").style.display="none";
                        const text_img_unavail = document.createElement('span');
                        text_img_unavail.textContent = "No Images Available.";
                        img_container.appendChild(text_img_unavail);
                    }


                    // var complaint_images=complaint_details.images;
                    // const img_container = document.getElementById('image-container');
                    // $('#image-container').empty();
                    // complaint_images.forEach(image=>
                    // {
                    //     const div_img = document.createElement('div');
                    //     div_img.classList.add("img-div");
                    //     const img = document.createElement('img');
                    //     img.src = image.url;
                    //     div_img.appendChild(img);
                    //     img_container.appendChild(div_img);
                    // })

                    // console.log(complaint_images);

                    $('#complaint_description').html(complaint_desc);
                    $('#complaint_customer_name').html(customer_name);
                    $('#complaint_part').html(complaint_part);
                    $('#complaint_make').html(complaint_make);
                    $('#complaint_model').html(complaint_model);
                    $('#complaint_chasis').html(complaint_chasis);
                    $('#complaint_status').html(complaint_status);
                    $('#complaint_priority').html(complaint_priority);
                    $('#complaint_dealer_name').html(complaint_dealer_name);
                    $('#complaint_date_created').html(complaint_date_created);
                    $('#complaint_date_updated').html(complaint_date_updated);
                    $('#complaint_comments').empty();
                    $('#complaint_comments').val(complaint_comments);
                    $('#complaint_motor').html(complaint_motor);
                    $('#complaint_battery').html(complaint_battery);
                    $('#complaint_comment_admin').html(comment_by_admin);
                  //  window.location.reload();
                }

            })
        })

        $('#update-status-admin').click(function(e){
            e.preventDefault();
            $(this).prop('disabled',true);
            var sel_complaint_id= $('#complaint_id_span').text();
            console.log(sel_complaint_id)
            var sel_complaint_comment = $("#complaint_comments").val();

            var sel_complaint_status =$("#status-complaint").val();
            var form_data={
                'complaint_id':sel_complaint_id,
                'complaint_status':sel_complaint_status,
                'comment_by_admin':sel_complaint_comment,
                'user_id':"{{ Auth::user()->id }}"
            };
            $.ajax({
                url:complaint_update_status_api,
                type: "POST",
                headers: {
                    "Authorization": "Bearer "+accessToken,
                    "Accept": "application/json"
                },
                data:form_data,
                success:function(response)
                {
                    var data_response=response.response;
                    var response_msg=data_response.msg;
                    var response_success=data_response.success;
                    if (response_success==true)
                    {
                        $( "div.alert_success" ).html(response_msg);
                        $( "div.alert_success" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                        // location.href = "{{ url('/users')}}";
                        window.location.reload();
                    } else {
                        $( "div.alert_failure" ).html(response_msg);
                        $( "div.alert_failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
                    }
                   

                 


                }

            })
        })
        


           


        



        document.getElementById("inline-complaint-show").addEventListener("click", inlineComplaintsShow);
        function inlineComplaintsShow()
        {
            console.log("inline button pressed");
        }




    });
</script>


<div>
    <div class="parallel-heading-div">
        <p class="dashboard-main-heading">
            {{ Str::ucfirst($request_status_desc) }}
        </p>

        <div class="search-input-div">
            <input type="search" id="searchBox" placeholder="search" onkeyup="searchTable()" />
        </div>
        <div class="tabs-btn-div">
            <button onclick="window.location.href='{{url('/complaints')}}?request_status=All'" id="all-complaint-show">All</button>
            <button onclick="window.location.href='{{url('/complaints')}}?request_status=Pending'" id="pending-complaint-show">Pending</button>
            <button onclick="window.location.href='{{url('/complaints')}}?request_status=InProcess'" id="inprocess-complaint-show">In Process</button>
            <button onclick="window.location.href='{{url('/complaints')}}?request_status=Resolved'" id="resolved-complaint-show">Resolved</button>
            <button onclick="window.location.href='{{url('/complaints')}}?request_status=Cancelled'" id="cancelled-complaint-show">Cancelled</button>
            <button onclick="window.location.href='{{url('/complaints')}}?request_status=warranty'">Warranty</button>
            <!-- <button>See All</button> -->
        </div>
    </div>
    <div class="table-responsive table-div" style="max-width: 1920px;">
        <table class="table" id="table_data">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Customer</th>
                    <th scope="col">Part Name</th>
                    <th scope="col">Make</th>
                    <th scope="col">Model</th>
                    <th scope="col">Battery No.</th>
                    <th scope="col">Motor No.</th>
                    <th scope="col">Dealer Name</th>
                    <th scope="col">Priority</th>
                    <th scope="col">Warranty</th>
                    <th scope="col">Status</th>
                    <th scope="col">Date</th>
                </tr>
            </thead>
            <?php $count = 1; ?>
                @foreach($complaint_lst_recs as $itm)

                <tbody>
                    <tr data-id= "{{ $itm['complaint_id'] }}" class="complaints-row" data-bs-toggle="modal" data-bs-target="#complainDetailModal" style="cursor:pointer;">
                        <td>{{ $count }}</td>
                        <td>{{ $itm['customer_name'] }}</td>
                        <td>{{ $itm['part_name'] }}</td>
                        <td>{{ $itm['make'] }}</td>
                        <td>{{ $itm['model'] }}</td>
                        <td>{{ $itm['battery_number'] }}</td>
                        <td>{{ $itm['motor_number'] }}</td>
                        <td>{{ $itm['vendor_name'] }}</td>
                        <td>{{ $itm['priority_name'] }}</td>
                        <td>{{ $itm['warranty_desc'] }}</td>
                        <td>{{ $itm['complaint_status'] }}</td>
                        <td>{{ $itm['created_at'] }}</td>
                    </tr>
                    <?php $count++; ?>
                    @endforeach

                </tbody>
        </table>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="complainDetailModal" tabindex="-1" aria-labelledby="complainDetailModal"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content modal-lg">
                    <div class="modal-header  modal-lg">
                        <h5 class="modal-title" id="exampleModalLabel">Complaint Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body modal-lg">
                        <div class="modal-content-div">
                            <div class="mb-4"><strong>ID No. : <span id="complaint_id_span"></span></strong></div>
                            <div class="mb-4">
                                <strong> Customer : <span id="complaint_customer_name"></span> </strong>
                            </div>
                            <div class="mb-4">
                                <strong> Description : <span id="complaint_description"></span> </strong>
                            </div>
                            <div id="img-display" class="mb-4">
                                <strong> Images : </strong>

                                <div id ="image-container" class="mt-2 d-flex flex-wrap gap-4">
                                    <!-- <div class="img-div">
                                        <img src="./assets/images/bike.png" />
                                    </div>
                                    <div class="img-div">
                                        <img src="./assets/images/bike.png" />
                                    </div>
                                    <div class="img-div">
                                        <img src="./assets/images/bike.png" />
                                    </div> -->
                                </div>
                            </div>
                            <div class="d-flex gap-5 mb-4">
                                <div class="custom-mn-width"><strong>Part Name : </strong> <span id="complaint_part"></span></div>
                                <div class="custom-mn-width"><strong>Make : </strong> <span id="complaint_make"></span></div>
                                <div class="custom-mn-width"><strong>Model : </strong> <span id="complaint_model"></span></div>
                            </div>
                            <div class="d-flex gap-5 mb-4">
                                <div class="custom-mn-width"> <strong> Chasis No.: </strong><span id="complaint_chasis"></span></div>
                                <div class="custom-mn-width"> <strong> Battery No.: </strong><span id="complaint_battery"></span></div>
                                <div class="custom-mn-width"> <strong> Motor No.: </strong><span id="complaint_motor"></span></div>
                            </div>
                            <div class="d-flex gap-5 mb-4">
                                <div class="custom-mn-width"> <strong> Priority: </strong>
                                    <span id="complaint_priority"></span>
                                </div>
                                <div class="custom-mn-width"> <strong> Dealer Name: </strong>
                                    <span id="complaint_dealer_name"></span>
                                </div>
                            </div>
                            <div class="d-flex gap-5 mb-4">
                                <div class="custom-mn-width"> <strong> Date Logged: </strong><span id="complaint_date_created"></div>
                                <div class="custom-mn-width"> <strong> Date Updated: </strong><span id="complaint_date_updated"></div>
                            </div>
                            <div class="d-flex gap-5 mb-4" id="details_status_comments_resolved">
                                <div class="custom-mn-width"> <strong> Comment(s): </strong><span id="complaint_comment_admin"></span></div>
                            </div>
                            <div class="d-flex gap-5 mb-4 details_update_status_div">
                                <div id="details_update_status_select"> <strong> Status: </strong>
                                    <select id="status-complaint">

                                    </select>
                                </div>

                                <div class="text-area-div" id="details_update_status_div_comments">
                                    <strong> Comment by Admin : </strong>
                                    <textarea id="complaint_comments" rows="4"> </textarea>
                                </div>
                            </div>
                            <div class="alert-box alert_success"></div>
                            <div class="alert-box alert_failure"></div>
                            <div class="alert-box alert_warning"></div>
                        </div>
                    </div>
                    <div class="modal-footer modal-lg">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="update-status-admin" class="btn btn-success update-status">Update changes</button>
                    </div>
                </div>
            </div>
    </div>
</div>


@endsection








