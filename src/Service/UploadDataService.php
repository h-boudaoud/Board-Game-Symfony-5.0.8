<?php


namespace App\Service;


class UploadDataService
{

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
