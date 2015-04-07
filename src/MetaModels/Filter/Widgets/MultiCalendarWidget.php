<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package    MetaModels
 * @subpackage FrontendFilter
 * @author     Henry Lamorski <henry.lamorski@mailbox.org>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Filter\Widgets;

/**
 * Datepicker-Form field with more than 1 input, based on MultiInputWidget from Christian de la Haye <service@delahaye.de>  and form field by Leo Feyer
 *
 * @package	   MetaModels
 * @subpackage FrontendFilter
 * @author     Henry Lamorski <henry.lamorski@mailbox.org>
 */
class MultiCalendarWidget extends \Widget
{
	/**
	 * Submit user input.
	 *
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * The template to use.
	 *
	 * @var string
	 */
	protected $strTemplate = 'form_widget';

	/**
	 * Add specific attributes.
	 *
	 * @param string $strKey   Name of the key to set.
	 *
	 * @param mixed  $varValue The value to use.
	 *
	 * @return void
	 */
	public function __set($strKey, $varValue)
	{

		$this->arrAttributes['maxlength'] =  10;

		switch ($strKey)
		{
			
			case 'dateImage':
				$this->arrAttributes['dateImage'] = $varValue;
				break;

			case 'placeholder':
				$this->arrAttributes['placeholder'] = $varValue;
			break;
			

			default:
				parent::__set($strKey, $varValue);
				break;
		}
	}

	/**
	 * Trim the values and validate them.
	 *
	 * @param mixed $varInput The value to process.
	 *
	 * @return mixed The processed value
	 */
	protected function validator($varInput)
	{
		if (is_array($varInput))
		{
			return parent::validator($varInput);
		}

		return parent::validator(trim($varInput));
	}


