<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Excel;
use Storage;

class ExcelController extends Controller
{
    public function __construct()
    {
        //Масив алиасов колонок. Так как дополнение возвращает не цифру а букву
        $this->arrayAlias = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10,
            'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20,
            'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26];
    }


    public function index()
    {
        return view('excel.excel');
    }

    public function getExcel(Request $request)
    {
        $excels = $request->file('file');

        $excel = [];
        $highestColumn = 0;
        Excel::load($excels, function ($reader) use (&$excel, &$highestColumn) {
            $objExcel = $reader->getExcel();
            $sheet = $objExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            //  Loop through each row of the worksheet in turn
            for ($row = 1; $row <= $highestRow; $row++) {
                //  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

                $excel[] = $rowData[0];
            }
        });

        $excel = array_filter($excel, function ($value) {
            $counter = 0;
            $lenght = count($value);
            foreach ($value as $v) {
                if (is_null($v)) {
                    $counter++;
                }
            }
            return ($counter > ($lenght / 2)) ? false : true;
        });

        $data = json_encode($excel);
        $fileName = time() . '_datafile.json';
        Storage::disk('excel')->put($fileName, $data);

        session(['file_name' => $fileName, 'columns' => $this->arrayAlias[$highestColumn]]);

        $manufacturer = DB::select('SELECT DISTINCT (manufacturer) FROM s_variants WHERE manufacturer IS NOT NULL ORDER BY manufacturer');
        $currency = DB::select("SELECT code FROM s_currencies WHERE manufacturer = ''");

        return view('excel.excel_table',
            ['data' => array_slice($excel, 0, 15), 'manufacturer' => $manufacturer, 'currency' => $currency]);
    }


    public function updateExcel(Request $request)
    {
//        dump($request);

        $excel = json_decode(Storage::disk('excel')->get(session('file_name')));

        $articul = $request->get('articul');
        $price = $request->get('price');
        $currency = $this->getCurrency($request->get('currency'), $request->get('manufacturer'));
        $manufacturer = $request->get('manufacturer');

        $id = [];

//        var_dump($excel);
        //Проверка каждой строки и выборка id для последуешего запроса
        foreach ($excel as $v) {
            if ($this->checkRow($v, $articul, $price)) {
                $id[strval($v[$articul])] = $v[$price];
            }
        }

        $tableRow = [];

        if (is_null($manufacturer) || $manufacturer === '---') {
            $tableRow = DB::table('s_variants')->whereIn('sku', array_keys($id))
                ->get();
        } else {
            $tableRow = DB::table('s_variants')->whereIn('sku', array_keys($id))
                ->where('manufacturer', '=', $request->get('manufacturer'))
                ->get();
        }


//        foreach ($tableRow->all() as $row){
//            var_dump($row);
//            DB::table('s_variants')
//                ->where('sku', strval($row->sku))
//                ->update(['price' => (strval($id[$row->sku]) * $currency)]);
//        }

        Storage::disk('excel')->delete(session('file_name'));
        $request->session()->forget(['file_name', 'columns']);



        return view('excel.result',['result' => 'Заебись', 'excelRow' => count($id), 'tableRow' => $tableRow->count()]);
    }



    public function dbtest()
    {

        $curr = DB::table('s_variants')->where('sku', '=', '14521020-004')->get();

        var_dump($curr);

    }


    private function checkRow($row, $articul, $price)
    {
        if (is_null($row[$articul])) {
            return false;
        } elseif (is_null($row[$price]) || !is_numeric($row[$price])) {
            return false;
        }
        return true;
    }

    /**
     * Выбор валюты
     * Если валюта не указана или UAH то возвращаеться 1.
     * Если указан производитель для которого зарезервирован собственный тип валюты возвращаеться текущий курс по производителю.
     * В остальных случаях возвращаеть стандартный курс валюты.
     * @param $currency
     * @param $manufacturer
     * @return int
     */
    private function getCurrency($currency, $manufacturer)
    {

        $curr = DB::select("SELECT rate_to FROM s_currencies WHERE manufacturer = ?", [$manufacturer]);
        if (empty($curr)) {
            if ($currency === 'UAH' || is_null($currency)) {
                return 1;
            } else {
                return DB::select("SELECT rate_to FROM s_currencies WHERE code = ? LIMIT 1", [$currency])[0]->rate_to;
            }
        }

        return $curr[0]->rate_to;
    }
}
































