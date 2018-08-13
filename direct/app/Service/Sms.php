<?php
namespace App\Service;

class Sms {

    /**
     * 系统发送短信，包含 注册   修改手机号  等信息
     */
    public static function send($mcode, $to_mobile) {
        $result = self::sendTemplateSMS($to_mobile, array($mcode, '5分钟'), 252884);
        return $result;
    }
    /**
     * 容联云通讯发送模板短信
     * @param to 手机号码集合,用英文逗号分开
     * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
     * @param $tempId 模板Id,测试应用和未上线应用使用测试模板请填写1，正式应用上线后填写已申请审核通过的模板ID
     */
    public static function sendTemplateSMS($to, $datas, $tempId) {
        // 初始化REST SDK
        $smsconfig = config('sms');
        $accountSid = $smsconfig['rl_accountSid'];
        $accountToken = $smsconfig['rl_accountToken'];
        $appId = $smsconfig['rl_appId'];
        $serverIP = $smsconfig['rl_serverIP'];
        $serverPort = $smsconfig['rl_serverPort'];
        $softVersion = $smsconfig['rl_softVersion'];
        $rest = new Rest($serverIP, $serverPort, $softVersion);
        $rest->setAccount($accountSid, $accountToken);
        $rest->setAppId($appId);

        // 发送模板短信
        //echo "Sending TemplateSMS to $to <br/>";
        $result = $rest->sendTemplateSMS($to, $datas, $tempId);
        if ($result == NULL) {
            echo "返回为NULL";
            //return false;
        }
        $return['msg'] = $result->statusMsg;
        $return['code'] = $result->statusCode;
        return $return;
    }
}
