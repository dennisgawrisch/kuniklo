<?php
class Kuniklo_Markup {
    protected function __construct() {
    }

    final private function __clone() {}

    final public function __wakeup() {
        throw new Exception(get_class($this) . " is a singleton and thus cannot be deserialized");
    }

    private static $instance;

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return string
     */
    public function toXml($string) {
        $string = trim($string);

        // normalize line breaks
        $string = str_replace("\r\n", "\n", $string);
        $string = str_replace("\r", "\n", $string);

        // replace tabulations with 8 spaces
        $string = str_replace("\t", "        ", $string);

        $xml = "<document>";

        foreach (explode("\n\n", $string) as $text) {
            if (empty($text)) {
                continue;
            }

            $text = htmlspecialchars($text, ENT_QUOTES, "UTF-8");

            if (preg_match("/^;(\\S+) \\((.+)\\)$/", $text, $matches)) {
                $xml .= "<picture href='{$matches[1]}'>{$matches[2]}</picture>";
                continue;
            }

            $text = preg_replace("!^(&quot;){3}\n!", "<quotation>", $text);
            $text = preg_replace("!\n(&quot;){3}$!", "</quotation>", $text);

            $text = preg_replace("!\\*\\*(.+)\\*\\*!U", "<strong>$1</strong>", $text);
            $text = preg_replace("!\\*(.+)\\*!U", "<emphasis>$1</emphasis>", $text);

            $text = preg_replace("!%([^%]+)%(([a-z]+:|/)\\S+)%!", "<link href='$2'>$1</link>", $text);

            $xml .= "<paragraph>$text</paragraph>";
        }

        $xml = str_replace("<paragraph><quotation>", "<quotation><paragraph>", $xml);
        $xml = str_replace("</quotation></paragraph>", "</paragraph></quotation>", $xml);

        $xml .= "</document>";
        return $xml;
    }
}