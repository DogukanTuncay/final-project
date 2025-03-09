<?php

return [
    // Genel hata mesajları
    'server_error' => 'Sunucuda beklenmeyen bir hata oluştu.',
    'not_found' => 'İstenen :model bulunamadı.',
    'route_not_found' => 'İstenen sayfa mevcut değil.',
    'method_not_allowed' => 'Bu HTTP metodu bu endpoint için izin verilmiyor.',
    'validation_error' => 'Girilen veriler geçersiz.',
    'unauthenticated' => 'Bu işlemi gerçekleştirmek için giriş yapmanız gerekiyor.',
    'forbidden' => 'Bu işlemi gerçekleştirmek için yetkiniz yok.',
    'http_error' => 'Bir HTTP hatası oluştu.',
    
    // Veritabanı hataları
    'database_error' => 'Veritabanı hatası oluştu.',
    'duplicate_entry' => 'Bu kayıt zaten mevcut.',
    'duplicate_course_chapter_slug' => 'Bu bölüm adı zaten kullanılıyor. Lütfen farklı bir isim seçin.',
    'duplicate_course_slug' => 'Bu kurs adı zaten kullanılıyor. Lütfen farklı bir isim seçin.',
    'duplicate_lesson_slug' => 'Bu ders adı zaten kullanılıyor. Lütfen farklı bir isim seçin.',
    
    // İşlem hataları
    'create_failed' => 'Kaynak oluşturulamadı.',
    'update_failed' => 'Kaynak güncellenemedi.',
    'delete_failed' => 'Kaynak silinemedi.',
    
    // Dosya işleme hataları
    'file_upload_failed' => 'Dosya yüklenemedi.',
    'file_too_large' => 'Dosya çok büyük. İzin verilen maksimum boyut: :size.',
    'invalid_file_type' => 'Geçersiz dosya türü. İzin verilen türler: :types.',
    
    // E-posta hataları
    'email_sending_failed' => 'E-posta gönderilemedi. Lütfen SMTP ayarlarınızı kontrol edin.',
    
    // Yetkilendirme hataları
    'token_invalid' => 'Kimlik doğrulama jetonu geçersiz.',
    'token_expired' => 'Kimlik doğrulama jetonunun süresi doldu.',
]; 