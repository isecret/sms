<?php
namespace Isecret\Sms;

use GuzzleHttp\Client;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Isecret\Sms\Exceptions\HttpException;

class Sms
{
    /**
     * 配置文件
     *
     * @var array
     */
    protected $config = [
        'timeout' => 5.0,
        'account' => [
            'spcode' => null,
            'loginname' => null,
            'password' => null
        ],
        'log_path' => '/tmp/sms.log'
    ];
    
    /**
     * Guzzle 可选请求参数
     *
     * @var array
     */
    protected $guzzleOptions = [];

    /**
     * API 接口地址
     *
     * @var string
     */
    protected $url = 'http://sms.api.ums86.com:8899/sms/Api/Send.do';

    /**
     * 收信人
     *
     * @var string
     */
    protected $to;

    /**
     * 短信内容
     *
     * @var string
     */
    protected $contents;
    
    /**
     * 构造函数
     *
     * @param array $config
     * @param array $guzzleOptions
     */
    public function __construct(Array $config, $guzzleOptions=[])
    {
        $this->config = $config;
        $this->guzzleOptions = $guzzleOptions;
        
    }

    /**
     * 日志操作实例
     *
     * @return void
     */
    public function log()
    {
        $log = new Logger('sms');
        $log->pushHandler(new StreamHandler($this->config['log_path'], Logger::DEBUG));
        $log->pushHandler(new FirePHPHandler());

        return $log;
    }

    /**
     * 获取 Http 实例
     *
     * @return void
     */
    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    /**
     * 发送短信
     *
     * @param string $to
     * @param string $contents
     * @return void
     */
    public function send($to, $contents)
    {
        $this->to = $to;
        $this->contents = $contents;
        return $this->request();
    }

    /**
     * 发送短信 多人
     *
     * @param array $to
     * @param string $contents
     * @return void
     */
    public function sendMany(Array $to, $contents)
    {
        $toMany = join(',', $to);
        return $this->send($toMany, $contents);
    }

    /**
     * 请求发送接口
     *
     * @return void
     */
    public function request()
    {
        try {
            $response = $this->getHttpClient()->get($this->url, [
                'query' => $this->getQuery(),
                'timeout' => $this->config['timeout'],
            ])->getBody()->getContents();
            
            $response = iconv('gbk', 'utf-8', $response);
            parse_str($response, $result);

            $this->log()->info($result['description'], [
                'url' => $this->url,
                'to' => $this->to,
                'contents' => $this->contents,
                'response' => $response,
                'result' => $result
            ]);

            return $result;
        } catch(\Exception $e) {
            $this->log()->error($e->getMessage());
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
        
    }

    /**
     * 获取请求参数
     *
     * @return void
     */
    public function getQuery()
    {
        return [
            'SpCode' => $this->config['account']['spcode'],
            'LoginName' => $this->config['account']['loginname'],
            'Password' => $this->config['account']['password'],
            'MessageContent' => iconv('utf-8', 'gbk', $this->contents),
            'UserNumber' => $this->to,
            'SerialNumber' => time() . '' . substr(str_shuffle('0123456789'), 0, 9),
            'ScheduleTime' => date('YmdHis'),
            'ExtendaccountNum' => '3333',
            'f' => '1',
        ];
    }
}