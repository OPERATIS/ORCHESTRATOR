@foreach ($alerts as $alert)
    {{$alert->metric}}
    {{$alert->result}}
    {{$alert->period}}
    {{$alert->created_at}}
    <a href="{{route('chat', ['alert' => $alert->id])}}">More details</a>
    <br>
@endforeach

Connected Apps
<div>
    <a href="https://t.me/{{config('integrations.telegram.botName')}}?start={{base64_encode($user->id)}}">Telegram</a>
    <br>
    <a href="https://wa.me/{{config('integrations.whatsapp.displayPhoneNumber')}}?text={{urlencode('Start to notifications #' . $user->id)}}">WhatsApp</a>
    <br>
    <a href="https://m.me/{{config('integrations.messenger.pageName')}}?text={{urlencode('Start to notifications #' . $user->id)}}">Messenger</a>
    <br>
    <a href="{{route('slackLogin')}}">Slack</a>
</div>
