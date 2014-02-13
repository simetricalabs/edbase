/**
 * Main JavaScript file
 *
 * @package         Modals
 * @version         4.7.1
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

(function($)
{
	$(document).ready(function()
	{
		$.each($('.' + modal_class), function(i, el)
		{
			p = $.extend({}, modal_defaults);

			// Get data from tag
			$.each(el.attributes, function(index, attr)
			{
				if (attr.name.indexOf("data-modal-") === 0)
				{
					k = $.camelCase(attr.name.substring(11));
					p[k] = attr.value;
				}
			});

			// remove width/height if inner is already set
			if (p['innerWidth'] != undefined)
			{
				delete p['width'];
			}
			if (p['innerHeight'] != undefined)
			{
				delete p['height'];
			}

			// set true/false values to booleans
			for (k in p)
			{
				if (p[k] == 'true')
				{
					p[k] = true;
				}
				else if (p[k] == 'false')
				{
					p[k] = false;
				}
				else if (!isNaN(p[k]))
				{
					p[k] = parseFloat(p[k]);
				}
			}

			p['onComplete'] = function() { modalsResize()};

			// Bind the modal script to the element
			$(el).colorbox(p);
		});
	});
})(jQuery);

function modalsResize()
{
	(function($)
	{

		$.each($('#colorbox'), function(i, el)
		{
			$el = $(el);
			$title = $('#cboxTitle');
			$content = $('#cboxLoadedContent');

			$th = $title.outerHeight() + 1;
			$m = parseInt($content.css('marginTop'));

			if ($th > $m)
			{
				$h = parseInt($content.css('height')) - ($th - $m);
				$div = $th - $m;
				$content.css('marginTop', $th);

				if (parseInt($el.css('top')) < 23)
				{
					// resize the inner content
					$content.css('height', parseInt($content.css('height')) - $div);
				}
				else
				{
					// resize the window
					$el.css('height', parseInt($el.css('height')) + $div);
					$el.css('top', parseInt($el.css('top')) - ($div / 2));
					$('#cboxWrapper').css('height', parseInt($('#cboxWrapper').css('height')) + $div);
					$('#cboxContent').css('height', parseInt($('#cboxContent').css('height')) + $div);
					$('#cboxMiddleLeft').css('height', parseInt($('#cboxMiddleLeft').css('height')) + $div);
					$('#cboxMiddleRight').css('height', parseInt($('#cboxMiddleRight').css('height')) + $div);
				}
			}
		});
	})(jQuery);
}
