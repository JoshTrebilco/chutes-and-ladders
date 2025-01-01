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
            'chutes' => $this->getChutes(),
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
            ['start' => 4, 'end' => 14],
            ['start' => 9, 'end' => 31],
            ['start' => 21, 'end' => 42],
            ['start' => 28, 'end' => 84],
            ['start' => 36, 'end' => 44],
            ['start' => 51, 'end' => 67],
            ['start' => 71, 'end' => 91],
            ['start' => 80, 'end' => 100],
        ];
    }

    private function getChutes(): array
    {
        return [
            ['start' => 98, 'end' => 78],
            ['start' => 95, 'end' => 75],
            ['start' => 93, 'end' => 73],
            ['start' => 87, 'end' => 24],
            ['start' => 64, 'end' => 60],
            ['start' => 62, 'end' => 19],
            ['start' => 56, 'end' => 53],
            ['start' => 49, 'end' => 11],
            ['start' => 48, 'end' => 26],
            ['start' => 16, 'end' => 6],
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

    public function calculatePathGeometry(array $path, bool $isChute = false): array
    {
        [$startX, $startY] = $this->getSquarePosition($path['start']);
        [$endX, $endY] = $this->getSquarePosition($path['end']);

        $width = $isChute ? 30 : 20;
        $angle = atan2($endY - $startY, $endX - $startX);
        $length = sqrt(pow($endX - $startX, 2) + pow($endY - $startY, 2));
        $steps = floor($length / 40);

        // Calculate perpendicular offset for rails/chute edges
        $perpX = -sin($angle) * ($width / 2);
        $perpY = cos($angle) * ($width / 2);

        // For chutes, calculate control points for S-curved path
        $controlPoints = [];
        if ($isChute) {
            // Calculate two control points for S-curve
            $offset = $length * 0.4; // Increased offset for more pronounced curve

            // First control point curves right
            $control1X = $startX + ($endX - $startX) * 0.25 + cos($angle + M_PI / 2) * $offset;
            $control1Y = $startY + ($endY - $startY) * 0.25 + sin($angle + M_PI / 2) * $offset;

            // Second control point curves left
            $control2X = $startX + ($endX - $startX) * 0.75 + cos($angle - M_PI / 2) * $offset;
            $control2Y = $startY + ($endY - $startY) * 0.75 + sin($angle - M_PI / 2) * $offset;

            $controlPoints = [
                'c1x' => $control1X,
                'c1y' => $control1Y,
                'c2x' => $control2X,
                'c2y' => $control2Y,
            ];
        }

        return [
            'startX' => $startX,
            'startY' => $startY,
            'endX' => $endX,
            'endY' => $endY,
            'perpX' => $perpX,
            'perpY' => $perpY,
            'steps' => $steps,
            'controlPoints' => $controlPoints,
            'width' => $width,
        ];
    }

    public function calculateLadderGeometry(array $ladder): array
    {
        return $this->calculatePathGeometry($ladder, false);
    }

    public function calculateChuteGeometry(array $chute): array
    {
        return $this->calculatePathGeometry($chute, true);
    }
}
