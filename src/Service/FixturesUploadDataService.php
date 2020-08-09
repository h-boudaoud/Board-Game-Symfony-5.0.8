<?php


namespace App\Service;


class FixturesUploadDataService
{
    public function dataFormatting($dataObject, Object $dataclass, string $nameClass)
    {
    // dd(['array'=> $dataObject, 'Object' => $dataclass, 'string'=> $nameClass]);
        foreach ($dataObject as $key => $value) {

            $key = $key != 'id' ? $key : $nameClass.'_Id';

            if ($key == "name" && empty($value)) {
                $value = ($dataObject->names && Count($dataObject->names) > 0) ? $dataObject->names[0] : $dataObject->id;
                $dataObject->name = $value;
            }
            $dynamicMethodName = "set" . str_replace(
                    ' ', '',
                    ucwords(
                        strtolower(
                            str_replace('_', ' ', $key)
                        )
                    )
                );

            if (method_exists($dataclass, $dynamicMethodName) && $value) {

                try {
                    $dataclass->$dynamicMethodName($value);
                } catch (\Exception $exception) {
                    dump([
                        'Error message : '=>$exception->getMessage(),
                        'dynamicMethodName'=>$dynamicMethodName,
                        'value'=>$value
                    ]);
                }
            }

        }


    }
    public function uploadDataFromApi(string $url,string $nameData,?string $folder=''): ?array
    {
        $data =[];
        $folder = getcwd() . '\public\asset\js\jsonData' . $folder;

        try {
            $data = json_decode(file_get_contents($url))->$nameData;
            dump("Success upload ".Count($data)." $nameData json data: $url");
        } catch (\Exception $exception) {
            dump("Error upload $nameData json data: $url");
            dump("Error message : ".$exception->getMessage());
        }

        return $this->saveOrUploadData($folder,$nameData, $data);
    }

    private function saveOrUploadData(string $folder,string $nameData, ?array  $array=[]): ?array
    {

        dump(Count($array));
        $filename = $folder . '\\' . $nameData . '.json';

        if (Count($array)) {

            try{
                unlink($filename);
            }catch(\Exception $e){
                dump("$filename not Exist");
                // $fileData = fopen($filename, 'a+');
            }

            if (!is_dir($folder)) {
                if ($this->create_dir($folder)) {
                    echo "Create new folder $folder";
                } else {
                    echo "Error : Create new folder $folder";
                }
            }


            $fileData = fopen($filename, 'a+');
            fputs($fileData, json_encode($array));
            fclose($fileData);
        } else if(is_file ($filename)){
            dump("use the data from the $nameData.json file");
            $array = json_decode(file_get_contents($filename));
        }

        return $array;
    }

    private function create_dir($folder) {
        if (!empty($folder) && !is_dir($folder)) {
            if ($this->create_dir(dirname($folder))) {
                return mkdir($folder);
            } else {
                return false;
            }
        } else {
            return true;
        }
    }




}
