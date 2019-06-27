<?php

namespace Pqf\Smscode;

class SendReturn
{
    const SUCCESS_CODE = 0;
    const FAIL_CODE = 1;
    public $success; //0是成功
    public $result; //成功或失败原因

    public function __construct($success, $result)
    {
        $this->success = $success;
        $this->result = $result;
    }
}
