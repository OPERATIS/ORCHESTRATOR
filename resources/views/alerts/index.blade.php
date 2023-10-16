@extends('layouts.app')
@section('content')
    <div>
        <div class="h-[4.25rem] flex items-center px-7 text-sm text-gray_1 border-b border-black border-opacity-10">
            Alerts
        </div>
        <div class="max-w-[76.75rem]">
            <div class="px-7 py-6">
                <div class="bg-primary_light p-6 pr-0" style="border-radius: 10px;">
                    <div class="text-sm text-black font-semibold mb-4">
                        Notifications
                    </div>
                    <div class="space-y-2 h-[35.1rem] max-h-[35.1rem] overflow-y-scroll pr-14">
                        @foreach ($alerts as $alert)
                        <div class="w-full flex items-center p-1">
                            <div class="flex items-start mr-4">
                                <div class="flex items-center justify-center w-6 h-6 rounded-md bg-primary_purple mr-2">
                                    <x-icon name="broadcast-icon" class="w-4 h-4"/>
                                </div>
                                <div class="flex flex-col">
                                    <div class="text-sm text-black">
                                        {{$alert->getMessage()}}
                                    </div>
                                    <div class="text-xs text-black text-opacity-40" style="line-height: 18px;">
                                        {{-- Condition for demo --}}
                                        @if ($alert->created_at->day == $alert->end_period->day)
                                            {{$alert->created_at->format('d M, H:i:s')}}
                                        @else
                                            {{$alert->end_period->format('d M, H:i:s')}}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="ml-auto">
                                <a href="{{route('chatsCreate', ['alert' => $alert->id])}}"
                                   class="flex items-center font-extrabold text-sm text-green_2"
                                >
                                    <x-icon name="chats-circle-icon" class="text-green_1 w-5 h-5 mr-3"/>
                                    <span class="underline">More details</span>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="bg-primary_light mt-11 pt-11 pb-6 px-6" style="border-radius: 10px;">
                    <div class="text-2xl font-semibold text-black" style="line-height: 20px;">
                        Connected Apps
                    </div>
                    <div class="text-sm text-black max-w-[34.8rem] py-4">
                        Give your team permission to post Jams directly to your chat or ticketing system. Once connected,
                        each team member must authenticate the app to post on their behalf.
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <a class="flex items-center text-lg text-black font-semibold rounded-lg px-4 h-11" style="background: rgba(217, 228, 239, 0.60);"
                               href="https://t.me/{{config('integrations.telegram.botName')}}?start={{base64_encode($user->id)}}"
                            >
                                <x-icon name="telegram-icon" class="w-7 h-7 mr-2"/>
                                Telegram
                            </a>
                        </div>
                        <div class="flex items-center">
                            <a class="flex items-center text-lg text-black font-semibold rounded-lg px-4 h-11" style="background: #E3FFE4;"
                               href="https://wa.me/{{config('integrations.whatsapp.displayPhoneNumber')}}?text={{urlencode('I want to receive alerts #' . $user->id)}}"
                            >
                                <x-icon name="messenger-icon" class="w-7 h-7 mr-2"/>
                                WhatsApp
                            </a>
                        </div>
                        <div class="flex items-center">
                            <a class="flex items-center text-lg text-black font-semibold rounded-lg px-4 h-11" style="background: rgba(217, 228, 239, 0.60);"
                               href="https://m.me/{{config('integrations.messenger.pageName')}}?text={{urlencode('I want to receive alerts #' . $user->id)}}"
                            >
                                <x-icon name="messenger-icon" class="w-7 h-7 mr-2"/>
                                Messenger
                            </a>
                        </div>
                        <div class="flex items-center">
                            <a class="flex items-center text-lg text-black font-semibold rounded-lg px-4 h-11" style="background: rgba(217, 228, 239, 0.60);"
                               href="{{route('integrationsSlackLogin')}}"
                            >
                                <x-icon name="messenger-icon" class="w-7 h-7 mr-2"/>
                                Slack
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
