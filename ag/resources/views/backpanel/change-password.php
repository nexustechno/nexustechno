<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" href="img/fevicon.ico" type="image/x-icon">
    <title>BETEXCHANGE - Agent</title>

    <link href="{{ asset('asset/css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/color-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/jquery-ui.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('asset/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/responsive.css') }}" rel="stylesheet">
</head>

<body class="white-bg text-color-black1">
    <div class="chpassword_wrapper" style="background-image: url(img/bg-login.jpg);">
        <div class="chpassword-block yellow-bg">
            <div class="chpasscontent-block">
                <div>
                    <ul>
                        <li>Password must have 8 to 15 alphanumeric without white space</li>
                        <li>Password cannot be the same as username/nickname</li>
                        <li>Must contain at least 1 capital letter, 1 samll letter and 1 number</li>
                        <li>Password must not contain any special characters (!,@,#,etc..)</li>
                    </ul>
                </div>
                <div>
                    <h3> Change Password </h3>
                    <form>
                        <div class="form-group">
                            <input type="password" name="password[]" id="password" placeholder="New Password" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="password" name="password[]" id="password" placeholder="New Password Confirm" class="form-control">
                        </div>
                        <div class="form-group mb-2">
                            <input type="password" name="password[]" id="password" placeholder="Old Password" class="form-control">
                        </div>
                        <button class="login-btn text-color-yellow"> Change </button>
                    </form>
                </div>
            </div>
            <div class="logo_block black-gradient-bg1">
                <img src="{{ URL::to('img/logo2.png')}}" alt="Logo">
            </div>
        </div>
    </div>
    <script src="js/jquery.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>