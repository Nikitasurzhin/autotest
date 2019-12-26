<?php

use PHPUnit\Framework\TestCase;

final class TagankaTest extends TestCase
{
   public static $soap_client;
   const BASE_PARAMS = array(
        'AuthInfo'=>array(
            'Login'=>'tshmikova',
            'Password'=>'tsh123',
            'SessionId'=>'12345'),
    );
    public static $subject_in_period_result = array();

    public static function setUpBeforeClass()
    {
        $wsdl = getenv('TAGANKA_WSDL_URL');
        echo "TAGANKA_WSDL_URL: $wsdl \n";

        self::$soap_client = new SoapClient( $wsdl
             //"http://taganka.elt-poisk.com/soapTaganka.php?wsdl"
            //"http://stends.elt-poisk.com/492-0-T/soapTaganka.php?wsd
             // "http://stends.elt-poisk.com/TAGANKA-DEV/soapTaganka.php?wsdl"
            //"http://taganka-test.elt-poisk.com/soapTaganka.php?wsdl"
            ,
            array("trace" => 1, "exception" => 0)
        );
    }

    public function testGetSubjectInPeriod():void
    {
        $params = self::BASE_PARAMS;
        $params['DateFrom'] = "01.01.2018";
        $params['DateTo'] = "01.01.2020";

        $resp = self::$soap_client->GetSubjectInPeriod($params);
        $resp_arr = get_object_vars($resp);
        $subjects_arr = get_object_vars($resp_arr['GetSubjectInPeriodResult'])['id'];
        $this->assertNotEmpty($subjects_arr,"Список субъектов пуст");

        self::$subject_in_period_result = is_array($subjects_arr) ? $subjects_arr : array($subjects_arr);
        print_r('GetSubjectInPeriod returned: ');
        print_r(self::$subject_in_period_result);
    }

    public function testImportContract():void
    {

        foreach (self::$subject_in_period_result as $subject_id){

            echo "\nВызываем ImportContract для $subject_id\n";

            $params = self::BASE_PARAMS;
            $params['DateFrom'] = "2018-01-01";
            $params['DateTo']   = "2020-01-01";
            $params['SubjectId'] = $subject_id;

            $resp = self::$soap_client->ImportContract($params);
            $resp_arr = (get_object_vars(get_object_vars($resp)['BUSINESS_PARTNER']));
          //  $this->assertEquals($subject_id, $resp_arr['PARTNER_NO'],"PARTNER_NO не совпадает с запрашиваемым");

            $contracts = get_object_vars($resp_arr['CONTRACTS']);


               // print_r(json_decode(json_encode($resp),true));

            if(!array_key_exists('CONTRACT',$contracts))
            {
                echo "Нет контрактов\r\n";
            }
            else
            {

                $contracts = $contracts['CONTRACT'];
                if(array_key_exists('CONTRACT_ID',$contracts))$contracts = get_object_vars($resp_arr['CONTRACTS']);

                foreach($contracts as $contract){


                    $contract_arr =  get_object_vars($contract);
                    echo "CONTRACT_ID: " .$contract_arr['CONTRACT_ID'] ." \n";

                    //print_r($contract_arr);

                    if (array_key_exists('SELLER_CODE_FIO',$contract_arr)) echo 'SELLER_CODE_FIO: '.$contract_arr['SELLER_CODE_FIO']."\n";
                    if (array_key_exists('PROLONG',$contract_arr)) echo 'PROLONG: '.$contract_arr['PROLONG']."\n";

                    $this->assertArrayHasKey('SELLER_CODE_FIO',$contract_arr,"Отсутствует поле CONTRACT.SELLER_CODE_FIO\r\n".var_export($contract_arr, true));
                    $this->assertArrayHasKey('PROLONG',$contract_arr,"Отсутствует новое поле CONTRACT.PROLONG\r\n".var_export($contract_arr, true));

                    $transport_arr = get_object_vars($contract_arr['TRANSPORT']);


                    $this->assertArrayHasKey('ISNEW',$transport_arr,"Отсутствует новое поле TRANSPORT.ISNEW\r\n".var_export($transport_arr, true));
                    echo 'ISNEW: '.$transport_arr['ISNEW']."\n";

                    $invoices_arr = get_object_vars($contract_arr['INVOICES']);
                    foreach ($invoices_arr as $invoice)
                    {
                        $inv = is_array($invoice) ?  $invoice : get_object_vars($invoice);
                     //   echo 'AMOUNT_SK: '.$inv['AMOUNT_SK']."\n";
                        $this->assertArrayHasKey('AMOUNT_SK',$inv,"Отсутствует новое поле INVOICE.AMOUNT_SK\r\n".var_export($invoices_arr, true));
                    }

                    echo "\n";
                }

            }
        }

    }

    public function testGetActList():void
    {
        $params = self::BASE_PARAMS;
        $resp = self::$soap_client->GetActList($params);
        $resp_arr = get_object_vars($resp);

        if(count($resp_arr)==0) echo "GetActList: Нет ни одного акта в статусе 'Принят в СК'!";
        $this->assertTrue(true);
    }

    public function testCreateClosedActs():void
    {
        $params = self::BASE_PARAMS;
        $params['ActId'] = 366286;

        $resp = self::$soap_client->CreateClosedActs($params);
        $resp_arr = get_object_vars($resp);
        $this->assertArrayHasKey('ACT',$resp_arr);
    }

    public function testChangeStatusAct():void
    {
        $params = self::BASE_PARAMS;
        $params['ActId'] = 366286;
        $params['StatusId'] = 5;

        $resp = self::$soap_client->ChangeStatusAct($params);
        $resp_arr = get_object_vars($resp);

        $this->assertArrayHasKey('Success',$resp_arr);
        $this->assertArrayHasKey('ResponseStatus',$resp_arr);
    }


    public function testGetInvoiceData():void
    {
        $params = self::BASE_PARAMS;
        $params['InvoiceId'] = 121212;

        $resp = self::$soap_client->GetInvoiceData($params);
        $resp_arr = get_object_vars($resp);

        $this->assertArrayHasKey('INVOICE',$resp_arr);

    }


}
