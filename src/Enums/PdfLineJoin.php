<?php
/*
 * This file is part of the 'fpdf' package.
 *
 * For the license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author bibi.nu <bibi@bibi.nu>
 */

declare(strict_types=1);

namespace fpdf\Enums;

/**
 * The PDF line join enumeration.
 *
 * The line join style specifies the shape to be used at the corners of paths that are stroked.
 *
 * Join styles are significant only at points where consecutive segments of a path connect at an angle; segments that
 * meet or intersect fortuitously receive no special treatment.
 */
enum PdfLineJoin: int
{
    /**
     * Bevel join.
     *
     * The two segments are finished with the butt caps, and the resulting notch beyond the ends of the segments is
     * filled with a triangle.
     */
    case BEVEL = 2;

    /**
     * Miter join.
     *
     * The outer edges of the strokes for the two segments are extended until they meet at an angle, as in a picture
     * frame. If the segments meet at too sharp an angle (as defined by the miter limit parameter), a bevel join is
     * used instead.
     */
    case MITER = 0;
    /**
     * Round join.
     *
     * An arc of a circle with a diameter equal to the line width is drawn around the point where the two segments
     * meet, connecting the outer edges of the strokes for the two segments. This pies-lice-shaped figure is filled
     * in, producing a rounded corner.
     */
    case ROUND = 1;
}
