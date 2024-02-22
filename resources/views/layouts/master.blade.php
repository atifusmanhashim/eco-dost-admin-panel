<!DOCTYPE html>
<html lang="en">

<head>
    <title> Welcome {{ request()->session()->get('user_name') }} | ECO DOST Admin Panel - @yield('title') </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="{{URL::asset('css/layout.css')}}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"> </script>  
    <script>
        $(document).ready(function() {
            const accessToken = "{{ Auth::user()->createToken(env('TOKEN_NAME'))->accessToken }}";
            const user_role_id = "{{ Auth::user()->role_id }}";
            // const pendingapprovals_api_url="{{ url('api/pendingapprovals') }}";
            // get_complaint_approvals();

            // function get_complaint_approvals()
            // {
            //     var req_approval_qty=0;
            //     $.ajax({
            //         url:pendingapprovals_api_url,
            //         type: "GET",
            //         headers: {
            //             "Authorization": "Bearer "+accessToken,
            //             "Accept": "application/json"
            //         },
            //         success:function(response)
            //         {
            //             var data_response=response.response;
            //             console.log(data_response)

            //             console.log(data_response.total_approvals)
            //             $('#complaint_approval').html(data_response.total_approvals);

            //         }
            //     })

            // }
            console.log(user_role_id)
            if (user_role_id == 3) {

                console.log('Operator')

                $('#pending_approval').css('display', 'none');
                $('#users_option').css('display', 'none');
            } else if (user_role_id == 1 || user_role_id == 2) {
                console.log('Manager / Super Admin')
                $('#pending_approval').css('display', 'block');
                $('#users_option').css('display', 'block');
            }

            $('#inventory-table').DataTable({
                "aLengthMenu": [
                    [5, 10, 25, -1],
                    [5, 10, 25, "All"]
                ],
                "iDisplayLength": 5
            });

            $("#sidebar-collapse").click(function() {
                $(".sidebar").toggleClass("sidebar-is-collapsed");
                $(".sidebar-link-text").toggleClass("sidebar-link-text-none")
                var img = $(".logo-div img");
                if (img.attr("src") === "{{url::asset('images/ecodost-logo.svg')}}") {
                    img.attr("src",
                    "{{url::asset('images/collapsed-eco-dost.png')}}"); // Change to the new image path
                } else {
                    img.attr("src",
                    "{{url::asset('images/ecodost-logo.svg')}}"); // Change back to the original image path
                }
            });

            $('.numberonly').empty();
            $('.numberonly').keypress(function (e) {    
        
                var charCode = (e.which) ? e.which : event.keyCode    

                if (String.fromCharCode(charCode).match(/[^0-9]/g))    

                    return false;                        

            });   
        });
    </script>
</head>

