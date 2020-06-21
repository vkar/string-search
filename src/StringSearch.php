<?php

namespace stringSearch;

use stringSearch\dto\SearchConfigDto;
use stringSearch\dto\StringSearchResultDto;
use stringSearch\engine\AbstractSearchEngine;
use stringSearch\engine\BoyerMoorSearchEngine;
use stringSearch\engine\SimpleSearchEngine;
use stringSearch\exception\ConfigFileNotFoundException;
use stringSearch\exception\FileNotFoundException;
use stringSearch\exception\FileSizeExceededException;
use stringSearch\exception\NeedleSizeExceededException;
use stringSearch\exception\WrongMimeFileTypeException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class StringSearch
 * @package stringSearch
 */
class StringSearch
{
    const ENGINE_BOYER_MOOR = BoyerMoorSearchEngine::class;
    const ENGINE_SIMPLE = SimpleSearchEngine::class;
    const DEFAULT_ENGINE = self::ENGINE_SIMPLE;
    const DEFAULT_CONFIG = [
        'MAX_FILE_SIZE' => 20240000000,
        'MAX_NEEDLE_SIZE' => 1024,
        'ALLOWED_MIME_FILE_TYPE_LIST' => [
            'text/plain'
        ]
    ];

    /** @var SearchConfigDto */
    private SearchConfigDto $searchConfigDto;

    /**
     * StringSearch constructor.
     * @param string|null $configYmlFileUrl
     * @throws ConfigFileNotFoundException
     * @throws exception\WrongConfigFile
     */
    public function __construct(?string $configYmlFileUrl = null)
    {
        $this->searchConfigDto = $this->getSearchConfigDto($configYmlFileUrl);
    }

    /**
     * @param string $needle
     * @param string $fileName
     * @param string $engineClass
     * @return StringSearchResultDto
     * @throws FileNotFoundException
     * @throws FileSizeExceededException
     * @throws NeedleSizeExceededException
     * @throws WrongMimeFileTypeException
     */
    public function search(
        string $needle,
        string $fileName,
        string $engineClass = self::DEFAULT_ENGINE
    ): StringSearchResultDto {
        $this->checkRequirements($needle, $fileName);

        /** @var AbstractSearchEngine $engine */
        $engine = new $engineClass($needle, $fileName, $this->searchConfigDto);
        return $engine->execute();
    }

    /**
     * @param string|null $configYmlFileUrl
     * @return SearchConfigDto
     * @throws ConfigFileNotFoundException
     * @throws exception\WrongConfigFile
     */
    private function getSearchConfigDto(?string $configYmlFileUrl = null): SearchConfigDto
    {
        $config = self::DEFAULT_CONFIG;
        if ($configYmlFileUrl !== null) {
            $configContent = file_get_contents($configYmlFileUrl);
            if ($configContent === false) {
                throw new ConfigFileNotFoundException();
            }

            $config = (array)Yaml::parse($configContent);
        }

        return new SearchConfigDto($config);
    }

    /**
     * @param string $needle
     * @param string $fileName
     * @throws FileNotFoundException
     * @throws FileSizeExceededException
     * @throws NeedleSizeExceededException
     * @throws WrongMimeFileTypeException
     */
    private function checkRequirements(string $needle, string $fileName): void
    {
        if (strlen($needle) > $this->searchConfigDto->getMaxNeedleSize()) {
            throw new NeedleSizeExceededException();
        }

        if (!file_exists($fileName)) {
            throw new FileNotFoundException();
        }

        if (!in_array(mime_content_type($fileName), $this->searchConfigDto->getAllowedMiMeFileTypeList())) {
            throw new WrongMimeFileTypeException();
        }

        if (filesize($fileName) > $this->searchConfigDto->getMaxFileSize()) {
            throw new FileSizeExceededException();
        }
    }
}
