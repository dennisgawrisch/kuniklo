<?php
class Kuniklo_Xml_Normalizer {
    private $parser;
    private $resulting_xml = "";

    public function __construct($xml) {
        $this->parser = xml_parser_create();

        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, "handleTagOpen", "handleTagClose");
        xml_set_character_data_handler($this->parser, "handleCharacterData");

        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, FALSE);
        xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, "UTF-8");

        xml_parse($this->parser, $xml, TRUE);

        xml_parser_free($this->parser);
    }

    public function __toString() {
        return $this->resulting_xml;
    }

    private function handleTagOpen($parser, $name, $attributes) {
        $this->resulting_xml .= "<$name>";
    }

    private function handleTagClose($parser, $name) {
        $this->resulting_xml .= "</$name>";
    }

    private function handleCharacterData($parser, $cdata) {
        $this->resulting_xml .= trim(htmlspecialchars($cdata));
    }
}