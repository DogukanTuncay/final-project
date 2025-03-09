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
        'completed' => 'Ders başarıyla tamamlandı olarak işaretlendi.',
        'already_completed' => 'Bu ders zaten tamamlandı olarak işaretlenmiş.',
        'completion_failed' => 'Ders tamamlandı olarak işaretlenirken bir hata oluştu.',
        'not_authorized' => 'Bu dersi tamamlandı olarak işaretlemek için giriş yapmalısınız.',
        'lesson_not_found' => 'İşaretlenmek istenen ders bulunamadı.',
        'progress_updated' => 'Ders ilerleme durumu güncellendi.',
    ]
    // Diğer modeller için benzer şekilde mesajlar eklenebilir
];