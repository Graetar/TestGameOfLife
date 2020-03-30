<?php

namespace GameOfLife;


class Output
{

    /** @var int */
    private $cellsCount;

    /** @var int */
    private $numberOfSpecies;

    /** @var int */
    private $numberOfIterations;

    /** @var array */
    private $cells;

    /**
     * @param int $cellsCount
     * @param int $numberOfSpecies
     * @param int $numberOfIterations
     * @param array $cells
     */
    public function __construct(int $cellsCount, int $numberOfSpecies, int $numberOfIterations, array $cells)
    {
        $this->cellsCount = $cellsCount;
        $this->numberOfSpecies = $numberOfSpecies;
        $this->numberOfIterations = $numberOfIterations;
        $this->cells = $cells;
    }

    /**
     * @return string
     */
    public function getXml(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF­8"?><life><world>';
        $xml .= '<cells>' . $this->cellsCount . '</cells>';
        $xml .= '<species>' . $this->numberOfSpecies . '</species>';
        $xml .= '<iterations>' . $this->numberOfIterations . '</iterations>';

        $xml .= '<organisms>';
        foreach ($this->cells as $x => $rows) {
            foreach ($rows as $y => $species) {
                $xml .= '<organism>';
                $xml .= '<x_pos>' . $x . '</x_pos>';
                $xml .= '<x_pos>' . $y . '</x_pos>';
                $xml .= '<species>' . $species . '</species>'; // todo What about zero value (no organism)?
                $xml .= '</organism>';
            }
        }
        $xml .= '</organisms>';

        $xml .= '</world></life></xml>';

        return $xml;
    }
}