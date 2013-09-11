<?php

namespace docspad\client\php;

error_reporting(E_ALL);

require_once __DIR__.'/../../lib/php/lib/Thrift/ClassLoader/ThriftClassLoader.php';

use Thrift\ClassLoader\ThriftClassLoader;

$GEN_DIR = realpath(dirname(__FILE__).'/..').'/gen-php';

$loader = new ThriftClassLoader();
$loader->registerNamespace('Thrift', __DIR__ . '/../lib/php/lib');
$loader->registerDefinition('docspad\client', $GEN_DIR);
$loader->register();

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;
use Thrift\Transport\THttpClient;
use Thrift\Transport\TBufferedTransport;
use Thrift\Exception\TException;

class DocspadClient{
    
    private $client;
    private $transport;
    
    public function __construct(){
        echo 'entered constructor...';

        $socket = new THttpClient('localhost', 8080, '/php/PhpServer.php');
        $this->transport = new TBufferedTransport($socket, 1024, 1024);
        $protocol = new TBinaryProtocol($this->transport);
        $this->client = new \docspad\client\DocspadClient($protocol);
        echo 'left constructor...';

    
    }
    
    public function __destruct(){
        $this->transport->close();
    }
    
    public function upload($filename){
        $file = __DIR__ .'/client-dir/'.$filename;
        $io = new \docspad\client\InvalidOperation();
        if(!file_exists($file)){
            $io->what =\docspad\client\DocspadOperation::UPLOAD;
            $io->why = "The given file does not exist on your client";
            throw $io;
        }
        $contents = file_get_contents($file);
        
        $doc = new \docspad\client\Document();
        $doc->filename = $filename;
        $doc->contents= $contents;
        
        return $this->client->upload($doc);
    }
    
    public function download($filename){
        $file = __DIR__ .'/client-dir/'.$filename;
        $contents = $this->client->download($filename);
        file_put_contents($file, $contents);
        return 1;
    }

}
    
try {

    $PhpClient = new DocspadClient();
   // $PhpClient->upload("people.txt");
    $PhpClient->download("people.txt");
    
} catch (TException $tx) {
  print 'TException: '.$tx->why."\n";
}

?>
