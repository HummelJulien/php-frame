<?php



namespace Hummel\PhpFrame\Controllers\Traits;

/**
 * @author Hummel julien
 * @category Hummel\PhpFrame Controller
 */
trait RenderTrait
{
    /**
     * Provide and return view, use template and variable
     *
     * Generate view with the **templating** systeme and caching this
     *
     * @param string $path
     * @param array $data
     *
     * @return void
     */
    public function render(string $path, array $data = []): void
    {
        $pathArray = explode(DIRECTORY_SEPARATOR, $path);
        $offset = count($pathArray) - 1;

        if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/../cache/'. $pathArray[$offset]) ||
            filemtime($_SERVER['DOCUMENT_ROOT'].'/../cache/'. $pathArray[$offset]) < filemtime($_SERVER['DOCUMENT_ROOT'].'/../'.$path)
        ) {
            $template = file($_SERVER['DOCUMENT_ROOT'].'/../'.$path);

            foreach ($template as $key => $value) {
                $template[$key] = str_replace('{{', '<?=', $template[$key]);
                $template[$key] = str_replace('}}', '?>', $template[$key]);
                $template[$key] = str_replace('{%', '<?php', $template[$key]);
                $template[$key] = str_replace('%}', '?>', $template[$key]);
            }

            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/../cache/'. $pathArray[$offset], "w") or die("Unable to open file!");
            fwrite($myfile, implode('', $template));
            fclose($myfile);
        }

        extract($data);
        require $_SERVER['DOCUMENT_ROOT'].'/../cache/'. $pathArray[$offset];
    }
}