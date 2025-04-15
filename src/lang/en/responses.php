<?php

return [
    'courses' => [
        'listed' => 'Courses listed successfully',
        'created' => 'Course created successfully',
        'retrieved' => 'Course retrieved successfully',
        'updated' => 'Course updated successfully',
        'deleted' => 'Course deleted successfully',
        'not_found' => 'Course not found',
        'update_error' => 'An error occurred while updating the course',
        'delete_error' => 'An error occurred while deleting the course',
        'order_updated' => 'Course order updated successfully',
        'order_invalid' => 'Order value is invalid',
        'order_error' => 'An error occurred while updating the order',
        'status_active' => 'Course status updated to active',
        'status_inactive' => 'Course status updated to inactive',
        'status_error' => 'An error occurred while changing the status',
        'featured' => 'Course has been featured',
        'unfeatured' => 'Course has been unfeatured',
        'featured_error' => 'An error occurred while changing the featured status',
        'by_category' => 'Category courses listed successfully',
        'by_category_error' => 'An error occurred while listing category courses'
    ],
    'course_chapter' => [
        // Success Messages
        'created' => 'Course chapter created successfully.',
        'updated' => 'Course chapter updated successfully.',
        'deleted' => 'Course chapter deleted successfully.',
        'status_updated' => 'Course chapter status updated successfully.',
        'order_updated' => 'Course chapter order updated successfully.',
        'list_success' => 'Course chapters listed successfully.',
        'detail_success' => 'Course chapter details retrieved successfully.',
        'list_by_course_success' => 'Course chapters for the specified course listed successfully.',

        // Error Messages
        'not_found' => 'Course chapter not found.',
        'already_exists' => 'A course chapter with this name already exists.',
        'create_failed' => 'Failed to create course chapter.',
        'update_failed' => 'Failed to update course chapter.',
        'delete_failed' => 'Failed to delete course chapter.',
        'status_update_failed' => 'Failed to update course chapter status.',
        'order_update_failed' => 'Failed to update course chapter order.',
        'list_failed' => 'Failed to list course chapters.',
        'list_by_course_failed' => 'Failed to list course chapters for the specified course.',
        'detail_failed' => 'Failed to retrieve course chapter details.',
        'validation_failed' => 'The provided data is invalid.',

        // Authorization Messages
        'unauthorized' => 'You are not authorized to perform this action.',
        'forbidden' => 'You do not have access to this resource.',

        // Information Messages
        'no_items' => 'No course chapters have been created yet.',
        'no_items_in_course' => 'No chapters have been created for this course yet.',
    ],

    'course_chapters' => [
        // Plural forms for API response format
        'list_success' => 'Course chapters listed successfully.',
        'list_by_course_success' => 'Course chapters for the specified course listed successfully.',
        'list_failed' => 'Failed to list course chapters.',
        'list_by_course_failed' => 'Failed to list course chapters for the specified course.',
        'no_items' => 'No course chapters have been created yet.',
        'no_items_in_course' => 'No chapters have been created for this course yet.',
    ],

    'course_chapter_lesson' => [
        // Success Messages
        'created' => 'Course lesson created successfully.',
        'updated' => 'Course lesson updated successfully.',
        'deleted' => 'Course lesson deleted successfully.',
        'status_updated' => 'Course lesson status updated successfully.',
        'order_updated' => 'Course lesson order updated successfully.',
        'list_success' => 'Course lessons listed successfully.',
        'detail_success' => 'Lesson details retrieved successfully.',
        'list_by_chapter_success' => 'Lessons by chapter listed successfully.',
        'lesson_completed' => 'Lesson has been marked as completed successfully.',
        'prerequisites_list_success' => 'Lesson prerequisites listed successfully.',
        'lock_status_success' => 'Lesson lock status retrieved successfully.',
        'locked' => 'You need to complete :prerequisites lessons first to view this lesson.',

        // Error Messages
        'not_found' => 'Lesson not found.',
        'already_exists' => 'A course lesson with this name already exists.',
        'create_failed' => 'Failed to create course lesson.',
        'update_failed' => 'Failed to update course lesson.',
        'delete_failed' => 'Failed to delete course lesson.',
        'status_update_failed' => 'Failed to update course lesson status.',
        'order_update_failed' => 'Failed to update course lesson order.',
        'list_failed' => 'Failed to list course lessons.',
        'list_by_chapter_failed' => 'Failed to list course lessons for the specified chapter.',
        'detail_failed' => 'Failed to retrieve course lesson details.',
        'validation_failed' => 'The provided data is invalid.',
        'completion_failed' => 'Failed to mark the lesson as completed.',

        // Authorization Messages
        'unauthorized' => 'You are not authorized to perform this action.',
        'forbidden' => 'You do not have access to this resource.',

        // Information Messages
        'no_items' => 'No course lessons have been created yet.',
        'no_items_in_chapter' => 'No lessons have been created for this chapter yet.',
        'already_completed' => 'This lesson has already been marked as completed.',
    ],

    'course_chapter_lessons' => [
        // Plural forms for API response format
        'list_success' => 'Course lessons listed successfully.',
        'list_by_chapter_success' => 'Course lessons for the specified chapter listed successfully.',
        'list_failed' => 'Failed to list course lessons.',
        'list_by_chapter_failed' => 'Failed to list course lessons for the specified chapter.',
        'no_items' => 'No course lessons have been created yet.',
        'no_items_in_chapter' => 'No lessons have been created for this chapter yet.',
    ],

    'lesson_completion' => [
        // Success Messages
        'completed' => 'Lesson completed successfully.',
        'already_completed' => 'This lesson is already completed.',
        'completion_failed' => 'Failed to mark the lesson as completed.',
        'not_authorized' => 'You must be logged in to mark this lesson as completed.',
        'lesson_not_found' => 'The lesson to be marked was not found.',
        'progress_updated' => 'Lesson progress has been updated.',
    ],

    'auth' => [
        'register_success' => 'Registration successful. Please verify your email address.',
        'login_success' => 'Login successful.',
        'logout_success' => 'Logout successful.',
        'refresh_success' => 'Token refreshed successfully.',
        'forgot_password_success' => 'Password reset link has been sent to your email.',
        'profile_success' => 'Profile information retrieved successfully.',
        'email_not_verified' => 'Your email address is not verified. Please check your email.',
        'invalid_credentials' => 'Invalid email or password.',
        'invalid_token' => 'Invalid or expired token.',
        'email_already_verified' => 'Your email is already verified.',
    ],

    'verification' => [
        'success' => 'Your email has been verified successfully.',
        'already_verified' => 'Your email is already verified.',
        'invalid_link' => 'Invalid verification link or link has expired.',
        'user_not_found' => 'User with this email address not found.',
        'link_sent' => 'Verification link has been sent to your email.',
    ],

    // Lesson content translations
    'admin' => [
        'lesson-contents' => [
            // Admin Success Messages
            'list' => [
                'success' => 'Lesson contents listed successfully.'
            ],
            'show' => [
                'success' => 'Lesson content displayed successfully.'
            ],
            'create' => [
                'success' => 'Lesson content created successfully.'
            ],
            'update' => [
                'success' => 'Lesson content updated successfully.'
            ],
            'delete' => [
                'success' => 'Lesson content deleted successfully.'
            ],
            'status' => [
                'success' => 'Lesson content status updated successfully.'
            ],
            'order' => [
                'success' => 'Lesson content order updated successfully.'
            ],
            'bulk-order' => [
                'success' => 'Lesson contents order updated successfully.'
            ],
            'by-lesson' => [
                'success' => 'Lesson contents for the specified lesson listed successfully.'
            ],
            'create-text' => [
                'success' => 'Text content created successfully.'
            ],
            'create-video' => [
                'success' => 'Video content created successfully.'
            ],
            'create-fill-in-the-blank' => [
                'success' => 'Fill in the blank content created successfully.'
            ],
            'create-multiple-choice' => [
                'success' => 'Multiple choice question content created successfully.'
            ],
            'create-true-false' => [
                'success' => 'True/False question content created successfully.'
            ]
        ],

        'quiz' => [
            // Quiz mesajları kaldırıldı
        ],
        'Missions' => [
            'create' => [
                'success' => 'Mission created successfully.',
            ],
            'update' => [
                'success' => 'Mission updated successfully.',
            ],
            'delete' => [
                'success' => 'Mission deleted successfully.',
            ],
            'toggleStatus' => [
                'success' => 'Mission status updated successfully.',
            ],
        ],
    ],

    // API translations
    'api' => [
        'lesson-contents' => [
            'find' => [
                'success' => 'Lesson content found successfully.'
            ],
            'by-lesson' => [
                'success' => 'Lesson contents for the specified lesson listed successfully.'
            ],
            'by-type' => [
                'success' => 'Lesson contents of the specified type listed successfully.'
            ],
            'invalid-type' => 'Invalid content type specified.',
            'not_found' => 'Content not found.'
        ],

        'quiz' => [
            // Quiz mesajları kaldırıldı
        ],
        'Missions' => [
            'list' => [
                'success' => 'Missions listed successfully.',
            ],
            'show' => [
                'success' => 'Mission details retrieved successfully.',
            ],
            'complete' => [
                'success' => 'Mission completed successfully.',
                'already_completed' => 'You have already completed this mission.',
            ],
            'available' => [
                'success' => 'Available missions retrieved successfully.',
            ],
        ]
    ]
    // Similar messages for other models
];
