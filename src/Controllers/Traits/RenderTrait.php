<?php

namespace Hummel\PhpFrame\Controllers\Traits;

trait RenderTrait
{
    public function render(string $path, array $data = []): void
    {
        $pathArray = explode(DIRECTORY_SEPARATOR, $path);
        $offset = count($pathArray) - 1;

        if (filemtime($_SERVER['DOCUMENT_ROOT'].'/../cache/'. $pathArray[$offset]) < filemtime($_SERVER['DOCUMENT_ROOT'].'/../'.$path)) {
            $template = file('/'.$path);

            foreach ($template as $key => $value) {
                $template[$key] = str_replace('{{', '<?= ', $template[$key]);
                $template[$key] = str_replace('}}', '; ?>', $template[$key]);
                $template[$key] = str_replace('{%', '<?php ', $template[$key]);
                $template[$key] = str_replace('%}', '; ?>', $template[$key]);
            }

            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/../cache/'. $pathArray[$offset], "w") or die("Unable to open file!");
            fwrite($myfile, implode('', $template));
            fclose($myfile);
        }

        extract($data, EXTR_OVERWRITE);
        require $_SERVER['DOCUMENT_ROOT'].'/../cache/'. $pathArray[$offset];
    }
}