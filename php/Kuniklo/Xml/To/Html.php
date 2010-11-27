<?php
class Kuniklo_Xml_To_Html {
    /**
     * @param string $xml
     * @return string
     * @todo This is quick and dirty approach. It must be refactored.
     */
    public function toHtml($xml) {
        $html = $xml;

        $html = str_replace('<document>', '<div class="kuniklo-markup">', $html);
        $html = str_replace('</document>', '</div>', $html);

        $html = str_replace('<paragraph>', '<p>', $html);
        $html = str_replace('</paragraph>', '</p>', $html);

        $html = str_replace('<emphasis>', '<em>', $html);
        $html = str_replace('</emphasis>', '</em>', $html);

        // strong remains intact

        $html = str_replace('<link ', '<a ', $html);
        $html = str_replace('</link>', '</a>', $html);

        $html = preg_replace('!<picture href=([^>]+)>(.+)</picture>!', '<p class="picture"><img src=$1 alt="$2" /></p>', $html);

        $html = str_replace('<quotation>', '<blockquote>', $html);
        $html = str_replace('</quotation>', '</blockquote>', $html);

        return $html;
    }
}