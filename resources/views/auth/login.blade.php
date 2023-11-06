@extends('layouts.login')
@section('content')
    <div class="min-h-screen min-h-screen flex justify-center pt-20 pb-14">
        <login-blocks :view_auth="'{{@$view}}'"></login-blocks>
    </div>
@endsection
