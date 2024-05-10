<?php

/**
 * Get page data from an array by index.
 *
 * @param array $arr The input array.
 * @param string $index The index to retrieve.
 * @return mixed The value at the specified index, or an error message if the index doesn't exist.
 */
function getPageData(array $arr, string $index): mixed
{
    return $arr[$index] ?? "\$page_data does not contain an index of [$index]";
}

/**
 * Echo page data from an array by index.
 *
 * @param array $arr The input array.
 * @param string $index The index to retrieve.
 * @return void
 */
function echoPageData(array $arr, string $index): void
{
    echo $arr[$index] ?? "\$page_data does not contain an index of [$index]";
}

/**
 * Output a formatted trace of a variable.
 *
 * @param mixed $input The variable to trace.
 * @param string $varName The name of the variable (optional).
 * @return void
 */
function trace(mixed $input, string $varName = ''): void
{
    $html = "<div style='font-size:14px; background: #f4f4f4; border:1px solid #ddd; padding:10px; margin: 10px 0; line-height: 1.4;'>";

    $type = gettype($input);
    $html .= "<strong>Variable";
    if (!empty($varName)) {
        $html .= " ($varName)";
    }
    $html .= ":</strong> <span style='color: #888;'>($type)</span><br>";

    switch ($type) {
        case 'array':
            $html .= "<ul style='margin-top: 5px; padding-left: 20px;'>";
            foreach ($input as $key => $value) {
                $html .= "<li>";
                $html .= "<strong>" . htmlspecialchars((string)$key) . "</strong> =&gt; ";
                $html .= "<span style='color: #888;'>(" . gettype($value) . ")</span> ";
                if (is_array($value)) {
                    $html .= "Array (" . count($value) . ")";
                } elseif (is_object($value)) {
                    $html .= "Object (" . $value::class . ")";
                } elseif (is_string($value)) {
                    $html .= "'" . htmlspecialchars($value) . "'";
                } else {
                    $html .= htmlspecialchars(var_export($value, true));
                }
                $html .= "</li>";
            }
            $html .= "</ul>";
            break;

        case 'object':
            $className = $input::class;
            $html .= "<strong>Object ($className):</strong><br>";
            $html .= "<ul style='margin-top: 5px; padding-left: 20px;'>";
            $reflection = new ReflectionObject($input);
            $properties = $reflection->getProperties();
            foreach ($properties as $property) {
                $property->setAccessible(true);
                $value = $property->getValue($input);
                $html .= "<li>";
                $html .= "<strong>" . $property->getName() . "</strong> =&gt; ";
                $html .= "<span style='color: #888;'>(" . gettype($value) . ")</span> ";
                if (is_array($value)) {
                    $html .= "Array (" . count($value) . ")";
                } elseif (is_object($value)) {
                    $html .= "Object (" . $value::class . ")";
                } elseif (is_string($value)) {
                    $html .= "'" . htmlspecialchars($value) . "'";
                } else {
                    $html .= htmlspecialchars(var_export($value, true));
                }
                $html .= "</li>";
            }
            $html .= "</ul>";
            break;

        case 'string':
            $html .= "<span style='color: #080;'>&quot;" . htmlspecialchars($input) . "&quot;</span>";
            break;

        case 'integer':
        case 'double':
            $html .= "<span style='color: #00d;'>" . $input . "</span>";
            break;

        case 'boolean':
            $html .= "<span style='color: #800;'>" . ($input ? 'true' : 'false') . "</span>";
            break;

        case 'NULL':
            $html .= "<span style='color: #888;'>null</span>";
            break;

        default:
            $html .= "<span style='color: #000;'>" . htmlspecialchars(var_export($input, true)) . "</span>";
            break;
    }

    $html .= "</div>";

    echo $html;
}

/**
 * Output JSON-encoded data and exit.
 *
 * @param mixed $data The data to encode and output.
 * @return never
 */
function dj(mixed $data): never
{
    $response = match (gettype($data)) {
        'array' => $data,
        'object' => (array)$data,
        'resource' => ['resource_type' => get_resource_type($data)],
        'boolean' => ['boolean_value' => $data],
        'integer', 'double' => ['number_value' => $data],
        'string' => ['string_value' => $data],
        'NULL' => ['null_value' => null],
        default => ['unknown_type' => gettype($data)],
    };

    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit();
}

/**
 * Output a formatted var_dump of a variable.
 *
 * @param mixed $var The variable to dump.
 * @return void
 */
function vd(mixed $var): void
{
    ob_start();
    var_dump($var);
    $output = ob_get_clean();

    $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
    $output = '<pre style="background: #f4f4f4; border: 1px solid #ddd; padding: 10px; font-size: 14px; line-height: 1.4; margin: 10px 0;">' . htmlspecialchars($output, ENT_QUOTES) . '</pre>';

    echo $output;
}

/**
 * Output a formatted var_dump of a variable and exit.
 *
 * @param mixed $variable The variable to dump.
 * @return never
 */
function vdd(mixed $variable): never
{
    if (is_string($variable) && is_array(json_decode($variable, true)) && (json_last_error() === JSON_ERROR_NONE)) {
        header('Content-Type: application/json');
        echo json_encode(json_decode($variable), JSON_PRETTY_PRINT);
    } else {
        ob_start();
        var_dump($variable);
        $output = ob_get_clean();

        $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
        $output = '<pre style="background: #f4f4f4; border: 1px solid #ddd; padding: 10px; font-size: 14px; line-height: 1.4; margin: 10px 0;">' . htmlspecialchars($output, ENT_QUOTES) . '</pre>';

        echo $output;
    }

    exit();
}

/**
 * Output a formatted list of files and directories in a directory.
 *
 * @param string $dir The directory to dump.
 * @return void
 */
function dumpDir(string $dir): void
{
    $sd = scandir($dir);
    $html = "$dir<hr>";
    foreach ($sd as $value) {
        if (!in_array($value, [".", ".."])) {
            $html .= is_dir("$dir/$value") ? "$value [DIR]<br>" : "$value<br>";
        }
    }
    echo $html;
}

/**
 * Output information about uploaded files.
 *
 * @return void
 */
function dumpFileUploads(): void
{
    foreach ($_FILES as $key => $file) {
        trace("Uploaded: key: " . $key);
        trace("Uploaded: " . $file['name']);
        trace("Uploaded: " . $file['type']);
        trace("Uploaded: " . $file['size']);
        trace("Uploaded: " . $file['error']);
        trace("Uploaded: " . $file['tmp_name']);
        trace("");
    }
}