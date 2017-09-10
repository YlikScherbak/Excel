<?php

namespace App\Http\Controllers;

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
        return view('excel');
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
                if (is_null($v)){
                    $counter++;
                }
            }
            return ($counter > ($lenght / 2 )) ? false : true;
        });

        $data = json_encode($excel);
        $fileName = time() . '_datafile.json';
        Storage::disk('excel')->put($fileName, $data);

        session(['file_name' => $fileName, 'columns' => $this->arrayAlias[$highestColumn]]);

        return view('excel_table', ['data' => array_slice($excel, 0, 15)]);
    }


    public function updateExcel(Request $request)
    {

//        $max = session('columns');

    // TODO Нужно подумать нужна ли здесь валидация. Или же передалать предыдущий метод. Т.к. при обратном возвращение весь код отработает снова.
//        $this->validate($request, [
//            'articul' => 'required|min:0|max:' . $max,
//            'price' => 'required|min:0|max:' . $max,
//            'currency_default' => 'required_without:currency',
//            'currency' => 'required_without:currency_default',
//        ],[
//            'articul.required' => 'Поле артикула должно быть заполнено',
//            'price.required' => 'Поле цены должно быть заполнено',
//            'currency_default.required_without' => 'Поле валюто должно быть заполнено, если вы не указываете тип валюты',
//            'currency.required_without' => 'Поле тип валюты должно быть заполнено, если вы не указываете столбец с валютой'
//        ]);


        $excel = json_decode(Storage::disk('excel')->get(session('file_name')));

        $articul = $request->get('articul');
        $price = $request->get('price');
        $currency = is_null($request->get('currency')) ? $request->get('currency_default') : $request->get('currency');


        foreach ($excel as $v) {
            if ($this->checkRow($v, $articul, $price, $currency)) {
                var_dump('update hren(a) where articul=' . $v[$articul] . ' set(' . $v[$price] . ')');
            }
        }
        Storage::disk('excel')->delete(session('file_name'));
        $request->session()->forget(['file_name', 'columns']);

    }


    //Проверка отработа вроде нормально на обоих екселинах. Осталось подумать что бы ищё добавить
    private function checkRow($row, $articul,$price, $currency){
        if (is_null($row[$articul])){
            return false;
        } elseif (is_null($row[$price]) || !is_numeric($row[$price])){
            return false;
        }
        return true;
    }
}
































