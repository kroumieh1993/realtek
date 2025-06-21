<?php

/**
 * Interface Es_Payment_Method.
 */
interface Es_Payment_Method {

	/**
	 * Check is payment method active.
	 *
	 * @return mixed
	 */
	public function is_active();

	/**
	 * Make payment handler.
	 *
	 * @param $data array Payment data
	 *
	 * @return mixed
	 */
	public function proceed( $data );
}
