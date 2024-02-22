<!DOCTYPE html>
<html>
<head>


    <title>{{ $mailData['complaint']['complaint_no'] }} (Date: {{ $mailData['complaint']['complaint_date'] }})</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container"></div>
    <h1>Complaint Details for {{ $mailData['complaint']['complaint_no']}} </h1>
    <div class="modal-body modal-lg">
        <div class="modal-content-div">
            <div class="mb-4"><strong>ID No. : <span id="complaint_id_span">{{ $mailData['complaint']['id'] }}</span></strong></div>
            <div class="mb-4">
                <strong> Description : <span id="complaint_description">{{ $mailData['complaint']['description'] }}</span> </strong>
            </div>
            @if (count($mailData['attachment'])>=1)

            <div id="img-display" class="mb-4">
                <strong> Images : </strong>
                @foreach($mailData['attachment'] as $attachment)
                <div id ="image-container" class="mt-2 d-flex flex-wrap gap-4">
                   Image: {{ $attachment['url'] }}
                </div>
                @endforeach
            </div>
            @endif
            <div class="d-flex gap-5 mb-4">
                <div><strong>Part Name : </strong> <span id="complaint_part">{{ $mailData['complaint']['part_name'] }}</span></div>
                <div><strong>Make : </strong> <span id="complaint_make">{{ $mailData['complaint']['make'] }}</span></div>
                <div><strong>Model : </strong> <span id="complaint_model">{{ $mailData['complaint']['model'] }}</span></div>
            </div>
            <div class="d-flex gap-5 mb-4">
                <div> <strong> Chasis No.: </strong><span id="complaint_chasis">{{ $mailData['complaint']['chasis_number'] }}</span></div>
                <div> <strong> Battery No.: </strong><span id="complaint_battery">{{ $mailData['complaint']['battery_number'] }}</span></div>
                <div> <strong> Motor No.: </strong><span id="complaint_motor">{{ $mailData['complaint']['motor_number'] }}</span></div>
            </div>
            <div class="d-flex gap-5 mb-4">
                <div> <strong> Priority: </strong>
                    <span id="complaint_priority">{{ $mailData['complaint']['priority_name'] }}</span>
                </div>
                <div> <strong> Dealer Name: </strong>
                    <span id="complaint_dealer_name">{{ $mailData['complaint']['dealer_name'] }}</span>
                </div>
            </div>
            <div class="d-flex gap-5 mb-4">
                <div> <strong> Date Logged: </strong><span id="complaint_date_created">{{ $mailData['complaint']['created_at'] }}</span></div>
                <div> <strong> Date Updated: </strong><span id="complaint_date_updated">{{ $mailData['complaint']['updated_at'] }}</div>
            </div>
            <div class="d-flex gap-5 mb-4">
                <div> <strong> Status: {{ $mailData['complaint']['complaint_status'] }}</strong>

                </div>
                <strong> Comment by Admin : </strong>
                <div class="text-area-div"> <span>{{ $mailData['complaint']['comment_by_admin'] }}</span>

                </div>
            </div>
        </div>
    </div>


    <p>Thank you</p>
</body>
</html>
