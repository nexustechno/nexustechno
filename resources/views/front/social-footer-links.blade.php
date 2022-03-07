<div class="foter-wraper">
    <div class="social-block white-bg1">
        <ul class="nav nav-pills" id="pills-tab" role="tablist" data-mouse="hover">
            <li class="nav-item">
                <a class="nav-link bg-transparent email active" id="pills-email-tab" data-toggle="tab" href="#pillsemail" role="tab" aria-controls="pills-email" aria-selected="true">
                    <img src="{{ URL::to('asset/front/img/login/email.svg') }}" title="Email">
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link bg-transparent whatsapp" id="pills-whatsapp-tab" data-toggle="tab" href="#pillswhatsapp" role="tab" aria-controls="pills-whatsapp" aria-selected="false">
                    <img src="{{ URL::to('asset/front/img/login/whatsapp.svg') }}" title="WhatsApp">
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link bg-transparent telegram" id="pills-telegram-tab" data-toggle="tab" href="#pillstelegram" role="tab" aria-controls="pills-telegram" aria-selected="false">
                    <img src="{{ URL::to('asset/front/img/login/telegram.svg') }} " title="Telegram">
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link bg-transparent skype" id="pills-skype-tab" data-toggle="tab" href="#pillsskype" role="tab" aria-controls="pills-skype" aria-selected="false">
                    <img src="{{ URL::to('asset/front/img/login/skype.svg') }} " title="Skype">
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link bg-transparent instagram" id="pills-instagram-tab" data-toggle="tab" href="#pillsinstagram" role="tab" aria-controls="pills-instagram" aria-selected="false">
                    <img src="{{ URL::to('asset/front/img/login/instagram.svg') }}" title="Instagram">
                </a>
            </li>
        </ul>


        @if(!empty($socialdata))
            <div class="tab-content">
                <div class="tab-pane fade show active" id="pillsemail" role="tabpanel" aria-labelledby="pills-email-tab">
                    <a class="text-color-black" href="mailto:{{$socialdata->em1}}">{{$socialdata->em1}}</a>
                    <a class="text-color-black" href="mailto:{{$socialdata->em2}}">{{$socialdata->em2}}</a>
                    <a class="text-color-black" href="mailto:{{$socialdata->em3}}">{{$socialdata->em3}}</a>
                </div>
                <div class="tab-pane fade" id="pillswhatsapp" role="tabpanel" aria-labelledby="pills-whatsapp-tab">
                    <a class="text-color-black" href="">{{$socialdata->wa1}}</a>
                    <a class="text-color-black" href="">{{$socialdata->wa2}}</a>
                    <a class="text-color-black" href="">{{$socialdata->wa3}}</a>
                </div>
                <div class="tab-pane fade" id="pillstelegram" role="tabpanel" aria-labelledby="pills-telegram-tab">
                    <a class="text-color-black">{{$socialdata->tl1}}</a>
                    <a class="text-color-black">{{$socialdata->tl2}}</a>
                    <a class="text-color-black">{{$socialdata->tl3}}</a>
                </div>
                <div class="tab-pane fade" id="pillsskype" role="tabpanel" aria-labelledby="pills-skype-tab">
                    <a class="text-color-black">{{$socialdata->sk1}}</a>
                    <a class="text-color-black">{{$socialdata->sk2}}</a>
                    <a class="text-color-black">{{$socialdata->sk2}}</a>
                </div>
                <div class="tab-pane fade" id="pillsinstagram" role="tabpanel" aria-labelledby="pills-instagram-tab">
                    <a class="text-color-black" target="_blank">{{$socialdata->ins1}}</a>
                    <a class="text-color-black" target="_blank">{{$socialdata->ins2}}</a>
                    <a class="text-color-black" target="_blank">{{$socialdata->ins3}}</a>
                </div>
            </div>
        @endif
    </div>
    <div class="brand-wrap d-none d-lg-block">
        <h3 class="text-color-rgb1"><span>Powered by</span> <img src="{{ URL::to('asset/front/img/betfair.png') }}"> </h3>
    </div>
    <div class="browser-wraper text-color-rgb2">
        <i class="fab fa-chrome"></i>
        <i class="fab fa-firefox-browser"></i> <br>
        Our website works best in the newest and last prior version of these browsers: <br>
        Google Chrome.
    </div>
    <div class="foter-links">
        <ul>
            <li><a class="text-color-rgb2">Privacy Policy</a></li>
            <li><a class="text-color-rgb2">Terms and Conditions</a></li>
            <li><a class="text-color-rgb2">Rules and Regulations</a></li>
            <li><a class="text-color-rgb2">KYC</a></li>
            <li><a class="text-color-rgb2">Responsible Gaming</a></li>
            <li><a class="text-color-rgb2">About Us</a></li>
            <li><a class="text-color-rgb2">Self-exclusion Policy</a></li>
            <li><a class="text-color-rgb2">Underage Policy</a></li>
        </ul>
    </div>
    <div class="extrab_wrap d-lg-none">
        <div class="brand-wrap">
            <h3 class="text-color-rgb1"><span>Powered by</span> <img src="{{ URL::to('asset/front/img/betfair.png') }}"> </h3>
        </div>
        <div class="app_android">
            <a href="#"><img src="{{ URL::to('asset/front/img/app-android.png') }}" alt=""></a>
            <p>v1.07 - 2020-11-11 - 8.2MB</p>
        </div>
    </div>
</div>
