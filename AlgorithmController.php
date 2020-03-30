<?php

namespace GameOfLife;


/**
 * Class AlgorithmController
 * Inspired by Daniel Pepin's conway-game-of-life-php
 * @see https://github.com/donutdan4114/conway-game-of-life-php
 *
 * @package GameOfLife
 */
class AlgorithmController
{

    /** @var int */
    private $width = 100;

    /** @var int */
    private $height = 100;

    /** @var int */
    private $numberOfSpecies = 5;

    /** @var int */
    private $numberOfIterations = 4000000;

    /** @var int */
    private $initRandDensity = 1;

    /** @var array */
    public $cells = [];

    /**
     * @param int|null $width
     * @param int|null $height
     * @param int|null $numberOfSpecies
     * @param int|null $numberOfIterations
     * @param int|null $initRandDensity
     */
    public function __construct(
        ?int $width,
        ?int $height,
        ?int $numberOfSpecies,
        ?int $numberOfIterations,
        ?int $initRandDensity
    ) {
        $this->width = $width ?: $this->width;
        $this->height = $height ?: $this->height;
        $this->numberOfSpecies = $numberOfSpecies ?: $this->numberOfSpecies;
        $this->numberOfIterations = $numberOfIterations ?: $this->numberOfIterations;
        $this->initRandDensity = $initRandDensity ?: $this->initRandDensity;
    }

    public function render(): void
    {
        $this->loop();

        $output = new Output(
            $this->width * $this->height,
            $this->numberOfSpecies,
            $this->numberOfIterations,
            $this->cells
        );

        header('Content-Type: application/xml; charset=utf-8');
        echo $xml = $output->getXml();
    }

    public function loop(): void
    {
        $this->initCells();

        for ($i = 0; $i < $this->numberOfIterations; $i++) {
            $this->newGeneration();
        }
    }

    private function initCells(): void
    {
        for ($x = 0; $x < $this->width; $x++) {
            for ($y = 0; $y < $this->height; $y++) {
                $this->cells[$x][$y] = rand(0, $this->initRandDensity) === 0 ? 0 : rand(1, $this->numberOfSpecies);
            }
        }
    }

    private function newGeneration(): void
    {
        $killQueue = $bornQueue = [];

        for ($x = 0; $x < $this->width; $x++) {
            for ($y = 0; $y < $this->height; $y++) {
                $neighbourCounts = $this->getAliveNeighbourCounts($x, $y); // cell activity is determined by the neighbours
                $species = $this->cells[$x][$y];
                $hasOwnSpeciesNeighbours = array_key_exists($species, $neighbourCounts);

                if ($species > 0 && (!$hasOwnSpeciesNeighbours || $neighbourCounts[$species] < 2 || $neighbourCounts[$species] > 3)) {
                    $killQueue[] = [$x, $y];
                }

                if ($species === 0 && $hasOwnSpeciesNeighbours && $neighbourCounts[$species] === 3) {
                    $bornQueue[] = [$x, $y, $species]; // saving information about species
                }
            }
        }

        foreach ($killQueue as $coordinates) {
            $this->cells[$coordinates[0]][$coordinates[1]] = 0;
        }

        foreach ($bornQueue as $coordinates) {
            $this->cells[$coordinates[0]][$coordinates[1]] = $coordinates[2]; // value of species type
        }
    }

    /**
     * @param int $x
     * @param int $y
     *
     * @return int[]
     */
    private function getAliveNeighbourCounts($x, $y): array
    {
        $aliveCounts = [];
        for ($i = 1; $i <= $this->numberOfSpecies; $i++) {
            $aliveCounts[$i] = 0;
        }

        for ($x1 = $x - 1; $x1 <= $x + 1; $x1++) {
            if ($x1 < 0 || $x1 >= $this->width) {
                continue; // out of range
            }

            for ($y1 = $y - 1; $y1 <= $y + 1; $y1++) {
                if ($y1 < 0 || $y1 >= $this->height) {
                    continue; // out of range
                }

                if ($x1 === $x && $y1 === $y) {
                    continue; // current cell spot
                }

                if ($this->cells[$x1][$y1] !== 0) {
                    $aliveCounts[$this->cells[$x1][$y1]] += 1;
                }
            }
        }

        return $aliveCounts;
    }
}