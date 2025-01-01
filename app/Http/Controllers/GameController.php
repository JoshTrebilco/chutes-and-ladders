<?php

namespace App\Http\Controllers;

class GameController extends Controller
{
    private const BOARD_SIZE = 600;

    private const GRID_SIZE = 10;

    private const PADDING = 16;

    private const GAP = 4;

    public function index()
    {
        return view('welcome', [
            'numbers' => $this->generateBoardNumbers(),
            'ladders' => $this->getLadders(),
            'squareSize' => $this->calculateSquareSize(),
        ]);
    }

    private function generateBoardNumbers(): array
    {
        $numbers = [];
        for ($row = 9; $row >= 0; $row--) {
            $start = ($row * 10) + 1;
            $rowNumbers = range($start, $start + 9);
            if ($row % 2 !== 0) {
                $rowNumbers = array_reverse($rowNumbers);
            }
            $numbers = array_merge($numbers, $rowNumbers);
        }

        return $numbers;
    }

    private function getLadders(): array
    {
        return [
            ['start' => 1, 'end' => 38],
            ['start' => 21, 'end' => 42],
            ['start' => 51, 'end' => 67],
            ['start' => 80, 'end' => 100],
        ];
    }

    private function calculateSquareSize(): float
    {
        return (self::BOARD_SIZE - 2 * self::PADDING - ((self::GRID_SIZE - 1) * self::GAP)) / self::GRID_SIZE;
    }

    public function getSquarePosition(int $number): array
    {
        $number -= 1; // Convert to 0-based index
        $row = floor($number / self::GRID_SIZE);
        $col = $number % self::GRID_SIZE;

        // Reverse column number for odd-numbered rows
        if ($row % 2 !== 0) {
            $col = self::GRID_SIZE - 1 - $col;
        }

        $squareSize = $this->calculateSquareSize();

        // Calculate position including gaps
        $x = self::PADDING + ($col * ($squareSize + self::GAP)) + ($squareSize / 2);
        $y = self::PADDING + ((9 - $row) * ($squareSize + self::GAP)) + ($squareSize / 2);

        return [$x, $y];
    }

    public function calculateLadderGeometry(array $ladder): array
    {
        [$startX, $startY] = $this->getSquarePosition($ladder['start']);
        [$endX, $endY] = $this->getSquarePosition($ladder['end']);

        $ladderWidth = 20;
        $angle = atan2($endY - $startY, $endX - $startX);
        $length = sqrt(pow($endX - $startX, 2) + pow($endY - $startY, 2));
        $steps = floor($length / 40);

        // Calculate perpendicular offset for rails
        $perpX = -sin($angle) * ($ladderWidth / 2);
        $perpY = cos($angle) * ($ladderWidth / 2);

        return [
            'startX' => $startX,
            'startY' => $startY,
            'endX' => $endX,
            'endY' => $endY,
            'perpX' => $perpX,
            'perpY' => $perpY,
            'steps' => $steps,
        ];
    }
}
