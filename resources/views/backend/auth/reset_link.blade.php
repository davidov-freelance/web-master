@extends('backend.layouts.app')

@section('title', 'Login')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Reset your password</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form" method="POST" action="{{ backend_asset('reset-password-finally') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="token" value="{{ $token }}">
                            <fieldset>
                            @if (session('status'))
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif
                            @if ( $errors->count() )
                                <div class="alert alert-danger">
                                    {!! implode('<br />', $errors->all()) !!}
                                </div>
                            @endif
                                <div class="form-group">
                                    <input class="form-control" placeholder="E-mail" name="email" type="email" autofocus value="{{ $email or old('email') }}">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="password" type="password">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Confirm password" name="password_confirmation" type="password">
                                </div>

                                <button type="submit" class="btn btn-lg btn-success btn-block">Reset</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection