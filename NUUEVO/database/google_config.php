<?php
// ARCHIVO DE CONFIGURACIÓN DE GOOGLE OAUTH
// ==========================================
// INSTRUCCIONES:
// 1. Ve a: https://console.cloud.google.com/
// 2. Crea un proyecto llamado "RentNono"
// 3. Configura la pantalla de consentimiento OAuth
// 4. Crea credenciales OAuth 2.0
// 5. Reemplaza los valores de abajo con tus credenciales

class GoogleConfig {
    // === REEMPLAZA ESTOS VALORES CON TUS CREDENCIALES ===
    const CLIENT_ID = '24939222054-j2nhbalkqbqk0hivb51kidq5duacpglk.apps.googleusercontent.com'; // Ej: 1234567890-abcdefghijklmnopqrstuvwxyz.apps.googleusercontent.com
    const CLIENT_SECRET = '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'; // Ej: GOCSPX-abcdefghijklmnopqrstuvwxyz
    
    const REDIRECT_URI = 'http://localhost/RentNono/database/google_callback.php';
    const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    const USERINFO_URL = 'https://www.googleapis.com/oauth2/v2/userinfo';
    
    // Scopes necesarios
    const SCOPES = [
        'email',
        'profile',
        'openid'
    ];
    
    // Verificar si las credenciales están configuradas
    public static function isConfigured() {
        return self::CLIENT_ID !== '24939222054-j2nhbalkqbqk0hivb51kidq5duacpglk.apps.googleusercontent.com' && 
               self::CLIENT_SECRET !== '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';
    }
    
    // Generar URL de autorización
    public static function getAuthUrl() {
        $params = [
            'client_id' => self::CLIENT_ID,
            'redirect_uri' => self::REDIRECT_URI,
            'response_type' => 'code',
            'scope' => implode(' ', self::SCOPES),
            'access_type' => 'offline',
            'prompt' => 'consent'
        ];
        
        return self::AUTH_URL . '?' . http_build_query($params);
    }
    
    // Obtener token de acceso
    public static function getAccessToken($code) {
        $data = [
            'code' => $code,
            'client_id' => self::CLIENT_ID,
            'client_secret' => self::CLIENT_SECRET,
            'redirect_uri' => self::REDIRECT_URI,
            'grant_type' => 'authorization_code'
        ];
        
        $ch = curl_init(self::TOKEN_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    // Obtener información del usuario
    public static function getUserInfo($access_token) {
        $ch = curl_init(self::USERINFO_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
}

// Ejemplo de credenciales válidas (formato):
// CLIENT_ID: 1234567890-abcdefghijklmnopqrstuvwxyz.apps.googleusercontent.com
// CLIENT_SECRET: GOCSPX-abcdefghijklmnopqrstuvwxyz
?>
