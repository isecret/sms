<h1 align="center"> sms </h1>

<p align="center">E易信通短信 SDK。</p>


## Installing

```shell
$ composer require isecret/sms -vvv
```

## Usage

```
use Isecret\Sms\Sms;

$config = [
    'timeout' => 5.0,
    'account' => [
            'spcode' => null,
            'loginname' => null,
            'password' => null
        ],
    'log_path' => '/tmp/sms.log'
];

$sms = new Sms($config);

$sms->send('18888888888', '您的验证码是 123456，10 分钟内有效');
```



## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/isecret/sms/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/isecret/sms/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT
