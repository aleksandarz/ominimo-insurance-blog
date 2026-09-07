<?php

namespace App\Enums;

enum UserRole: string {
    case ADMIN = 'admin';
    case GUEST = 'guest';
    case USER = 'user';
}
