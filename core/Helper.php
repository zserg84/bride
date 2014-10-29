<?php

class Helper{

	public static function urlParamsReplace($params, $replaceParams, $delimiter1 = '&', $delimiter2 = '='){
		if($params){
			foreach($params as $paramKey=>$paramVal){
				if(is_array($paramVal))
					continue;
				foreach($replaceParams as $j=>$rp){
					if($paramKey == $j){
						$params[$paramKey] = $rp;
						unset($replaceParams[$j]);
						break;
					}
				}
			}
		}
		else{
			$params = array();
		}
//		var_dump($replaceParams);
		foreach($replaceParams as $j=>$rp){
			$params[$j]= $rp;
		}
//		var_dump($params);
		$paramsStr = '';
		foreach($params as $key=>$param){
			if(is_array($param)){
				foreach($param as $k=>$p){
					$paramsStr .= $paramsStr ? '&' : '';
					$paramsStr.= $key.'['.$k.']' . $delimiter2 . $p;
				}
			}
			else{
				$paramsStr .= $paramsStr ? '&' : '';
				$paramsStr .= $key . $delimiter2. $param;
			}
		}
		return $paramsStr;
	}

	public static function getParam($name, $default=null){
		$value = $default;
		if(isset($_REQUEST[$name]))
			$value = $_REQUEST[$name];
		return $value;
	}
}