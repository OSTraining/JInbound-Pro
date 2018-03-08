<?php

require_once JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php';

/**
 * Tests for JInbound helper class.
 *
 */
class JInboundHelperTest extends JInboundTestCase
{
	/**
	 * Cases for JInbound::config
	 * 
	 * @return array
	 */
	function casesConfig() {
		$null = null;
		return array(
			'No Parameters' => array($null, $null, false)
		);
	}
	
	/**
	 * Test for JInbound::config
	 * 
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @param unknown_type $expected
	 * 
	 * @dataProvider casesConfig
	 */
	public function testConfig($key, $value, $expected) {
		$this->markTestSkipped('TODO');
		//$this->assertEquals(JInbound::config($key, $value), $expected);
	}
	
	/**
	 * Test for JInbound::language
	 */
	public function testLanguage() {
		$this->markTestSkipped('TODO');
	}
	
	/**
	 * Test for JInbound::debugger
	 */
	public function testDebugger() {
		// first assertion
		$name = 'JInbound::debugger Test Assertion 1';
		$data = array('test1' => 123, 'test2' => array(4, 5, 6));
		// debug this assertion
		$debug = JInbound::debugger($name, $data);
		// create expected
		$expected = array($name => $data);
		// check assertion
		$this->assertEquals($debug, $expected);
		
		// add a second case
		$name = 'JInbound::debugger Test Assertion 1';
		$data = array('test3' => 'foo', 'test4' => array(7, 8, 9), 'test5' => json_decode('{"foo":"bar"}'));
		
		// debug this one too
		$debug = JInbound::debugger($name, $data);
		// add to expected values
		$expected[$name] = $data;
		// check assertion
		$this->assertEquals($debug, $expected);
	}
	
	/**
	 * Test for JInbound::debug
	 */
	public function testDebug() {
		$this->markTestSkipped('Don\'t bother testing this...');
	}
	
	/**
	 * Test for JInbound::version
	 */
	public function testVersion() {
		// get JVersion
		jimport('cms');
		jimport('cms.version.version');
		$version = new JVersion;
		
		// check RELEASE
		$this->assertEquals(JInbound::version()->RELEASE, $version->RELEASE);
		
		// check compatibility
		$this->assertEquals(JInbound::version()->isCompatible('2.5'), $version->isCompatible('2.5'));
	}
}
