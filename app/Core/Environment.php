<?php
namespace app\Core;

use Exception;

class Environment
{
    /**
     * Carrega as variáveis de um arquivo .env para o ambiente PHP.
     *
     * @param string $path O caminho para o diretório que contém o arquivo .env.
     * @return void
     * @throws Exception Se o arquivo .env não for encontrado ou não puder ser lido.
     */
    public static function load(string $path): void
    {
        $envFile = $path . '/.env';

        if (!is_file($envFile) || !is_readable($envFile)) {
            // Se não encontrar, lança a exceção (pode ser tratada ou exibida na tela)
            throw new Exception("O arquivo .env não foi encontrado ou não pode ser lido. Crie o arquivo na raiz do projeto.");
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Ignora comentários
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Garante que a linha contém o separador '='
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                // Remove aspas do valor, se existirem
                if (strlen($value) > 1 && $value[0] === '"' && $value[strlen($value) - 1] === '"') {
                    $value = substr($value, 1, -1);
                }

                // Define a variável de ambiente
                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    putenv(sprintf('%s=%s', $name, $value));
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
    }
}