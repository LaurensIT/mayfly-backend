<?php

class Inuse extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'inuse';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */

	public $timestamps = false;
}
