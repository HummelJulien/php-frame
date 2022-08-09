<?php

namespace App\Controllers;

use App\Controllers\ControllerInterface;
use App\Controllers\Traits\HasRouteInterface;
use App\Controllers\Traits\HasRouteTrait;
use App\Controllers\Traits\RenderInterface;
use App\Controllers\Traits\RenderTrait;
use App\Models\Interfaces\RouteInterface;
use App\Models\Route;

class BaseController implements ControllerInterface, RenderInterface, HasRouteInterface
{
    use RenderTrait;
    use HasRouteTrait;

    public $data;

    protected function parsePostDataAndSecureIt(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = parsePostDataAndSecureIt($value);
            } else {
                $data[$key] = strip_tags($value);
                $data[$key] = htmlspecialchars($data[$key]);
                $data[$key] = trim($data[$key]);
                $data[$key] = stripslashes($data[$key]);
            }
        }
        return $data;
    }

    public static function show404(string $path = 'src/views/404.php', array $data = []): void
    {
        header('HTTP/1.0 404 Not Found');
        $template = file('../'.$path);
        $pathArray = explode(DIRECTORY_SEPARATOR, $path);
        $offset = count($pathArray) - 1;
        foreach ($template as $key => $value) {
            $template[$key] = str_replace('{{', '<?= ', $template[$key]);
            $template[$key] = str_replace('}}', '; ?>', $template[$key]);
            $template[$key] = str_replace('{%', '<?php ', $template[$key]);
            $template[$key] = str_replace('%}', '; ?>', $template[$key]);
        }

        $myfile = fopen('../cache/'. $pathArray[$offset], "w") or die("Unable to open file!");
        fwrite($myfile, implode('', $template));
        fclose($myfile);

        extract($data);
        require '../cache/'. $pathArray[$offset];
    }

}