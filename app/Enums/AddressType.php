<?php

namespace App\Enums;

enum AddressType: string
{
    case HOME = 'HOME';
    case WORK = 'WORK';
    case SCHOOL = 'SCHOOL';
    case OTHER = 'OTHER';
}
