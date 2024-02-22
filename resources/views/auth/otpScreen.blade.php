@extends('layouts.master-without-nav')
@section('title')
OTP
@endsection
@section('content')
<div class="section-background">
    <div class="form-outer-div">
        <p class="form-heading">Enter OTP</p>
        <p class="form-text">
            Enter the six digit code that was sent to you on +92 333 3333333
        </p>

        <div class="form-div">
            <form>

                <div class="input-outer-div">
                    <div class="otp-container">
                        <input class="otp-input" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric"
                            required>
                        <input class="otp-input" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric"
                            required>
                        <input class="otp-input" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric"
                            required>
                        <input class="otp-input" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric"
                            required>
                        <input class="otp-input" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric"
                            required>
                        <input class="otp-input" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric"
                            required>
                    </div>
                </div>
                <div class="button-div">
                    <button>Verify</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
