<?php

namespace Ideil\ImageMiner;

use Intervention\Image\ImageManagerStatic as Image;
use InvalidArgumentException;
use Symfony\Component\Debug\ExceptionHandler as SymfonyDisplayer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class Miner
{
    use TokenTrait;

    const LOCK_DIR_LIFETIME = 60; // in seconds

    /**
     * @var string
     */
    protected $requestChecksum;

    /**
     * @var string
     */
    protected $requestFilePath;

    /**
     * @var Closure|null
     */
    protected $requestFilePathResolver;

    /**
     * @var string
     */
    protected $clearUri;

    /**
     * @var array
     */
    protected $conversions = [];

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $uriRoot = '';

    /**
     * @var string
     */
    protected $handledFilesRoot = '';

    /**
     * @var string
     */
    protected $originalFilesRoot = '';

    /**
     * @var string
     */
    protected $pathRegexp;

    /**
     * @var array
     */
    protected $imgHandlingSettings;

    /**
     * @var bool
     */
    protected $isDebug = false;

    /**
     * @var callable
     */
    protected $cleanUriResolver;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var null|\Closure
     */
    protected $allowedConversionsChecker;

    /**
     * @param Request $request
     * @param string $pathRegexp
     * @param array $config
     */
    public function __construct(Request $request, $pathRegexp, $config = [])
    {
        $this->request = $request;
        $this->pathRegexp = $pathRegexp;

        $this->config = $config;

        $this->imgHandlingSettings = [
            'driver' => env('IMAGE_MINER_DRIVER', 'imagick'),
        ];
    }

    /**
     * @param Closure $requestFilePathResolver
     */
    public function setRequestFilePathResolver(Closure $requestFilePathResolver)
    {
        $this->requestFilePathResolver = $requestFilePathResolver;
    }

    /**
     * @param bool $isActive
     */
    public function setDevModeActivity($isActive)
    {
        $this->isDebug = $isActive;
    }

    /**
     * @param string $url
     * @return RedirectResponse
     * @throws InvalidArgumentException
     */
    public function getRedirectTo($url)
    {
        return new RedirectResponse($url);
    }

    /**
     * @param string $pattern
     * @param callable $handler
     *
     * @return mixed
     */
    public function uriMatch($pattern, callable $handler)
    {
        if (preg_match($pattern, $this->getCleanUri(), $matches)) {
            return $handler($this->getCleanUri(), $matches);
        }
    }

    /**
     * @return string
     */
    public function getCleanUri()
    {
        if ($this->clearUri !== null) {
            return $this->clearUri;
        }

        list($this->clearUri) = explode(
            '?',
            $this->request->getRequestUri()
        );

        $this->clearUri = $this->cleanUriResolve($this->clearUri);

        return $this->clearUri;
    }

    /**
     * @param string $uri
     * @return string
     */
    public function cleanUriResolve($uri)
    {
        if ($this->cleanUriResolver) {
            return \call_user_func($this->cleanUriResolver, $uri);
        }

        return $uri;
    }

    /**
     * @param string $pattern
     * @param callable $handler
     *
     * @return mixed
     */
    public function uriNotMatch($pattern, callable $handler)
    {
        if (! preg_match($pattern, $this->getCleanUri(), $matches)) {
            return $handler($this->getCleanUri(), $matches);
        }
    }

    /**
     * @param string $name
     * @param string $regexp
     * @param callable $handler
     */
    public function addConversion($name, $regexp, callable $handler)
    {
        $this->conversions[$name] = [$regexp, $handler];
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handle()
    {
        try {
            if ($this->getRequestChecksum()) {
                $uriRoot = rtrim(
                    str_replace(
                        '{checksum}',
                        $this->getRequestChecksum(),
                        $this->uriRoot
                    ),
                    '/'
                );

                // with trailling left slash
                if (strpos($this->getCleanUri(), $uriRoot) !== 0) {
                    throw new InvalidArgumentException('Wrong uri root');
                }

                $hashableUri = $this->getHashableUri();

                $uriPayload = substr($this->getCleanUri(), strlen($uriRoot));

                if (! $this->validateChecksum($hashableUri)) {
                    throw new InvalidArgumentException('Invalid checksum value');
                }
            }

            $realFilePath = rtrim($this->originalFilesRoot, '/')
                . '/'
                . ltrim($this->getRequestFilePath(), '/');

            if (! file_exists($realFilePath)) {
                throw new InvalidArgumentException('File not exists ' . $realFilePath);
            }

            Image::configure($this->imgHandlingSettings);

            $image = Image::make($realFilePath);

            foreach ($this->conversions as $name => $data) {
                if (! preg_match($data[0], $uriPayload, $matches)) {
                    continue;
                }

                if ($this->allowedConversionsChecker
                    && ! \call_user_func($this->allowedConversionsChecker, $name, $matches)
                ) {
                    throw new InvalidArgumentException('Not allowed conversion');
                }

                $image = $data[1]($image, $matches);
            }

            if (! $this->createLockDir()
                && $response = $this->checkLockDir($image)
            ) {
                return $response;
            }

            $this->save($image);

            $this->removeLockDir();

            $response = new Response('', Response::HTTP_OK, [
                'content-type' => $image->mime(),
                'x-accel-redirect' => $_SERVER['REQUEST_URI'],
            ]);

            return $response->prepare($this->request);
        } catch (\Exception $e) {
            if ($this->isDebug) {
                return (new SymfonyDisplayer($this->isDebug))->handle($e);
            }

            return new Response('404 Not found', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @return string
     */
    public function getRequestChecksum()
    {
        if ($this->requestChecksum !== null) {
            return $this->requestChecksum;
        }

        if (! preg_match('/.+\/([a-z\d]{6})(-)/', $this->getCleanUri(), $matches)) {
            throw new InvalidArgumentException('Invalid checksum format');
        }

        return $this->requestChecksum = $matches[1];
    }

    /**
     * @return string
     */
    public function getHashableUri()
    {
        $uri = str_replace(
            rtrim($this->uriRoot, '/'),
            '',
            $this->getCleanUri()
        );

        return str_replace(
            $this->getChecksumPartOfUrl(),
            '',
            $uri
        );
    }

    /**
     *
     * @return string
     */
    public function getChecksumPartOfUrl()
    {
        return $this->getRequestChecksum() . '-';
    }

    /**
     * @param string $uriPayload
     * @return bool
     * @throws \InvalidArgumentException
     */
    protected function validateChecksum($uriPayload)
    {
        $requesChecksum = $this->getRequestChecksum();

        if ($requesChecksum === $this->token6FromStr($uriPayload)) {
            return true;
        }

        if (! empty($this->config['external_tokens'])) {
            foreach ($this->config['external_tokens'] as $token => $callback) {
                if (! $callback instanceof \Closure) {
                    $token = $callback;
                    $callback = null;
                }

                if ($requesChecksum === $this->token6FromStr($uriPayload, $token)) {
                    $this->allowedConversionsChecker = $callback;

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getRequestFilePath()
    {
        if (null !== $this->requestFilePath) {
            return $this->requestFilePath;
        }

        if (! preg_match($this->pathRegexp, $this->getCleanUri(), $matches)) {
            throw new InvalidArgumentException('Invalid file path format');
        }

        $path = $matches[0];
        $withoutChecksum = str_replace(
            $this->getChecksumPartOfUrl(),
            '',
            $path
        );

        if ($this->requestFilePathResolver) {
            $this->requestFilePath = \call_user_func(
                $this->requestFilePathResolver,
                $withoutChecksum,
                $path
            ) ?: '';
        } else {
            $withoutCrop = preg_replace(
                '/(-?[a-z0-9]+)(x)(-?[a-z0-9]+)-(-?[a-z0-9]+)(x)(-?[a-z0-9]+)-/',
                '',
                $withoutChecksum
            );
            $withoutWatermark = str_replace('watermark-', '', $withoutCrop);

            $this->requestFilePath = $withoutWatermark;
        }

        return $this->requestFilePath;
    }

    /**
     * @return bool
     */
    public function createLockDir()
    {
        $path = $this->getLockDirPath();

        return @mkdir($path);
    }

    /**
     * @param  string $postfix
     * @return string
     */
    public function getLockDirPath($postfix = '')
    {
        $filename = $this->getRequestChecksum() . '-' . basename($this->getRequestFilePath()) . $postfix;

        return rtrim(sys_get_temp_dir(), '/')
            . '/'
            . ltrim($filename, '/');
    }

    /**
     * @param $image
     * @return bool|Response
     */
    public function checkLockDir($image)
    {
        while (true) {
            // sleep for 0.1 sec
            time_nanosleep(0, 100000000);

            if (! $this->isLockDirExists()) {
                if (! file_exists($this->getFileStorePath())) {
                    return false;
                }

                $response = new Response('', Response::HTTP_OK, [
                    'content-type' => $image->mime(),
                    'x-accel-redirect' => $_SERVER['REQUEST_URI'],
                ]);

                return $response->prepare($this->request);
            }

            clearstatcache(true, $this->getLockDirPath());

            $stats = stat($this->getLockDirPath());

            if (empty($stats['mtime'])) {
                return false;
            }

            if ((time() - $stats['mtime']) >= static::LOCK_DIR_LIFETIME) {
                $this->removeLockDir();

                return false;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isLockDirExists()
    {
        $path = $this->getLockDirPath();

        clearstatcache(true, $path);

        return is_dir($path);
    }

    /**
     * @return string
     */
    public function getFileStorePath()
    {
        return rtrim($this->handledFilesRoot, '/')
            . '/'
            . ltrim($this->getCleanUri(), '/');
    }

    /**
     * @return bool
     */
    public function removeLockDir()
    {
        $path = $this->getLockDirPath();
        @rmdir($path);
    }

    /**
     * @param Image $image
     */
    public function save($image)
    {
        if ($this->handledFilesRoot) {
            $fileStorePath = rtrim($this->handledFilesRoot, '/') . '/' . ltrim($this->getCleanUri(), '/');

            $fileStoreDir = \dirname($fileStorePath);

            if (! file_exists($fileStoreDir)) {
                mkdir($fileStoreDir, 0755, true);
            }

            $image->save($fileStorePath);
        }
    }

    /**
     * @param callable $callback
     */
    public function setCleanUriResolver($callback)
    {
        $this->cleanUriResolver = $callback;
    }

    /**
     * @param string $path
     */
    public function setHandledFilesRoot($path)
    {
        $this->handledFilesRoot = $path;
    }

    /**
     * @param string $path
     */
    public function setOriginalFilesRoot($path)
    {
        $this->originalFilesRoot = $path;
    }

    /**
     * @param string $path
     */
    public function setUriRoot($path)
    {
        $this->uriRoot = $path;
    }
}
