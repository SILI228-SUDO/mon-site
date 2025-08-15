<?php
class FPDF {
    protected $content = '';
    protected $filename = 'document.pdf';

    public function AddPage() {
        $this->content .= "%PDF-1.4\n";
    }

    public function SetFont($family, $style = '', $size = null) {
        // Ignoré dans cette version simplifiée
    }

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '') {
        $this->content .= "BT /F1 12 Tf 72 720 Td ($txt) Tj ET\n";
    }

    public function Ln($h = null) {
        // Ignoré dans cette version simplifiée
    }

    public function Output($dest = '', $name = '', $isUTF8 = false) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $this->filename . '"');
        echo $this->content . "%%EOF";
    }
}
?>
