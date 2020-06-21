<?php

namespace stringSearch\dto;

use stringSearch\exception\WrongConfigFile;

/**
 * Class SearchConfigDto
 * @package stringSearch\dto
 */
class SearchConfigDto
{
    private int $maxFileSize;
    private int $maxNeedleSize;
    private array $allowedMiMeFileTypeList;

    /**
     * SearchConfigDto constructor.
     * @param array $config
     * @throws WrongConfigFile
     */
    public function __construct(array $config)
    {
        $this->checkConfigData($config);

        $this->maxFileSize = (int)($config['MAX_FILE_SIZE'] ?? 0);
        $this->maxNeedleSize = (int)($config['MAX_NEEDLE_SIZE'] ?? 0);
        $this->allowedMiMeFileTypeList = (array)($config['ALLOWED_MIME_FILE_TYPE_LIST'] ?? []);
    }

    /**
     * @return int
     */
    public function getMaxFileSize(): int
    {
        return $this->maxFileSize;
    }

    /**
     * @return array
     */
    public function getAllowedMiMeFileTypeList(): array
    {
        return $this->allowedMiMeFileTypeList;
    }

    /**
     * @return int
     */
    public function getMaxNeedleSize(): int
    {
        return $this->maxNeedleSize;
    }

    /**
     * @param array $config
     * @throws WrongConfigFile
     */
    private function checkConfigData(array $config): void
    {
        if (
            !isset($config['MAX_FILE_SIZE'])
            ||
            !isset($config['MAX_NEEDLE_SIZE'])
            ||
            !isset($config['ALLOWED_MIME_FILE_TYPE_LIST'])
        ) {
            throw new WrongConfigFile();
        }
    }
}
