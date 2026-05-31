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

namespace JchOptimize\Model;

use JchOptimize\Core\Exception\ExceptionInterface;
use JchOptimize\Core\Mvc\Model;
use JchOptimize\Core\Registry;
use JchOptimize\Core\SystemUri;
use JchOptimize\Core\Uri\UploadedFile;
use Joomla\Filesystem\File;

use function file_exists;
use function tempnam;

use const JPATH_ROOT;
use const JPATH_SITE;

class BulkSettings extends Model
{
    use SaveSettingsTrait;

    public function __construct(Registry $params)
    {
        $this->setState($params);

        $this->name = 'bulk_settings';
    }

    /**
     * @throws ExceptionInterface
     */
    public function importSettings(UploadedFile $file): void
    {
        $tmpDir = JPATH_ROOT . '/tmp';
        $fileName = $file->getClientFilename() ?? tempnam($tmpDir, 'jchoptimize_');
        $targetPath = $tmpDir . '/' . $fileName;

        //if file not already at target path move it
        if (!file_exists($targetPath)) {
            $file->moveTo($targetPath);
        }

        $params = (new Registry())->loadFile($targetPath);

        File::delete($targetPath);

        $this->setState($params);
        $this->saveSettings();
    }

    public function exportSettings(): string
    {
        $file = JPATH_SITE . '/tmp/' . SystemUri::currentUri()->getHost() . '_jchoptimize_settings.json';

        $params = $this->state->toString();

        File::write($file, $params);

        return $file;
    }

    /**
     * @throws ExceptionInterface
     */
    public function setDefaultSettings(): void
    {
        $this->setState(new Registry([]));
        $this->saveSettings();
    }
}
