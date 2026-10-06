<?php
namespace App\Services;

use Symfony\Component\Process\Process;

class EncryptedBackup
{
    protected function run(array $command): void
    {
        $process = new Process($command);
        $process->setTimeout(600);
        if ($process->run() !== 0) throw new \RuntimeException('Backup step failed: ' . basename($command[0]));
    }
    public function create(): array
    {
        $dir = storage_path('app/private/backups');
        if (!is_dir($dir) && !mkdir($dir, 0700, true)) throw new \RuntimeException('Cannot create backup directory.');
        chmod($dir, 0700);
        $lock = fopen($dir . '/backup.lock', 'c');
        if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) throw new \RuntimeException('Another backup is running.');
        $work = $dir . '/work-' . bin2hex(random_bytes(8));
        mkdir($work, 0700);
        $certificate = storage_path('app/private/backup-public-certificate.pem');
        $name = 'jailaoi_' . gmdate('Ymd_His') . '_' . bin2hex(random_bytes(3));
        $files = [];
        try {
            if (!is_file($certificate)) throw new \RuntimeException('Backup encryption certificate is missing.');
            $cfg = config('database.connections.mysql');
            $quote = fn ($value) => '"' . str_replace(["\\", '"', "\n", "\r"], ["\\\\", '\\"', '\\n', '\\r'], (string) $value) . '"';
            $options = "[client]\n";
            foreach (['host'=>$cfg['host'],'port'=>$cfg['port'],'user'=>$cfg['username'],'password'=>$cfg['password']] as $key=>$value) $options .= $key . '=' . $quote($value) . "\n";
            file_put_contents($work . '/mysql.cnf', $options); chmod($work . '/mysql.cnf', 0600);
            $this->run(['mysqldump','--defaults-extra-file=' . $work . '/mysql.cnf','--single-transaction','--quick','--skip-lock-tables','--no-tablespaces','--hex-blob','--result-file=' . $work . '/database.sql',$cfg['database']]);
            if (!is_file($work . '/database.sql') || filesize($work . '/database.sql') < 100) throw new \RuntimeException('Database dump is empty.');
            $this->run(['gzip', '-6', $work . '/database.sql']);
            $this->run(['gzip', '-t', $work . '/database.sql.gz']);
            $media = storage_path('app/public');
            $this->run(['tar','-czf',$work . '/local-media.tar.gz','-C',$media,'.']);
            $this->run(['tar','-tzf',$work . '/local-media.tar.gz']);
            foreach (['database.sql.gz','local-media.tar.gz'] as $part) {
                $out = $dir . '/' . $name . '_' . $part . '.cms';
                $this->run(['openssl','cms','-encrypt','-binary','-aes-256-cbc','-in',$work . '/' . $part,'-out',$out . '.part','-outform','DER',$certificate]);
                chmod($out . '.part', 0600);
                if (filesize($out . '.part') < 100) throw new \RuntimeException('Encrypted backup is empty.');
                rename($out . '.part', $out);$files[]=$out;
            }
            $manifest = ['created_at'=>gmdate('c'),'coverage'=>['database','local_media'],'remote_media_included'=>false,'files'=>array_map(fn($p)=>['name'=>basename($p),'bytes'=>filesize($p),'sha256'=>hash_file('sha256',$p)],$files)];
            file_put_contents($dir . '/' . $name . '_manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
            chmod($dir . '/' . $name . '_manifest.json',0600);
            file_put_contents($dir . '/latest.json.tmp',json_encode($manifest));chmod($dir . '/latest.json.tmp',0600);rename($dir . '/latest.json.tmp',$dir . '/latest.json');
            // Retain at least the newest successful backup. Never prune before success.
            foreach (glob($dir . '/jailaoi_*') ?: [] as $old) if (filemtime($old)<time()-30*86400 && !str_contains(basename($old),$name)) unlink($old);
            return $manifest;
        } finally {
            foreach (glob($work . '/*') ?: [] as $temp) unlink($temp);
            rmdir($work);flock($lock,LOCK_UN);fclose($lock);
        }
    }
}
