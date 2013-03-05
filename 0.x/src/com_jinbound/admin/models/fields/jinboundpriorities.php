<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');

class JFormFieldJinboundPriorities extends JFormField {

	protected $type = 'JinboundPriorities';


	public function getLabel() {

		//init

		$ret='<table width="80%" border="0">';
			$ret .='<tr><td align="left"><strong>Lead Priorities</strong></td><td align="right"><a href="#"><strong>+</strong> Create New Priority</a></td></tr>';
			$ret.='<tr><td colspan="2">';
				$ret.='<table width="100%" border="1" cellspacing="0" cellpadding="2">';
					$ret.='<tr><th></th><th>Name</th><th>Published</th><th>ID #</th><th>Description</th></tr>';
					for($i=0;$i<=3;$i++) {
						$ret .= '<tr>';
							$ret .= '<td width="20"><input type="checkbox" /></td>';
							$ret .= '<td>&nbsp; Priority '.($i+1).'</td>';
							$ret .= '<td>&nbsp; <input type="radio"></td>';
							$ret .= '<td>&nbsp; '.$i.'</td>';
							$ret .= '<td>&nbsp; </td>';
						$ret .= '</tr>';
					}
				$ret.='</table>';
			$ret.='</td></tr>';
		$ret.='</table>';
		return $ret;
	}

	public function getInput() {
		return '';
	}
}