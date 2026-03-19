<?php
defined('ERISIM') or exit('401 Unauthorized');

/*************************************************
 * Döküman Controller Modülü
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/
class Api_Controller extends Controller
{
    public function index()
    {
        $URL = URL_Segmentleri();
        $format = strtolower($URL[1] ?? 'html');

        // İzin verilen formatlar
        $izinliFormatlar = [
            'html',
            'json',
            'xml',
            'markdown',
            'md',
            'yaml',
            'yml',
            'openapi',
            'swagger',
            'postman',
            'text',
            'txt',
            'array',
            'csv',
            'curl',
            'httpie',
            'php',
            'javascript',
            'js',
            'python',
            'py',
            'bash',
            'sh',
            'ruby',
            'rb',
            'go',
            'golang',
            'java',
            'csharp',
            'cs',
            'typescript',
            'ts',
            'powershell',
            'ps1',
            'wget',
            'axios',
            'insomnia',
            'swagger2'
        ];

        // Format kontrolü
        if (!in_array($format, $izinliFormatlar)) {
            $format = 'html';
        }

        // Dokümantasyonu oluştur ve göster
        echo Api_Router::Dokumanlar($format);
    }
}