<body>
    <div class="layout-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="w-100">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                    <a class="navbar-brand" href="#">
                        <img src="{{url::asset('images/ecodost-logo.svg')}}"  />
                    </a>
                    <ul class="navbar-nav me-auto mb-lg-0">
                        <li class="nav-item">
                            <a href="{{url('/dashboard')}}">
                                <div class="sidebar-links-outer-div">
                                    <div class="sidebar-link">
                                        <div>
                                            <img src="{{url::asset('images/dashboard-icon.svg')}}" />
                                        </div>
                                        <div class="sidebar-link-text">
                                            Dashboard
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/complaints')}}">
                                <div class="sidebar-links-outer-div">
                                    <div class="sidebar-link">
                                        <div>
                                            <img src="{{url::asset('images/Complaint.svg')}}" />

                                        </div>
                                        <div class="sidebar-link-text">
                                            Complaints
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/inventory')}}">
                                <div class="sidebar-links-outer-div">
                                    <div class="sidebar-link">
                                        <div>
                                            <img src="{{url::asset('images/inventory.svg')}}" />
                                        </div>
                                        <div class="sidebar-link-text">
                                            Inventory
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/users')}}">
                                <div class="sidebar-links-outer-div" id="users_option">
                                    <div class="sidebar-link">
                                        <div>
                                            <img src="{{url::asset('images/user-icon.svg')}}" />

                                        </div>
                                        <div class="sidebar-link-text">
                                            Users

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/vendors')}}">
                                <div class="sidebar-links-outer-div">
                                    <div class="sidebar-link">
                                        <div>
                                            <img src="{{url::asset('images/vendor-icon.svg')}}" />

                                        </div>
                                        <div class="sidebar-link-text">
                                            Dealers
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/faqs')}}">
                                <div class="sidebar-links-outer-div">
                                    <div class="sidebar-link">
                                        <div>
                                            <img src="{{url::asset('images/FAQs.svg')}}" />

                                        </div>
                                        <div class="sidebar-link-text">
                                            FAQs
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/logout')}}">
                                <div class="sidebar-links-outer-div">
                                    <div class="sidebar-link">
                                        <div>
                                            <img src="{{url::asset('images/logout.svg')}}" />
                                        </div>
                                        <div class="sidebar-link-text">
                                            Logout
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="sidebar">
            <button id="sidebar-collapse">
                <img src="{{url::asset('images/HamBurger.svg')}}" />
            </button>
            <a href="{{url('/dashboard')}}">
                <div class="logo-div">
                    <img src="{{url::asset('images/ecodost-logo.svg')}}" />

                </div>
            </a>
            <a href="{{url('/dashboard')}}">
                <div class="sidebar-links-outer-div">
                    <div class="sidebar-link">
                        <div>
                            <img src="{{url::asset('images/dashboard-icon.svg')}}" />
                        </div>
                        <div class="sidebar-link-text">
                            Dashboard
                        </div>
                    </div>
                </div>
            </a>

            <a href="{{url('/complaints')}}">
                <div class="sidebar-links-outer-div">
                    <div class="sidebar-link">
                        <div>
                            <img src="{{url::asset('images/Complaint.svg')}}" />

                        </div>
                        <div class="sidebar-link-text">
                            Complaints
                        </div>
                    </div>
                </div>
            </a>

            <a href="{{url('/inventory')}}">
                <div class="sidebar-links-outer-div">
                    <div class="sidebar-link">
                        <div>
                            <img src="{{url::asset('images/inventory.svg')}}" />
                        </div>
                        <div class="sidebar-link-text">
                            Inventory
                        </div>
                    </div>
                </div>
            </a>

            <a href="{{url('/vehicles')}}">
                <div class="sidebar-links-outer-div">
                    <div class="sidebar-link">
                        <div>
                            <img src="{{url::asset('images/Vehicle_White.svg')}}" />
                        </div>
                        <div class="sidebar-link-text">
                            Vehicles
                        </div>
                    </div>
                </div>
            </a>
            <a href="{{url('/users')}}">
                <div class="sidebar-links-outer-div" id="users_option">
                    <div class="sidebar-link">
                        <div>
                            <img src="{{url::asset('images/user-icon.svg')}}" />

                        </div>
                        <div class="sidebar-link-text">
                            Users

                        </div>
                    </div>
                </div>
            </a>
            <a href="{{url('/vendors')}}">
                <div class="sidebar-links-outer-div">
                    <div class="sidebar-link">
                        <div>
                            <img src="{{url::asset('images/vendor-icon.svg')}}" />

                        </div>
                        <div class="sidebar-link-text">
                            Dealers
                        </div>
                    </div>
                </div>
            </a>
            <a href="{{url('/faqs')}}">
                <div class="sidebar-links-outer-div">
                    <div class="sidebar-link">
                        <div>
                            <img src="{{url::asset('images/FAQs.svg')}}" />

                        </div>
                        <div class="sidebar-link-text">
                            FAQs
                        </div>
                    </div>
                </div>
            </a>
            <a href="{{url('/logout')}}">
                <div class="sidebar-links-outer-div">
                    <div class="sidebar-link">
                        <div>
                            <img src="{{url::asset('images/logout.svg')}}" />
                        </div>
                        <div class="sidebar-link-text">
                            Logout
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="right-div">
            <div class="header-div">
                <div class="header-inner-div">
                    <!-- <div class="dropdown">
                        <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <img src="{{url::asset('images/Doorbell.svg')}}" />

                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                        </li>
                    </div> -->
                    <div class="dropdown">
                        <a class="dropdown-toggle dropdown-name" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">

                            <div class="welcome-name"> Welcome {{ request()->session()->get('user_name') }} </div>
                        </a>
                       
                    </div>
                </div>
            </div>
            <div class="content-div">
                @yield('content')
            </div>
        </div>
    </div>

    <!--  Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>




</body>


</html>