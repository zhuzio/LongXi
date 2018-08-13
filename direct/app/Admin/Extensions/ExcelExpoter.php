<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExpoter extends AbstractExporter {
	protected $filename;
	protected $head = [];
	protected $body = [];
	public function setAttr($head, $body, $filename) {
		$this->filename = $filename;
		$this->head = $head;
		$this->body = $body;
	}
	public function export() {
		Excel::create($this->filename, function ($excel) {

			$excel->sheet('sheet', function ($sheet) {
				// 这段逻辑是从表格数据中取出需要导出的字段
				$head = $this->head;
				$body = $this->body;
				$bodyRows = collect($this->getData())->map(function ($item) use ($body) {
					foreach ($body as $keyName) {
						$arr[] = ' '.array_get($item, $keyName);
					}
					return $arr;
				});
				$rows = collect([$head])->merge($bodyRows);
				$sheet->rows($rows);

			});

		})->export('xls');
	}
}