<?php declare(strict_types=1);

namespace Plugin\S3Proto;

use App\Domain\AbstractPlugin;
use App\Domain\Entities\File;
use App\Domain\Service\File\FileService;
use Psr\Container\ContainerInterface;

class S3ProtoPlugin extends AbstractPlugin
{
    const NAME = 'S3ProtoPlugin';
    const TITLE = 'Облачное хранилище [S3]';
    const DESCRIPTION = 'Интеграция с облачным хранилищем по протоколу Amazon S3';
    const AUTHOR = 'Aleksey Ilyin';
    const AUTHOR_SITE = 'https://getwebspace.org';
    const VERSION = '1.0';

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->addSettingsField([
            'label' => 'Сервис',
            'type' => 'text',
            'name' => 'endpoint',
            'args' => [
                'placeholder' => 's3.amazonaws.com',
            ],
        ]);
        $this->addSettingsField([
            'label' => 'Access Key',
            'type' => 'text',
            'name' => 'access_key',
        ]);
        $this->addSettingsField([
            'label' => 'Secret Key',
            'type' => 'text',
            'name' => 'secret_key',
        ]);
        $this->addSettingsField([
            'label' => 'Контейнер',
            'type' => 'text',
            'name' => 'bucket',
        ]);
        $this->addSettingsField([
            'label' => 'Контейнер ID',
            'type' => 'number',
            'name' => 'id',
        ]);
        $this->addSettingsField([
            'label' => 'Регион',
            'type' => 'text',
            'name' => 'region',
        ]);
    }

    public function getS3Client(): \Aws\S3\S3Client
    {
        static $client;

        if (!$client) {
            require_once __DIR__ . '/vendor/autoload.php';
            $client = new \Aws\S3\S3Client([
                'version' => 'latest',
                'region' => $this->parameter('S3ProtoPlugin_region'),
                'use_path_style_endpoint' => true,
                'credentials' => [
                    'key' => $this->parameter('S3ProtoPlugin_access_key'),
                    'secret' => $this->parameter('S3ProtoPlugin_secret_key'),
                ],
                'endpoint' => 'https://' . $this->parameter('S3ProtoPlugin_endpoint'),
            ]);
        }

        return $client;
    }

    public function putObject($key, $body)
    {
        return $this->getS3Client()->putObject([
            'Bucket' => $this->parameter('S3ProtoPlugin_bucket'),
            'Key' => $key,
            'Body' => $body,
        ]);
    }

    public function getObject($key)
    {
        return $this->getS3Client()->getObject([
            'Bucket' => $this->parameter('S3ProtoPlugin_bucket'),
            'Key' => $key,
        ]);
    }

    public function putFileAndGetURL(File $file)
    {
        $result = $this->putObject($file->getSalt() . '-' . $file->getFileName(), $file->getResource());

        return str_replace('s3.', $this->parameter('S3ProtoPlugin_id', '') . '.', $result->get('ObjectURL'));
    }
}
