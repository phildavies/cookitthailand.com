<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/core
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2023 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 *  If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Core\Html\Elements;

/**
 * @method Style media(string $value)
 * @method Style nonce(string $value)
 * @method Style title(string $value)
 * @method string|bool getMedia()
 * @method string|bool getNonce()
 * @method string|bool getTitle()
 */
final class Style extends \JchOptimize\Core\Html\Elements\BaseElement
{
    protected string $name = 'style';
}
