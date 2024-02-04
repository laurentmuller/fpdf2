<?php
/*
 *     FPDF
 *     Version: 1.86
 *     Date:    2023-06-25
 *     Author:  Olivier PLATHEY
 */

declare(strict_types=1);
require '../fpdf.php';

class tuto2 extends FPDF
{
    // Page footer
    public function footer(): void
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->cell(0, 10, 'Page ' . $this->getPage() . '/{nb}', 0, 0, 'C');
    }

    // Page header
    public function header(): void
    {
        // Logo
        $this->image('logo.png', 10, 6, 30);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Move to the right
        $this->cell(80);
        // Title
        $this->cell(30, 10, 'Title', 1, 0, 'C');
        // Line break
        $this->lineFeed(20);
    }
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times', '', 12);
for ($i = 1; $i <= 40; ++$i) {
    $pdf->Cell(0, 10, 'Printing line number ' . $i, 0, 1);
}
$pdf->Output();
