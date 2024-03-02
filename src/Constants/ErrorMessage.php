<?php

namespace src\Constants;

class ErrorMessage
{
    public const EMAIL_INVALID = 'The email address is invalid ! ';
    public const PASSWORD_INVALID = 'The password must be at least 12 characters long and include at least one lowercase letter, one uppercase letter, one number and one special character.';
    public const PASSWORD_MISMATCH = 'Passwords do not match !';
    public const EMAIL_ALREADYEXISTS = 'This email address already exists !';
    public const TITLE_INVALID = 'The title field is required !';
    public const INTRODUCTION_INVALID = 'The introduction field is required !';
    public const CONTENT_INVALID = 'The content field is required !';
    public const CATEGORY_INVALID = 'Please select a category !';
    public const STATUS_INVALID = 'Please select a status !';
    public const IMAGEPOST_INVALID = 'Please upload an image !';
    public const TRANSFERT_INVALID = 'There was a problem with the transfer !';
    public const SIZE_INVALID = 'File too large (2 Mo max) !';
    public const EXTENSION_INVALID = 'File extension is not allowed (jpg, jpeg & png only) !';

}
