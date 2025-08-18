<?php
/**
 * @license MIT
 *
 * Modified by COQPIT on 18-July-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace COQPIT\Core\Vendor\Illuminate\Contracts\Support;

interface MessageProvider
{
    /**
     * Get the messages for the instance.
     *
     * @return \COQPIT\Core\Vendor\Illuminate\Contracts\Support\MessageBag
     */
    public function getMessageBag();
}