	/**
	 * Generate the widget and return it as string.
	 *
	 * @return string
	 */
	public function generate()
	{
	
		$blnV3 = version_compare(VERSION, '3.0', '>=');
	        $GLOBALS['TL_CSS'][] = $blnV3 ? 'assets/mootools/datepicker/'.DATEPICKER.'/dashboard.css' : 'plugins/datepicker/dashboard.css';
                $GLOBALS['TL_JAVASCRIPT'][] = $blnV3 ? 'assets/mootools/datepicker/'.DATEPICKER.'/datepicker.js' : 'plugins/datepicker/datepicker.js';
	        $dateFormat = strlen($this->dateFormat) ? $this->dateFormat : $GLOBALS['TL_CONFIG'][$this->rgxp . 'Format'];
	        $dateDirection = strlen($this->dateDirection) ? $this->dateDirection : '0';
	        $jsEvent = $this->jsevent ? $this->jsevent : 'domready';

	        if ($this->dateParseValue && $this->varValue != '') {
	            $this->varValue = $this->parseDate($dateFormat, strtotime($this->varValue));
	        }

	      $GLOBALS['TL_HEAD'][] = $this->getDateString();
		
		 // Initialize the default config
	        $arrConfig = array(
            'draggable'            => (($this->draggable) ? "'true'" : "'false'"),
            'pickerClass'        => "'datepicker_dashboard'",
            'useFadeInOut'        => "'!Browser.ie'",
            'startDay'            => $GLOBALS['TL_LANG']['MSC']['weekOffset'],
            'titleFormat'        => "'{$GLOBALS['TL_LANG']['MSC']['titleFormat']}'",
        );

        switch ($this->rgxp) {

            case 'datim':
                $arrConfig['timePicker'] = 'true';
                break;

            case 'time':
                $arrConfig['pickOnly'] = 'time';
                break;
        }

        switch ($dateDirection) {

            case '+0':
                $arrConfig['minDate'] = 'new Date(' . date('Y') . ', ' . (date('n')-1) . ', ' . date('j') . ')';
                break;

            case '+1':
                $time = strtotime('+1 day');
                $arrConfig['minDate'] = 'new Date(' . date('Y', $time) . ', ' . (date('n', $time)+1) . ', ' . date('j', $time) . ')';
                break;

            case '-1':
                $time = strtotime('-1 day');
                $arrConfig['maxDate'] = 'new Date(' . date('Y', $time) . ', ' . (date('n', $time)-1) . ', ' . date('j', $time) . ')';
                break;
        }

        // default Offset
        $intOffsetX = 0;
        $intOffsetY = 0;

	
        if ($this->arrAttributes['dateImage']) {
            // icon
            $strIcon = $blnV3 ? 'assets/mootools/datepicker/'.DATEPICKER.'/icon.gif' : 'plugins/datepicker/icon.gif';

            if ($this->dateImageSRC) {
                if (is_numeric($this->dateImageSRC)) {
                    if (($objFile = \FilesModel::findByPk($this->dateImageSRC)) !== null) {
                        $this->dateImageSRC = $objFile->path;
                    }
                }

                if (is_file(TL_ROOT . '/' . $this->dateImageSRC)) {
                    $strIcon = $this->dateImageSRC;
                }
            }

            $arrSize = @getimagesize(TL_ROOT . '/' . $strIcon);

           
	    for ($i = 0; $i < $this->size; $i++)
	    {
		$arrConfigSeperate[$i]['toggle'] = "$$('#toggle_" . $this->name . "_".$i . "')";
	    }

            if ($this->dateImageOnly) {
                $arrConfig['togglesOnly'] = 'false';
            }

            // make offsets configurable (useful for the front end but can be used in the back end as well)
            $intOffsetX = -197;
            $intOffsetY = -182;
        }

	// make offsets configurable (useful for the front end but can be used in the back end as well)
        $intOffsetX = (is_numeric($this->offsetX)) ? $this->offsetX : $intOffsetX;
        $intOffsetY = (is_numeric($this->offsetY)) ? $this->offsetY : $intOffsetY;
        $arrConfig['positionOffset'] = '{x:' . $intOffsetX . ',y:' . $intOffsetY . '}';

        // correctly style the date format
	$objDate  = new \Date($StrDateVal, $GLOBALS['TL_CONFIG']['dateFormat']);
        $arrConfig['format'] = "'" . $objDate->formatToJs($dateFormat) . "'";

	$arrCompiledConfig = array();
	$arrCompiledConfigSeperate = array();

	// compile Mootools calendar configs global 
	foreach ($arrConfig as $k => $v) {
        	    $arrCompiledConfig[] = "    '" . $k . "': " . $v;
        }

	// compile Mootools calendar configs for each calender seperate
	for ($i = 0; $i < $this->size; $i++)
	{
        	foreach ($arrConfigSeperate[$i] as $k => $v) {
        	    $arrCompiledConfigSeperate[$i][] = "    '" . $k . "': " . $v;
	        }
	}

	// init/create Javascript Buffer
	$strBuffer .= '' . $this->getScriptTag() . " 
			window.addEvent('" . $jsEvent . "', function() {";

	// init HTML Input Buffer
	$return = '';

	
	for ($i = 0; $i < $this->size; $i++)
	{

		
		$return .= '<span class="wrapper item'.$i.'">';
		$return .= sprintf('<input type="%s" name="%s[]" placeholder="%s" id="ctrl_%s_%s" class="text%s%s" value="%s"%s%s',
				'text',
				$this->strName,
				$this->arrAttributes['placeholder'][$i],
				$this->strId,
				$i,
				'',
				(strlen($this->strClass) ? ' ' . $this->strClass : ''),
				specialchars($this->varValue[$i]),
				$this->getAttributes(),
				$this->strTagEnding);

		$return .= ($this->arrAttributes['dateImage']) ?
				'<img src="' . $strIcon . '" width="' . $arrSize[0] . 
				'" height="' . $arrSize[1] . '" alt="" class="CalendarFieldIcon" id="toggle_' . 
				$this->name . "_".$i . '"' . $style . '>' : '';

		$return .= '</span>';
		$strBuffer .= "new Picker.Date($$('#ctrl_" . $this->name . "_".$i."'), {" . 
			      implode(",\n", $arrCompiledConfig) .",\n" . 
			      implode(",\n", $arrCompiledConfigSeperate[$i]) ." });";	

	}

	$strBuffer .= "});</script>";


	return $return.$strBuffer;
	}


    /**
     * Return the datepicker string
     *
     * Fix the MooTools more parsers which incorrectly parse ISO-8601 and do
     * not handle German date formats at all.
     * @return string
     */
    public function getDateString()
    {
        return $this->getScriptTag() . '
window.addEvent("domready",function(){
  Locale.define("en-US","Date",{
    months:["' . implode('","', $GLOBALS['TL_LANG']['MONTHS']) . '"],
    days:["' . implode('","', $GLOBALS['TL_LANG']['DAYS']) . '"],
    months_abbr:["' . implode('","', $GLOBALS['TL_LANG']['MONTHS_SHORT']) . '"],
    days_abbr:["' . implode('","', $GLOBALS['TL_LANG']['DAYS_SHORT']) . '"]
  });
  Locale.define("en-US","DatePicker",{
    select_a_time:"' . $GLOBALS['TL_LANG']['DP']['select_a_time'] . '",
    use_mouse_wheel:"' . $GLOBALS['TL_LANG']['DP']['use_mouse_wheel'] . '",
    time_confirm_button:"' . $GLOBALS['TL_LANG']['DP']['time_confirm_button'] . '",
    apply_range:"' . $GLOBALS['TL_LANG']['DP']['apply_range'] . '",
    cancel:"' . $GLOBALS['TL_LANG']['DP']['cancel'] . '",
    week:"' . $GLOBALS['TL_LANG']['DP']['week'] . '"
  });
});
</script>';
    }


    public function getScriptTag()
    {

        global $objPage;

        return $objPage->outputFormat == 'html' ? '<script>' : '<script type="text/javascript">';
    }


}
