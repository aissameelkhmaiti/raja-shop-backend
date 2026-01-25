<?php

namespace App\Enums;

enum NotificationType: string
{
    case LOGIN   = 'login';
    case REGISTER  = 'register';
    case UPDATE_PROFILE = 'update_profile';
    case PRODUCT = 'product';
}
