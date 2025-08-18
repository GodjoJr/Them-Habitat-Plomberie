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

namespace COQPIT\Core\Vendor\Symfony\Component\Translation\Provider;

use COQPIT\Core\Vendor\Symfony\Component\Translation\TranslatorBag;
use COQPIT\Core\Vendor\Symfony\Component\Translation\TranslatorBagInterface;

interface ProviderInterface extends \COQPIT_Core_VendorStringableStringable;

    public function read(array $domains, array $locales): TranslatorBag;

    public function delete(TranslatorBagInterface $translatorBag): void;
}
