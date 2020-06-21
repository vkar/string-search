<?php

namespace stringSearch\engine;

use stringSearch\dto\StringSearchResultDto;

/**
 * Class SimpleSearchEngine
 * @package stringSearch\engine
 */
class SimpleSearchEngine extends AbstractSearchEngine
{

    /**
     * @return StringSearchResultDto
     */
    public function execute(): StringSearchResultDto
    {
        $stringSearchResultDto = new StringSearchResultDto();

        $position = null;
        $text = $this->getPartFileContent();
        $rowNumber = 0;
        $lastEolPosition = 0;
        while ($text !== null) {
            $position = $this->simpleEngine($text, $this->needle);
            $lastEolPosition = $this->getLastEolPosition($text, $position) ?? $lastEolPosition;
            $rowNumber += $this->countRows($text, $position);
            if ($position !== null) {
                break;
            }
            $text = $this->getPartFileContent();
        }

        if ($position === null) {
            return $stringSearchResultDto;
        }

        $fullTextPosition = $this->getCurrentFileOffset() + $position;
        $rowPosition = $fullTextPosition - $lastEolPosition;

        $stringSearchResultDto->setPosition($fullTextPosition);
        $stringSearchResultDto->setRowPosition($rowNumber, $rowPosition);
        return $stringSearchResultDto;
    }

    /**
     * @param string $text
     * @param string $needle
     * @return int|null
     */
    private function simpleEngine(string $text, string $needle): ?int
    {
        $position = strpos($text, $needle);
        if ($position === false) {
            return null;
        }

        return $position;
    }

    /**
     * @param string $text
     * @param int|null $position
     * @return int
     */
    private function countRows(string $text, ?int $position = null): int
    {
        $text = $this->prepareUniqueText($text, $position);
        return substr_count($text, PHP_EOL);
    }

    /**
     * @param string $text
     * @param int|null $position
     * @return int|null
     */
    private function getLastEolPosition(string $text, ?int $position = null): ?int
    {
        $text = $this->prepareUniqueText($text, $position);
        $lastEolPosition = strrpos($text, PHP_EOL);
        if ($lastEolPosition === false) {
            return null;
        }

        return $lastEolPosition + $this->getCurrentFileOffset();
    }

    /**
     * @param string $text
     * @param int|null $position
     * @return string
     */
    private function prepareUniqueText(string $text, ?int $position = null): string
    {
        $text = substr($text, 0, (strlen($text) - strlen($this->needle)));
        if ($position !== null) {
            $text = substr($text, 0, $position);
        }

        return $text;
    }
}
