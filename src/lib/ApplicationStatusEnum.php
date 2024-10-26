<?php

namespace App\lib;

enum ApplicationStatusEnum: int {
    case Pending = 1;
    case Invited = 2;
    case Approved = 3;
    case Rejected = 4;
}