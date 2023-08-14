<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EmotionsExport implements FromView
{
    protected $data;
    protected $userName;
    public function __construct($data, $userName)
    {
        $this->data = $data;
        $this->userName = $userName;
    }
    public function view(): View
    {
        return view('excel.emotions', [
            'data' => $this->data,
            'user_name' => $this->userName
        ]);
    }
}
