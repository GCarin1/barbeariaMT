<?php
/**
 * Don Barbero - Rate Limiting Service
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:58
 * @version 1.0.0
 */

declare(strict_types=1);

namespace services;

class RateLimitService {
    private string $cacheDir;
    
    public function __construct() {
        $this->cacheDir = BASE_PATH . '/cache/rate_limit';
        ensureDir($this->cacheDir);
    }
    
    /**
     * Verificar e registrar tentativa
     * 
     * @param string $key Chave única (ex: login_192.168.1.1)
     * @param int $maxAttempts Máximo de tentativas
     * @param int $windowSeconds Janela de tempo em segundos
     * @return array Status do rate limit
     */
    public function check(string $key, int $maxAttempts, int $windowSeconds): array {
        $file = $this->getCacheFile($key);
        $now = time();
        
        // Ler tentativas anteriores
        $attempts = $this->readAttempts($file);
        
        // Limpar tentativas antigas
        $attempts = array_filter($attempts, function($timestamp) use ($now, $windowSeconds) {
            return ($now - $timestamp) < $windowSeconds;
        });
        
        // Verificar se excedeu o limite
        if (count($attempts) >= $maxAttempts) {
            $oldestAttempt = min($attempts);
            $remainingTime = $windowSeconds - ($now - $oldestAttempt);
            
            return [
                'allowed' => false,
                'remaining' => 0,
                'retry_after' => $remainingTime,
                'message' => "Muitas tentativas. Tente novamente em " . ceil($remainingTime / 60) . " minuto(s)."
            ];
        }
        
        // Registrar nova tentativa
        $attempts[] = $now;
        $this->saveAttempts($file, $attempts);
        
        return [
            'allowed' => true,
            'remaining' => $maxAttempts - count($attempts),
            'retry_after' => 0,
            'message' => ''
        ];
    }
    
    /**
     * Limpar tentativas de uma chave
     * 
     * @param string $key Chave
     * @return void
     */
    public function clear(string $key): void {
        $file = $this->getCacheFile($key);
        if (file_exists($file)) {
            unlink($file);
        }
    }
    
    /**
     * Obter arquivo de cache para chave
     * 
     * @param string $key Chave
     * @return string Caminho do arquivo
     */
    private function getCacheFile(string $key): string {
        $hash = md5($key);
        return $this->cacheDir . '/' . $hash . '.json';
    }
    
    /**
     * Ler tentativas do arquivo
     * 
     * @param string $file Arquivo
     * @return array Array de timestamps
     */
    private function readAttempts(string $file): array {
        if (!file_exists($file)) {
            return [];
        }
        
        $content = file_get_contents($file);
        if ($content === false) {
            return [];
        }
        
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }
    
    /**
     * Salvar tentativas no arquivo
     * 
     * @param string $file Arquivo
     * @param array $attempts Array de timestamps
     * @return void
     */
    private function saveAttempts(string $file, array $attempts): void {
        file_put_contents($file, json_encode(array_values($attempts)));
    }
    
    /**
     * Limpar arquivos antigos de cache
     * 
     * @param int $olderThanSeconds Remover arquivos mais antigos que X segundos
     * @return int Número de arquivos removidos
     */
    public function cleanup(int $olderThanSeconds = 86400): int {
        $files = glob($this->cacheDir . '/*.json');
        $now = time();
        $removed = 0;
        
        foreach ($files as $file) {
            if (($now - filemtime($file)) > $olderThanSeconds) {
                unlink($file);
                $removed++;
            }
        }
        
        return $removed;
    }
}
