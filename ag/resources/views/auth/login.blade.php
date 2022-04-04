<!doctype html>
<?php
//$main_url=explode(".",$_SERVER['HTTP_HOST']);
//use App\Website;
/*if($main_url[0] =='www'){
    $main_url_admin = 'betexchange';
}else{
    $main_url_admin = $main_url[1];
}*/
//$website = Website::where('title',$main_url[1])->first();
$getUser = Auth::user();
?>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" href="{{ asset('asset/front/img')}}/{{$website->favicon}}" type="image/x-icon">
    <title>{{$website->title}}</title>

    <link href="{{ asset('asset/css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/color-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/jquery-ui.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('asset/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/responsive.css') }}" rel="stylesheet">
     <!-- toster script and js -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="{{ asset('asset/css/toastr.min.css') }}" rel="stylesheet">
    <script src="{{ asset('asset/js/toastr.min.js') }}" ></script>
    <!-- Styles -->
    <style type="text/css">
        .toast {
            left: 50% !important;
            position: fixed !important;
            transform: translate(-50%, 0px) !important;
            z-index: 9999 !important;
            }
        .text-color-black.bk-clr{
            color: white !important;
        }

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

    </style>
    <!-- Styles -->
</head>
<body class="white-bg text-color-black1 {{ $website->themeClass }}">
    <div class="login_wrapper" style="background-image: url(/asset/img/bg-login.jpg);">
        <div class="login-block yellow-bg">
            <div class="loginleft-block black-bg mobile-none">
                <img src="{{ URL::to('asset/front/img')}}/{{$website->logo}}" alt="Logo">
            </div>
            <div class="loginleft-block login-header mobile-display" style="background-image: url('{{ asset('asset/front/img') }}/{{$website->login_image}}') ;" >
               <?php /*?> <img src="{{ URL::to('asset/front/img')}}/{{$website->logo}}" alt="Logo"><?php */?>
            </div>
            <?php
            use App\setting;
            $maintanenceMsg = setting::select('maintanence_msg')->latest('id')->first();
            ?>
            <div class="loginright-block">
                @if(!empty($getUser))
                    @if( $maintanenceMsg)
                    <h3>{{$maintanenceMsg->maintanence_msg}}</h3>
                    @endif
                @endif
                @if($errors->any())
                <h4 style="color: red;">{{$errors->first()}}</h4>
                @endif
                <h3> Agent login </h3>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <input id="user_name" placeholder="Enter Your UserName" type="text" class="form-control @error('user_name') is-invalid @enderror" name="user_name" value="{{ old('user_name') }}" autocomplete="email" autofocus>
                        <span class="text-danger cls-error" id="erremail"></span>
                        @error('user_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input id="password" placeholder="Enter Your Password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="current-password">
                        <span class="text-danger cls-error" id="errpass"></span>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="number" name="validationcode" id="validationcode" placeholder="Validation Code" class="form-control" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57">
                        <span class="text-danger cls-error" id="errvalid"></span>
                        <?php
                        $randomNumber = random_int(1000, 9999);
                        ?>
                        <span class="validation-txt text-color-black">{{$randomNumber}}</span>
                    </div>
                    <button class="login-btn text-color-yellow" id="loginbtn">
                        Login<img src="/asset/img/login/logout-yellow.svg"> </button>
                </form>
            </div>
        </div>

        <div class="login-social-block black-bg-rgb">
            <ul class="nav nav-pills" id="pills-tab" role="tablist" data-mouse="hover">
                <li class="nav-item">
                    <a class="nav-link bg-transparent email active" id="pills-email-tab" data-toggle="pill" href="#pills-email" role="tab" aria-controls="pills-email" aria-selected="true">
                        <img src="/asset/img/login/email.svg" title="Email">
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link bg-transparent whatsapp" id="pills-whatsapp-tab" data-toggle="pill" href="#pills-whatsapp" role="tab" aria-controls="pills-whatsapp" aria-selected="false">
                        <img src="/asset/img/login/whatsapp.svg" title="WhatsApp">
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link bg-transparent telegram" id="pills-telegram-tab" data-toggle="pill" href="#pills-telegram" role="tab" aria-controls="pills-telegram" aria-selected="false">
                        <img src="/asset/img/login/telegram.svg" title="Telegram">
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link bg-transparent skype" id="pills-skype-tab" data-toggle="pill" href="#pills-skype" role="tab" aria-controls="pills-skype" aria-selected="false">
                        <img src="/asset/img/login/skype.svg" title="Skype">
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link bg-transparent instagram" id="pills-instagram-tab" data-toggle="pill" href="#pills-instagram" role="tab" aria-controls="pills-instagram" aria-selected="false">
                        <img src="/asset/img/login/instagram.svg" title="Instagram">
                    </a>
                </li>
            </ul>

            <?php
            use App\SocialMedia;
            $socialdata = SocialMedia::first();
            ?>
            @if(!empty($socialdata))
            <div class="tab-content">
                <div class="tab-pane fade show active" id="pills-email" role="tabpanel" aria-labelledby="pills-email-tab">
                    <a class="text-color-black bk-clr" href="mailto:{{$socialdata->em1}}">{{$socialdata->em1}}</a>
                    <a class="text-color-black bk-clr" href="mailto:{{$socialdata->em2}}">{{$socialdata->em2}}</a>
                    <a class="text-color-black bk-clr" href="mailto:{{$socialdata->em3}}">{{$socialdata->em3}}</a>
                </div>
                <div class="tab-pane fade" id="pills-whatsapp" role="tabpanel" aria-labelledby="pills-whatsapp-tab">
                    <a class="text-color-black bk-clr" href="">{{$socialdata->wa1}}</a>
                    <a class="text-color-black bk-clr" href="">{{$socialdata->wa2}}</a>
                    <a class="text-color-black bk-clr" href="">{{$socialdata->wa3}}</a>
                </div>
                <div class="tab-pane fade" id="pills-telegram" role="tabpanel" aria-labelledby="pills-telegram-tab">
                    <a class="text-color-black bk-clr">{{$socialdata->tl1}}</a>
                    <a class="text-color-black bk-clr">{{$socialdata->tl2}}</a>
                    <a class="text-color-black bk-clr">{{$socialdata->tl3}}</a>
                </div>
                <div class="tab-pane fade" id="pills-skype" role="tabpanel" aria-labelledby="pills-skype-tab">
                    <a class="text-color-black bk-clr">{{$socialdata->sk1}}</a>
                    <a class="text-color-black bk-clr">{{$socialdata->sk2}}</a>
                    <a class="text-color-black bk-clr">{{$socialdata->sk2}}</a>
                </div>
                <div class="tab-pane fade" id="pills-instagram" role="tabpanel" aria-labelledby="pills-instagram-tab">
                    <a class="text-color-black bk-clr" target="_blank">{{$socialdata->ins1}}</a>
                    <a class="text-color-black bk-clr" target="_blank">{{$socialdata->ins2}}</a>
                    <a class="text-color-black bk-clr" target="_blank">{{$socialdata->ins3}}</a>
                </div>
            </div>
            @endif
        </div>
    </div>
<script src="{{ asset('asset/js/jquery.js') }}" ></script>
<script src="{{ asset('asset/js/popper.min.js') }}" ></script>
<script src="{{ asset('asset/js/bootstrap.min.js') }}" ></script>
{{--<script src="{{ asset('asset/js/script.js') }}" ></script>--}}

<script type="text/javascript">
$('#loginbtn').click(function () {
    var user_name = $('#user_name').val();
    var password = $('#password').val();
    var randomNumber = '<?php echo $randomNumber; ?>';
    var validationcode = $('#validationcode').val();

    $('#erremail').html('');
    $('#errpass').html('');
    $('#errvalid').html('');

    if(user_name == ''){
        toastr.error('Username can not be blank!');
        return false;
    }
    if(password == ''){
        toastr.error('Password can not be blank!');
        return false;
    }
    if(validationcode != randomNumber){
        toastr.error('Captcha is not valid!');
        return false;
    }

    /*$(this).prop("disabled", true);
      // add spinner to button
      $(this).html(
        `<i class="fa fa-circle-o-notch fa-spin"></i> Loading...`
      );*/

/*setTimeout(function(){

    document.getElementByID('testForm').submit();
},5000)*/
});

@if(Session::has('message'))
toastr.options =
{
    "closeButton" : true,
    "progressBar" : true
}
    toastr.success("{{ session('message') }}");
@endif

@if(Session::has('error'))
toastr.options =
{
"closeButton" : true,
"progressBar" : true
}
    toastr.error("{{ session('error') }}");
@endif

@if(Session::has('info'))
toastr.options =
{
"closeButton" : true,
"progressBar" : true
}
    toastr.info("{{ session('info') }}");
@endif

@if(Session::has('warning'))
toastr.options =
{
"closeButton" : true,
"progressBar" : true
}
    toastr.warning("{{ session('warning') }}");
@endif
</script>
<script src="{{ asset('asset/js/index.js') }}" ></script>

<script type="text/javascript">
    /*
function disableBack() { window.history.forward(); }
setTimeout("disableBack()", 0);
window.onunload = function () { null };

// right click disable
$(document).bind("contextmenu",function(e){

    window.location.replace("https://www.google.com/");
    return false;
});

// disable using keys
$(document).keydown(function(e){
    if(e.which === 123){
       return false;
    }

    if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
        return false;
    }
    if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
        return false;
    }
    if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
        return false;
    }
    if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
        return false;
    }
});

console.log('Is DevTools open:', window.devtools.isOpen);
console.log('DevTools orientation:', window.devtools.orientation);
window.addEventListener('devtoolschange', event => {
    if(event.detail.isOpen){
        $.ajax({
            type: 'POST',
            url: '{{route("autoLogout")}}',
            success: function(data) {
             window.location.replace("https://www.google.com/");
            }
        });
        window.location.replace("https://www.google.com/");
    }
    if(window.devtools.isOpen){
        $.ajax({
            type: 'POST',
            url: '{{route("autoLogout")}}',
            success: function(data) {
                window.location.replace("https://www.google.com/");
            }
        });
        window.location.replace("https://www.google.com/");
    }
    console.log('Is DevTools open1:', event.detail.isOpen);
    console.log('DevTools orientation1:', event.detail.orientation);
});*/
</script>
</body>
</html>
