<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;


/**

 * 验证码(阿里云短信)

 */

function smsVerify($mobile)
{
    AlibabaCloud::accessKeyClient('LTAIdIot5c1UUbwt', 'QmbZpjkephISxT8vw6OopMEn2IYPUr')
        ->regionId('cn-hangzhou') //replace regionId as you need（这个地方是发短信的节点，默认即可，或者换成你想要的）
        ->asDefaultClient();
    $data = [];
    $code = mt_rand(100000,999999);
    $code = "{\"code\":\"$code\"}";
    try {
        $result = AlibabaCloud::rpc()
            ->product('Dysmsapi')
            // ->scheme('https') // https | http
            ->version('2017-05-25')
            ->action('SendSms')
            ->method('POST')
            ->host('dysmsapi.aliyuncs.com')
            ->options([
                'query' => [
                    'RegionId' => "cn-hangzhou",
                    'PhoneNumbers' => $mobile,
                    'SignName' => "季圣胤",
                    'TemplateCode' => "SMS_142115455",
                    'TemplateParam' => $code,
                ],
            ])
            ->request();
        print_r($result->toArray());die;
        $res    = $result->toArray();
        if($res['Code'] == 'OK'){
            $data['status'] = 1;
            $data['info']   = $res['Message'];
        }else{
            $data['status'] = 0;
            $data['info']   = $res['Message'];
        }
        return $data;

    } catch (ClientException $e) {
        $data['status'] = 0;
        $data['info']   = $e->getErrorMessage().PHP_EOL;
        return $data;
    } catch (ServerException $e) {
        $data['status'] = 0;
        $data['info']   = $e->getErrorMessage().PHP_EOL;
        return $data;

    }

}