<?php
/*
 *     FPDF
 *     Version: 1.86
 *     Date:    2023-06-25
 *     Author:  Olivier PLATHEY
 */

declare(strict_types=1);
require '../fpdf.php';

class tuto5 extends FPDF
{
    // Simple table
    public function BasicTable($header, $data): void
    {
        // Header
        foreach ($header as $col) {
            $this->cell(40, 7, $col, 1);
        }
        $this->lineFeed();
        // Data
        foreach ($data as $row) {
            foreach ($row as $col) {
                $this->cell(40, 6, $col, 1);
            }
            $this->lineFeed();
        }
    }

    // Colored table
    public function FancyTable($header, $data): void
    {
        // Colors, line width and bold font
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B');
        // Header
        $w = [40, 35, 40, 45];
        for ($i = 0; $i < \count($header); ++$i) {
            $this->cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        }
        $this->lineFeed();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        foreach ($data as $row) {
            $this->cell($w[0], 6, $row[0], 'LR', 0, 'L', $fill);
            $this->cell($w[1], 6, $row[1], 'LR', 0, 'L', $fill);
            $this->cell($w[2], 6, \number_format($row[2]), 'LR', 0, 'R', $fill);
            $this->cell($w[3], 6, \number_format($row[3]), 'LR', 0, 'R', $fill);
            $this->lineFeed();
            $fill = !$fill;
        }
        // Closing line
        $this->cell(\array_sum($w), 0, '', 'T');
    }

    // Better table
    public function ImprovedTable($header, $data): void
    {
        // Column widths
        $w = [40, 35, 40, 45];
        // Header
        for ($i = 0; $i < \count($header); ++$i) {
            $this->cell($w[$i], 7, $header[$i], 1, 0, 'C');
        }
        $this->lineFeed();
        // Data
        foreach ($data as $row) {
            $this->cell($w[0], 6, $row[0], 'LR');
            $this->cell($w[1], 6, $row[1], 'LR');
            $this->cell($w[2], 6, \number_format($row[2]), 'LR', 0, 'R');
            $this->cell($w[3], 6, \number_format($row[3]), 'LR', 0, 'R');
            $this->lineFeed();
        }
        // Closing line
        $this->cell(\array_sum($w), 0, '', 'T');
    }

    // Load data
    public function LoadData($file)
    {
        // Read file lines
        $lines = \file($file);
        $data = [];
        foreach ($lines as $line) {
            $data[] = \explode(';', \trim($line));
        }

        return $data;
    }
}

$pdf = new PDF();
// Column headings
$header = ['Country', 'Capital', 'Area (sq km)', 'Pop. (thousands)'];
// Data loading
$data = $pdf->LoadData('countries.txt');
$pdf->SetFont('Arial', '', 14);
$pdf->AddPage();
$pdf->BasicTable($header, $data);
$pdf->AddPage();
$pdf->ImprovedTable($header, $data);
$pdf->AddPage();
$pdf->FancyTable($header, $data);
$pdf->Output();
