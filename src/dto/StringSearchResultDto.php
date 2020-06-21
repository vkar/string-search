<?php

namespace stringSearch\dto;

/**
 * Class StringSearchResultDto
 * @package stringSearch\dto
 */
class StringSearchResultDto
{
    const STATUS_NOT_FOUND = 10;
    const STATUS_FOUND_POSITION = 20;
    const STATUS_FOUND_RAW_POSITION = 30;
    const STATUS_FOUND_POSITION_AND_RAW_POSITION = 40;

    private int $status = self::STATUS_NOT_FOUND;
    private ?int $position = null;
    private ?int $row = null;
    private ?int $rowPosition = null;

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
        $this->updateStatus();
    }

    /**
     * @param int $row
     * @param int $rowPosition
     */
    public function setRowPosition(int $row, int $rowPosition): void
    {
        $this->row = $row;
        $this->rowPosition = $rowPosition;
        $this->updateStatus();
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isPositionFound(): bool
    {
        return $this->status === self::STATUS_FOUND_POSITION || $this->status === self::STATUS_FOUND_POSITION_AND_RAW_POSITION;
    }

    /**
     * @return bool
     */
    public function isRowPositionFound(): bool
    {
        return $this->status === self::STATUS_FOUND_RAW_POSITION || $this->status === self::STATUS_FOUND_POSITION_AND_RAW_POSITION;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return int
     */
    public function getRow(): int
    {
        return $this->row;
    }

    /**
     * @return int
     */
    public function getRowPosition(): int
    {
        return $this->rowPosition;
    }

    /**
     *
     */
    private function updateStatus(): void
    {
        if ($this->position !== null && $this->row !== null) {
            $this->status = self::STATUS_FOUND_POSITION_AND_RAW_POSITION;
            return;
        }

        if ($this->position !== null) {
            $this->status = self::STATUS_FOUND_POSITION;
            return;
        }

        if ($this->row !== null) {
            $this->status = self::STATUS_FOUND_RAW_POSITION;
            return;
        }
    }
}
