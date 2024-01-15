<?php

namespace src\Constants;

class ErrorMessage
{
    public const EMAIL_INVALID = 'The email address is invalid.';
    public const PASSWORD_INVALID = 'The password must be at least 12 characters long and include at least one lowercase letter, one uppercase letter, one number and one special character.';
    public const PASSWORD_MISMATCH = 'Passwords do not match.';
    public const EMAIL_ALREADYEXISTS = 'This email address already exists.';
}
