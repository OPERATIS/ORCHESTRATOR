ID #{{$chat->id}}
<br>
{{$chat->title}}


@if ($systemMessage)
    {{$systemMessage}}
@endif

@foreach ($messages as $message)
    {{$message['role']}}
    <br>
    {{$message['content']}}
    <br>
    {{$message['updated_at']}}
    <br>
@endforeach

<form method="post" action="{{route('chatSendMessage', ['chatId' => $chat->id])}}">
    @csrf
    <textarea name="content"></textarea>
    <button type="submit">Submit</button>
</form>


@foreach($chats as $item)
    {{$item->title}}
    {{$item->id}}
@endforeach
