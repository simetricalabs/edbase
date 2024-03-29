<?php
/**
 * Element: AssignmentSelection
 * Displays Assignment Selection radio options
 *
 * @package         NoNumber Framework
 * @version         14.1.1
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nnframework/helpers/text.php';

class JFormFieldNN_AssignmentSelection extends JFormField
{
	public $type = 'AssignmentSelection';
	private $params = null;

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		JHtml::stylesheet('nnframework/style.min.css', false, true);

		require_once __DIR__ . '/toggler.php';
		$toggler = new nnFieldToggler;

		$this->value = (int) $this->value;
		$label = $this->get('label');
		$param_name = $this->get('name');
		$noshow = $this->get('noshow', 0);
		$showclose = $this->get('showclose', 0);

		$html = array();

		if ($label)
		{
			$label = NNText::html_entity_decoder(JText::_($label));

			$html[] = '<div style="clear: both;"></div>';
			if (!$noshow)
			{
				$html[] = $toggler->getInput(array('div' => 1, 'param' => 'show_assignments|' . $param_name, 'value' => '1|1,2'));
			}

			$class = 'nn_panel nn_panel_title nn_panel_toggle';
			if ($this->value === 1)
			{
				$class .= ' nn_panel_include';
			}
			else if ($this->value === 2)
			{
				$class .= ' nn_panel_exclude';
			}
			$html[] = '<div class="' . $class . '">';
			if ($showclose && JFactory::getUser()->authorise('core.admin'))
			{
				$html[] = '<button type="button" class="nn_close nn_remove_assignment">&times;</button>';
			}
			$html[] = '<label class="nn_block nn_title" for="cb_' . $param_name . '">';
			$html[] = '<input id="cb_' . $param_name . '" type="checkbox" class="checkbox" ' . ($this->value ? 'checked="checked"' : '') . ' onchange="nnScripts.setRadio(\'' . $param_name . '\', this.checked);">';
			$html[] = $label;
			$html[] = '<div style="clear: both;"></div>';
			$html[] = '</label></div>';

			$html[] = $toggler->getInput(array('div' => 1, 'param' => $param_name, 'value' => '1,2'));
			$html[] = '<div class="nn_panel nn_panel"><div class="nn_block">';

			$html[] = '<ul class="adminformlist"><li>';

			$label = JText::_('NN_SELECTION');
			$tip = htmlspecialchars(trim($label, ':') . '::' . JText::_('NN_SELECTION_DESC'), ENT_COMPAT, 'UTF-8');
			$html[] = '<label id="' . $this->id . '-lbl" for="' . $this->id . '" class="hasTip" title="' . $tip . '">' . $label . '</label>';
			$html[] = '<fieldset id="' . $this->id . '" class="radio">';

			$html[] = '<div style="display:none;">';
			$onclick = ' onclick="nnScripts.setToggleTitleClass(this, 0, 8)"';
			$html[] = '<input type="radio" id="' . $this->id . '0" name="' . $this->name . '" value="0"' . ((!$this->value) ? ' checked="checked"' : '') . $onclick . '/>';
			$html[] = '</div>';

			$onclick = ' onclick="nnScripts.setToggleTitleClass(this, 1, 7)"';
			$html[] = '<input type="radio" id="' . $this->id . '1" name="' . $this->name . '" value="1"' . (($this->value === 1) ? ' checked="checked"' : '') . $onclick . '/>';
			$html[] = '<label for="' . $this->id . '1">' . JText::_('NN_INCLUDE') . '</label>';

			$onclick = ' onclick="nnScripts.setToggleTitleClass(this, 2, 7)"';
			$onclick .= ' onload="nnScripts.setToggleTitleClass(this, ' . $this->value . ', 7)"';
			$html[] = '<input type="radio" id="' . $this->id . '2" name="' . $this->name . '" value="2"' . (($this->value === 2) ? ' checked="checked"' : '') . $onclick . '/>';
			$html[] = '<label for="' . $this->id . '2">' . JText::_('NN_EXCLUDE') . '</label>';

			$html[] = '</fieldset>';
		}
		else
		{
			$html[] = '<div style="clear: both;"></div>';
			$html[] = '</li></ul>';

			$html[] = '<div style="clear: both;"></div>';
			$html[] = '</div></div>';

			if (!$noshow)
			{
				$html[] = $toggler->getInput(array('div' => 1));
			}
			$html[] = $toggler->getInput(array('div' => 1));
		}

		return implode($html);
	}

	private function get($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}
