<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

echo $this->loadTemplate('edit');

JHtml::_('behavior.formvalidation');

JText::script('COM_JINBOUND_ENTER_EMAIL_RECIPIENT');
JText::script('COM_JINBOUND_EMAIL_NOT_SENT');
JText::script('COM_JINBOUND_EMAIL_SENT');
JText::script('JGLOBAL_VALIDATION_FORM_FAILED');

?>
<script type="text/javascript">
	Joomla.emailtest = function(form)
	{
		<?php echo $this->form->getField('htmlbody')->save(); ?>
		var sendto = prompt(Joomla.JText._('COM_JINBOUND_ENTER_EMAIL_RECIPIENT'));
		var url = 'index.php?option=com_jinbound&task=email.test';
		var token = '<?php echo JSession::getFormToken(); ?>';
		var data = {
			to: sendto
		,	fromname: document.getElementById('jform_fromname').value
		,	fromemail: document.getElementById('jform_fromemail').value
		,	subject: document.getElementById('jform_subject').value
		,	htmlbody: document.getElementById('jform_htmlbody').value
		,	plainbody: document.getElementById('jform_plainbody').value
		};
		data[token] = 1;
		var success = function(response)
		{
			alert(Joomla.JText._('Done' == response ? 'COM_JINBOUND_EMAIL_SENT' : 'COM_JINBOUND_EMAIL_NOT_SENT'));
		};
		if ('undefined' === typeof jQuery)
		{
			var r = new Request.HTML({
				url: url
			,	method: 'POST'
			,	data: data
			, onSuccess: success
			}).send();
		}
		else
		{
			jQuery.ajax(url, {
				type: 'POST'
			,	data: data
			,	success: success
			});
		}
	};
	Joomla.submitbutton = function(task)
	{
		var form = document.getElementById('adminForm');
		if ('email.cancel' === task)
		{
			Joomla.submitform(task, form);
		}
		else if ('email.test' === task)
		{
			Joomla.emailtest(form);
		}
		else if (!document.formvalidator.isValid(form))
		{
			alert(Joomla.JText._('JGLOBAL_VALIDATION_FORM_FAILED'));
		}
		else
		{
			Joomla.submitform(task, form);
		}
	};
</script>
