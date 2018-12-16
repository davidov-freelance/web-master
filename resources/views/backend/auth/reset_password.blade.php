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
                        <form role="form" method="POST">
                        {{ csrf_field() }}
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
                                    <input class="form-control" placeholder="E-mail" name="email" type="email" autofocus value="{{ old('email') }}">
                                </div>

                                <button type="submit" class="btn btn-lg btn-success btn-block">Reset Password</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection