<?php

namespace Hummel\PhpFrame\Controllers\Traits;
use Dompdf\Dompdf;
use Dompdf\Options;
/**
 * @author Hummel julien
 * @category Hummel\PhpFrame Controller
 */
trait RenderPDFTrait
{
    /**
     * Provide generate PDF, use template and variable
     *
     * Generate PDF with the package @var Dompdf and send this to final client
     *
     * @param string $path
     * @param array $data
     *
     * @return void
     */
    public function renderPDF(string $path, array $data = []): void
    {
        $pathArray = explode(DIRECTORY_SEPARATOR, $path);
        $offset = count($pathArray) - 1;

        $template = file($_SERVER['DOCUMENT_ROOT'].'/../'.$path);

        foreach ($template as $key => $value) {
            $template[$key] = str_replace('{{', '<?= ', $template[$key]);
            $template[$key] = str_replace('}}', '; ?>', $template[$key]);
            $template[$key] = str_replace('{%', '<?php ', $template[$key]);
            $template[$key] = str_replace('%}', '; ?>', $template[$key]);
        }

        $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/../cache/'. $pathArray[$offset], "w") or die("Unable to open file!");
        fwrite($myfile, implode('', $template));
        fclose($myfile);


        extract($data, EXTR_OVERWRITE);
        ob_start();
        require $_SERVER['DOCUMENT_ROOT'].'/../cache/'. $pathArray[$offset];
        $content = ob_get_clean();
        $conf = new Options();
        $conf->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($conf);
        $conf = $dompdf->getOptions();

        $dompdf->setOptions($conf);
        $dompdf->loadHtml($content);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream();

    }
}