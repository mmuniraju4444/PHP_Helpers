<?php
/**
 * Created by
 * User: Manjunath Muniraju
 * Date: 22/11/17
 */

namespace Mmuniraju4444\PHP\Helpers;

class CSVReader
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $delimiter = ",";

    /**
     * @var array
     */
    protected $header = [];

    /**
     * @var bool
     */
    protected $process_header = true;

    /**
     * @var int
     */
    protected $header_count = 0;

    /**
     * @var array
     */
    protected $row;

    /**
     * @var resource
     */
    protected $file;

    /**
     * @var bool
     */
    protected $ignore_first_row = false;

    /**
     * CSVReader constructor.
     * @param string $path
     */
    public function __construct($path = "")
    {
        $this->path = $path;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path = "")
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return \Generator
     */
    public function getCSVData()
    {
        $this->openFile();

        return $this->processData();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    private function openFile()
    {
        $this->closeFile()->validatePath()->preOpenConfigurations();

        $this->file = fopen($this->path, "r");

        $this->validResources();

        return $this;
    }

    /**
     * @return $this
     */
    private function preOpenConfigurations()
    {
        // recognizing the line endings when reading files
        ini_set('auto_detect_line_endings', TRUE);

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    private function validatePath()
    {
        if (empty($this->path) && !file_exists($this->path) && is_readable($this->path)) {
            throw new \Exception("Error In Opening File.");
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function closeFile()
    {
        if ($this->file) {
            $this->preCloseConfigurations();
            fclose($this->file);
        }

        $this->file = null;

        return $this;
    }

    /**
     * @return $this
     */
    private function preCloseConfigurations()
    {
        ini_set('auto_detect_line_endings', FALSE);

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    private function validResources()
    {
        if ($this->file == false) {
            throw new \Exception("Error In Creating File Object.");
        }

        return $this;
    }

    /**
     * @return \Generator
     */
    private function processData()
    {
        if ($this->process_header) {
            $this->initHeader();
        }

        if ($this->ignore_first_row) {
            fgetcsv($this->file, 0, $this->delimiter);
        }

        while (($this->row = fgetcsv($this->file, 0, $this->delimiter)) !== false) {
            if (empty($this->row)) {
                continue;
            }
            $this->getRow();
            yield $this->row;
        }

        $this->closeFile();
    }

    /**
     * @return $this
     */
    private function initHeader()
    {
        if (empty($this->header)) {
            $this->header = fgetcsv($this->file, 0, $this->delimiter);
            array_walk_recursive($this->header, array($this, 'convert_header_to_keys'));
        }

        $this->header_count = count($this->header);

        return $this;
    }

    /**
     * @return $this
     */
    private function getRow()
    {
        $this->getFormattedRow();

        return $this;
    }

    /**
     * @return $this
     */
    private function getFormattedRow()
    {
        $row_count = count($this->row);
        if ($this->process_header && (count($this->row) == $this->header_count)) {
            $this->row = array_combine($this->header, $this->row);
        } elseif ($this->process_header && ($row_count < $this->header_count)) {
            $this->row = array_combine(array_slice($this->header, 0, $row_count), $this->row);
        } else {
            $this->row = array_combine($this->header, array_slice($this->row, 0, $this->header_count));
        }

        return $this;
    }

    /**
     * @param $value
     * @param $key
     */
    function convert_header_to_keys(&$value, $key)
    {
        $value = str_replace(' ', '_', strtolower($value));
    }

    /**
     * @param string $delimiter
     * @return $this
     */
    public function setDelimiter($delimiter = ',')
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * @param bool $bool
     * @return $this
     */
    public function ignoreFirstRow($bool = true)
    {
        $this->ignore_first_row = $bool;

        return $this;
    }

    /**
     * @param $header
     * @return $this
     */
    public function setHeader($header)
    {
        $this->header = $header;
        $this->processHeader();

        return $this;
    }

    /**
     * @param bool $bool
     * @return $this
     */
    public function processHeader($bool = true)
    {
        $this->process_header = $bool;

        return $this;
    }

}
