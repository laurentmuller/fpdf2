<?php
/*
 *     FPDF
 *     Version: 1.86
 *     Date:    2023-06-25
 *     Author:  Olivier PLATHEY
 */

declare(strict_types=1);
require '../fpdf.php';

$pdf = new FPDF();
$pdf->addPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->cell(40, 10, 'Hello World!');
$pdf->output();
