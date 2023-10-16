<div class="flex flex-col px-4 pt-12 pt-5 pb-7 lg:min-h-screen border-r border-black border-opacity-10">
    <div class="flex-1">
        <div class="w-full flex items-center py-1 px-2">
            <img class="h-6 w-6" src="/img/profile_icon.png" alt="logo">
            <div class="text-sm text-black line-clamp-1 mx-2">
                {{Auth::user()->brand_name}}
            </div>
            <div class="ml-auto">
                <div class="relative pr-0 cursor-pointer" v-click-outside-element="closeMenuDropdown">
                    <div class="p-1 pr-0" @click="isOpenMenuDropdown = !isOpenMenuDropdown">
                        <x-icon name="dots-icon" class="w-4 h-4"/>
                    </div>
                    <ul
                        v-if="isOpenMenuDropdown"
                        class="absolute mt-3 py-2 px-3 bg-white whitespace-nowrap z-10"
                        style="box-shadow: 0 4px 14px 0 rgba(0, 0, 0, 0.15); border-radius: 10px; transform: translateX(-30%);"

                    >
                        <li class="flex items-center text-sm text-dark pb-1.5" style="line-height: 18px;">
                            <x-icon name="user-icon" class="w-5 h-5 text-black mr-1"/>
                            <a href="{{ route('profile') }}">Profile Settings</a>
                        </li>
                        <li class="flex items-center text-sm text-dark pt-1.5 border-t border-dark border-opacity-10" style="line-height: 18px;">
                            <x-icon name="logout-icon" class="w-5 h-5 text-black mr-1"/>
                            <a href="{{ route('logout') }}">Log Out</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center h-7 text-sm text-black text-opacity-40 px-3 mb-1">
                Pages
            </div>
            <div class="space-y-1">
                <a href="{{route('dashboard')}}"
                   class="menu-item
                            hover:bg-black hover:bg-opacity-5
                            @if(request()->routeIs('dashboard')) bg-black bg-opacity-5 active @endif"
                >
                    <x-icon name="arrow-right-icon" class="icon-arrow w-4 h-4"/>
                    <x-icon name="chart-pie-icon" class="w-4 h-4 mx-1"/>
                    Dashboard
                </a>
                <a
{{--                    href="{{route('chats')}}"--}}
                    href="https://app.slack.com/client/T055NGS6N3G/D058FV1BXHB"
                    target="_blank"
                   class="menu-item @if(request()->routeIs('chats')) active @endif"
                >
                    <x-icon name="arrow-right-icon" class="icon-arrow w-4 h-4"/>
                    <x-icon name="chats-circle-icon" class="text-black w-4 h-4 mx-1"/>
                    Chat
                </a>
                <a href="{{route('alerts')}}"
                   class="menu-item @if(request()->routeIs('alerts')) active @endif"
                >
                    <x-icon name="arrow-right-icon" class="icon-arrow w-4 h-4"/>
                    <x-icon name="bell-ringing-icon" class="w-4 h-4 mx-1"/>
                    Alerts
                </a>
                <a href="{{route('integrations')}}"
                   class="menu-item @if(request()->routeIs('integrations')) active @endif"
                >
                    <x-icon name="arrow-right-icon" class="icon-arrow w-4 h-4"/>
                    <x-icon name="book-open-icon" class="w-4 h-4 mx-1"/>
                    Integrations
                </a>
            </div>
        </div>
    </div>
    <div class="mt-auto">
        <a href="{{route('dashboard')}}">
            <img class="h-9" src="/img/logo.svg" alt="logo">
        </a>
    </div>
{{--    <div class="menu_mob">--}}
{{--        <button--}}
{{--            class="menu-toggle"--}}
{{--            data-menuid="main-menu-mobile-toogle"--}}
{{--            aria-label="Open site navigation"--}}
{{--            :aria-label="menu_open ? 'Close site navigation' : 'Open site navigation'"--}}
{{--            @click="menu_open = !menu_open;"--}}
{{--        >--}}
{{--            <span x-show="!menu_open">--}}
{{--                <x-icon class="w-5 h-5" name="menu-line"/>--}}
{{--            </span>--}}
{{--            <span x-show="menu_open" x-cloak class="x-cloak-hidden">--}}
{{--                <x-icon class="w-5 h-5" name="close"/>--}}
{{--            </span>--}}
{{--        </button>--}}
{{--        <a href="{{ route('landing.index') }}" class="mx-auto">--}}
{{--            <img class="h-8" src="/img/logos/dohuya-text-beta.svg" alt="dohuya logo">--}}
{{--        </a>--}}
{{--    </div>--}}
{{--    <div x-show="menu_open" class="relative flex flex-col z-20 bg-body md-max:px-3 md-max:pb-6 md-max:w-[19rem] lg:!block md-max:h-screen md-max:overflow-y-auto">--}}
{{--        <div class="items-center flex-shrink-0 md-max:flex md-max:h-14 md-max:bg-white md-max:-mx-3">--}}
{{--            <a href="{{ route('landing.index') }}" class="md-max:mx-auto">--}}
{{--                <img class="md:w-43 md-max:h-8" src="/img/logos/dohuya-text-beta.svg" alt="dohuya logo">--}}
{{--            </a>--}}
{{--            <div @click="menu_open = !menu_open;" class="absolute flex items-center justify-center w-8 h-8 cursor-pointer lg:hidden right-3">--}}
{{--                <x-icon name="close" class="w-5 h-5 text-black"/>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <ul class="mt-6 nav">--}}
{{--            <li>--}}
{{--                <a class="nav_item text-green hover:bg-green @if(request()->routeIs('*.channels.index')) !bg-green !text-white @endif" href="{{ route('channels.index') }}">--}}
{{--                    <x-icon name="channels" class="w-6 h-6 mr-3"/>--}}
{{--                    <span class="nav_item__text">{{ __('menu.channels') }}</span>--}}
{{--                </a>--}}
{{--            </li>--}}
{{--            <li>--}}
{{--                <a class="nav_item text-violet hover:bg-violet @if(request()->routeIs('*.games.index')) !bg-violet !text-white @endif" href="{{ route('games.index') }}">--}}
{{--                    <x-icon name="games-fill" class="w-6 h-6 mr-3"/>--}}
{{--                    <span class="nav_item__text">{{ __('menu.games') }}</span>--}}
{{--                </a>--}}
{{--            </li>--}}
{{--            <li>--}}
{{--                <a class="nav_item text-red hover:bg-red @if(request()->routeIs('*.channels.live-streams')) !bg-red !text-white @endif" href="{{ route('channels.live-streams') }}">--}}
{{--                    <x-icon name="live" class="w-6 h-6 mr-3"/>--}}
{{--                    <span class="nav_item__text">{{ __('menu.live_streams') }}</span>--}}
{{--                </a>--}}
{{--            </li>--}}
{{--            <li>--}}
{{--                <div class="nav_item disabled">--}}
{{--                    <x-icon name="trophy-fill" class="w-6 h-6 mr-3"/>--}}
{{--                    <span class="nav_item__text">{{ __('menu.esports') }}</span>--}}
{{--                    <span class="ml-auto tracking-wide uppercase text-xxs text-red">{{ __('menu.soon') }}</span>--}}
{{--                </div>--}}
{{--            </li>--}}
{{--            <li>--}}
{{--                <a class="nav_item" href="https://streamscharts.com/" target="_blank" rel="noopener noreferrer nofollow">--}}
{{--                    <x-icon name="twitch-icon" class="w-5 h-5 mr-1"/>--}}
{{--                    <x-icon name="trovo-icon" class="w-5 h-5 mr-1"/>--}}
{{--                    <x-icon name="youtube-icon" class="w-5 h-5 mr-1"/>--}}
{{--                    <x-icon name="kick-fill" class="w-5 h-5 mr-1"/>--}}
{{--                    <span class="!text-black ml-1">{{ __('menu.analytics') }}</span>--}}
{{--                    <x-icon name="arrow-right-up" class="w-4 h-4 ml-auto icon"/>--}}
{{--                </a>--}}
{{--            </li>--}}
{{--        </ul>--}}
{{--        <div class="flex items-center mt-6 md-max:mt-12">--}}
{{--            <div>--}}
{{--                <div class="flex items-center text-xs text-gray-dark">--}}
{{--                    <x-icon name="language-fill" class="w-4 h-4 mr-1"/>--}}
{{--                    {{ __('menu.language') }}--}}
{{--                </div>--}}
{{--                @php--}}
{{--                    $locales = \App\Helpers\GlobalFunctions::getLocales();--}}
{{--                    $activeLocale = \App\Helpers\GlobalFunctions::getLocale();--}}
{{--                @endphp--}}
{{--                <div class="mt-2 rounded-lg lang-switcher">--}}
{{--                    @foreach($locales as $key)--}}
{{--                        <a data-locale="{{ $key }}"--}}
{{--                           @if ($activeLocale === $key)--}}
{{--                           class="lang-switcher_item active"--}}
{{--                           @else--}}
{{--                           class="lang-switcher_item"--}}
{{--                           href="{{ \App\Helpers\GlobalFunctions::changeLanguage($key) }}"--}}
{{--                            @endif--}}
{{--                        >--}}
{{--                            {{$key}}--}}
{{--                        </a>--}}
{{--                    @endforeach--}}
{{--                </div>--}}
{{--            </div>--}}
{{-- temp --}}
{{--            <div class="flex items-center mt-6 ml-auto space-x-4 lg:hidden">--}}
{{--                <a href="https://twitter.com/StreamsCharts" target="_blank" class="social_btn sm">--}}
{{--                    <x-icon name="twitter-fill" class="flex-shrink-0 w-5 h-5"/>--}}
{{--                </a>--}}
{{--                <a href="https://www.instagram.com/streamscharts/" target="_blank" class="social_btn sm">--}}
{{--                    <x-icon name="instagram-fill" class="flex-shrink-0 w-5 h-5"/>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="mt-16 md-max:mt-12">--}}
{{-- temp --}}
{{--            <div class="flex items-center space-x-4 md-max:hidden">--}}
{{--                <a href="https://twitter.com/StreamsCharts" target="_blank" class="social_btn">--}}
{{--                    <x-icon name="twitter-fill" class="flex-shrink-0 w-6 h-6"/>--}}
{{--                </a>--}}
{{--                <a href="https://www.instagram.com/streamscharts/" target="_blank" class="social_btn">--}}
{{--                    <x-icon name="instagram-fill" class="flex-shrink-0 w-6 h-6"/>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--            <a href="mailto:team@streamscharts.com"--}}
{{--               class="flex items-center mt-6 text-sm transition duration-200 text-gray-dark hover:text-default"--}}
{{--            >--}}
{{--                <x-icon name="mail-fill" class="flex-shrink-0 w-6 h-6 mr-2"/>--}}
{{--                team@streamscharts.com--}}
{{--            </a>--}}
{{--            <div class="flex flex-col mt-6 space-y-2 text-sm">--}}
{{--                <a href="{{route('pages.about')}}" class="text-gray-dark hover:text-default @if(request()->routeIs('*.pages.about')) !text-default @endif underline transition duration-200">{{ __('menu.about_dohuya') }}</a>--}}
{{--                <a href="{{route('pages.terms')}}" class="text-gray-dark hover:text-default @if(request()->routeIs('*.pages.terms')) !text-default @endif underline transition duration-200">{{ __('menu.terms') }}</a>--}}
{{--                <a href="{{route('pages.privacy')}}" class="text-gray-dark hover:text-default @if(request()->routeIs('*.pages.privacy')) !text-default @endif underline transition duration-200">{{ __('menu.privacy') }}</a>--}}
{{--            </div>--}}
{{--            <a href="https://streamscharts.com/" target="_blank" class="block my-8 md-max:my-12" rel="noopener noreferrer nofollow">--}}
{{--                <div class="mb-2 text-sm text-gray-dark">{{__('menu.powered_by')}}</div>--}}
{{--                <img src="/img/logos/sc-logo.svg" alt="StreamsCharts logo">--}}
{{--            </a>--}}
{{--            <div class="text-sm text-gray-dark">--}}
{{--                © {{ date('Y') }}, STREAMS CHARTS, SO. <br/>--}}
{{--                {{ __('menu.all_rights_reserved') }}--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <div x-show="menu_open" class="absolute top-0 bottom-0 left-0 right-0 z-10 w-full h-full bg-black lg:hidden bg-opacity-40"></div>--}}
</div>
