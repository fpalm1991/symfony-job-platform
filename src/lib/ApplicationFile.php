<?php

declare(strict_types=1);

namespace App\lib;

enum ApplicationFile: string {
    case CV = "Curriculum Vitae";
    case Motivation = "Letter of Motivation";
}