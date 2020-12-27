<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CodeEnum extends Enum
{
    const SUCCESS = 1;
    const FAIL = 0;
    const MODEL_NOT_FOUND = 10000;
    const VALIDATION_ERROR = 10001;
}
