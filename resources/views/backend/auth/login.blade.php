@extends('backend.layouts.app-guest')

@section('title', 'Login')

@section('content')
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
                        <form role="form" method="POST">
						   <h1>Login Form</h1>
                        {{ csrf_field() }}
                            <fieldset>
                            @if ( $errors->count() )
                                <div class="alert alert-danger">
                                    {!! implode('<br />', $errors->all()) !!}
                                </div>
                            @endif
                                <div class="form-group">
                                    <input class="form-control" placeholder="E-mail" name="email" type="email" autofocus value="{{ old('email') }}">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="password" type="password" value="">
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="Remember Me" {{ old('remember') ? ' checked="checked"' : '' }}>Remember Me
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-lg btn-success btn-block">Login</button>

                                <div class="text-center magt-10">

                                </div>
                            </fieldset>
                        </form>
                    </section>
					</div>
					</div>
					</div>
					
@endsection