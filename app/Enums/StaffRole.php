<?php

namespace App\Enums;

enum StaffRole: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case MEMBER = 'member';
}
