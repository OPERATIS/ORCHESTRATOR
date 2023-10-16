@extends('layouts.app')
@section('content')
    <div class="flex flex-col min-h-screen">
        <div class="h-[4.25rem] flex items-center px-7 text-sm text-gray_1 border-b border-black border-opacity-10">
            Chats
        </div>
        <div class="flex flex-col h-full flex-1">
            <chat-page></chat-page>
        </div>

{{--        <div>--}}
{{--            @foreach($chats as $chat)--}}
{{--                {{$chat->title}}--}}
{{--                <a href="{{route('chatShow', ['chatId' => $chat->id])}}">Show</a>--}}
{{--                <br>--}}
{{--            @endforeach--}}
{{--        </div>--}}
    </div>
@endsection
