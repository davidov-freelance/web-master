Click here to reset your password: <a href="{{ $link = url('backend/reset-password', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"> {{ $link }} </a>