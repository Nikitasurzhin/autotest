<?php

use PHPUnit\Framework\TestCase;

Final class MajorTest extends TestCase
{
    public static $soap_client;
    public static $ins_array;

    public function getInsuransCompaniesId(){
        $wsdl = getenv('MAJOR_WSDL_URL');
        echo "коннектимся к WSDL: ".$wsdl ."\n";

        self::$soap_client = new SoapClient( $wsdl,
            array("trace" => 1, "exception" => 0)
        );
        $result = self::$soap_client->GetInsuranceCompanies();
        $insurances = $result->GetInsuranceCompaniesResult->InsuranceCompany;
        $json = json_encode($insurances);
        $ins_array = json_decode($json,true);

        foreach ( $ins_array as $sk){
            self::$ins_array[$sk['Id']] = $sk;
        }

    //    print_r(self::$ins_array);

        return self::$ins_array;
    }

    /**
     * @dataProvider getInsuransCompaniesId
     */
    public function testOSAGOFullCalculation ($Id,$Name,$LegaName,$Logo): void
    {
        $cur_date = date("Y-m-d\TH:i:s", strtotime('+3 hours'));
        $req =  array (
            'AuthInfo' =>
                array (
                    'Login' => 'tshmikova',
                    'Password' => 'tsh123',
                    'SessionId' => '166899a9-a841-4c99-a818-ab944d0201fe',
                ),
            'InsuranceCompany' => $Id,
            'InsurerType' => '0',
            'OwnerType' => '0',
            'ContractOptionId' => '13',
            'ContractStatusId' => '13',
            'UsagePlace' => '7700000000000',
            'TSToRegistrationPlace' => '0',
            'ContractBeginDate' => $cur_date,
            'Duration' => '12',
            'CarInfo' =>
                array (
                    'VehiclePower' => '120',
                    'TSType' =>
                        array (
                            'Category' => 'B',
                            'Subcategory' => '10',
                        ),
                    'Vehicle' =>
                        array (
                            'RegNumber' => 'Р061УА177',
                            'VIN' => 'YV1SZ595771288075',
                        ),
                    'UseWithTrailer' => false,
                ),
            'GrossViolations' => false,
            'Owner' =>
                array (
                    'DOB' => '1956-05-17T00:00:00',
                    'Surname' => 'Шевырков',
                    'Name' => 'Владимир',
                    'Patronymic' => 'Леонидович',
                    'Sex' => '0',
                    'PersonDocument' =>
                        array (
                            'Series' => '4505',
                            'Number' => '163081',
                            'Type' => '1',
                            'IssuedBy' => 'ОВД Жулебино г. Москвы',
                            'IssuedDate' => '2003-02-19T00:00:00',
                        ),
                    'SubjectType' => '0',
                    'RegistrationAddress' =>
                        array (
                            'Resident' => '1',
                            'Index' => '109145',
                            'Country' => 'Россия',
                            'Region' => 'г. Москва',
                            'CityKLADR' => '7700000000000',
                            'Street' => 'Пронская',
                            'House' => '6',
                            'Korpus' => '1',
                            'Flat' => '13',
                        ),
                    'FactAddress' =>
                        array (
                            'Resident' => '1',
                            'Index' => '109145',
                            'Country' => 'Россия',
                            'Region' => 'г. Москва',
                            'CityKLADR' => '7700000000000',
                            'Street' => 'Пронская',
                            'House' => '6',
                            'Korpus' => '1',
                            'Flat' => '13',
                        ),
                    'Phone' => '79000000000',
                ),
            'DriversCount' => '1',
            'FullDriversInfo' =>
                array (
                    'FullDriver' =>
                        array (
                            'DOB' => '1961-11-07T00:00:00',
                            'Surname' => 'Шевыркова',
                            'Name' => 'Татьяна',
                            'Patronymic' => 'Борисовна',
                            'Sex' => '1',
                            'DriverLicence' =>
                                array (
                                    'Series' => '77УЕ',
                                    'Number' => '195838',
                                ),
                            'ExpertienceStart' => '1992-12-31T00:00:00',
                            'IsDLForeign' => false,
                            'OldDriverLicence' =>
                                array (
                                    'Series' => '',
                                    'Number' => '',
                                ),
                        ),
                ),
        );

        echo $Name;
        $result = self::$soap_client->OSAGOFullCalculation($req);
        $json = json_encode($result);

        print_r(" ОТВЕТ: ".$json. "\n");

        $array = json_decode($json,true);
        $err = $array['Error'];
        echo $err ."\n";

        $this->assertEmpty($err, $err);

    }

}


