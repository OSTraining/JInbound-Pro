<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('_JEXEC') or die;

abstract class ModJInboundCTAAdapter
{
	/**
	 * Module parameters
	 * @var JRegistry
	 */
	protected $params;
	
	/**
	 * Flag to denote if alternate content should be rendered or not
	 * @var bool
	 */
	public $is_alt;
	
	/**
	 * Constructor
	 * @param JRegistry $params
	 */
	function __construct(JRegistry $params)
	{
		$this->params = $params;
	}
	
	/**
	 * Adapters must have a render method that sends back the data to render
	 * @return string
	 */
	abstract public function render();
}
