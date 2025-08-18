<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by COQPIT on 18-July-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace COQPIT\Core\Vendor\Symfony\Component\Translation\Exception;

/**
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
class MissingRequiredOptionException extends IncompleteDsnException
{
    public function __construct(string $option, ?string $dsn = null, ?\Throwable $previous = null)
    {
        $message = sprintf('The option "%s" is required but missing.', $option);

        parent::__construct($message, $dsn, $previous);
    }
}
