<?php

return [
    // Genel hata mesajları
    'server_error' => 'An unexpected error has occurred on the server.',
    'not_found' => 'The requested :model was not found.',
    'route_not_found' => 'The requested page does not exist.',
    'method_not_allowed' => 'This HTTP method is not allowed for this endpoint.',
    'validation_error' => 'The provided data is invalid.',
    'unauthenticated' => 'You must be logged in to perform this action.',
    'forbidden' => 'You do not have permission to perform this action.',
    'http_error' => 'An HTTP error has occurred.',
    
    // Veritabanı hataları
    'database_error' => 'A database error has occurred.',
    'duplicate_entry' => 'The record already exists.',
    'duplicate_course_chapter_slug' => 'This chapter name is already in use. Please choose a different name.',
    'duplicate_course_slug' => 'This course name is already in use. Please choose a different name.',
    'duplicate_lesson_slug' => 'This lesson name is already in use. Please choose a different name.',
    
    // İşlem hataları
    'create_failed' => 'Failed to create the resource.',
    'update_failed' => 'Failed to update the resource.',
    'delete_failed' => 'Failed to delete the resource.',
    
    // Dosya işleme hataları
    'file_upload_failed' => 'Failed to upload the file.',
    'file_too_large' => 'File is too large. Maximum allowed size is :size.',
    'invalid_file_type' => 'Invalid file type. Allowed types are: :types.',
    
    // E-posta hataları
    'email_sending_failed' => 'Failed to send email. Please check SMTP settings.',
    
    // Yetkilendirme hataları
    'token_invalid' => 'The authentication token is invalid.',
    'token_expired' => 'The authentication token has expired.',
]; 