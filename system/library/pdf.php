<?php if (!defined('DIR_SYSTEM')) exit;

/**
 * PDF Invoice by opencart-templates
 *
 * Settings: library/shared/tcpdf/config
 */
require_once(DIR_SYSTEM . 'library/shared/tcpdf/tcpdf.php');
require_once(DIR_SYSTEM . 'library/shared/tcpdf/include/tcpdf.EasyTable.php');
require_once(DIR_SYSTEM . 'library/shared/tcpdf/include/tcpdf.PDFImage.php');

class pdf {

	public $data = array();
	public $tcpdf;

	public function __construct(Registry $registry) {
		if ($this->tcpdf === null) {
			$this->tcpdf = new Invoice_TCPDF_EasyTable('P', 'mm', 'A4');
		}
		return $this->tcpdf;
	}

	/**
	 * Sets PDF Config
	 *
	 * @param array $data
	 * @return invoicePdf
	 */
	public function Build() {
		$this->tcpdf->SetAuthor('opencart-templates');
		$this->tcpdf->SetCreator('tdpdf');
		$this->tcpdf->SetSubject('Funiclar Pass System');
		$this->tcpdf->SetTitle('Funiclar Pass System');
		//$this->tcpdf->SetKeywords();
		//$this->tcpdf->SetProtection(array('modify', 'copy'), '', null, 1, null);

		if ($this->tcpdf->fontFamily == 'dejavusans') {
			$subset = false;
		} else {
			$subset = 'default';
		}
		$this->tcpdf->AddFont($this->tcpdf->fontFamily, '', $this->tcpdf->fontFamily, $subset);
		$this->tcpdf->AddFont($this->tcpdf->fontFamily, 'B', $this->tcpdf->fontFamily, $subset);

		$this->tcpdf->SetFont($this->tcpdf->fontFamily, '', 9);
		$this->tcpdf->SetTextColor(0, 0, 0);

		$this->tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// remove default header/footer
		$this->tcpdf->setPrintHeader(false);
		$this->tcpdf->setPrintFooter(false);

		// set margins
		$this->tcpdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);

		$this->tcpdf->SetAutoPageBreak(TRUE, 10);

		$this->tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$this->tcpdf->SetFillImageCell(false);

		$this->tcpdf->SetTableHeaderPerPage(true);

		$this->tcpdf->SetLineWidth(0.1);
		$this->tcpdf->SetCellPaddings(1, 1, 1, 1);

		$this->tcpdf->SetCellFillStyle(2);
		$this->tcpdf->SetFillColor(255, 255, 255);
		$this->tcpdf->SetDrawColor(150, 150, 150);

		$this->tcpdf->SetTableRowFillColors(array(array(255, 255, 255)));

		$this->tcpdf->setCellHeightRatio(1.5);

		return $this->tcpdf;
	}

	/**
	 * Main method responsible for drawing the sections onto the page
	 * Group products into page(s)?
	 *
	 * WriteHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
	 * writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
	 *
	 * @return invoicePdf
	 */
	public function Draw()
	{
		$this->tcpdf->AddPage();

		if (!empty($this->data['config']['module_pdf_invoice_header_'])) {
			$html = html_entity_decode($this->data['config']['module_pdf_invoice_header_'], ENT_QUOTES, 'UTF-8');

			// Hack for summernote empty content
			if (trim(strip_tags($html)) != '') {
				$html = str_replace('\'', '"', $html);
				$html = $this->parseShortcodes($html);
				$this->tcpdf->writeHTML($html, true, false, false, '');
			}
		}

		if ($this->data['html']) {
			$html = html_entity_decode($this->data['html'], ENT_QUOTES, 'UTF-8');
			$html = str_replace('\'', '"', $html);
			$this->tcpdf->writeHTML($html, true, false, false, '');
		}

		if (!empty($this->data['config']['module_pdf_invoice_footer_'])) {
			$html = html_entity_decode($this->data['config']['module_pdf_invoice_footer_'], ENT_QUOTES, 'UTF-8');

			// Hack for summernote empty content
			if (trim(strip_tags($html)) != '') {
				$html = str_replace('\'', '"', $html);
				$html = $this->parseShortcodes($html);
				$this->tcpdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'C', true);
			}
		}

		return $this->tcpdf;
	}


	public function parseShortcodes($html)
	{
		$data = $this->data;

		if (isset($data['store'])) {
			foreach ($data['store'] as $key => $var) {
				if (is_string($var) || is_int($var)) {
					if (substr($key, 0, 7) == 'config_') { // Replace config_ with store_
						$key = 'store_' . substr($key, 7);
					}
					$find[] = '{$' . $key . '}';
					$replace[] = $var;
				}
			}
			unset($data['store']);
		}

		foreach ($data as $key => $var) {
			if (is_array($var)) {
				foreach ($var as $key2 => $var2) {
					if (is_string($var2) || is_int($var2)) {
						$find[] = '{$' . $key . '.' . $key2 . '}';
						$replace[] = $var2;
					}
				}
			} elseif (is_string($var) || is_int($var)) {
				$find[] = '{$' . $key . '}';
				$replace[] = $var;
			}
		}

		return str_replace($find, $replace, $html);
	}
}

