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
$pdf->addFont('CevicheOne', '', 'CevicheOne-Regular.php', '.');
$pdf->addPage();
$pdf->SetFont('CevicheOne', '', 45);
$pdf->Write(10, 'Enjoy new fonts with FPDF!');
$pdf->output();
