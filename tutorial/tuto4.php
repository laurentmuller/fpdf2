<?php
/*
 *     FPDF
 *     Version: 1.86
 *     Date:    2023-06-25
 *     Author:  Olivier PLATHEY
 */

declare(strict_types=1);
require '../fpdf.php';

class tuto4 extends FPDF
{
    protected $col = 0; // Current column
    protected $y0;      // Ordinate of column start

    public function acceptPageBreak()
    {
        // Method accepting or not automatic page break
        if ($this->col < 2) {
            // Go to next column
            $this->SetCol($this->col + 1);
            // Set ordinate to top
            $this->SetY($this->y0);

            // Keep on page
            return false;
        }

        // Go back to first column
        $this->SetCol(0);

        // Page break
        return true;
    }

    public function ChapterBody($file): void
    {
        // Read text file
        $txt = \file_get_contents($file);
        // Font
        $this->SetFont('Times', '', 12);
        // Output text in a 6 cm width column
        $this->multiCell(60, 5, $txt);
        $this->lineFeed();
        // Mention
        $this->SetFont('', 'I');
        $this->cell(0, 5, '(end of excerpt)');
        // Go back to first column
        $this->SetCol(0);
    }

    public function ChapterTitle($num, $label): void
    {
        // Title
        $this->SetFont('Arial', '', 12);
        $this->SetFillColor(200, 220, 255);
        $this->cell(0, 6, "Chapter $num : $label", 0, 1, 'L', true);
        $this->lineFeed(4);
        // Save ordinate
        $this->y0 = $this->getY();
    }

    public function footer(): void
    {
        // Page footer
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128);
        $this->cell(0, 10, 'Page ' . $this->getPage(), 0, 0, 'C');
    }

    public function header(): void
    {
        // Page header
        global $title;

        $this->SetFont('Arial', 'B', 15);
        $w = $this->getStringWidth($title) + 6;
        $this->SetX((210 - $w) / 2);
        $this->SetDrawColor(0, 80, 180);
        $this->SetFillColor(230, 230, 0);
        $this->SetTextColor(220, 50, 50);
        $this->SetLineWidth(1);
        $this->cell($w, 9, $title, 1, 1, 'C', true);
        $this->lineFeed(10);
        // Save ordinate
        $this->y0 = $this->getY();
    }

    public function PrintChapter($num, $title, $file): void
    {
        // Add chapter
        $this->addPage();
        $this->ChapterTitle($num, $title);
        $this->ChapterBody($file);
    }

    public function SetCol($col): void
    {
        // Set position at a given column
        $this->col = $col;
        $x = 10 + $col * 65;
        $this->SetLeftMargin($x);
        $this->SetX($x);
    }
}

$pdf = new PDF();
$title = '20000 Leagues Under the Seas';
$pdf->SetTitle($title);
$pdf->SetAuthor('Jules Verne');
$pdf->PrintChapter(1, 'A RUNAWAY REEF', '20k_c1.txt');
$pdf->PrintChapter(2, 'THE PROS AND CONS', '20k_c2.txt');
$pdf->Output();
