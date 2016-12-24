<?
require_once absRoot . '/../htmlpurifier/library/HTMLPurifier.auto.php';

/* Create database connection */
// Set allowed elements and attributes.
$HTML_Allowed_Elms = array('a', 'abbr', 'acronym', 'b', 'blockquote', 'br', 'caption', 'cite', 'code',
                           'dd', 'del', 'dfn', 'div', 'dl', 'dt', 'em', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                           'i', 'img', 'ins', 'kbd', 'li', 'ol', 'p', 'pre', 'span', 'strong', 'sub', 'sup',
                           'table', 'tbody', 'td', 'tfoot', 'th', 'thead', 'tr', 'tt', 'ul', 'var');
$HTML_Allowed_Attr = array('abbr.title', 'acronym.title', 'blockquote.cite', 'div.style', 'h1.style',
                           'h2.style', 'h3.style', 'h4.style', 'h5.style', 'h6.style', 'img.src', 'img.alt',
                           'img.title', 'img.class', 'img.style', 'img.height', 'img.width', 'ol.style',
                           'p.style', 'span.style', 'span.class', 'span.id', 'table.class', 'table.id',
                           'table.border', 'table.cellpadding', 'table.cellspacing', 'table.style',
                           'table.width', 'td.abbr', 'td.align', 'td.class', 'td.id', 'td.colspan',
                           'td.rowspan', 'td.style', 'td.valign', 'tr.align', 'tr.class', 'tr.id',
                           'tr.style', 'tr.valign', 'th.abbr', 'th.align', 'th.class', 'th.id', 'th.colspan',
                           'th.rowspan', 'th.style', 'th.valign', 'ul.style');

// Create the configuration.
$pureconfig = HTMLPurifier_Config::createDefault();
$pureconfig->set('HTML.AllowedElements', $HTML_Allowed_Elms); // sets allowed html elements that can be used.
$pureconfig->set('HTML.AllowedAttributes', $HTML_Allowed_Attr); // sets allowed html attributes that can be used.

// Build the purifier.
$purifier = new HTMLPurifier($pureconfig);
?>