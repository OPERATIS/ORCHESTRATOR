@extends('layouts.app')
@section('content')
    <div>
        <div class="h-[4.25rem] flex items-center px-7 text-sm text-gray_1 border-b border-black border-opacity-10">
            Profile
        </div>
        <div>

            <form method="POST" action="{{route('profileUpdate')}}" >
                @csrf
                <input name="brand" placeholder="brand" value="{{$user->brand}}">
                <input name="email" placeholder="EMAIL" value="{{$user->email}}">
                <input name="new_email" placeholder="EMAIL">
                <input name="password" placeholder="OLD PASSWORD">
                <input name="new_password" placeholder="NEW PASSWORD">
                <input name="new_password_confirmation" placeholder="NEW PASSWORD">
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
@endsection
