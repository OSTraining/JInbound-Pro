<?php
/**
 * @package             JInbound
 * @subpackage          mod_jinbound_cta
 * @ant_copyright_header@
 */

defined('_JEXEC') or die;

abstract class ModJInboundCTAAdapter
{
    /**
     * Parameter prefix, one of '', 'c1_', 'c2_', 'c3_'
     *
     * @var string
     */
    public $pfx = '';
    /**
     * Module parameters
     *
     * @var JRegistry
     */
    protected $params;

    /**
     * Constructor
     *
     * @param JRegistry $params
     */
    function __construct(JRegistry $params)
    {
        $this->params = $params;
    }

    /**
     * Adapters must have a render method that sends back the data to render
     *
     * @return string
     */
    abstract public function render();
}
