<?php

namespace src\Constants;

class ErrorMessage
{
    public const EMAIL_INVALID = 'Email address is invalid ! ';
    public const PASSWORD_INVALID = 'Password must be at least 12 characters long and include at least one lowercase letter, one uppercase letter, one number and one special character.';
    public const PASSWORD_MISMATCH = 'Passwords do not match !';
    public const EMAIL_ALREADYEXISTS = 'Email address already exists !';
    public const TITLE_INVALID = 'Title field is required !';
    public const CONTENT_INVALID = 'Content field is required !';
    public const CATEGORY_INVALID = 'Please select a category !';
    public const STATUS_INVALID = 'Please select a status !';
    public const IMAGEPOST_INVALID = 'Please upload an image !';
    public const TRANSFERT_INVALID = 'There was a problem with the transfer !';
    public const SIZE_INVALID = 'File too large (2 Mo max) !';
    public const EXTENSION_INVALID = 'File extension is not allowed (jpg, jpeg & png only) !';
    public const NAMECATEGORY_INVALID = 'Category name field is required !';
    public const DESCRIPTIONCATEGORY_INVALID = 'Category description field is required !';
    public const UNIQUENAMECATEGORY_INVALID = 'Category name already exists !';
    public const NAMETAG_INVALID = 'Tag name field is required !';
    public const DESCRIPTIONTAG_INVALID = 'Tag description field is required !';
    public const UNIQUENAMETAG_INVALID = 'Tag name already exists !';
}
