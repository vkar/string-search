<?php

namespace stringSearch\engine;

use stringSearch\dto\StringSearchResultDto;

/**
 * Class BoyerMoorSearchEngine
 * @package stringSearch\engine
 */
class BoyerMoorSearchEngine extends AbstractSearchEngine
{

    /**
     * @return StringSearchResultDto
     */
    public function execute(): StringSearchResultDto
    {
        $stringSearchResultDto = new StringSearchResultDto();

        $position = null;
        $text = $this->getPartFileContent();
        while ($text !== null) {
            $position = $this->boyerMooreEngine($text, $this->needle);
            if ($position !== null) {
                break;
            }
            $text = $this->getPartFileContent();
        }

        if ($position === null) {
            return $stringSearchResultDto;
        }

        $stringSearchResultDto->setPosition($this->getCurrentFileOffset() + $position);
        return $stringSearchResultDto;
    }

    /**
     * @param string $text
     * @param string $needle
     * @return int|null
     */
    private function boyerMooreEngine(string $text, string $needle): ?int
    {
        $needleLength = strlen($needle);
        $textLength = strlen($text);
        $charTable = $this->makeCharTable($needle);

        for ($i = $needleLength - 1; $i < $textLength;) {
            $t = $i;
            for ($j = $needleLength - 1; $needle[$j] == $text[$i]; $j--, $i--) {
                if ($j == 0) {
                    return $i;
                }
            }
            $i = $t;
            if (array_key_exists($text[$i], $charTable)) {
                $i = $i + max($charTable[$text[$i]], 1);
            } else {
                $i += $needleLength;
            }
        }
        return null;
    }

    /**
     * @param string $needle
     * @return array
     */
    private function makeCharTable(string $needle): array
    {
        $needleLength = strlen($needle);
        $table = [];
        for ($i = 0; $i < $needleLength; $i++) {
            $table[$needle[$i]] = $needleLength - $i - 1;
        }

        return $table;
    }
}
