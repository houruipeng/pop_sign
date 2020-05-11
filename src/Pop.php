<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<houruipeng@duoguan.com>
 * @date: 2020/3/13 16:17
 */
namespace hrp\pop;

class Pop{

	private $Version = '2019-11-06';

	private $Format = 'JSON';

	private $SignatureMethod = 'HMAC-SHA1';

	private $SignatureVersion = '1.0';

	private $method = 'POST';

	private $Timeout = 3000;

	//秘钥
	private $accessSecret='';

	/**
	 * Pop constructor.
	 *
	 * @param string $accessSecret
	 */
	public function __construct(string $accessSecret){
		$this->accessSecret = $accessSecret;
	}

	public function composeUrl(array $apiParams){
		date_default_timezone_set("UTC");

		(isset($apiParams['Header']) && !is_string($apiParams['Header'])) && $apiParams['Header'] = json_encode($apiParams['Header'], JSON_UNESCAPED_UNICODE);
		(isset($apiParams['Query']) && !is_string($apiParams['Query']))
		&& $apiParams['Query'] = json_encode
		($apiParams['Query'], JSON_UNESCAPED_UNICODE);
		(isset($apiParams['Body']) && !is_string($apiParams['Body']))
		&& $apiParams['Body'] = json_encode
		($apiParams['Body'], JSON_UNESCAPED_UNICODE);

		$apiParams['Method'] = $apiParams['Method'] ?? 'POST';

		$apiParams["Timestamp"] = (new \DateTime())->format("Y-m-d\TH:i:s\Z");
		$apiParams["Version"] = $this->Version;
		$apiParams["Format"] = $this->Format;
		$apiParams["SignatureNonce"] = uniqid();
		$apiParams["SignatureMethod"] = $this->SignatureMethod;
		$apiParams['SignatureVersion'] = $this->SignatureVersion;

		$apiParams['Timeout'] = $apiParams['Timeout'] ?? $this->Timeout;

		foreach($apiParams as $key => $value){
			$apiParams[$key] = $this->prepareValue($value);
		}

		$apiParams["Signature"] = $this->computeSignature($apiParams);

		return $apiParams;
	}

	private function computeSignature($parameters){
		ksort($parameters);
		$canonicalizedQueryString = '';
		foreach($parameters as $key => $value){
			$canonicalizedQueryString .= '&'.$this->percentEncode($key).'='.$this->percentEncode($value);
		}
		$stringToSign = $this->method.'&%2F&'.$this->percentencode(substr($canonicalizedQueryString, 1));
		$signature = $this->signString($stringToSign);

		return $signature;
	}

	private function signString($source){
		return base64_encode(hash_hmac('sha1', $source, $this->accessSecret.'&', true));
	}

	/**
	 * 布尔值处理
	 *
	 * @param $value
	 * @return string
	 */
	private function prepareValue($value){
		if(is_bool($value)){
			if($value){
				return "true";
			}else{
				return "false";
			}
		}else{
			return $value;
		}
	}

	protected function percentEncode($str){
		$res = urlencode($str);
		$res = preg_replace('/\+/', '%20', $res);
		$res = preg_replace('/\*/', '%2A', $res);
		$res = preg_replace('/%7E/', '~', $res);
		return $res;
	}

}
