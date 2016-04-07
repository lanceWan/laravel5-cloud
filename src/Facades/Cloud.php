<?php
namespace Lance\Cloud\Facades;
use Illuminate\Support\Facades\Facade;
class Cloud extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'cloud';
	}
}