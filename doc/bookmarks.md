# Bookmarks

Since version `2.0.2`, bookmarks can be added within the `PdfDocument`.

**Note:** The code is inspired from this given
[FPDF script](http://www.fpdf.org/en/script/script1.php) created by Olivier.

**Definition:**

```php
addBookmark(
    string $text,
    bool $isUTF8 = false,
    int $level = 0,
    bool $currentY = true,
    bool $createLink = true
)
```

**Parameters:**

- `$text`: The bookmark text.
- `$isUTF8`: Indicates if the text is encoded in ISO-8859-1 (false) or
   UTF-8 (true).
- `$level`: The outline level (0 is the top level, 1 is just below, and so on).
- `$currentY`: Indicate if the ordinate of the outline destination in the
  current page is the current position (true) or the top of the page (false).
- `$createLink`: true to create and add a link at the given ordinate position
  and page

**Usage:**

To use it, create a derived class and use the `PdfBookmarkTrait` trait:

```php
use fpdf\PdfDocument;
use fpdf\Traits\PdfBookmarkTrait;

class PdfBookmarkDocument extends PdfDocument
{
    use PdfBookmarkTrait;
}

// instanciation of inherited class
$pdf = new PdfBookmarkDocument();
$pdf->addPage();
// add root bookmark (level 0) 
$pdf->addBookmark('Root Bookmark');
// add child bookmark
$pdf->addBookmark('Child Bookmark', level: 1);
```

The given trait can also output a new index page with all bookmarks. The index
page contains a centered title ('Index') and for each bookmark:

- The bookmark text on the left.
- A character separator ('.') between the bookmark text and the page number.
- The page number on the right.

**Definition:**

```php
addPageIndex(
    string $title = 'Index',
    PdfFontName|string|null $fontName = null,
    float $titleSize = 9.0,
    float $contentSize = 9.0,
    bool $addBookmark = true,
    string $separator = '.'
)
```

**Parameters:**

- `$title`: The index title. If empty (''), the title is not rendered.
- `$fontName`: The title and content font name.
  It can be a font name enumeration or a name defined by `addFont()`.
  If `null`, the current font name is kept.
- `$titleSize`: The title font size in points.
- `$contentSize`: The content font size in points.
- `$addBookmark`: true to add the index page itself in the list of the
  bookmarks.
- `$separator`: The separator character used between the bookmark text and the
  page number

Call the following function before closing or outputting the document:

```php
$pdf->addPageIndex();
```

After calling this function, the font name, style and size are restored to the
previous values.

**Screenshot:**

![Screenshot](images/bookmarks.png)

**See also:**

- [Examples](examples.md)
- [Home](../README.md)
