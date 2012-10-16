<?php
/**
 * @package    JInbound.UnitTest
 */

/**
 * Test case class for JInbound Unit Testing
 *
 * @package  JInbound.UnitTest
 * @since    3.0
 */
abstract class JInboundTestCase extends JoomlaTestCase
{
	/**
	 * Overrides the parent setup method.
	 *
	 * @return  void
	 *
	 * @see     PHPUnit_Framework_TestCase::setUp()
	 * @since   3.0
	 */
	protected function setUp()
	{
		parent::setUp();
	}

	/**
	 * Overrides the parent tearDown method.
	 *
	 * @return  void
	 *
	 * @see     PHPUnit_Framework_TestCase::tearDown()
	 * @since   3.0
	 */
	protected function tearDown()
	{
		parent::tearDown();
		require JPATH_TESTS . '/includes/env/teardown.php';
	}
}
