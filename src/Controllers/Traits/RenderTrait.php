<?php

namespace App\Controllers\Traits;

trait RenderTrait
{
    public function render(string $path, array $data = []): void
    {
        $pathArray = explode(DIRECTORY_SEPARATOR, $path);
        $offset = count($pathArray) - 1;

        if (filemtime('../cache/'. $pathArray[$offset]) < filemtime('../'.$path)) {
            $template = file('../'.$path);

            foreach ($template as $key => $value) {
                $template[$key] = str_replace('{{', '<?= ', $template[$key]);
                $template[$key] = str_replace('}}', '; ?>', $template[$key]);
                $template[$key] = str_replace('{%', '<?php ', $template[$key]);
                $template[$key] = str_replace('%}', '; ?>', $template[$key]);
            }

            $myfile = fopen('../cache/'. $pathArray[$offset], "w") or die("Unable to open file!");
            fwrite($myfile, implode('', $template));
            fclose($myfile);
        }

        extract($data, EXTR_OVERWRITE);
        require '../cache/'. $pathArray[$offset];
    }
}