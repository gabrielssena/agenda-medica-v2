<?php

class PersistenciaSimples {
    private static $arquivo_dados = __DIR__ . '/../dados/sistema_dados.json';
    
    public static function salvar($dados) {
        try {
            $dados['timestamp'] = time();
            $json = json_encode($dados, JSON_PRETTY_PRINT);
            file_put_contents(self::$arquivo_dados, $json);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public static function carregar() {
        try {
            if (!file_exists(self::$arquivo_dados)) {
                return [
                    'pacientes' => [],
                    'consultas' => [],
                    'urgencias' => [],
                    'timestamp' => time()
                ];
            }
            
            $json = file_get_contents(self::$arquivo_dados);
            $dados = json_decode($json, true);
            
            return $dados ?: [
                'pacientes' => [],
                'consultas' => [],
                'urgencias' => [],
                'timestamp' => time()
            ];
        } catch (Exception $e) {
            return [
                'pacientes' => [],
                'consultas' => [],
                'urgencias' => [],
                'timestamp' => time()
            ];
        }
    }
    
    public static function limpar() {
        try {
            if (file_exists(self::$arquivo_dados)) {
                unlink(self::$arquivo_dados);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>