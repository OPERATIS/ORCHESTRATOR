@extends('layouts.error')
@section('content')
    <div class="min-h-screen min-h-screen flex justify-center items-center">
        <div class="flex flex-col max-w-[28.75rem]">
            <img class="h-12" src="/img/logo.svg" alt="logo">
            <div class="text-center font-medium w-full mt-8 py-9 px-11 border border-grey bg-white rounded-[1.24rem]">
                <div class="flex justify-center">
                    <x-icon name="circle-wavy-warning" class="w-15 h-15 text-red"/>
                </div>
                <div class="text-2xl text-black_5 mt-5">
                    Ooops! Something went wrong
                </div>
                <div class="text-sm text-gray_3 mt-2">
                    Page not found
                </div>
                <div class="text-sm text-gray_3 mt-2">
                    The page you are looking for might have been removed
                    had its name changed or is temporarily unavailable
                </div>
                <a href="{{route('dashboard')}}"
                   class="btn lg btn_default w-full mt-5">
                    Go to Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection
