<?php

return [
    /*
    |--------------------------------------------------------------------------
    | POSHUB Enterprise Cryptographic Public Key (RSA-2048 / SHA-256)
    |--------------------------------------------------------------------------
    | Kunci publik resmi ini tertanam di dalam sistem untuk memvalidasi
    | sertifikat lisensi 1 domain yang diterbitkan secara sah oleh Author.
    | Pembeli dapat memodifikasi source code tanpa bisa memalsukan lisensi domain.
    */
    'public_key' => <<<EOT
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxdLmO4hQotMmNksl9r4j
5TWgMBm/0OFevQ6C/FAXnMIcH8YXyiUz9dMOfDwmRzmxRbFn1zYItA1jJrn16pmZ
CTURVc/9/aWYQonoA6Z2rlPqAj4hPde2wVkh+DTHtPq2dOkEc+Ur5go5GTg31dyM
e0M3Kvj7F5nDBL0SJWxVQbvTQzazPmgMP6PivLKhq3JtSFT5YFStRHETHi+UGsdO
ZMlXw3/z2LJNf9BWTYUSjftNzlc3lus8VLkAfAlV1FQJX+QwHhLrr/W3zQ+ElqHL
wyLv9ri1y8sBlnnkRHuUgnhEilkEE86XmRubvUMNW0+9IyuFatqrqQ4Gkt65Japx
NwIDAQAB
-----END PUBLIC KEY-----
EOT,

    /*
    |--------------------------------------------------------------------------
    | Durasi Cache Lisensi & Masa Tenggang Offline (Detik)
    |--------------------------------------------------------------------------
    */
    'cache_ttl' => (int) env('LICENSE_CACHE_TTL', 21600), // 6 Jam
    'offline_grace_period' => (int) env('LICENSE_OFFLINE_GRACE_PERIOD', 604800), // 7 Hari
    'sheet_id' => env('LICENSE_SHEET_ID', ''),
    'allow_all' => env('ALLOW_ALL_LICENSE', false),
];
