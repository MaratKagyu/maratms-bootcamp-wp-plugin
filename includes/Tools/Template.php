<?php

namespace MaratMSBootcampPlugin\Tools;


class Template{

    /**
     * @param string $file
     * @param array $content
     * @return string
     */
    public static function render($file, $content = [])
    {
        if (!is_readable($file)){
            return "Couldn't access the template file: " . $file;
        }

        extract($content, EXTR_SKIP);
        ob_start();
        require($file);
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

}