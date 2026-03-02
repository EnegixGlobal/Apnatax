<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Simple wrapper around Dompdf for generating PDFs.
 *
 * Requirements:
 * - Install dompdf via Composer: composer require dompdf/dompdf
 * - Ensure Composer autoload is enabled in application/config/config.php
 *   $config['composer_autoload'] = FCPATH . 'vendor/autoload.php';
 */
class Pdf
{
    /**
     * Generate and stream a PDF from HTML.
     *
     * @param string $html
     * @param string $filename Without .pdf extension
     * @param string $paper
     * @param string $orientation
     */
    public function generate($html, $filename = 'document', $paper = 'A4', $orientation = 'portrait')
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        $dompdf->stream($filename . '.pdf', ['Attachment' => 1]);
    }

    /**
     * Generate a PDF and return its binary content (for API download endpoints).
     *
     * @param string $html
     * @param string $paper
     * @param string $orientation
     * @return string Raw PDF binary
     */
    public function generateBinary($html, $paper = 'A4', $orientation = 'portrait')
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        return $dompdf->output();
    }
}
