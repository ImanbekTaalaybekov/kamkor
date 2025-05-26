<?php

namespace App\Services;

use Artisaninweb\SoapWrapper\SoapWrapper;

class KamkorSoapService
{
    public function registerService()
    {
        SoapWrapper::add('kamkor', function ($service) {
            $service
                ->wsdl('https://kamkor.mvd.kg/service?wsdl')
                ->trace(true)
                ->cache(WSDL_CACHE_NONE);
        });
    }

    public function getUserDataByPin(string $pin)
    {
        $this->registerService();

        return SoapWrapper::call('kamkor.SomeMethodName', [
            'pin' => $pin
        ]);
    }
}
