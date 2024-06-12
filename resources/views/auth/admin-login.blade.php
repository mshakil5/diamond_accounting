<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Main CSS-->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/logincss.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/main.css') }}">
  <!-- Font-icon css-->
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <title>Login - Accounting Soft</title>
</head>

<body>
<section class="backimg">
    <div class="cover"></div>
  </section>
  <section class="login-content">
      <div>
          <h1 style="text-align: center">DIAMONDS GROUP ACCOUNTS MANAGEMENT </h1>
          <h1 style="text-align: center; color: #cd0a0a">WARNING!! </h1>
          <h1 style="text-align: center; color: #cd0a0a">THIS IS A PRIVATE SYSTEM - AUTHORIZED ACCESS ONLY </h1>
          <h5 style="text-align: center; color: #cd0a0a">If you attempt to  connect or access this system and you are not an authorized user you will breach the Computer Misuse Act 1990, which is criminal offence.  </h5>
      </div>
    
    @if (!$errors->isEmpty())
    <div class="danger">
      <span style="color: red">{{ $errors }}</span>
    </div>
  @endif

    <div class="login-box">


  {{-- login form start  --}}
      <form method="POST" action="{{ route('admin.login.submit') }}" class="login-form">

        @csrf
        <h6 style="text-align: center"><i class="fa fa-lg fa-fw fa-user"></i>SIGN IN</h6>
        <hr>

        <div class="form-group">
          <label class="control-label">OFFICE</label>
          <select name="branch_id" id="branch_id" class="form-control">
            <option value="" disabled selected>Please Select Branch</option>
            @foreach($branch as $branches)
                <option value="{{$branches->id}}">{{ $branches->branch_name }}</option>
            @endforeach
        </select>
          <span class="help-block"></span>
        </div>

        <div class="form-group">            
          <label class="control-label">{{ __('E-Mail Address') }}</label>
          <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
          @error('email')
          <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>

        <div class="form-group">
          <label class="control-label">{{ __('Password') }}</label>
          <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

          @error('password')
          <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>


        <div class="form-group">
          <div class="utility">
            <div class="animated-checkbox">
              <label>
                <button type="submit" class="btn btn-primary">
                    {{ __('Login') }}
                </button>
              </label>
            </div>
          </div>
        </div>
      </form>
{{-- login form end  --}}

{{-- forget pass form start  --}}
      <form class="forget-form" action="index.html">
        <h3 class="login-head"><i class="fa fa-lg fa-fw fa-lock"></i></h3>
        <div class="form-group">
          <label class="control-label">EMAIL</label>
          <input class="form-control" type="text" placeholder="Email">
        </div>
        <div class="form-group btn-container">
          <button class="btn btn-primary btn-block"><i class="fa fa-unlock fa-lg fa-fw"></i>RESET</button>
        </div>
        <div class="form-group mt-3">
          <p class="semibold-text mb-0"><a href="#" data-toggle="flip"><i class="fa fa-angle-left fa-fw"></i> Back to Login</a></p>
        </div>
      </form>
{{-- forget pass form end  --}}

    </div>
  </section>
  <!-- Essential javascripts for application to work-->
  <script src="{{ asset('assets/js/jquery-3.3.1.min.js') }}"></script>
  <script src="{{ asset('assets/js/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <!-- The javascript plugin to display page loading on top-->
  <script src="{{ asset('js/plugins/pace.min.js') }}"></script>
  <script type="text/javascript">
    // Login Page Flipbox control
    $('.login-content [data-toggle="flip"]').click(function() {
      $('.login-box').toggleClass('flipped');
      return false;
    });
  </script>
</body>
</html>