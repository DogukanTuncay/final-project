<?php

return [
    'courses' => [
        'listed' => 'Kurslar başarıyla listelendi',
        'created' => 'Kurs başarıyla oluşturuldu',
        'retrieved' => 'Kurs başarıyla getirildi',
        'updated' => 'Kurs başarıyla güncellendi',
        'deleted' => 'Kurs başarıyla silindi',
        'not_found' => 'Kurs bulunamadı',
        'update_error' => 'Kurs güncellenirken bir hata oluştu',
        'delete_error' => 'Kurs silinirken bir hata oluştu',
        'order_updated' => 'Kurs sıralaması başarıyla güncellendi',
        'order_invalid' => 'Sıralama değeri geçerli değil',
        'order_error' => 'Sıralama güncellenirken bir hata oluştu',
        'status_active' => 'Kurs durumu aktif olarak güncellendi',
        'status_inactive' => 'Kurs durumu pasif olarak güncellendi',
        'status_error' => 'Durum değiştirilirken bir hata oluştu',
        'featured' => 'Kurs öne çıkarıldı',
        'unfeatured' => 'Kursun öne çıkarılması kaldırıldı',
        'featured_error' => 'Öne çıkarma durumu değiştirilirken bir hata oluştu',
        'by_category' => 'Kategori kursları başarıyla listelendi',
        'by_category_error' => 'Kategori kursları listelenirken bir hata oluştu'
    ],
    'course_chapter' => [
        // Başarı Mesajları
        'created' => 'Kurs bölümü başarıyla oluşturuldu.',
        'updated' => 'Kurs bölümü başarıyla güncellendi.',
        'deleted' => 'Kurs bölümü başarıyla silindi.',
        'status_updated' => 'Kurs bölümü durumu başarıyla güncellendi.',
        'order_updated' => 'Kurs bölümü sırası başarıyla güncellendi.',
        'list_success' => 'Kurs bölümleri başarıyla listelendi.',
        'detail_success' => 'Kurs bölümü detayları başarıyla getirildi.',
        'list_by_course_success' => 'Kursa ait bölümler başarıyla listelendi.',

        // Hata Mesajları
        'not_found' => 'Kurs bölümü bulunamadı.',
        'already_exists' => 'Bu isimde bir kurs bölümü zaten mevcut.',
        'create_failed' => 'Kurs bölümü oluşturulurken bir hata oluştu.',
        'update_failed' => 'Kurs bölümü güncellenirken bir hata oluştu.',
        'delete_failed' => 'Kurs bölümü silinirken bir hata oluştu.',
        'status_update_failed' => 'Kurs bölümü durumu güncellenirken bir hata oluştu.',
        'order_update_failed' => 'Kurs bölümü sırası güncellenirken bir hata oluştu.',
        'list_failed' => 'Kurs bölümleri listelenirken bir hata oluştu.',
        'list_by_course_failed' => 'Kursa ait bölümler listelenirken bir hata oluştu.',
        'detail_failed' => 'Kurs bölümü detayları getirilirken bir hata oluştu.',
        'validation_failed' => 'Girilen bilgiler geçerli değil.',

        // Yetkilendirme Mesajları
        'unauthorized' => 'Bu işlemi yapmak için yetkiniz bulunmamaktadır.',
        'forbidden' => 'Bu kaynağa erişim izniniz bulunmamaktadır.',

        // Bilgilendirme Mesajları
        'no_items' => 'Henüz bir kurs bölümü oluşturulmamış.',
        'no_items_in_course' => 'Bu kursa ait henüz bir bölüm oluşturulmamış.',
    ],

    'course_chapters' => [
        // API response formatında çoğul kullanımlar için
        'list_success' => 'Kurs bölümleri başarıyla listelendi.',
        'list_by_course_success' => 'Kursa ait bölümler başarıyla listelendi.',
        'list_failed' => 'Kurs bölümleri listelenirken bir hata oluştu.',
        'list_by_course_failed' => 'Kursa ait bölümler listelenirken bir hata oluştu.',
        'no_items' => 'Henüz bir kurs bölümü oluşturulmamış.',
        'no_items_in_course' => 'Bu kursa ait henüz bir bölüm oluşturulmamış.',
    ],

    'course_chapter_lesson' => [
        // Başarı Mesajları
        'created' => 'Kurs dersi başarıyla oluşturuldu.',
        'updated' => 'Kurs dersi başarıyla güncellendi.',
        'deleted' => 'Kurs dersi başarıyla silindi.',
        'status_updated' => 'Kurs dersi durumu başarıyla güncellendi.',
        'order_updated' => 'Kurs dersi sırası başarıyla güncellendi.',
        'list_success' => 'Kurs dersleri başarıyla listelendi.',
        'detail_success' => 'Kurs dersi detayları başarıyla getirildi.',
        'list_by_chapter_success' => 'Bölüme ait dersler başarıyla listelendi.',
        'lesson_completed' => 'Ders başarıyla tamamlandı olarak işaretlendi.',

        // Hata Mesajları
        'not_found' => 'Kurs dersi bulunamadı.',
        'already_exists' => 'Bu isimde bir kurs dersi zaten mevcut.',
        'create_failed' => 'Kurs dersi oluşturulurken bir hata oluştu.',
        'update_failed' => 'Kurs dersi güncellenirken bir hata oluştu.',
        'delete_failed' => 'Kurs dersi silinirken bir hata oluştu.',
        'status_update_failed' => 'Kurs dersi durumu güncellenirken bir hata oluştu.',
        'order_update_failed' => 'Kurs dersi sırası güncellenirken bir hata oluştu.',
        'list_failed' => 'Kurs dersleri listelenirken bir hata oluştu.',
        'list_by_chapter_failed' => 'Bölüme ait dersler listelenirken bir hata oluştu.',
        'detail_failed' => 'Kurs dersi detayları getirilirken bir hata oluştu.',
        'validation_failed' => 'Girilen bilgiler geçerli değil.',
        'completion_failed' => 'Ders tamamlandı olarak işaretlenirken bir hata oluştu.',

        // Yetkilendirme Mesajları
        'unauthorized' => 'Bu işlemi yapmak için yetkiniz bulunmamaktadır.',
        'forbidden' => 'Bu kaynağa erişim izniniz bulunmamaktadır.',

        // Bilgilendirme Mesajları
        'no_items' => 'Henüz bir kurs dersi oluşturulmamış.',
        'no_items_in_chapter' => 'Bu bölüme ait henüz bir ders oluşturulmamış.',
        'already_completed' => 'Bu ders zaten tamamlandı olarak işaretlenmiş.',
    ],

    'course_chapter_lessons' => [
        // API response formatında çoğul kullanımlar için
        'list_success' => 'Kurs dersleri başarıyla listelendi.',
        'list_by_chapter_success' => 'Bölüme ait dersler başarıyla listelendi.',
        'list_failed' => 'Kurs dersleri listelenirken bir hata oluştu.',
        'list_by_chapter_failed' => 'Bölüme ait dersler listelenirken bir hata oluştu.',
        'no_items' => 'Henüz bir kurs dersi oluşturulmamış.',
        'no_items_in_chapter' => 'Bu bölüme ait henüz bir ders oluşturulmamış.',
    ],

    'lesson_completion' => [
        // Başarı Mesajları
        'completed' => 'Ders başarıyla tamamlandı.',
        'already_completed' => 'Bu ders zaten tamamlanmış.',
        'completion_failed' => 'Ders tamamlandı olarak işaretlenirken bir hata oluştu.',
        'not_authorized' => 'Bu dersi tamamlandı olarak işaretlemek için giriş yapmalısınız.',
        'lesson_not_found' => 'İşaretlenmek istenen ders bulunamadı.',
        'progress_updated' => 'Ders ilerleme durumu güncellendi.',
    ],

    // Ders içerikleri için çeviriler
    'admin' => [
        'lesson-contents' => [
            // Admin Başarı Mesajları
            'list' => [
                'success' => 'Ders içerikleri başarıyla listelendi.'
            ],
            'show' => [
                'success' => 'Ders içeriği başarıyla gösterildi.'
            ],
            'create' => [
                'success' => 'Ders içeriği başarıyla oluşturuldu.'
            ],
            'update' => [
                'success' => 'Ders içeriği başarıyla güncellendi.'
            ],
            'delete' => [
                'success' => 'Ders içeriği başarıyla silindi.'
            ],
            'status' => [
                'success' => 'Ders içeriği durumu başarıyla güncellendi.'
            ],
            'order' => [
                'success' => 'Ders içeriği sırası başarıyla güncellendi.'
            ],
            'bulk-order' => [
                'success' => 'Ders içerikleri sıralaması başarıyla güncellendi.'
            ],
            'by-lesson' => [
                'success' => 'Derse ait içerikler başarıyla listelendi.'
            ],
            'create-text' => [
                'success' => 'Metin içeriği başarıyla oluşturuldu.'
            ],
            'create-video' => [
                'success' => 'Video içeriği başarıyla oluşturuldu.'
            ],
            'create-fill-in-the-blank' => [
                'success' => 'Boşluk doldurma içeriği başarıyla oluşturuldu.'
            ],
            'create-multiple-choice' => [
                'success' => 'Çoktan seçmeli soru içeriği başarıyla oluşturuldu.'
            ],
        ],

        'quiz' => [
            // Quiz mesajları kaldırıldı
        ],
        'Missions' => [
            'create' => [
                'success' => 'Görev başarıyla oluşturuldu.',
            ],
            'update' => [
                'success' => 'Görev başarıyla güncellendi.',
            ],
            'delete' => [
                'success' => 'Görev başarıyla silindi.',
            ],
            'toggleStatus' => [
                'success' => 'Görev durumu başarıyla güncellendi.',
            ],
        ],
    ],

    // API tarafı için çeviriler
        'api' => [
            'lesson-contents' => [
                'find' => [
                    'success' => 'Ders içeriği başarıyla bulundu.'
                ],
                'by-lesson' => [
                    'success' => 'Derse ait içerikler başarıyla listelendi.'
                ],
                'by-type' => [
                    'success' => 'Belirtilen türdeki içerikler başarıyla listelendi.'
                ],
                'invalid-type' => 'Geçersiz içerik türü belirtildi.',
                'not_found' => 'İçerik bulunamadı.'
            ],

            'quiz' => [
                'list' => [
                    'success' => 'Quiz listesi başarıyla getirildi.'
                ],
                'show' => [
                    'success' => 'Quiz detayları başarıyla getirildi.'
                ],
                'start' => [
                    'success' => 'Quiz başarıyla başlatıldı.'
                ],
                'questions' => [
                    'success' => 'Quiz soruları başarıyla getirildi.'
                ],
                'submit' => [
                    'success' => 'Quiz cevapları başarıyla gönderildi.'
                ],
                'not_found' => 'Quiz bulunamadı.'

        ],
        'Missions' => [
            'list' => [
                'success' => 'Görevler başarıyla listelendi.',
            ],
            'show' => [
                'success' => 'Görev detayları başarıyla getirildi.',
            ],
            'complete' => [
                'success' => 'Görev başarıyla tamamlandı.',
                'already_completed' => 'Bu görevi zaten tamamladınız.',
            ],
            'available' => [
                'success' => 'Uygun görevler başarıyla getirildi.',
            ],
        ],
    ],
    'auth' => [
        'register_success' => 'Kayıt işlemi başarılı. Lütfen e-posta adresinizi doğrulayın.',
        'login_success' => 'Giriş başarılı.',
        'logout_success' => 'Çıkış başarılı.',
        'refresh_success' => 'Token başarıyla yenilendi.',
        'forgot_password_success' => 'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.',
        'profile_success' => 'Profil bilgileri başarıyla getirildi.',
        'email_not_verified' => 'E-posta adresiniz doğrulanmamış. Lütfen e-postanızı kontrol edin.',
        'invalid_credentials' => 'E-posta adresi veya şifre hatalı.',
        'invalid_token' => 'Geçersiz veya süresi dolmuş token.',
        'email_already_verified' => 'E-posta adresiniz zaten doğrulanmış.',
        'reset_form' => 'Şifre sıfırlama formu.',
        'reset_success' => 'Şifreniz başarıyla sıfırlanmıştır.',
        'reset_failed' => 'Şifre sıfırlama işlemi başarısız oldu.',
    ],
    'verification' => [
        'success' => 'E-posta adresiniz başarıyla doğrulandı.',
        'already_verified' => 'E-posta adresiniz zaten doğrulanmış.',
        'invalid_link' => 'Geçersiz doğrulama bağlantısı veya bağlantının süresi dolmuş.',
        'user_not_found' => 'Bu e-posta adresine sahip bir kullanıcı bulunamadı.',
        'link_sent' => 'Doğrulama bağlantısı e-posta adresinize gönderildi.',
    ],
    'course_chapter_lesson' => [
        'not_found' => 'Ders bulunamadı.',
        'list_by_chapter_success' => 'Bölüme ait dersler başarıyla listelendi.',
        'detail_success' => 'Ders detayı başarıyla getirildi.',
        'prerequisites_list_success' => 'Ders ön koşulları başarıyla listelendi.',
        'lock_status_success' => 'Ders kilit durumu başarıyla getirildi.',
        'locked' => 'Bu dersi görüntülemek için önce :prerequisites derslerini tamamlamalısınız.',
    ],
    'lesson_completion' => [
        'completed' => 'Ders başarıyla tamamlandı.',
        'already_completed' => 'Bu ders zaten tamamlanmış.',
    ],
    'true_false_question' => [
        'list_success' => 'Doğru/Yanlış soruları başarıyla listelendi.',
        'create_success' => 'Doğru/Yanlış sorusu başarıyla oluşturuldu.',
        'retrieve_success' => 'Doğru/Yanlış sorusu başarıyla getirildi.',
        'update_success' => 'Doğru/Yanlış sorusu başarıyla güncellendi.',
        'delete_success' => 'Doğru/Yanlış sorusu başarıyla silindi.',
        'status_active' => 'Doğru/Yanlış sorusu aktif duruma getirildi.',
        'status_inactive' => 'Doğru/Yanlış sorusu pasif duruma getirildi.',
        'add_to_lesson_success' => 'Doğru/Yanlış sorusu derse başarıyla eklendi.',
    ],
];
