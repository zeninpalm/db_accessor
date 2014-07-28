<?php

class ModelLoader {
	public static function load($projectName, $modelName) {
		$model = NULL;
		if (class_exists($projectName . $modelName, false)) {
			return $projectName . $modelName;
		} else {
			return $modelName;
		}
	}	
}
