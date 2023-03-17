<?php
namespace App\Service;
use App\Exception\UpstreamException;

class FtpUpstream
{
    /**
     * @throws UpstreamException
     */
    public static function upload(array $files): void
    {
        if (empty($files)) {
            return;
        }

        $mh = curl_multi_init();

        $local_paths = [];
        $fhs = [];
        $chs = [];
        foreach ($files as $local_path => $remote_basename) {
            $dir = date("Ymd");
           
            $local_paths[] = $local_path;

            $fh = fopen($local_path, 'r');
            $fhs[] = $fh;

            $ch = curl_init();
            $chs[] = $ch;
             
            $url = $_ENV['FTP_UPSTREAM_URL'].$dir;
            if(!file_exists($url)) {
                mkdir($_ENV['FTP_UPSTREAM_URL'].$dir, 0777, true);
            }
 
            curl_setopt(
                $ch,
                CURLOPT_URL,
                $url.'/'.$remote_basename

            );
            if ($_ENV['FTP_UPSTREAM_SSL']) {
                curl_setopt($ch, CURLOPT_USE_SSL, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            }
            curl_setopt($ch, CURLOPT_UPLOAD, 1);
            curl_setopt($ch, CURLOPT_INFILE, $fh);
            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($local_path));

            curl_multi_add_handle($mh, $ch);
        }

        $error = null;
        $mh_errno = [];
        $mh_running = 0;
        do {
            $status = curl_multi_exec($mh, $mh_running);
            if ($mh_running) {
                curl_multi_select($mh);
            }
            while (false !== ($info = curl_multi_info_read($mh))) {
                $i = array_search($info['handle'], $chs);
                if (false === $i) {
                    $error = 'Logic exception';
                } else {
                    $mh_errno[$local_paths[$i]] = $info['result'];
                }

            }
        } while ($mh_running && CURLM_OK === $status);

        foreach ($chs as $ch) {
            curl_multi_remove_handle($mh, $ch);
        }
        curl_multi_close($mh);

        if ($error) {
            throw new UpstreamException($error);
        }

        foreach ($mh_errno as $local_path => $errno) {
            if (CURLE_OK !== $errno) {
                $relative_file_path = mb_substr(
                    $local_path,
                    mb_strlen(FILE_ROOT) + 1
                );
                $error = $relative_file_path.': '.$errno;
                throw new UpstreamException($error);
            }
        }
    }
}
