@extends('layouts.master')
@section('title')
Error
@endsection

@section('content')
<div>
    <h2>Something went wrong!</h2>
    <p>{{$error_msg}}</p>
</div>
@endsection
