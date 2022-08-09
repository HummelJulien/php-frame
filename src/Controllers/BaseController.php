<?php

namespace Hummel\PhpFrame\Controllers;

use Hummel\PhpFrame\Controllers\ControllerInterface;
use Hummel\PhpFrame\Controllers\Traits\HasRouteInterface;
use Hummel\PhpFrame\Controllers\Traits\HasRouteTrait;
use Hummel\PhpFrame\Controllers\Traits\RenderInterface;
use Hummel\PhpFrame\Controllers\Traits\RenderTrait;
use Hummel\PhpFrame\Models\Interfaces\RouteInterface;
use Hummel\PhpFrame\Models\Route;

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

    public static function show404(string $path = null, array $data = []): void
    {
        if (is_null($path)) {
            $path = $_SERVER['DOCUMENT_ROOT'].'/../vendor/hummel/php-frame/src/views/404.php';
        }
        header('HTTP/1.0 404 Not Found');
        $template = file($path);
        $pathArray = explode(DIRECTORY_SEPARATOR, $path);
        $offset = count($pathArray) - 1;
        foreach ($template as $key => $value) {
            $template[$key] = str_replace('{{', '<?= ', $template[$key]);
            $template[$key] = str_replace('}}', '; ?>', $template[$key]);
            $template[$key] = str_replace('{%', '<?php ', $template[$key]);
            $template[$key] = str_replace('%}', '; ?>', $template[$key]);
        }

        $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/../cache/'. $pathArray[$offset], "w") or die("Unable to open file!");
        fwrite($myfile, implode('', $template));
        fclose($myfile);

        extract($data);
        require $_SERVER['DOCUMENT_ROOT'].'/../cache/'. $pathArray[$offset];
    }

}