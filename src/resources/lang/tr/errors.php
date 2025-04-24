<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Genel Hata Mesajları
    |--------------------------------------------------------------------------
    */

    'general_error' => 'Beklenmedik bir hata oluştu. Lütfen daha sonra tekrar deneyin.',
    'validation_error' => 'Gönderilen veriler geçersiz.',
    'unauthenticated' => 'Bu işlemi yapmak için giriş yapmalısınız.',
    'forbidden' => 'Bu işlemi yapmaya yetkiniz yok.',
    'route_not_found' => 'İstenen kaynak bulunamadı.',
    'method_not_allowed' => 'Bu kaynak için kullanılan metot desteklenmiyor.',
    'model_not_found' => 'İstenen :model bulunamadı.',

    /*
    |--------------------------------------------------------------------------
    | FillInTheBlank Modülü Hata Mesajları
    |--------------------------------------------------------------------------
    */
    'fill_in_the_blank' => [
        'create_error' => 'Boşluk doldurma sorusu oluşturulurken bir hata oluştu.',
        'update_error' => 'Boşluk doldurma sorusu güncellenirken bir hata oluştu.',
        'delete_error' => 'Boşluk doldurma sorusu silinirken bir hata oluştu.',
        'not_found' => 'Belirtilen boşluk doldurma sorusu bulunamadı.',
        // ... diğer fill_in_the_blank özel hataları
    ],

    /*
    |--------------------------------------------------------------------------
    | User Modülü Hata Mesajları
    |--------------------------------------------------------------------------
    */
    'user' => [
        'locale_update_failed' => 'Dil tercihi güncellenirken bir hata oluştu.',
        'profile_not_found' => 'Profil bilgileri bulunamadı.',
        'profile_update_failed' => 'Profil bilgileri güncellenirken bir hata oluştu.',
        // ... diğer kullanıcı hataları
    ],

    // ... Diğer modül hataları buraya eklenebilir
]; 