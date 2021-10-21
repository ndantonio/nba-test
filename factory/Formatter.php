<?php

class Formatter() 
{
	private $data;
	private $format;

	public function __construct($data, $format)
	{
		$this->data = $data;
		$this->format = $format;

		// return the right data format
        switch($this->format) {
            case 'xml':
                createXml();
                break;
            case 'json':
                createJson();
                break;
            case 'csv':
                createCsv();
                break;
            default: // html
                createHtml();
                break;
        }
	}

    private function createXml()
    {
    	header('Content-type: text/xml');
                
        // fix any keys starting with numbers
        $keyMap = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
        $xmlData = [];
        foreach ($this->data->all() as $row) {
            $xmlRow = [];
            foreach ($row as $key => $value) {
                $key = preg_replace_callback('(\d)', function($matches) use ($keyMap) {
                    return $keyMap[$matches[0]] . '_';
                }, $key);
                $xmlRow[$key] = $value;
            }
            $xmlData[] = $xmlRow;
        }
        $xml = Array2XML::createXML('data', [
            'entry' => $xmlData
        ]);

        return $xml->saveXML();
    }

    private function createJson()
    {
    	header('Content-type: application/json');
        return json_encode($this->data->all());
    }

    private function createCsv()
    {
    	header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="export.csv";');
        if (!$this->data->count()) {
            return;
        }
        $csv = [];
        
        // extract headings
        // replace underscores with space & ucfirst each word for a decent headings
        $headings = collect($this->data->get(0))->keys();
        $headings = $headings->map(function($item, $key) {
            return collect(explode('_', $item))
                ->map(function($item, $key) {
                    return ucfirst($item);
                })
                ->join(' ');
        });
        $csv[] = $headings->join(',');

        // format data
        foreach ($this->data as $dataRow) {
            $csv[] = implode(',', array_values($dataRow));
        }

        return implode("\n", $csv);
    }

    private function createHtml()
    {
    	if (!$this->data->count()) {
            return $this->htmlTemplate('Sorry, no matching data was found');
        }
        
        // extract headings
        // replace underscores with space & ucfirst each word for a decent heading
        $headings = collect($this->data->get(0))->keys();
        $headings = $headings->map(function($item, $key) {
            return collect(explode('_', $item))
                ->map(function($item, $key) {
                    return ucfirst($item);
                })
                ->join(' ');
        });
        $headings = '<tr><th>' . $headings->join('</th><th>') . '</th></tr>';

        // output data
        $rows = [];
        foreach ($this->data as $dataRow) {
            $row = '<tr>';
            foreach ($dataRow as $key => $value) {
                $row .= '<td>' . $value . '</td>';
            }
            $row .= '</tr>';
            $rows[] = $row;
        }
        $rows = implode('', $rows);

        return $this->htmlTemplate('<table>' . $headings . $rows . '</table>');
    }

    // wrap html in a standard template
    public function htmlTemplate($html) {
        include('../templates/export.php');
    }
}