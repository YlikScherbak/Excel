<?php

namespace App\Http\Controllers;

use App\ExcelData;
use DB;
use Illuminate\Http\Request;
use Excel;
use Illuminate\Support\Collection;
use Session;
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

    public function info() {
        return view('excel.info');
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
        $currency = DB::select("SELECT id, code, manufacturer FROM s_currencies");

        return view('excel.excel_table',
            ['data' => array_slice($excel, 0, 15), 'manufacturer' => $manufacturer, 'currency' => $currency]);
    }


    public function previewExcel(Request $request)
    {

        $excel = json_decode(Storage::disk('excel')->get(session('file_name')));

        $articul = $request->get('articul');
        $price = $request->get('price');
        $manufacturer = $request->get('manufacturer');
        $currency = $request->get('currency');
        $currencyCol = $request->get('currency_col');
        $surcharge = is_null($request->get('surcharge')) ? 1 : ($request->get('surcharge') / 100 + 1);

        Session::put('surcharge', $surcharge);

        if ($request->get('old')) {
            session(['old' => true]);
        } else {
            session(['old' => false]);
        }

        $error = false;

        $currency_responce = '';

        if (!is_null($currency) && is_null($currencyCol)) {
            $code = DB::table('s_currencies')->where('id', '=', $currency)->get()->first()->code;
            $currency_responce = 'Валюта : ' . $code;
        } elseif (!is_null($currencyCol) && is_null($currency)) {
            $currency_responce = 'Тип валюты указан в столбце ' . $currencyCol;
        } elseif (is_null($currency) && is_null($currencyCol)) {
            $currency = DB::table('s_currencies')->where('code', '=', 'UAH')->get()->first()->id;
            $currency_responce = 'Вы не указали валюту. Поэтому будет выбрана гривна.';
        } elseif (!is_null($currency) && !is_null($currencyCol)) {
            $currency_responce = 'Ошибка. Вы указали столбец и тип валюты. Выберите что то одно.';
            $error = true;
        }

        $collection = new Collection();

        $currencies = $this->getAllCurrencies();
        $currencies['грн'] = DB::table('s_currencies')->where('code', '=', 'UAH')->get()->first()->id;

        //Создание коллекции всех записей екселины
        if (!is_null($currency)) {
            foreach ($excel as $v) {
                if ($this->checkRow($v, $articul, $price)) {
                    $collection->push(new ExcelData($v[$articul], $v[$price], $manufacturer, $currency));
                }
            }
        } elseif (!is_null($currencyCol)) {
            foreach ($excel as $v) {
                if ($this->checkRow($v, $articul, $price)) {
                    $collection->push(new ExcelData($v[$articul], $v[$price], $manufacturer, $v[$currencyCol]));
                }
            }
        }

        $tableRow = [];

        $id = $collection->map(function ($e) {
            return $e->sku;
        })->all();

        //Выбока с базы всех совпадений
        $tableRow = DB::table('s_variants')->whereIn('sku', $id)
            ->where('manufacturer', '=', $manufacturer)
            ->get();

        //Фильтрация коллекции только по совпадающим артикулам
        $rowSku = $tableRow->map(function ($row) {
            return $row->sku;
        })->all();

        $collection = $collection->filter(function ($data) use ($rowSku) {
            return in_array($data->sku, $rowSku);
        });

        //Устанавливаю валюту в зависимоти от той которая указана в колонке
        if (!is_null($currencyCol) && is_null($currency)) {
            foreach ($collection as $col) {
                $col->currency_id = $currencies[$col->currency_id];
            }
        }

        Session::put('excel_data', $collection);

        return view('excel.preview', ['error' => $error, 'excelRow' => count($id), 'tableRow' => $tableRow->count(),
            'currency' => $currency_responce, 'manufacturer' => $manufacturer, 'surcharge' => $surcharge]);
    }


    /**
     * Обновление БД.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateExcel()
    {
        $data = Session::get('excel_data');

        $surcharge = Session::get('surcharge');

        if (session('old')) {
            $this->updateWithOld($data, $surcharge);
        } else {
            $this->update($data, $surcharge);
        }

        Storage::disk('excel')->delete(session('file_name'));
        Session::flush();

        return redirect(route('index_excel'))->with('message', 'Обновление БД завершено');
    }

    /**
     * Отменна обновления БД. Файл з данными удаляеться, а сессия очищаеться.
     * Редирект на начальную страницу.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function cancel()
    {
        Storage::disk('excel')->delete(session('file_name'));
        Session::flush();
        return redirect(route('index_excel'));
    }

    public function dbtest()
    {
    }


    /**
     * Обновление БД без учета старой цены
     * @param $data - Collection of ExcelData
     * @param $surcharge - float
     */
    private function update($data, $surcharge) {
        foreach ($data->all() as $row) {
           $req =  DB::table('s_variants')
                ->where([['sku', '=', (string) $row->sku], ['manufacturer', '=', $row->manufacturer]]);
                $req->update([
                    'price' =>  (integer)$row->price * (integer) $surcharge,
                    'currency' => (integer)$row->currency_id
                ]);
        }
    }

    /**
     * Обновление БД с учетом старой цены. Если старая цена меньше, то она записываеться в compare_price
     * @param $data - Collection of ExcelData
     * @param $surcharge - float
     */
    private function updateWithOld($data, $surcharge) {

        foreach ($data->all() as $row) {
            $oldPrice = DB::table('s_variants')
                ->where([['sku', '=', $row->sku], ['manufacturer', '=', $row->manufacturer]])
                ->get()->first()->price;
            if ($oldPrice < $row->price) {
                DB::table('s_variants')
                    ->where([['sku', '=', $row->sku], ['manufacturer', '=', $row->manufacturer]])
                    ->update([
                        'price' => $row->price * $surcharge,
                        'currency' => $row->currency_id,
                        'compare_price' => $oldPrice
                    ]);
            } else {
                $this->update($data, $surcharge);
            }
        }

    }

    /**
     *
     * @param $row - Строка записи из экселя
     * @param $articul - позиция артикула в массиве
     * @param $price - позиция цены в массиве
     * @return bool
     */
    private function checkRow($row, $articul, $price)
    {
        if (is_null($row[$articul])) {
            return false;
        } elseif (is_null($row[$price]) || !is_numeric($row[$price])) {
            return false;
        }
        return true;
    }

    private function checkPriceDifference($tableRow, $collection)
    {

    }

    /**
     * Функция берёт из БД все записи по валюте и возвращает массив
     * @return mixed
     */
    private function getAllCurrencies()
    {
        $currencies = DB::table('s_currencies')->where('manufacturer', '=', '')->get();
        return array_reduce($currencies->map(function ($e) {
            return array($e->code => $e->id);
        })->all(), 'array_merge', array());
    }

}
































