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
    
    if (php_sapi_name() == 'cli') {
        ini_set("display_errors", "stderr");
    }
    
    use Thrift\Protocol\TBinaryProtocol;
    use Thrift\Transport\TPhpStream;
    use Thrift\Transport\TBufferedTransport;
    
    class DocspadHandler implements \docspad\client\DocspadIf {
        
        public function download($filename){
            $file = 'server-dir/'.$filename;
            $io = new \docspad\client\InvalidOperation();
            if(!file_exists($file)){
                $io->what =\docspad\client\DocspadOperation::DOWNLOAD;
                $io->why = "The given file does not exist on the server";
                throw $io;
            }
            $contents = file_get_contents($file);
            return $contents;
        }
        
        public function upload(\docspad\client\Document $uploaded) {
            $file = 'server-dir/'.$uploaded->filename;
            $contents = $uploaded->contents."\n";
            file_put_contents($file, $contents);
            return $uploaded->filename;
        }
        
        public function ping(){
            return 1;
        }
        
    };
    
    //header('Content-Type', 'application/x-thrift');
    if (php_sapi_name() == 'cli') {
        echo "\r\n";
    }
    
    $handler = new DocspadHandler();
    $processor = new \docspad\client\DocspadProcessor($handler);
    
    
    $transport = new TBufferedTransport(new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W));
    $protocol = new TBinaryProtocol($transport, true, true);
    
    $transport->open();
    $processor->process($protocol, $protocol);
    $transport->close();