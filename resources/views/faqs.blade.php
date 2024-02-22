@extends('layouts.master')
@section('title')
FAQs
@endsection

@section('content')

<script>
    const accessToken = "{{ Auth::user()->createToken('SastaTareenAdmin Personal Access Client')->accessToken }}";


    var delete_faq_api="{{ url('api/delete_faq')}}";


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
        $('.edit_faq_btn').click(function(){
            var sel_faq_id=$(this).attr('data-id');
            // console.log(sel_user_id);
            window.location.href="{{url('/editfaq')}}/"+sel_faq_id;
        });

        $('.delete_faq_btn').click(function(){
            var faq_id=$(this).attr('data-id');
            console.log(faq_id);
            $('h4.modal-title').text('Confirm FAQ # '+faq_id+' Deletion ');
            $("#data_faq_id").val(faq_id);
        });

        $('#btn_delete_faq_yes').click(function(e){
            e.preventDefault();
            $('#delete_faq_frm').submit();
            
        });
        $('#delete_faq_frm').submit(function(e){
                e.preventDefault();
                $('#btn_delete_faq_yes').prop('disabled',true);
                var formData = new FormData(this);
                console.log(formData);
                $.ajax({
                    url: delete_faq_api,
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
                        location.href="{{url('/faqs')}}";
                    }
                })
        });
       
    });
</script>


<div>
    <div class="parallel-heading-div">
        <p class="dashboard-main-heading">
        FAQs
        </p>
        <div class="search-input-div" >
            <input type="search" placeholder="search" id="searchBox" onkeyup="searchTable()"/>
        </div>
        <div class="tabs-btn-div">

            <button onclick="window.location.href='{{url('/addfaq')}}'">+ Add New FAQ</button>
        </div>
    </div>
    <div class="table-responsive table-div" style="max-width: 1920px;">
        <table class="table" id="table_data">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">ID</th>
                    <th scope="col">Question</th>
                    <th scope="col">Answer</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1; ?>
                @foreach($faq_lst_recs as $itm)

                <tr>
                    <td>{{ $count }}</td>
                    <td>{{ $itm['faq_id'] }}</td>
                    <td>{{ $itm['faq_question'] }}</td>
                    <td>{{ $itm['faq_answer'] }}</td>
                    <td>{{ $itm['faq_status'] }}</td>
                    <td>
                        <div class="two-btn-div">
                            <button class="edit_faq_btn" data-id= "{{ $itm['faq_id'] }}" class="edit-btn"> <img class="edit-icon"
                                    src="{{url('images/edit-icon.svg')}}" /></button>
                            <button data-id="{{ $itm['faq_id'] }}" class="edit-btn delete_faq_btn" data-bs-toggle="modal"" data-bs-target="#faq_delete_modal"> <img class="delete-icon"
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
<div class="modal" id="faq_delete_modal">
    <form name="delete_faq_frm" id="delete_faq_frm" class="delete_faq_frm">
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
            <p>Do you want to delete selected faq?</p>
            <input type="hidden" name="id" id="data_faq_id" class="txt_data_faq_id">
        </div>
  
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
          <button type="button" class="btn btn-primary" id="btn_delete_faq_yes">Yes</button>
        </div>
  
      </div>
    </div>
  </div>
</div>

@endsection








