<?php

namespace stringSearch\engine;

use stringSearch\dto\SearchConfigDto;
use stringSearch\dto\StringSearchResultDto;
use stringSearch\exception\FilePermissionDeniedException;

/**
 * Class AbstractSearchEngine
 * @package stringSearch\engine
 */
abstract class AbstractSearchEngine
{
    const FILE_OPEN_MODE = 'r';

    protected string $needle;
    protected string $fileName;
    protected SearchConfigDto $searchConfigDto;
    protected int $fileSize;
    private int $fileOffset = 0;
    /** @var resource */
    private $fileHandler;

    /**
     * AbstractSearchEngine constructor.
     * @param string $needle
     * @param string $fileName
     * @param SearchConfigDto $searchConfigDto
     * @throws FilePermissionDeniedException
     */
    public function __construct(string $needle, string $fileName, SearchConfigDto $searchConfigDto)
    {
        $this->needle = $needle;
        $this->fileName = $fileName;
        $this->searchConfigDto = $searchConfigDto;
        $this->setFileHandler();
    }

    /**
     * @return string|null
     */
    protected function getPartFileContent(): ?string
    {
        if ($this->fileOffset > $this->fileSize) {
            return null;
        }

        $content = stream_get_contents(
            $this->fileHandler,
            $this->searchConfigDto->getMaxNeedleSize() + strlen($this->needle),
            $this->fileOffset
        );
        if ($content === false) {
            return null;
        }

        $this->fileOffset += $this->searchConfigDto->getMaxNeedleSize();
        return $content;
    }

    /**
     * @return int
     */
    protected function getCurrentFileOffset(): int
    {
        return $this->fileOffset - $this->searchConfigDto->getMaxNeedleSize();
    }

    /**
     * @throws FilePermissionDeniedException
     */
    private function setFileHandler(): void
    {
        $fileSize = filesize($this->fileName);
        if ($fileSize === false) {
            throw new FilePermissionDeniedException();
        }
        $this->fileSize = $fileSize;

        $fileHandler = fopen($this->fileName, self::FILE_OPEN_MODE);
        if ($fileHandler === false) {
            throw new FilePermissionDeniedException();
        }
        $this->fileHandler = $fileHandler;
    }

    /**
     * @return StringSearchResultDto
     */
    abstract public function execute(): StringSearchResultDto;
}
