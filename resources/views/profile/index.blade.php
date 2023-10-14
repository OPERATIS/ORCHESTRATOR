@extends('layouts.app')
@section('content')
    <div>
        <div class="h-[4.25rem] flex items-center px-7 text-sm text-gray_1 border-b border-black border-opacity-10">
            Profile settings
        </div>
        <div>
            <profile-page :user-data="{{ json_encode($user) }}"></profile-page>
        </div>
    </div>
@endsection




