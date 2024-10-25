<?php

namespace App\lib;

enum ApplicationStatusEnum: int {
    case Pending = 1;
    case Archived = 2;
}