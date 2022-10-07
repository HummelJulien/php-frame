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
            if (preg_match('{@extend\(([a-z]+),([a-z-A-Z\/\.]+)\)@}',$template[0], $file_directory)) {
                $template[0] = preg_replace('{@extend\(([a-z]+),([a-z-A-Z\/\.]+)\)@}', '', $template[0]);
                $template[0] = str_replace('{}','', $template[0]);
                $template = $this->extend($file_directory, $template);
            }
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

    protected function extend(array $file_directory, $template)
    {
        $extendable = file($_SERVER['DOCUMENT_ROOT'].'/../'.$file_directory[2]);
        if (is_array($extendable)) {
            $slot_regex_name = '{@slot('.$file_directory[1].')@}';
            foreach ($extendable as $key => $value) {
                if (preg_match('{@extend\('.$file_directory[1].'\)@}',$extendable[$key], $matched)) {
                    $extendable[$key] = $this->extend($matched, $extendable);
                }
                if (preg_match('{@slot\('.$file_directory[1].'\)@}',$extendable[$key], $matched)) {
                    $extendable[$key] = str_replace($slot_regex_name, implode(' ',$template), $extendable[$key]);
                }
            }
        }
        return $extendable;
        exit();
    }
}
