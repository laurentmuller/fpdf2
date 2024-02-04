<?php
/*
 *     FPDF
 *     Version: 1.86
 *     Date:    2023-06-25
 *     Author:  Olivier PLATHEY
 */

declare(strict_types=1);
require '../fpdf.php';

class tuto3 extends FPDF
{
    public function ChapterBody($file): void
    {
        // Read text file
        $txt = \file_get_contents($file);
        // Times 12
        $this->SetFont('Times', '', 12);
        // Output justified text
        $this->multiCell(0, 5, $txt);
        // Line break
        $this->lineFeed();
        // Mention in italics
        $this->SetFont('', 'I');
        $this->cell(0, 5, '(end of excerpt)');
    }

    public function ChapterTitle($num, $label): void
    {
        // Arial 12
        $this->SetFont('Arial', '', 12);
        // Background color
        $this->SetFillColor(200, 220, 255);
        // Title
        $this->cell(0, 6, "Chapter $num : $label", 0, 1, 'L', true);
        // Line break
        $this->lineFeed(4);
    }

    public function footer(): void
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Text color in gray
        $this->SetTextColor(128);
        // Page number
        $this->cell(0, 10, 'Page ' . $this->getPage(), 0, 0, 'C');
    }

    public function header(): void
    {
        global $title;

        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Calculate width of title and position
        $w = $this->getStringWidth($title) + 6;
        $this->SetX((210 - $w) / 2);
        // Colors of frame, background and text
        $this->SetDrawColor(0, 80, 180);
        $this->SetFillColor(230, 230, 0);
        $this->SetTextColor(220, 50, 50);
        // Thickness of frame (1 mm)
        $this->SetLineWidth(1);
        // Title
        $this->cell($w, 9, $title, 1, 1, 'C', true);
        // Line break
        $this->lineFeed(10);
    }

    public function PrintChapter($num, $title, $file): void
    {
        $this->addPage();
        $this->ChapterTitle($num, $title);
        $this->ChapterBody($file);
    }
}

$pdf = new PDF();
$title = '20000 Leagues Under the Seas';
$pdf->SetTitle($title);
$pdf->SetAuthor('Jules Verne');
$pdf->PrintChapter(1, 'A RUNAWAY REEF', '20k_c1.txt');
$pdf->PrintChapter(2, 'THE PROS AND CONS', '20k_c2.txt');
$pdf->Output();
