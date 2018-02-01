# PHP_Helpers


#CSVReading.php
---

> Usage :
* Create Object of CSVReader
```PHP
 $csv = new CSVReader();
```

```PHP
 $csv = new CSVReader("/path/to/csv/file");
```

* Get CSV Data
```PHP
foreach ($csv->setPath("/path/to/csv/file")->getCSVData() as $row) {
    //Process $row here
}
```

```PHP
foreach ($csv->getCSVData() as $row) {
    //Process $row here
}
```

* Custom Delemiter
```PHP
$csv->setDelimiter(";");
```

* Ignore Header Processing.

```PHP
$csv->processHeader(false)
```

* Set Custom Header
```PHP
$csv->ignoreFirstRow()->setHeader(["row_1","row_2","row_3"]);
```

*Note* : $row is an key => value Array, here key is set as the first row of the CSV file.


> Example-1.

| row 1 | row2 | row 3 4 |
| --- | --- | --- |
| data1 | data2 | data3 |

_CODE_
```PHP

$csv = new CSVReader();
foreach ($csv->setPath("/tmp/sample.csv")->getCSVData() as $row) {
    print_r($row)
}
```
_OUTPUT_
```PHP
Array (
[row_1] => data1
[row2] => data2
[row_3_4] => data3
)
```

> Example-2.

| row 1 | row2 | row 3 4 |
| --- | --- | --- |
| data1 | data2 | data3 |

_CODE_
```PHP

$csv = new CSVReader("/tmp/sample.csv");
foreach ($csv->ignoreFirstRow()->setHeader(["row_1","row_2","row_3"])->getCSVData() as $row) {
    print_r($row)
}
```
_OUTPUT_
```PHP
Array (
[row_1] => data1
[row_2] => data2
[row_3] => data3
)
```

