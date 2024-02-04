<?php
/*
 *     FPDF
 *     Version: 1.86
 *     Date:    2023-06-25
 *     Author:  Olivier PLATHEY
 */

declare(strict_types=1);
require '../fpdf.php';

class tuto6 extends FPDF
{
    protected $B = 0;
    protected $HREF = '';
    protected $I = 0;
    protected $U = 0;

    public function CloseTag($tag): void
    {
        // Closing tag
        if ('B' === $tag || 'I' === $tag || 'U' === $tag) {
            $this->SetStyle($tag, false);
        }
        if ('A' === $tag) {
            $this->HREF = '';
        }
    }

    public function OpenTag($tag, $attr): void
    {
        // Opening tag
        if ('B' === $tag || 'I' === $tag || 'U' === $tag) {
            $this->SetStyle($tag, true);
        }
        if ('A' === $tag) {
            $this->HREF = $attr['HREF'];
        }
        if ('BR' === $tag) {
            $this->lineFeed(5);
        }
    }

    public function PutLink($URL, $txt): void
    {
        // Put a hyperlink
        $this->SetTextColor(0, 0, 255);
        $this->SetStyle('U', true);
        $this->Write(5, $txt, $URL);
        $this->SetStyle('U', false);
        $this->SetTextColor(0);
    }

    public function SetStyle($tag, $enable): void
    {
        // Modify style and select corresponding font
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach (['B', 'I', 'U'] as $s) {
            if ($this->$s > 0) {
                $style .= $s;
            }
        }
        $this->SetFont('', $style);
    }

    public function WriteHTML($html): void
    {
        // HTML parser
        $html = \str_replace("\n", ' ', $html);
        $a = \preg_split('/<(.*)>/U', $html, -1, \PREG_SPLIT_DELIM_CAPTURE);
        foreach ($a as $i => $e) {
            if (0 === $i % 2) {
                // Text
                if ($this->HREF) {
                    $this->PutLink($this->HREF, $e);
                } else {
                    $this->Write(5, $e);
                }
            } else {
                // Tag
                if ('/' === $e[0]) {
                    $this->CloseTag(\strtoupper(\substr($e, 1)));
                } else {
                    // Extract attributes
                    $a2 = \explode(' ', $e);
                    $tag = \strtoupper(\array_shift($a2));
                    $attr = [];
                    foreach ($a2 as $v) {
                        if (\preg_match('/([^=]*)=["\']?([^"\']*)/', $v, $a3)) {
                            $attr[\strtoupper($a3[1])] = $a3[2];
                        }
                    }
                    $this->OpenTag($tag, $attr);
                }
            }
        }
    }
}

$html = 'You can now easily print text mixing different styles: <b>bold</b>, <i>italic</i>,
<u>underlined</u>, or <b><i><u>all at once</u></i></b>!<br><br>You can also insert links on
text, such as <a href="http://www.fpdf.org">www.fpdf.org</a>, or on an image: click on the logo.';

$pdf = new PDF();
// First page
$pdf->AddPage();
$pdf->SetFont('Arial', '', 20);
$pdf->Write(5, "To find out what's new in this tutorial, click ");
$pdf->SetFont('', 'U');
$link = $pdf->AddLink();
$pdf->Write(5, 'here', $link);
$pdf->SetFont('');
// Second page
$pdf->AddPage();
$pdf->SetLink($link);
$pdf->Image('logo.png', 10, 12, 30, 0, '', 'http://www.fpdf.org');
$pdf->SetLeftMargin(45);
$pdf->SetFontSize(14);
$pdf->WriteHTML($html);
$pdf->Output();
