<?php

return [
    // Diğer çeviriler...
    
    'leaderboard' => [
        'success' => 'Lider tablosu başarıyla getirildi',
    ],
    
    'courses' => [
        'listed' => 'Kurslar başarıyla listelendi',
        'retrieved' => 'Kurs detayları başarıyla getirildi',
        'by_category' => 'Kategorideki kurslar listelendi',
    ],
    'profile' => [
        'retrieved' => 'Profil bilgileri başarıyla getirildi',
        'updated' => 'Profil bilgileri başarıyla güncellendi',
        'password_updated' => 'Şifreniz başarıyla güncellendi',
        'locale_updated' => 'Dil tercihiniz :locale olarak güncellendi',
    ],
    'badges' => [
        'listed' => 'Rozetler başarıyla listelendi',
    ],
    'api' => [
        'badges' => [
            'list' => [
                'success' => 'Rozetler başarıyla listelendi'
            ],
            'show' => [
                'success' => 'Rozet detayları başarıyla getirildi',
                'not_found' => 'Rozet bulunamadı'
            ],
            'user_badges' => [
                'success' => 'Kullanıcı rozetleri başarıyla listelendi'
            ],
            'check' => [
                'success' => 'Rozet kontrolleri başarıyla tamamlandı',
                'no_new_badges' => 'Yeni kazanılan rozet bulunmamaktadır'
            ],
            'awarded' => ':count adet yeni rozet kazandınız!'
        ],
        'auth' => [
            'unauthenticated' => 'Bu işlemi gerçekleştirmek için oturum açmalısınız'
        ],
        'contact' => [
            'list' => [
                'success' => 'İletişim talepleri başarıyla listelendi'
            ],
            'create' => [
                'success' => 'İletişim talebiniz başarıyla gönderildi. En kısa sürede size dönüş yapılacaktır.'
            ],
        ],
        'Missions' => [
            'list' => [
                'success' => 'Görevler başarıyla listelendi'
            ],
            'show' => [
                'success' => 'Görev detayları başarıyla getirildi',
                'not_found' => 'Görev bulunamadı'
            ],
            'complete' => [
                'success' => 'Görev başarıyla tamamlandı',
                'not_found' => 'Görev bulunamadı',
                'already_completed' => 'Bu görev zaten tamamlanmış'
            ],
            'available' => [
                'success' => 'Kullanılabilir görevler başarıyla listelendi'
            ],
            'progress' => [
                'success' => 'Görev ilerleme durumunuz başarıyla getirildi'
            ]
        ],
        'story' => [
            'list' => [
                'success' => 'Hikayeler başarıyla listelendi'
            ],
            'show' => [
                'success' => 'Hikaye detayları başarıyla getirildi'
            ]
        ],
        'MultipleChoiceQuestion' => [
            'list' => [
                'success' => 'Çoktan seçmeli sorular başarıyla listelendi'
            ],
            'show' => [
                'success' => 'Çoktan seçmeli soru detayları başarıyla getirildi'
            ],
            'correct_answer' => 'Tebrikler! Doğru cevap verdiniz',
            'incorrect_answer' => 'Üzgünüz, cevabınız yanlış'
        ]
    ],
    
    'course_chapters' => [
        'list_success' => 'Kurs bölümleri başarıyla listelendi',
        'list_by_course_success' => 'Kursa ait bölümler başarıyla listelendi',
        'not_found' => 'Kurs bölümleri bulunamadı'
    ],
    
    'course_chapter' => [
        'not_found' => 'Kurs bölümü bulunamadı',
        'detail_success' => 'Kurs bölüm detayları başarıyla getirildi'
    ],
    
    'course_chapter_lesson' => [
        'list_by_chapter_success' => 'Bölüme ait dersler başarıyla listelendi',
        'not_found' => 'Ders bulunamadı',
        'detail_success' => 'Ders detayları başarıyla getirildi',
        'prerequisites_list_success' => 'Ders ön koşulları başarıyla listelendi',
        'lock_status_success' => 'Ders kilit durumu başarıyla getirildi',
        'locked' => 'Bu dersi açmak için önce şu dersleri tamamlamalısınız: :prerequisites'
    ],
    
    'lesson_completion' => [
        'already_completed' => 'Bu ders zaten tamamlanmış',
        'completed' => 'Ders başarıyla tamamlandı'
    ],
    
    'user' => [
        'onesignal_updated' => 'OneSignal bilgileri başarıyla güncellendi'
    ],
    
    'notification' => [
        'custom' => [
            'success' => ':count adet kullanıcıya özel bildirim başarıyla gönderildi',
            'error' => 'Özel bildirim gönderilirken bir hata oluştu'
        ],
        'broadcast' => [
            'success' => 'Toplu bildirim başarıyla gönderildi',
            'error' => 'Toplu bildirim gönderilirken bir hata oluştu'
        ],
        'logs' => [
            'retrieved' => 'Bildirim logları başarıyla getirildi',
            'error' => 'Bildirim logları getirilirken bir hata oluştu'
        ],
        'settings' => [
            'retrieved' => 'Bildirim ayarları başarıyla getirildi',
            'error' => 'Bildirim ayarları getirilirken bir hata oluştu',
            'updated' => 'Bildirim ayarları başarıyla güncellendi',
            'update_error' => 'Bildirim ayarları güncellenirken bir hata oluştu',
            'reset' => 'Bildirim ayarları varsayılan değerlere sıfırlandı',
            'reset_error' => 'Bildirim ayarları sıfırlanırken bir hata oluştu'
        ],
        'list_success' => 'Bildirimler başarıyla listelendi',
        'detail_success' => 'Bildirim detayı başarıyla getirildi',
        'send_to_user_success' => 'Bildirim kullanıcıya başarıyla gönderildi',
        'send_to_user_error' => 'Bildirim kullanıcıya gönderilirken bir hata oluştu',
        'send_to_segment_success' => 'Bildirim segmente başarıyla gönderildi',
        'send_to_segment_error' => 'Bildirim segmente gönderilirken bir hata oluştu',
        'broadcast_success' => 'Toplu bildirim başarıyla gönderildi',
        'broadcast_error' => 'Toplu bildirim gönderilirken bir hata oluştu',
        'cancel_success' => 'Bildirim başarıyla iptal edildi',
        'cancel_error' => 'Bildirim iptal edilirken bir hata oluştu',
        'template_create_success' => 'Bildirim şablonu başarıyla oluşturuldu',
        'template_create_error' => 'Bildirim şablonu oluşturulurken bir hata oluştu',
        'template_update_success' => 'Bildirim şablonu başarıyla güncellendi',
        'template_update_error' => 'Bildirim şablonu güncellenirken bir hata oluştu',
        'template_delete_success' => 'Bildirim şablonu başarıyla silindi',
        'template_delete_error' => 'Bildirim şablonu silinirken bir hata oluştu',
        'statistics_success' => 'Bildirim istatistikleri başarıyla getirildi'
    ],
    
    'story_category' => [
        'list_success' => 'Hikaye kategorileri başarıyla listelendi',
        'show_success' => 'Hikaye kategorisi başarıyla getirildi',
        'not_found' => 'Hikaye kategorisi bulunamadı'
    ],
    
    'app_version_too_low' => 'Uygulama sürümünüz (:current) çok eski. Lütfen uygulamayı güncelleyin. Minimum gerekli sürüm: :required',
    
    'admin' => [
        'contact' => [
            'list' => [
                'success' => 'İletişim talepleri başarıyla listelendi'
            ],
            'create' => [
                'success' => 'İletişim talebi başarıyla oluşturuldu'
            ],
            'show' => [
                'success' => 'İletişim talebi detayları başarıyla getirildi'
            ],
            'update' => [
                'success' => 'İletişim talebi başarıyla güncellendi'
            ],
            'delete' => [
                'success' => 'İletişim talebi başarıyla silindi'
            ]
        ]
    ],
    
    // Diğer çeviriler...
]; 