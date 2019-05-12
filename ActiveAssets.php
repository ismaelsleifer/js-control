<?php

namespace sleifer\jscontrol;

use \yii\web\AssetBundle;

/**
 * @author Ismael Sleifer <ismaelsleifer@gmail.com>
 */
class ActiveAssets extends AssetBundle
{
	public $sourcePath = '@sleifer/jscontrol/assets';

	public $js = [
	    'js/jscontrol.js'
	];

	public $depends = [
		'yii\web\JqueryAsset',
	];

	public $css = [
	];
}
