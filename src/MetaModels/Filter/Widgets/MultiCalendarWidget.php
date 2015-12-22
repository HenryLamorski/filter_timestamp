<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 *
 * @package    MetaModels
 * @subpackage FrontendFilter
 * @author     Henry Lamorski <henry.lamorski@mailbox.org>
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Filter\Widgets;

/**
 * Datepicker-Form field with more than 1 input.
 *
 * Based on MultiInputWidget from Christian de la Haye <service@delahaye.de>  and form field by Leo Feyer.
 *
 * @package       MetaModels
 * @subpackage    FrontendFilter
 * @author        Henry Lamorski <henry.lamorski@mailbox.org>
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

        $this->arrAttributes['maxlength'] = 10;

        switch ($strKey) {

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
        if (is_array($varInput)) {
            return parent::validator($varInput);
        }

        return parent::validator(trim($varInput));
    }

    /**
     * Generate the widget and return it as string.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function generate()
    {

        $GLOBALS['TL_CSS'][]        = 'assets/mootools/datepicker/' . DATEPICKER . '/datepicker.css';
        $GLOBALS['TL_JAVASCRIPT'][] = 'assets/mootools/datepicker/' . DATEPICKER . '/datepicker.js';
        $dateFormat                 = strlen(
            $this->dateFormat
        ) ? $this->dateFormat : $GLOBALS['TL_CONFIG'][$this->rgxp . 'Format'];
        $dateDirection              = strlen($this->dateDirection) ? $this->dateDirection : '0';
        $jsEvent                    = $this->jsevent ? $this->jsevent : 'domready';

        if ($this->dateParseValue && $this->varValue != '') {
            $this->varValue = \Date::parse($dateFormat, strtotime($this->varValue));
        }

        $GLOBALS['TL_HEAD'][] = $this->getDateString();

        // Initialize the default config
        $arrConfig = array(
            'draggable'    => (($this->draggable) ? "'true'" : "'false'"),
            'pickerClass'  => "'datepicker_dashboard'",
            'useFadeInOut' => "'!Browser.ie'",
            'startDay'     => $GLOBALS['TL_LANG']['MSC']['weekOffset'],
            'titleFormat'  => sprintf('\'%s\'', $GLOBALS['TL_LANG']['MSC']['titleFormat']),
        );

        $arrConfigSeparate = array();

        switch ($this->rgxp) {
            case 'datim':
                $arrConfig['timePicker'] = 'true';
                break;

            case 'time':
                $arrConfig['pickOnly'] = 'time';
                break;
            default:
        }

        switch ($dateDirection) {
            case '+0':
                $arrConfig['minDate'] = sprintf(
                    'new Date(%s, %s, %s)',
                    date('Y'),
                    (date('n') - 1),
                    date('j')
                );
                break;
            case '+1':
                $time                 = strtotime('+1 day');
                $arrConfig['minDate'] = sprintf(
                    'new Date(%s, %s, %s)',
                    date('Y', $time),
                    (date('n', $time) + 1),
                    date('j', $time)
                );
                break;
            case '-1':
                $time                 = strtotime('-1 day');
                $arrConfig['maxDate'] = sprintf(
                    'new Date(%s, %s, %s)',
                    date('Y', $time),
                    (date('n', $time) - 1),
                    date('j', $time)
                );
                break;
            default:
        }

        // default Offset
        $intOffsetX = 0;
        $intOffsetY = 0;

        if ($this->arrAttributes['dateImage']) {
            // icon
            $strIcon = 'assets/mootools/datepicker/' . DATEPICKER . '/icon.gif';

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

            $arrSize = getimagesize(TL_ROOT . '/' . $strIcon);

            for ($i = 0; $i < $this->size; $i++) {
                $arrConfigSeparate[$i]['toggle'] = sprintf('$$(\'#toggle_%s_%s\')', $this->name, $i);
            }

            if ($this->dateImageOnly) {
                $arrConfig['togglesOnly'] = 'false';
            }

            // make offsets configurable (useful for the front end but can be used in the back end as well)
            $intOffsetX = -197;
            $intOffsetY = -182;
        }

        // make offsets configurable (useful for the front end but can be used in the back end as well)
        $intOffsetX                  = (is_numeric($this->offsetX)) ? $this->offsetX : $intOffsetX;
        $intOffsetY                  = (is_numeric($this->offsetY)) ? $this->offsetY : $intOffsetY;
        $arrConfig['positionOffset'] = '{x:' . $intOffsetX . ',y:' . $intOffsetY . '}';

        // correctly style the date format
        $objDate             = new \Date(null, $GLOBALS['TL_CONFIG']['dateFormat']);
        $arrConfig['format'] = "'" . $objDate->formatToJs($dateFormat) . "'";

        $arrCompiledConfig         = array();
        $arrCompiledConfigSeparate = array();

        // compile Mootools calendar configs global
        foreach ($arrConfig as $k => $v) {
            $arrCompiledConfig[] = "    '" . $k . "': " . $v;
        }

        // compile Mootools calendar configs for each calender seperate
        for ($i = 0; $i < $this->size; $i++) {
            foreach ($arrConfigSeparate[$i] as $k => $v) {
                $arrCompiledConfigSeparate[$i][] = "    '" . $k . "': " . $v;
            }
        }

        // init/create Javascript Buffer
        $strBuffer = '' . $this->getScriptTag() . '
window.addEvent(\'' . $jsEvent . '\', function() {';

        // init HTML Input Buffer
        $return = '';

        for ($i = 0; $i < $this->size; $i++) {

            $return .= '<span class="wrapper item' . $i . '">';
            $return .= sprintf(
                '<input type="text" name="%s[]" placeholder="%s" id="ctrl_%s_%s" class="text%s" value="%s"%s%s',
                $this->strName,
                $this->arrAttributes['placeholder'][$i],
                $this->strId,
                $i,
                (strlen($this->strClass) ? ' ' . $this->strClass : ''),
                specialchars($this->varValue[$i]),
                $this->getAttributes(),
                $this->strTagEnding
            );
            if ($this->arrAttributes['dateImage']) {
                $return .= sprintf(
                    '<img src="%s" width="%d" height="%d" alt="" class="CalendarFieldIcon" id="toggle_%s_%s" />',
                    $strIcon,
                    $arrSize[0],
                    $arrSize[1],
                    $this->name,
                    $i
                );
            }

            $return    .= '</span>';
            $strBuffer .= sprintf(
                'new Picker.Date($$(\'#ctrl_%s_%s\'), {%s' . ",\n" . ' });',
                $this->name,
                $i,
                implode(",\n", $arrCompiledConfig),
                implode(",\n", $arrCompiledConfigSeparate[$i])
            );
        }

        $strBuffer .= '});</script>';

        return $return . $strBuffer;
    }

    /**
     * Return the datepicker string.
     *
     * Fix the MooTools more parsers which incorrectly parse ISO-8601 and do
     * not handle German date formats at all.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
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

    /**
     * Retrieve the correct script tag.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function getScriptTag()
    {
        return $GLOBALS['objPage']->outputFormat == 'html' ? '<script>' : '<script type="text/javascript">';
    }
}
