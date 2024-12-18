<?php

namespace Core\Domain\Factory;

use Core\Domain\Validation\{ValidatorInterface, VideoRakitValidator};

class VideoValidatorFactory
{
    public static function create(): ValidatorInterface
    {
        return new VideoRakitValidator();
    }

}