class Invoice_TCPDF_EasyTable extends TCPDF_EasyTable {

	var $fontFamily = 'helvetica'; // 'dejavusans' for utf8 support

	/**
	 *
	 * @see tFPDF::Header()
	 */
	function Header() {

	}

	/**
	 * @see TCPDF::Footer()
	 */
	function Footer() {
		$this->SetY($this->y);
		$this->SetFont($this->fontFamily, '', 7);
		$w_page = isset($this->l['text_paging']) ? $this->l['text_paging'] : 'Page %s of %s';
		if (empty($this->pagegroups)) {
			$html = sprintf($w_page, $this->getAliasNumPage(), $this->getAliasNbPages());
		} else {
			$html = sprintf($w_page, $this->getPageNumGroupAlias(), $this->getPageGroupAlias());
		}
		$this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'C', true);
	}

	function GetInnerPageWidth() {
		return $this->getPageWidth()-(PDF_MARGIN_LEFT+PDF_MARGIN_RIGHT);
	}

	/**
	 * Overload to allow HEX color
	 * @see TCPDF::SetDrawColor()
	 */
	function SetDrawColor($col1=0, $col2=-1, $col3=-1, $col4=-1, $ret=false, $name='') {
		list($col1, $col2, $col3) = $this->_hex2rbg($col1);
		// if ($col1 && $col1[0] == '#') {
		// 	list($col1, $col2, $col3) = $this->_hex2rbg($col1);
		// }
		return parent::SetDrawColor($col1, $col2, $col3, $col4, $ret, $name);
	}

	/**
	 * Overload to allow HEX color
	 * @see TCPDF::SetTextColor()
	 */
	function SetTextColor($col1=0, $col2=-1, $col3=-1, $col4=-1, $ret=false, $name='') {
		if ($col1 && $col1[0] == '#') {
			list($col1, $col2, $col3) = $this->_hex2rbg($col1);
		}
		return parent::SetTextColor($col1, $col2, $col3, $col4, $ret, $name);
	}

	/**
	 * Overload to allow HEX color
	 * @see FPDF::SetFillColor()
	 */
	function SetFillColor($col1=0, $col2=-1, $col3=-1, $col4=-1, $ret=false, $name='') {
		list($col1, $col2, $col3) = $this->_hex2rbg($col1);
		// if ($col1 && $col1[0] == '#') {
		// 	list($col1, $col2, $col3) = $this->_hex2rbg($col1);
		// }
		return parent::SetFillColor($col1, $col2, $col3, $col4, $ret, $name);
	}

	/**
	 * Overload to allow HEX color
	 * @see TCPDF_EasyTable::SetHeaderCellsFillColor()
	 */
	function SetHeaderCellsFillColor($R, $G=-1, $B=-1) {
		if ($R && $R[0] == '#') {
			list($R, $G, $B) = $this->_hex2rbg($R);
		}
		return parent::SetHeaderCellsFillColor($R, $G, $B);
	}

	/**
	 * Overload to allow HEX color
	 * @see TCPDF_EasyTable::SetCellFontColor()
	 */
	function SetCellFontColor($R, $G=-1, $B=-1) {
		if ($R && $R[0] == '#') {
			list($R, $G, $B) = $this->_hex2rbg($R);
		}
		return parent::SetCellFontColor($R, $G, $B);
	}

	# HEX to RGB
	function _hex2rbg($hex) {
		$hex = substr($hex, 1);
		if (strlen($hex) == 6) {
			list($col1, $col2, $col3) = array($hex[0].$hex[1], $hex[2].$hex[3], $hex[4].$hex[5]);
		} elseif (strlen($hex) == 3) {
			list($col1, $col2, $col3) = array($hex[0].$hex[0], $hex[1].$hex[1], $hex[2].$hex[2]);
		} else {
			return false;
		}
		return array(hexdec($col1), hexdec($col2), hexdec($col3));
	}

	# pixel -> millimeter in 72 dpi
	function _px2mm($px) {
		return $px*25.4/72;
	}
}
?>