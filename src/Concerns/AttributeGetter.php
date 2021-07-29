<?php

declare(strict_types=1);

namespace Shahrestani\Avatar\Concerns;

trait AttributeGetter
{
    public function getAttribute($key)
    {
        return $this->$key;
    }
}
