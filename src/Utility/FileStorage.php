<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/12/24
 * Time: 9:44 PM
 */

namespace WanJing\Ys7\Utility;


use WanJing\Ys7\AbstractInterface\StorageInterface;

class FileStorage implements StorageInterface
{
    private $file;

    private $tempDir;

    public function __construct(string $tempDir, $appKey)
    {
        $this->tempDir = $tempDir;
        $this->file = "{$tempDir}/ys7_{$appKey}.data";
    }

    public function get($key)
    {
        // TODO: Implement get() method.
        $data = $this->read();
        if (isset($data[$key])) {
            $data = $data[$key];
            if (isset($data['expire'])) {
                if (time() <= $data['expire']) {
                    return $data['data'];
                }
            } else {
                return $data['data'];
            }
        }
        return null;
    }

    public function set($key, $value, int $expire = null)
    {
        // TODO: Implement set() method.
        $data = $this->read();
        $data[$key] = [
            'data' => $value,
            'expire' => $expire
        ];
        return $this->write($data);
    }

    private function read(): array
    {
        if (!file_exists($this->file)) {
            return [];
        }
        $data = file_get_contents($this->file);
        $json = json_decode($data, true);
        if (!is_array($json)) {
            $json = [];
        }
        return $json;
    }

    private function write(array $data)
    {
        return file_put_contents($this->file, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
